# Tech-Spec: Autoblog 9am run failure (IndentationError)

**Created:** 2025-12-23
**Status:** Completed

## Overview

### Problem Statement
The local Docker autoblog scheduler run at Tuesday 9:00am PST failed with a Python `IndentationError` in `/app/src/orchestrator.py` (line 296). The run exited before publishing, so no weekly post was created. The cadence target is two posts per week globally (max one per run).

### Solution
Fix the indentation error in the orchestrator source, rebuild the Docker image, and restart the scheduler container so it runs the corrected code. Add a lightweight syntax check at image build time to prevent shipping invalid Python in the future. Validate a successful run via container logs and the SQLite `run_log` table.

### Scope (In/Out)
- In: `automation/blog_automation` Python sources, Docker image build, scheduler container restart, run log verification.
- Out: content strategy changes, WordPress theme changes, marketing copy edits.

## Context for Development

### Codebase Patterns
- Python 3.12, 4-space indentation, logging via `logging` module.
- Scheduler runs `scheduler.py` which invokes `main.py` to run the pipeline.
- Run history tracked in SQLite (`data/posts_history.db`) with `run_log` and `posts_history`.

### Files to Reference
- `automation/blog_automation/src/orchestrator.py`
- `automation/blog_automation/scheduler.py`
- `automation/blog_automation/main.py`
- `automation/blog_automation/Dockerfile`
- `automation/blog_automation/config.yaml`
- `automation/blog_automation/data/posts_history.db`
- `automation/blog_automation/logs/automation.log`
- `automation/blog_automation/logs/cron.log`

### Technical Decisions
- Keep the Tuesday/Saturday 09:00 PST schedule and weekly cap as-is.
- Fail fast on Python syntax errors during Docker image build.
- Use the existing `svicloud/autoblog:latest` image/tag for the scheduler container.

## Implementation Plan

### Tasks

- [x] Task 1: Verify and fix indentation around the `if created == 0:` block in `automation/blog_automation/src/orchestrator.py` if it is mis-indented.
- [x] Task 2: Rebuild the Docker image from the repo and restart the `autoblog-scheduler` container to pick up the corrected code.
- [x] Task 3: Add a build-time syntax check to `automation/blog_automation/Dockerfile` (e.g., `python -m py_compile src/orchestrator.py` or `python -m compileall -q /app/src`).
- [x] Task 4: Run a manual one-off execution (scheduler `--run-once` or `main.py` with config) and confirm success in container logs.
- [x] Task 5: Verify `run_log` updates in `data/posts_history.db` and confirm a post was created (draft/published) or a non-crash skip notice was recorded.

### Acceptance Criteria

- [x] The scheduled 09:00 PST run completes without `IndentationError` or Python syntax errors.
- [x] A new `run_log` record exists for the run with non-zero `topics_found` and `posts_attempted` >= 1.
- [x] At least one WP post is created as draft/published unless caps/quality/dedupe block it; in that case the run completes and logs the skip.
- [x] Docker image build fails if any Python syntax error exists in `src/`.

## Additional Context

### Dependencies
- Docker runtime, local `.env` with API keys/REST credentials, WordPress REST API access.

### Testing Strategy
- `python -m py_compile automation/blog_automation/src/orchestrator.py`
- Manual run via container or `main.py --test` to confirm no crash.
- `docker logs autoblog-scheduler` and SQLite query of `run_log` for validation.

### Notes
The running `autoblog-scheduler` container currently has a mis-indented `if created == 0:` block in `/app/src/orchestrator.py`, which is why the 9am run failed. Rebuilding the image from the corrected repo state should resolve the issue.
