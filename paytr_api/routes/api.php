<?php

use App\Http\Controllers\Auth\ApiAuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FavouriteController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
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

Route::group(['middleware' => ['cors', 'json.response']], function () {

    // public routes
    Route::post('/login', [ApiAuthController::class, 'login'])->name('login.api');
    Route::post('/register',[ApiAuthController::class, 'register'])->name('register.api');

    Route::post('/category/list', [CategoryController::class, 'index'])->name('category.list.api');
    Route::post('/product/list', [ProductController::class, 'index'])->name('product.list.api');


});

Route::middleware('auth:api')->group(function () {
    // our routes to be protected will go in here
    Route::post('/logout', [ApiAuthController::class, 'logout'])->name('logout.api');

    Route::post('/category/create', [CategoryController::class, 'create'])->name('category.create.api');
    Route::post('/category/edit', [CategoryController::class, 'edit'])->name('category.edit.api');
    Route::post('/category/delete', [CategoryController::class, 'destroy'])->name('category.delete.api');

    Route::post('/product/create', [ProductController::class, 'create'])->name('product.create.api');
    Route::post('/product/edit', [ProductController::class, 'edit'])->name('product.edit.api');
    Route::post('/product/delete', [ProductController::class, 'delete'])->name('product.delete.api');

    Route::post('/favourite/add', [FavouriteController::class, 'addOrRemoveFromFavourites'])->name('favourite.addOrRemoveFromFavourites.api');
    Route::post('/favourite/list', [FavouriteController::class, 'index'])->name('favourite.list.api');

    Route::post('/cart/create', [CartController::class, 'create'])->name('cart.create.api');
    Route::post('/cart/list', [CartController::class, 'index'])->name('cart.list.api');
    Route::post('/cart/removeProduct', [CartController::class, 'removeProduct'])->name('cart.removeProduct.api');

});
