# eSawda — PageSpeed Findings & Action Plan

**Date:** 2026-09-17
**Reviewed by:** Rovo Dev (research only — no code changed)
**Pages sampled via PageSpeed Insights:** `/` (Home), `/ads`, `/shops`

> ⚠️ No CrUX field data on any page (site lacks enough real-user traffic). All numbers are **Lab data (Lighthouse)**.

---

## 1. Scores (sampled pages)

| Page | 🖥️ Perf | 📱 Perf | A11y | Best Prac. | SEO | Notes |
|------|:-------:|:-------:|:----:|:----------:|:---:|-------|
| `/` Home | 71 🟠 | 87 🟢 | 93 | 96 | 100 | Desktop LCP **14.4s** |
| `/ads` | 77 🟠 | ⚠️ fail | 95 | 96 | 100 | **31 MB** page; mobile Lighthouse errored |
| `/shops` (mobile, earlier) | — | 78 🟠 | 93 | 96 | **69 🔴** | LCP 5.0s; SEO low |

### Core Web Vitals
| Metric | Home 🖥️ | Home 📱 | /ads 🖥️ |
|--------|:------:|:------:|:------:|
| FCP | 1.7s | 1.7s | 0.3s |
| **LCP** | **14.4s** 🔴 | 3.5s 🟠 | 5.5s 🟠 |
| TBT | 30ms | 110ms | 0ms |
| CLS | 0 🟢 | 0 🟢 | 0 🟢 |
| Speed Index | 4.7s | 4.5s | 0.7s |

---

## 2. 🔥 THE #1 PROBLEM — Image delivery (every page)

Every page ships enormous images. This single issue causes the bad LCP scores.

| Page | Total payload | Image savings possible |
|------|--------------|------------------------|
| Home | 17–19 MB | **16.7–18.4 MB** |
| /ads | **31 MB** 🔴 | **30.4 MB** |

`/ads` is so heavy that **mobile Lighthouse repeatedly failed to finish** — that alone shows how severe this is.

---

## 3. 🎯 ROOT CAUSE (confirmed on production storage)

Inspected `storage/app/public/products` on the VPS:

| Fact | Value |
|------|-------|
| Display images | 85 files, **91 MB total** |
| Largest single image | **5.7 MB** (a `.png`) |
| Images **> 1 MB** | **32 files** |
| Format: **PNG** (bad for photos) | **47 files** |
| Format: JPG/JPEG | 38 files |
| Format: **WebP/AVIF** | **0 files** 🔴 |
| Display images **with NO thumbnail** | **28 files** 🔴 |

### Why the pages are heavy — 3 concrete reasons

1. **28 images have no thumbnail.**
   `AdImage::thumbUrl()` (see `app/Support/AdImage.php:59`) falls back to the **full-size display image** when a thumb is missing. So listing cards — which should load a 480px thumb — instead download the full **1–5 MB** original. On a grid of 30+ cards this explodes to 30 MB.

2. **47 images are PNG.** PNG is lossless — a photo saved as PNG is 5–10× larger than the same JPEG/WebP. The 5.7 MB monsters are all PNG.

3. **The optimizer never ran on existing images.** The pipeline is fine in code — `OptimizeAdImageJob` → `AdImageOptimizerService` resizes to 1600px + makes a 480px thumb (`app/Services/AdImageOptimizerService.php`). It's dispatched on **new** uploads (`AdMutationService.php:219`), but the **old images predate it** and were never backfilled. Also it re-encodes JPEG/PNG in place — it does **not** produce WebP/AVIF.

### The frontend is actually fine ✅
`ListingCard.tsx` correctly uses `next/image` with `sizes` and the `thumb` URL. The bottleneck is **backend assets**, not React markup. (Minor: 16 stray `<img>` tags exist in admin/dashboard, low priority.)

---

## 4. ✅ Action Plan (priority order)

### 🥇 P0 — Fix the image assets (biggest win, ~90% of the problem)
1. **Backfill: generate missing thumbnails + re-compress all 85 existing images.**
   - A command already exists: `php artisan ads:optimize-images` (`OptimizeAdImages.php`). **Run it on production** — it will create the 28 missing thumbs and shrink oversized originals.
   - ⚠️ Verify it force-reprocesses already-large files (currently it may skip). May need a `--force` flag.
2. **Add WebP/AVIF output** to `AdImageOptimizerService` (currently JPEG/PNG only). Serving WebP thumbs alone cuts ~70% off.
3. **Convert PNG photos → JPEG/WebP** during optimization (detect non-transparent PNGs and re-encode).

### 🥈 P1 — Guardrails so it never regresses
4. **Reject/convert huge uploads:** `max_image_kb` is **25,600 KB (25 MB!)** in `config/quickad.php`. Lower to ~5 MB and always run the optimizer synchronously enough that thumbs exist before the ad is shown.
5. **Ensure the queue worker is running** so `OptimizeAdImageJob` actually processes (if the worker is down, new uploads also stay unoptimized).

### 🥉 P2 — Page-level polish
6. **`/shops` mobile SEO = 69** — investigate meta description / crawlable links / robots. Quick, high-ROI for search ranking.
7. Render-blocking CSS/JS (mobile home: 1.3s) — defer non-critical.
8. Reduce unused CSS/JS, legacy JS (~30 KiB total).
9. Accessibility: prohibited ARIA attributes + colour contrast.

---

## 5. Expected impact

Fixing P0 alone should take:
- `/ads` payload **31 MB → ~2–3 MB**
- Home LCP **14.4s → ~2s**
- Mobile Perf into the **90s**, desktop back to **95+**

---

## 6. Note on full per-page crawl

Crawling all ~30 pages individually via PageSpeed is possible but extremely slow (each page = a fresh desktop + mobile Lighthouse run, and heavy pages time out). Since **every page shares the same root cause** (backend image assets), fixing P0 improves them all at once. Recommend fixing first, then re-running PageSpeed on a few key pages to confirm.
