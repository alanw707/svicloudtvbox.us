# Blog Automation Script

Python implementation of the autonomous Chinese blog workflow described in `claudedocs/blog-automation-plans-nov-2025.md`. The script handles topic research, duplicate detection, multi-pass generation with Claude, quality assurance, and WordPress publishing.

## Project Layout

```
automation/blog_automation/
├─ blog_automation.py      # Main entry point
├─ config.example.yaml     # Copy to config.yaml with your settings
├─ requirements.txt        # Python dependencies
└─ README.md               # This file
```

## Quick Start

1. Create a virtual environment (Python 3.10+ recommended) and install dependencies:
   ```bash
   python3 -m venv .venv
   source .venv/bin/activate
   pip install -r automation/blog_automation/requirements.txt
   ```
2. Copy the example config and customize values:
   ```bash
   cp automation/blog_automation/config.example.yaml automation/blog_automation/config.yaml
   ```
3. Ensure your `.env` (or environment variables) exports:
   - `CLAUDE_API_KEY`
   - `OPENAI_API_KEY`
   - `WP_REST_PASSWORD` (or WP-CLI creds depending on config)
   - `SMTP_PASSWORD` (if notifications enabled)
   - Any other secrets referenced in `config.yaml`
4. Run a dry test:
   ```bash
   python automation/blog_automation/blog_automation.py --config automation/blog_automation/config.yaml --test
   ```

## Command Flags

- `--config PATH` – path to config file (defaults to `config.yaml` next to the script).
- `--env PATH` – optional `.env` file to load (defaults to repo root `.env`).
- `--test` – skip publishing actions, log what would happen.
- `--max-posts N` – override weekly post cap for a single run.
- `--rebuild-embeddings` – regenerate embeddings cache / duplicate index.

## Next Implementation Steps

- Wire up real Google Search Console API calls for `research_topics`.
- Implement OpenAI embedding storage and cosine similarity computation.
- Finish Claude prompt templates for each generation pass.
- Connect to WordPress via REST or WP-CLI based on config.
- Add SMTP-backed email summaries and alerts.

Track outstanding prerequisites in `claudedocs/blog-automation-task-checklist.md`.
