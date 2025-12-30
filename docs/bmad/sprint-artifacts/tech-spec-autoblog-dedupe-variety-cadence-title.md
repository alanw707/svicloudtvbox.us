# Tech-Spec: Autoblog dedupe + variety + cadence cap + title safety

**Created:** 2025-12-16  
**Status:** Ready for Development

## Overview

### Problem Statement
- The autoblog automation published **3 near-duplicate “4K” posts in a single morning** (same day within minutes), violating the desired cadence (**max 2 posts per week, max 1 per run**).
- Duplicate detection is not preventing clusters of similar posts/topics (especially “4K/HDR/Wi‑Fi 6” angles).
- Some post titles are being published in a truncated/awkward state (example: `美國華人必買｜SVICLOUD TV BOX 4K電`).
- Output quality is too repetitive: similar hooks, same benefit ordering, and the same “4K/Wi‑Fi/Dolby” positioning across posts, with insufficient emphasis on:
  - Product comparison (10P+ vs 10S, SVICLOUD vs competitors)
  - Seasonal/contextual angles
  - SVICLOUD services (fast shipping, US-based warranty, bilingual support)

### Solution
1. Enforce **hard cadence caps**:
   - Max **1 post per run**
   - Max **2 posts per week** (Monday–Sunday), in a configured timezone
   - Missed schedules **do not burst**: backlog is allowed, but still one-per-run
2. Fix dedupe at the root:
   - Use consistent embeddings (store and compare **content embeddings**, not keyword vs content mismatches)
   - Add title/slug/topic-cluster guards (e.g., don’t keep publishing “4K” angle repeatedly)
   - Add “same-day / same-run” guardrails and atomic locking to prevent multiple posts from overlapping executions
3. Increase content variety by design:
   - Topic mix rules (comparison/service/geo/campaign/faq rotation)
   - Prompt/template rotation for hooks, outlines, CTA blocks, and benefit ordering
   - Competitor topic ingestion remains, but content must be **SVICLOUD-differentiated**, not a rehash
4. Make title generation safe and readable:
   - Prevent “dangling character” truncations (like ending on `電`)
   - Allow slightly longer zh titles (configurable), and validate final title structure

### Scope (In/Out)
- In:
  - Cadence caps (per-run and per-week), timezone-aware week counting
  - Atomic locking to prevent overlapping runs from publishing multiple posts
  - Duplicate detection: embeddings + title/slug fuzz + topic-cluster / overused-angle guards
  - Title generation/truncation safeguards
  - Content variety controls (topic selection + prompt/template rotation + required “service/comparison” mix)
- Out:
  - Redesigning the entire SEO strategy or keyword plan source
  - Changing WordPress theme rendering for blog titles (this is a content generation/publishing problem)
  - Building a full competitor scraper crawler (we already have targeted scrapers)

## Context for Development

### Codebase Patterns
- Autoblog pipeline (modular) lives in `automation/blog_automation/`:
  - Entry: `automation/blog_automation/main.py`
  - Orchestrator + caps + dedupe: `automation/blog_automation/src/orchestrator.py`
  - Topic discovery (GSC + competitor + filters): `automation/blog_automation/src/stages/discovery.py`
  - Title generation: `automation/blog_automation/src/stages/titling.py`
  - Drafting/prompting/enforcement: `automation/blog_automation/src/stages/briefing.py`, `outlining.py`, `drafting.py`
  - WP REST publishing: `automation/blog_automation/src/stages/publishing.py`
  - History DB: `automation/blog_automation/src/database.py` backed by `automation/blog_automation/data/posts_history.db`
- Legacy monolithic script exists at `automation/blog_automation/blog_automation.py` (kept for reference); ensure we’re fixing the actual invoked entrypoint (Docker uses `main.py`).

### Evidence / Current Behavior
- `automation/blog_automation/data/posts_history.db` shows 3 posts created on `2025-12-16` within minutes, including the truncated-title example.
- Title candidates for that post included a full version, but the final published title ended in an awkward truncation.

### Files to Reference
- `automation/blog_automation/config.yaml` (caps, schedule, title config, topic inputs)
- `automation/blog_automation/main.py` (CLI flags: `--max-posts`, `--dry-run`, `--test`)
- `automation/blog_automation/docker-compose.yml` / `run-local*.sh` (how the container is invoked)
- `automation/blog_automation/src/orchestrator.py` (cadence + dedupe + lock)
- `automation/blog_automation/src/database.py` (history + run_log schema)
- `automation/blog_automation/src/stages/discovery.py` (topic scoring + overused token filters)
- `automation/blog_automation/src/stages/titling.py` (max length + trimming rules)
- `automation/blog_automation/src/stages/drafting.py` (prompting currently over-emphasizes 4K/Wi‑Fi/Dolby every time)

### Technical Decisions
- Week definition: Monday–Sunday, configured via `publishing.timezone` (IANA tz, e.g. `America/Los_Angeles`).
- Cadence enforcement uses **both**:
  - local DB counts (fast) and
  - remote WP counts (author/status filtered) as a safety check when DB may drift.
