# Z-Subscriptions

A multi-domain admin panel and API platform for managing subscriptions, products, orders, customers, and regional settings.

This is built, so far, with mostly AI "Vibe" coding to see what is now possible with AI assistance in development.

## Scope

- **Admin Panel** (`admin.zsubscriptions.local`) — Inertia.js + Vue 3 interface for managing catalogue data, orders, customers and system configuration.
- **Shop API** (`api.zsubscriptions.local`) — Internal API consumed by the admin panel through a server-side proxy.
- **Partner API** (`partner.zsubscriptions.local`) — Partner-facing API protected by separate API keys.
- **Cache Control** — Redis cache inspection and management for application settings.

## Technology Stack

- **Backend:** Laravel 13 (PHP 8.3), PostgreSQL, Redis
- **Frontend:** Vue 3, Inertia.js, Tailwind CSS, Vite
- **API Auth:** API keys stored in the `api_keys` table, basic-auth over HTTPS

## Major Work Done

- **Region-aware data** — Issues, Offers, Shops, Products and Publications display region codes instead of raw IDs.
- **Settings caching** — Application settings cached in Redis with a configurable `Default Setting Cache Time` and per-setting `cache_seconds` overrides.
- **Cache Control page** — New admin page listing Redis cached keys with TTL/size, clearing individual keys or all keys.
- **API key basic auth** — Replaced session sharing for the Shop and Partner APIs with `api_keys` table basic auth.
- **Admin API proxy** — Admin frontend calls `api.zsubscriptions.local` endpoints through a backend proxy so API keys stay out of the browser bundle.
- **Product Offers** — Renamed from generic Offers, removed unused fields, made `shop_id` and `price` behave as product-pricing values.
- **Dashboard** — Grouped, colour-coded menu dashboard with all admin sections.
- **Security hardening** — Removed hardcoded credentials from tracked source files and migrations.

## Setup

1. Copy `.env.example` to `.env` and fill in the database, Redis and domain values.
2. Generate an application key:
   ```bash
   php artisan key:generate
   ```
3. Run migrations:
   ```bash
   php artisan migrate
   ```
4. Create an admin user by setting `ADMIN_USER_EMAIL` and `ADMIN_USER_PASSWORD` in `.env` then running:
   ```bash
   php artisan db:seed --class=AdminUserSeeder
   ```
5. Create at least one active `is_shop` API key in the `api_keys` table and set the matching `SHOP_API_USER` / `SHOP_API_PASS` in `.env`.
6. Install and build the frontend:
   ```bash
   npm install
   npm run build
   ```

## Important Security Notes

- **No credentials in the browser bundle.** The admin panel uses a server-side proxy for all internal API calls.
- **Do not commit `.env`.** It contains the application key, database password and API client credentials.
