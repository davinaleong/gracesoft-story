# GraceSoft Story Progress Log

## 2026-04-07 - Milestone: Account and Subscription Schema Integration

### Completed in this iteration
- Integrated behind-the-scenes account management schema from sister product into this codebase.
- Added `accounts` table with UUID primary key, owner linkage, and Stripe customer mapping.
- Added `plans` table with UUID primary key and seeded `Free`, `Growth`, and `Pro` rows.
- Added `subscriptions` table with UUID primary key, status, billing period, and account/plan foreign keys.
- Added `stripe_webhook_events` table for event idempotency and processing tracking.
- Added `two_factor_enabled_at` column to `users`.
- Added Eloquent models: `Account`, `Plan`, `Subscription`, and `StripeWebhookEvent`.
- Wired `User` -> `accounts` relationship and cast for `two_factor_enabled_at`.

### Current architecture status
- Existing Git sync/timeline domain remains intact.
- Account, plan, subscription, and Stripe webhook persistence layer is now present and ready for billing/auth flows.
- Plan defaults are seeded in migration to align provisioning logic with pricing tiers.

### Validation snapshot
- Full test run: 31 passed, 104 assertions.

## 2026-04-06 - Milestone: End-to-End Sync Pipeline (OAuth -> Repositories -> Commits)

### Completed in this iteration
- Implemented GitHub OAuth connect and callback flow with state validation.
- Persisted provider token in git_accounts per user/provider.
- Added repository sync job to upsert repositories from provider responses.
- Added commit sync job to upsert commits for each synced repository.
- Wired repository sync to dispatch commit sync jobs automatically.
- Added and passed feature tests for OAuth, provider normalization, repository sync, and commit sync.

### Current architecture status
- Provider abstraction and GitHub provider are active.
- OAuth callback triggers initial repository sync.
- Repository sync now fans out into commit sync jobs.
- Commit records are persisted and updated idempotently via SHA upsert.

### What is ready for next iteration
- Add manual refresh endpoint to trigger resync on demand.
- Build the initial story timeline route and page using stored commits.
- Add basic sync run visibility (timestamps / simple status info).

### Validation snapshot
- Focused test run: 11 passed, 44 assertions.

## 2026-04-06 - Milestone: Manual GitHub Refresh Trigger

### Completed in this iteration
- Added a manual refresh endpoint to queue GitHub repository sync for the current user.
- Added authorization behavior for refresh requests (authenticated only).
- Added feature tests for successful queue dispatch and unauthenticated rejection.

### Current architecture status
- Initial sync runs automatically after OAuth callback.
- Manual sync can now be triggered on demand from the app.
- Repository sync continues to fan out commit sync jobs.

### Validation snapshot
- Focused test run: 13 passed, 49 assertions.

## 2026-04-06 - Milestone: Story Timeline MVP Route and View

### Completed in this iteration
- Added story timeline endpoint at /story/{repo}.
- Implemented StoryController with repository ownership checks.
- Built a timeline Blade view that renders commits in reverse chronological order.
- Added feature tests for owner access, unauthorized access, and unauthenticated access.

### Current architecture status
- End-to-end ingest path is functional: OAuth -> repositories -> commits.
- Users can now view synced commits as a timeline for owned repositories.
- Timeline foundation is ready for labels, filters, and richer chapter detail views.

### Validation snapshot
- Focused test run: 16 passed, 56 assertions.

## 2026-04-06 - Milestone: Labels, Chapter View, and Tagging Workflows

### Completed in this iteration
- Added labels and commit_label database tables.
- Added Label model and commit-label relations.
- Added label CRUD endpoints (create, edit, delete).
- Added commit label assignment endpoints (add, remove, bulk apply).
- Added chapter detail route and view from timeline commit links.
- Updated timeline to display labels per chapter.
- Added feature tests covering label CRUD, chapter detail, and commit labeling workflows.

### Current architecture status
- Timeline now supports chapter drill-down and label display.
- Label system backend is functional with per-user ownership checks.
- Bulk tag application is available for multi-commit workflows.

### Validation snapshot
- Focused test run: 22 passed, 84 assertions.
