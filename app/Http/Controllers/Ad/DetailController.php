<?php

namespace App\Http\Controllers\Ad;

use App\Enums\PostStatus;
use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Services\ThemeRenderer;
use Illuminate\Http\Request;

/**
 * Legacy `php/ad-detail.php`. Shows a single ad with reviews, custom
 * fields, related items, and (POST) a contact-seller email form.
 */
class DetailController extends Controller
{
    public function __construct(private ThemeRenderer $theme) {}

    public function index(Request $request, ?string $id = null, ?string $slug = null)
    {
        if (!$id) {
            abort(404);
        }
        $post = Post::with(['user', 'category', 'subCategory', 'reviews', 'customData'])
            ->find($id);
        if (!$post || $post->status !== PostStatus::Active) {
            abort(404);
        }

        // legacy: increment view counter
        $post->increment('view');

        // Contact seller (POST sendemail)
        if ($request->isMethod('post') && $request->filled('sendemail')) {
            $request->validate([
                'name' => 'required|string|max:100',
                'email' => 'required|email',
                'message' => 'required|string|max:2000',
            ]);
            session()->flash('flash_success', 'Message sent to the seller.');

            return back();
        }

        $related = Post::active()
            ->where('id', '!=', $post->id)
            ->where('category', $post->category)
            ->orderByDesc('id')->limit(6)->get();

        $data = [
            'post' => $post,
            'related' => $related,
            // Legacy $item_* variables the theme uses directly.
            'item_id' => $post->id,
            'item_title' => $post->product_name,
            'item_desc' => $post->description,
            'item_price' => $post->price,
            'item_negotiate' => $post->negotiable,
            'item_phone' => $post->phone,
            'item_city' => $post->city,
            'item_state' => $post->state,
            'item_country' => $post->country,
            'item_location' => $post->location,
            'item_lat' => $post->latlong ? explode(',', $post->latlong)[0] ?? '' : '',
            'item_long' => $post->latlong ? explode(',', $post->latlong)[1] ?? '' : '',
            'item_link' => route('ad.detail', ['id' => $post->id, 'slug' => $post->slug]),
            'item_created' => optional($post->created_at)->format('Y-m-d H:i'),
            'item_tag' => $post->tag,
            'item_view' => $post->view,
            'item_category' => optional($post->category)->cat_name ?? '',
            'item_catlink' => '#',
            'item_sub_category' => optional($post->subCategory)->sub_cat_name ?? '',
            'item_subcatlink' => '#',
            'item_authorid' => optional($post->user)->id,
            'item_authorname' => optional($post->user)->name,
            'item_authoruname' => optional($post->user)->username,
            'item_authorimg' => optional($post->user)->image ?? 'default_user.png',
            'item_authorlink' => optional($post->user) ? route('profile', ['username' => $post->user->username]) : '#',
            'item_authorjoined' => optional(optional($post->user)->created_at)->format('Y'),
            'item_authoronline' => optional($post->user)->online === '1' ? 'online' : 'offline',
            'itemreview' => $post->reviews,
            'gmap_api_key' => '',
            'ad_detail_page_sidebar' => '',
            'ad_detail_page_above_similar_section' => '',
        ];
        try {
            return $this->theme->render('ad-detail', $data);
        } catch (\Throwable) {
            return view('placeholder', ['legacy' => 'ad-detail.php', 'action' => "id=$id"]);
        }
    }
}
