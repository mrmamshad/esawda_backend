# eSawda — Project Overview

> Full technical reference for the eSawda classified-marketing platform.
> Repo root: `/home/mamshad/Downloads/laravel-quickad` (Laravel backend) + `frontend/` (Next.js frontend).
> Last audited: 2026-08-15.

---

## 1. Project Overview

**eSawda** is a **classified ads marketplace** (Bangladesh market) — buyers browse ads and sellers post products, run shops, buy membership tiers, boost ads, and chat with buyers. It is a **"strangler-fig" rewrite** of the legacy **Quickad v10.4** PHP CMS (Bylancer) into modern Laravel + Next.js, while preserving the legacy MySQL table names (`user`, `product`, `catagory_main`, …).

Two separate repos, both deployed from `main`:

| Part | Stack | Repo / path | Production domain |
|---|---|---|---|
| **Backend** | Laravel 11 / PHP 8.2, Sanctum, Reverb, Filament | `laravel-quickad/` | `api.esawda.com` |
| **Frontend** | Next.js 15 (App Router) / React 19 / Tailwind | `laravel-quickad/frontend/` | `demo.esawda.com` |

### Core features (confirmed in code)

- **Listing (ads)** — create/update/delete ads, image gallery, custom fields, conditions (`new`/`used`), statuses (`draft → pending → active → sold_out/removed/rejected/expire`), featured/urgent/highlight boosts, soft-delete, moderation flow.
- **Taxonomy** — main categories (`catagory_main`) + subcategories (`catagory_sub`), multi-language translations.
- **User auth** — Sanctum bearer tokens (24h), register/login/logout-all, forgot/reset password (single-use 60-min token), brute-force lockout (5 tries / 2h), **Google & Facebook OAuth** (no Socialite — direct HTTP to provider APIs).
- **Seller / Shop** — shop profile, `/store/{username}`, seller stats, reviews & ratings, wishlist (`favads`).
- **Chat** — private message threads; `MessageSent` broadcast event wired to **Laravel Reverb**; frontend currently **polls** every 8s (Echo is wired but dormant).
- **Membership & Payments** — plans (monthly/annual/lifetime), **SSLCommerz** checkout, pay-before-post (creates ad from `transaction.meta` after payment), boost purchases, wallet/balance.
- **CMS / Content** — blogs + categories, static pages, FAQs, testimonials, contact form.
- **Ad slots** — 20 banner placements across public pages (see §2.7), currently showing client-provided static banner images.
- **Admin** — two admin UIs: a **Next.js JSON admin panel** (`/admin/*` against the V1 admin API) **and** a **Filament 3 panel** (Laravel, `/admin`).

---

## 2. Frontend (Next.js) — Page by Page

App Router under `frontend/src/app/`. All data fetching is server components + ISR, or `force-dynamic` protected pages.

### 2.1 Public pages

