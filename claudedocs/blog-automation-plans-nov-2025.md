# Blog Content Automation Plans - November 2025

## Executive Summary

Two complete automation solutions for fully automated Chinese blog content creation, from keyword research through WordPress publishing.

**Your Requirements:**
- Full automation (no manual review)
- WordPress deployment via WP-CLI + application password
- Duplicate content detection
- Budget: $20-50/month (or free option)
- SSH access to Hostinger server
- Python proficiency

---

## Option 1: n8n Cloud Solution ($24/month)

### Overview
- **Platform**: n8n Cloud-hosted workflow automation
- **Cost**: $20/month (n8n) + $4/month (AI APIs) = $24/month
- **Pros**: Visual workflows, built-in integrations, no server maintenance
- **Cons**: Monthly subscription, vendor lock-in
- **Best For**: Less technical users, prefer GUI over code

### Architecture
```
n8n Cloud Workflows (triggered weekly)
├─ Workflow 1: Content Research (GSC API → topic selection)
├─ Workflow 2: Duplicate Detection (embeddings + similarity)
├─ Workflow 3: Content Generation (5-pass Claude system)
├─ Workflow 4: Publishing (WordPress REST API → staging → production)
└─ Workflow 5: Monitoring (alerts + metrics)
```

### Key Features
- **Native WordPress Integration**: Built-in WordPress node (no WP-CLI needed)
- **Visual Debugging**: See workflow execution in real-time
- **Error Handling**: Automatic retries, email alerts
- **99.9% Uptime**: Cloud-hosted reliability
- **400+ Integrations**: Connect to any service

### Cost Breakdown
```
n8n Cloud Starter:                    $20.00/month
Claude API (30 posts):                 $3.90/month
├─ Haiku (brief, outline, QA):        $0.72
├─ Sonnet (full generation):          $2.52
├─ Regenerations (20%):                $0.65
OpenAI Embeddings (duplicate check):   $0.01/month
────────────────────────────────────────────────
Total:                                $23.91/month
Cost per post:                        $0.80
```

### Implementation Timeline
- **Week 1-2**: Platform setup + duplicate detection
- **Week 3-4**: Content generation pipeline
- **Week 5-6**: Publishing + safety mechanisms
- **Week 7-8**: Content research automation
- **Week 9-12**: Soft launch → full automation
- **Total**: 8 weeks to autonomous operation

### Technical Details

#### Duplicate Detection
```yaml
Method: Semantic Embeddings
API: OpenAI text-embedding-3-small
Algorithm: Cosine similarity
Threshold: 0.85 (reject if similarity > 0.85)
Cost: $0.02 per 1M tokens (~$0.0002 per title check)
Storage: n8n workflow static data (cached 7 days)
```

#### Multi-Pass Content Generation
```yaml
Pass 1 - Strategic Brief:
  Model: Claude 3.5 Haiku
  Input: Target keyword, brand guidelines
  Output: 500-character brief (zh-TW)
  Cost: ~$0.008 per post

Pass 2 - Outline:
  Model: Claude 3.5 Haiku
  Input: Strategic brief
  Output: 5-7 section headings with key points
  Cost: ~$0.008 per post

Pass 3 - Quality Check:
  Model: Claude 3.5 Haiku
  Input: Outline
  Decision: Approve → Continue | Reject → Regenerate
  Cost: ~$0.008 per post

Pass 4 - Full Post:
  Model: Claude 3.5 Sonnet
  Input: Approved outline, brand examples
  Output: 4000+ Chinese characters + YAML frontmatter
  Cost: ~$0.084 per post

Pass 5 - Final QA:
  Model: Claude 3.5 Haiku
  Checks: Grammar, SEO, brand, technical
  Score: 0-100 (>80 = publish, <80 = review)
  Cost: ~$0.008 per post
```

