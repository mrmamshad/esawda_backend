<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — Legacy Blade Frontend DISABLED
|--------------------------------------------------------------------------
|
| Per migration decision (2026-07-22): the classified-ads storefront is now
| served by the Next.js app in /frontend against /api/v1. All Blade page
| routes below have been commented out. Filament admin (/admin) is mounted
| by the Filament service provider and is NOT affected. The XML sitemap
| stays live so Google keeps indexing while the new site takes over.
|
| To re-enable any legacy page during the transition, uncomment the single
| line — every controller class is still present in app/Http/Controllers/.
|
*/

use App\Http\Controllers\XmlController;

// ---- Kept live -----------------------------------------------------------
Route::get('/sitemap.xml', [XmlController::class, 'index'])->name('sitemap.xml');

Route::get('/', function () {
    // Root now advertises the new SPA. Change target once frontend is deployed.
    return response()->json([
        'name'    => config('app.name'),
        'status'  => 'API only — visit the offersale. frontend',
        'api'     => url('/api/v1'),
        'admin'   => url('/admin'),
    ]);
});

// ---- Legacy Blade storefront (commented out) -----------------------------
/*
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SignupController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\ForgotController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\CountriesController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\AdvertiseController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AccountSettingController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TestimonialsController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\HtmlController;
use App\Http\Controllers\Ad\DetailController as AdDetailController;
use App\Http\Controllers\Ad\PostController as AdPostController;
use App\Http\Controllers\Ad\EditController as AdEditController;
use App\Http\Controllers\Ad\MyAdsController;
use App\Http\Controllers\Ad\PendingController as AdPendingController;
use App\Http\Controllers\Ad\ExpireController as AdExpireController;
use App\Http\Controllers\Ad\FavouriteController as AdFavouriteController;
use App\Http\Controllers\Ad\HiddenController as AdHiddenController;
use App\Http\Controllers\Ad\ResubmissionController as AdResubmissionController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\Blog\BlogController;
use App\Http\Controllers\Blog\BlogCategoryController;
use App\Http\Controllers\Blog\BlogAuthorController;
use App\Http\Controllers\Blog\BlogSingleController;

Route::match(['get', 'post'], '/home/{lang?}',                     [HomeController::class, 'index'])->name('home.lang');
Route::match(['get', 'post'], '/home/{lang}/{country}',            [HomeController::class, 'index'])->name('home.langCountry');
Route::match(['get', 'post'], '/theme/{theme?}',                   [HomeController::class, 'index'])->name('home.theme')->where('theme', '.*');
Route::match(['get', 'post'], '/index1',                           [HomeController::class, 'variant1'])->name('home.variant1');
Route::match(['get', 'post'], '/index2',                           [HomeController::class, 'variant2'])->name('home.variant2');
Route::match(['get', 'post'], '/signup',                           [SignupController::class, 'index'])->name('auth.signup');
Route::match(['get', 'post'], '/login',                            [LoginController::class,  'index'])->name('auth.login');
Route::match(['get', 'post'], '/logout',                           [LogoutController::class, 'index'])->name('auth.logout');
Route::match(['get', 'post'], '/forgot',                           [ForgotController::class, 'index'])->name('auth.forgot');
Route::match(['get', 'post'], '/dashboard',                        [DashboardController::class, 'index'])->name('dashboard');
Route::match(['get', 'post'], '/myads/{page?}',                    [MyAdsController::class, 'index'])->name('ad.mine')->where('page', '.*');
Route::match(['get', 'post'], '/pending/{page?}',                  [AdPendingController::class, 'index'])->name('ad.pending')->where('page', '.*');
Route::match(['get', 'post'], '/expire/{page?}',                   [AdExpireController::class, 'index'])->name('ad.expire')->where('page', '.*');
Route::match(['get', 'post'], '/favourite/{page?}',                [AdFavouriteController::class, 'index'])->name('ad.favourite')->where('page', '.*');
Route::match(['get', 'post'], '/hidden/{page?}',                   [AdHiddenController::class, 'index'])->name('ad.hidden')->where('page', '.*');
Route::match(['get', 'post'], '/resubmission/{page?}',             [AdResubmissionController::class, 'index'])->name('ad.resubmission')->where('page', '.*');
Route::match(['get', 'post'], '/transaction',                      [TransactionController::class, 'index'])->name('transaction');
Route::match(['get', 'post'], '/account-setting',                  [AccountSettingController::class, 'index'])->name('account.setting');
Route::match(['get', 'post'], '/message',                          [MessageController::class, 'index'])->name('message');
Route::match(['get', 'post'], '/report',                           [ReportController::class, 'index'])->name('report');
Route::match(['get', 'post'], '/contact',                          [ContactController::class, 'index'])->name('contact');
Route::match(['get', 'post'], '/sitemap',                          [SitemapController::class, 'index'])->name('sitemap');
Route::match(['get', 'post'], '/countries',                        [CountriesController::class, 'index'])->name('countries');
Route::match(['get', 'post'], '/faq',                              [FaqController::class, 'index'])->name('faq');
Route::match(['get', 'post'], '/feedback',                         [FeedbackController::class, 'index'])->name('feedback');
Route::match(['get', 'post'], '/advertise-here',                   [AdvertiseController::class, 'index'])->name('advertise-here');
Route::match(['get', 'post'], '/profile/{username?}/{page?}',      [ProfileController::class, 'index'])->name('profile')->where(['username' => '.*', 'page' => '.*']);
Route::match(['get', 'post'], '/ad/{id?}/{slug?}',                 [AdDetailController::class, 'index'])->name('ad.detail')->where('slug', '.*');
Route::match(['get', 'post'], '/post-ad/{lang?}/{country?}/{action?}', [AdPostController::class, 'index'])->name('ad.post');
Route::match(['get', 'post'], '/edit-ad/{id?}/{lang?}/{country?}/{action?}', [AdEditController::class, 'index'])->name('ad.edit');
Route::match(['get', 'post'], '/listing',                          [ListingController::class, 'index'])->name('listing');
Route::match(['get', 'post'], '/category/{cat?}/{subcat?}',        [ListingController::class, 'category'])->name('listing.category')->where(['cat' => '.*', 'subcat' => '.*']);
Route::match(['get', 'post'], '/sub-category/{subcat?}/{slug?}',   [ListingController::class, 'subCategory'])->name('listing.subcategory')->where(['subcat' => '.*', 'slug' => '.*']);
Route::match(['get', 'post'], '/city/{city?}/{slug?}',             [ListingController::class, 'city'])->name('listing.city')->where('slug', '.*');
Route::match(['get', 'post'], '/keywords/{keywords?}',             [ListingController::class, 'keywords'])->name('listing.keywords')->where('keywords', '.*');
Route::match(['get', 'post'], '/page/{id?}',                       [HtmlController::class, 'index'])->name('page')->where('id', '.*');
Route::match(['get', 'post'], '/membership/{change_plan?}',        [MembershipController::class, 'index'])->name('membership');
Route::match(['get', 'post'], '/ipn/{i?}/{access_token?}',         [PaymentController::class, 'ipn'])->name('payment.ipn')->where(['access_token' => '[^/]*']);
Route::match(['get', 'post'], '/payment/{access_token?}/{i?}/{status?}', [PaymentController::class, 'index'])->name('payment')->where(['access_token' => '[^/]*', 'i' => '[^/]*', 'status' => '[^/]*']);
Route::match(['get', 'post'], '/testimonials',                     [TestimonialsController::class, 'index'])->name('testimonials');
Route::match(['get', 'post'], '/blog',                             [BlogController::class, 'index'])->name('blog.index');
Route::match(['get', 'post'], '/blog/category/{keyword}',          [BlogCategoryController::class, 'index'])->name('blog.category')->where('keyword', '.*');
Route::match(['get', 'post'], '/blog/author/{keyword}',            [BlogAuthorController::class, 'index'])->name('blog.author')->where('keyword', '.*');
Route::match(['get', 'post'], '/blog/{id?}/{slug?}',               [BlogSingleController::class, 'index'])->name('blog.single')->where('slug', '.*');
Route::match(['get', 'post'], '/webhook/{i?}',                     [WebhookController::class, 'index'])->name('webhook')->where('i', '.*');
Route::match(['get', 'post'], '/invoice/{id?}',                    [InvoiceController::class, 'index'])->name('invoice');
*/