| Route | File | Purpose |
|---|---|---|
| `/` | `app/page.tsx` | Homepage. Hero + condition toggle, categories, sponsored, latest, new/used grids, plans, testimonials, blog preview, 3 ad slots, final CTA. ISR 120s. |
| `/ads` | `app/ads/page.tsx` | Browse/search. Category sidebar + price filter + condition chips + search, 3 ad slots. ISR 60s. |
| `/ads/[idSlug]` | `app/ads/[idSlug]/page.tsx` | Ad detail. `generateMetadata` + JSON-LD Product/Offer (BDT), gallery, reviews, seller card, related, 3 ad slots. ISR 60s. |
| `/category/[cat]` | `app/category/[cat]/page.tsx` | Category listing via shared `ListingGrid` (3 ad slots). ISR 120s. |
| `/category/[cat]/[subcat]` | `app/category/[cat]/[subcat]/page.tsx` | Sub-category listing via `ListingGrid` (no slots). ISR 120s. |
| `/city/[city]` | `app/city/[city]/page.tsx` | City-filtered ads via `ListingGrid` (3 ad slots). ISR 120s. |
| `/keywords/[q]` | `app/keywords/[q]/page.tsx` | Search-keyword results via `ListingGrid` (no slots). ISR 60s. |
| `/blog` | `app/blog/page.tsx` | Blog index, featured post, category chips, 2 ad slots. ISR 300s. |
| `/blog/[idSlug]` | `app/blog/[idSlug]/page.tsx` | Blog article (sanitized HTML), share row, author, related, 2 ad slots. ISR 300s. |
| `/blog/author/[username]` | `app/blog/author/[username]/page.tsx` | Posts by author. |
| `/blog/category/[slug]` | `app/blog/category/[slug]/page.tsx` | Posts by blog category. |
| `/about` | `app/about/page.tsx` | Static marketing page. |
| `/contact` | `app/contact/page.tsx` | Contact form (`ContactForm.tsx` client) + info. |
| `/faq` | `app/faq/page.tsx` | Static FAQ accordion. |
| `/testimonials` | `app/testimonials/page.tsx` | Testimonial grid. |
| `/terms` | `app/terms/page.tsx` | Static ToS. |
| `/privacy` | `app/privacy/page.tsx` | Static privacy policy. |
| `/safety-tips` | `app/safety-tips/page.tsx` | Static safety tips. |
| `/pages/[slug]` | `app/pages/[slug]/page.tsx` | Dynamic CMS page (sanitized HTML), 404 if missing. |
| `/store/[username]` | `app/store/[username]/page.tsx` | Public seller/shop profile: stats, listings, reviews. |
| `/membership` | `app/membership/page.tsx` | Plan comparison. |
| `/membership/checkout/[planId]` | `app/membership/checkout/[planId]/page.tsx` | Checkout (`force-dynamic`, `requireUser`), cadence toggle, `CheckoutForm`. |
| `/membership/success` · `/membership/failed` | `app/membership/{success,failed}/page.tsx` | SSLCommerz return landings. |
| `/payment/success` | `app/payment/success/page.tsx` | Client poller — verifies tx every 2s (15 tries), redirects by `purpose`. |

### 2.2 Auth pages

| Route | File | Purpose |
|---|---|---|
| `/auth/login` | `app/auth/login/page.tsx` | Buyer login (`RoleLoginForm` in `Suspense`). |
| `/auth/signup` | `app/auth/signup/page.tsx` | Client registration. |
| `/auth/forgot` | `app/auth/forgot/page.tsx` | Request reset link. |
| `/auth/reset` | `app/auth/reset/page.tsx` | Reset form (`?token=`). |
| `/auth/logout` | `app/auth/logout/page.tsx` | POST logout + clear token. |
| `/auth/oauth-callback` | `app/auth/oauth-callback/page.tsx` | OAuth popup exchange + `postMessage` back to opener. |

### 2.3 Dashboard & messages

| Route | File | Purpose |
|---|---|---|
| `/dashboard` | `app/dashboard/page.tsx` | Buyer dashboard (`force-dynamic`, `requireUser`); redirects to `/shop` if `is_shop`. |
| `/messages` | `app/messages/page.tsx` | Inbox (master-detail, `requireUser`). |
| `/messages/[userId]` | `app/messages/[userId]/page.tsx` | Conversation thread with `ChatClient` (8s polling). |

### 2.4 Seller / Shop panel (`/shop/(panel)` — `force-dynamic`, `requireUser`)

| Route | File | Purpose |
|---|---|---|
| `/shop/login` | `app/shop/login/page.tsx` | Seller login. |
| `/shop` | `app/shop/(panel)/page.tsx` | Shop dashboard: KPIs, sales chart, ad-status grid. |
| `/shop/ads` | `.../ads/page.tsx` | All ads (`AdsListView`). |
| `/shop/ads/active` · `/pending` · `/drafts` · `/removed` · `/sold-out` | `.../ads/{state}/page.tsx` | Status-filtered ad lists. |
| `/shop/ads/new` | `app/shop/ads/new/page.tsx` | **Public** post-ad composer (`AdForm`; guests get temp account) — outside `(panel)`. |
| `/shop/ads/[id]/boost` | `.../ads/[id]/boost/page.tsx` | Boost purchase (`POST /checkout/ad-upgrade`). |
| `/shop/ads/[id]/edit` | `.../ads/[id]/edit/page.tsx` | Edit ad (re-enters moderation). |
| `/shop/favourites` | `.../favourites/page.tsx` | Saved ads. |
| `/shop/wishlisted` | `.../wishlisted/page.tsx` | Buyers who saved my ads. |
| `/shop/messages` | `.../messages/page.tsx` | Redirect to `/messages`. |
| `/shop/plan` | `.../plan/page.tsx` | Current tier + plan cards. |
| `/shop/profile` | `.../profile/page.tsx` | Edit public shop profile. |
| `/shop/settings` | `.../settings/page.tsx` | Account settings. |
| `/shop/settings/password` | `.../settings/password/page.tsx` | Change password. |
| `/shop/transactions` | `.../transactions/page.tsx` | Invoice/payment history. |

