<?php

use App\Http\Controllers\Products\AnalyticsController;
use Illuminate\Support\Facades\Route;
// Products
use App\Http\Controllers\Products\BrandController;
use App\Http\Controllers\Products\CategoryController;
use App\Http\Controllers\Products\ProductController;
use App\Http\Controllers\Products\QuantityController;

/**
 * Product Routes
 */
Route::get('random-products', [ProductController::class, 'randomProducts']);
Route::get('all-products', [ProductController::class, 'allProducts']);
Route::any('products', [ProductController::class, 'index'])->middleware();
Route::get('products/by-label', [ProductController::class, 'productsByLabel']);
Route::any('products/search', [ProductController::class, 'searchProducts']);
Route::get('products/view/{id}', [ProductController::class, 'show']);
Route::get('products/slug/{url}', [ProductController::class, 'product']);
Route::get('products/slug/related/{url}', [ProductController::class, 'relatedProducts']);
Route::get('products/categories/{url}', [CategoryController::class, 'getProductsByCategorySlug']);

Route::post('products/create', [ProductController::class, 'create'])->middleware('auth:api', 'admin');
Route::put('products/update/{id}', [ProductController::class, 'update'])->middleware('auth:api', 'admin');
Route::delete('products/delete/{id}', [ProductController::class, 'delete'])->middleware('auth:api', 'admin');
Route::get('products/remove-quantity', [ProductController::class, 'removeQuantity'])->middleware('auth:api', 'admin');
// Product analytics
Route::get('products/expensive', [AnalyticsController::class, 'expensiveDrinks']);
Route::get('products/price-list', [AnalyticsController::class, 'priceList']);
//Brands
Route::get('brands', [BrandController::class, 'brands']);
Route::get('brands/all', [BrandController::class, 'allBrands']);
Route::get('brands/grouped', [BrandController::class, 'groupByCategory']);
Route::get('brands/slug/{url}', [BrandController::class, 'brand']);
Route::get('brands/slug/products/{url}', [BrandController::class, 'brandProducts']);
Route::get('brands/get-by-name/{name}', [BrandController::class, 'brandByName']);
Route::get('brands/products/get-by-name/{name}', [BrandController::class, 'productsByName']);
Route::get('brands/get-by-category/{category}', [BrandController::class, 'getByCategory']);
Route::get('brands/view/{id}', [BrandController::class, 'show']);
Route::post('brands/create', [BrandController::class, 'create'])->middleware('auth:api', 'admin');
Route::put('brands/update/{id}', [BrandController::class, 'update'])->middleware('auth:api', 'admin');
Route::delete('brands/delete/{id}', [BrandController::class, 'delete'])->middleware('auth:api', 'admin');
//product categories
Route::get('categories/all', [CategoryController::class, 'allCategories']);
Route::get('categories', [CategoryController::class, 'categories']);
Route::get('categories/get-by-subcategory/{subcategory}', [CategoryController::class, 'getBySubcategory']);
Route::get('categories/get-by-slug/{slug}', [CategoryController::class, 'getBySlug']);
Route::get('categories/get-by-name/{name}', [CategoryController::class, 'getByName']);
Route::get('categories/view/{id}', [CategoryController::class, 'show']);
Route::get('categories/get-by-menu', [CategoryController::class, 'getByMenu']);
Route::get('categories/liquor-menu', [CategoryController::class, 'liquorMenu']);
Route::post('categories/create', [CategoryController::class, 'create'])->middleware('auth:api', 'admin');
Route::put('categories/update/{id}', [CategoryController::class, 'update'])->middleware('auth:api', 'admin');
Route::get('categories/delete/{id}', [CategoryController::class, 'delete'])->middleware('auth:api', 'admin');
//product quantities
Route::get('quantities', [QuantityController::class, 'quantities']);
Route::get('quantities/all', [QuantityController::class, 'allQuantities']);
Route::get('quantities/{id}', [QuantityController::class, 'show']);
Route::post('quantities/create', [QuantityController::class, 'create'])->middleware('auth:api', 'admin');
Route::put('quantities/{id}', [QuantityController::class, 'update'])->middleware('auth:api', 'admin');
Route::delete('quantities/{id}', [QuantityController::class, 'delete'])->middleware('auth:api', 'admin');
