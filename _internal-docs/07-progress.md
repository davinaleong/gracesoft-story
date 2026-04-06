# GraceSoft Story Progress Log

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