- Locking must be **atomic** (no check-then-write race), ensuring only one publish-capable process runs at a time.
- Dedupe must compare like-for-like:
  - Store **content embeddings** (or title+excerpt embeddings) and compare those to candidates.
  - Keep keyword embeddings only for topic-level dedupe, not post-body dedupe.
- Variety must be enforced as explicit rules, not “hope Claude is creative”.

## Implementation Plan

### Tasks
- [ ] **Repro + root-cause the burst (3 posts/run):**
  - Confirm what command ran (docker compose vs scheduler vs manual) and whether `--max-posts` was overridden.
  - Validate whether overlapping runs occurred (lock race / multiple containers).
  - Add a startup log line that prints effective caps: `max_posts_per_run`, `max_posts_per_week`, timezone, schedule, and CLI overrides.
- [ ] **Atomic lock + overlap protection:**
  - Replace check-then-write lock with atomic create (`O_CREAT|O_EXCL`) and include PID/timestamp in the file.
  - Ensure lock uses the persisted `data/` volume path so multiple containers share it.
- [ ] **Timezone-aware weekly cap (Mon–Sun):**
  - Add `publishing.timezone` and compute week window in that timezone.
  - Count “WP-created” posts for the current week (published + staged) and cap at 2.
  - Ensure caps apply before any expensive generation steps.
- [ ] **Fix dedupe correctness (embedding mismatch):**
  - Store and retrieve embeddings consistently:
    - `topic_embedding` (keyword) for topic candidate dedupe
    - `content_embedding` (post title+excerpt or post body sample) for post-level dedupe
  - Update `_is_duplicate_post` to compare candidate content embeddings to stored content embeddings.
  - Add lightweight similarity checks before embeddings:
    - slug collision
    - title fuzzy match
    - “overused angle” / token heuristics (e.g., avoid repetitive “4K” titles without new angle)
- [ ] **Improve content variety (topic mix + template rotation):**
  - Add config-driven “content strategy”:
    - Target mix per 4-week window: comparisons, service/shipping, seasonal/campaign, geo, FAQ.
    - Enforce “no more than X posts/week with title containing `4K` unless comparison/campaign”.
  - Update discovery scoring to favor `comparison` + `service` + `campaign` angles over generic “4K benefits”.
  - Add hook + outline + CTA variant rotation (deterministic by keyword hash) so posts don’t share the same structure/order.
  - Ensure each post includes at least one “service differentiator” section (fast shipping, US warranty, bilingual support) but not in identical phrasing every time.
- [ ] **Title generation safety:**
  - Increase zh title max length to avoid unnatural truncation (configurable per locale).
  - Add a final validation pass:
    - avoid ending in single-character fragments for common words (e.g., `電`, `本`, `保`)
    - prefer separators like `｜` and punctuation boundaries
    - if unsafe, re-trim to a safe boundary or fall back to a shorter template candidate
- [ ] **Testing + operational checks:**
  - Add a dry-run test that simulates:
    - weekly cap reached → 0 posts created + notice email
    - duplicate candidate → skipped + notice email
    - ensure max 1 post per run even if many topics pass
  - Validate that logs clearly show caps, lock status, and skip reasons.

### Acceptance Criteria
- Cadence:
  - The automation creates **at most 1 WP post per run**.
  - The automation creates **at most 2 WP posts per week (Mon–Sun)** in the configured timezone.
  - If a schedule window was missed, the system does **not** burst; it still creates max 1/run and respects the weekly cap.
- Dedupe:
  - Near-duplicate posts (semantic similarity or title/slug similarity) are skipped consistently.
  - Dedupe comparisons are consistent (content vs content), not mismatched (content vs keyword).
  - Skip reasons are logged and a notice email is sent when a candidate is skipped due to duplication.
- Variety:
  - Posts rotate between comparison/service/campaign/geo/faq angles per the configured strategy.
  - Not every post title includes `4K` by default; “4K” appears when it’s genuinely the angle (comparison/campaign) and within configured limits.
  - Draft structure and CTA blocks vary across runs (no identical repeated outlines week-over-week).
- Titles:
  - No published title ends in awkward truncation (e.g., trailing single-character fragments like `電`).
  - Title max length is configurable, and final title is validated before publish.

## Additional Context

### Dependencies
- Uses Claude API for generation and OpenAI embeddings for dedupe; both keys must exist in the runtime environment.
- Competitor scraping uses configured URLs under `content_inputs.competitors.sites`.

### Testing Strategy
- Run `python automation/blog_automation/main.py --config automation/blog_automation/config.yaml --test` and validate:
  - per-run cap enforced
  - weekly cap enforced (simulate by backfilling DB or mocking count)
  - dedupe skip path exercised
  - title safety path exercised

### Notes
- We should confirm whether the title is truly truncated on the frontend/SEO plugin output versus only in WP admin UI; however the DB record indicates the truncated title was published and should be corrected at generation time regardless.
