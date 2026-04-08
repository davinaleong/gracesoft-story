# GraceSoft Story Progress Log

## 2026-04-08 - Milestone: Lucide Icon Pass (Navigation + Providers)

### Completed in this iteration
- Added reusable `lucide-icon` Blade component for centralized icon rendering.
- Replaced symbol-based navigation markers in sidebar with Lucide-style icons (`plus`, `book-open`, `lock`, `settings`).
- Added provider iconography to connect cards and provider setup inspector list (`github`, `git-branch`).
- Kept icon integration component-driven to avoid repeated inline SVG blocks and simplify future icon updates.

### Current architecture status
- Sidebar and provider visuals now align with the updated iPhone Notes + Lucide direction.
- Icon usage is now reusable and consistent across pages that use the shared shell/partials.

### Validation snapshot
- IDE diagnostics: no errors in touched Blade files.

## 2026-04-08 - Milestone: iPhone Notes-Inspired UI Refactor (Reusable Shell + Mobile-First)

### Completed in this iteration
- Added reusable Blade shell component (`story-shell`) to centralize page structure, head metadata, favicon, and shared sidebar/inspector slots.
- Extracted sidebar into reusable partial (`story-sidebar`) and wired active nav states (`connect`, `timeline`, `chapter`) across story pages.
- Refactored `welcome`, `story.timeline`, and `story.chapter` views to consume the shared shell and reduce duplicated layout markup.
- Introduced reusable Notes-style list classes (`gs-notes-list`, `gs-note-row`, `gs-note-meta`) and applied them to timeline/chapter list rendering.
- Improved mobile-first behavior by tightening spacing and typography defaults for small screens, while preserving larger-screen three-column shell.
- Updated visual tokens to better match the iPhone Notes-inspired direction (soft neutral surfaces, subtle sky accent atmosphere).

### Current architecture status
- Story UI now has a reusable page composition pattern: shell component + sidebar partial + per-page content/inspector slots.
- Core pages remain functionally equivalent but are now easier to iterate, extend, and keep visually consistent.
- Responsive behavior and list styling are now centrally managed via shared CSS component classes.

### Validation snapshot
- IDE diagnostics: no errors in touched Blade/CSS files.

## 2026-04-07 - Milestone: UI Foundations (Shell, Connect, Timeline, Inspector)

### Completed in this iteration
- Implemented a global UI shell with fixed left sidebar, centered content, and optional right inspector panel.
- Added repository-aware sidebar navigation with active state, empty state, and scrollable repository list.
- Rebuilt the root page as "Connect your repository" with GitHub primary action and disabled GitLab/Bitbucket options.
- Redesigned story timeline page to use chapter-first language and lightweight vertical list styling.
- Redesigned chapter detail page to use a right-side inspector while keeping timeline context in the center.
- Added global UI tokens and reusable layout/interaction classes in `resources/css/app.css`.
- Updated routing/controller data loading so sidebar and connect page have repository/provider connection context.

### Current architecture status
- Story pages now share a consistent pre-glass MacOS-inspired visual system using gray and sky accents.
- Primary storytelling pages (connect, timeline, chapter details) align with the first checklist slice.
- Remaining UI checklist items are now mostly focused on labels UX polish, skeleton/error states, and paid insights/paywall views.

### Validation snapshot
- IDE diagnostics: no errors in touched PHP/Blade/CSS files.

## 2026-04-07 - Milestone: Stripe and Postmark Webhooks (API)

### Completed in this iteration
- Enabled API route loading in app bootstrap and introduced `routes/api.php`.
- Added `POST /api/webhooks/stripe` endpoint with Stripe signature verification.
- Added Stripe event idempotency handling using `stripe_webhook_events`.
- Implemented subscription synchronization for Stripe subscription lifecycle events.
- Added `POST /api/webhooks/postmark` endpoint with token-based verification.
- Added webhook feature tests covering valid/invalid Stripe and Postmark webhook requests.

### Current architecture status
- Billing/inbound integration surface now includes API webhooks for Stripe and Postmark.
- Stripe webhook processing now updates local subscription state for mapped accounts/plans.
- Webhook entry points are validated by automated feature tests.

### Validation snapshot
- Full test run: 35 passed, 112 assertions.

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
