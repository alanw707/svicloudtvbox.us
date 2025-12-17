"""Content brief generation stage for blog automation.

This module handles generating strategic content briefs using Claude AI
or fallback to template-based briefs when Claude is unavailable.
"""

import logging
from typing import Dict, Any, Optional
from ..models import TopicCandidate


class BriefGenerator:
    """Generates strategic content briefs for blog topics.

    Uses Claude AI for intelligent brief generation with fallback to
    template-based briefs. Briefs provide strategic direction including
    target audience, brand messaging, and topic-specific focus areas.
    """

    def __init__(
        self,
        claude_client: Any,
        config: Dict[str, Any],
        logger: logging.Logger
    ):
        """Initialize brief generator.

        Args:
            claude_client: Anthropic Claude API client instance
            config: Configuration dictionary with API settings
            logger: Logger instance for operations tracking
        """
        self.claude = claude_client
        self.config = config
        self.log = logger

    def generate_brief(self, topic: TopicCandidate) -> str:
        """Generate strategic content brief for topic.

        Attempts Claude-powered brief generation first, falling back to
        template-based brief if Claude is unavailable or fails.

        Args:
            topic: TopicCandidate with keyword and metadata

        Returns:
            Strategic brief text (100-150 characters) with:
            - Target keyword and audience
            - Brand messaging points
            - Topic-specific focus areas

        Example:
            >>> brief = generator.generate_brief(topic)
            >>> print(brief)
            目標關鍵字：svicloud 10p+
            受眾：美國華人家庭、長輩與新移民
            品牌重點：4K HDR、Wi-Fi 6 與一年美國保固...
        """
        metadata = topic.metadata or {}
        claude_model = self.config.get("apis", {}).get("claude_model_brief")

        # Try Claude-powered generation first
        if self.claude and claude_model:
            try:
                system = self._get_brief_prompt(topic)
                user_prompt = (
                    f"請以100-150字撰寫策略簡報：\n"
                    f"- 關鍵字：{topic.keyword}\n"
                    f"- 主題分類：{metadata.get('topic_type')}\n"
                    f"- 地區：{metadata.get('geo_target') or '全美'}\n"
                    f"- 比較焦點：{metadata.get('is_comparison')}\n"
                    f"- 活動/節慶：{metadata.get('is_campaign')}\n"
                    "務必強調：美國倉儲現貨/快速出貨、中文/雙語客服、售後保固與遠端協助；"
                    "並依主題分類選擇性帶到 4K/Wi‑Fi/Dolby 等規格（不要每篇都把 4K 當主角）。"
                )

                # Add competitor context if available
                competitor_note = self._competitor_context(metadata)
                if competitor_note:
                    user_prompt += f"\n- 競品提醒：{competitor_note}"

                # Call Claude API
                brief_text = self._claude_complete(
                    model=claude_model,
                    system_prompt=system,
                    user_prompt=user_prompt,
                    max_tokens=400,
                    temperature=0.2,
                )

                if brief_text:
                    return brief_text.strip()

            except Exception as exc:
                self.log.warning("Claude brief generation failed: %s", exc)

        # Fallback: Template-based brief
        return self._generate_fallback_brief(topic)

    def _generate_fallback_brief(self, topic: TopicCandidate) -> str:
        """Generate template-based brief when Claude unavailable.

        Args:
            topic: TopicCandidate with keyword and metadata

        Returns:
            Template-based brief with standard brand messaging
        """
        metadata = topic.metadata or {}

        lines = [
            f"目標關鍵字：{topic.keyword}",
            "受眾：美國華人家庭、長輩與新移民",
            "品牌重點：4K HDR + Dolby Audio、Wi-Fi 6 雙頻、中文/英文語音介面與一年美國保固。",
        ]

        # Add topic-specific focus areas
        if metadata.get("is_geo"):
            lines.append(
            f"地區聚焦：針對{metadata.get('geo_target')}地區，強調本地影音體驗與售後。"
            )

        if metadata.get("is_comparison"):
            lines.append(
                "比較焦點：突出SVICLOUD 10P+/10S與競品在頻道數、保固與售後的差異。"
            )

        if metadata.get("is_campaign"):
            lines.append(
                "活動語境：結合節慶/派對需求，提供歌單、流程與器材搭配。"
            )

        if metadata.get("topic_type") == "faq":
            lines.append(
                "FAQ目標：回答美國華人最常見的安裝、付款與售後問題。"
            )

        return "\n".join(lines)

    def _get_brief_prompt(self, topic: TopicCandidate) -> str:
        """Get system prompt for brief generation.

        Args:
            topic: TopicCandidate with metadata

        Returns:
            System prompt string for Claude
        """
        metadata = topic.metadata or {}
        topic_type = metadata.get("topic_type", "pillar")
        geo = metadata.get("geo_target") or "全美"

        base_prompt = "你是 SVICLOUD 策略總監，撰寫100字簡報，聚焦美國本地內容體驗、售後與客服優勢。"

        # Add topic-specific modifiers
        modifiers = []

        if topic_type == "comparison":
            modifiers.append("比較SVICLOUD與競品的頻道數、保固、售後。")

        if topic_type == "geo":
            modifiers.append(f"聚焦{geo}地區的內容需求、安裝與售後支援。")

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
            model: Claude model ID (e.g., "claude-3-haiku-20240307")
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
