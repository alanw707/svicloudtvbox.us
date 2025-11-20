#!/usr/bin/env python3
"""SVICLOUD Blog Automation CLI.

Command-line interface for automated blog post generation and publishing.
"""

import argparse
import sys
from pathlib import Path

from dotenv import load_dotenv

from src.orchestrator import BlogAutomation


def main() -> int:
    """Main CLI entry point.

    Returns:
        Exit code (0 for success, 1 for error)
    """
    parser = argparse.ArgumentParser(
        description="SVICLOUD Blog Automation - Automated content generation and publishing",
        formatter_class=argparse.RawDescriptionHelpFormatter,
        epilog="""
Examples:
  # Run with default config and .env
  python main.py

  # Dry run (no actual publishing)
  python main.py --dry-run

  # Generate maximum 3 posts
  python main.py --max-posts 3

  # Test mode (dry run with 1 post)
  python main.py --test

  # Custom config and .env files
  python main.py --config my-config.yaml --env .env.staging

  # Enable debug logging
  python main.py --debug
        """
    )

    script_dir = Path(__file__).resolve().parent
    repo_root_env = script_dir.parent.parent.parent / ".env"

    parser.add_argument(
        "--config",
        type=Path,
        default=Path("config.yaml"),
        help="Path to configuration file (default: config.yaml)"
    )

    parser.add_argument(
        "--env",
        type=Path,
        default=repo_root_env,
        help="Path to .env file with credentials (default: repo root .env)"
    )

    parser.add_argument(
        "--dry-run",
        action="store_true",
        help="Simulate operations without actual publishing"
    )

    parser.add_argument(
        "--test",
        action="store_true",
        help="Test mode: dry run with max_posts=1"
    )

    parser.add_argument(
        "--max-posts",
        type=int,
        metavar="N",
        help="Maximum number of posts to generate (overrides config)"
    )

    parser.add_argument(
        "--debug",
        action="store_true",
        help="Enable debug logging"
    )

    args = parser.parse_args()

    # Load environment variables from .env file
    env_loaded = False
    env_candidates = [args.env]

    # Fallback to automation-local .env when running inside Docker image
    if (
        not args.env.exists()
        and args.env.resolve() == repo_root_env
    ):
        env_candidates.append(script_dir / ".env")

    for env_path in env_candidates:
        if env_path.exists():
            load_dotenv(env_path)
            env_loaded = True
            break

    if not env_loaded:
        print(f"Warning: .env file not found: {env_candidates[-1]}", file=sys.stderr)
        print("Continuing without .env (credentials must be in environment)", file=sys.stderr)

    # Resolve config path
    config_path = args.config.resolve()
    if not config_path.exists():
        print(f"Error: Config file not found: {config_path}", file=sys.stderr)
        return 1

    # Determine dry run mode
    dry_run = args.dry_run or args.test

    # Determine max posts
    max_posts = args.max_posts
    if args.test and not max_posts:
        max_posts = 1

    try:
        # Initialize automation
        automation = BlogAutomation(config_path=config_path, dry_run=dry_run)

        # Override log level if debug flag set
        if args.debug:
            import logging
            automation.log.setLevel(logging.DEBUG)
            automation.log.debug("Debug logging enabled")

        # Run automation pipeline
        automation.run(max_posts=max_posts)

        # Clean up
        automation.close()

        return 0

    except KeyboardInterrupt:
        print("\nAutomation interrupted by user", file=sys.stderr)
        return 130  # Standard exit code for SIGINT

    except FileNotFoundError as exc:
        print(f"Error: {exc}", file=sys.stderr)
        return 1

    except Exception as exc:
        print(f"Fatal error: {exc}", file=sys.stderr)
        import traceback
        traceback.print_exc()

        # Try to send alert if automation was initialized
        try:
            if 'automation' in locals():
                automation.email.send_alert(exc, str(config_path))
        except Exception:
            pass  # Ignore errors in error handler

        return 1


if __name__ == "__main__":
    sys.exit(main())
