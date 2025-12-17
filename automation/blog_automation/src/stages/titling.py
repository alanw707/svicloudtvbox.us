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

        configured_max = int(seo_cfg.get("max_length", 32))
        min_zh = int(seo_cfg.get("min_length_zh", 30))
        max_len = configured_max

        # Avoid overly short zh title caps that cause awkward truncation (e.g., ending on "電")
        if any("\u4e00" <= ch <= "\u9fff" for ch in "".join(candidates)):
            max_len = max(max_len, min_zh)

        overused_tokens = seo_cfg.get("overused_tokens") or ["4K", "HDR", "Wi-Fi", "Dolby"]

        def overused_count(text: str) -> int:
            return sum(1 for tok in overused_tokens if tok and tok in text)

        def normalize_choice(text: str) -> str:
            return text.strip()

        # Prefer candidates that avoid overused tokens while respecting max length
        within = [normalize_choice(t) for t in candidates if len(t) <= max_len]
        if within:
            within.sort(key=lambda t: (overused_count(t), len(t)))
            title = within[0]
        else:
            trimmed = [self._trim_safely(normalize_choice(t), max_len) for t in candidates]
            trimmed.sort(key=lambda t: (overused_count(t), len(t)))
            title = trimmed[0]

        title = self._sanitize_title(title)

        result = {
            "title": title,
            "candidates": candidates,
            "slug_source": title,
        }
        return result

    @staticmethod
    def _sanitize_title(title: str) -> str:
        """Remove overly-specific shipping guarantees from titles (avoid misleading promises)."""
        import re

        text = (title or "").strip()
        if not text:
            return text

        # Replace explicit delivery/fulfillment timelines when tied to shipping verbs.
        # Examples: "48小時快速出貨" -> "快速出貨", "2天送達" -> "快速送達"
        text = re.sub(
            r"\\b\\d+\\s*(?:天|日|小時|hours?|hrs?)\\s*(?:內)?\\s*(?=(送達|到貨|出貨|配送))",
            "快速",
            text,
            flags=re.IGNORECASE,
        )
        # Normalize accidental double "快速快速"
        text = re.sub(r"(快速)\\1+", r"\\1", text)
        return text.strip()

    @staticmethod
    def _trim_safely(title: str, max_len: int) -> str:
        """Trim title at safe boundaries without leaving broken suffixes."""
        if len(title) <= max_len:
            return title
        cutoff = title[:max_len]
        # Prefer to cut at common separators if present
        for sep in ("｜", "|", "：", ":", "，", " ", "、"):
            if sep in cutoff:
                candidate = cutoff.rsplit(sep, 1)[0].strip()
                if candidate:
                    return candidate
        # Fallback: strip trailing non-alnum
        import re
        cleaned = re.sub(r"[^\w\u4e00-\u9fff]+$", "", cutoff)
        cleaned = cleaned.strip() or cutoff.strip()

        # If we still end in a single-character fragment and there is no separator,
        # back up a couple characters to avoid dangling compounds like "電視盒" -> "電".
        if len(cleaned) >= 2 and "｜" not in cleaned and "|" not in cleaned:
            if cleaned[-1] in {"電", "視", "盒", "保", "固", "售", "後", "免", "運"}:
                cleaned = cleaned[:-1].strip()
        return cleaned or cutoff.strip()

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
