# 🧱 1. `repositories` (connected git repos)

```php
Schema::create('repositories', function (Blueprint $table) {
    $table->uuid('id')->primary();

    $table->uuid('user_id'); // owner in your system
    $table->string('provider'); // github, gitlab, bitbucket
    $table->string('external_id'); // repo ID from provider

    $table->string('name');
    $table->string('full_name')->nullable(); // e.g. org/repo
    $table->string('url')->nullable();

    $table->timestamp('last_synced_at')->nullable();

    $table->timestamps();

    $table->unique(['provider', 'external_id']);
});
```

---

# 📖 2. `commits` (core of Story)

```php
Schema::create('commits', function (Blueprint $table) {
    $table->uuid('id')->primary();

    $table->uuid('repository_id');

    $table->string('sha')->unique();
    $table->text('message');

    $table->string('author_name')->nullable();
    $table->string('author_email')->nullable();

    $table->timestamp('committed_at');

    // Optional useful metadata
    $table->string('branch')->nullable();
    $table->integer('additions')->nullable();
    $table->integer('deletions')->nullable();
    $table->integer('total_changes')->nullable();

    $table->timestamps();

    $table->index(['repository_id', 'committed_at']);
});
```

---

# 🏷️ 3. `labels` (paid feature)

```php
Schema::create('labels', function (Blueprint $table) {
    $table->uuid('id')->primary();

    $table->uuid('user_id');

    $table->string('name');
    $table->string('color')->default('#6366f1'); // Tailwind indigo

    $table->timestamps();

    $table->unique(['user_id', 'name']);
});
```

---

# 🔗 4. `commit_label` (pivot)

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

# 🔐 5. `git_accounts` (OAuth tokens per provider)

You’ll need this to support multi-provider later.

```php
Schema::create('git_accounts', function (Blueprint $table) {
    $table->uuid('id')->primary();

    $table->uuid('user_id');

    $table->string('provider'); // github, gitlab, bitbucket
    $table->string('provider_user_id')->nullable();

    $table->text('access_token');
    $table->text('refresh_token')->nullable();

    $table->timestamp('token_expires_at')->nullable();

    $table->timestamps();

    $table->unique(['user_id', 'provider']);
});
```

---

# 📊 6. (Optional) `commit_metrics_cache` (for performance later)

Not needed for MVP — but useful if you scale.

```php
Schema::create('commit_metrics_cache', function (Blueprint $table) {
    $table->uuid('id')->primary();

    $table->uuid('user_id');
    $table->uuid('repository_id')->nullable();

    $table->json('data'); // aggregated metrics
    $table->timestamp('generated_at');

    $table->timestamps();
});
```

---

# 🧩 Relationships (Eloquent mental model)

## Repository

* hasMany → commits

## Commit

* belongsTo → repository
* belongsToMany → labels

## Label

* belongsTo → user
* belongsToMany → commits

## GitAccount

* belongsTo → user

---

# ⚡ Indexing Strategy (important)

Already included, but key ones:

* commits:

  * `repository_id + committed_at`
* unique:

  * `sha`
  * `provider + external_id`
  * `user_id + name` (labels)

---

# 🧠 Design Decisions (why this works)

### ✅ UUID everywhere

* consistent with your Laravel style
* safe for multi-tenant / API

---

### ✅ No branches table (yet)

* keep it simple
* store branch as string
* normalize later if needed

---

### ✅ Labels are user-scoped

* allows:

  * personal workflows
  * future team-level expansion

---

### ✅ Metrics NOT precomputed (yet)

* compute on demand first
* cache later if needed

---

# 🔥 Optional Upgrade (VERY useful for you later)

## Add this to `commits`:

```php
$table->uuid('user_id')->nullable();
```

Why:

* if you support team repos later
* map commits to your internal users

---

# 🚀 What to build next (in order)

1. `git_accounts`
2. `repositories`
3. `commits`
4. sync job
5. UI timeline
6. labels
7. pivot
8. metrics

---

# 💡 Final Thought

This schema is:

* ✅ simple enough for MVP
* ✅ flexible for multi-provider
* ✅ ready for your **pricing + analytics vision**
