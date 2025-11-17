# Blog Automation Script

Python implementation of the autonomous Chinese blog workflow described in `claudedocs/blog-automation-plans-nov-2025.md`. The script handles topic research, duplicate detection, multi-pass generation with Claude, quality assurance, and WordPress publishing.

## Project Layout

```
automation/blog_automation/
├─ blog_automation.py      # Main entry point
├─ config.example.yaml     # Copy to config.yaml with your settings
├─ config.yaml             # Environment-specific settings (committed here for reference)
├─ requirements.txt        # Python dependencies
├─ Dockerfile              # Container build for cron/Hostinger deployment
├─ docker-compose.yml      # Optional local run helper
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
   - `WP_REST_PASSWORD` (for WordPress REST publishing)
   - `BREVO_API_KEY` (used for Brevo transactional email API)
   - Any other secrets referenced in `config.yaml`
   - Note: Set `notifications.provider` to `brevo` (default in `config.yaml`) to send via Brevo's REST API. Brevo requires you to whitelist the host’s outbound IP under “Authorised IPs”; otherwise the API responds with 401. Switch to `smtp` if you prefer a traditional SMTP relay.
   - `content_inputs.keyword_source` is relative to `automation/blog_automation/`; use `../../claudedocs/...` to reference files in the repo root.
   - Configure hero images under `content_inputs.images` (per-topic-type blocks) so every post gets Markdown image sections automatically.
   - WordPress category targeting is configured via `wordpress.category_map`; adjust IDs (e.g., Guides `24`, Comparisons `22`) to match your taxonomy, and set `wordpress.rest.author` to the ID of your publishing account.
4. Run a dry test:
   ```bash
   python automation/blog_automation/blog_automation.py --config automation/blog_automation/config.yaml --test
   ```

### Docker Workflow

1. Copy `.env` and `config.yaml` into `automation/blog_automation/` (same folder as the Dockerfile).
2. Build the container:
   ```bash
   cd automation/blog_automation
   docker build -t svicloud/autoblog:latest .
   ```
3. Run it (mounting data/log directories so history persists):
   ```bash
   docker run --rm \
     --env-file .env \
     -v $(pwd)/data:/app/data \
     -v $(pwd)/logs:/app/logs \
     -v $(pwd)/drafts:/app/drafts \
     -v $(pwd)/../../claudedocs:/claudedocs \
      svicloud/autoblog:latest --max-posts 1
   ```
   Alternatively use `docker compose up autoblog` to respect the volumes/command in `docker-compose.yml`.
4. Cron example for Hostinger (UTC 17:00 Tue/Thu):
   ```cron
   0 17 * * 2,4 docker run --rm \
      --env-file /home/alanw/blog-automation/.env \
      -v /home/alanw/blog-automation/data:/app/data \
      -v /home/alanw/blog-automation/logs:/app/logs \
      -v /home/alanw/claudedocs:/claudedocs \
      svicloud/autoblog:latest --max-posts 1 >> /home/alanw/blog-automation/logs/cron.log 2>&1
   ```

## Command Flags

- `--config PATH` – path to config file (defaults to `config.yaml` next to the script).
- `--env PATH` – optional `.env` file to load (defaults to repo root `.env`).
- `--test` – skip publishing actions, log what would happen.
- `--max-posts N` – override weekly post cap for a single run.
- `--rebuild-embeddings` – regenerate embeddings cache / duplicate index.

## Next Implementation Steps

- Wire up real Google Search Console API calls for `research_topics`.
- Install the OpenAI Python client (`pip install openai`) so semantic duplicate checks work end-to-end. The script already stores embeddings in SQLite.
- Finish Claude prompt templates for each generation pass (currently heuristics).

Track outstanding prerequisites in `claudedocs/blog-automation-task-checklist.md`.
