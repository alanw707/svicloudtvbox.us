# Tech-Spec: Autoblog cadence control, dedupe, and tri-locale publishing

**Created:** 2025-12-10  
**Status:** Ready for Development

## Overview

### Problem Statement
- Auto-blogger is publishing clusters of near-duplicate “4K” posts and occasionally drops multiple posts on the same day.
- Cadence must be enforced: one post per run, publish windows on Tuesday and Saturday only, with a hard cap of two posts per week. Missed runs should be caught up by staging posts across subsequent windows, not in a single burst.
- Site supports three locales; each run should publish one article localized into all supported locales (same content, translated), not three distinct topics.
- When a post is skipped (duplication/cadence guard), stakeholders want an email alert.

### Solution
- Tighten scheduler + orchestrator limits: per-run cap = 1, per-week cap = 2, windows = Tue/Sat; add week-aware counting and skip logic with alerts.
- Add stronger pre-publish dedupe using embeddings and title/body similarity across local history and recent remote posts; skip and notify on collision.
- Add localization step to generate and publish translated variants for all configured locales while keeping a single topic per run.

### Scope (In/Out)
- In: scheduler cadence, per-run/week caps, duplicate detection (title/slug/content), localization pipeline, skip notifications.
- Out: changing topic discovery sources, prompt content quality, or hero art generation logic (reuse existing).

## Context for Development

### Codebase Patterns
- Automation lives in `automation/blog_automation/`; Docker entrypoint is `main.py` -> `src/orchestrator.py`.
- Scheduling helper: `scheduler.py` reads `publishing.schedule`; current schedule is Mon/Wed/Fri/Sun 09:00.
- Quality/dedupe exists but limited: embeddings are stored in SQLite (`data/posts_history.db`); orchestrator uses keyword-only “recently published” checks.
- Notifications via Brevo (`notifications.provider: brevo`) already configured for summary emails.

### Files to Reference
- `automation/blog_automation/config.yaml` (publishing schedule, caps, notifications)
- `automation/blog_automation/scheduler.py` (next run computation)
- `automation/blog_automation/src/orchestrator.py` (run loop, caps, dedupe, lock)
- `automation/blog_automation/src/database.py` (posts_history, run_log)
- `automation/blog_automation/src/stages/publishing.py` and `src/clients/email.py` (REST publish + email)
- `automation/blog_automation/src/clients/openai_client.py` (embeddings)

### Technical Decisions
- Use config-driven schedule: set `publishing.schedule` to Tuesday/Saturday 09:00; set `publishing.max_posts_per_week: 2` and add `publishing.max_posts_per_run: 1`.
- Week-aware guard: count published posts in the current ISO week from `run_log` and/or WP recent posts; enforce remaining quota and hard cap per run.
- Backlog handling: do not emit multiple posts in a single run. If the last successful run missed N scheduled windows, process only `max_posts_per_run` in the current run; remaining backlog naturally clears on subsequent scheduled runs.
- Duplicate protection: before drafting/publishing, compute title and content embeddings and compare against:
  - local history (`posts_history` embeddings) and
  - recent remote posts via REST list (last N posts, e.g., 20) on all locales.
  Skip publish if similarity >= threshold (e.g., 0.85) or if slug/title fuzzy matches; send alert email with reason.
- Localization: after generating the canonical post, produce translated variants for each locale in `publishing.locales` (list of locale codes). Publish one base post + translations linked together (reuse featured media). Slug pattern `slug-{locale}`. Preserve categories; pass language parameter supported by the WP multilingual plugin (confirm actual param, default assumption: `lang` or Polylang `lang`).
- Notifications: reuse Brevo email client; send a concise alert when a run skips publishing due to cadence cap or duplication, including keyword/title and reason.

## Implementation Plan

### Tasks
- [ ] Update config defaults: `publishing.schedule` -> Tue 09:00, Sat 09:00; `publishing.max_posts_per_week: 2`; add `publishing.max_posts_per_run: 1`; add `publishing.locales` array (three locale codes from site).
- [ ] Scheduler (`scheduler.py`): ensure schedule uses the updated config; no change to cadence logic besides consuming new schedule.
- [ ] Orchestrator caps: compute current ISO-week published count (from `run_log` or WP recent posts) and remaining quota; limit attempts to `min(max_posts_per_run, remaining_weekly_quota)`. If quota is 0, exit early and send alert.
- [ ] Backlog staging: detect missed windows (based on last run timestamp vs schedule); log backlog count but still respect per-run cap=1 so backlog spills into future windows automatically.
- [ ] Duplicate checks: before generation/publish, compare proposed title/slug/content embeddings against:
      - local embeddings (`posts_history` via `DatabaseManager`)
      - recent WP posts (fetch recent titles/slugs/contents) across locales.
      Skip and alert on similarity >= threshold or fuzzy title/slug match; do not consume weekly quota when skipped.
- [ ] Localization pipeline: after generating one article, translate body/excerpt/title into each configured locale; publish via REST with language parameter and link translations; reuse hero media and categories; store translation metadata in `posts_history.metadata`.
- [ ] Notifications: extend email client/reporting to send alert on skip (duplicate or cadence cap), including keyword/title/reason.
- [ ] Testing: dry-run path that exercises cadence blocking (quota reached) and duplicate skipping; unit-style check for week counting and per-run cap; smoke run with `--test` ensures only one post attempt and emits localization stubs.

### Acceptance Criteria
- Cadence: Automation runs only respect Tuesday/Saturday windows; each run attempts at most 1 post; no more than 2 posts are published in any ISO week. If quota is exhausted, run exits without publishing and sends an alert.
- Backlog: After downtime, subsequent runs continue at 1 post per window until backlog clears; no run publishes more than 1 post.
- Dedupe: Title/slug/content similarity checks block near-duplicate posts (local or recent remote) and send an alert; skipped posts do not consume weekly quota.
- Localization: Each successful run produces one base article plus translated variants for all configured locales, with per-locale slugs and language assignment, sharing the same topic/hero asset.
- Notifications: End-of-run summary remains; additional alert emails fire when a post is skipped due to duplication or cadence cap.

## Additional Context

### Dependencies
- OpenAI embeddings and Brevo email keys must be present in environment.
- WordPress multilingual plugin must support language parameter on REST posts (confirm actual param and category mapping per locale).

### Testing Strategy
- Dry-run with weekly quota already met -> expect no publish and alert.
- Simulated duplicate (same keyword/title) -> expect skip + alert.
- Successful run -> 1 base + N translations published, hero media reused, categories set; run_log records 1 attempt/1 published.
