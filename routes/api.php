<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RegistrationController;
use App\Http\Controllers\Api\AttendanceController;
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

Route::post('/login', [RegistrationController::class, 'login']);
Route::get('/no-token', [RegistrationController::class, 'noTokenFound']);

Route::middleware('auth:api')->group(function () {
    Route::get('/logout', [RegistrationController::class, 'logout']);

    Route::get('/profile', [RegistrationController::class, 'profile']);
    Route::post('/update/profile', [RegistrationController::class, 'updateProfile']);
    Route::post('/change/password', [RegistrationController::class, 'changePassword']);
    
    Route::get('/status', [AttendanceController::class, 'status']);
    Route::get('/check/in', [AttendanceController::class, 'checkIn']);
    Route::get('/check/out', [AttendanceController::class, 'checkOut']);
    
});
