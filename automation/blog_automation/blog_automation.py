#!/usr/bin/env python3
"""SVICLOUD autonomous Chinese blog automation pipeline."""
from __future__ import annotations

import argparse
import json
import logging
import os
import re
import smtplib
import sqlite3
import math
from dataclasses import dataclass, field
from datetime import datetime, timezone, date, timedelta
from email.message import EmailMessage
from pathlib import Path
from typing import Any, Dict, Iterable, List, Optional, Tuple, Union

import requests
import yaml
from bs4 import BeautifulSoup
from dotenv import load_dotenv
import markdown
from google.oauth2 import service_account
from googleapiclient.discovery import build

GSC_SCOPES = ["https://www.googleapis.com/auth/webmasters.readonly"]

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
        self.embedding_model = self.config.get("apis", {}).get("openai_embedding", "text-embedding-3-small")
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
        project = os.getenv("OPENAI_PROJECT")
        client = OpenAI(api_key=api_key, project=project)
        return client

    # ------------------------------------------------------------------
    # Core flow
    def run(self, max_posts: Optional[int] = None) -> None:
        if self._emergency_stop():
            return

        self._auto_refresh_sources()
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

    def _auto_refresh_sources(self) -> None:
        self._refresh_gsc_topics()
        self._refresh_competitor_topics()

    # ------------------------------------------------------------------
    # Pipeline stages (initial stubs)
    def research_topics(self) -> List[TopicCandidate]:
        """Derive topic candidates from the configured keyword source or fallback list."""
        plan_candidates = self._load_keyword_plan_candidates()
        gsc_candidates = self._load_gsc_candidates()
        competitor_candidates = self._load_competitor_candidates()
        merged_candidates = self._merge_candidates([
            plan_candidates,
            gsc_candidates,
            competitor_candidates,
        ])
        if not merged_candidates:
            content_cfg = self.config.get("content_inputs", {})
            locale_targets = content_cfg.get("locale_targets", [])
            fallback = [
                self._build_topic_candidate(
                    "美國買小雲電視盒完整指南 2025",
                    base_score=75,
                    section="fallback",
                    volume_estimate=350,
                    locale_targets=locale_targets,
                    source="fallback",
                ),
                self._build_topic_candidate(
                    "小雲10P+ vs 10S 機型比較",
                    base_score=72,
                    section="fallback",
                    volume_estimate=340,
                    locale_targets=locale_targets,
                    source="fallback",
                ),
                self._build_topic_candidate(
                    "紐約小雲電視盒購買",
                    base_score=70,
                    section="fallback",
                    volume_estimate=320,
                    locale_targets=locale_targets,
                    source="fallback",
                ),
            ]
            return fallback
        sorted_candidates = sorted(merged_candidates, key=lambda c: c.metadata.get("score", 0), reverse=True)
        self.log.info(
            "Loaded %s topic candidates (plan: %s, gsc: %s, competitor: %s)",
            len(sorted_candidates),
            len(plan_candidates),
            len(gsc_candidates),
            len(competitor_candidates),
        )
        return sorted_candidates

    def filter_duplicates(self, topics: Iterable[TopicCandidate]) -> List[TopicCandidate]:
        """Remove topics that already exist in the posts_history table based on plaintext match."""
        topics_list = list(topics)
        existing_keywords = {
            row[0].lower()
            for row in self.db.execute("SELECT DISTINCT keyword FROM posts_history WHERE keyword IS NOT NULL")
            if row[0]
        }
        if not topics_list:
            return []

        unique: List[TopicCandidate] = []
        dropped = 0
        embedding_threshold = self.config.get("quality", {}).get("duplicate_threshold", 0.85)
        embedding_cache: Dict[str, List[float]] = {}
        existing_embeddings = self._load_existing_embeddings() if self.openai else []

        for topic in topics_list:
            keyword_lower = topic.keyword.lower()
            if keyword_lower in existing_keywords:
                dropped += 1
                continue
            if existing_embeddings:
                embedding = embedding_cache.get(keyword_lower)
                if embedding is None:
                    embedding = self._generate_embedding_vector(topic.keyword)
                    if embedding:
                        embedding_cache[keyword_lower] = embedding
                if embedding and self._is_duplicate_embedding(embedding, existing_embeddings, embedding_threshold):
                    dropped += 1
                    continue
            unique.append(topic)

        if dropped:
            self.log.info("Dropped %s topics already covered or too similar", dropped)
        return unique

    def generate_post(self, topic: TopicCandidate) -> GeneratedPost:
        """Multi-pass generation pipeline using Claude with fallbacks."""
        brief = self.generate_brief(topic)
        outline = self.generate_outline(topic, brief)
        if not self.validate_outline(outline):
            self.log.warning("Outline validation failed for '%s', regenerating once", topic.keyword)
            outline = self.generate_outline(topic, brief, retry=True)
        post_body = self.generate_full_post(topic, outline, brief)
        post_body = self._enforce_internal_requirements(topic, post_body)
        post_body = self._insert_internal_links(post_body)
        post_body = self._inject_images(post_body, topic)
        post_body = self._append_cta(post_body, topic)
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

    def validate_quality(self, post: GeneratedPost) -> float:
        score = self._qa_with_claude(post)
        if score is not None:
            post.quality_score = score
            return score

        # FIX: Fallback scoring now actually validates criteria (not just awards points)
        content = post.content
        min_length = self.config.get("quality", {}).get("min_length", 4000)

        # Length validation (25 points)
        length_score = 25 if len(content) >= min_length else (10 if len(content) >= min_length * 0.7 else 0)

        # Structure validation (20 points) - check for proper H2 headings
        has_structure = "##" in content
        structure_score = 20 if has_structure else 10

        # Brand voice validation (20 points) - check for required product terms
        brand_terms = self.config.get("brand_voice", {}).get("product_terms", [])
        brand_count = sum(1 for term in brand_terms if term in content)
        if brand_count >= 3:
            brand_score = 20
        elif brand_count >= 2:
            brand_score = 15
        elif brand_count >= 1:
            brand_score = 10
        else:
            brand_score = 0

        # Internal links validation (15 points) - actually check for site URLs
        import re
        site_domain = "svicloudtvbox.us"
        internal_links = re.findall(rf"https?://{re.escape(site_domain)}/[^\s\)\]]+", content)
        links_count = len(internal_links)
        if links_count >= 3:
            links_score = 15
        elif links_count >= 2:
            links_score = 10
        elif links_count >= 1:
            links_score = 5
        else:
            links_score = 0

        # Language validation (20 points) - check for Traditional Chinese
        # Traditional Chinese characters (繁體中文): U+4E00-U+9FFF
        chinese_chars = re.findall(r'[\u4e00-\u9fff]', content)
        chinese_char_count = len(chinese_chars)

        # Check if predominantly Traditional Chinese (vs Simplified)
        # Simplified-specific chars often in: 国, 为, 这, 么, 们, 时, 个, 过
        simplified_indicators = ['国', '为', '这', '么', '们', '时', '个', '过']
        simplified_count = sum(content.count(char) for char in simplified_indicators)

        if chinese_char_count >= 500:  # Good amount of Chinese content
            # If Simplified indicators are less than 5% of total Chinese chars, it's Traditional
            if simplified_count < (chinese_char_count * 0.05):
                language_score = 20
            else:
                language_score = 10  # Mixed or Simplified
        elif chinese_char_count >= 200:
            language_score = 10  # Some Chinese but not enough
        else:
            language_score = 0  # Insufficient Chinese content

        scores = {
            "length": length_score,
            "structure": structure_score,
            "brand": brand_score,
            "links": links_score,
            "language": language_score,
        }

        total = float(sum(scores.values()))
        post.quality_score = total

        # Log detailed breakdown for debugging
        self.log.info(
            "Fallback quality score: %.1f (length=%d, structure=%d, brand=%d, links=%d, language=%d)",
            total, length_score, structure_score, brand_score, links_score, language_score
        )

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
        rest_cfg = self.config.get("wordpress", {}).get("rest", {})
        endpoint = rest_cfg.get("endpoint")
        username = rest_cfg.get("username")
        password_env = rest_cfg.get("password_env")
        password = os.getenv(password_env) if password_env else rest_cfg.get("password")
        status = rest_cfg.get("status", "draft")
        if not endpoint or not username or not password:
            self.log.error("REST API not fully configured (endpoint/username/password missing)")
            return False
        html_content = self._markdown_to_html(post.content)
        excerpt_text = self._excerpt_from_html(html_content, keyword=post.keyword)
        payload = {
            "title": post.title,
            "slug": post.slug,
            "content": html_content,
            "excerpt": excerpt_text,
            "status": status,
        }
        if rest_cfg.get("author"):
            payload["author"] = rest_cfg["author"]
        selected_categories = self._select_categories_for_post(post)
        if selected_categories:
            payload["categories"] = selected_categories
        if self.dry_run:
            self.log.info("[DRY] Would POST to %s with status '%s'", endpoint, status)
            self.log.debug("Payload: %s", payload)
            return True
        try:
            response = requests.post(endpoint, auth=(username, password), json=payload, timeout=30)
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
        lines = [
            "SVICLOUD blog automation run summary",
            f"- Topics discovered: {run_stats.get('topics_found', 0)}",
            f"- Unique topics: {run_stats.get('unique_topics', 0)}",
            f"- Posts attempted: {run_stats.get('attempts', 0)}",
            f"- Posts published: {run_stats.get('published', 0)}",
            f"- Dry run: {run_stats.get('dry_run', False)}",
            f"- Timestamp: {datetime.now(timezone.utc).isoformat()}",
        ]
        body = "\n".join(lines)
        subject = "SVICLOUD Blog Automation Report"
        self.log.info(" | ".join(lines))
        self._send_email(subject, body)

    def send_alert(self, error: Exception) -> None:
        self.log.error("Automation failed: %s", error)
        subject = "SVICLOUD Blog Automation ALERT"
        body = (
            "Automation run encountered an error.\n\n"
            f"Error: {error}\n"
            f"Timestamp: {datetime.now(timezone.utc).isoformat()}\n"
            f"Config: {self.config_path}\n"
        )
        self._send_email(subject, body, high_priority=True)

    def rebuild_embeddings(self) -> None:
        self.log.info("Rebuilding embeddings cache (stub)")
        # TODO: fetch all posts, call OpenAI embeddings, store BLOBs

    # ------------------------------------------------------------------
    # Generation stubs (to be replaced with Claude calls)
    def generate_brief(self, topic: TopicCandidate) -> str:
        metadata = topic.metadata or {}
        claude_model = self.config.get("apis", {}).get("claude_model_brief")
        if self.claude and claude_model:
            try:
                system = self._prompt_library("brief", topic)
                user_prompt = (
                    f"請以100-150字撰寫策略簡報：\n"
                    f"- 關鍵字：{topic.keyword}\n"
                    f"- 主題分類：{metadata.get('topic_type')}\n"
                    f"- 地區：{metadata.get('geo_target') or '全美'}\n"
                    f"- 比較焦點：{metadata.get('is_comparison')}\n"
                    f"- 活動/節慶：{metadata.get('is_campaign')}\n"
                    "務必強調：4K HDR + Dolby Audio、Wi-Fi 6 雙頻、中文/英文語音介面與一年美國保固。"
                )
                competitor_note = self._competitor_context(metadata)
                if competitor_note:
                    user_prompt += f"\n- 競品提醒：{competitor_note}"
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

        lines = [
            f"目標關鍵字：{topic.keyword}",
            "受眾：美國華人家庭、長輩與新移民",
            "品牌重點：4K HDR + Dolby Audio、Wi-Fi 6 雙頻、中文/英文語音介面與一年美國保固。",
        ]
        if metadata.get("is_geo"):
            lines.append(f"地區聚焦：針對{metadata.get('geo_target')}地區，強調本地物流與售後。")
        if metadata.get("is_comparison"):
            lines.append("比較焦點：突出SVICLOUD 10P+/10S與競品在頻道數、保固與售後的差異。")
        if metadata.get("is_campaign"):
            lines.append("活動語境：結合節慶/派對需求，提供歌單、流程與器材搭配。")
        if metadata.get("topic_type") == "faq":
            lines.append("FAQ目標：回答美國華人最常見的安裝、付款與售後問題。")
        return "\n".join(lines)

    def generate_outline(self, topic: TopicCandidate, brief: str, retry: bool = False) -> str:
        claude_model = self.config.get("apis", {}).get("claude_model_outline")
        metadata = topic.metadata or {}
        if self.claude and claude_model:
            try:
                system = (
                    self._prompt_library("outline", topic)
                )
                retry_note = "重新優化" if retry else "首次草稿"
                user_prompt = (
                    f"{retry_note}，請依據以下簡報撰寫條列式大綱：\n\n"
                    f"簡報：\n{brief}\n\n"
                    f"主題分類：{metadata.get('topic_type')}\n"
                    f"地區：{metadata.get('geo_target') or '全美'}\n"
                    "輸出格式：以數字排序的清單，每條不超過40字，聚焦美國本地服務、比較與FAQ。"
                )
                competitor_note = self._competitor_context(metadata)
                if competitor_note:
                    user_prompt += f"\n競品提醒：{competitor_note}"
                outline_text = self._claude_complete(
                    model=claude_model,
                    system_prompt=system,
                    user_prompt=user_prompt,
                    max_tokens=600,
                    temperature=0.3,
                )
                if outline_text:
                    return outline_text.strip()
            except Exception as exc:
                self.log.warning("Claude outline generation failed: %s", exc)

        sections = self._build_outline_sections(topic)
        header = "## 再次優化專家概覽" if retry else "## 專家指南概覽"
        outline_lines = [header]
        for idx, section in enumerate(sections, 1):
            outline_lines.append(f"{idx}. {section}")
        return "\n".join(outline_lines)

    def validate_outline(self, outline: str) -> bool:
        threshold = self.config.get("quality", {}).get("outline_score_threshold", 0.75)
        score = min(outline.count("\n"), 4) / 4
        return score >= threshold

    def generate_full_post(self, topic: TopicCandidate, outline: str, brief: str) -> str:
        claude_model = self.config.get("apis", {}).get("claude_model_full")
        metadata = topic.metadata or {}
        if self.claude and claude_model:
            try:
                system = self._prompt_library("full", topic)
                user_prompt = (
                    f"請依據以下資訊撰寫完整部落格文章（Markdown）：\n"
                    f"- 關鍵字：{topic.keyword}\n"
                    f"- 簡報：{brief}\n"
                    f"- 大綱：\n{outline}\n"
                    f"- 主題分類：{metadata.get('topic_type')}\n"
                    f"- 地區：{metadata.get('geo_target') or '全美'}\n"
                    "需求：\n"
                    "1. 強調 SVICLOUD 10P+/10S 的 4K HDR、Dolby Audio、Wi-Fi 6、中文/英文語音介面與一年美國保固。\n"
                    "2. 至少3個內部連結（以純文字描述，稍後由腳本替換）。\n"
                    "3. 每段落加入具體情境或案例。\n"
                    "4. 結尾加入CTA導引至購買/聯絡。\n"
                    "輸出：純Markdown內容，不含YAML。"
                )
                competitor_note = self._competitor_context(metadata)
                if competitor_note:
                    user_prompt += f"\n- 競品提醒：{competitor_note}"
                full_text = self._claude_complete(
                    model=claude_model,
                    system_prompt=system,
                    user_prompt=user_prompt,
                    max_tokens=2800,
                    temperature=0.35,
                )
                if full_text:
                    return full_text.strip()
            except Exception as exc:
                self.log.warning("Claude full post generation failed: %s", exc)

        sections = self._build_outline_sections(topic)
        paragraphs = []
        for section in sections:
            paragraph = self._build_section_paragraph(section, topic)
            paragraphs.append(paragraph)
        return "\n\n".join(paragraphs)

    # ------------------------------------------------------------------
    # Helpers
    def _load_keyword_plan_candidates(self) -> List[TopicCandidate]:
        content_cfg = self.config.get("content_inputs", {})
        keyword_source = content_cfg.get("keyword_source")
        if not keyword_source:
            return []
        source_path = self._resolve_path(keyword_source)
        if not source_path.exists():
            self.log.warning("Keyword plan source %s does not exist", source_path)
            return []

        section_weights = {
            "target keywords not ranking": 90,
            "primary keywords (target now)": 88,
            "secondary keywords (3-6 months)": 72,
            "create chinese blog posts": 84,
            "create location-specific pages": 78,
            "keyword strategy expansion": 70,
        }
        base_volumes = {
            "target keywords not ranking": 550,
            "primary keywords (target now)": 500,
            "secondary keywords (3-6 months)": 360,
            "create chinese blog posts": 420,
            "create location-specific pages": 300,
            "keyword strategy expansion": 320,
        }

        locale_targets = content_cfg.get("locale_targets", [])
        current_section = ""
        candidates: List[TopicCandidate] = []

        with source_path.open("r", encoding="utf-8") as handle:
            for raw_line in handle:
                line = raw_line.strip()
                if not line:
                    continue
                if line.startswith("##"):
                    current_section = line.strip("# ").lower()
                    continue
                if line.startswith("###"):
                    current_section = line.strip("# ").lower()
                    continue
                if line.startswith("**") and line.endswith("**"):
                    current_section = line.strip("* ").lower()
                    continue

                if not line.startswith("- "):
                    continue
                keyword = self._extract_keyword_from_line(line)
                if not keyword:
                    continue
                if not self._looks_like_keyword(keyword):
                    continue

                normalized_section = current_section or "keyword strategy expansion"
                base_score = section_weights.get(normalized_section, 65)
                volume_estimate = base_volumes.get(normalized_section, 300)
                candidate = self._build_topic_candidate(
                    keyword=keyword,
                    base_score=base_score,
                    section=normalized_section,
                    volume_estimate=volume_estimate,
                    locale_targets=locale_targets,
                    source=str(source_path),
                )
                candidates.append(candidate)
        return candidates

    def _build_topic_candidate(
        self,
        keyword: str,
        base_score: int,
        section: str,
        volume_estimate: int,
        locale_targets: List[str],
        source: str,
    ) -> TopicCandidate:
        classification = self._classify_topic(keyword, locale_targets)
        topic_score = base_score
        if classification["is_geo"]:
            topic_score += 8
        if classification["is_comparison"]:
            topic_score += 6
        if classification["is_campaign"]:
            topic_score += 4
        if classification["topic_type"] == "faq":
            topic_score += 3
        novelty_bonus = 3 if any(str(year) in keyword for year in (2025, 2026, 2027)) else 0
        length_modifier = min(len(keyword), 32) % 7
        final_score = topic_score + novelty_bonus + length_modifier
        metadata = {
            "section": section,
            "topic_type": classification["topic_type"],
            "is_geo": classification["is_geo"],
            "geo_target": classification.get("geo_target"),
            "is_comparison": classification["is_comparison"],
            "is_campaign": classification["is_campaign"],
            "score": final_score,
            "source": source,
        }
        estimated_volume = volume_estimate + classification.get("volume_bonus", 0)
        opportunity = round(min(final_score / 100, 0.99), 2)
        return TopicCandidate(
            keyword=keyword,
            search_volume=estimated_volume,
            opportunity_score=opportunity,
            metadata=metadata,
        )

    def _classify_topic(self, keyword: str, locale_targets: List[str]) -> Dict[str, Any]:
        lower_kw = keyword.lower()
        classification = {
            "topic_type": "pillar",
            "is_geo": False,
            "is_comparison": False,
            "is_campaign": False,
            "volume_bonus": 0,
        }
        for region in locale_targets:
            if region and region in keyword:
                classification["is_geo"] = True
                classification["geo_target"] = region
                classification["topic_type"] = "geo"
                classification["volume_bonus"] += 40
                break
        if "vs" in lower_kw or "比較" in keyword or "對比" in keyword:
            classification["is_comparison"] = True
            classification["topic_type"] = "comparison"
            classification["volume_bonus"] += 20
        if any(token in keyword for token in ["新年", "春節", "派對", "節", "2025", "2026"]):
            classification["is_campaign"] = True
            if classification["topic_type"] == "pillar":
                classification["topic_type"] = "campaign"
        if "常見問題" in keyword or "FAQ" in keyword.upper():
            classification["topic_type"] = "faq"
        return classification

    @staticmethod
    def _extract_keyword_from_line(line: str) -> Optional[str]:
        candidate = line[2:].strip()
        if not candidate:
            return None
        if candidate.startswith("\""):
            parts = candidate.split("\"")
            if len(parts) >= 3:
                candidate = parts[1]
        candidate = candidate.split("(")[0].strip()
        return candidate or None

    @staticmethod
    def _looks_like_keyword(text: str) -> bool:
        has_cjk = any("\u4e00" <= ch <= "\u9fff" for ch in text)
        if has_cjk:
            return True
        lowered = text.lower()
        return "svicloud" in lowered or "tv box" in lowered

    def _load_gsc_candidates(self) -> List[TopicCandidate]:
        gsc_config = self.config.get("content_inputs", {}).get("gsc")
        if not gsc_config:
            return []
        gsc_path = gsc_config.get("path")
        if not gsc_path:
            return []
        source_path = self._resolve_path(gsc_path)
        if not source_path.exists():
            self.log.warning("GSC data file %s not found", source_path)
            return []
        try:
            data = json.loads(source_path.read_text(encoding="utf-8"))
        except json.JSONDecodeError as exc:
            self.log.warning("Failed to parse GSC JSON: %s", exc)
            return []
        locale_targets = self.config.get("content_inputs", {}).get("locale_targets", [])
        candidates: List[TopicCandidate] = []
        for entry in data:
            query = entry.get("query") or entry.get("keyword")
            if not query:
                continue
            impressions = float(entry.get("impressions", 0))
            ctr = float(entry.get("ctr", 0))
            position = float(entry.get("position", 99))
            score = self._score_gsc_entry(impressions, ctr, position)
            candidate = self._build_topic_candidate(
                keyword=query,
                base_score=int(score * 100),
                section="gsc",
                volume_estimate=int(impressions),
                locale_targets=locale_targets,
                source=str(source_path),
            )
            candidate.metadata["gsc"] = {
                "impressions": impressions,
                "ctr": ctr,
                "position": position,
            }
            candidate.metadata["score"] = score * 100
            candidates.append(candidate)
        return candidates

    def _load_competitor_candidates(self) -> List[TopicCandidate]:
        content_cfg = self.config.get("content_inputs", {})
        competitor_cfg = content_cfg.get("competitors", {})
        path = competitor_cfg.get("path", "data/competitor_topics.json")
        source_path = self._resolve_path(path)
        if not source_path.exists():
            return []
        try:
            entries = json.loads(source_path.read_text(encoding="utf-8"))
        except json.JSONDecodeError as exc:
            self.log.warning("Failed to parse competitor topics JSON: %s", exc)
            return []
        locale_targets = content_cfg.get("locale_targets", [])
        base_score = competitor_cfg.get("base_score", 75)
        base_volume = competitor_cfg.get("base_volume", 150)
        candidates: List[TopicCandidate] = []
        for entry in entries:
            keyword = entry.get("query")
            if not keyword:
                continue
            impressions = int(entry.get("impressions", base_volume))
            score = base_score + min(int(impressions / 20), 10)
            candidate = self._build_topic_candidate(
                keyword=keyword,
                base_score=score,
                section="competitor",
                volume_estimate=impressions,
                locale_targets=locale_targets,
                source=str(source_path),
            )
            candidate.metadata["source_domain"] = entry.get("source")
            candidate.metadata["competitor_query"] = keyword
            candidate.metadata["score"] = score
            candidates.append(candidate)
        return candidates

    @staticmethod
    def _score_gsc_entry(impressions: float, ctr: float, position: float) -> float:
        impression_component = min(impressions / 5000, 1.0)
        ctr_gap = max(0.1 - ctr, 0)
        ctr_component = min(ctr_gap * 5, 1.0)
        position_component = max((20 - position) / 20, 0)
        raw = (impression_component * 0.5) + (ctr_component * 0.3) + (position_component * 0.2)
        return min(raw, 1.0)

    def _merge_candidates(
        self,
        candidate_groups: List[List[TopicCandidate]],
    ) -> List[TopicCandidate]:
        merged: Dict[str, TopicCandidate] = {}
        for candidate in [c for group in candidate_groups for c in group]:
            key = candidate.keyword.lower()
            existing = merged.get(key)
            if not existing:
                merged[key] = candidate
                continue
            if candidate.metadata.get("score", 0) > existing.metadata.get("score", 0):
                merged[key] = candidate
        return list(merged.values())

    def _refresh_gsc_topics(self) -> None:
        gsc_cfg = self.config.get("content_inputs", {}).get("gsc", {})
        if not gsc_cfg.get("auto_refresh"):
            return
        site = gsc_cfg.get("site")
        cred_setting = gsc_cfg.get("credentials") or os.getenv("GSC_CREDENTIALS_PATH")
        if not site or not cred_setting:
            self.log.warning("Skipping GSC refresh, missing site or credentials path")
            return
        cred_path = self._resolve_path(cred_setting)
        if not cred_path.exists():
            self.log.warning("GSC credentials not found at %s", cred_path)
            return
        target_path = self._resolve_path(gsc_cfg.get("path", "data/gsc_topics.json"))
        lookback = int(gsc_cfg.get("lookback_days", 30))
        min_impressions = float(gsc_cfg.get("min_impressions", 1))
        max_position = float(gsc_cfg.get("max_position", 50))
        limit = int(gsc_cfg.get("limit", 200))
        try:
            creds = service_account.Credentials.from_service_account_file(str(cred_path), scopes=GSC_SCOPES)
            service = build("searchconsole", "v1", credentials=creds, cache_discovery=False)
            end_date = date.today() - timedelta(days=1)
            start_date = end_date - timedelta(days=lookback)
            request_body = {
                "startDate": start_date.isoformat(),
                "endDate": end_date.isoformat(),
                "dimensions": ["query"],
                "rowLimit": limit,
                "type": "web",
                "dataState": "all",
            }
            response = service.searchanalytics().query(siteUrl=site, body=request_body).execute()
            rows = response.get("rows", [])
            filtered = []
            for row in rows:
                query = row.get("keys", [""])[0]
                impressions = row.get("impressions", 0)
                position = row.get("position", 99)
                ctr = row.get("ctr", 0)
                if impressions < min_impressions or position > max_position:
                    continue
                filtered.append(
                    {
                        "query": query,
                        "impressions": impressions,
                        "position": position,
                        "ctr": ctr,
                    }
                )
            target_path.parent.mkdir(parents=True, exist_ok=True)
            target_path.write_text(json.dumps(filtered, ensure_ascii=False, indent=2), encoding="utf-8")
            self.log.info("Refreshed GSC topics (%s rows)", len(filtered))
        except Exception as exc:  # pragma: no cover - network/credential dependent
            self.log.warning("GSC refresh failed: %s", exc)

    def _refresh_competitor_topics(self) -> None:
        comp_cfg = self.config.get("content_inputs", {}).get("competitors", {})
        if not comp_cfg.get("auto_refresh"):
            return
        sites = comp_cfg.get("sites") or {}
        if not sites:
            self.log.warning("Competitor auto-refresh enabled but no sites configured")
            return
        min_length = int(comp_cfg.get("min_length", 4))
        entries = []
        for domain, urls in sites.items():
            for url in urls:
                for keyword in self._scrape_competitor_keywords(url, min_length):
                    entries.append(
                        {
                            "query": keyword,
                            "source": domain,
                            "impressions": comp_cfg.get("base_volume", 150),
                            "position": 15,
                            "ctr": 0.01,
                        }
                    )
        if not entries:
            self.log.warning("Competitor refresh yielded no keywords")
            return
        target_path = self._resolve_path(comp_cfg.get("path", "data/competitor_topics.json"))
        target_path.parent.mkdir(parents=True, exist_ok=True)
        target_path.write_text(json.dumps(entries, ensure_ascii=False, indent=2), encoding="utf-8")
        self.log.info("Refreshed competitor topics (%s rows)", len(entries))

    def _scrape_competitor_keywords(self, url: str, min_length: int) -> List[str]:
        tokens: List[str] = []
        try:
            resp = requests.get(url, timeout=15)
            resp.raise_for_status()
            soup = BeautifulSoup(resp.text, "html.parser")
            seen = set()
            for tag in soup.find_all(["h1", "h2", "h3", "h4", "strong"]):
                text = tag.get_text(strip=True)
                if not text:
                    continue
                for token in re.findall(r"[\u4e00-\u9fffA-Za-z0-9＋+\- ]{2,40}", text):
                    cleaned = token.strip()
                    if len(cleaned) < min_length:
                        continue
                    if cleaned in seen:
                        continue
                    seen.add(cleaned)
                    tokens.append(cleaned)
        except requests.RequestException as exc:
            self.log.warning("Competitor fetch failed for %s: %s", url, exc)
        return tokens

    def _notification_recipients(self) -> List[str]:
        notifications_cfg = self.config.get("notifications", {})
        recipients = notifications_cfg.get("recipients", [])
        if isinstance(recipients, str):
            return [recipients]
        return [r for r in (recipients or []) if r]

    def _build_outline_sections(self, topic: TopicCandidate) -> List[str]:
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
                "透過處理器、Wi-Fi 規格、App 相容性與售後流程比較 SVICLOUD 與其他影音盒，凸顯 4K HDR 與 Google Play 認證的優勢。"
            )
        if metadata.get("is_geo"):
            geo = metadata.get("geo_target", "美國主要城市")
            body.append(
                f"針對{geo}客戶，說明 Wi-Fi 6 擺位、Mesh 建議與本地節目清單，讓華語直播穩定無延遲。"
            )
        if metadata.get("is_campaign"):
            body.append("結合節慶/派對流程，提供歌單、互動與設備建議，並導引至購買。")
        if metadata.get("topic_type") == "faq" or "常見問題" in section_title:
            body.append("整理付款方式、安裝疑難排解與保固流程，減少客服負擔。")
        return "\n\n".join(body)

    def _insert_internal_links(self, content: str) -> str:
        replacements = self.config.get("content_inputs", {}).get("internal_links", {})
        for placeholder, url in replacements.items():
            marker = f"[{placeholder}]"
            if marker in content:
                content = content.replace(marker, f"[{placeholder}]({url})")
        return content

    def _inject_images(self, content: str, topic: TopicCandidate) -> str:
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
            return content
        hero = "\n\n".join(blocks)
        return f"{hero}\n\n{content}" if hero not in content else content

    def _append_cta(self, content: str, topic: TopicCandidate) -> str:
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
        if cta not in content:
            content = f"{content}\n\n{cta}"
        return content

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

    def _enforce_internal_requirements(self, topic: TopicCandidate, content: str) -> str:
        required_phrases = self.config.get("brand_voice", {}).get("key_phrases", [])
        missing: List[str] = []
        keyword = topic.keyword if isinstance(topic.keyword, str) else ""
        for phrase in required_phrases:
            if phrase in content:
                continue
            contextual = f"{keyword}｜{phrase}" if keyword else phrase
            missing.append(contextual)

        if not missing:
            return content

        reminder_block = "\n\n".join(f"> {line}" for line in missing)
        return f"{content}\n\n{reminder_block}"

    def _prompt_library(self, prompt_type: str, context: Union[TopicCandidate, GeneratedPost]) -> str:
        if isinstance(context, TopicCandidate):
            metadata = context.metadata or {}
        else:
            metadata = {
                "topic_type": context.frontmatter.get("topic_type"),
                "geo_target": context.frontmatter.get("geo_target"),
            }
        topic_type = metadata.get("topic_type", "pillar")
        geo = metadata.get("geo_target") or "全美"
        base_prompts = {
            "brief": "你是 SVICLOUD 策略總監，撰寫100字簡報，聚焦美國本地優勢與受眾痛點。",
            "outline": "你是 SVICLOUD 編輯總監，產出5-7個段落，涵蓋美國倉儲、售後與FAQ。",
            "full": "你是 SVICLOUD 中文內容顧問，撰寫4000字以上Markdown文章，需含實例與內部連結。",
            "qa": "你是 SVICLOUD 品質稽核，需回傳 JSON score 與 notes。",
        }
        modifiers = []
        if topic_type == "comparison":
            modifiers.append("比較SVICLOUD與競品的頻道數、保固、售後。")
        if topic_type == "geo":
            modifiers.append(f"聚焦{geo}地區的物流與售後需求。")
        if topic_type == "campaign":
            modifiers.append("融入節慶/活動情境、歌單或互動建議。")
        if topic_type == "faq":
            modifiers.append("以問答形式整理常見問題。")
        return base_prompts.get(prompt_type, base_prompts["full"]) + " " + " ".join(modifiers)

    def _competitor_context(self, metadata: Dict[str, Any]) -> str:
        domain = metadata.get("source_domain")
        query = metadata.get("competitor_query")
        if not domain or not query:
            return ""
        return f"競品 {domain} 近期內容聚焦「{query}」，須強調SVICLOUD的差異化優勢。"

    def _markdown_to_html(self, markdown_text: str) -> str:
        try:
            return markdown.markdown(markdown_text, extensions=["extra", "sane_lists"])
        except Exception as exc:
            self.log.warning("Markdown conversion failed: %s", exc)
            return markdown_text

    def _excerpt_from_html(
        self,
        html_text: str,
        length: int = 220,
        keyword: Optional[str] = None,
    ) -> str:
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
        generic_markers = (
            "內華達倉庫48小時",
            "內華達倉庫 48",
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

    def _select_categories_for_post(self, post: GeneratedPost) -> List[int]:
        category_map = self.config.get("wordpress", {}).get("category_map", {})
        topic_type = (post.frontmatter.get("topic_type") or "default").lower()
        selected = category_map.get(topic_type) or category_map.get("default") or []
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

    def _claude_complete(
        self,
        model: str,
        system_prompt: str,
        user_prompt: str,
        max_tokens: int,
        temperature: float,
    ) -> str:
        if not self.claude:
            raise RuntimeError("Claude client not available")
        response = self.claude.messages.create(
            model=model,
            system=system_prompt,
            max_tokens=max_tokens,
            temperature=temperature,
            messages=[{"role": "user", "content": user_prompt}],
        )
        chunks = []
        for block in response.content:
            if getattr(block, "type", None) == "text":
                chunks.append(block.text)
        return "".join(chunks)

    def _qa_with_claude(self, post: GeneratedPost) -> Optional[float]:
        model = self.config.get("apis", {}).get("claude_model_qa")
        if not self.claude or not model:
            return None
        system = self._prompt_library("qa", post)

        # FIX: Truncate content to avoid token limit (242K > 200K max)
        # Use first 10K characters - sufficient for quality validation
        content_sample = post.content[:10000] if len(post.content) > 10000 else post.content
        truncated_note = "（內容已截取前10000字元進行評估）" if len(post.content) > 10000 else ""

        user_prompt = (
            "請以純 JSON 格式回覆（不要用 markdown 代碼塊包裹），格式範例：{\"score\":85,\"notes\":[\"語句順暢\"]}\n"
            "評估以下文章是否符合：傳統中文、關鍵字自然出現、強調美國本地優勢、包含內部連結描述、長度>4000字。\n"
            f"{truncated_note}\n"
            f"文章內容：\n{content_sample}\n"
        )
        for attempt in range(3):  # Increased from 2 to 3 attempts
            try:
                completion = self._claude_complete(
                    model=model,
                    system_prompt=system,
                    user_prompt=user_prompt,
                    max_tokens=500,  # Increased from 400 for more context
                    temperature=0.2,
                )

                # FIX: Improved JSON extraction with multiple strategies
                cleaned = completion.strip()

                # Strategy 1: Extract from markdown code blocks
                if "```json" in cleaned:
                    json_start = cleaned.find("```json") + 7
                    json_end = cleaned.find("```", json_start)
                    if json_end > json_start:
                        cleaned = cleaned[json_start:json_end].strip()

                # Strategy 2: Remove any remaining backticks and whitespace
                cleaned = cleaned.strip("`").strip()

                # Strategy 3: Find first { and last } to extract JSON object
                first_brace = cleaned.find("{")
                last_brace = cleaned.rfind("}")
                if first_brace >= 0 and last_brace > first_brace:
                    cleaned = cleaned[first_brace : last_brace + 1]

                # Parse and validate JSON
                data = json.loads(cleaned)

                # Validate schema: must have 'score' field
                if "score" not in data:
                    self.log.warning("QA JSON missing 'score' field (attempt %s)", attempt + 1)
                    continue

                score = float(data.get("score", 0))

                # Validate score range: 0-100
                if not (0 <= score <= 100):
                    self.log.warning("QA score out of range: %.1f (attempt %s)", score, attempt + 1)
                    continue

                notes = data.get("notes") or []
                post.frontmatter["qa_notes"] = notes
                self.log.info("Claude QA successful: score=%.1f (attempt %s)", score, attempt + 1)
                return score

            except json.JSONDecodeError as exc:
                self.log.warning("QA JSON parse failed (attempt %s): %s - Response: %s", attempt + 1, exc, completion[:200])
            except ValueError as exc:
                self.log.warning("QA score conversion failed (attempt %s): %s", attempt + 1, exc)
            except Exception as exc:
                self.log.warning("Claude QA failed (attempt %s): %s", attempt + 1, exc)
                if "token" in str(exc).lower() or "limit" in str(exc).lower():
                    self.log.error("Token limit still exceeded - content may need further truncation")
                    break
        return None

    def _smtp_settings(self) -> Optional[Dict[str, Any]]:
        smtp_cfg = self.config.get("notifications", {}).get("smtp")
        if not smtp_cfg:
            return None
        password = smtp_cfg.get("password")
        password_env = smtp_cfg.get("password_env")
        if password_env:
            password = os.getenv(password_env, password)
        if not password:
            self.log.warning("SMTP password missing; set %s", password_env)
            return None
        username = smtp_cfg.get("username")
        if not username:
            self.log.warning("SMTP username missing; set notifications.smtp.username")
            return None
        from_address = smtp_cfg.get("from_address") or username
        return {
            "host": smtp_cfg.get("host", "localhost"),
            "port": int(smtp_cfg.get("port", 25)),
            "username": username,
            "password": password,
            "use_tls": smtp_cfg.get("use_tls", True),
            "from_address": from_address,
        }

    def _send_email(self, subject: str, body: str, high_priority: bool = False) -> bool:
        recipients = self._notification_recipients()
        if not recipients:
            self.log.debug("No notification recipients configured; skipping email '%s'", subject)
            return False
        provider = self.config.get("notifications", {}).get("provider", "smtp").lower()
        if provider == "brevo":
            return self._send_via_brevo_api(subject, body, recipients, high_priority)
        return self._send_via_smtp(subject, body, recipients, high_priority)

    def _send_via_smtp(
        self, subject: str, body: str, recipients: List[str], high_priority: bool = False
    ) -> bool:
        settings = self._smtp_settings()
        if not settings:
            self.log.debug("SMTP not configured; skipping email '%s'", subject)
            return False
        message = EmailMessage()
        message["Subject"] = subject
        message["From"] = settings["from_address"]
        message["To"] = ", ".join(recipients)
        if high_priority:
            message["X-Priority"] = "1"
        message.set_content(body)
        try:
            with smtplib.SMTP(settings["host"], settings["port"], timeout=30) as client:
                if settings.get("use_tls", True):
                    client.starttls()
                if settings["username"]:
                    client.login(settings["username"], settings["password"])
                client.send_message(message)
            self.log.info("Notification '%s' sent to %s via SMTP", subject, recipients)
            return True
        except Exception as exc:
            self.log.error("Failed to send email '%s': %s", subject, exc)
            return False

    def _send_via_brevo_api(
        self, subject: str, body: str, recipients: List[str], high_priority: bool = False
    ) -> bool:
        brevo_cfg = self.config.get("notifications", {}).get("brevo", {})
        api_key_env = brevo_cfg.get("api_key_env", "BREVO_API_KEY")
        api_key = os.getenv(api_key_env) or brevo_cfg.get("api_key")
        if not api_key:
            self.log.warning("Brevo API key missing; set %s", api_key_env)
            return False
        sender_email = brevo_cfg.get("sender_email")
        if not sender_email:
            self.log.warning("Brevo sender_email missing in configuration")
            return False
        sender_name = brevo_cfg.get("sender_name", sender_email)
        api_base = brevo_cfg.get("api_base", "https://api.brevo.com/v3/smtp/email")
        payload = {
            "sender": {"email": sender_email, "name": sender_name},
            "to": [{"email": addr} for addr in recipients],
            "subject": subject,
            "textContent": body,
        }
        if high_priority:
            payload["headers"] = {"X-Priority": "1"}
        headers = {
            "api-key": api_key,
            "accept": "application/json",
            "content-type": "application/json",
        }
        try:
            response = requests.post(api_base, json=payload, headers=headers, timeout=30)
            if 200 <= response.status_code < 300:
                self.log.info("Notification '%s' sent via Brevo API", subject)
                return True
            self.log.error("Brevo API error %s: %s", response.status_code, response.text[:500])
            return False
        except requests.RequestException as exc:
            self.log.error("Brevo API request failed: %s", exc)
            return False

    def _load_existing_embeddings(self) -> List[Dict[str, Any]]:
        if not self.openai:
            return []
        embeddings: List[Dict[str, Any]] = []
        cursor = self.db.execute("SELECT id, keyword, title, embedding FROM posts_history WHERE embedding IS NOT NULL")
        for row in cursor:
            stored = row[3]
            vector: Optional[List[float]] = None
            if stored:
                try:
                    if isinstance(stored, bytes):
                        stored = stored.decode("utf-8")
                    vector = json.loads(stored)
                except (ValueError, TypeError):
                    vector = None
            if vector:
                embeddings.append({"id": row[0], "keyword": row[1], "embedding": vector})
        return embeddings

    def _generate_embedding_vector(self, text: str) -> Optional[List[float]]:
        if not self.openai:
            return None
        trimmed = text.strip()
        if not trimmed:
            return None
        try:
            response = self.openai.embeddings.create(model=self.embedding_model, input=trimmed)
            return response.data[0].embedding  # type: ignore[attr-defined]
        except Exception as exc:  # pragma: no cover - external call
            self.log.warning("Failed to generate embedding: %s", exc)
            return None

    @staticmethod
    def _is_duplicate_embedding(
        candidate: List[float], existing: List[Dict[str, Any]], threshold: float
    ) -> bool:
        def cosine_similarity(vec_a: List[float], vec_b: List[float]) -> float:
            dot = sum(a * b for a, b in zip(vec_a, vec_b))
            norm_a = math.sqrt(sum(a * a for a in vec_a))
            norm_b = math.sqrt(sum(b * b for b in vec_b))
            if not norm_a or not norm_b:
                return 0.0
            return dot / (norm_a * norm_b)

        for row in existing:
            similarity = cosine_similarity(candidate, row["embedding"])
            if similarity >= threshold:
                return True
        return False

    def _resolve_path(self, relative: str) -> Path:
        path = Path(relative)
        if not path.is_absolute():
            return (self.base_dir / path).resolve()
        return path

    def _record_post(self, post: GeneratedPost, status: str) -> None:
        embedding_blob = None
        embedding_vector = self._generate_embedding_vector(post.keyword or post.title)
        if embedding_vector:
            embedding_blob = json.dumps(embedding_vector).encode("utf-8")
        self.db.execute(
            "INSERT INTO posts_history (title, slug, keyword, quality_score, status, metadata, embedding) VALUES (?, ?, ?, ?, ?, ?, ?)",
            (
                post.title,
                post.slug,
                post.keyword,
                post.quality_score,
                status,
                json.dumps(post.frontmatter, ensure_ascii=False),
                embedding_blob,
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
    repo_root = script_dir.parents[1] if len(script_dir.parents) > 1 else script_dir

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
