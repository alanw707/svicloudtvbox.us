"""Quality validation stage for blog automation.

This module handles quality assessment using Claude AI with robust fallback
to heuristic-based scoring. Contains critical Phase 1 fixes for token limits
and JSON parsing reliability.

PHASE 1 FIXES PRESERVED:
1. Token limit fix: Content truncation to 10K chars (lines 81-83)
2. JSON parsing: 3-strategy extraction with retry logic (lines 95-116)
3. Fallback scoring: Validated links, language, brand checks (lines 153-228)

PHASE 2 ADDITION:
4. Content-policy compliance scanner — rejects posts with prohibited phrases.
"""

import json
import logging
import re
from pathlib import Path
from typing import Any, Dict, List, Optional, Tuple

import yaml

from ..models import GeneratedPost


def _load_content_policies(config_dir: Optional[Path] = None) -> Dict[str, Any]:
    """Load content-policies.yaml from the automation config directory."""
    if config_dir is None:
        config_dir = Path(__file__).resolve().parent.parent.parent
    policy_path = config_dir / "content-policies.yaml"
    if not policy_path.exists():
        return {}
    try:
        return yaml.safe_load(policy_path.read_text(encoding="utf-8")) or {}
    except Exception:
        return {}


def _collect_prohibited_phrases(policies: Dict[str, Any]) -> List[str]:
    """Gather all prohibited phrases from every policy section."""
    phrases: List[str] = []
    for section_key, section_val in policies.items():
        if isinstance(section_val, dict):
            phrases.extend(section_val.get("prohibited_phrases", []))
        elif isinstance(section_val, list) and section_key == "global_prohibited":
            phrases.extend(section_val)
    # Deduplicate while preserving order
    seen: set[str] = set()
    unique: List[str] = []
    for p in phrases:
        low = p.lower()
        if low not in seen:
            seen.add(low)
            unique.append(p)
    return unique


def check_policy_compliance(
    content: str,
    policies: Dict[str, Any],
) -> Tuple[bool, List[str]]:
    """Check content against content-policies.yaml prohibited phrases.

    Returns:
        (passes, violations) — passes is True if no violations found.
        violations is a list of human-readable violation descriptions.
    """
    prohibited = _collect_prohibited_phrases(policies)
    if not prohibited:
        return True, []

    violations: List[str] = []
    for phrase in prohibited:
        if phrase in content:
            violations.append(f"禁用詞偵測：「{phrase}」出現在文章中")

    return len(violations) == 0, violations


