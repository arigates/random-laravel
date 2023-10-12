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

Route::get('/products/datatable', [ProductController::class, 'datatable'])
    ->name('products.data')
    ->middleware('auth');
Route::resource('products', ProductController::class)->middleware('auth');

Route::get('/activities/datatable', [ActivityController::class, 'datatable'])
    ->name('activities.data')
    ->middleware('auth');
Route::resource('activities', ActivityController::class)
    ->except(['show', 'update'])
    ->middleware('auth');
Route::post('/activities/{activity}/update', [ActivityController::class, 'update'])
    ->name('activities.update')
    ->middleware('auth');
