# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## About this project

Snipe-IT is an open-source IT asset management web application built on **Laravel 11**. It tracks hardware assets, software licenses, accessories, consumables, and components with checkout/check-in workflows.

## Common commands

### Local development

```bash
# Install PHP dependencies
composer install

# Install JS dependencies and build assets (development)
npm install && npm run dev

# Watch for asset changes
npm run watch

# Build for production
npm run prod

# Start dev server
php artisan serve
```

### Docker development

```bash
docker-compose -f dev.docker-compose.yml up
```

Services: snipeit (PHP 8.2 + Apache), mariadb (11.4), redis, mailhog (UI at :8025).

### Database

```bash
php artisan migrate
php artisan db:seed
```

### Testing

```bash
# Run all tests
php artisan test

# Run a single test file
php artisan test tests/Feature/Assets/AssetCheckoutTest.php

# Run a specific test method
php artisan test --filter=testCheckoutAsset

# Run only unit tests
php artisan test tests/Unit

# Run only feature tests
php artisan test tests/Feature

# With HTML coverage (requires Herd)
herd coverage vendor/bin/phpunit --coverage-html tests/coverage/html
```

Test setup requires a `.env.testing` file (copy from `.env.testing.example`) and Passport keys:

```bash
php artisan passport:install
```

### Static analysis / linting

```bash
vendor/bin/phpstan analyse
vendor/bin/phpinsights
vendor/bin/phpcs
```

## Architecture overview

### Request flow

Web requests: `routes/web.php` → auth middleware → Controllers → Models → Blade views
API requests: `routes/api.php` (prefix `/api/v1`) → token auth + rate limiting → `app/Http/Controllers/Api/` → Transformers → JSON

### Key layers

**Controllers** (`app/Http/Controllers/`): Split into web controllers (top-level) and API controllers (`Api/` subdirectory). API controllers return data via Transformers; web controllers return Blade views.

**Models** (`app/Models/`): `Asset` is the most complex model (core entity). Related models: `AssetModel`, `Category`, `Manufacturer`, `Supplier`, `Location`, `Company`, `Department`. Checkout targets can be `User`, `Asset`, or `Location`. `ActionLog` records all audit trail events.

**Transformers** (`app/Http/Transformers/`): Shape API responses. Every API-exposed model has a corresponding transformer (e.g., `AssetsTransformer`, `UsersTransformer`).

**Form Requests** (`app/Http/Requests/`): Validation logic separated from controllers.

**Policies** (`app/Policies/`): Authorization rules per model, enforced via `$this->authorize()` calls in controllers.

**Presenters** (`app/Presenters/`): View-layer helpers that add display logic to models (e.g., formatted dates, status badges).

**Notifications** (`app/Notifications/`): Email/Slack/webhook notifications for checkout, checkin, expiration, etc.

### Routes organization

Web routes are split into sub-files under `routes/web/` (hardware, accessories, licenses, etc.) and loaded from `routes/web.php`. All web routes are behind `auth` middleware. API routes are in `routes/api.php` under prefix `v1` with token auth via Laravel Passport.

### Asset lifecycle

Assets go through: create → deploy (checkout to user/asset/location) → checkin → retire/delete. Each transition is logged in `action_logs`. Status is tracked via `Statuslabel` with types: deployable, pending, archived, undeployable.

### Multi-tenancy

Company scoping is optional. When enabled, users only see assets belonging to their company. Controlled via `full_multiple_companies_support` setting and `CompanyableScope` trait on models.

### Custom fields

Assets support custom fields via `CustomField` and `CustomFieldset` models. Fields are stored as JSON-encoded values in the `asset_custom_fields` table and attached to asset models through fieldsets.

### Authentication

Supports: local auth, LDAP (`app/Models/Ldap.php`), SAML2, and token-based API auth (Laravel Passport). Two-factor authentication is built-in.

### Frontend

Uses **AdminLTE 2.4** (Bootstrap 3 + jQuery). Assets are compiled with **Laravel Mix** (webpack). Livewire v3 is used for the importer UI (`app/Livewire/`). Bootstrap Table handles server-side paginated/sorted data tables throughout the app.
