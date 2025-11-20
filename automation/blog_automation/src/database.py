"""Database operations for SVICLOUD blog automation."""
from __future__ import annotations

import json
import sqlite3
from datetime import datetime, timezone
from pathlib import Path
from typing import Any, Dict, List, Optional, Set

from .models import GeneratedPost


class DatabaseManager:
    """Manages SQLite database operations for blog post tracking and analytics.

    Handles two main tables:
    - posts_history: Stores all generated posts with metadata and embeddings
    - run_log: Tracks automation execution history for monitoring
    """

    def __init__(self, db_path: str | Path):
        """Initialize database connection and create tables if needed.

        Args:
            db_path: Path to SQLite database file
        """
        self.db_path = Path(db_path)
        self.db_path.parent.mkdir(parents=True, exist_ok=True)
        self.conn = sqlite3.connect(self.db_path)
        self._create_tables()

    def _create_tables(self) -> None:
        """Create database tables if they don't exist."""
        self.conn.execute(
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
        self.conn.execute(
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
        self.conn.commit()

    def get_existing_keywords(self) -> Set[str]:
        """Retrieve all existing keywords from posts_history (case-insensitive).

        Returns:
            Set of lowercased keywords already covered in the database
        """
        return {
            row[0].lower()
            for row in self.conn.execute(
                "SELECT DISTINCT keyword FROM posts_history WHERE keyword IS NOT NULL"
            )
            if row[0]
        }

    def keyword_exists(self, keyword: str) -> bool:
        """Check whether a keyword already exists in posts_history (case-insensitive)."""
        cursor = self.conn.execute(
            "SELECT 1 FROM posts_history WHERE LOWER(keyword) = ? LIMIT 1",
            (keyword.lower(),)
        )
        return cursor.fetchone() is not None

    def load_existing_embeddings(self) -> List[Dict[str, Any]]:
        """Load all stored embeddings for duplicate detection.

        Returns:
            List of dicts with 'id', 'keyword', and 'embedding' (vector) keys
        """
        embeddings: List[Dict[str, Any]] = []
        cursor = self.conn.execute(
            "SELECT id, keyword, title, embedding FROM posts_history WHERE embedding IS NOT NULL"
        )
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

    def record_post(
        self,
        post: GeneratedPost,
        status: str,
        embedding_vector: Optional[List[float]] = None
    ) -> None:
        """Save a post record to posts_history table.

        Args:
            post: Generated blog post with all metadata
            status: Post status ('published', 'draft', 'failed', etc.)
            embedding_vector: Optional embedding vector for duplicate detection
        """
        embedding_blob = None
        if embedding_vector:
            embedding_blob = json.dumps(embedding_vector).encode("utf-8")

        self.conn.execute(
            """
            INSERT INTO posts_history
            (title, slug, keyword, quality_score, status, metadata, embedding)
            VALUES (?, ?, ?, ?, ?, ?, ?)
            """,
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
        self.conn.commit()

    def record_run(
        self,
        topics_found: int,
        unique_topics: int,
        posts_attempted: int,
        posts_published: int,
        dry_run: bool = False
    ) -> None:
        """Log an automation run to run_log table for monitoring.

        Args:
            topics_found: Total topics discovered from all sources
            unique_topics: Topics remaining after deduplication
            posts_attempted: Number of posts generation was attempted
            posts_published: Number of posts successfully published
            dry_run: Whether this was a dry run (no actual publishing)
        """
        self.conn.execute(
            """
            INSERT INTO run_log
            (run_at, topics_found, unique_topics, posts_attempted, posts_published, dry_run)
            VALUES (?, ?, ?, ?, ?, ?)
            """,
            (
                datetime.now(timezone.utc).isoformat(),
                topics_found,
                unique_topics,
                posts_attempted,
                posts_published,
                1 if dry_run else 0,
            ),
        )
        self.conn.commit()

    def close(self) -> None:
        """Close the database connection."""
        if self.conn:
            self.conn.close()

    def __enter__(self):
        """Context manager entry."""
        return self

    def __exit__(self, exc_type, exc_val, exc_tb):
        """Context manager exit - ensures connection is closed."""
        self.close()
