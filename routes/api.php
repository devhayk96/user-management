<?php

use App\Http\Controllers\Api\Auth\ForgotPasswordController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\ResetPasswordController;
use App\Http\Controllers\Api\Auth\VerificationController;
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

Route::middleware('guest')->group(function ($route) {
    $route->post('login', [LoginController::class, 'login']);
    $route->post('register', RegisterController::class);

    $route->prefix('password')->group(function () use ($route) {
        $route->post('forgot', ForgotPasswordController::class);
        $route->post('reset', ResetPasswordController::class)->name('password.reset');
    });
});

Route::middleware('auth:sanctum')->group(function ($route) {
    $route->post('logout', [LoginController::class, 'logout']);

    $route->get('user', [UserController::class, 'getProfile']);

    $route->post('email/verification-resend', [VerificationController::class, 'resendNotification'])
        ->name('verification.send');
});
