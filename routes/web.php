<?php

use App\Http\Controllers\admin\CategoryController;
use App\Http\Controllers\admin\CMSController;
use App\Http\Controllers\admin\CommunityController;
use App\Http\Controllers\admin\CouponController;
use App\Http\Controllers\Admin\PermissionsController;
use App\Http\Controllers\admin\PlanController;
use App\Http\Controllers\admin\ProductController;
use App\Http\Controllers\Admin\RolesController;
use App\Http\Controllers\admin\SettingsController;
use App\Http\Controllers\admin\UserController;
use App\Http\Controllers\Front\CartController;
use App\Http\Controllers\Front\OrdersController;
use App\Http\Controllers\Front\ProductController as FrontProductController;
use App\Http\Controllers\Front\StripeController;
use App\Http\Controllers\Front\SubscriberController;
use App\Http\Controllers\Front\UserController as FrontUserController;
use App\Http\Controllers\Vendor\ProductController as VendorProductController;
use App\Http\Controllers\Vendor\UserController as VendorUserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Auth::routes();

// Admin Routes
Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->group(function () {
    // Admin Login
    Route::match(['get', 'post'], '/', [UserController::class, 'admin_login'])->name('admin.login');
    // Admin Forgot password
    Route::match(['get', 'post'], 'forgot-password', [UserController::class, 'forgotPassword'])->name('forgot-password');
    // Reset Password
    Route::match(['get', 'post'], 'reset-password/{token}', [UserController::class, 'resetPassword'])->name('reset-password');
    // Route::group(['middleware' => ['auth']], function () {
    Route::group(['middleware' => ['auth', 'permission']], function () {
        // Dashboard
        Route::get('/dashboard', [UserController::class, 'index'])->name('admin.dashboard');
        // Logout
        Route::get('/logout', [UserController::class, 'logout'])->name('admin.logout');
        // Profile
        Route::match(['get', 'post'], '/profile', [UserController::class, 'profile'])->name('admin.profile');

        // CMS Routes
        Route::get('/cms-index', [CMSController::class, 'index'])->name('admin.cms-index');
        Route::match(['get', 'post'], '/cms-create', [CMSController::class, 'create'])->name('admin.cms-create');
        Route::match(['get', 'post'], '/cms-update/{slug}/{id}', [CMSController::class, 'update'])->name('admin.cms-update');
        Route::get('delete-page/{slug}/{id}', [CMSController::class, 'destroy'])->name('admin.delete-page');

        // Settings
        Route::match(['get', 'post'], '/setting-email', [SettingsController::class, 'email'])->name('admin.setting-email');
        Route::match(['get', 'post'], '/setting-twillio', [SettingsController::class, 'twillio'])->name('admin.setting-twillio');

        // Products
        Route::match(['get', 'post'], '/product-create', [ProductController::class, 'create'])->name('admin.product-create');
        Route::match(['get', 'post'], '/product-index', [ProductController::class, 'index'])->name('admin.product-index');
        Route::match(['get', 'post'], '/product-update/{slug}/{id}', [ProductController::class, 'update'])->name('admin.product-update');
        Route::get('delete-product/{slug}/{id}', [ProductController::class, 'destroy'])->name('admin.delete-product');

        // Category
        Route::match(['get', 'post'], '/category-create', [CategoryController::class, 'create'])->name('admin.category-create');
        Route::match(['get', 'post'], '/category-index', [CategoryController::class, 'index'])->name('admin.category-index');
        Route::match(['get', 'post'], '/category-update/{slug}/{id}', [CategoryController::class, 'update'])->name('admin.category-update');
        Route::get('delete-category/{slug}/{id}', [CategoryController::class, 'destroy'])->name('admin.delete-category');

        // Coupon
        Route::match(['get', 'post'], '/coupon-create', [CouponController::class, 'create'])->name('admin.coupon-create');
        Route::match(['get', 'post'], '/coupon-index', [CouponController::class, 'index'])->name('admin.coupon-index');
        Route::match(['get', 'post'], '/coupon-update/{slug}/{id}', [CouponController::class, 'update'])->name('admin.coupon-update');
        Route::get('delete-coupon/{slug}/{id}', [CouponController::class, 'destroy'])->name('admin.delete-coupon');

        // User
        Route::get('/user-index', 'UserController@users')->name('admin.user.index');
        Route::get('/user-create', 'UserController@create')->name('admin.user.create');
        Route::post('/user-create', 'UserController@store')->name('admin.user.store');
        Route::get('/user-edit/{user}', 'UserController@edit')->name('admin.user.edit');
        Route::post('/user-update/{user}', 'UserController@update')->name('admin.user.update');
        Route::delete('/user-delete/{user}', 'UserController@destroy')->name('admin.user.destroy');

        Route::resource('roles', RolesController::class);
        Route::resource('permissions', PermissionsController::class);

        // Plans Route
        Route::match(['get', 'post'], '/plan-create', [PlanController::class, 'create'])->name('admin.plan-create');
        Route::match(['get', 'post'], '/plan-index', [PlanController::class, 'index'])->name('admin.plan-index');
        Route::match(['get', 'post'], '/plan-update/{slug}/{id}', [PlanController::class, 'update'])->name('admin.plan-update');
        Route::get('delete-plan/{slug}/{id}', [PlanController::class, 'destroy'])->name('admin.delete-plan');

        // Community
        Route::get('discussion-index', [CommunityController::class, 'index'])->name('admin.discussion-index');
        Route::match(['get', 'post'],'discussion-create', [CommunityController::class, 'create'])->name('admin.discussion-create');
        Route::match(['get', 'post'],'discussion-update/{slug}/{id}', [CommunityController::class, 'update'])->name('admin.discussion-update');
        Route::get('delete-discussion/{slug}/{id}', [CommunityController::class, 'destroy'])->name('admin.delete-discussion');

        // Stripe
        Route::get('stripe-index', [SettingsController::class, 'stripeIndex'])->name('stripe-index');
        Route::post('single-charge', [SettingsController::class, 'singelCharge'])->name('single-charge');
        Route::post('save-stripe-card', [SettingsController::class, 'saveStripeCard'])->name('save-stripe-card');
    });
});

