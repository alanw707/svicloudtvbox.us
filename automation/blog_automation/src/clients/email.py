"""Email notification client for blog automation.

This module handles sending email notifications via SMTP or Brevo API
for automation run summaries and error alerts.
"""

import logging
import os
import smtplib
from datetime import datetime, timezone
from email.message import EmailMessage
from typing import Dict, Any, List, Optional

try:
    import requests
except ImportError:
    requests = None  # type: ignore


class EmailClient:
    """Email notification client supporting SMTP and Brevo API.

    Sends automation run summaries and error alerts via configured
    email provider (SMTP or Brevo).
    """

    def __init__(
        self,
        config: Dict[str, Any],
        logger: logging.Logger
    ):
        """Initialize email client.

        Args:
            config: Configuration dictionary with notifications settings
            logger: Logger instance for operations tracking
        """
        self.config = config
        self.log = logger

    def send_report(self, run_stats: Dict[str, Any]) -> None:
        """Send automation run summary report.

        Creates formatted summary of automation run and sends via email.

        Args:
            run_stats: Dictionary with run statistics:
                - topics_found: Number of topics discovered
                - unique_topics: Number after deduplication
                - attempts: Number of posts attempted
                - published: Number successfully published
                - dry_run: Whether this was a dry run

        Example:
            >>> stats = {
            ...     "topics_found": 15,
            ...     "unique_topics": 12,
            ...     "attempts": 5,
            ...     "published": 4,
            ...     "dry_run": False
            ... }
            >>> email_client.send_report(stats)
        """
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

    def send_alert(self, error: Exception, config_path: str = "") -> None:
        """Send error alert notification.

        Creates error alert with exception details and sends via email
        with high priority flag.

        Args:
            error: Exception that occurred
            config_path: Path to configuration file (optional)

        Example:
            >>> try:
            ...     raise RuntimeError("API failure")
            ... except Exception as exc:
            ...     email_client.send_alert(exc, "config.yaml")
        """
        self.log.error("Automation failed: %s", error)
        subject = "SVICLOUD Blog Automation ALERT"
        body = (
            "Automation run encountered an error.\n\n"
            f"Error: {error}\n"
            f"Timestamp: {datetime.now(timezone.utc).isoformat()}\n"
        )
        if config_path:
            body += f"Config: {config_path}\n"
        self._send_email(subject, body, high_priority=True)

    def _send_email(self, subject: str, body: str, high_priority: bool = False) -> bool:
        """Send email via configured provider.

        Routes to appropriate provider (SMTP or Brevo) based on config.

        Args:
            subject: Email subject line
            body: Email body text
            high_priority: Whether to mark email as high priority

        Returns:
            True if email sent successfully, False otherwise
        """
        recipients = self._notification_recipients()
        if not recipients:
            self.log.debug("No notification recipients configured; skipping email '%s'", subject)
            return False

        provider = self.config.get("notifications", {}).get("provider", "smtp").lower()
        if provider == "brevo":
            return self._send_via_brevo_api(subject, body, recipients, high_priority)
        return self._send_via_smtp(subject, body, recipients, high_priority)

    def _send_via_smtp(
        self,
        subject: str,
        body: str,
        recipients: List[str],
        high_priority: bool = False
    ) -> bool:
        """Send email via SMTP server.

        Args:
            subject: Email subject line
            body: Email body text
            recipients: List of recipient email addresses
            high_priority: Whether to mark email as high priority

        Returns:
            True if email sent successfully, False otherwise
        """
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
        self,
        subject: str,
        body: str,
        recipients: List[str],
        high_priority: bool = False
    ) -> bool:
        """Send email via Brevo API.

        Args:
            subject: Email subject line
            body: Email body text
            recipients: List of recipient email addresses
            high_priority: Whether to mark email as high priority

        Returns:
            True if email sent successfully, False otherwise

        Environment Variables:
            BREVO_API_KEY: Brevo API key (or configured in config)
        """
        if requests is None:
            self.log.warning("requests package not installed; cannot use Brevo API")
            return False

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

    def _notification_recipients(self) -> List[str]:
        """Get list of notification recipients from config.

        Returns:
            List of email addresses to receive notifications
        """
        notifications_cfg = self.config.get("notifications", {})
        recipients = notifications_cfg.get("recipients", [])
        if isinstance(recipients, str):
            return [recipients]
        return [r for r in (recipients or []) if r]

    def _smtp_settings(self) -> Optional[Dict[str, Any]]:
        """Get SMTP configuration settings.

        Returns:
            Dictionary with SMTP settings or None if not configured:
                - host: SMTP server hostname
                - port: SMTP server port
                - username: SMTP username
                - password: SMTP password
                - use_tls: Whether to use TLS
                - from_address: Sender email address

        Environment Variables:
            SMTP_PASSWORD: SMTP password (or configured in config)
        """
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
