<?php

use App\Http\Controllers\Api\V1\AccountController;
use App\Http\Controllers\Api\V1\AdController;
use App\Http\Controllers\Api\V1\Admin\AdAdminController;
use App\Http\Controllers\Api\V1\Admin\AdPlacementAdminController;
use App\Http\Controllers\Api\V1\Admin\BlogAdminController;
use App\Http\Controllers\Api\V1\Admin\CategoryAdminController;
use App\Http\Controllers\Api\V1\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Api\V1\Admin\OrderAdminController;
use App\Http\Controllers\Api\V1\Admin\PlanAdminController;
use App\Http\Controllers\Api\V1\Admin\SettingsAdminController;
use App\Http\Controllers\Api\V1\Admin\TransactionAdminController;
use App\Http\Controllers\Api\V1\Admin\UserAdminController;
use App\Http\Controllers\Api\V1\AdMineController;
use App\Http\Controllers\Api\V1\AdPlacementController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\CheckoutController;
use App\Http\Controllers\Api\V1\ContentController;
use App\Http\Controllers\Api\V1\CountryController;
use App\Http\Controllers\Api\V1\FavouriteController;
use App\Http\Controllers\Api\V1\FilterSchemaController;
use App\Http\Controllers\Api\V1\HomeController;
use App\Http\Controllers\Api\V1\MessageController;
use App\Http\Controllers\Api\V1\MetaController;
use App\Http\Controllers\Api\V1\PaymentCallbackController;
use App\Http\Controllers\Api\V1\ReviewController;
use App\Http\Controllers\Api\V1\SellerController;
use App\Http\Controllers\Api\V1\ShopController;
use App\Http\Controllers\Api\V1\SocialAuthController;
use App\Http\Controllers\Api\V1\SubCategoryController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| offersale. API (v1)
|--------------------------------------------------------------------------
|
| Token-based auth (Sanctum). All routes return the uniform { data, meta,
| links? } envelope. Error responses come from the global exception
| handler in bootstrap/app.php.
|
| Endpoint spec: Step 3 of the migration plan.
|
*/

