<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

/**
 * sitemap.xml generator.
 *
 * Since the legacy Blade storefront has been retired in favour of the
 * Next.js frontend, every <loc> is built against FRONTEND_URL rather
 * than named Laravel routes. Google keeps indexing the public site
 * while the SPA takes over.
 */
class XmlController extends Controller
{
    public function index(Request $request)
    {
        $base = rtrim(explode(',', env('FRONTEND_URLS', 'http://localhost:3000'))[0], '/');

        $urls = [
            ['loc' => $base.'/',            'priority' => '1.0', 'changefreq' => 'daily'],
            ['loc' => $base.'/ads',         'priority' => '0.9', 'changefreq' => 'daily'],
            ['loc' => $base.'/categories',  'priority' => '0.8', 'changefreq' => 'weekly'],
        ];

        try {
            $ads = Post::active()->orderByDesc('id')->limit(2000)->get(['id', 'slug', 'updated_at']);
            foreach ($ads as $p) {
                $urls[] = [
                    'loc' => $base.'/ads/'.$p->id.'-'.($p->slug ?: 'ad'),
                    'lastmod' => optional($p->updated_at)->toAtomString(),
                    'priority' => '0.6',
                    'changefreq' => 'weekly',
                ];
            }
        } catch (\Throwable) {
            // DB not seeded (test env or fresh install) — skip dynamic entries.
        }

        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"
             ."<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
        foreach ($urls as $u) {
            $xml .= "  <url>\n    <loc>".htmlspecialchars($u['loc'])."</loc>\n";
            if (!empty($u['lastmod'])) {
                $xml .= "    <lastmod>{$u['lastmod']}</lastmod>\n";
            }
            if (!empty($u['changefreq'])) {
                $xml .= "    <changefreq>{$u['changefreq']}</changefreq>\n";
            }
            if (!empty($u['priority'])) {
                $xml .= "    <priority>{$u['priority']}</priority>\n";
            }
            $xml .= "  </url>\n";
        }
        $xml .= "</urlset>\n";

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }
}
