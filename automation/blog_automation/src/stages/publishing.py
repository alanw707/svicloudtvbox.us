"""Publishing stage for blog automation.

This module handles publishing blog posts to WordPress via REST API
and saving failed posts as draft files for manual review.
"""

import json
import logging
import os
import re
import mimetypes
from pathlib import Path
from typing import Dict, Any, List, Optional, Tuple
import requests
import markdown
from bs4 import BeautifulSoup
from ..models import GeneratedPost


class Publisher:
    """Publishes blog posts to WordPress and manages drafts.

    Supports WordPress REST API publishing with configurable status
    (publish/draft). Saves failed posts as JSON files for manual review.
    """

    def __init__(
        self,
        config: Dict[str, Any],
        logger: logging.Logger,
        dry_run: bool = False
    ):
        """Initialize publisher.

        Args:
            config: Configuration dictionary with WordPress settings
            logger: Logger instance for operations tracking
            dry_run: If True, simulate operations without actual publishing
        """
        self.config = config
        self.log = logger
        self.dry_run = dry_run

    def publish_post(self, post: GeneratedPost) -> bool:
        """Publish post to WordPress using configured method.

        Routes to appropriate publishing method based on config:
        - rest-api: WordPress REST API (default)
        - wp-cli: WP-CLI command line (not yet implemented)

        Args:
            post: GeneratedPost with content and metadata

        Returns:
            True if publishing succeeded, False otherwise

        Example:
            >>> success = publisher.publish_post(post)
            >>> if success:
            ...     print(f"Published: {post.title}")
        """
        method = self.config.get("wordpress", {}).get("method", "rest-api")

        if method == "rest-api":
            return self._publish_via_rest_api(post)

        if method == "wp-cli":
            return self._publish_via_wpcli(post)

        self.log.error("Unsupported publishing method: %s", method)
        return False

    def save_draft(self, post: GeneratedPost, draft_dir: Path) -> None:
        """Save post as draft JSON file for manual review.

        Creates draft directory if needed and saves post as JSON
        with all metadata and content.

        Args:
            post: GeneratedPost that failed quality check
            draft_dir: Directory path for draft files

        Example:
            >>> publisher.save_draft(post, Path("drafts"))
            # Creates: drafts/svicloud-10p-review.md
        """
        draft_dir.mkdir(parents=True, exist_ok=True)
        path = draft_dir / f"{post.slug}.md"

        payload = {
            "title": post.title,
            "excerpt": post.excerpt,
            "keyword": post.keyword,
            "frontmatter": post.frontmatter,
            "content": post.content,
        }

        path.write_text(
            json.dumps(payload, ensure_ascii=False, indent=2),
            encoding="utf-8"
        )

        self.log.info("Saved draft to %s", path)

    def publish_translation(
        self,
        post: GeneratedPost,
        locale: str,
        lang_field: str = "lang",
        translated_title: Optional[str] = None,
        translated_slug: Optional[str] = None,
        translated_content: Optional[str] = None,
        translated_excerpt: Optional[str] = None,
    ) -> Optional[int]:
        """Publish a translated variant of a post."""
        rest_cfg = self.config.get("wordpress", {}).get("rest", {})
        endpoint = rest_cfg.get("endpoint")
        username = rest_cfg.get("username")
        password_env = rest_cfg.get("password_env")
        password = os.getenv(password_env) if password_env else rest_cfg.get("password")
        status = rest_cfg.get("status", "draft")

        if not endpoint or not username or not password:
            self.log.error("REST API not fully configured (endpoint/username/password missing)")
            return None

        title = translated_title or post.title
        slug = translated_slug or f"{post.slug}-{locale.lower()}"

        markdown_source = translated_content or post.content
        html_content = self._markdown_to_html(markdown_source)
        excerpt_text = translated_excerpt or self._excerpt_from_html(html_content, keyword=post.keyword)

        payload = {
            "title": title,
            "slug": slug,
            "content": html_content,
            "excerpt": excerpt_text,
            "status": status,
            lang_field: locale,
        }

        if rest_cfg.get("author"):
            payload["author"] = rest_cfg["author"]

        selected_categories = self._select_categories_for_post(post)
        if selected_categories:
            payload["categories"] = selected_categories

        try:
            response = requests.post(
                endpoint,
                auth=(username, password),
                json=payload,
                timeout=30
            )

            if response.status_code >= 200 and response.status_code < 300:
                data = response.json()
                self.log.info("Published translation '%s' (%s) (WP ID %s)", title, locale, data.get("id"))
                return data.get("id")

            self.log.error("WordPress REST API error %s for locale %s: %s", response.status_code, locale, response.text[:500])
            return None

        except requests.RequestException as exc:
            self.log.error("Failed to call WordPress REST API for locale %s: %s", locale, exc)
            return None

    def update_translation_meta(
        self,
        post_id: Optional[int],
        locale: str,
        translation: Dict[str, str],
    ) -> bool:
        """Store translated content into meta fields on the base post."""
        if post_id is None:
            self.log.warning("Cannot update translation meta without post_id")
            return False

        suffix = self._locale_suffix(locale)
        if not suffix:
            self.log.warning("Unsupported locale for translation meta: %s", locale)
            return False

        rest_cfg = self.config.get("wordpress", {}).get("rest", {})
        endpoint = rest_cfg.get("endpoint")
        username = rest_cfg.get("username")
        password_env = rest_cfg.get("password_env")
        password = os.getenv(password_env) if password_env else rest_cfg.get("password")
        if not endpoint or not username or not password:
            self.log.error("REST API not fully configured (endpoint/username/password missing)")
            return False

        meta_payload = {
            f"_svic_content_{suffix}": translation.get("content", ""),
            f"_svic_title_{suffix}": translation.get("title", ""),
            f"_svic_description_{suffix}": translation.get("excerpt", ""),
        }

        url = f"{endpoint.rstrip('/')}/{post_id}"
        try:
            resp = requests.post(
                url,
                auth=(username, password),
                json={"meta": meta_payload},
                timeout=30,
            )
            if 200 <= resp.status_code < 300:
                self.log.info("Updated translation meta for post %s locale %s", post_id, locale)
                return True
            self.log.warning("Failed to update translation meta (%s): %s", resp.status_code, resp.text[:200])
            return False
        except requests.RequestException as exc:
            self.log.error("Translation meta update failed for post %s locale %s: %s", post_id, locale, exc)
            return False

    @staticmethod
    def _locale_suffix(locale: str) -> Optional[str]:
        mapping = {
            "zh_TW": "zh_tw",
            "zh_CN": "zh_cn",
            "en_US": "en_us",
        }
        return mapping.get(locale)

    def _publish_via_rest_api(self, post: GeneratedPost) -> bool:
        """Publish post via WordPress REST API.

        Converts Markdown to HTML, generates excerpt, and POSTs to
        WordPress /wp-json/wp/v2/posts endpoint.

        Args:
            post: GeneratedPost to publish

        Returns:
            True if publish succeeded, False otherwise
        """
        rest_cfg = self.config.get("wordpress", {}).get("rest", {})
        endpoint = rest_cfg.get("endpoint")
        username = rest_cfg.get("username")
        password_env = rest_cfg.get("password_env")
        password = os.getenv(password_env) if password_env else rest_cfg.get("password")
        status = rest_cfg.get("status", "draft")

        if not endpoint or not username or not password:
            self.log.error("REST API not fully configured (endpoint/username/password missing)")
            return False

        # Prepare hero media and convert Markdown to HTML
        content_markdown, featured_media_id = self._prepare_media_assets(
            post,
            post.content,
            rest_cfg,
            auth=(username, password),
        )
        html_content = self._markdown_to_html(content_markdown)
        excerpt_text = self._excerpt_from_html(html_content, keyword=post.keyword)

        # Build REST API payload
        payload = {
            "title": post.title,
            "slug": post.slug,
            "content": html_content,
            "excerpt": excerpt_text,
            "status": status,
        }

        # Apply base language if configured
        base_locale = self.config.get("publishing", {}).get("base_locale")
        lang_field = self.config.get("publishing", {}).get("locale_param", "lang")
        if base_locale:
            payload[lang_field] = base_locale

        # Add optional fields
        if rest_cfg.get("author"):
            payload["author"] = rest_cfg["author"]

        selected_categories = self._select_categories_for_post(post)
        if selected_categories:
            payload["categories"] = selected_categories

        if featured_media_id:
            payload["featured_media"] = featured_media_id

        # Dry run mode - simulate publishing
        if self.dry_run:
            self.log.info("[DRY] Would POST to %s with status '%s'", endpoint, status)
            self.log.debug("Payload: %s", payload)
            return True

        # Actual publishing
        try:
            response = requests.post(
                endpoint,
                auth=(username, password),
                json=payload,
                timeout=30
            )

            if response.status_code >= 200 and response.status_code < 300:
                data = response.json()
                post.frontmatter["wp_post_id"] = data.get("id")
                post.frontmatter["wp_status"] = data.get("status")
                self.log.info("Published draft '%s' (WP ID %s)", post.title, data.get("id"))
                return True

            self.log.error("WordPress REST API error %s: %s", response.status_code, response.text[:500])
            return False

        except requests.RequestException as exc:
            self.log.error("Failed to call WordPress REST API: %s", exc)
            return False

    def _publish_via_wpcli(self, post: GeneratedPost) -> bool:
        """Publish post via WP-CLI (not yet implemented).

        Placeholder for WP-CLI publishing method.

        Args:
            post: GeneratedPost to publish

        Returns:
            True in dry run, False otherwise
        """
        if self.dry_run:
            self.log.info("[DRY] WP-CLI publish stub for '%s'", post.title)
            return True

        self.log.info("WP-CLI publishing not yet implemented; leaving as draft")
        return False

    def _markdown_to_html(self, markdown_text: str) -> str:
        """Convert Markdown to HTML.

        Uses Python-Markdown with 'extra' and 'sane_lists' extensions.

        Args:
            markdown_text: Markdown content

        Returns:
            HTML content
        """
        try:
            return markdown.markdown(
                markdown_text,
                extensions=["extra", "sane_lists"]
            )
        except Exception as exc:
            self.log.warning("Markdown conversion failed: %s", exc)
            return markdown_text

    def _excerpt_from_html(
        self,
        html_text: str,
        length: int = 220,
        keyword: Optional[str] = None,
    ) -> str:
        """Extract text excerpt from HTML.

        Strips HTML tags and returns first N characters.

        Args:
            html_text: HTML content
            length: Maximum excerpt length (default: 220)

        Returns:
            Plain text excerpt
        """
        soup = BeautifulSoup(html_text, "html.parser")
        text = soup.get_text(separator=" ", strip=True)
        if text == "":
            return ""

        keyword = (keyword or "").strip()
        sentences = [
            segment.strip()
            for segment in re.split(r"(?<=[。！？!?\.])\s*", text)
            if segment.strip()
        ]
        sentences = [
            re.sub(r"^[✅✔\s]+", "", s)
            for s in sentences
            if not re.search(r"chinese title\s*[:：]", s, re.IGNORECASE)
        ]

        generic_markers = (
            "美國本地授權服務據點與雙語客服",
        )

        def is_generic(sentence: str) -> bool:
            return any(marker in sentence for marker in generic_markers)

        ordered: List[str] = []
        if keyword:
            ordered.extend([s for s in sentences if keyword in s])
        ordered.extend([s for s in sentences if not is_generic(s)])
        ordered.extend(sentences)

        result_parts: List[str] = []
        for sentence in ordered:
            if sentence in result_parts:
                continue
            result_parts.append(sentence)
            if len("".join(result_parts)) >= length:
                break

        excerpt = "".join(result_parts).strip() or text
        return excerpt[:length]

    def _prepare_media_assets(
        self,
        post: GeneratedPost,
        markdown_text: str,
        rest_cfg: Dict[str, Any],
        auth: tuple[str, str],
    ) -> Tuple[str, Optional[int]]:
        """Upload hero image (if configured) and replace placeholders."""
        hero = (post.frontmatter or {}).get("hero_image") or {}
        image_path = hero.get("path")
        placeholder = hero.get("placeholder")

        if not image_path:
            return markdown_text, None

        upload = self._upload_media(
            image_path,
            hero.get("alt") or post.title,
            rest_cfg,
            auth,
        )

        if not upload:
            return markdown_text, None

        source_url = upload.get("source_url")
        media_id = upload.get("id")
        if source_url and placeholder:
            markdown_text = markdown_text.replace(placeholder, source_url, 1)
        hero.update({"uploaded_url": source_url, "wp_media_id": media_id})
        return markdown_text, media_id

    def _upload_media(
        self,
        file_path: str,
        title: str,
        rest_cfg: Dict[str, Any],
        auth: tuple[str, str],
    ) -> Optional[Dict[str, Any]]:
        """Upload a local file to WordPress media library."""
        media_endpoint = rest_cfg.get("media_endpoint") or self._derive_media_endpoint(rest_cfg.get("endpoint"))
        if not media_endpoint:
            self.log.warning("Media endpoint missing; cannot upload %s", file_path)
            return None

        path = Path(file_path)
        if not path.exists():
            self.log.warning("Hero image path does not exist: %s", path)
            return None

        content_type, _ = mimetypes.guess_type(path.name)
        files = {"file": (path.name, path.read_bytes(), content_type or "application/octet-stream")}
        data = {"title": title}

        try:
            response = requests.post(
                media_endpoint,
                auth=auth,
                files=files,
                data=data,
                timeout=60,
            )
            if 200 <= response.status_code < 300:
                return response.json()
            self.log.warning("Media upload failed (%s): %s", response.status_code, response.text[:200])
        except requests.RequestException as exc:
            self.log.warning("Media upload error: %s", exc)
        return None

    def _derive_media_endpoint(self, endpoint: Optional[str]) -> Optional[str]:
        if not endpoint:
            return None
        clean = endpoint.rstrip("/")
        if clean.endswith("/posts"):
            base = clean.rsplit("/posts", 1)[0]
            return f"{base}/media"
        return f"{clean}/media"

    def _select_categories_for_post(self, post: GeneratedPost) -> List[int]:
        """Select WordPress category IDs for post.

        Maps topic_type from frontmatter to category IDs from config.

        Args:
            post: GeneratedPost with frontmatter metadata

        Returns:
            List of WordPress category IDs

        Example:
            >>> post.frontmatter["topic_type"] = "comparison"
            >>> publisher._select_categories_for_post(post)
            [5, 12]  # Category IDs from config
        """
        category_map = self.config.get("wordpress", {}).get("category_map", {})
        topic_type = (post.frontmatter.get("topic_type") or "default").lower()

        selected = category_map.get(topic_type) or category_map.get("default") or []

        # Normalize to integers
        normalized: List[int] = []
        for value in selected:
            if isinstance(value, int):
                normalized.append(value)
            else:
                try:
                    normalized.append(int(value))
                except (ValueError, TypeError):
                    continue

        return normalized