#### Quality Validation System
```yaml
Grammar & Language (0-25 points):
  - Traditional Chinese character validation
  - Sentence structure analysis
  - Punctuation correctness

SEO Compliance (0-25 points):
  - Keyword density: 1-2%
  - Meta description: 150-160 chars
  - Title length: 50-60 chars
  - H2/H3 heading distribution

Brand Consistency (0-25 points):
  - Product name accuracy
  - Tone alignment
  - No contradictory claims
  - Consistent terminology

Technical Quality (0-25 points):
  - Internal links valid (3-5 links)
  - Markdown syntax correct
  - Frontmatter complete
  - Readability score

Threshold:
  - >80: Auto-publish
  - 60-80: Human review
  - <60: Regenerate
```

#### Publishing Pipeline
```yaml
Stage 1 - Staging Deployment:
  - Create draft post in WordPress
  - Run automated tests (rendering, links, SEO tags)
  - Email preview link to admin
  - 24-hour hold period (emergency intervention window)

Stage 2 - Automated Testing:
  - HTTP 200 status check
  - No PHP errors
  - Internal links resolve
  - Meta tags present

Stage 3 - Production Publishing:
  - Update post status to 'publish'
  - Schedule optimal time (Tuesday/Thursday 9 AM PST)
  - Auto-assign categories
  - Request Google Search Console indexing

Stage 4 - Backup:
  - Save markdown to GitHub repo
  - Git commit + push
  - Maintain version history
```

#### Safety Mechanisms
```yaml
Pre-Publishing:
  - 24-hour staging hold
  - Quality score threshold (>80)
  - Rate limiting (max 1/day, 7/week)

Post-Publishing:
  - Automatic rollback capability
  - Error monitoring
  - Weekly health checks

Emergency Controls:
  - Emergency stop webhook
  - Workflow pause/resume
  - Email + SMS alerts
```

### n8n Workflow Examples

#### Workflow 1: Content Research
```
Trigger: Schedule (Every Monday 9 AM)
├─ Google Sheets: Read keyword queue
├─ HTTP Request: Fetch GSC data
│   └─ Filter: High impressions, low CTR, position 11-20
├─ Claude API: Analyze opportunities
├─ Function: Score and prioritize topics
├─ Duplicate Check: Compare embeddings
└─ Output: Top 2 topics for the week
```

#### Workflow 3: Content Generation
```
Trigger: New topic approved
├─ Claude Haiku: Generate strategic brief
├─ Wait: 2 seconds (rate limiting)
├─ Claude Haiku: Generate outline
├─ Wait: 2 seconds
├─ Claude Haiku: Validate outline (score)
├─ IF score >75:
│   ├─ Claude Sonnet: Generate full post
│   ├─ Function: Format markdown + frontmatter
│   └─ Claude Haiku: Final quality check
├─ ELSE:
│   └─ Regenerate outline with feedback
└─ Output: Complete blog post (if QA >80)
```

#### Workflow 4: Publishing
```
Trigger: New post QA passed
├─ WordPress Node: Create draft post (staging)
├─ HTTP Request: Test rendering
├─ Function: Validate links + SEO tags
├─ Email: Send preview link to admin
├─ Wait: 24 hours (scheduled trigger)
├─ WordPress Node: Update status to 'publish'
├─ GitHub API: Save markdown file
├─ HTTP Request: Request GSC indexing
└─ Email: Success notification with URL
```

---

## Option 2: Free Python Solution ($4/month)

### Overview
- **Platform**: Python script on Hostinger server (FREE)
- **Automation**: Linux cron job (FREE)
- **Cost**: $4/month (Claude API only)
- **Pros**: Full control, no subscription, portable, 85% cheaper
- **Cons**: Need Python skills, manual server setup
- **Best For**: Technical users comfortable with Python and server management

### Architecture
```
Hostinger Server
├─ /home/username/blog-automation/
│   ├─ blog_automation.py (main script, ~500 lines)
│   ├─ config.yaml (settings + prompts)
│   ├─ .env (API keys, secrets)
│   ├─ requirements.txt (dependencies)
│   ├─ posts_history.db (SQLite, duplicate tracking)
│   └─ logs/ (execution logs)
└─ Cron job: 0 9 * * 1 (Every Monday 9 AM)
```

