<?php

use App\Http\Controllers\CarouselController;
use App\Http\Controllers\CommonController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomePageController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Products\AnalyticsController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\Users\AuthController;
use App\Http\Controllers\Users\DefaultController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */
Route::get('countries', [CommonController::class, 'countries']);
Route::get('page-categories', [CommonController::class, 'pageCategories']);
//Auth Routes
Route::prefix('auth')->group(function () {
	Route::post('login', [AuthController::class, 'loginUser']);
	Route::get('profile', [AuthController::class, 'profile'])->middleware('auth:api');
	Route::post('change-password', [AuthController::class, 'changePassword'])->middleware('auth:api');
});
// User routes
Route::prefix('users')->group(function () {
	Route::get('all', [DefaultController::class, 'users'])->middleware('auth:api', 'admin');
	Route::post('register', [DefaultController::class, 'create']);
	Route::post('create-user', [DefaultController::class, 'create'])->middleware('auth:api', 'admin');
	Route::get('show/{id}', [DefaultController::class, 'show'])->middleware('auth:api', 'admin');
	Route::put('update/{id}', [DefaultController::class, 'update'])->middleware('auth:api');
	Route::delete('delete/{id}', [DefaultController::class, 'delete'])->middleware('auth:api', 'admin');
});

// Orders routes
Route::get('orders', [OrderController::class, 'index'])->middleware('auth:api', 'staff');
Route::get('orders/pending', [OrderController::class, 'pendingOrders'])->middleware('auth:api', 'staff');
Route::get('orders/completed', [OrderController::class, 'completedOrders'])->middleware('auth:api', 'staff');
Route::put('orders/close/{id}', [OrderController::class, 'closeOrder'])->middleware('auth:api', 'staff');
Route::get('my-orders', [OrderController::class, 'myOrders'])->middleware('auth:api');
Route::get('orders/view/{id}', [OrderController::class, 'show'])->middleware('auth:api', 'staff');
Route::put('orders/update/{id}', [OrderController::class, 'update'])->middleware('auth:api', 'staff');
Route::post('place-order', [OrderController::class, 'order']);
// Carousel routes
Route::get('carousels', [CarouselController::class, 'index']);
Route::get('carousels/view/{id}', [CarouselController::class, 'show']);
Route::post('carousels/create', [CarouselController::class, 'store'])->middleware('auth:api', 'staff');
Route::delete('carousels/delete/{id}', [CarouselController::class, 'delete'])->middleware('auth:api', 'admin');

// Rating routes
Route::get('ratings/all', [RatingController::class, 'index'])->middleware('auth:api', 'staff');
Route::get('ratings/product', [RatingController::class, 'reviews']);
Route::get('ratings/my-reviews', [RatingController::class, 'clientReviews'])->middleware('auth:api');
Route::post('ratings/create', [RatingController::class, 'create']);
Route::put('ratings/approve/{id}', [RatingController::class, 'approve'])->middleware('auth:api', 'admin');
Route::get('ratings/client-product-review/{productId}', [RatingController::class, 'clientProductReview'])->middleware('auth:api');

// Statistics
Route::get('statistics/dashboard', [StatsController::class, 'index']);
Route::post('contact-us', [ContactController::class, 'contact']);

// Shop By Country
Route::get('product-countries',[AnalyticsController::class,'productCountries']);
Route::get('products/country/{country}',[AnalyticsController::class,'countryProducts']);
// Home Page Data
Route::get('homepage-data', [HomePageController::class, 'index']);
//Route::get('top-picks', [HomePageController::class, 'index']);
//Route::get('offers', [HomePageController::class, 'offers']);
//Route::get('best-wines', [HomePageController::class, 'best_wines']);
//Route::get('best-spirits', [HomePageController::class, 'best_spirits']);
//Route::get('best-beers', [HomePageController::class, 'best_beers']);
//Route::get('home-brands', [HomePageController::class, 'brands']);
//Route::get('home-carousels', [HomePageController::class, 'carousels']);
