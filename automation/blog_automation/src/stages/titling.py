"""SEO title generation stage."""

from __future__ import annotations

import json
import logging
import random
import re
from datetime import datetime
from difflib import SequenceMatcher
from typing import Any, Dict, List, Optional

from ..models import TopicCandidate


class TitleGenerator:
    """Generates SEO-friendly titles for each post."""

    def __init__(self, claude_client: Any, config: Dict[str, Any], logger: logging.Logger):
        self.claude = claude_client
        self.config = config
        self.log = logger
        self.recent_titles: List[str] = []

    def set_recent_titles(self, titles: List[str]) -> None:
        limit = int(self.config.get("seo", {}).get("title", {}).get("recent_window", 30))
        trimmed = [t for t in titles if isinstance(t, str) and t.strip()]
        self.recent_titles = trimmed[:limit] if limit > 0 else trimmed

    def add_recent_title(self, title: str) -> None:
        if isinstance(title, str) and title.strip():
            self.recent_titles.insert(0, title.strip())
            limit = int(self.config.get("seo", {}).get("title", {}).get("recent_window", 30))
            if limit > 0:
                self.recent_titles = self.recent_titles[:limit]

    def generate_title(self, topic: TopicCandidate, brief: str, outline: str) -> Dict[str, Any]:
        """Generate a headline from Claude or fallback templates."""
        seo_cfg = self.config.get("seo", {}).get("title", {})
        claude_model = seo_cfg.get("claude_model")
        candidates: List[str] = []

        configured_max = int(seo_cfg.get("max_length", 32))
        min_zh = int(seo_cfg.get("min_length_zh", 30))

        def has_cjk(text: str) -> bool:
            return any("\u4e00" <= ch <= "\u9fff" for ch in (text or ""))

        max_len = configured_max
        if has_cjk(topic.keyword):
            max_len = max(max_len, min_zh)

        if self.claude and claude_model:
            try:
                response = self._claude_titles(topic, brief, outline, claude_model, max_len=max_len)
                if response:
                    candidates = response
            except Exception as exc:  # pragma: no cover - network
                self.log.warning("Claude title generation failed: %s", exc)

        if not candidates:
            candidates = self._fallback_titles(topic, seo_cfg)

        # Avoid overly short zh title caps that cause awkward truncation (e.g., ending on "電")
        if has_cjk("".join(candidates)):
            max_len = max(max_len, min_zh)

        overused_tokens = seo_cfg.get("overused_tokens") or ["4K", "HDR", "Wi-Fi", "Dolby"]
        disallowed_tokens = seo_cfg.get("disallowed_tokens") or []
        allowed_topic_types = {
            (t or "").lower()
            for t in (seo_cfg.get("allow_disallowed_for_topic_types") or ["comparison", "campaign"])
        }
        topic_type = ((topic.metadata or {}).get("topic_type") or "pillar").lower()

        if disallowed_tokens and topic_type not in allowed_topic_types:
            filtered = [
                t for t in candidates
                if not any(tok and tok in t for tok in disallowed_tokens)
            ]
            if filtered:
                candidates = filtered
            else:
                def strip_disallowed(text: str) -> str:
                    cleaned = text
                    for tok in disallowed_tokens:
                        if tok:
                            cleaned = cleaned.replace(tok, "")
                    return cleaned

                stripped = [strip_disallowed(t) for t in candidates]
                stripped = [self._fix_dangling_suffix(self._sanitize_title(t), seo_cfg) for t in stripped]
                stripped = [t for t in stripped if t]
                if stripped:
                    candidates = stripped

        def overused_count(text: str) -> int:
            return sum(1 for tok in overused_tokens if tok and tok in text)

        def normalize_choice(text: str) -> str:
            return text.strip()

        def finalize(text: str) -> str:
            text = normalize_choice(text)
            if not text:
                return text
            if len(text) > max_len:
                text = self._trim_safely(text, max_len)
            text = self._sanitize_title(text)
            text = self._fix_dangling_suffix(text, seo_cfg)
            return text.strip()

        def is_unsafe_end(text: str) -> bool:
            if not text:
                return True
            unsafe_chars = set(seo_cfg.get("unsafe_trailing_chars") or [])
            default_unsafe = {"電", "視", "盒", "保", "固", "售", "後", "免", "運", "安", "裝", "+", "＋", "-", "—", "–"}
            unsafe = unsafe_chars or default_unsafe
            return text[-1] in unsafe

        finalized = [finalize(t) for t in candidates]
        finalized = [t for t in finalized if t]
        if not finalized:
            fallback = finalize(topic.keyword or "SVICLOUD")
            finalized = [fallback] if fallback else ["SVICLOUD"]

        recent_titles = [t for t in self.recent_titles if t]
        if recent_titles:
            threshold = float(seo_cfg.get("recent_similarity_threshold", 0.82))
            drop_tokens = seo_cfg.get("normalize_drop_tokens") or [
                "svicloud",
                "小雲",
                "小云",
                "10p+",
                "10p",
                "10s",
                "tvbox",
                "tv box",
                "電視盒",
                "电视盒",
            ]
            normalized_recent = [self._normalize_title(t, drop_tokens) for t in recent_titles]

            def is_similar(candidate: str) -> bool:
                norm = self._normalize_title(candidate, drop_tokens)
                if not norm:
                    return False
                return any(
                    SequenceMatcher(None, norm, ref).ratio() >= threshold
                    for ref in normalized_recent
                    if ref
                )

            filtered = [t for t in finalized if not is_similar(t)]
            if filtered:
                finalized = filtered
            else:
                self.log.warning("All title candidates too similar to recent titles; keeping best available.")

        # Prefer candidates that avoid unsafe endings, avoid overused tokens, and stay concise.
        seed = abs(hash(topic.keyword or "")) ^ int(datetime.utcnow().strftime("%Y%m%d"))
        rng = random.Random(seed)
        finalized.sort(key=lambda t: (is_unsafe_end(t), overused_count(t), len(t), rng.random()))
        title = finalized[0]

        result = {
            "title": title,
            "candidates": candidates,
            "slug_source": title,
        }
        return result

    def _normalize_title(self, title: str, drop_tokens: Optional[List[str]] = None) -> str:
        if not title:
            return ""
        text = title.lower()
        text = re.sub(r"<[^>]+>", "", text)
        if drop_tokens:
            for token in drop_tokens:
                if not token:
                    continue
                text = text.replace(str(token).lower(), "")
        text = re.sub(r"[\\s\\|｜:：,，。\\.\\-—–]+", "", text)
        return text.strip()

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
    def _fix_dangling_suffix(title: str, seo_cfg: Dict[str, Any]) -> str:
        """Remove awkward dangling fragments (e.g., ending with '免費安')."""
        import re

        text = (title or "").strip()
        if not text:
            return text

        # Trim trailing punctuation / symbols.
        text = re.sub(r"[\\s\\+\\-\\–\\—\\|｜：:,，、]+$", "", text).strip()
        if not text:
            return ""

        unsafe_chars = set(seo_cfg.get("unsafe_trailing_chars") or [])
        default_unsafe = {"電", "視", "盒", "保", "固", "售", "後", "免", "運", "安", "裝"}
        unsafe = unsafe_chars or default_unsafe

        # If we end on an unsafe single-character fragment, try dropping the last segment after a separator.
        if text and text[-1] in unsafe:
            for sep in ("、", "，", " ", ":", "：", "|", "｜"):
                if sep in text:
                    head, tail = text.rsplit(sep, 1)
                    head = head.rstrip(sep).strip()
                    tail = tail.strip()
                    if head and (len(tail) <= 3 or (tail and tail[-1] in unsafe)):
                        text = head
                        break

        # Final cleanup: remove any remaining trailing unsafe chars.
        while text and text[-1] in unsafe:
            text = text[:-1].strip()
        return text

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
            if cleaned[-1] in {"電", "視", "盒", "保", "固", "售", "後", "免", "運", "安", "裝"}:
                cleaned = cleaned[:-1].strip()
        return cleaned or cutoff.strip()

    def _claude_titles(
        self,
        topic: TopicCandidate,
        brief: str,
        outline: str,
        model: str,
        *,
        max_len: int,
    ) -> Optional[List[str]]:
        metadata = topic.metadata or {}
        geo = metadata.get("geo_target") or "美國"
        topic_type = metadata.get("topic_type") or "pillar"
        disallowed_tokens = self.config.get("seo", {}).get("title", {}).get("disallowed_tokens") or []
        allowed_topic_types = {
            (t or "").lower()
            for t in (self.config.get("seo", {}).get("title", {}).get("allow_disallowed_for_topic_types") or ["comparison", "campaign"])
        }
        disallow_clause = ""
        if disallowed_tokens and str(topic_type).lower() not in allowed_topic_types:
            disallow_clause = f"避免在標題中出現以下字詞：{', '.join(map(str, disallowed_tokens))}。"
        recent_clause = ""
        recent_titles = [t for t in self.recent_titles if t][:6]
        if recent_titles:
            recent_list = "\n".join(f"- {t}" for t in recent_titles)
            recent_clause = f"\n避免與以下近期標題過於相似：\n{recent_list}\n"
        prompt = (
            "請根據下列資訊產出3個繁體中文SEO標題，需包含關鍵字，"
            "語氣自然易讀，避免使用英文與重複結尾詞彙，不要全部以『SVICLOUD 專家指南』結尾。"
            f"每個標題長度請控制在 <= {max_len} 字，且不要以單一字/不完整詞結尾。"
            f"{disallow_clause}"
            f"{recent_clause}"
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
        seed = abs(hash(f"{topic.keyword}|{geo_text}|{topic_type}")) ^ int(datetime.utcnow().strftime("%Y%m%d"))

        def pick(seq: List[str], idx: int) -> str:
            if not seq:
                return ""
            return seq[(seed + idx) % len(seq)]

        def render(template: str, idx: int) -> str:
            benefit = pick(benefits, idx)
            descriptor = pick(descriptors, idx * 3 + 1)
            return template.format(
                keyword=topic.keyword,
                geo=geo_text,
                topic_type=topic_type or "",
                benefit=benefit,
                descriptor=descriptor,
            )

        titles = [render(tpl, idx) for idx, tpl in enumerate(templates)]
        unique = []
        for title in titles:
            if title not in unique:
                unique.append(title)
        return unique
