<?php

use App\Http\Controllers\API\FeedController;
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
Route::post('/feeds', [FeedController::class, 'store'])->name('feeds.store');
Route::get('/feeds/list', [FeedController::class, 'index'])->name('feeds.index');
Route::get('/feeds/{id}', [FeedController::class, 'show'])->name('feeds.show');
Route::put('/feeds/{id}/edit', [FeedController::class, 'edit'])->name('feeds.edit');
Route::delete('/feeds/{id}/delete', [FeedController::class, 'delete'])->name('feeds.delete');
Route::post('/feeds/fetch-go', [FeedController::class, 'fetchFromGolang'])->name('feeds.go-fetch');