// Vendor Routes
Route::prefix('vendor')->namespace('App\Http\Controllers\Vendor')->group(function () {
    // Vendor Login
    Route::match(['get', 'post'], '/', [VendorUserController::class, 'vendor_login'])->name('vendor.login');
    // Vendor Forgot password
    Route::match(['get', 'post'], 'forgot-password', [VendorUserController::class, 'forgotPassword'])->name('vendor.forgot-password');
    // Reset Password
    Route::match(['get', 'post'], 'reset-password/{token}', [VendorUserController::class, 'resetPassword'])->name('vendor.reset-password');
    Route::group(['middleware' => ['auth']], function () {
        // Dashboard
        Route::get('/dashboard', [VendorUserController::class, 'index'])->name('vendor.dashboard');
        // Logout
        Route::get('/log-out', [VendorUserController::class, 'logout'])->name('vendor.logout');
        // Profile
        Route::match(['get', 'post'], '/profile', [VendorUserController::class, 'profile'])->name('vendor.profile');

        // Products
        Route::match(['get', 'post'], '/product-create', [VendorProductController::class, 'create'])->name('vendor.product-create');
        Route::match(['get', 'post'], '/product-index', [VendorProductController::class, 'index'])->name('vendor.product-index');
        Route::match(['get', 'post'], '/product-update/{slug}/{id}', [VendorProductController::class, 'update'])->name('vendor.product-update');
        Route::get('delete-product/{slug}/{id}', [VendorProductController::class, 'destroy'])->name('vendor.delete-product');

        // Stripe Payment
        Route::match(['get','post'], '/payment', [VendorUserController::class, 'stripePayment'])->name('vendor.stripePayment');
    });
});

Route::any('/', function () {
    return redirect('/listing');
})->name('home');

// Front Routes

// Login
Route::match(['get','post'],'/log-in', [FrontUserController::class, 'login'])->name('log-in');
// Register
Route::match(['get', 'post'], '/register', [FrontUserController::class, 'register'])->name('register');

// Product Listing
Route::get('/listing', [FrontProductController::class, 'listing'])->name('listing');
// Product Details
Route::get('/product-details/{slug}/{id}', [FrontProductController::class, 'productDetails'])->name('product-details');

Route::group(['middleware' => ['auth']], function () {
    // Logout
    Route::controller(FrontUserController::class)->group(function () {
        Route::get('/log-out', 'logout')->name('log-out');
        Route::match(['get', 'post'], '/invite', 'invite')->name('invite');
        Route::match(['get', 'post'], '/profile', 'profile')->name('profile');
    });

    // Cart
    Route::controller(CartController::class)->group(function () {
        Route::match(['get', 'post'], '/cart', 'cart')->name('cart');
        Route::match(['get', 'post'], '/add-to-cart', 'addToCart')->name('add-to-cart');
    });

    // Product Payment Routes
    Route::controller(StripeController::class)->group(function () {
        Route::match(['get', 'post'], '/checkout', 'stripePost')->name('stripe.post');
        Route::get('stripe-checkout', 'checkoutSuccess')->name('stripe-checkout');
    });

    // Subscription Payment Routes
    Route::controller(SubscriberController::class)->group(function () {
        Route::match(['get', 'post'], '/subscribe', 'subscriberPost')->name('subscribe.post');
        Route::get('subscribe-checkout', 'subscriberCheckoutSuccess')->name('subscribe-checkout');
    });

    // Orders Route
    Route::controller(OrdersController::class)->group(function () {
        Route::get('order-list/{type?}', 'orderList')->name('order-list');
    });

    // Rating And Review Routes
    Route::controller(FrontProductController::class)->group(function () {
        Route::match(['get', 'post'], '/rating-review/{id}', 'ratingReview')->name('rating-review');
    });

    // Community Discussion
    Route::controller(CommunityController::class)->group(function(){
        Route::get('/get-discussion', 'getDiscussion')->name('get-discussion');
        Route::post('/reply', 'reply')->name('reply');
    });
});



// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