### Cost Breakdown
```
Server hosting:                        $0 (existing Hostinger)
Platform/SaaS:                         $0 (Python script)
Cron job:                              $0 (built-in Linux)
Claude API (30 posts):                 $3.90/month
OpenAI Embeddings:                     $0.01/month
────────────────────────────────────────────────
Total:                                 $3.91/month
Cost per post:                         $0.13
Savings vs n8n:                        $20/month (83% cheaper)
```

### Python Script Structure

#### Main Script: `blog_automation.py`
```python
#!/usr/bin/env python3
"""
Automated Blog Content Pipeline
Handles: Research → Generation → Duplicate Check → Publishing
"""

import os
import yaml
import anthropic
import openai
from datetime import datetime
import sqlite3
import logging

class BlogAutomation:
    def __init__(self, config_path='config.yaml'):
        self.config = self.load_config(config_path)
        self.claude = anthropic.Anthropic(api_key=os.getenv('CLAUDE_API_KEY'))
        openai.api_key = os.getenv('OPENAI_API_KEY')
        self.db = self.init_database()
        self.setup_logging()

    def run(self):
        """Main execution flow"""
        try:
            # Step 1: Research topics
            topics = self.research_topics()
            self.log.info(f"Found {len(topics)} topic candidates")

            # Step 2: Check duplicates
            unique_topics = self.filter_duplicates(topics)
            self.log.info(f"Filtered to {len(unique_topics)} unique topics")

            # Step 3: Generate content
            for topic in unique_topics[:2]:  # Top 2 topics
                post = self.generate_post(topic)

                # Step 4: Quality check
                if self.validate_quality(post) > 80:
                    # Step 5: Publish
                    self.publish_post(post)
                    self.log.info(f"Published: {post['title']}")
                else:
                    self.log.warning(f"Quality too low, saved as draft")
                    self.save_draft(post)

            self.send_report()
        except Exception as e:
            self.log.error(f"Automation failed: {e}")
            self.send_alert(e)

# Core modules (each ~50-100 lines):
# - research_topics() - GSC API integration
# - filter_duplicates() - Embedding similarity
# - generate_post() - 5-pass Claude system
# - validate_quality() - QA scoring
# - publish_post() - WP-CLI or REST API
```

#### Configuration: `config.yaml`
```yaml
# API Configuration
apis:
  claude_model_brief: "claude-3-5-haiku-20241022"
  claude_model_full: "claude-3-5-sonnet-20241022"
  openai_embedding: "text-embedding-3-small"

# Quality Thresholds
quality:
  min_score: 80
  duplicate_threshold: 0.85
  min_length: 4000  # Chinese characters

# Publishing
publishing:
  staging_hold_hours: 24
  max_posts_per_week: 7
  schedule:
    - day: "Tuesday"
      time: "09:00"
    - day: "Thursday"
      time: "09:00"

# WordPress
wordpress:
  url: "https://svicloudtvbox.us"
  method: "wp-cli"  # or "rest-api"
  categories: ["Blog", "Chinese"]

# Brand Voice (examples for AI)
brand_voice:
  tone: "Professional but friendly, emphasize US advantages"
  key_phrases:
    - "內華達倉庫48小時快速配送"
    - "美國本土一年保固"
    - "中英雙語客服"
  product_terms:
    - "小雲電視盒"
    - "SVICLOUD 10P+"
    - "SVICLOUD 10S"

# Email Alerts
notifications:
  email: "admin@svicloudtvbox.us"
  smtp_server: "smtp.gmail.com"
  smtp_port: 587
```

#### Dependencies: `requirements.txt`
```txt
anthropic>=0.18.0
openai>=1.0.0
pyyaml>=6.0
requests>=2.31.0
python-dotenv>=1.0.0
beautifulsoup4>=4.12.0  # For HTML parsing if needed
```

### Key Modules

