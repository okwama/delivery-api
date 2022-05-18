<?php
use Illuminate\Support\Facades\Route;
// Blogs and Articles
use App\Http\Controllers\Blog\ArticleController;
use App\Http\Controllers\Blog\MetaController;


/**
 * Blogs and Articles Routes
 */

Route::get('articles/all', [ArticleController::class, 'all']);
Route::get('articles', [ArticleController::class, 'index']);
Route::get('articles/slug/{slug}', [ArticleController::class, 'article']);
Route::get('articles/view/{id}', [ArticleController::class, 'show']);
Route::post('articles/create', [ArticleController::class, 'create'])->middleware('auth:api', 'admin');
Route::put('articles/update/{id}', [ArticleController::class, 'update'])->middleware('auth:api', 'admin');
Route::delete('articles/delete/{id}', [ArticleController::class, 'delete'])->middleware('auth:api', 'admin');
//product categories
Route::get('metas', [MetaController::class, 'index']);
Route::get('metas/get-by-category/{category}', [MetaController::class, 'getByCategory']);
Route::get('metas/show/{id}', [MetaController::class, 'show']);
Route::post('metas/create', [MetaController::class, 'create'])->middleware('auth:api', 'admin');
Route::put('metas/update/{id}', [MetaController::class, 'update'])->middleware('auth:api', 'admin');
Route::delete('metas/delete/{id}', [MetaController::class, 'delete'])->middleware('auth:api', 'admin');
