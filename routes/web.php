<?php

use App\Http\Controllers\Api\Auth\VerificationController;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect('api/documentation');
});

// testing endpoint for allowing user to take parameter values and use for API call
Route::get('email/verify/{id}/{hash}', [Controller::class, 'displayVerifySegments'])
    ->middleware(['throttle:6,1'])
    ->name('verification.verify');
