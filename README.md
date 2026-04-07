# GraceSoft Story

GraceSoft Story is a Laravel application that turns repository activity into a structured timeline with labeling, chapter views, sync notifications, and account/billing foundations.

## Current Capabilities

- GitHub OAuth connect and disconnect flow
- Repository and commit synchronization pipeline via queued jobs
- Story timeline and chapter detail pages
- Label CRUD and commit label assignment (including bulk apply)
- In-app notification center and event notifications
- Account, plan, and subscription persistence layer
- Stripe webhook endpoint for subscription state syncing
- Postmark webhook endpoint for inbound event handling

## Stack

- Laravel 12
- PHP 8.2+
- Pest for testing
- MySQL (local dev via Laragon)

## Quick Start

1. Install PHP dependencies.

```bash
composer install
```

1. Install frontend dependencies.

```bash
npm install
```

1. Copy environment file and generate app key.

```bash
cp .env.example .env
php artisan key:generate
```

1. Configure database and service credentials in .env.

1. Run migrations.

```bash
php artisan migrate
```

1. Start local services.

```bash
php artisan serve
npm run dev
```

## Important Environment Variables

GitHub OAuth:

- GITHUB_CLIENT_ID
- GITHUB_CLIENT_SECRET
- GITHUB_REDIRECT_URI
- GITHUB_TOKEN (optional)

Stripe and Postmark webhooks:

- STRIPE_WEBHOOK_SECRET
- POSTMARK_WEBHOOK_TOKEN

Pricing seed helpers:

- STRIPE_GROWTH_PRICE_ID
- STRIPE_GROWTH_PRODUCT_ID
- STRIPE_PRO_PRICE_ID
- STRIPE_PRO_PRODUCT_ID

## API Webhook Endpoints

- POST /api/webhooks/stripe
	- Validates Stripe-Signature against STRIPE_WEBHOOK_SECRET
	- Stores event idempotency records in stripe_webhook_events
	- Syncs local subscriptions for customer.subscription.created, updated, deleted

- POST /api/webhooks/postmark
	- Validates X-Postmark-Server-Token against POSTMARK_WEBHOOK_TOKEN
	- Accepts and logs inbound Postmark webhook payload metadata

## Run Tests

```bash
php artisan test
```

## Project Notes

- Progress log: _internal-docs/07-progress.md
- Build checklist: _internal-docs/06-checklist.md
- Pricing and plan strategy: _internal-docs/08-pricing-plans.md

## License

This project is open-sourced under the MIT license.
