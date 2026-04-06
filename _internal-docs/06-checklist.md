# ✅ GraceSoft Story — Git Provider Integration Checklist

---

# 🧭 PHASE 0 — PRODUCT DEFINITION

## Core Positioning

* [ ] Define product one-liner
  → *“Understand where your development effort goes.”* 
* [ ] Define target users

  * freelancers
  * agencies
  * solo devs
* [ ] Define MVP scope

  * timeline view
  * basic commit display
  * optional tagging

---

# 🔌 PHASE 1 — GIT PROVIDER INTEGRATION

## Architecture Setup

* [x] Create `GitProviderInterface`

```php
interface GitProviderInterface {
    public function getRepositories(User $user);
    public function getCommits(string $repoId);
}
```

---

## Provider Implementation (Start with GitHub)

* [x] Create `GitHubService`

  * [x] Fetch repositories
  * [x] Fetch commits
* [x] Normalize API responses → internal format
* [x] Ensure provider-agnostic structure

---

## Future Providers (Do NOT build yet)

* [ ] GitLabService
* [ ] BitbucketService

---

# 🔐 PHASE 2 — AUTH & ACCOUNT LINKING

## OAuth Setup

* [ ] Create GitHub OAuth App
* [ ] Add “Connect GitHub” button
* [x] Handle OAuth callback
* [x] Store access token securely

---

## Git Accounts Table

* [x] Create `git_accounts` table

  * [x] user_id
  * [x] provider
  * [x] access_token
  * [x] refresh_token
  * [x] expiry

---

## Integration Flow

* [x] User connects provider
* [x] Save tokens
* [x] Trigger initial sync

---

# 🗄️ PHASE 3 — DATABASE SETUP

## Core Tables

### Repositories

* [x] Create `repositories`

  * [x] user_id
  * [x] provider
  * [x] external_id
  * [x] name
  * [x] full_name
  * [x] url
  * [x] last_synced_at

---

### Commits

* [x] Create `commits`

  * [x] repository_id
  * [x] sha (unique)
  * [x] message
  * [x] author_name
  * [x] author_email
  * [x] committed_at
  * [x] branch (optional)

---

## Labels (Paid Feature)

### Labels Table

* [ ] Create `labels`

  * [ ] user_id
  * [ ] name
  * [ ] color

---

### Pivot Table

* [ ] Create `commit_label`

  * [ ] commit_id
  * [ ] label_id

---

## Optional (Later)

* [ ] commit_metrics_cache
* [ ] branches table
* [ ] sync_logs

---

# 🔄 PHASE 4 — SYNC SYSTEM

## Jobs

* [x] Create `SyncRepositoriesJob`
* [x] Create `SyncCommitsJob`

---

## Sync Flow

* [x] Fetch repositories from provider
* [x] Store in database
* [x] Fetch commits per repo
* [x] Store commits
* [x] Update `last_synced_at`

---

## Triggers

* [x] On initial connect
* [x] Manual refresh button
* [ ] (Later) scheduled cron

---

# 📖 PHASE 5 — CORE FEATURES (MVP)

## Story Timeline (Main Feature)

* [ ] Create route `/story/{repo}`
* [ ] Display commits chronologically
* [ ] Show:

  * [ ] message (chapter)
  * [ ] author
  * [ ] date
  * [ ] labels

---

## Chapter View (Commit Details)

* [ ] Click commit → open detail view
* [ ] Show:

  * [ ] full message
  * [ ] metadata
  * [ ] labels
  * [ ] (optional) file changes

---

# 🏷️ PHASE 6 — LABEL SYSTEM (PAID)

## CRUD

* [ ] Create label
* [ ] Edit label
* [ ] Delete label

---

## Tagging UX

* [ ] Add label to commit
* [ ] Remove label
* [ ] Support multiple labels

---

## Bulk Actions

* [ ] Select multiple commits
* [ ] Apply label to all

---

## Example Labels

* [ ] Bug Fix
* [ ] Feature
* [ ] Refactor
* [ ] Client A
* [ ] Urgent 

---

# 💰 PHASE 7 — PAYWALL

* [ ] Gate label features
* [ ] Show locked UI state
* [ ] Add upgrade CTA
  → “Unlock insights with labels”

---

# 📊 PHASE 8 — METRICS DASHBOARD (PAID)

## Metrics Logic

* [ ] Count commits per label
* [ ] Calculate % distribution
* [ ] Aggregate by date

---

## Dashboard Page

* [ ] Create `/insights`
* [ ] Add:

  * [ ] Pie chart (label distribution)
  * [ ] Bar chart (activity over time)
  * [ ] Top labels

---

## Insights Engine

* [ ] Most used label (weekly)
* [ ] Trend comparison (week vs week)
* [ ] Detect spikes (e.g. bug fixes)

---

## Weekly Summary (Key Feature)

* [ ] Generate summary text
  → “You focused 52% on Feature work this week” 

---

# 🎨 PHASE 9 — UX & COPY

## UX Principles

* [ ] Avoid Git jargon
* [ ] Keep UI clean and simple
* [ ] Make tagging fast & optional

---

## Copy System

* [ ] commit → chapter
* [ ] branch → path
* [ ] diff → changes

---

## UI Elements

* [ ] Timeline layout
* [ ] Label badges (color-coded)
* [ ] Empty states

Example:

* [ ] “No story yet — connect your repo”

---

# ⚡ PHASE 10 — PERFORMANCE

* [ ] Paginate commits
* [ ] Cache results
* [ ] Lazy load older commits

---

# 🔁 PHASE 11 — EXPANSION

## Additional Providers

* [ ] GitLab integration
* [ ] Bitbucket integration

---

## Smart Features

* [ ] AI auto-label suggestions
* [ ] Smart categorisation

---

## Power Features

* [ ] Filter by label
* [ ] Filter by date
* [ ] Search commits

---

# 🧩 PHASE 12 — ECOSYSTEM INTEGRATION

* [ ] Link labels → projects (your tracker)
* [ ] Map commits → cost tracking
* [ ] Generate client reports

---

# 🚀 PHASE 13 — LAUNCH

## Landing Page

* [ ] Problem: Git is confusing
* [ ] Solution: Visual story
* [ ] CTA: Connect GitHub

---

## Content

* [ ] Demo video
* [ ] Screenshots
* [ ] Onboarding flow

---

# 🔥 PRIORITY EXECUTION PLAN

## Week 1

* [x] GitHub OAuth
* [x] Sync repos + commits
* [ ] Basic timeline UI

---

## Week 2

* [ ] Labels system
* [ ] Tagging UX

---

## Week 3

* [ ] Metrics dashboard
* [ ] Paywall

---

# 🧠 FINAL RULE

* [ ] If it doesn’t help users understand their story faster → **skip it**
