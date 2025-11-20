"""Post drafting stage for blog automation.

This module handles generating full blog posts from outlines using Claude AI
or fallback to template-based generation, with content enhancement including
internal links, images, and call-to-action footers.
"""

import logging
import re
from typing import Any, Dict, List, Optional, Tuple
from ..models import TopicCandidate


class PostDrafter:
    """Generates complete blog posts with content enhancements.

    Creates 4000+ word blog posts from outlines using Claude AI with
    fallback to template-based generation. Handles content enhancement:
    - Brand voice enforcement
    - Internal link insertion
    - Image injection
    - CTA footer appending
    """

    def __init__(
        self,
        claude_client: Any,
        config: Dict[str, Any],
        logger: logging.Logger,
        image_generator: Optional[Any] = None,
    ):
        """Initialize post drafter.

        Args:
            claude_client: Anthropic Claude API client instance
            config: Configuration dictionary with content settings
            logger: Logger instance for operations tracking
        """
        self.claude = claude_client
        self.config = config
        self.log = logger
        self.image_generator = image_generator

    def generate_full_post(
        self,
        topic: TopicCandidate,
        outline: str,
        brief: str
    ) -> str:
        """Generate complete blog post from outline.

        Creates 4000+ word blog post in Markdown format using Claude AI
        or fallback to template-based generation. Includes brand voice,
        internal links, images, and CTA footer.

        Args:
            topic: TopicCandidate with keyword and metadata
            outline: Structured outline from outlining stage
            brief: Strategic brief from briefing stage

        Returns:
            Complete blog post in Markdown (4000+ words) with:
            - H2/H3 heading structure
            - Brand messaging naturally integrated
            - Internal link placeholders
            - Image references
            - CTA footer

        Example:
            >>> content = drafter.generate_full_post(topic, outline, brief)
            >>> len(content) >= 4000  # True
        """
        claude_model = self.config.get("apis", {}).get("claude_model_full")
        metadata = topic.metadata or {}

        content: str = ""

        # Try Claude-powered generation first
        if self.claude and claude_model:
            try:
                system = self._get_full_post_prompt(topic)

                user_prompt = (
                    f"請依據以下資訊撰寫完整部落格文章（Markdown）：\n"
                    f"- 關鍵字：{topic.keyword}\n"
                    f"- 簡報：{brief}\n"
                    f"- 大綱：\n{outline}\n"
                    f"- 主題分類：{metadata.get('topic_type')}\n"
                    f"- 地區：{metadata.get('geo_target') or '全美'}\n"
                    "需求：\n"
                    "1. 穿插 4K HDR + Dolby Audio 影音、Wi-Fi 6 雙頻、雙語介面與語音搜尋等產品賣點。\n"
                    "2. 至少3個內部連結（以純文字描述，稍後由腳本替換）。\n"
                    "3. 每段落加入具體情境或案例。\n"
                    "4. 結尾加入CTA導引至購買/聯絡。\n"
                    "5. 文章長度務必超過4000個字元，段落豐富且包含具體數據或案例。\n"
                    "輸出：純Markdown內容，不含YAML。"
                )

                if metadata.get("official_summary"):
                    user_prompt += f"\n- 官方功能重點：{metadata['official_summary']}"

                # Add competitor context if available
                competitor_note = self._competitor_context(metadata)
                if competitor_note:
                    user_prompt += f"\n- 競品提醒：{competitor_note}"

                # Call Claude API
                full_text = self._claude_complete(
                    model=claude_model,
                    system_prompt=system,
                    user_prompt=user_prompt,
                    max_tokens=4200,
                    temperature=0.35,
                )

                if full_text:
                    content = full_text.strip()

            except Exception as exc:
                self.log.warning("Claude full post generation failed: %s", exc)

        if not content:
            # Fallback: Template-based generation
            content = self._generate_fallback_post(topic)

        return self.ensure_minimum_length(topic, outline, brief, content)

    def enforce_requirements(self, topic: TopicCandidate, content: str) -> str:
        """Ensure brand voice and keyword placement in content.

        Adds required brand phrases to beginning of content if not present.

        Args:
            topic: TopicCandidate with metadata
            content: Blog post content

        Returns:
            Content with brand voice requirements enforced
        """
        required_phrases = self.config.get("brand_voice", {}).get("key_phrases", [])
        keyword = topic.keyword if isinstance(topic.keyword, str) else ""
        missing: List[str] = []

        for phrase in required_phrases:
            if phrase in content:
                continue
            contextual = f"{keyword}｜{phrase}" if keyword else phrase
            missing.append(contextual)

        if not missing:
            return content

        reminder_block = "\n\n".join(f"> {line}" for line in missing)
        return f"{content}\n\n{reminder_block}"

    def insert_internal_links(self, content: str) -> str:
        """Add contextual internal links to content.

        Replaces link placeholders with actual URLs from config.

        Args:
            content: Blog post content with link placeholders

        Returns:
            Content with internal links inserted

        Example:
            >>> content = "See [產品比較] for details"
            >>> drafter.insert_internal_links(content)
            "See [產品比較](https://svicloudtvbox.us/compare) for details"
        """
        replacements = self.config.get("content_inputs", {}).get("internal_links", {})

        for placeholder, url in replacements.items():
            marker = f"[{placeholder}]"
            if marker in content:
                content = content.replace(marker, f"[{placeholder}]({url})")

        return content

    def ensure_internal_links(self, content: str, min_links: int = 3) -> str:
        """Guarantee that the article surfaces enough internal links.

        Adds a recommended reading block if fewer than min_links internal
        URLs for svicloudtvbox.us exist in the content.
        """
        domain = "svicloudtvbox.us"
        existing_links = re.findall(rf"https?://{re.escape(domain)}/[^\s\)\]]+", content)

        if len(existing_links) >= min_links:
            return content

        targets = self.config.get("content_inputs", {}).get("internal_link_targets", [])
        if not targets:
            return content

        used_urls = set(existing_links)
        link_lines: List[str] = []

        for target in targets:
            url = target.get("url")
            if not url or url in used_urls:
                continue

            label = target.get("label") or target.get("text") or "SVICLOUD"
            description = target.get("description")
            line = f"- [{label}]({url})"
            if description:
                line += f" — {description}"

            link_lines.append(line)
            used_urls.add(url)

            if len(used_urls) >= min_links:
                break

        if not link_lines:
            return content

        block_title = "### 延伸閱讀推薦"
        block = "\n".join([block_title] + link_lines) if block_title not in content else "\n".join(link_lines)
        return f"{content.rstrip()}\n\n{block}\n"

    def inject_images(
        self,
        content: str,
        topic: TopicCandidate,
        brief: str,
        outline: str,
    ) -> Tuple[str, Optional[Dict[str, Any]]]:
        """Add hero images with optional OpenAI generation."""
        hero_meta: Optional[Dict[str, Any]] = None

        if self.image_generator:
            hero_meta = self.image_generator.generate(topic, brief, outline)
            if hero_meta:
                block = f"![{hero_meta['alt']}]({hero_meta['placeholder']})"
                if hero_meta.get("caption"):
                    block = f"{block}\n*{hero_meta['caption']}*"
                return f"{block}\n\n{content}", hero_meta

        images_cfg = self.config.get("content_inputs", {}).get("images", {})
        topic_type = (topic.metadata.get("topic_type") or "default").lower()
        image_entries = images_cfg.get(topic_type) or images_cfg.get("default") or []

        blocks = []
        for entry in image_entries:
            url = entry.get("url")
            alt = entry.get("alt") or "SVICLOUD"
            caption = entry.get("caption")

            if not url:
                continue

            block = f"![{alt}]({url})"
            if caption:
                block = f"{block}\n*{caption}*"

            blocks.append(block)

        if not blocks:
            return content, hero_meta

        hero = "\n\n".join(blocks)

        if hero not in content:
            return f"{hero}\n\n{content}", hero_meta

        return content, hero_meta

    def append_cta(self, content: str, topic: TopicCandidate) -> str:
        """Add call-to-action footer to content.

        Appends standard CTA with contact information and brand messaging.

        Args:
            content: Blog post content
            topic: TopicCandidate with metadata

        Returns:
            Content with CTA footer appended

        Example:
            >>> content = "# Post\\n\\nContent..."
            >>> drafter.append_cta(content, topic)
            "# Post\\n\\nContent...\\n\\n## 下一步：美國本地SVICLOUD服務..."
        """
        voice = self.config.get("brand_voice", {})
        title = voice.get("cta_title", "下一步：體驗 SVICLOUD 旗艦盒")
        point_lines = voice.get("cta_points", [
            "預約 4K HDR + Dolby Audio 示範",
            "取得 Wi-Fi 6 擺位與 Mesh 建議",
            "請專人代為設定 App 與語音搜尋",
        ])
        tagline = voice.get(
            "cta_tagline",
            "填寫表單或致電 702-398-3416，由 SVICLOUD 美國顧問協助規劃。"
        )
        points = "\n".join(f"- {line}" for line in point_lines)
        contact = (
            "- 電話：702-398-3416\n"
            "- Email：support@svicloudtvbox.us\n"
            "- [線上表單](https://svicloudtvbox.us/zh/contact/)"
        )
        cta = (
            f"## {title}\n"
            f"{points}\n"
            f"{tagline}\n"
            f"{contact}"
        )

        # Add CTA if not already present
        if cta not in content:
            content = f"{content}\n\n{cta}"

        return content

    def _generate_fallback_post(self, topic: TopicCandidate) -> str:
        """Generate template-based post when Claude unavailable.

        Args:
            topic: TopicCandidate with metadata

        Returns:
            Template-based blog post content
        """
        sections = self._build_outline_sections(topic)
        paragraphs = []

        for section in sections:
            paragraph = self._build_section_paragraph(section, topic)
            paragraphs.append(paragraph)

        return "\n\n".join(paragraphs)

    def ensure_minimum_length(
        self,
        topic: TopicCandidate,
        outline: str,
        brief: str,
        content: str
    ) -> str:
        """Extend content until it satisfies minimum length requirements."""
        min_length = int(self.config.get("quality", {}).get("min_length", 4000))
        if len(content) >= min_length:
            return content

        claude_model = self.config.get("apis", {}).get("claude_model_full")

        if self.claude and claude_model:
            try:
                extension_prompt = (
                    "以下文章長度不足4000字元，請延伸內容，補強案例、數據與FAQ，"
                    "新增段落須維持繁體中文，並自然融入SVICLOUD品牌優勢與至少一個內部連結文字描述。\n"
                    f"關鍵字：{topic.keyword}\n"
                    f"簡報重點：{brief[:800]}\n"
                    f"原始大綱：\n{outline[:1000]}\n"
                    "請撰寫額外3-4個段落，含子標題與具體故事或數字，輸出純Markdown："
                )
                extension = self._claude_complete(
                    model=claude_model,
                    system_prompt="你是 SVICLOUD 內容編輯，需延伸既有文章。",
                    user_prompt=extension_prompt,
                    max_tokens=1600,
                    temperature=0.4,
                )

                if extension and extension.strip():
                    content = f"{content.rstrip()}\n\n{extension.strip()}"

            except Exception as exc:
                self.log.warning("Claude length extension failed: %s", exc)

        if len(content) >= min_length:
            return content

        filler = self._build_length_padding(topic)
        return f"{content.rstrip()}\n\n{filler}"

    def _build_outline_sections(self, topic: TopicCandidate) -> List[str]:
        """Build standard outline sections based on topic metadata.

        Args:
            topic: TopicCandidate with metadata

        Returns:
            List of section titles
        """
        metadata = topic.metadata or {}

        sections = [
            "SVICLOUD 美國本地服務優勢",
            "型號/方案與適用族群",
            "安裝步驟與售後支援",
        ]

        if metadata.get("is_comparison"):
            sections.insert(1, "SVICLOUD 與競品差異")

        if metadata.get("is_geo"):
            sections.append(f"{metadata.get('geo_target')} 客戶專屬物流與體驗")

        if metadata.get("is_campaign"):
            sections.append("活動/節慶使用情境")

        sections.append("常見問題與下一步")

        return sections

    def _build_section_paragraph(self, section_title: str, topic: TopicCandidate) -> str:
        metadata = topic.metadata or {}
        body = [
            f"## {section_title}",
            self._select_feature_statement(section_title, topic)
        ]
        if "競品" in section_title or metadata.get("is_comparison"):
            body.append(
                "從處理器、Wi-Fi 規格、內容來源與售後服務切入，說明 SVICLOUD 與其他串流盒的差異。"
            )
        if metadata.get("is_geo"):
            geo = metadata.get("geo_target", "美國主要城市")
            body.append(
                f"針對{geo}用戶，分享 Wi-Fi 6 擺位、Mesh 建議與熱門華語/英文 App 組合，確保全家順暢觀看。"
            )
        if metadata.get("is_campaign"):
            body.append("結合節慶或派對情境，提供歌單、互動遊戲與設備建議。")
        if metadata.get("topic_type") == "faq" or "常見問題" in section_title:
            body.append("整理付款、保固、更新與遠端協助流程，降低使用門檻。")
        return "\n\n".join(body)

    def _select_feature_statement(self, section_title: str, topic: TopicCandidate) -> str:
        voice = self.config.get("brand_voice", {})
        statements_cfg = voice.get("feature_statements", {})
        metadata = topic.metadata or {}
        pool: List[str] = []

        comparison_statements = statements_cfg.get("comparison", []) or []
        geo_statements = statements_cfg.get("geo", []) or []
        core_statements = statements_cfg.get("core", []) or []
        official_summary = metadata.get("official_summary")
        if official_summary:
            pool.append(official_summary)

        if metadata.get("is_comparison") or "競品" in section_title:
            pool.extend(comparison_statements)

        if metadata.get("is_geo"):
            geo = metadata.get("geo_target") or "美國"
            for statement in geo_statements:
                pool.append(statement.replace("{geo}", geo))

        pool.extend(core_statements)

        if not pool:
            return "SVICLOUD 10P+ 搭載 4K HDR、Dolby Audio、Wi-Fi 6 與 Google Play 認證，兼顧畫質與 App 兼容性。"

        index = abs(hash(f"{section_title}-{topic.keyword}")) % len(pool)
        return pool[index]

    def _build_length_padding(self, topic: TopicCandidate) -> str:
        """Generate deterministic fallback sections to pad content length."""
        filler_sections = [
            "SVICLOUD 客戶回饋與案例延伸",
            "美國售後支援流程詳解",
            "常見問題與選購建議"
        ]
        paragraphs = [self._build_section_paragraph(title, topic) for title in filler_sections]
        paragraphs.append(
            "## 延伸服務與支援\n"
            "想要安排遠端協助、售後維修或內容設定，請透過 [SVICLOUD 客服中心](https://svicloudtvbox.us/zh/contact/) 預約專人協助。"
        )
        return "\n\n".join(paragraphs)

    def _get_full_post_prompt(self, topic: TopicCandidate) -> str:
        """Get system prompt for full post generation.

        Args:
            topic: TopicCandidate with metadata

        Returns:
            System prompt string for Claude
        """
        metadata = topic.metadata or {}
        topic_type = metadata.get("topic_type", "pillar")
        geo = metadata.get("geo_target") or "全美"

        base_prompt = "你是 SVICLOUD 中文內容顧問，撰寫4000字以上Markdown文章，需含實例與內部連結，避免提及配送時效。"

        # Add topic-specific modifiers
        modifiers = []

        if topic_type == "comparison":
            modifiers.append("比較SVICLOUD與競品的頻道數、保固、售後。")

        if topic_type == "geo":
            modifiers.append(f"聚焦{geo}地區的內容體驗、安裝與售後需求，避開配送承諾。")

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