#### Module 1: Keyword Research
```python
def research_topics(self):
    """Query Google Search Console for opportunities"""
    # Connect to GSC API
    # Filter: Chinese queries, position 11-20, high impressions
    # Score: (volume * 0.5) + (opportunity * 0.3) + (novelty * 0.2)
    # Return: Top 10 topic candidates
```

#### Module 2: Duplicate Detection
```python
def filter_duplicates(self, topics):
    """Check against existing posts using embeddings"""
    # Load existing titles from DB
    # Generate embeddings for new topics (OpenAI API)
    # Calculate cosine similarity
    # Reject if similarity > 0.85
    # Return: Unique topics only
```

#### Module 3: Content Generation (5-Pass System)
```python
def generate_post(self, topic):
    """Multi-pass generation with Claude"""
    # Pass 1: Strategic brief (Haiku)
    brief = self.generate_brief(topic)

    # Pass 2: Outline (Haiku)
    outline = self.generate_outline(brief)

    # Pass 3: Outline validation (Haiku)
    if not self.validate_outline(outline):
        outline = self.regenerate_outline(brief)

    # Pass 4: Full post (Sonnet)
    post = self.generate_full_post(outline)

    # Pass 5: Final QA (Haiku)
    post['quality_score'] = self.score_quality(post)

    return post
```

#### Module 4: Quality Validation
```python
def validate_quality(self, post):
    """Score post on 0-100 scale"""
    scores = {
        'grammar': self.check_grammar(post['content']),      # 0-25
        'seo': self.check_seo(post),                          # 0-25
        'brand': self.check_brand_consistency(post),          # 0-25
        'technical': self.check_technical(post)               # 0-25
    }
    return sum(scores.values())
```

#### Module 5: Publishing
```python
def publish_post(self, post):
    """Publish via WP-CLI or REST API"""
    if self.config['wordpress']['method'] == 'wp-cli':
        # Option A: Use your existing WP-CLI script
        self.publish_via_wpcli(post)
    else:
        # Option B: Direct REST API
        self.publish_via_rest_api(post)

    # Save to GitHub
    self.save_to_github(post)

    # Request indexing
    self.request_gsc_indexing(post['url'])
```

### Deployment Instructions

#### Step 1: Prepare Server
```bash
# SSH into Hostinger
ssh username@147.79.122.118

# Create directory
mkdir -p ~/blog-automation/logs
cd ~/blog-automation

# Install Python dependencies
pip3 install --user -r requirements.txt
```

#### Step 2: Configure Secrets
```bash
# Create .env file
cat > .env << 'EOF'
CLAUDE_API_KEY=sk-ant-xxx
OPENAI_API_KEY=sk-xxx
WP_APP_PASSWORD=xxxx xxxx xxxx xxxx
GSC_CREDENTIALS_PATH=/home/username/gsc-credentials.json
EOF

# Secure the file
chmod 600 .env
```

#### Step 3: Test Script
```bash
# Manual test run (no publishing)
python3 blog_automation.py --test

# Should output:
# [INFO] Found 8 topic candidates
# [INFO] Filtered to 5 unique topics
# [INFO] Generated post: 加州華人如何選購... (Quality: 87)
# [TEST MODE] Would publish to staging, skipping...
```

#### Step 4: Setup Cron Job
```bash
# Edit crontab
crontab -e

# Add line (runs every Monday 9 AM):
0 9 * * 1 /usr/bin/python3 /home/username/blog-automation/blog_automation.py >> /home/username/blog-automation/logs/cron.log 2>&1

# Verify cron job
crontab -l
```

#### Step 5: Enable Monitoring
```bash
# Check logs
tail -f ~/blog-automation/logs/cron.log

# Check execution history
grep "Published:" ~/blog-automation/logs/cron.log

# Check costs
grep "API cost:" ~/blog-automation/logs/cron.log
```

### Error Handling

#### Retry Logic
```python
def call_claude_with_retry(self, prompt, max_retries=3):
    """Exponential backoff retry"""
    for attempt in range(max_retries):
        try:
            response = self.claude.messages.create(...)
            return response
        except anthropic.RateLimitError:
            wait = 2 ** attempt  # 1s, 2s, 4s
            time.sleep(wait)
        except Exception as e:
            self.log.error(f"Claude API error: {e}")
            if attempt == max_retries - 1:
                raise
```

