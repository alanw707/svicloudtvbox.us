"""Hero image generation via OpenAI Images API."""

from __future__ import annotations

import base64
import logging
import os
import time
from io import BytesIO
from pathlib import Path
from typing import Any, Dict, Optional
from uuid import uuid4

try:
    from openai import OpenAI
except ImportError:  # pragma: no cover - optional dependency
    OpenAI = None  # type: ignore[assignment]

try:
    from PIL import Image
except ImportError:  # pragma: no cover - optional dependency
    Image = None  # type: ignore[assignment]

class HeroImageGenerator:
    """Generates unique hero images for blog posts."""

    def __init__(self, config: Dict[str, Any], logger: logging.Logger):
        self.log = logger
        self.cfg = config or {}
        provider = self.cfg.get("provider")
        self.enabled = provider == "openai"
        self.output_dir = Path(self.cfg.get("output_dir", "data/generated_images"))
        try:
            self.output_dir.mkdir(parents=True, exist_ok=True)
        except PermissionError as exc:
            self.log.warning("Hero image output_dir not writable (%s): %s", self.output_dir, exc)
            self.enabled = False
        openai_cfg = self.cfg.get("openai", {}) or {}
        self.model = openai_cfg.get("model", "gpt-image-1")
        self.size = openai_cfg.get("size", "1024x1024")
        self.output_format = (self.cfg.get("output_format") or "jpeg").lower()
        self.quality = int(self.cfg.get("quality", 82))
        self.max_width = int(self.cfg.get("max_width", 1200))
        self.max_height = int(self.cfg.get("max_height", 1200))
        self.prompt_template = self.cfg.get("prompt_template") or (
            "以{geo}家庭客廳為背景，呈現華人喜愛的串流影音氛圍，"
            "聚焦SVICLOUD小雲電視盒，展示{keyword}主題，強調{benefit}。"
            "使用寫實光線、暖色溫，方便用於行銷宣傳。"
        )

        self.client: Optional[Any] = None
        if self.enabled and OpenAI is not None:
            api_key = os.getenv("OPENAI_API_KEY")
            if api_key:
                project = os.getenv("OPENAI_PROJECT")
                self.client = OpenAI(api_key=api_key, project=project)
            else:
                self.log.warning("OPENAI_API_KEY not set; hero image generation disabled")
                self.enabled = False
        elif self.enabled:
            self.log.warning("openai package missing; hero image generation disabled")
            self.enabled = False

    def generate(self, topic, brief: str, outline: str) -> Optional[Dict[str, Any]]:
        """Generate hero image and return metadata."""
        if not self.enabled or not self.client:
            return None
        if not os.access(self.output_dir, os.W_OK):
            self.log.warning("Hero image output_dir not writable (%s); skipping image generation", self.output_dir)
            return None

        metadata = topic.metadata or {}
        geo = metadata.get("geo_target") or "美國"
        benefits = self.cfg.get("benefits") or [
            "4K HDR 串流",
            "Wi-Fi 6 雙頻",
            "Dolby Audio",
            "中文/英文語音遙控",
        ]
        benefit = benefits[hash(topic.keyword) % len(benefits)]

        prompt = self.prompt_template.format(
            keyword=topic.keyword,
            geo=geo,
            benefit=benefit,
        )

        payload = {
            "model": self.model,
            "prompt": prompt,
            "size": self.size,
        }
        try:
            response = self.client.images.generate(**payload)  # type: ignore[arg-type]
        except Exception as exc:  # pragma: no cover - network
            self.log.warning("OpenAI image generation failed: %s", exc)
            return None

        if not response.data:
            return None

        image_b64 = getattr(response.data[0], "b64_json", None)
        if not image_b64:
            return None

        image_bytes = base64.b64decode(image_b64)
        image_bytes, ext, mime_type = self._process_image(image_bytes)
        slug = self._slugify(topic.keyword)
        filename = f"{slug}-{int(time.time())}.{ext}"
        path = self.output_dir / filename
        try:
            path.write_bytes(image_bytes)
        except PermissionError as exc:
            self.log.warning("Failed to write hero image %s: %s", path, exc)
            return None

        placeholder = f"@@HERO::{uuid4()}@@"
        alt = f"{topic.keyword} 情境示意圖"
        caption = f"{geo}用戶真實環境示意"

        return {
            "path": str(path),
            "alt": alt,
            "caption": caption,
            "prompt": prompt,
            "placeholder": placeholder,
            "mime_type": mime_type,
        }

    def _slugify(self, text: str) -> str:
        cleaned = "".join(ch if ch.isalnum() else "-" for ch in text)
        slug = "-".join(part for part in cleaned.split("-") if part)
        return slug.lower()[:80]

    def _process_image(self, image_bytes: bytes) -> tuple[bytes, str, str]:
        if Image is None:
            self.log.warning("Pillow not installed; saving raw PNG output.")
            return image_bytes, "png", "image/png"

        fmt = self._normalize_format(self.output_format)
        if fmt in ("jpg", "jpeg"):
            pil_format = "JPEG"
            ext = "jpg"
            mime_type = "image/jpeg"
        elif fmt == "webp":
            pil_format = "WEBP"
            ext = "webp"
            mime_type = "image/webp"
        else:
            pil_format = "PNG"
            ext = "png"
            mime_type = "image/png"

        try:
            with Image.open(BytesIO(image_bytes)) as img:
                if pil_format in ("JPEG", "WEBP") and img.mode not in ("RGB", "L"):
                    img = img.convert("RGB")
                img.thumbnail((self.max_width, self.max_height), Image.LANCZOS)

                out = BytesIO()
                save_kwargs: Dict[str, Any] = {}
                if pil_format == "JPEG":
                    save_kwargs = {"quality": self.quality, "optimize": True, "progressive": True}
                elif pil_format == "WEBP":
                    save_kwargs = {"quality": self.quality, "method": 6}
                elif pil_format == "PNG":
                    save_kwargs = {"optimize": True}
                img.save(out, format=pil_format, **save_kwargs)
                return out.getvalue(), ext, mime_type
        except Exception as exc:  # pragma: no cover - defensive fallback
            self.log.warning("Failed to optimize hero image, saving raw PNG: %s", exc)
            return image_bytes, "png", "image/png"

    def _normalize_format(self, fmt: str) -> str:
        if fmt in ("jpg", "jpeg", "png", "webp"):
            return fmt
        return "jpeg"
