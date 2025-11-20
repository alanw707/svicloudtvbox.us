"""OpenAI API client for embeddings generation.

This module handles OpenAI API integration for generating text embeddings
and detecting semantic duplicates using cosine similarity.
"""

import logging
import math
import os
from typing import List, Dict, Any, Optional

try:
    from openai import OpenAI
except ImportError:
    OpenAI = None  # type: ignore


class OpenAIClient:
    """OpenAI API client for text embeddings.

    Generates semantic embeddings for keywords and detects duplicates
    using cosine similarity comparison.
    """

    def __init__(
        self,
        logger: logging.Logger,
        model: str = "text-embedding-3-small"
    ):
        """Initialize OpenAI client.

        Args:
            logger: Logger instance for operations tracking
            model: Embedding model ID (default: text-embedding-3-small)

        Environment Variables:
            OPENAI_API_KEY: Required OpenAI API key
            OPENAI_PROJECT: Optional OpenAI project ID

        Raises:
            RuntimeError: If openai package not installed or API key missing
        """
        self.log = logger
        self.model = model
        self.client = self._init_client()

    def _init_client(self) -> Optional[OpenAI]:
        """Initialize OpenAI client with credentials.

        Returns:
            OpenAI client instance or None if package unavailable

        Raises:
            RuntimeError: If OPENAI_API_KEY not set
        """
        if OpenAI is None:
            self.log.warning("openai package not installed; embeddings disabled until deps installed")
            return None

        api_key = os.getenv("OPENAI_API_KEY")
        if not api_key:
            raise RuntimeError("OPENAI_API_KEY environment variable not set")

        project = os.getenv("OPENAI_PROJECT")

        return OpenAI(api_key=api_key, project=project)

    def generate_embedding(self, text: str) -> Optional[List[float]]:
        """Generate embedding vector for text.

        Creates semantic embedding using OpenAI embeddings API.
        Returns None if client unavailable or API call fails.

        Args:
            text: Text to embed (will be trimmed)

        Returns:
            List of float embedding values or None on failure

        Example:
            >>> client = OpenAIClient(logger)
            >>> embedding = client.generate_embedding("SVICLOUD 10P+")
            >>> len(embedding)
            1536  # Dimension for text-embedding-3-small
        """
        if not self.client:
            return None

        trimmed = text.strip()
        if not trimmed:
            return None

        try:
            response = self.client.embeddings.create(
                model=self.model,
                input=trimmed
            )
            return response.data[0].embedding  # type: ignore[attr-defined]

        except Exception as exc:
            self.log.warning("Failed to generate embedding: %s", exc)
            return None

    @staticmethod
    def is_duplicate(
        candidate: List[float],
        existing: List[Dict[str, Any]],
        threshold: float = 0.85
    ) -> bool:
        """Check if candidate embedding is duplicate of existing.

        Uses cosine similarity to detect semantic duplicates.
        Returns True if any existing embedding exceeds similarity threshold.

        Args:
            candidate: Embedding vector to check
            existing: List of dicts with 'embedding' key containing vectors
            threshold: Similarity threshold (0.0-1.0, default: 0.85)

        Returns:
            True if duplicate detected (similarity >= threshold)

        Example:
            >>> candidate = client.generate_embedding("小雲電視盒")
            >>> existing = [{"embedding": stored_embedding, "keyword": "SVICLOUD"}]
            >>> client.is_duplicate(candidate, existing, threshold=0.85)
            False  # Different enough
        """
        def cosine_similarity(vec_a: List[float], vec_b: List[float]) -> float:
            """Calculate cosine similarity between two vectors.

            Returns value from 0.0 (orthogonal) to 1.0 (identical).

            Args:
                vec_a: First embedding vector
                vec_b: Second embedding vector

            Returns:
                Cosine similarity score (0.0-1.0)
            """
            # Dot product
            dot = sum(a * b for a, b in zip(vec_a, vec_b))

            # Magnitudes
            norm_a = math.sqrt(sum(a * a for a in vec_a))
            norm_b = math.sqrt(sum(b * b for b in vec_b))

            # Avoid division by zero
            if not norm_a or not norm_b:
                return 0.0

            return dot / (norm_a * norm_b)

        # Check similarity against all existing embeddings
        for row in existing:
            similarity = cosine_similarity(candidate, row["embedding"])
            if similarity >= threshold:
                return True

        return False