### 2.5 Admin panel (`/admin/(panel)` — `force-dynamic`, `requireAdmin`)

| Route | File | Purpose |
|---|---|---|
| `/admin/login` | `app/admin/login/page.tsx` | Admin login (role gate; links to Filament). |
| `/admin` | `app/admin/(panel)/page.tsx` | Dashboard: KPIs, revenue chart, category donut, activity. |
| `/admin/ads` | `.../admin/ads/page.tsx` | Ad moderation (`AdsTableClient`). |
| `/admin/ads/new` | `.../admin/ads/new/page.tsx` | Redirects to `/shop/ads/new`. |
| `/admin/blog` | `.../admin/blog/page.tsx` | Blog management. |
| `/admin/blog/new` | `.../admin/blog/new/page.tsx` | Create blog (client form, tiptap editor). |
| `/admin/categories` | `.../admin/categories/page.tsx` | Category CRUD. |
| `/admin/plans` | `.../admin/plans/page.tsx` | Plan CRUD. |
| `/admin/settings` | `.../admin/settings/page.tsx` | Key/value site settings. |
| `/admin/transactions` | `.../admin/transactions/page.tsx` | Transaction list/actions. |
| `/admin/users` | `.../admin/users/page.tsx` | User management. |

### 2.6 Dynamic routes (summary)

`[idSlug]` (ads, blog) · `[id]` (shop ad edit/boost) · `[cat]` + `[subcat]` · `[city]` · `[q]` · `[slug]` (blog category, CMS pages) · `[username]` (blog author, store) · `[userId]` (messages) · `[planId]` (checkout).
No `generateStaticParams` — all dynamic pages rely on ISR `revalidate`; protected pages are `force-dynamic`.

### 2.7 Ad slot placements

| Page | Placements (size) |
|---|---|
| Home | `home.after_categories` (large 970×250), `home.sponsored_infeed` (infeed), `home.after_preowned` (wide 970×90), `home.pre_cta` (wide) |
| Browse `/ads` | `search.filter_under` (large), `search.mid_infeed` (infeed), `search.results_bottom` (wide) |
| Ad detail | `ad.{id}.post_description` (wide), `ad.{id}.sidebar_mpu` (mpu 300×250), `ad.{id}.sidebar_bottom` (mpu) |
| Category / City (`ListingGrid`) | `{cat|city}.{slug}.filter_under|header_under`, `.mid_infeed`, `.pre_pagination|footer_above` |
| Blog index | `blog.header_under` (wide), `blog.post_mid` (infeed) |
| Blog post | `blog.{id}.content_inline` (wide), `blog.{id}.related_before` (large) |

Component: `src/components/ads/AdSlot.tsx` (sizes: `leaderboard 728×90`, `large 970×250`, `mpu 300×250`, `infeed` native, `wide 970×90`). Currently renders static banner images from `/public/ad-{size}.jpg`.

### 2.8 Layout, shared components & state

