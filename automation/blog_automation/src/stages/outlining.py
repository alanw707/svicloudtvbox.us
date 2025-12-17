"""Outline generation and validation stage for blog automation.

This module handles creating structured content outlines using Claude AI
or fallback to template-based outlines when Claude is unavailable.
"""

import logging
from typing import Dict, Any, List
from ..models import TopicCandidate


class OutlineGenerator:
    """Generates and validates structured content outlines.

    Creates 5-7 section outlines for blog posts using Claude AI with
    fallback to template-based outlines. Validates outline completeness
    and quality before passing to drafting stage.
    """

    def __init__(
        self,
        claude_client: Any,
        config: Dict[str, Any],
        logger: logging.Logger
    ):
        """Initialize outline generator.

        Args:
            claude_client: Anthropic Claude API client instance
            config: Configuration dictionary with API and quality settings
            logger: Logger instance for operations tracking
        """
        self.claude = claude_client
        self.config = config
        self.log = logger

    def generate_outline(
        self,
        topic: TopicCandidate,
        brief: str,
        retry: bool = False
    ) -> str:
        """Generate structured content outline.

        Creates numbered list outline (5-7 sections) based on topic and brief.
        Attempts Claude-powered generation first, falling back to template-based
        outline if Claude unavailable or fails.

        Args:
            topic: TopicCandidate with keyword and metadata
            brief: Strategic brief text from briefing stage
            retry: Whether this is a retry after failed validation

        Returns:
            Numbered outline text with 5-7 sections (max 40 chars each)

        Example:
            >>> outline = generator.generate_outline(topic, brief)
            >>> print(outline)
            ## 專家指南概覽
            1. SVICLOUD 美國本地服務優勢
            2. 型號/方案與適用族群
            ...
        """
        claude_model = self.config.get("apis", {}).get("claude_model_outline")
        metadata = topic.metadata or {}

        # Try Claude-powered generation first
        if self.claude and claude_model:
            try:
                system = self._get_outline_prompt(topic)
                retry_note = "重新優化" if retry else "首次草稿"

                user_prompt = (
                    f"{retry_note}，請依據以下簡報撰寫條列式大綱：\n\n"
                    f"簡報：\n{brief}\n\n"
                    f"主題分類：{metadata.get('topic_type')}\n"
                    f"地區：{metadata.get('geo_target') or '全美'}\n"
                    "輸出格式：以數字排序的清單，每條不超過40字。\n"
                    "- 一定要有：美國本地服務（現貨/快速出貨、客服、保固/維修）\n"
                    "- 若是 comparison：要有明確對比段落\n"
                    "- 若是 campaign：要有季節/情境段落\n"
                    "- 若是 geo：要有地區生活情境段落\n"
                    "- 不要每一篇都用相同段落順序"
                )

                # Add competitor context if available
                competitor_note = self._competitor_context(metadata)
                if competitor_note:
                    user_prompt += f"\n競品提醒：{competitor_note}"

                # Call Claude API
                outline_text = self._claude_complete(
                    model=claude_model,
                    system_prompt=system,
                    user_prompt=user_prompt,
                    max_tokens=600,
                    temperature=0.3,
                )

                if outline_text:
                    return outline_text.strip()

            except Exception as exc:
                self.log.warning("Claude outline generation failed: %s", exc)

        # Fallback: Template-based outline
        return self._generate_fallback_outline(topic, retry)

    def validate_outline(self, outline: str) -> bool:
        """Validate outline structure and completeness.

        Checks that outline has sufficient sections (lines) to meet
        quality threshold configured in config.yaml.

        Args:
            outline: Outline text to validate

        Returns:
            True if outline meets quality threshold (default: 0.75 * 4 lines)

        Example:
            >>> outline = "## Header\\n1. Section 1\\n2. Section 2\\n3. Section 3"
            >>> generator.validate_outline(outline)
            True
        """
        threshold = self.config.get("quality", {}).get("outline_score_threshold", 0.75)

        # Score based on line count (expect ~4+ sections minimum)
        score = min(outline.count("\n"), 4) / 4

        return score >= threshold

    def _generate_fallback_outline(
        self,
        topic: TopicCandidate,
        retry: bool = False
    ) -> str:
        """Generate template-based outline when Claude unavailable.

        Args:
            topic: TopicCandidate with keyword and metadata
            retry: Whether this is a retry attempt

        Returns:
            Template-based numbered outline
        """
        sections = self._build_outline_sections(topic)
        header = "## 再次優化專家概覽" if retry else "## 專家指南概覽"

        outline_lines = [header]
        for idx, section in enumerate(sections, 1):
            outline_lines.append(f"{idx}. {section}")

        return "\n".join(outline_lines)

    def _build_outline_sections(self, topic: TopicCandidate) -> List[str]:
        """Build standard outline sections based on topic metadata.

        Args:
            topic: TopicCandidate with metadata

        Returns:
            List of section titles (5-7 sections)
        """
        metadata = topic.metadata or {}

        # Base sections that appear in all outlines
        sections = [
            "SVICLOUD 美國本地服務優勢",
            "型號/方案與適用族群",
            "安裝步驟與售後支援",
        ]

        # Add topic-specific sections
        if metadata.get("is_comparison"):
            sections.insert(1, "SVICLOUD 與競品差異")

        if metadata.get("is_geo"):
            geo_target = metadata.get("geo_target", "特定地區")
            sections.append(f"{geo_target} 客戶專屬物流與體驗")

        if metadata.get("is_campaign"):
            sections.append("活動/節慶使用情境")

        # Always end with FAQ section
        sections.append("常見問題與下一步")

        return sections

    def _get_outline_prompt(self, topic: TopicCandidate) -> str:
        """Get system prompt for outline generation.

        Args:
            topic: TopicCandidate with metadata

        Returns:
            System prompt string for Claude
        """
        metadata = topic.metadata or {}
        topic_type = metadata.get("topic_type", "pillar")
        geo = metadata.get("geo_target") or "全美"

        base_prompt = "你是 SVICLOUD 編輯總監，產出5-7個段落，涵蓋美國倉儲、售後與FAQ。"

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

    def _competitor_context(self, metadata: Dict[str, Any]) -> str:
        """Generate competitor context note from metadata.

        Args:
            metadata: Topic metadata with competitor info

        Returns:
            Competitor context string or empty if no competitor data
        """
        domain = metadata.get("source_domain")
        query = metadata.get("competitor_query")

        if not domain or not query:
            return ""

        return f"競品 {domain} 近期內容聚焦「{query}」，須強調SVICLOUD的差異化優勢。"

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
