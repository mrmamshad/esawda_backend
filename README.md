# eSawda — Laravel 11 REST API

The **Laravel 11 rewrite** of the legacy raw-PHP Quickad Classified Ads CMS
(Bylancer v10.4). Serves a **REST API only** (`/api/v1/*`) that powers the
Next.js frontend in [`frontend/`](frontend/) (public marketplace) and its
Next.js admin panel at `/admin/*` (talks to `/api/v1/admin/*`).

> **Payment:** SSLCommerz is the sole registered gateway. The other 14
> legacy gateway classes remain under `App\Services\Payment\Gateways\*`
> but are commented out of `PaymentManager`.

---

## Architecture

```
Next.js (frontend/, :3000)  ── HTTP /api/v1/* ──►  Laravel (this repo, :8100)
   public marketplace            Sanctum bearer token         MySQL / sqlite (ad_ prefix)
   shop panel /admin/*
```

- **Auth:** Sanctum personal-access tokens. Login/register/social set an
  **HttpOnly + Secure cookie** (`eshauda_token`) so the Next.js server can
  forward it; the SPA keeps an in-memory copy.
- **Emails:** `EmailQueue::enqueue()` dispatches `SendMailJob` (password
  reset, contact form). `QUEUE_CONNECTION=sync` sends inline in dev;
  `database` defers to a worker in prod.
- **Payments:** `PaymentCallbackController` validates SSLCommerz callbacks
  (val_id + amount + `verify_hash` signature), then dispatches
  `FulfilTransactionJob` for the side-effects (plan bump / ad upgrade).

---

## Quick start

```bash
# Backend (this repo)
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --force        # sqlite by default; see .env for MySQL
php artisan serve --port=8100      # API at http://localhost:8100/api/v1

# Frontend (separate repo in frontend/)
cd frontend
npm install
cp .env.local.example .env.local   # NEXT_PUBLIC_API_URL=http://localhost:8100/api/v1
npm run dev                        # http://localhost:3000
```

To point at the real legacy MySQL database, edit `.env`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=quickad
DB_USERNAME=root
DB_PASSWORD=
DB_PREFIX=ad_
```

Set `APP_DEBUG=false` and `APP_ENV=production` on any non-local deploy.

---

## Key env vars

| Var | Purpose |
|-----|---------|
| `FRONTEND_URLS` | comma-separated allowed origins (CORS + reset-link base) |
| `SSLCOMMERZ_STORE_ID` / `SSLCOMMERZ_STORE_PASSWORD` | gateway creds — **required**, no defaults |
| `SANCTUM_TOKEN_EXPIRATION` | token lifetime minutes (default 1440) |
| `QUEUE_CONNECTION` | `sync` (dev) or `database` (prod, run a worker) |

---

## What lives where

```
app/
├── Models/                      # Eloquent models (User, Post, Category, Plan, ...)
├── Enums/                       # PostStatus, TransactionStatus (backed enums)
├── Http/
│   ├── Controllers/Api/V1/      # REST controllers (thin; delegate to Services)
│   │   └── Admin/               # /api/v1/admin/* panel endpoints
│   ├── Requests/V1/             # FormRequest validation classes
│   └── Resources/V1/            # API Resources (field shaping)
├── Jobs/                        # SendMailJob, FulfilTransactionJob
├── Mail/                        # LegacyMail (plain-text)
└── Services/                    # AdMutationService, AdStatsService, Payment/, ...
routes/api.php                   # /api/v1/* route surface
database/migrations/             # schema + non-destructive index migrations
tests/                           # PHPUnit (Feature API smoke, Unit)
```

---

## Testing

```bash
./vendor/bin/phpunit
```

Covers auth flows, read-only ad/taxonomy endpoints, route smoke checks,
and payment-manager registry resolution. (Write-path, checkout, and admin
endpoints lack coverage as of the quality review — see issue queue.)

---

## License note

Quickad is a commercial CodeCanyon product by Bylancer. This Laravel port is
for **single-site personal use only**; redistribution is not permitted
without Bylancer's written consent.