- **Root layout** `app/layout.tsx`: Inter font → `ThemeProvider` (next-themes) → `<SmoothScroll>` (Lenis) → **`<AuthGate>`** (client context) → `<Toaster>` (sonner).
- **Auth design decision:** auth is *not* server-preloaded on public pages (keeps them static/ISR). `AuthGate` resolves the session lazily client-side via `/auth/me`.
- **Shared components** (`src/components/`):
  - `layout/` — Header, Footer, Logo, MobileDrawer, UserMenu, NavPill, HeroBanner, PageSurface.
  - `ui/` — Button, Badge, Avatar, Breadcrumb, EmptyState, IconButton, Modal, Pagination, PriceTag, RatingStars, Skeleton, SocialRow, Tabs, TestimonialCard, Toast.
  - `home/` — HomeHero, HeroSearchBar, HomeSections, CategoryCard, CategoryConditionGrid, SectionHeader, LocationPicker.
  - `listing/` — ListingCard (4 variants), ListingGrid (shared shell + optional ad slots), AdGallery.
  - `shop/v2/` — ShopShellV2, ShopSidebar, ShopTopbar, StatCard, SalesPanel (recharts), LocationMap (leaflet), RichTextEditor (tiptap).
  - `admin/v2/` — AdminShellV2, AdminSidebar, AdminTopbar, DataTableV2 (TanStack Table), RevenueChart, CategoryDonut, ActivityFeed, etc.
  - `auth/`, `blog/`, `chat/`, `filter/`, `forms/`, `interactive/`, `membership/`, `seller/`, `ads/`.
- **State management:** only **React Context** (`AuthGate`). No Redux/Zustand. `@tanstack/react-query` is installed but **unused**. TanStack Table used only as headless table helper in admin. `next-themes` for admin dark mode.
- **Auth resolution (3 layers):** server `getSessionUser()` via `apiFromServer('/auth/me')`; client `AuthGate` via `api('/auth/me')`; token in **HttpOnly cookie `eshauda_token`** + in-memory mirror (`lib/auth.ts`).

### 2.9 API call pattern

- **No axios.** `src/lib/api.ts` is a thin native-`fetch` wrapper: injects `Accept`/JSON `Content-Type`/`Authorization: Bearer`, normalizes `{data, meta, links}` envelope, throws `ApiError{status,code,fields}`, supports `cache`, `revalidate`, `tags` (Next ISR).
- `apiFromServer()` — server-only; reads `eshauda_token` cookie via `next/headers`.
- **Public SEO pages** → `api()` with `revalidate`/`tags` (ISR). **Protected pages** → `apiFromServer()` + `cache:'no-store'` + `force-dynamic`.
- Base URL: `NEXT_PUBLIC_API_URL` (default `http://127.0.0.1:8100/api/v1`) via `lib/env.ts`.
- Other libs: `session.ts` (guards), `settings.ts`, `queryString.ts`, `format.ts` (BDT money), `sanitize.ts` (DOMPurify), `cn.ts`, `echo.ts`.

---

## 3. Backend (Laravel) — Module by Module

API root: `/api/v1`. Uniform response envelope `{ data, meta, links? }`. Errors: `{ error: { code, message, fields? } }` from the global handler in `bootstrap/app.php`.

### 3.1 API endpoints (routes/api.php)

**Public — auth**
- `POST auth/login` · `forgot` · `register` · `reset` (throttled)
- `POST auth/social/{google|facebook}/callback`

**Public — taxonomy & meta**
- `GET categories` · `categories/{slug}` · `subcategories` · `countries` · `countries/{code}/cities`
- `GET currencies` · `languages` · `settings` · `filter-schema`

**Public — ads & sellers**
- `GET ads` (filterable) · `ads/featured` · `ads/search-suggest` · `ads/{idSlug}` · `ads/{id}/similar`
- `GET sellers/{username}` · `sellers/{username}/ads` · `sellers/{username}/reviews`
- `GET ads/{id}/reviews`

**Public — content**
- `GET pages` · `pages/{slug}` · `faqs` · `testimonials` · `plans` · `blogs` · `blog-categories` · `blogs/{idSlug}`
- `POST contact` (throttled)

**Public — payment callbacks (SSLCommerz)**
- `POST payments/sslcommerz/{success|fail|cancel|ipn}`

