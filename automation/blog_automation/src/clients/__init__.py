"""Client integrations for external services.

This package contains client wrappers for:
- OpenAI: Embeddings generation and duplicate detection
- Email: Notification delivery via SMTP or Brevo API
"""

from .openai_client import OpenAIClient
from .email import EmailClient

__all__ = ["OpenAIClient", "EmailClient"]
