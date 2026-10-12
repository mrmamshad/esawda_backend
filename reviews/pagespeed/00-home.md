# PageSpeed Review — Home (`/`)

**URL:** https://esawda.com/
**Reviewed:** 2026-09-17
**Report:** https://pagespeed.web.dev/analysis/https-esawda-com/6csqh4yiv3

> ⚠️ No CrUX field data (insufficient real-user traffic). All values below are **Lab data (Lighthouse)**.

---

## Scores

| Category | 🖥️ Desktop | 📱 Mobile |
|----------|:----------:|:---------:|
| ⚡ Performance | **71** 🟠 | **87** 🟢 |
| ♿ Accessibility | 93 🟢 | 93 🟢 |
| ✅ Best Practices | 96 🟢 | 96 🟢 |
| 🔍 SEO | 100 🟢 | 100 🟢 |

## Core Web Vitals

| Metric | 🖥️ Desktop | 📱 Mobile |
|--------|:----------:|:---------:|
| First Contentful Paint (FCP) | 1.7 s 🟢 | 1.7 s 🟢 |
| Largest Contentful Paint (LCP) | **14.4 s** 🔴 | 3.5 s 🟠 |
| Total Blocking Time (TBT) | 30 ms 🟢 | 110 ms 🟢 |
| Cumulative Layout Shift (CLS) | 0 🟢 | 0 🟢 |
| Speed Index | 4.7 s 🟠 | 4.5 s 🟠 |

---

## 🔥 Top Opportunities (highest savings first)

| Issue | Est. Savings | Priority |
|-------|-------------|----------|
| **Improve image delivery** | 💥 **16,754 KiB** (desktop) / **18,389 KiB** (mobile) | 🔴 P0 |
| **Avoid enormous network payloads** | Total: **17,070 KiB** (desktop) / **19,005 KiB** (mobile) | 🔴 P0 |
| Render-blocking requests | 150 ms (desktop) / 1,300 ms (mobile) | 🟠 P1 |
| Reduce unused CSS | 10 KiB | 🟡 P2 |
| Legacy JavaScript | 12 KiB | 🟡 P2 |
| Avoid long main-thread tasks | 2 long tasks | 🟡 P2 |

## Diagnostics (no direct savings but worth checking)

- Optimize DOM size
- Use efficient cache lifetimes
- Duplicated JavaScript
- Font display
- Forced reflow
- LCP request discovery / LCP breakdown
- 3rd parties
- Minify CSS / Minify JS / Reduce unused JS
- Avoid non-composited animations
- JavaScript execution time: 0.3 s (desktop) / 0.5 s (mobile)
- Main-thread work: 0.6 s (desktop) / 1.1 s (mobile)

## Accessibility (score 93) — items to fix

- Elements use **prohibited ARIA attributes**
- **Contrast:** Background and foreground colors do not have a sufficient contrast ratio

---

## 💡 Recommended Actions (for later implementation)

1. **[P0] Image pipeline** — The homepage ships ~17-19 MB of images. This single issue causes the 14.4s LCP.
   - Serve **WebP/AVIF** instead of JPG/PNG
   - **Resize** images to actual displayed dimensions (biggest win)
   - Compress aggressively
   - Ensure the LCP hero image is **preloaded + `priority`**
   - Codebase already has `AdImageOptimizerService`, `OptimizeAdImages` command, `AdImage` support — verify they run & are applied to homepage listings.
2. **[P1] Render-blocking** — defer non-critical CSS/JS (mobile hit hardest: 1.3s).
3. **[P2] Accessibility** — fix prohibited ARIA + contrast issues.

---

_Notes: mobile scores/metrics to be filled after mobile analysis pass._
