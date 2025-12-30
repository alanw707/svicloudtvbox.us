# Tech-Spec: Autoblog Scheduler Auto-Publish + Resilience

**Created:** 2025-12-27
**Status:** Ready for Development

## Overview

### Problem Statement
The autoblog scheduler missed today's scheduled run after the Docker container restarted and exited. When runs do succeed, posts are created as drafts due to the default WordPress REST status, requiring manual publishing even when quality checks pass.

### Solution
1) Ensure the scheduler container auto-restarts after Docker restarts to avoid missing scheduled windows.
2) Set the WordPress REST default status to publish so that posts go live automatically when all quality and duplicate checks pass.
3) Preserve the existing quality gate: posts below threshold are still saved as drafts.

### Scope (In/Out)
**In scope**
- Update autoblog config to default to publish when validations pass.
- Ensure container restart policy is set to restart automatically.
- Document the operational behavior and verification steps.

**Out of scope**
- Changing the quality scoring model or thresholds.
- Altering topic discovery, drafting logic, or translation behavior.
- Adding new monitoring/alerting systems.

## Context for Development

### Codebase Patterns
- Publishing behavior is configured in `wordpress.rest.status` and enforced in the orchestrator and publisher layers.
- Quality gates are enforced before publishing; sub-threshold content is saved as draft.
- Docker container runs `scheduler.py` and reads `/app/config.yaml`, which is bind-mounted from the host and read-only in-container.

### Files to Reference
- `/home/alanw/projects/svicloudtvbox.us/automation/blog_automation/config.yaml`
- `/home/alanw/projects/svicloudtvbox.us/automation/blog_automation/src/orchestrator.py`
- `/home/alanw/projects/svicloudtvbox.us/automation/blog_automation/src/stages/publishing.py`
- Docker container `autoblog-scheduler` restart policy (via `docker update` or compose)

### Technical Decisions
- Use config-driven `wordpress.rest.status: publish` so successful QA posts publish automatically.
- Maintain the existing quality threshold to prevent low-quality posts from going live.
- Set container restart policy to `unless-stopped` to recover from Docker daemon restarts.

## Implementation Plan

### Tasks
- [ ] Update `/home/alanw/projects/svicloudtvbox.us/automation/blog_automation/config.yaml` to `wordpress.rest.status: "publish"`.
- [ ] Ensure `autoblog-scheduler` uses a restart policy (`unless-stopped`) in Docker runtime or compose definition.
- [ ] Verify next scheduled run produces a published post when quality passes, and draft when it fails.

### Acceptance Criteria
- [ ] Given the container restarts, when Docker is restarted, then `autoblog-scheduler` comes back up automatically.
- [ ] Given a successful QA run, when a post is created, then its WordPress status is `publish`.
- [ ] Given a QA score below threshold, when a post is generated, then it is saved as `draft`.

## Additional Context

### Dependencies
- WordPress REST credentials in `.env` must be valid.
- Docker runtime must allow restart policies.

### Testing Strategy
- Run `scheduler.py --run-once` and confirm post status in WP.
- Confirm `docker inspect` shows restart policy `unless-stopped`.

### Notes
- `autoblog-scheduler` binds `/app/config.yaml` read-only from the host; update the host file, not the container.