**Authenticated (`auth:sanctum`)**
- Auth: `GET auth/me` · `POST auth/logout` · `logout-all`
- Account: `PUT me` · `POST me/avatar` · `me/password` · `GET me/transactions`
- Ad owner: `POST ads` · `PUT ads/{id}` · `DELETE ads/{id}` · `POST ads/{id}/images` · `DELETE ads/{id}/images/{file}` · `POST ads/{id}/{hide|unhide|resubmit|sold-out|restock|remove|publish}` · `GET me/ads` · `me/shop/stats` · `me/wishlisted`
- Favourites: `GET me/favourites` · `POST/DELETE ads/{id}/favourite`
- Reviews: `POST ads/{id}/reviews` · `DELETE reviews/{id}`
- Chat: `GET me/threads` · `me/threads/unread-count` · `me/threads/{userId}` · `POST me/threads/{userId}/read` · `POST messages`
- Checkout: `POST checkout/plan/{planId}` · `checkout/ad-upgrade/{postId}` · `checkout/ad-post` · `GET checkout/transactions/{id}`

**Admin (`auth:sanctum` + `admin`)**
- `GET admin/dashboard`
- Users: `GET admin/users` · `{id}` · `PATCH {id}` · `POST {id}/ban` · `unban` · `DELETE {id}`
- Ads: `GET admin/ads` · `{id}` · `POST {id}/approve` · `reject` · `feature` · `unfeature` · `DELETE {id}`
- `apiResource`: `admin/categories` · `admin/plans` · `admin/blogs`
- Transactions: `GET admin/transactions` · `{id}` · `POST {id}/refund` · `mark-paid`
- Settings: `GET/PUT admin/settings`

### 3.2 Controllers (`app/Http/Controllers/Api/V1`)

| Controller | Responsibilities |
|---|---|
| `AdController` | Public ad read surface: listing (filter grammar via `Filterable`), detail (view count), featured, similar, search-suggest. Heavy caching. |
| `AdMineController` | Owner CRUD: create/update/delete, image management, lifecycle actions, `mine`, `shopStats`, `wishlisted`. **Enforces pay-before-post gate (free posting always → 402)**. Uses `AdMutationService`. |
| `AuthController` | Sanctum register/login/me/logout/logout-all, forgot (64-char token + 60-min expiry), reset (single-use). |
| `CategoryController` / `SubCategoryController` | Public taxonomy with optional ad counts; cached. |
| `CheckoutController` | Checkout: plan, ad-upgrade (static prices featured 200/urgent 150/highlight 100), ad-post (stores ad payload in `tx.meta`), status poll. → `PaymentManager`. |
| `ContentController` | Pages, FAQs, testimonials, plans, blogs, blog categories, single blog, contact form. |
| `AccountController` | Profile update, password change (revokes tokens), avatar upload, transaction ledger. |
| `MessageController` | Threads (canonical min/max user pair), thread, send (broadcasts `MessageSent`), mark-read, unread count. |
| `FavouriteController` | Wishlist index/add (`firstOrCreate`)/remove. |
| `ReviewController` | List (publish=1), store (blocks self-review, one per user), delete own. |
| `SellerController` | Seller profile + stats (cached), seller ads, seller reviews. |
| `SocialAuthController` | Google/Facebook OAuth: verifies access token via provider APIs, upserts user, returns token. |
| `PaymentCallbackController` | SSLCommerz success/fail/cancel/ipn; validates signature + amount, 302 to frontend, dispatches `FulfilTransactionJob`. |
| `CountryController` | Countries + paginated city search. |
| `FilterSchemaController` | "Advance Filter" field schema per category (from `custom_fields` scoping). |
| `MetaController` | Currencies, languages, key/value site settings. |
| `Admin/*` | Dashboard KPIs; ad approve/reject/feature; user ban/unban/update; category/plan/blog CRUD; transaction refund/mark-paid; settings read/write. |

### 3.3 Services

