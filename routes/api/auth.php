<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Auth\AuthController;

Route::post('/signin', [AuthController::class, 'signin']);

Route::post('/signup', [AuthController::class, 'signup']);

Route::middleware('auth:api')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/signout', [AuthController::class, 'signout']);
});
