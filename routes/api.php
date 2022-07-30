<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    HomeController,
    BookController,
    AuthorController,
    UserController
};
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/author/search-authors', [AuthorController::class, 'find_author_by_name'])->name('author.findByName');
Route::resource('/book', BookController::class)->only(['show', 'store', 'update','destroy']);
Route::resource('/author', AuthorController::class)->only(['show', 'store','update', 'destroy']);
Route::resource('/users', UserController::class)->only([ 'store', 'destroy']);
