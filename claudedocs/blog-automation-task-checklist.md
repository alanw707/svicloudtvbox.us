# Blog Automation Task Checklist

This list covers everything you need to gather or configure before the automation build can run end-to-end.

## Credentials & Secrets
- [ ] Claude API key with quota for multi-pass generation
- [ ] OpenAI API key for text-embedding-3-small duplicate checks
- [ ] WordPress application password for an editor/admin user with zh-Hant publishing rights
- [ ] Google Search Console service-account JSON scoped to the svicloudtvbox.us property
- [ ] SMTP credentials (or sendmail path) for weekly summary/error emails

## Server & Environment
- [ ] Confirm target install path (e.g., `/home/alanw/blog-automation`) and ensure 2GB free disk space
- [ ] Verify Python 3.10+ and git are installed; create a virtualenv location if preferred
- [ ] Decide on storage layer (default SQLite file vs. Postgres connection details)
- [ ] Ensure outbound HTTPS is allowed for Claude/OpenAI/WordPress API calls

## WordPress Preparation
- [ ] Verify REST API access from the server IP (no firewall blocks)
- [ ] Confirm Chinese locale (`zh-hant`) posts can be created via API with proper categories/tags
- [ ] Create a staging workflow (draft status or future publish date) and confirm auto-publish policy

## Content & SEO Inputs
- [ ] Finalize initial Chinese keyword priority list (from `claudedocs/chinese-keyword-ranking-strategy-nov-2025.md` plus new topics)
- [ ] Gather Chinese tone/voice guidelines, brand terminology, and prohibited claims
- [ ] Collect sample testimonials, FAQs, or product differentiators to feed prompts
- [ ] List geographic focus pages (e.g., 加州, 紐約, 德州) for localized posts

## Monitoring & Operations
- [ ] Choose email recipients for weekly summary + alert messages
- [ ] Decide log retention policy (default 90 days) and backup location for `posts_history.db`
- [ ] Schedule cron cadence (weekly or desired frequency) and confirm timezone
- [ ] Define manual QA sign-off steps for the initial pilot week

Review this file with Codex as you complete items so we can check off tasks and move to implementation.
