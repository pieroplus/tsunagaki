<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('auth')->group(function () {
    Route::post('signup', [AuthController::class, 'signup']);
    Route::post('signin', [AuthController::class, 'signin']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('signout', [AuthController::class, 'signout']);
});
