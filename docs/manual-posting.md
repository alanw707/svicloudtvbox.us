## Manual post checklist

1) Prefer automation: `./automation/blog_automation/run-local.sh --max-posts 1` (respects HTML formatting and categories).
2) If you must post by hand, always convert Markdown to HTML first:
   - `python3 automation/blog_automation/scripts/render_markdown.py /path/to/post.md > /tmp/post.html`
   - Then POST with `content@/tmp/post.html` (or paste HTML in WP editor).
3) Quick QA after publish: confirm table renders, bullets/numbered lists display properly, and “延伸閱讀” links are clickable.
