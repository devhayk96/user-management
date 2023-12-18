<?php

use App\Http\Controllers\Api\Auth\ForgotPasswordController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\ResetPasswordController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('guest')->group(function () {
    Route::post('login', [LoginController::class, 'login']);
    Route::post('register', RegisterController::class);

    Route::prefix('password')->group(function () {
        Route::post('forgot', ForgotPasswordController::class);
        Route::post('reset', ResetPasswordController::class)->name('password.reset');
    });

});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [LoginController::class, 'logout']);

    Route::get('user', [UserController::class, 'getProfile']);
});