class QualityValidator:
    """Validates blog post quality with Claude AI and heuristic fallback.

    Primary validation uses Claude for comprehensive quality assessment.
    Fallback heuristic scoring validates: length, structure, brand voice,
    internal links, and Traditional Chinese language usage.
    """

    def __init__(
        self,
        claude_client: Any,
        config: Dict[str, Any],
        logger: logging.Logger
    ):
        """Initialize quality validator.

        Args:
            claude_client: Anthropic Claude API client instance
            config: Configuration dictionary with quality thresholds
            logger: Logger instance for operations tracking
        """
        self.claude = claude_client
        self.config = config
        self.log = logger
        self._policies = _load_content_policies()

    def validate_quality(self, post: GeneratedPost) -> float:
        """Validate post quality with Claude AI or fallback scoring.

        Attempts Claude-based quality assessment first, falling back to
        heuristic scoring if Claude unavailable or fails. Updates post's
        quality_score attribute and returns the score.

        Args:
            post: GeneratedPost with content, keyword, frontmatter

        Returns:
            Quality score (0-100) with post.quality_score updated

        Example:
            >>> score = validator.validate_quality(post)
            >>> if score >= 75:
            ...     print("Post meets quality threshold")
        """
        quality_cfg = self.config.get("quality", {})
        min_required = float(quality_cfg.get("min_score", 80))
        claude_weight = float(quality_cfg.get("claude_weight", 0.6))
        claude_weight = max(0.0, min(1.0, claude_weight))
        claude_floor = float(quality_cfg.get("claude_floor", 65))

        # PHASE 2: Content-policy compliance gate (runs before everything else)
        passes, violations = check_policy_compliance(post.content, self._policies)
        if not passes:
            for v in violations:
                self.log.error("POLICY VIOLATION: %s", v)
            post.quality_score = 0.0
            post.frontmatter["policy_violations"] = violations
            self.log.error(
                "Post REJECTED by content-policy scanner (%d violation(s)). "
                "Score forced to 0 — post will be saved as draft.",
                len(violations),
            )
            return 0.0

        # Try Claude QA first
        score = self._qa_with_claude(post)
        if score is not None:
            fallback_score = self._fallback_quality_score(post, log_details=False)

            combined = (score * claude_weight) + (fallback_score * (1 - claude_weight))

            if score < claude_floor and fallback_score >= min_required:
                combined = fallback_score
                self.log.info(
                    "QA: Claude score %.1f below floor %.1f, using fallback score %.1f",
                    score,
                    claude_floor,
                    fallback_score,
                )
            else:
                self.log.info(
                    "QA: combined score %.1f (claude=%.1f, fallback=%.1f, weight=%.2f)",
                    combined,
                    score,
                    fallback_score,
                    claude_weight,
                )

            post.frontmatter["qa_components"] = {
                "claude_score": score,
                "fallback_score": fallback_score,
                "claude_weight": claude_weight,
            }
            post.quality_score = combined
            return combined

        # Fallback: Heuristic scoring with Phase 1 validation fixes
        return self._fallback_quality_score(post, log_details=True)

    def _qa_with_claude(self, post: GeneratedPost) -> Optional[float]:
        """Perform Claude-based quality assessment with Phase 1 fixes.

        PHASE 1 FIXES:
        - Token limit fix: Truncate content to 10K chars
        - JSON parsing: 3-strategy extraction with retry
        - Retry logic: 3 attempts with detailed error logging

        Args:
            post: GeneratedPost to validate

        Returns:
            Quality score (0-100) or None if Claude QA fails

        Raises:
            No exceptions - returns None on all failures
        """
        model = self.config.get("apis", {}).get("claude_model_qa")
        if not self.claude or not model:
            return None

        system = self._get_qa_prompt(post)

        # PHASE 1 FIX: Truncate content to avoid token limit (242K > 200K max)
        # Use first 10K characters - sufficient for quality validation
        content_sample = post.content[:10000] if len(post.content) > 10000 else post.content
        truncated_note = "（內容已截取前10000字元進行評估）" if len(post.content) > 10000 else ""

        user_prompt = (
            "請以純 JSON 格式回覆（不要用 markdown 代碼塊包裹），格式範例：{\"score\":85,\"notes\":[\"語句順暢\"]}\n"
            "評估以下文章是否符合：傳統中文、關鍵字自然出現、強調美國本地優勢、包含內部連結描述、長度>4000字。\n"
            f"{truncated_note}\n"
            f"文章內容：\n{content_sample}\n"
        )

        # PHASE 1 FIX: Increased retry attempts from 2 to 3
        for attempt in range(3):
            try:
                completion = self._claude_complete(
                    model=model,
                    system_prompt=system,
                    user_prompt=user_prompt,
                    max_tokens=500,  # Increased from 400 for more context
                    temperature=0.2,
                )

                # PHASE 1 FIX: Improved JSON extraction with 3 strategies
                cleaned = completion.strip()

                # Strategy 1: Extract from markdown code blocks
                if "```json" in cleaned:
                    json_start = cleaned.find("```json") + 7
                    json_end = cleaned.find("```", json_start)
                    if json_end > json_start:
                        cleaned = cleaned[json_start:json_end].strip()

                # Strategy 2: Remove any remaining backticks and whitespace
                cleaned = cleaned.strip("`").strip()

                # Strategy 3: Find first { and last } to extract JSON object
                first_brace = cleaned.find("{")
                last_brace = cleaned.rfind("}")
                if first_brace >= 0 and last_brace > first_brace:
                    cleaned = cleaned[first_brace : last_brace + 1]

                # Parse and validate JSON
                data = json.loads(cleaned)

                # Validate schema: must have 'score' field
                if "score" not in data:
                    self.log.warning("QA JSON missing 'score' field (attempt %s)", attempt + 1)
                    continue

                score = float(data.get("score", 0))

                # Validate score range: 0-100
                if not (0 <= score <= 100):
                    self.log.warning("QA score out of range: %.1f (attempt %s)", score, attempt + 1)
                    continue

                # Success! Store notes and return score
                notes = data.get("notes") or []
                post.frontmatter["qa_notes"] = notes
                self.log.info("Claude QA successful: score=%.1f (attempt %s)", score, attempt + 1)
                return score

            except json.JSONDecodeError as exc:
                self.log.warning("QA JSON parse failed (attempt %s): %s - Response: %s", attempt + 1, exc, completion[:200])
            except ValueError as exc:
                self.log.warning("QA score conversion failed (attempt %s): %s", attempt + 1, exc)
            except Exception as exc:
                self.log.warning("Claude QA failed (attempt %s): %s", attempt + 1, exc)
                if "token" in str(exc).lower() or "limit" in str(exc).lower():
                    self.log.error("Token limit still exceeded - content may need further truncation")
                    break

        # All attempts failed
        return None

    def _fallback_quality_score(self, post: GeneratedPost, log_details: bool = True) -> float:
        """Calculate heuristic quality score with Phase 1 validation fixes.

        PHASE 1 FIX: Actually validates criteria instead of just awarding points.
        Checks for:
        - Length: ≥4000 chars (25 points)
        - Structure: H2 headings present (20 points)
        - Brand voice: Product terms present (20 points)
        - Internal links: svicloudtvbox.us URLs (15 points)
        - Language: Traditional Chinese validation (20 points)

        Args:
            post: GeneratedPost to score

        Returns:
            Total quality score (0-100) with detailed logging

        Example:
            >>> score = validator._fallback_quality_score(post)
            >>> # Logs: "Fallback quality score: 75.0 (length=25, structure=20...)"
        """
        content = post.content
        min_length = self.config.get("quality", {}).get("min_length", 4000)

        # Length validation (25 points)
        if len(content) >= min_length:
            length_score = 25
        elif len(content) >= min_length * 0.7:
            length_score = 10
        else:
            length_score = 0

        # Structure validation (20 points) - check for proper H2 headings
        has_structure = "##" in content
        structure_score = 20 if has_structure else 10

        # Brand voice validation (20 points) - check for required product terms
        brand_terms = self.config.get("brand_voice", {}).get("product_terms", [])
        brand_count = sum(1 for term in brand_terms if term in content)

        if brand_count >= 3:
            brand_score = 20
        elif brand_count >= 2:
            brand_score = 15
        elif brand_count >= 1:
            brand_score = 10
        else:
            brand_score = 0

        # Internal links validation (15 points) - actually check for site URLs
        site_domain = "svicloudtvbox.us"
        internal_links = re.findall(rf"https?://{re.escape(site_domain)}/[^\s\)\]]+", content)
        links_count = len(internal_links)

        if links_count >= 3:
            links_score = 15
        elif links_count >= 2:
            links_score = 10
        elif links_count >= 1:
            links_score = 5
        else:
            links_score = 0

        # Language validation (20 points) - check for Traditional Chinese
        # Traditional Chinese characters (繁體中文): U+4E00-U+9FFF
        chinese_chars = re.findall(r'[\u4e00-\u9fff]', content)
        chinese_char_count = len(chinese_chars)

        # Check if predominantly Traditional Chinese (vs Simplified)
        # Simplified-specific chars often in: 国, 为, 这, 么, 们, 时, 个, 过
        simplified_indicators = ['国', '为', '这', '么', '们', '时', '个', '过']
        simplified_count = sum(content.count(char) for char in simplified_indicators)

        if chinese_char_count >= 500:  # Good amount of Chinese content
            # If Simplified indicators are less than 5% of total Chinese chars, it's Traditional
            if simplified_count < (chinese_char_count * 0.05):
                language_score = 20
            else:
                language_score = 10  # Mixed or Simplified
        elif chinese_char_count >= 200:
            language_score = 10  # Some Chinese but not enough
        else:
            language_score = 0  # Insufficient Chinese content

        # Calculate total score
        scores = {
            "length": length_score,
            "structure": structure_score,
            "brand": brand_score,
            "links": links_score,
            "language": language_score,
        }

        total = float(sum(scores.values()))
        post.quality_score = total

        # Log detailed breakdown for debugging
        if log_details:
            self.log.info(
                "Fallback quality score: %.1f (length=%d, structure=%d, brand=%d, links=%d, language=%d)",
                total, length_score, structure_score, brand_score, links_score, language_score
            )

        return total

    def _get_qa_prompt(self, post: GeneratedPost) -> str:
        """Get system prompt for QA assessment.

        Args:
            post: GeneratedPost with frontmatter metadata

        Returns:
            System prompt string for Claude QA
        """
        metadata = {
            "topic_type": post.frontmatter.get("topic_type"),
            "geo_target": post.frontmatter.get("geo_target"),
        }
        topic_type = metadata.get("topic_type", "pillar")
        geo = metadata.get("geo_target") or "全美"

        base_prompt = (
            "你是 SVICLOUD 品質稽核，需回傳 JSON score 與 notes。"
            "特別注意：保固期限必須為「1年」，若文章中出現「2年保固」「3年保固」等錯誤聲明，"
            "score 應直接給 0 分並在 notes 說明違規原因。"
        )

        # Add topic-specific modifiers
        modifiers = []

        if topic_type == "comparison":
            modifiers.append("比較SVICLOUD與競品的頻道數、保固、售後。")

        if topic_type == "geo":
            modifiers.append(f"聚焦{geo}地區的物流與售後需求。")

        if topic_type == "campaign":
            modifiers.append("融入節慶/活動情境、歌單或互動建議。")

        if topic_type == "faq":
            modifiers.append("以問答形式整理常見問題。")

        if modifiers:
            return base_prompt + " " + " ".join(modifiers)

        return base_prompt

    def _claude_complete(
        self,
        model: str,
        system_prompt: str,
        user_prompt: str,
        max_tokens: int,
        temperature: float,
    ) -> str:
        """Call Claude API for completion.

        Wrapper around Claude messages API for consistent error handling.

        Args:
            model: Claude model ID
            system_prompt: System prompt for role/context
            user_prompt: User message with task
            max_tokens: Maximum response tokens
            temperature: Sampling temperature (0.0-1.0)

        Returns:
            Completion text from Claude

        Raises:
            RuntimeError: If Claude client not available
        """
        if not self.claude:
            raise RuntimeError("Claude client not available")

        response = self.claude.messages.create(
            model=model,
            system=system_prompt,
            max_tokens=max_tokens,
            temperature=temperature,
            messages=[{"role": "user", "content": user_prompt}],
        )

        # Extract text from response blocks
        chunks = []
        for block in response.content:
            if getattr(block, "type", None) == "text":
                chunks.append(block.text)

        return "".join(chunks)
