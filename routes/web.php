<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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
Auth::routes([
    'register' => false
]);

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/products/datatable', [ProductController::class, 'datatable'])->name('products.data');
Route::resource('products', ProductController::class)->middleware('auth');
Route::resource('activities', ActivityController::class)->middleware('auth');
