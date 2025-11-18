"""Blog automation orchestrator.

This module coordinates the full blog generation pipeline by integrating
all stage modules and client services.
"""

import json
import logging
from datetime import datetime, timezone
from pathlib import Path
from typing import Dict, Any, List, Optional, Iterable

import yaml

from .models import TopicCandidate, GeneratedPost
from .database import DatabaseManager
from .clients.openai_client import OpenAIClient
from .clients.email import EmailClient
from .stages.discovery import TopicDiscovery
from .stages.briefing import BriefGenerator
from .stages.outlining import OutlineGenerator
from .stages.quality import QualityValidator
from .stages.drafting import PostDrafter
from .stages.publishing import Publisher

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

        # Initialize database
        self.db = self._init_database()

        # Initialize Claude client
        self.claude = self._init_claude_client()

        # Initialize OpenAI client
        self.openai = self._init_openai_client()

        # Initialize Email client
        self.email = self._init_email_client()

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

        self.drafting = PostDrafter(
            claude_client=self.claude,
            config=self.config,
            logger=self.log
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

        # Discover topics
        topics = self.discovery.discover_topics()
        unique_topics = self.filter_duplicates(topics)
        self.log.info("Processing %s topics (max_posts=%s)", len(unique_topics), max_posts)

        # Determine post limit
        publishing_cfg = self.config.get("publishing", {})
        weekly_cap = publishing_cfg.get("max_posts_per_week", 7)
        limit = max_posts or weekly_cap
        attempts = published = 0

        # Generate and publish posts
        for topic in unique_topics[:limit]:
            attempts += 1
            post = self.generate_post(topic)
            score = self.quality.validate_quality(post)

            if score < self.config.get("quality", {}).get("min_score", 80):
                self.log.warning("Quality score %.1f below threshold for '%s'", score, post.title)
                self._save_draft(post)
                continue

            if self.dry_run:
                self.log.info("[DRY] Would publish '%s' (score %.1f)", post.title, score)
                published += 1
                continue

            if self.publisher.publish_post(post):
                published += 1
                self._record_post(post, status="published")
            else:
                self._save_draft(post)

        # Record run statistics
        self.db.record_run(
            topics_found=len(topics),
            unique_topics=len(unique_topics),
            posts_attempted=attempts,
            posts_published=published,
            dry_run=self.dry_run
        )

        # Send summary report
        self.email.send_report(run_stats={
            "topics_found": len(topics),
            "unique_topics": len(unique_topics),
            "attempts": attempts,
            "published": published,
            "dry_run": self.dry_run,
        })

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
        post_body = self.drafting.inject_images(post_body, topic)
        post_body = self.drafting.append_cta(post_body, topic)

        # Build post metadata
        excerpt = post_body.split("\n\n", maxsplit=1)[0][:160]
        slug = _slugify(topic.keyword)
        frontmatter = {
            "keyword": topic.keyword,
            "generated_at": datetime.now(timezone.utc).isoformat(),
            "outline": outline,
            "topic_type": topic.metadata.get("topic_type") if topic.metadata else None,
            "geo_target": topic.metadata.get("geo_target") if topic.metadata else None,
            "source_domain": topic.metadata.get("source_domain") if topic.metadata else None,
        }

        return GeneratedPost(
            title=f"{topic.keyword}｜SVICLOUD 專家指南",
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
