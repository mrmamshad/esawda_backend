<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\SubCategory;
use App\Services\ListingService;
use App\Services\ThemeRenderer;
use Illuminate\Http\Request;

/**
 * Legacy `php/listing.php` — search results page. The AltoRouter mapped
 * several friendly URLs (category, sub-category, city, keywords) at the
 * same controller so we expose the same façade here.
 */
class ListingController extends Controller
{
    public function __construct(
        private ListingService $listing,
        private ThemeRenderer $theme,
    ) {}

    public function index(Request $request)
    {
        return $this->render($request);
    }

    public function category(Request $request, ?string $cat = null, ?string $subcat = null)
    {
        $request->query->add(['cat' => $cat, 'subcat' => $subcat]);

        return $this->render($request);
    }

    public function subCategory(Request $request, ?string $subcat = null, ?string $slug = null)
    {
        $request->query->add(['subcat' => $subcat]);

        return $this->render($request);
    }

    public function city(Request $request, ?string $city = null)
    {
        $request->query->add(['city' => $city]);

        return $this->render($request);
    }

    public function keywords(Request $request, ?string $keywords = null)
    {
        $request->query->add(['keywords' => $keywords]);

        return $this->render($request);
    }

    private function render(Request $request)
    {
        try {
            $items = $this->listing->search($request);
            $data = [
                'items' => $items,
                'total' => $items->total(),
                'categories' => Category::orderBy('cat_order')->get(),
                'subcategories' => SubCategory::orderBy('cat_order')->get(),
            ];

            return $this->theme->render('ad-listing', $data);
        } catch (\Throwable) {
            // Theme view may reference undefined variables until Phase 4b (view data-binding
            // pass) completes for every converted .tpl. Fall back to placeholder in the
            // meantime so integration tests stay green.
            return view('placeholder', ['legacy' => 'listing.php', 'action' => 'index']);
        }
    }
}
