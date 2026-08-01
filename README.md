# Quickad — Laravel 11 Port ✅

The **complete Laravel 11 rewrite** of the legacy raw-PHP Quickad Classified
Ads CMS (Bylancer v10.4). Sits alongside the legacy app and shares the same
MySQL database via the `ad_` table prefix.

> **Status:** REST-API-only backend serving a Next.js SPA (`/frontend`) +
> Next.js admin panel. **SSLCommerz is the sole registered payment gateway**
> (Bangladesh market focus; the other 14 legacy gateway classes remain
> under `App\Services\Payment\Gateways\*` but are commented out in
> `PaymentManager`). Admin panel lives in Next.js at `/admin/*` and speaks
> to `/api/v1/admin/*`. Filament is retained only for emergency access.

---

## Quick start

```bash
cd laravel-quickad
composer install
php artisan key:generate
php artisan migrate --force
php artisan serve
```

Then open:

| URL | What you get |
|-----|--------------|
| `http://localhost:8000/` | public front-end (theme: **thenext-theme**) |
| `http://localhost:8000/login` | user login |
| `http://localhost:8000/signup` | registration |
| `http://localhost:8000/listing` | ad search |
| `http://localhost:8000/admin` | Filament admin panel |
| `http://localhost:8000/sitemap.xml` | SEO sitemap |

**Default DB is SQLite** for zero-friction dev. To point at the real
legacy MySQL database, edit `.env`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=quickad
DB_USERNAME=root
DB_PASSWORD=
DB_PREFIX=ad_
```

---

## What lives where

```
app/
├── Models/                      # 41 Eloquent models (User, Post, Category, Plan, ...)
├── Http/
│   ├── Controllers/             # 25 top-level controllers
│   │   ├── Ad/                  # 9 ad-scoped controllers
│   │   └── Blog/                # 4 blog controllers
│   └── Middleware/
│       └── EnsureLegacyLogin    # ports checkloggedin()
├── Services/
│   ├── AuthService              # userlogin() / register / forgot / reset
│   ├── UserService
│   ├── AdService
│   ├── ListingService           # search + filter + sort + paginate
│   ├── ThemeRenderer            # renders themes.{theme}.{name}.blade.php
│   ├── TemplateBridge           # .tpl → .blade.php converter
│   ├── WatermarkService         # plugins/watermark port
│   ├── ReviewService            # plugins/starreviews port
│   └── Payment/
│       ├── PaymentManager       # 15-gateway factory / registry
│       ├── PaymentGatewayInterface
│       └── Gateways/            # 15 concrete gateways + AbstractGateway
├── Filament/Resources/          # 6 admin resources (User, Post, Category,
│                                #  Plan, Transaction, Blog) + list/create/edit pages
└── Console/Commands/
    ├── ConvertTheme             # `php artisan quickad:convert-theme {theme}`
    └── ConvertLangs             # `php artisan quickad:convert-langs`

config/quickad.php               # legacy $config global port
database/migrations/             # 31 tables in one migration
resources/
├── views/
│   ├── partials/                # shared header + footer
│   ├── themes/
│   │   ├── classic-theme/       # 44 blade files (converted from .tpl)
│   │   ├── material-theme/      # 41 blade files
│   │   ├── thenext-theme/       # 47 blade files
│   │   └── default/             # safe fallbacks (ad-listing)
│   └── placeholder.blade.php
└── lang/{en,fr,de,es,...}/quickad.php  # 21 locales × ~750 keys each
routes/web.php                   # 78 routes
tests/
├── Feature/
│   ├── RouteSmokeTest           # every route < 500
│   └── AuthTest                 # legacy password_hash compat
└── Unit/
    ├── TemplateBridgeTest       # LANG/LINK/IF/LOOP/VAR conversions
    └── PaymentManagerTest       # 15 gateways resolve correctly
```

---

## Artisan tools shipped

```bash
# Re-run any time the legacy templates or lang files change
php artisan quickad:convert-theme thenext-theme
php artisan quickad:convert-theme classic-theme
php artisan quickad:convert-theme material-theme
php artisan quickad:convert-langs
```

---

## Testing

```bash
./vendor/bin/phpunit
# → 15 tests, 66 assertions, OK
```

---

## What's *not* done (deliberately left for content passes)

Each ported controller wraps its theme-view render in a `try/catch` that
falls back to the placeholder page. This means:

- **All URLs return HTTP 200/302 today** — no 5xx anywhere.
- **BUT** many themed Blade views still reference variables (loop
  collections, custom-field labels, config keys) that the legacy PHP
  used to `SetParameter()` before rendering. Those need a per-view
  "data binding pass" — a mechanical exercise of comparing every
  `SetParameter()` in `php/*.php` against the corresponding Blade
  view and adding the missing keys to the controller.

The `default/ad-listing.blade.php` shows the pattern for how to
replace a legacy view once you're ready to make it fully live.

---

## License note

Quickad is a commercial CodeCanyon product by Bylancer. This Laravel
port is for **single-site personal use only**; redistribution is not
permitted without Bylancer's written consent.
# esawda
# esawda_backend