- **`Services/Payment`** — **Strategy pattern**: `PaymentGatewayInterface` + `PaymentManager` registry. **Only `sslcommerz` active**; 15 others (PayPal, Stripe, Razorpay, Paytm, Paystack, CCAvenue, etc.) exist as classes but are commented out of the registry. `SSLCommerzGateway` verifies SHA-512 `verify_hash` + amount match (anti-tamper). PayPal & Stripe have composer packages but are disabled.
- **`AdMutationService`** — DB-transactional ad create/update: syncs images, wipe-and-replace `custom_data`, any edit resets to `pending`.
- **`AdStatsService`** — Shop dashboard aggregates (rating, orders, sales, per-status counts, 30-day series).
- **`ListingService`** — Legacy search/filter/order/paginate + `promoted()` (featured/urgent/highlight).
- **`AuthService`** — Legacy auth port: brute-force lockout, sha512 login string, persistent cookie, register/reset.
- **`AdService`, `ReviewService`, `UserService`, `WatermarkService`** — cron expiry, ratings, registration, GD watermark.
- **`TemplateBridge`, `ThemeRenderer`** — legacy `.tpl` → Blade bridge (migration tooling).

### 3.4 Models & relationships (key)

| Model | Table | Key relations |
|---|---|---|
| `Post` (ad) | `product` | belongsTo `user`, `category`, `subCategory`; hasMany `reviews`, `customData`, `favouritedBy`; hasOne `resubmit`. Casts `status`→`PostStatus`. |
| `User` | `user` | hasMany `posts`, `favourites`, `transactions` (seller), `messagesSent/Recv`; appends `is_admin`, `is_shop`. |
| `Category` | `catagory_main` (PK `cat_id`) | hasMany `subCategories`, `posts`, `translations`. |
| `SubCategory` | `catagory_sub` (PK `sub_cat_id`) | belongsTo `category`; hasMany `posts`. |
| `Transaction` | `transaction` | belongsTo `seller`, `post`; casts `status`→`TransactionStatus`; 2026 cols `plan_id`, `purpose`, `meta`. |
| `Plan` | `plans` | Pricing tier (monthly/annual/lifetime); `PlanOption` = feature bullets. |
| `Message` | `messages` | One row per direction (`from_id`/`to_id`); belongsTo `post`. |
| `Review` | `reviews` | belongsTo `post`, `user`; one per user per ad. |
| `Favourite` | `favads` | user ↔ ad wishlist. |

Other models: `Blog`, `BlogCategory`, `BlogComment`, `Page`, `Faq`, `Testimonial`, `Adsense`, `City/Country/District/Region`, `Currency`, `CustomField(+Data+Option)`, `Option`, `UserOption`, `PaymentMethod`, `Balance`, `DeviceToken`, `PushNotification`, `EmailQueue`, `Language`, `TimeZone`, `Tax`, `Upgrade`, `PostResubmit`, `Admin`, `AuditLog`, `MobileNumber`, `CategoryTranslation`.

### 3.5 Key migrations / schema

- `2024_01_01_000001_create_legacy_quickad_tables.php` — recreates the full 31-table legacy schema (`ad_` prefix).
- `2026_07_22_000001_add_offersale_api_indexes.php` — browse/promo/inbox/fav/review indexes.
- `2026_07_24_120000_add_checkout_columns_to_transaction.php` — adds `plan_id`, `purpose`, `meta`, timestamps to `transaction`.
- `2026_07_24_160000_add_condition_and_statuses_to_post.php` — adds `product.condition` (new/used) + expanded status enum (`draft/pending/active/rejected/sold_out/removed/expire`).
- `2026_08_07_*` — quality/browse/composite/conversation indexes; **MySQL FULLTEXT** on `product(product_name, description, tag)` (ready for MATCH…AGAINST).
- `2026_08_07_120952_add_forgot_expires_at_to_user_table.php` — `user.forgot_expires_at`.
- `2026_08_12_070000_add_payment_subscription_fields.php` — `user.plan_id/plan_expires_at/ads_remaining` + `product.paid/transaction_id` (pay-before-post).

### 3.6 Middleware / security / infra

