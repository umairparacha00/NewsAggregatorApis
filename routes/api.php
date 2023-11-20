<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SourceController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/articles', ArticleController::class);

// api route to get all the categories for filter
Route::get('/categories', CategoryController::class);

// api route to get all the authors for filter
Route::get('/authors', AuthorController::class);

// api route to get all the authors for filter
Route::get('/sources', SourceController::class);
