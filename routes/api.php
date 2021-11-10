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
Route::post('/feeds', [FeedController::class, 'store']);
Route::get('/feeds/list', [FeedController::class, 'index']);
Route::get('/feeds/{id}', [FeedController::class, 'show']);
Route::put('/feeds/{id}/edit', [FeedController::class, 'edit']);
Route::delete('/feeds/{id}/delete', [FeedController::class, 'delete']);