- `bootstrap/app.php` — aliases `quickad.auth`→`EnsureLegacyLogin`, `admin`→`EnsureAdmin`; JSON error renderer for API; `SetLocale` on web group.
- `EnsureAdmin` — Sanctum-authed user must match an `admins` row (by email/username) **or** `user_type='admin'`.
- **Throttling** on auth/contact endpoints; app-level brute-force lockout in `AuthService`.
- **Events/Jobs/Mail** — `MessageSent` (broadcast, Reverb), `FulfilTransactionJob` (idempotent post-payment side-effects: plan upgrade / boost / create ad from `tx.meta`), `SendMailJob` + `LegacyMail`.
- **Filament admin** also exists: `AdminPanelProvider` (`/admin`, session guard on `admins` table) with resources: Post, User, Transaction, Plan, Category, Blog.

---

## 4. Deployment / Infra Context

### 4.1 Third-party services

**Backend (`composer.json` / config):**

| Service | Package / impl | Status |
|---|---|---|
| Payment | SSLCommerz (native HTTP, no package) | **Active** |
| Payment (alt) | PayPal (`srmklive/paypal`), Stripe (`stripe/stripe-php`), + 13 stub gateways | Disabled / commented out of registry |
| WebSocket | `laravel/reverb` ^1.0 (Pusher/Ably alt configs) | Wired; frontend polls, not subscribed |
| Auth tokens | `laravel/sanctum` ^4.0 | Active |
| Admin panel | `filament/filament` ^3.0 | Active (`/admin`) |
| Mail | SMTP/SES/Postmark/Resend/Sendmail/Log (default `log`) | Configured |
| Storage | Local (`public/storage`), S3 disk | Local default |
| OAuth | Google/Facebook via direct HTTP (no Socialite) | Active |
| Cache/Queue | Redis (example) / file / database | `file` + `sync`/`database` in dev |
| SMS | — | **Not integrated** |

**Frontend (`frontend/package.json`):** `next` 15, `react` 19, `tailwindcss` 3.4, `@tanstack/react-table`, `@tiptap/*` (rich text), `leaflet`+`react-leaflet` (maps), `recharts` (charts), `framer-motion`, `lucide-react`, `sonner`, `laravel-echo`+`pusher-js` (Reverb client), `lenis` (smooth scroll), `next-themes`, `date-fns`, `isomorphic-dompurify`.

### 4.2 CI/CD

| Repo | Workflow | Trigger | Action |
|---|---|---|---|
| Backend | `.github/workflows/deploy.yml` | push `main` | SSH → `api.esawda.com`: `git pull`, `composer install --no-dev`, `migrate --force`, `config:cache`, `route:cache`, `optimize`. **No tests.** |
| Frontend | `frontend/.github/workflows/deploy.yml` | push `main` + manual | SSH → `demo.esawda.com`: `git fetch + reset --hard origin/main`, `npm ci`, `rm -rf .next`, `npm run build`, `pm2 restart esawda-frontend`. **No lint/tests.** |

### 4.3 Production topology

- Backend: `/home/esawda-api/htdocs/api.esawda.com` — nginx + PHP-FPM.
- Frontend: `/home/esawda-demo/htdocs/demo.esawda.com` — pm2, process `esawda-frontend`, port 3000.
- SSLCommerz callbacks must be allow-listed on `api.esawda.com/api/v1/payments/sslcommerz/{success,fail,cancel,ipn}`.

### 4.4 Tests

- **Backend:** PHPUnit (not Pest). Feature tests: `AuthTest`, `PayToPostTest`, `MessageBroadcastTest`, `RouteSmokeTest`, `Api/V1/*`. Unit: `PaymentManagerTest`, `TemplateBridgeTest`. Run: `./vendor/bin/phpunit`.
- **Frontend:** no framework installed; `frontend/tests/README.md` is a manual Playwright-MCP verification plan.

---

## 5. Quick Reference — "Where is the code for X?"