#### Email Alerts
```python
def send_alert(self, error):
    """Email admin on failure"""
    subject = "Blog Automation Failed"
    body = f"""
    Execution failed at {datetime.now()}

    Error: {str(error)}

    Check logs: ~/blog-automation/logs/cron.log
    """
    self.send_email(subject, body)
```

### Monitoring & Maintenance

#### Weekly Summary Report
```
Email sent every Sunday:
────────────────────────────────────
Subject: Blog Automation Weekly Report

Posts Published: 4
Average Quality Score: 87.5
Total Cost: $0.52 ($0.13/post)
Duplicates Rejected: 2
Errors: 0

Top Performing Topics:
1. 加州華人如何選購 (traffic: 245, rank: #8)
2. 銀髮族輕鬆看中文 (traffic: 189, rank: #12)

Next Week Topics:
- 紐約小雲電視盒代理
- 德州華人電視盒推薦
────────────────────────────────────
```

#### Log Files
```bash
# Main log
~/blog-automation/logs/automation.log

# Cron output
~/blog-automation/logs/cron.log

# Quality scores
~/blog-automation/logs/quality_scores.csv

# API costs
~/blog-automation/logs/api_costs.csv
```

#### Monthly Maintenance (30 minutes)
```bash
# 1. Review performance
python3 analyze_logs.py --month 11

# 2. Check costs
grep "Total cost:" logs/cron.log | tail -4

# 3. Update prompts if needed
vim config.yaml

# 4. Refresh embeddings cache
python3 blog_automation.py --rebuild-embeddings

# 5. Backup database
cp posts_history.db posts_history_backup_$(date +%Y%m).db
```

---

## Comparison Matrix

| Feature | n8n Cloud | Python Script |
|---------|-----------|---------------|
| **Monthly Cost** | $24 | $4 |
| **Setup Time** | 8 weeks | 2 weeks |
| **Technical Skill** | Low (visual) | High (Python) |
| **Customization** | Limited | Unlimited |
| **Maintenance** | Minimal | Moderate |
| **Debugging** | Visual GUI | Log files |
| **Portability** | Vendor lock-in | Fully portable |
| **Control** | Limited | Complete |
| **Scalability** | Easy (GUI) | Easy (code) |
| **Updates** | Automatic | Manual |

## Shared Features (Both Options)

✅ **Duplicate Detection**: Semantic embeddings with 0.85 threshold
✅ **Multi-Pass Generation**: 5-pass Claude system with validation
✅ **Quality Assurance**: 100-point scoring system (>80 = publish)
✅ **Safety Mechanisms**: 24-hour staging, rollback, rate limiting
✅ **Monitoring**: Email alerts, execution logs, cost tracking
✅ **Content Research**: Google Search Console integration
✅ **Publishing**: WordPress integration (WP-CLI or REST API)
✅ **Backup**: Markdown files synced to GitHub

## Recommendation

### Choose n8n Cloud ($24/month) if:
- Prefer visual workflows over code
- Want minimal setup and maintenance
- Need GUI for troubleshooting
- Value vendor support

### Choose Python Script ($4/month) if:
- Comfortable with Python and server management
- Want maximum customization and control
- Budget is primary concern
- Prefer self-hosted solutions

## Next Steps

**For n8n Option:**
1. Sign up for n8n Cloud ($20/month trial)
2. Obtain Claude API key
3. Follow implementation roadmap (8 weeks)

**For Python Option:**
1. Approve Python script development
2. I create complete script + documentation
3. You deploy to Hostinger server
4. Enable cron job (2 weeks to autonomous)

**Ready to proceed?** Choose your preferred option and I'll begin implementation.

---

**Document Created**: November 10, 2025
**Status**: Planning Phase - Awaiting User Decision
**Contact**: Continue conversation to select option and begin implementation