Route::prefix('v1')->group(function () {

    /* ---- Payment gateway callbacks (public, gateway-posted) ----------- */
    // POST-only: SSLCommerz form-posts these URLs. The GET aliases were
    // removed — GET-reachable transaction mutations are CSRF-friendly and
    // trivially triggered via <img>/navigation.
    Route::post('payments/sslcommerz/success', [PaymentCallbackController::class, 'success']);
    Route::post('payments/sslcommerz/fail', [PaymentCallbackController::class, 'fail']);
    Route::post('payments/sslcommerz/cancel', [PaymentCallbackController::class, 'cancel']);
    Route::post('payments/sslcommerz/ipn', [PaymentCallbackController::class, 'ipn']);

    /* ---- Auth (public) — throttled to blunt brute-force/cred-stuffing -- */
    Route::post('auth/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
    Route::post('auth/forgot', [AuthController::class, 'forgot'])->middleware('throttle:5,1');
    Route::post('auth/register', [AuthController::class, 'register'])->middleware('throttle:10,1');
    Route::post('auth/reset', [AuthController::class, 'reset'])->middleware('throttle:5,1');
    Route::post('auth/guest-login', [AuthController::class, 'guestLogin'])->middleware('throttle:10,1');
    Route::post('auth/guest-register', [AuthController::class, 'guestRegister'])->middleware('throttle:10,1');

    /* ---- Social auth (public) — Google/Facebook OAuth popup flow ------- */
    // NOTE: the insecure `auth/social/google/silent` (email-only, no token
    // verification) was removed — see SILENT_GOOGLE_REMOVAL. All social
    // logins must go through the verified `callback` flow below.
    Route::post('auth/social/{provider}/callback', [SocialAuthController::class, 'callback'])
        ->where('provider', 'google|facebook')
        ->middleware('throttle:10,1');

    /* ---- Taxonomy (public) --------------------------------------------- */
    Route::get('categories', [CategoryController::class, 'index']);
    Route::get('categories/{slug}', [CategoryController::class, 'show']);
    Route::get('subcategories', [SubCategoryController::class, 'index']);
    Route::get('countries', [CountryController::class, 'index']);
    Route::get('countries/{code}/cities', [CountryController::class, 'cities']);
    Route::get('currencies', [MetaController::class, 'currencies']);
    Route::get('languages', [MetaController::class, 'languages']);
    Route::get('settings', [MetaController::class, 'settings']);
    Route::get('filter-schema', [FilterSchemaController::class, 'show']);

    /* ---- Homepage aggregate (one-shot, cached 120s) -------------------- */
    Route::get('home', [HomeController::class, 'index']);

    /* ---- Ads (public read) --------------------------------------------- */
    Route::get('ads', [AdController::class, 'index']);
    Route::get('ads/featured', [AdController::class, 'featured']);
    Route::get('ads/placements', [AdPlacementController::class, 'index']);
    Route::get('ads/search-suggest', [AdController::class, 'searchSuggest']);
    Route::get('ads/{idSlug}', [AdController::class, 'show'])->where('idSlug', '[0-9]+(-.*)?');
    Route::get('ads/{id}/similar', [AdController::class, 'similar'])->whereNumber('id');

    /* ---- Sellers (public) --------------------------------------------- */
    Route::get('sellers/{username}', [SellerController::class, 'show']);
    Route::get('sellers/{username}/ads', [SellerController::class, 'ads']);
    Route::get('sellers/{username}/reviews', [SellerController::class, 'reviews']);

    /* ---- Reviews (public read) ---------------------------------------- */
    Route::get('ads/{id}/reviews', [ReviewController::class, 'index'])->whereNumber('id');

    /* ---- Content (public read) ---------------------------------------- */
    Route::get('pages', [ContentController::class, 'pages']);
    Route::get('pages/{slug}', [ContentController::class, 'page']);
    Route::get('faqs', [ContentController::class, 'faqs']);
    Route::get('testimonials', [ContentController::class, 'testimonials']);
    Route::get('plans', [ContentController::class, 'plans']);
    Route::get('blogs', [ContentController::class, 'blogs']);
    Route::get('blog-categories', [ContentController::class, 'blogCategories']);
    Route::get('blogs/{idSlug}', [ContentController::class, 'blog']);
    Route::post('contact', [ContentController::class, 'contact'])
        ->middleware('throttle:10,1');

    /* ---- Authenticated (Sanctum bearer) -------------------------------- */
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('auth/me', [AuthController::class, 'me']);
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::post('auth/logout-all', [AuthController::class, 'logoutAll']);

        /* ---- Account (self-service) ------------------------------------ */
        Route::put('me', [AccountController::class, 'updateProfile']);
        Route::post('me/avatar', [AccountController::class, 'uploadAvatar']);
        Route::post('me/cover', [AccountController::class, 'uploadCover']);
        Route::post('me/shop-banner', [AccountController::class, 'uploadShopBanner']);
        Route::post('me/password', [AccountController::class, 'changePassword']);
        Route::get('me/transactions', [AccountController::class, 'transactions']);
        Route::get('me/purchases', [AccountController::class, 'purchases']);
        Route::get('me/orders', [AccountController::class, 'orders']);
        Route::post('me/shop/apply', [ShopController::class, 'apply']);
        Route::get('me/shop/status', [ShopController::class, 'status']);

        /* ---- Ads (owner write) ----------------------------------------- */
        Route::post('ads', [AdMineController::class, 'store']);
        Route::put('ads/{id}', [AdMineController::class, 'update'])->whereNumber('id');
        Route::delete('ads/{id}', [AdMineController::class, 'destroy'])->whereNumber('id');
        Route::post('ads/{id}/images', [AdMineController::class, 'addImages'])->whereNumber('id');
        Route::delete('ads/{id}/images/{filename}', [AdMineController::class, 'deleteImage'])->whereNumber('id');
        Route::post('ads/{id}/{action}', [AdMineController::class, 'action'])
            ->whereNumber('id')
            ->whereIn('action', ['hide', 'unhide', 'resubmit', 'sold-out', 'restock', 'remove', 'publish']);
        Route::get('me/ads', [AdMineController::class, 'mine']);
        Route::get('me/shop/stats', [AdMineController::class, 'shopStats']);
        Route::get('me/wishlisted', [AdMineController::class, 'wishlisted']);

        /* ---- Favourites (wishlist) ------------------------------------- */
        Route::get('me/favourites', [FavouriteController::class, 'index']);
        Route::post('ads/{id}/favourite', [FavouriteController::class, 'add'])->whereNumber('id');
        Route::delete('ads/{id}/favourite', [FavouriteController::class, 'remove'])->whereNumber('id');

        /* ---- Reviews (write) ------------------------------------------- */
        Route::post('ads/{id}/reviews', [ReviewController::class, 'store'])->whereNumber('id');
        Route::delete('reviews/{id}', [ReviewController::class, 'destroy'])->whereNumber('id');

        /* ---- Chat / messages ------------------------------------------- */
        Route::get('me/threads', [MessageController::class, 'threads']);
        Route::get('me/threads/unread-count', [MessageController::class, 'unreadCount']);
        Route::get('me/threads/{userId}', [MessageController::class, 'thread'])->whereNumber('userId');
        Route::post('me/threads/{userId}/read', [MessageController::class, 'markRead'])->whereNumber('userId');
        Route::post('messages', [MessageController::class, 'send']);

        /* ---- Checkout (SSLCommerz) ------------------------------------- */
        Route::post('checkout/plan/{planId}', [CheckoutController::class, 'plan'])->whereNumber('planId');
        Route::post('checkout/ad-upgrade/{postId}', [CheckoutController::class, 'adUpgrade'])->whereNumber('postId');
        Route::post('checkout/ad-post', [CheckoutController::class, 'adPost']);
        Route::post('checkout/paid-listing', [CheckoutController::class, 'paidListing']);
        Route::post('checkout/product-purchase/{postId}', [CheckoutController::class, 'productPurchase'])->whereNumber('postId');
        Route::get('checkout/transactions/{id}', [CheckoutController::class, 'status'])->whereNumber('id');
    });

    /* ---- Admin API (auth + admin middleware) -------------------------- */
    Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
        Route::get('dashboard', [AdminDashboardController::class, 'index']);

        Route::get('ads/placements', [AdPlacementAdminController::class, 'index']);
        Route::get('ads/placements/{id}', [AdPlacementAdminController::class, 'show'])->whereNumber('id');
        Route::post('ads/placements', [AdPlacementAdminController::class, 'store']);
        Route::put('ads/placements/{id}', [AdPlacementAdminController::class, 'update'])->whereNumber('id');
        Route::delete('ads/placements/{id}', [AdPlacementAdminController::class, 'destroy'])->whereNumber('id');

        Route::get('users', [UserAdminController::class, 'index']);
        Route::get('users/{id}', [UserAdminController::class, 'show'])->whereNumber('id');
        Route::patch('users/{id}', [UserAdminController::class, 'update'])->whereNumber('id');
        Route::post('users/{id}/ban', [UserAdminController::class, 'ban'])->whereNumber('id');
        Route::post('users/{id}/unban', [UserAdminController::class, 'unban'])->whereNumber('id');
        Route::post('users/{id}/verify-shop', [UserAdminController::class, 'verifyShop'])->whereNumber('id');
        Route::post('users/{id}/unverify-shop', [UserAdminController::class, 'unverifyShop'])->whereNumber('id');
        Route::post('users/{id}/reset-password', [UserAdminController::class, 'resetPassword'])->whereNumber('id');
        Route::delete('users/{id}', [UserAdminController::class, 'destroy'])->whereNumber('id');

        Route::get('ads', [AdAdminController::class, 'index']);
        Route::get('ads/{id}', [AdAdminController::class, 'show'])->whereNumber('id');
        Route::post('ads/{id}/approve', [AdAdminController::class, 'approve'])->whereNumber('id');
        Route::post('ads/{id}/reject', [AdAdminController::class, 'reject'])->whereNumber('id');
        Route::post('ads/{id}/feature', [AdAdminController::class, 'feature'])->whereNumber('id');
        Route::post('ads/{id}/unfeature', [AdAdminController::class, 'unfeature'])->whereNumber('id');
        Route::delete('ads/{id}', [AdAdminController::class, 'destroy'])->whereNumber('id');

        Route::apiResource('categories', CategoryAdminController::class);
        Route::apiResource('plans', PlanAdminController::class);
        Route::apiResource('blogs', BlogAdminController::class);

        Route::get('transactions', [TransactionAdminController::class, 'index']);
        Route::get('transactions/{id}', [TransactionAdminController::class, 'show'])->whereNumber('id');
        Route::post('transactions/{id}/refund', [TransactionAdminController::class, 'refund'])->whereNumber('id');
        Route::post('transactions/{id}/mark-paid', [TransactionAdminController::class, 'markPaid'])->whereNumber('id');

        Route::get('orders', [OrderAdminController::class, 'index']);
        Route::get('orders/{id}', [OrderAdminController::class, 'show'])->whereNumber('id');
        Route::patch('orders/{id}', [OrderAdminController::class, 'update'])->whereNumber('id');

        Route::get('settings', [SettingsAdminController::class, 'index']);
        Route::put('settings', [SettingsAdminController::class, 'update']);
    });
});
