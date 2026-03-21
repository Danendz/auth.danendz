<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::get('/auth/me', [UserController::class, 'me']);
    Route::put('/auth/me', [UserController::class, 'update']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
});

Route::post('/auth/refresh', [AuthController::class, 'refresh']);
