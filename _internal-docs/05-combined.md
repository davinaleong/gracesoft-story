# 🚀 GraceSoft Story — Git Provider Integration Guide

> **“Understand where your development effort really goes.”** 

---

# 🧠 Product Overview

GraceSoft Story transforms Git data into a **human-readable development story**:

* commits → **chapters**
* branches → **paths**
* merges → **joined paths**
* history → **timeline**
* diffs → **changes** 

👉 The goal is NOT a Git viewer
👉 It’s a **developer analytics + storytelling platform**

---

# 🎯 Core Value

* Visualise development effort
* Replace manual time tracking (approximation via commits)
* Provide insights for:

  * freelancers
  * agencies
  * solo developers

⚠️ Important positioning:

> “Effort insights based on commits” (NOT exact time tracking) 

---

# 🔌 Multi-Git Provider Integration

## Supported Providers

* GitHub (MVP)
* GitLab (future)
* Bitbucket (future)

---

## 🧩 Architecture Design

### 1. Provider Interface

```php
interface GitProviderInterface {
    public function getRepositories(User $user);
    public function getCommits(string $repoId);
}
```

---

### 2. Provider Implementations

#### Example:

```php
class GitHubService implements GitProviderInterface {
    public function getRepositories(User $user) {
        // call GitHub API
    }

    public function getCommits(string $repoId) {
        // fetch commits
    }
}
```

👉 Later:

* `GitLabService`
* `BitbucketService`

---

### 3. Normalisation Layer

All providers → unified internal format:

```json
{
  "id": "...",
  "name": "...",
  "full_name": "...",
  "commits": [...]
}
```

👉 This ensures your app logic stays **provider-agnostic**

---

# 🔐 OAuth & Account Linking

## Git Accounts Table

Stores tokens per provider:

```php
Schema::create('git_accounts', function (Blueprint $table) {
    $table->uuid('id')->primary();

    $table->uuid('user_id');
    $table->string('provider');

    $table->text('access_token');
    $table->text('refresh_token')->nullable();

    $table->timestamp('token_expires_at')->nullable();

    $table->timestamps();

    $table->unique(['user_id', 'provider']);
});
```



---

## Flow

1. User clicks **“Connect GitHub”**
2. OAuth redirect
3. Store access token
4. Trigger initial sync

---

# 🗄️ Database Schema

## 📦 Repositories

```php
Schema::create('repositories', function (Blueprint $table) {
    $table->uuid('id')->primary();

    $table->uuid('user_id');
    $table->string('provider');
    $table->string('external_id');

    $table->string('name');
    $table->string('full_name')->nullable();
    $table->string('url')->nullable();

    $table->timestamp('last_synced_at')->nullable();

    $table->timestamps();

    $table->unique(['provider', 'external_id']);
});
```



---

## 📖 Commits (Core)

```php
Schema::create('commits', function (Blueprint $table) {
    $table->uuid('id')->primary();

    $table->uuid('repository_id');

    $table->string('sha')->unique();
    $table->text('message');

    $table->string('author_name')->nullable();
    $table->string('author_email')->nullable();

    $table->timestamp('committed_at');

    $table->string('branch')->nullable();

    $table->timestamps();
});
```



---

## 🏷️ Labels (Paid Feature)

```php
Schema::create('labels', function (Blueprint $table) {
    $table->uuid('id')->primary();

    $table->uuid('user_id');

    $table->string('name');
    $table->string('color')->default('#6366f1');

    $table->timestamps();

    $table->unique(['user_id', 'name']);
});
```



---

## 🔗 Commit ↔ Label Pivot

```php
Schema::create('commit_label', function (Blueprint $table) {
    $table->uuid('id')->primary();

    $table->uuid('commit_id');
    $table->uuid('label_id');

    $table->timestamps();

    $table->unique(['commit_id', 'label_id']);
});
```



---

# 🔄 Sync Architecture

## Jobs

* `SyncRepositoriesJob`
* `SyncCommitsJob`

---

## Flow

1. Fetch repos from provider
2. Store in `repositories`
3. Fetch commits per repo
4. Store in `commits`
5. Update `last_synced_at`

---

## Trigger Points

* On connect
* Manual refresh
* (Later) scheduled cron

---

# 📖 Core Features

## 1. Story Timeline

Route:

```
/story/{repo}
```

Displays:

* commit message
* author
* date
* labels

---

## 2. Chapter View (Commit Details)

* full message
* metadata
* labels
* (optional) file changes

---

## 3. Labels System

### UX

* Add/remove labels per commit
* Multi-label support
* Bulk tagging

---

## Example Labels

* Bug Fix
* Feature
* Refactor
* Client A
* Urgent 

---

# 📊 Metrics Dashboard (Paid)

## Core Metrics

* commit distribution by label
* trends over time
* client breakdown

---

## Example Insights

* “40% Feature work”
* “Spike in bug fixes this week”
* “Client A took 60% effort” 

---

## Weekly Summary (Killer Feature)

> “This week, you focused mainly on Feature work (52%)…” 

---

# 💰 Monetisation

## Free Tier

* Story timeline
* Basic history browsing

---

## Paid Tier

* Labels
* Metrics dashboard
* Insights
* Filtering

---

## Future Tier

* Client reports
* Time tracking integration
* Export to invoices 

---

# 🎨 UX Principles

* No Git jargon
* Human-friendly language
* Fast, optional tagging

---

## Copy System

| Git Term | UX Term |
| -------- | ------- |
| Commit   | Chapter |
| Branch   | Path    |
| Diff     | Changes |

---

# 🧱 Build Phases

## Phase 0 — Definition

* product positioning
* MVP scope

---

## Phase 1 — Core Architecture

* provider interface
* GitHub integration
* database setup

---

## Phase 2 — Auth & Sync

* OAuth
* sync jobs

---

## Phase 3 — MVP Features

* timeline
* commit view
* labels

---

## Phase 4 — Paywall

* gate labels
* upgrade prompts

---

## Phase 5 — Metrics

* dashboard
* insights

---

## Phase 6 — UX Polish

* clean UI
* friendly copy

---

## Phase 7 — Performance

* pagination
* caching

---

## Phase 8 — Expansion

* GitLab
* Bitbucket
* AI tagging

---

## Phase 9 — Ecosystem Integration

* link to project tracker
* cost tracking
* reports

---

## Phase 10 — Launch

* landing page
* demo video
* onboarding 

---

# 🔥 Recommended Build Order

### Week 1

* GitHub integration
* commit sync
* timeline UI

### Week 2

* labels
* tagging UX

### Week 3

* metrics
* paywall

👉 **Launch early. Don’t overbuild.** 

---

# 🧠 Final Architecture Insight

You are building:

> **A Git-powered developer analytics platform**

That can evolve into:

* team insights
* financial tracking
* full GraceSoft ecosystem integration 
