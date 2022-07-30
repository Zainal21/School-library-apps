<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    HomeController,
    BookController,
    AuthorController,
    UserController,
};
use App\Http\Controllers\Auth\ForgotPasswordController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes(['password.reset' => false]);

Route::get('forget-password', [ForgotPasswordController::class, 'showForgetPasswordForm'])->name('forget.password.get');
Route::post('forget-password', [ForgotPasswordController::class, 'submitForgetPasswordForm'])->name('forget.password.post'); 
Route::get('reset-password/{token}/{email}', [ForgotPasswordController::class, 'showResetPasswordForm'])->name('reset.password.get');
Route::post('reset-password', [ForgotPasswordController::class, 'submitResetPasswordForm'])->name('reset.password.post');

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::group(['middleware' => 'auth'], function(){
    Route::get('/book', [BookController::class, 'index'])->name('book.index');
    Route::get('/author', [AuthorController::class, 'index'])->name('author.index');
    Route::get('/book/get-books', [BookController::class, 'get_books'])->name('book.get_book');
    Route::get('/author/get-authors', [AuthorController::class, 'get_authors'])->name('author.get_author');
    Route::group(['middleware' => 'only_admin'], function(){
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/get-users', [UserController::class, 'get_users'])->name('users.get_users');
    });
});
