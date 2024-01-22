<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AttendanceController;
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

Route::get('/', [RegistrationController::class, 'index']);
Route::post('/login', [RegistrationController::class, 'login']);

Route::group(['middleware' => 'admin'], function () {

    Route::get('/profile/personal', [RegistrationController::class, 'profile']);
    Route::post('profile/password/update', [RegistrationController::class, 'updatePassword']);
    Route::get('logout', [RegistrationController::class, 'logout']);
    Route::get('dashboard', [DashboardController::class, 'index']);

    // Employees
    Route::get('employees', [EmployeeController::class, 'index']);
    Route::post('register/employee', [EmployeeController::class, 'create']);
    Route::get('get/employee/{id}', [EmployeeController::class, 'get']);
    Route::post('update/employee', [EmployeeController::class, 'update']);
    Route::get('delete/employee/{id}', [EmployeeController::class, 'delete']);
    Route::get('approve/employee/{id}', [EmployeeController::class, 'approve']);
    Route::post('change/password', [EmployeeController::class, 'changePassword']);
    // Attendance
    Route::get('all/attendance', [AttendanceController::class, 'index']);
    Route::get('live/location/{device_id}/{user_id}', [EmployeeController::class, 'live']);
    Route::get('playback/index/{service_id}/{user_id}', [EmployeeController::class, 'playbackIndex']);
    Route::get('playback/history/{id}/{from}/{to}', [EmployeeController::class, 'playback']);
    // Reports 
    Route::get('reports/attendance', [AttendanceController::class, 'attendanceReport']);
    Route::get('employee/attendance/{user_id}/{from}/{to}', [AttendanceController::class, 'employee_report']);
});