| Feature | Backend | Frontend |
|---|---|---|
| **Ad listing/browse** | `AdController` (`app/Http/Controllers/Api/V1/AdController.php`), `Filterable` (`app/Http/Concerns/Filterable.php`) | `app/ads/page.tsx`, `components/listing/ListingGrid.tsx` |
| **Ad detail** | `AdController@show` | `app/ads/[idSlug]/page.tsx` |
| **Post / edit / manage ads** | `AdMineController`, `AdMutationService` | `app/shop/(panel)/ads/**`, `app/shop/ads/new/AdForm.tsx` |
| **Ad statuses/conditions** | `app/Enums/PostStatus.php`, migration `2026_07_24_160000_*` | `ListingGrid`, shop ad filters |
| **Categories / taxonomy** | `CategoryController`, models `Category`/`SubCategory` | `app/category/**`, `components/filter/CategorySidebar.tsx` |
| **Auth (register/login/reset)** | `AuthController`, `AuthService` | `app/auth/**`, `components/auth/RoleLoginForm.tsx` |
| **OAuth (Google/FB)** | `SocialAuthController` | `app/auth/oauth-callback/page.tsx`, `components/interactive/LoginPopup.tsx`, `GoogleOneTapCard` |
| **Shop / seller profile** | `SellerController`, `AdStatsService` | `app/store/[username]/page.tsx`, `app/shop/(panel)/**`, `components/shop/v2/**` |
| **Chat / messages** | `MessageController`, `app/Events/MessageSent.php` | `app/messages/**`, `components/chat/ChatClient.tsx` |
| **Websocket/real-time** | `config/reverb.php`, `MessageSent` | `src/lib/echo.ts` (dormant; chat polls) |
| **Membership plans** | `ContentController@plans`, `Plan`, `PlanOption` | `app/membership/**`, `components/membership/**` |
| **Checkout / payment** | `CheckoutController`, `PaymentCallbackController`, `Services/Payment/**`, `FulfilTransactionJob` | `app/membership/checkout/**`, `CheckoutForm.tsx`, `app/payment/success/page.tsx` |
| **Pay-before-post gate** | `AdMineController@store` (402), `CheckoutController@adPost`, `FulfilTransactionJob` | `app/shop/ads/new/page.tsx`, `app/payment/success/page.tsx` |
| **Reviews** | `ReviewController`, `ReviewService` | `components/interactive/ReviewsSection.tsx`, `ReviewForm.tsx` |
| **Wishlist / favourites** | `FavouriteController` | `components/interactive/FavouriteButton.tsx`, `app/shop/favourites/page.tsx` |
| **Ad slots / banners** | `Adsense` model (unused placeholder) | `components/ads/AdSlot.tsx`, placements in public pages (see §2.7), images `/public/ad-*.jpg` |
| **Blog** | `ContentController@blogs`, models `Blog`/`BlogCategory` | `app/blog/**`, `components/blog/**`, `app/admin/(panel)/blog/**` |
| **Static pages / CMS** | `ContentController@pages`, `Page` | `app/pages/[slug]/page.tsx` |
| **Contact / FAQs / testimonials** | `ContentController` | `app/contact`, `app/faq`, `app/testimonials` |
| **Site settings** | `MetaController@settings`, `Option` model, `SettingsAdminController` | `lib/settings.ts`, `app/admin/(panel)/settings/**` |
| **Admin panel (JSON)** | `Api/V1/Admin/*` controllers | `app/admin/(panel)/**`, `components/admin/v2/**` |
| **Admin panel (Filament)** | `app/Filament/**`, `AdminPanelProvider` | — (separate `/admin`) |
| **Transactions / invoices** | `Transaction`, `TransactionAdminController`, `AccountController@transactions` | `app/shop/transactions/page.tsx`, `components/dashboard/InvoiceRow.tsx` |
| **Dashboard KPIs** | `Admin/DashboardController`, `AdStatsService` | `app/shop/(panel)/page.tsx`, `app/admin/(panel)/page.tsx` |
| **Money/date formatting** | — | `lib/format.ts` (BDT, en-IN grouping) |
| **API client / fetch wrapper** | — | `lib/api.ts`, `lib/env.ts`, `lib/session.ts`, `lib/auth.ts` |
| **Styling / design tokens** | — | `tailwind.config.ts`, `styles/globals.css`, `styles/admin.css`, `styles/shop.css` |
| **Tests** | `tests/Feature/**`, `tests/Unit/**` | `frontend/tests/README.md` (manual plan) |
| **Deploy** | `.github/workflows/deploy.yml` | `frontend/.github/workflows/deploy.yml` |

---

*Generated as a code-only analysis reference — no files were changed.*
