#!/usr/bin/env python3
"""SVICLOUD autonomous Chinese blog automation pipeline."""
from __future__ import annotations

import argparse
import json
import logging
import os
import sqlite3
from dataclasses import dataclass, field
from datetime import datetime, timezone
from pathlib import Path
from typing import Any, Dict, Iterable, List, Optional

import yaml
from dotenv import load_dotenv

try:
    import anthropic
except ImportError:  # pragma: no cover - optional until deps installed
    anthropic = None  # type: ignore

try:
    from openai import OpenAI
except ImportError:  # pragma: no cover - optional until deps installed
    OpenAI = None  # type: ignore


@dataclass
class TopicCandidate:
    keyword: str
    search_volume: int = 0
    opportunity_score: float = 0.0
    metadata: Dict[str, Any] = field(default_factory=dict)


@dataclass
class GeneratedPost:
    title: str
    slug: str
    content: str
    excerpt: str
    keyword: str
    quality_score: float
    frontmatter: Dict[str, Any] = field(default_factory=dict)


class BlogAutomation:
    """Encapsulates the research → generation → publish pipeline."""

    def __init__(self, config_path: Path, dry_run: bool = False) -> None:
        self.config_path = config_path
        self.config = self._load_config(config_path)
        self.base_dir = config_path.parent
        safety = self.config.get("safety", {})
        self.dry_run = dry_run or safety.get("dry_run", False)
        self.log = self._setup_logging()
        self.db = self._init_database()
        self.claude = self._init_claude_client()
        self.openai = self._init_openai_client()
        self.log.info("BlogAutomation ready (dry_run=%s)", self.dry_run)

    # ------------------------------------------------------------------
    # Initialisation helpers
    def _load_config(self, config_path: Path) -> Dict[str, Any]:
        if not config_path.exists():
            raise FileNotFoundError(f"Missing config file: {config_path}")
        with config_path.open("r", encoding="utf-8") as fp:
            config = yaml.safe_load(fp) or {}
        return config

    def _setup_logging(self) -> logging.Logger:
        log_cfg = self.config.get("monitoring", {})
        log_level = getattr(logging, log_cfg.get("log_level", "INFO"))
        logger = logging.getLogger("blog_automation")
        logger.setLevel(log_level)
        logger.handlers.clear()

        log_path = log_cfg.get("log_path")
        formatter = logging.Formatter(
            "%(asctime)s | %(levelname)s | %(message)s",
            datefmt="%Y-%m-%d %H:%M:%S",
        )
        console_handler = logging.StreamHandler()
        console_handler.setFormatter(formatter)
        logger.addHandler(console_handler)

        if log_path:
            path = self._resolve_path(log_path)
            path.parent.mkdir(parents=True, exist_ok=True)
            file_handler = logging.FileHandler(path)
            file_handler.setFormatter(formatter)
            logger.addHandler(file_handler)
        return logger

    def _init_database(self) -> sqlite3.Connection:
        storage_cfg = self.config.get("storage", {})
        db_path = storage_cfg.get("db_path", "posts_history.db")
        resolved = self._resolve_path(db_path)
        resolved.parent.mkdir(parents=True, exist_ok=True)
        conn = sqlite3.connect(resolved)
        conn.execute(
            """
            CREATE TABLE IF NOT EXISTS posts_history (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL,
                slug TEXT,
                keyword TEXT,
                quality_score REAL,
                status TEXT,
                created_at TEXT DEFAULT CURRENT_TIMESTAMP,
                metadata TEXT,
                embedding BLOB
            );
            """
        )
        conn.execute(
            """
            CREATE TABLE IF NOT EXISTS run_log (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                run_at TEXT NOT NULL,
                topics_found INTEGER,
                unique_topics INTEGER,
                posts_attempted INTEGER,
                posts_published INTEGER,
                dry_run INTEGER
            );
            """
        )
        conn.commit()
        return conn

    def _init_claude_client(self) -> Optional["anthropic.Anthropic"]:
        if anthropic is None:
            self.log.warning("anthropic package not installed; generation disabled until deps installed")
            return None
        api_key = _env_or_raise("CLAUDE_API_KEY")
        return anthropic.Anthropic(api_key=api_key)

    def _init_openai_client(self) -> Optional["OpenAI"]:
        if OpenAI is None:
            self.log.warning("openai package not installed; embeddings disabled until deps installed")
            return None
        api_key = _env_or_raise("OPENAI_API_KEY")
        return OpenAI(api_key=api_key)

    # ------------------------------------------------------------------
    # Core flow
    def run(self, max_posts: Optional[int] = None) -> None:
        if self._emergency_stop():
            return

        topics = self.research_topics()
        unique_topics = self.filter_duplicates(topics)
        self.log.info("Processing %s topics (max_posts=%s)", len(unique_topics), max_posts)

        publishing_cfg = self.config.get("publishing", {})
        weekly_cap = publishing_cfg.get("max_posts_per_week", 7)
        limit = max_posts or weekly_cap
        attempts = published = 0

        for topic in unique_topics[:limit]:
            attempts += 1
            post = self.generate_post(topic)
            score = self.validate_quality(post)
            if score < self.config.get("quality", {}).get("min_score", 80):
                self.log.warning("Quality score %.1f below threshold for '%s'", score, post.title)
                self.save_draft(post)
                continue

            if self.dry_run:
                self.log.info("[DRY] Would publish '%s' (score %.1f)", post.title, score)
                published += 1
                self._record_post(post, status="dry-run")
                continue

            if self.publish_post(post):
                published += 1
                self._record_post(post, status="published")
            else:
                self.save_draft(post)

        self.db.execute(
            "INSERT INTO run_log (run_at, topics_found, unique_topics, posts_attempted, posts_published, dry_run) VALUES (?, ?, ?, ?, ?, ?)",
            (
                datetime.now(timezone.utc).isoformat(),
                len(topics),
                len(unique_topics),
                attempts,
                published,
                int(self.dry_run),
            ),
        )
        self.db.commit()
        self.send_report(run_stats={
            "topics_found": len(topics),
            "unique_topics": len(unique_topics),
            "attempts": attempts,
            "published": published,
            "dry_run": self.dry_run,
        })

    # ------------------------------------------------------------------
    # Pipeline stages (initial stubs)
    def research_topics(self) -> List[TopicCandidate]:
        """Derive topic candidates from the configured keyword source or fallback list."""
        content_cfg = self.config.get("content_inputs", {})
        keyword_source = content_cfg.get("keyword_source")
        candidates: List[TopicCandidate] = []

        if keyword_source:
            source_path = self._resolve_path(keyword_source)
            if source_path.exists():
                with source_path.open("r", encoding="utf-8") as fp:
                    for line in fp:
                        stripped = line.strip()
                        if not stripped.startswith("- "):
                            continue
                        keyword = stripped[2:].strip().strip("\"")
                        if not keyword:
                            continue
                        score_hint = len(keyword.encode("utf-8")) % 100
                        candidates.append(
                            TopicCandidate(
                                keyword=keyword,
                                search_volume=200 + score_hint,
                                opportunity_score=round(0.4 + (score_hint / 250), 2),
                                metadata={"source": str(source_path)},
                            )
                        )
        if not candidates:
            seed_keywords = [
                "美國買小雲電視盒完整指南 2025",
                "小雲10P+ vs 10S 機型比較",
                "紐約小雲電視盒購買",
            ]
            candidates = [
                TopicCandidate(keyword=kw, search_volume=250, opportunity_score=0.65, metadata={"source": "fallback"})
                for kw in seed_keywords
            ]
        self.log.debug("Collected %s topic candidates", len(candidates))
        return candidates

    def filter_duplicates(self, topics: Iterable[TopicCandidate]) -> List[TopicCandidate]:
        """Remove topics that already exist in the posts_history table based on plaintext match."""
        existing_keywords = {
            row[0].lower()
            for row in self.db.execute("SELECT DISTINCT keyword FROM posts_history WHERE keyword IS NOT NULL")
            if row[0]
        }
        unique = [topic for topic in topics if topic.keyword.lower() not in existing_keywords]
        dropped = len(list(topics)) - len(unique) if not isinstance(topics, list) else len(topics) - len(unique)
        if dropped:
            self.log.info("Dropped %s topics already covered", dropped)
        return unique

    def generate_post(self, topic: TopicCandidate) -> GeneratedPost:
        """Placeholder multi-pass generation hook."""
        brief = self.generate_brief(topic)
        outline = self.generate_outline(brief)
        if not self.validate_outline(outline):
            self.log.warning("Outline validation failed for '%s', regenerating once", topic.keyword)
            outline = self.generate_outline(brief, retry=True)
        post_body = self.generate_full_post(outline)
        excerpt = post_body.split("\n\n", maxsplit=1)[0][:160]
        slug = _slugify(topic.keyword)
        frontmatter = {
            "keyword": topic.keyword,
            "generated_at": datetime.now(timezone.utc).isoformat(),
            "outline": outline,
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

    def validate_quality(self, post: GeneratedPost) -> float:
        """Composite score builder. Actual AI checks wired later."""
        scores = {
            "length": 25 if len(post.content) >= self.config.get("quality", {}).get("min_length", 4000) else 10,
            "structure": 20 if "##" in post.content else 10,
            "brand": 20 if any(term in post.content for term in self.config.get("brand_voice", {}).get("product_terms", [])) else 5,
            "links": 15,
            "language": 20,
        }
        total = float(sum(scores.values()))
        post.quality_score = total
        return total

    def publish_post(self, post: GeneratedPost) -> bool:
        method = self.config.get("wordpress", {}).get("method", "rest-api")
        if method == "rest-api":
            return self.publish_via_rest_api(post)
        if method == "wp-cli":
            return self.publish_via_wpcli(post)
        self.log.error("Unsupported publishing method: %s", method)
        return False

    def publish_via_rest_api(self, post: GeneratedPost) -> bool:
        if self.dry_run:
            self.log.info("[DRY] REST publish stub for '%s'", post.title)
            return True
        self.log.info("REST publishing not yet implemented; leaving as draft")
        return False

    def publish_via_wpcli(self, post: GeneratedPost) -> bool:
        if self.dry_run:
            self.log.info("[DRY] WP-CLI publish stub for '%s'", post.title)
            return True
        self.log.info("WP-CLI publishing not yet implemented; leaving as draft")
        return False

    def save_draft(self, post: GeneratedPost) -> None:
        draft_dir = self._resolve_path("drafts")
        draft_dir.mkdir(parents=True, exist_ok=True)
        path = draft_dir / f"{post.slug}.md"
        payload = {
            "title": post.title,
            "excerpt": post.excerpt,
            "keyword": post.keyword,
            "frontmatter": post.frontmatter,
            "content": post.content,
        }
        path.write_text(json.dumps(payload, ensure_ascii=False, indent=2), encoding="utf-8")
        self.log.info("Saved draft to %s", path)
        self._record_post(post, status="draft")

    def send_report(self, run_stats: Dict[str, Any]) -> None:
        self.log.info(
            "Weekly report → topics:%s unique:%s published:%s dry:%s",
            run_stats.get("topics_found"),
            run_stats.get("unique_topics"),
            run_stats.get("published"),
            run_stats.get("dry_run"),
        )
        # SMTP integration will be added later

    def send_alert(self, error: Exception) -> None:
        self.log.error("Automation failed: %s", error)
        # Email/SMS hooks will be wired here

    def rebuild_embeddings(self) -> None:
        self.log.info("Rebuilding embeddings cache (stub)")
        # TODO: fetch all posts, call OpenAI embeddings, store BLOBs

    # ------------------------------------------------------------------
    # Generation stubs (to be replaced with Claude calls)
    def generate_brief(self, topic: TopicCandidate) -> str:
        template = (
            "目標關鍵字：{keyword}\n"
            "受眾：美國華人家庭與長輩\n"
            "定位：突出美國倉庫48小時配送與中文客服。"
        )
        return template.format(keyword=topic.keyword)

    def generate_outline(self, brief: str, retry: bool = False) -> str:
        prefix = "再次優化：" if retry else ""
        return (
            f"{prefix}## 專家指南概覽\n"
            "1. 為何選擇美國本土小雲電視盒\n"
            "2. 型號比較與適用族群\n"
            "3. 安裝步驟與客服支援\n"
            "4. 常見問題與保固政策\n"
        )

    def validate_outline(self, outline: str) -> bool:
        threshold = self.config.get("quality", {}).get("outline_score_threshold", 0.75)
        score = min(outline.count("\n"), 4) / 4
        return score >= threshold

    def generate_full_post(self, outline: str) -> str:
        body_sections = []
        for line in outline.splitlines():
            if not line.strip() or line.startswith("##"):
                continue
            body_sections.append(f"## {line}\n\n美國倉庫現貨、中文客服與一年保固，確保使用者體驗安心。")
        return "\n\n".join(body_sections)

    # ------------------------------------------------------------------
    # Helpers
    def _resolve_path(self, relative: str) -> Path:
        path = Path(relative)
        if not path.is_absolute():
            return (self.base_dir / path).resolve()
        return path

    def _record_post(self, post: GeneratedPost, status: str) -> None:
        self.db.execute(
            "INSERT INTO posts_history (title, slug, keyword, quality_score, status, metadata) VALUES (?, ?, ?, ?, ?, ?)",
            (
                post.title,
                post.slug,
                post.keyword,
                post.quality_score,
                status,
                json.dumps(post.frontmatter, ensure_ascii=False),
            ),
        )
        self.db.commit()

    def _emergency_stop(self) -> bool:
        stop_file = self.config.get("safety", {}).get("emergency_stop_file")
        if not stop_file:
            return False
        path = self._resolve_path(stop_file)
        if path.exists():
            self.log.warning("Emergency stop engaged via %s", path)
            return True
        return False

    # Context manager support for clean shutdowns
    def close(self) -> None:
        self.db.close()


# ----------------------------------------------------------------------
def _slugify(text: str) -> str:
    cleaned = "".join(ch if ch.isalnum() else "-" for ch in text)
    slug = "-".join(part for part in cleaned.split("-") if part)
    return slug.lower()[:80]


def _env_or_raise(name: str) -> str:
    value = os.getenv(name)
    if not value:
        raise RuntimeError(f"Missing required environment variable: {name}")
    return value


def parse_args() -> argparse.Namespace:
    script_dir = Path(__file__).resolve().parent
    default_config = script_dir / "config.yaml"
    repo_root = script_dir.parents[1]

    parser = argparse.ArgumentParser(description="SVICLOUD autonomous blog automation")
    parser.add_argument("--config", type=Path, default=default_config, help="Path to config.yaml")
    parser.add_argument("--env", type=Path, default=repo_root / ".env", help="Optional .env file")
    parser.add_argument("--test", action="store_true", help="Enable dry-run mode")
    parser.add_argument("--max-posts", type=int, default=None, help="Override max posts for this run")
    parser.add_argument("--rebuild-embeddings", action="store_true", help="Regenerate embedding cache")
    return parser.parse_args()


def main() -> None:
    args = parse_args()

    if args.env and args.env.exists():
        load_dotenv(args.env)

    automation = BlogAutomation(config_path=args.config, dry_run=args.test)
    try:
        if args.rebuild_embeddings:
            automation.rebuild_embeddings()
            return
        automation.run(max_posts=args.max_posts)
    except Exception as exc:  # pragma: no cover - top-level guard
        automation.send_alert(exc)
        raise
    finally:
        automation.close()


if __name__ == "__main__":
    main()
