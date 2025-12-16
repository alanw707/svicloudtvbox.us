"""Blog automation orchestrator.

This module coordinates the full blog generation pipeline by integrating
all stage modules and client services.
"""

import json
import logging
import os
import re
from datetime import datetime, timezone, timedelta
from pathlib import Path
from typing import Dict, Any, List, Optional, Iterable
from difflib import SequenceMatcher

import yaml
import requests

from .models import TopicCandidate, GeneratedPost
from .database import DatabaseManager
from .clients.openai_client import OpenAIClient
from .clients.email import EmailClient
from .clients.image_generator import HeroImageGenerator
from .stages.discovery import TopicDiscovery
from .stages.briefing import BriefGenerator
from .stages.outlining import OutlineGenerator
from .stages.quality import QualityValidator
from .stages.drafting import PostDrafter
from .stages.publishing import Publisher
from .stages.titling import TitleGenerator

try:
    from anthropic import Anthropic
except ImportError:
    Anthropic = None  # type: ignore


class BlogAutomation:
    """Main blog automation orchestrator.

    Coordinates topic discovery, content generation, quality validation,
    and publishing pipeline with all stage modules and client services.
    """

    def __init__(self, config_path: Path, dry_run: bool = False) -> None:
        """Initialize blog automation orchestrator.

        Args:
            config_path: Path to YAML configuration file
            dry_run: If True, simulate operations without actual publishing

        Raises:
            FileNotFoundError: If config file doesn't exist
        """
        self.config_path = config_path
        self.config = self._load_config(config_path)
        self.base_dir = config_path.parent

        # Apply dry run setting
        safety = self.config.get("safety", {})
        self.dry_run = dry_run or safety.get("dry_run", False)

        # Setup logging
        self.log = self._setup_logging()
        self._lock_path: Optional[Path] = None
        self._lock_acquired = False

        # Initialize database
        self.db = self._init_database()

        # Initialize Claude client
        self.claude = self._init_claude_client()

        # Initialize OpenAI client
        self.openai = self._init_openai_client()

        # Initialize Email client
        self.email = self._init_email_client()

        # Media generator
        hero_cfg = self.config.get("content_inputs", {}).get("hero_images", {})
        self.hero_images = HeroImageGenerator(hero_cfg, self.log)

        # Initialize all pipeline stages
        self.discovery = TopicDiscovery(
            config=self.config,
            logger=self.log,
            base_dir=self.base_dir
        )

        self.briefing = BriefGenerator(
            claude_client=self.claude,
            config=self.config,
            logger=self.log
        )

        self.outlining = OutlineGenerator(
            claude_client=self.claude,
            config=self.config,
            logger=self.log
        )

        self.titles = TitleGenerator(
            claude_client=self.claude,
            config=self.config,
            logger=self.log
        )

        self.drafting = PostDrafter(
            claude_client=self.claude,
            config=self.config,
            logger=self.log,
            image_generator=self.hero_images
        )

        self.quality = QualityValidator(
            claude_client=self.claude,
            config=self.config,
            logger=self.log
        )

        self.publisher = Publisher(
            config=self.config,
            logger=self.log,
            dry_run=self.dry_run
        )

        self.log.info("BlogAutomation ready (dry_run=%s)", self.dry_run)

    def run(self, max_posts: Optional[int] = None) -> None:
        """Execute full automation pipeline.

        Discovers topics, filters duplicates, generates posts, validates quality,
        and publishes or saves drafts based on quality scores.

        Args:
            max_posts: Maximum number of posts to generate (overrides config)

        Example:
            >>> automation = BlogAutomation(Path("config.yaml"))
            >>> automation.run(max_posts=3)
        """
        if self._emergency_stop():
            return

        if not self._acquire_lock():
            return

        try:
            # Discover topics
            topics = self.discovery.discover_topics()
            unique_topics = self.filter_duplicates(topics)
            self.log.info("Processing %s topics (max_posts=%s)", len(unique_topics), max_posts)

            # Cadence + quota gates
            now = datetime.now(timezone.utc)
            start_of_week = (now - timedelta(days=now.isoweekday() - 1)).replace(hour=0, minute=0, second=0, microsecond=0)
            weekly_cap = self.config.get("publishing", {}).get("max_posts_per_week", 2)
            per_run_cap = self.config.get("publishing", {}).get("max_posts_per_run", 1)
            already_created = self.db.count_wp_created_since(start_of_week.isoformat())
            remaining_week = max(0, weekly_cap - already_created)

            # Backlog visibility
            last_run_iso = self.db.last_run_at()
            backlog_windows = self._count_missed_windows(last_run_iso, now)
            if backlog_windows > 0:
                self.log.info("Detected %s missed scheduled windows since last run", backlog_windows)

            limit = min(per_run_cap, remaining_week)
            if max_posts is not None:
                limit = min(limit, max_posts)

            if limit <= 0:
                msg = f"Weekly cap reached (cap={weekly_cap}, created={already_created}). No new WordPress posts will be created this run."
                self.log.warning(msg)
                self.email.send_notice("SVICLOUD Autoblog cadence cap reached", msg)
                self.db.record_run(
                    topics_found=len(topics),
                    unique_topics=len(unique_topics),
                    posts_attempted=0,
                    posts_published=0,
                    posts_staged=0,
                    dry_run=self.dry_run,
                )
                return

            rest_cfg = self.config.get("wordpress", {}).get("rest", {})
            desired_wp_status = (rest_cfg.get("status") or "draft").lower()

            attempts = created = published = staged = 0

            # Pre-fetch remote posts for dedupe
            remote_posts = self._recent_remote_posts()
            local_recent = self.db.recent_posts()

            # Generate and publish posts
            for topic in unique_topics:
                if created >= limit:
                    break
                attempts += 1
                post = self.generate_post(topic)
                score = self.quality.validate_quality(post)

                if score < self.config.get("quality", {}).get("min_score", 80):
                    self.log.warning("Quality score %.1f below threshold for '%s'", score, post.title)
                    self._save_draft(post)
                    continue

                if self._is_duplicate_post(post, local_recent, remote_posts):
                    self.log.warning("Skipping '%s' due to duplicate detection", post.title)
                    self.email.send_notice(
                        "SVICLOUD Autoblog duplicate skipped",
                        f"Skipped post '{post.title}' (keyword='{post.keyword}') due to duplicate detection."
                    )
                    continue

                if self.dry_run:
                    self.log.info("[DRY] Would publish '%s' (score %.1f)", post.title, score)
                    created += 1
                    if desired_wp_status == "publish":
                        published += 1
                    else:
                        staged += 1
                    continue

                if self._keyword_recently_published(post.keyword):
                    self.log.warning("Skipping '%s' because keyword '%s' was already published recently", post.title, post.keyword)
                    continue

                if self.publisher.publish_post(post):
                    created += 1
                    wp_status = (post.frontmatter.get("wp_status") or desired_wp_status or "draft").lower()
                    if wp_status == "publish":
                        published += 1
                        self._record_post(post, status="published")
                    else:
                        staged += 1
                        self._record_post(post, status="staged")
                    self._publish_translations(post)
                else:
                    self._save_draft(post)

            # Record run statistics
            self.db.record_run(
                topics_found=len(topics),
                unique_topics=len(unique_topics),
                posts_attempted=attempts,
                posts_published=published,
                posts_staged=staged,
                dry_run=self.dry_run
            )

            # Send summary report
            self.email.send_report(run_stats={
                "topics_found": len(topics),
                "unique_topics": len(unique_topics),
                "attempts": attempts,
                "created": created,
                "published": published,
                "staged": staged,
                "dry_run": self.dry_run,
            })
            if created == 0:
                self.email.send_notice(
                    "SVICLOUD Autoblog skipped all candidates",
                    "No WordPress post was created this run (all candidates skipped or failed)."
                )
        finally:
            self._release_lock()

    def filter_duplicates(self, topics: Iterable[TopicCandidate]) -> List[TopicCandidate]:
        """Remove duplicate topics using database and embeddings.

        Filters out topics that:
        1. Already exist in posts_history (exact keyword match)
        2. Are semantically similar to existing posts (embedding similarity)

        Args:
            topics: Iterable of topic candidates

        Returns:
            List of unique topics after deduplication

        Example:
            >>> topics = discovery.discover_topics()
            >>> unique = automation.filter_duplicates(topics)
            >>> len(unique) < len(topics)
            True
        """
        topics_list = list(topics)
        existing_keywords = self.db.get_existing_keywords()

        if not topics_list:
            return []

        unique: List[TopicCandidate] = []
        dropped = 0
        embedding_threshold = self.config.get("quality", {}).get("duplicate_threshold", 0.85)
        embedding_cache: Dict[str, List[float]] = {}
        existing_embeddings = self.db.load_existing_embeddings() if self.openai else []

        for topic in topics_list:
            keyword_lower = topic.keyword.lower()

            # Check exact keyword match
            if keyword_lower in existing_keywords:
                dropped += 1
                continue

            # Check semantic similarity
            if existing_embeddings:
                embedding = embedding_cache.get(keyword_lower)
                if embedding is None:
                    embedding = self.openai.generate_embedding(topic.keyword)
                    if embedding:
                        embedding_cache[keyword_lower] = embedding

                if embedding and self.openai.is_duplicate(embedding, existing_embeddings, embedding_threshold):
                    dropped += 1
                    continue

            unique.append(topic)

        if dropped:
            self.log.info("Dropped %s topics already covered or too similar", dropped)
        return unique

    def generate_post(self, topic: TopicCandidate) -> GeneratedPost:
        """Execute multi-stage post generation pipeline.

        Pipeline stages:
        1. Brief generation (strategic content plan)
        2. Outline creation and validation
        3. Full post drafting
        4. Content enhancement (links, images, CTA)

        Args:
            topic: Topic candidate with keyword and metadata

        Returns:
            Generated post with content and metadata

        Example:
            >>> topic = TopicCandidate(keyword="SVICLOUD 10P+", metadata={})
            >>> post = automation.generate_post(topic)
            >>> len(post.content) > 4000
            True
        """
        # Stage 1: Brief
        brief = self.briefing.generate_brief(topic)

        # Stage 2: Outline
        outline = self.outlining.generate_outline(topic, brief)
        if not self.outlining.validate_outline(outline):
            self.log.warning("Outline validation failed for '%s', regenerating once", topic.keyword)
            outline = self.outlining.generate_outline(topic, brief)

        # Stage 3: Full post
        post_body = self.drafting.generate_full_post(topic, outline, brief)

        # Stage 4: Content enhancement
        post_body = self.drafting.enforce_requirements(topic, post_body)
        post_body = self.drafting.insert_internal_links(post_body)
        post_body, hero_meta = self.drafting.inject_images(post_body, topic, brief, outline)
        post_body = self.drafting.ensure_internal_links(post_body)
        post_body = self.drafting.append_cta(post_body, topic)

        title_info = self.titles.generate_title(topic, brief, outline)
        title = title_info.get("title") or f"{topic.keyword}"

        # Build post metadata
        excerpt = post_body.split("\n\n", maxsplit=1)[0][:160]
        slug_source = title_info.get("slug_source") or title
        slug = _slugify(slug_source)
        frontmatter = {
            "keyword": topic.keyword,
            "generated_at": datetime.now(timezone.utc).isoformat(),
            "outline": outline,
            "topic_type": topic.metadata.get("topic_type") if topic.metadata else None,
            "geo_target": topic.metadata.get("geo_target") if topic.metadata else None,
            "source_domain": topic.metadata.get("source_domain") if topic.metadata else None,
            "seo_title_candidates": title_info.get("candidates"),
        }
        if hero_meta:
            frontmatter["hero_image"] = hero_meta

        return GeneratedPost(
            title=title,
            slug=slug,
            content=post_body,
            excerpt=excerpt,
            keyword=topic.keyword,
            quality_score=0.0,
            frontmatter=frontmatter,
        )

    def _load_config(self, config_path: Path) -> Dict[str, Any]:
        """Load YAML configuration file.

        Args:
            config_path: Path to config file

        Returns:
            Configuration dictionary

        Raises:
            FileNotFoundError: If config file doesn't exist
        """
        if not config_path.exists():
            raise FileNotFoundError(f"Missing config file: {config_path}")
        with config_path.open("r", encoding="utf-8") as fp:
            config = yaml.safe_load(fp) or {}
        return config

    def _setup_logging(self) -> logging.Logger:
        """Setup logging configuration.

        Returns:
            Configured logger instance
        """
        log_level_str = self.config.get("logging", {}).get("level", "INFO")
        log_level = getattr(logging, log_level_str.upper(), logging.INFO)
        logging.basicConfig(
            level=log_level,
            format="%(asctime)s [%(levelname)s] %(message)s",
            datefmt="%Y-%m-%d %H:%M:%S"
        )
        logger = logging.getLogger("blog_automation")
        logger.setLevel(log_level)
        return logger

    def _init_database(self) -> DatabaseManager:
        """Initialize database connection.

        Returns:
            DatabaseManager instance
        """
        storage_cfg = self.config.get("storage", {})
        legacy_cfg = self.config.get("database", {})
        configured_path = storage_cfg.get("db_path") or legacy_cfg.get("path")
        default_path = "data/posts_history.db"

        db_path = self._resolve_path(configured_path or default_path)
        return DatabaseManager(db_path)

    def _init_claude_client(self) -> Optional[Any]:
        """Initialize Anthropic Claude client.

        Returns:
            Anthropic client instance or None if not configured

        Environment Variables:
            CLAUDE_API_KEY: Anthropic API key
        """
        if Anthropic is None:
            self.log.warning("anthropic package not installed; Claude features disabled")
            return None

        import os
        api_key = os.getenv("CLAUDE_API_KEY")
        if not api_key:
            self.log.warning("CLAUDE_API_KEY not set; Claude features disabled")
            return None

        return Anthropic(api_key=api_key)

    def _init_openai_client(self) -> Optional[OpenAIClient]:
        """Initialize OpenAI client for embeddings.

        Returns:
            OpenAIClient instance or None if not configured
        """
        embedding_model = self.config.get("apis", {}).get("openai_embedding", "text-embedding-3-small")
        return OpenAIClient(logger=self.log, model=embedding_model)

    def _init_email_client(self) -> EmailClient:
        """Initialize email notification client.

        Returns:
            EmailClient instance
        """
        return EmailClient(config=self.config, logger=self.log)

    def _save_draft(self, post: GeneratedPost) -> None:
        """Save post as draft and record in database.

        Args:
            post: Generated post that failed quality check
        """
        draft_dir = self._resolve_path(
            self.config.get("publishing", {}).get("draft_dir", "drafts")
        )
        self.publisher.save_draft(post, draft_dir)
        self._record_post(post, status="draft")

    def _record_post(self, post: GeneratedPost, status: str) -> None:
        """Record post in database with embedding.

        Args:
            post: Generated post to record
            status: Post status ("published" or "draft")
        """
        embedding_vector = None
        if self.openai:
            embedding_vector = self.openai.generate_embedding(post.keyword or post.title)

        self.db.record_post(post, status, embedding_vector)

    def _keyword_recently_published(self, keyword: Optional[str]) -> bool:
        """Check database for an existing keyword record to avoid duplicates."""
        if not keyword:
            return False
        return self.db.keyword_exists(keyword)

    def _emergency_stop(self) -> bool:
        """Check for emergency stop file.

        Returns:
            True if emergency stop is engaged, False otherwise
        """
        stop_file = self.config.get("safety", {}).get("emergency_stop_file")
        if not stop_file:
            return False

        path = self._resolve_path(stop_file)
        if path.exists():
            self.log.warning("Emergency stop engaged via %s", path)
            return True
        return False

    def _acquire_lock(self) -> bool:
        """Create a file lock to prevent overlapping automation runs."""
        safety_cfg = self.config.get("safety", {})
        lock_rel = safety_cfg.get("lock_file", "data/.automation.lock")
        max_age_minutes = float(safety_cfg.get("lock_timeout_minutes", 180))
        lock_path = self._resolve_path(lock_rel)
        lock_path.parent.mkdir(parents=True, exist_ok=True)

        if lock_path.exists():
            modified = datetime.fromtimestamp(lock_path.stat().st_mtime, timezone.utc)
            age_minutes = (datetime.now(timezone.utc) - modified).total_seconds() / 60
            if age_minutes < max_age_minutes:
                self.log.warning(
                    "Another automation run appears active (lock %s, age %.1f min)",
                    lock_path,
                    age_minutes,
                )
                return False
            self.log.warning(
                "Stale automation lock %s detected (age %.1f min > %.0f min). Removing.",
                lock_path,
                age_minutes,
                max_age_minutes,
            )
            try:
                lock_path.unlink()
            except FileNotFoundError:
                pass

        lock_path.write_text(f"{datetime.now(timezone.utc).isoformat()}|pid={os.getpid()}", encoding="utf-8")
        self._lock_path = lock_path
        self._lock_acquired = True
        self.log.debug("Automation lock acquired at %s", lock_path)
        return True

    def _release_lock(self) -> None:
        """Remove automation lock file if held."""
        if self._lock_acquired and self._lock_path:
            try:
                self._lock_path.unlink()
                self.log.debug("Automation lock %s released", self._lock_path)
            except FileNotFoundError:
                pass
            finally:
                self._lock_acquired = False
                self._lock_path = None

    def _resolve_path(self, relative: str) -> Path:
        """Resolve relative path to absolute path.

        Args:
            relative: Relative or absolute path string

        Returns:
            Absolute Path object
        """
        path = Path(relative)
        if not path.is_absolute():
            return (self.base_dir / path).resolve()
        return path

    def _count_missed_windows(self, last_run_iso: Optional[str], now: datetime) -> int:
        """Estimate missed scheduled windows since last run."""
        if not last_run_iso:
            return 0
        try:
            last_run = datetime.fromisoformat(last_run_iso)
        except Exception:
            return 0

        sched = self.config.get("publishing", {}).get("schedule", [])
        if not sched:
            return 0

        weekday_map = {
            "monday": 0,
            "tuesday": 1,
            "wednesday": 2,
            "thursday": 3,
            "friday": 4,
            "saturday": 5,
            "sunday": 6,
        }

        missed = 0
        day_cursor = last_run.date()
        while day_cursor <= now.date():
            for entry in sched:
                day_raw = (entry.get("day") or "").strip().lower()
                time_raw = (entry.get("time") or "").strip()
                if day_raw not in weekday_map or ":" not in time_raw:
                    continue
                hour, minute = [int(part) for part in time_raw.split(":", 1)]
                scheduled_dt = datetime.combine(day_cursor, datetime.min.time()).replace(
                    hour=hour, minute=minute, tzinfo=now.tzinfo
                )
                if last_run < scheduled_dt < now:
                    missed += 1
            day_cursor += timedelta(days=1)
        return missed

    def _recent_remote_posts(self, pages: int = 2, per_page: int = 10) -> List[Dict[str, Any]]:
        """Fetch recent WP posts for duplicate detection."""
        rest_cfg = self.config.get("wordpress", {}).get("rest", {})
        endpoint = rest_cfg.get("endpoint")
        username = rest_cfg.get("username")
        password_env = rest_cfg.get("password_env")
        password = os.getenv(password_env) if password_env else rest_cfg.get("password")
        if not endpoint:
            return []

        headers = {"Accept": "application/json"}
        auth = (username, password) if username and password else None
        collected: List[Dict[str, Any]] = []
        for page in range(1, pages + 1):
            params = {"per_page": per_page, "page": page, "_fields": "id,title,slug,content"}
            try:
                resp = requests.get(endpoint, params=params, headers=headers, auth=auth, timeout=20)
                if resp.status_code != 200:
                    self.log.debug("Remote posts fetch error %s: %s", resp.status_code, resp.text[:200])
                    break
                rows = resp.json()
                if not rows:
                    break
                for row in rows:
                    collected.append(
                        {
                            "id": row.get("id"),
                            "title": (row.get("title") or {}).get("rendered", ""),
                            "slug": row.get("slug", ""),
                            "content": (row.get("content") or {}).get("rendered", ""),
                        }
                    )
            except requests.RequestException as exc:
                self.log.debug("Remote posts fetch failed: %s", exc)
                break
        return collected

    def _is_duplicate_post(
        self,
        post: GeneratedPost,
        local_recent: List[Dict[str, Any]],
        remote_posts: List[Dict[str, Any]],
    ) -> bool:
        """Check for duplicate titles/slugs/content against local and remote sources."""
        threshold = float(self.config.get("quality", {}).get("duplicate_threshold", 0.85))

        existing_titles = [p.get("title", "") for p in local_recent]
        existing_slugs = {p.get("slug", "") for p in local_recent}
        remote_titles = [p.get("title", "") for p in remote_posts]
        remote_slugs = {p.get("slug", "") for p in remote_posts}

        # Exact slug collision
        if post.slug in existing_slugs or post.slug in remote_slugs:
            return True

        def similar(a: str, b: str) -> float:
            return SequenceMatcher(None, a or "", b or "").ratio()

        for title in existing_titles + remote_titles:
            if similar(post.title, title) >= threshold:
                return True

        # Embedding-based content similarity (local + remote)
        if self.openai:
            candidate_embedding = self.openai.generate_embedding((post.content or "")[:4000])
            if candidate_embedding:
                existing_embeddings = self.db.load_existing_embeddings()
                remote_embeddings: List[Dict[str, Any]] = []
                for remote in remote_posts[:10]:
                    plain = self._strip_html(remote.get("content", ""))[:4000]
                    if not plain:
                        continue
                    emb = self.openai.generate_embedding(plain)
                    if emb:
                        remote_embeddings.append({"embedding": emb})

                combined = existing_embeddings + remote_embeddings
                if self.openai.is_duplicate(candidate_embedding, combined, threshold):
                    return True
        return False

    def _publish_translations(self, post: GeneratedPost) -> None:
        """Generate translations and store into meta fields (single-post model)."""
        publishing_cfg = self.config.get("publishing", {})
        locales: List[str] = publishing_cfg.get("locales", []) or []
        if not locales or not publishing_cfg.get("publish_translations", False):
            return
        base_locale = publishing_cfg.get("base_locale")
        for locale in locales:
            if base_locale and locale == base_locale:
                continue
            translated = self._translate_for_locale(post, locale)
            if translated:
                self.publisher.update_translation_meta(
                    post_id=post.frontmatter.get("wp_post_id"),
                    locale=locale,
                    translation=translated,
                )

    def _translate_for_locale(self, post: GeneratedPost, locale: str) -> Optional[Dict[str, str]]:
        """Translate post content/title/excerpt into target locale."""
        if not self.claude:
            return None
        model = self.config.get("apis", {}).get("claude_model_full")
        if not model:
            return None

        locale_map = {
            "zh_TW": "Traditional Chinese (zh-TW)",
            "zh_CN": "Simplified Chinese (zh-CN)",
            "en_US": "English (en-US)",
        }
        target = locale_map.get(locale, locale)
        system = (
            "You are a precise localization assistant. Translate Markdown while keeping structure, headings, links, and URLs unchanged. "
            "Do not change code blocks or URLs. Keep brand terms (SVICLOUD, 10P+, 10S) as-is."
        )
        user_prompt = (
            f"Target language/locale: {target}\n"
            "Translate the following Markdown blog post. Preserve Markdown, headings, links, image URLs, CTA, and numeric data.\n\n"
            f"TITLE:\n{post.title}\n\n"
            f"CONTENT:\n{post.content}"
        )
        try:
            from anthropic import NOT_GIVEN  # type: ignore
        except Exception:
            NOT_GIVEN = None  # type: ignore
        try:
            completion = self.claude.messages.create(  # type: ignore[attr-defined]
                model=model,
                max_tokens=3000,
                temperature=0.1,
                system=system,
                messages=[{"role": "user", "content": user_prompt}],
            )
            translated_text = completion.content[0].text if completion and completion.content else None  # type: ignore
            if not translated_text:
                return None
            return {
                "title": post.title,  # keep original title unless caller overrides
                "content": translated_text.strip(),
                "excerpt": translated_text.strip().split("\n\n", 1)[0][:220],
            }
        except Exception as exc:
            self.log.warning("Translation failed for locale %s: %s", locale, exc)
            return None

    @staticmethod
    def _strip_html(html_text: str) -> str:
        if not html_text:
            return ""
        return re.sub(r"<[^>]+>", " ", html_text)

    def close(self) -> None:
        """Clean up resources."""
        self.db.close()


def _slugify(text: str) -> str:
    """Convert text to URL-safe slug.

    Args:
        text: Text to slugify

    Returns:
        URL-safe slug (max 80 characters)

    Example:
        >>> _slugify("SVICLOUD 10P+ 電視盒")
        "svicloud-10p-"
    """
    cleaned = "".join(ch if ch.isalnum() else "-" for ch in text)
    slug = "-".join(part for part in cleaned.split("-") if part)
    return slug.lower()[:80]
