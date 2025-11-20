"""SEO title generation stage."""

from __future__ import annotations

import json
import logging
import random
from typing import Any, Dict, List, Optional

from ..models import TopicCandidate


class TitleGenerator:
    """Generates SEO-friendly titles for each post."""

    def __init__(self, claude_client: Any, config: Dict[str, Any], logger: logging.Logger):
        self.claude = claude_client
        self.config = config
        self.log = logger

    def generate_title(self, topic: TopicCandidate, brief: str, outline: str) -> Dict[str, Any]:
        """Generate a headline from Claude or fallback templates."""
        seo_cfg = self.config.get("seo", {}).get("title", {})
        claude_model = seo_cfg.get("claude_model")
        candidates: List[str] = []

        if self.claude and claude_model:
            try:
                response = self._claude_titles(topic, brief, outline, claude_model)
                if response:
                    candidates = response
            except Exception as exc:  # pragma: no cover - network
                self.log.warning("Claude title generation failed: %s", exc)

        if not candidates:
            candidates = self._fallback_titles(topic, seo_cfg)

        title = candidates[0]
        max_len = int(seo_cfg.get("max_length", 32))
        if len(title) > max_len:
            title = title[:max_len]

        result = {
            "title": title,
            "candidates": candidates,
        }
        return result

    def _claude_titles(
        self,
        topic: TopicCandidate,
        brief: str,
        outline: str,
        model: str,
    ) -> Optional[List[str]]:
        metadata = topic.metadata or {}
        geo = metadata.get("geo_target") or "美國"
        topic_type = metadata.get("topic_type") or "pillar"
        prompt = (
            "請根據下列資訊產出3個繁體中文SEO標題，每個<=26字，需包含關鍵字，"
            "語氣自然易讀，避免使用英文與重複結尾詞彙，不要全部以『SVICLOUD 專家指南』結尾。"
            "請回傳JSON格式：{\"titles\":[{\"text\":\"...\",\"reason\":\"...\"}]}\n"
            f"關鍵字：{topic.keyword}\n"
            f"主題類型：{topic_type}\n"
            f"地區：{geo}\n"
            f"內容提要：{brief[:800]}\n"
            f"大綱：{outline[:800]}"
        )

        completion = self.claude.messages.create(  # type: ignore[call-arg]
            model=model,
            system="你是SEO文案專家，需輸出JSON資料，並讓標題聚焦消費者需求。",
            max_tokens=400,
            temperature=0.4,
            messages=[{"role": "user", "content": prompt}],
        )

        text = "".join(block.text for block in completion.content if getattr(block, "type", "") == "text")
        cleaned = text.strip()
        if "```" in cleaned:
            start = cleaned.find("```")
            fence = cleaned[start + 3:]
            if fence.startswith("json"):
                fence = fence[4:]
            end = fence.find("```")
            if end > -1:
                cleaned = fence[:end].strip()
        cleaned = cleaned.strip("`").strip()
        first = cleaned.find("{")
        last = cleaned.rfind("}")
        if first >= 0 and last > first:
            cleaned = cleaned[first : last + 1]

        try:
            data = json.loads(cleaned)
            titles = [item.get("text") for item in data.get("titles", []) if item.get("text")]
            return [t for t in titles if isinstance(t, str)]
        except json.JSONDecodeError:
            self.log.warning("Title JSON parse failed: %s", cleaned[:200])
            return None

    def _fallback_titles(self, topic: TopicCandidate, seo_cfg: Dict[str, Any]) -> List[str]:
        metadata = topic.metadata or {}
        geo = metadata.get("geo_target") or ""
        topic_type = metadata.get("topic_type") or ""
        templates = seo_cfg.get("fallback_templates") or [
            "{keyword}｜{geo}家庭影音攻略",
            "{keyword} 實測心得：{benefit}",
            "{keyword} {descriptor}：2025 完整指南",
        ]
        benefits = seo_cfg.get("benefits") or [
            "4K HDR 串流",
            "Wi-Fi 6 雙頻",
            "Dolby Audio",
            "語音遙控",
        ]
        descriptors = seo_cfg.get("descriptors") or [
            "懶人包",
            "入門指南",
            "最新價格",
            "安裝教學",
        ]

        geo_text = geo if geo else "美國"
        benefit = random.choice(benefits)
        descriptor = random.choice(descriptors)

        def render(template: str) -> str:
            return template.format(
                keyword=topic.keyword,
                geo=geo_text,
                topic_type=topic_type or "",
                benefit=benefit,
                descriptor=descriptor,
            )

        titles = [render(tpl) for tpl in templates]
        unique = []
        for title in titles:
            if title not in unique:
                unique.append(title)
        return unique
