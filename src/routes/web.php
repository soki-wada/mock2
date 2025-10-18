<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\AttendanceController;

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/', [UserController::class, 'assign']);
Route::get('/admin/login', [UserController::class, 'showAdminLogin']);
Route::post('/register', [UserController::class, 'store']);
Route::post('/login', [UserController::class, 'login']);

Route::middleware('isAdmin')->group(function(){
        Route::get('/admin/users', [AdminController::class, 'showUsers']);
        Route::get('admin/attendances', [AdminController::class, 'showAdminList']);
        Route::get('admin/attendances/{id}', [AdminController::class, 'showAdminDetail']);
        Route::post('/admin/attendances/{id}', [AdminController::class, 'updateWorkTime']);
        Route::get('/admin/users/{user}/attendances', [AdminController::class, 'showAdminIndex']);
        Route::post('/export', [AdminController::class, 'export']);
        Route::get('admin/requests', [RequestController::class, 'showAdminRequests']);
        Route::get('/admin/requests/{id}', [RequestController::class, 'showAdminRequestsApproval']);
        Route::post('/admin/requests/{id}', [RequestController::class, 'requestApprove']);
        Route::post('/admin/logout', [AdminController::class, 'logout']);
});

Route::middleware(['auth', 'isUser'])->group(function () {
        Route::get('/email/verify', [UserController::class, 'emailAuth'])->name('verification.notice');
        Route::get('/email/verify/{id}/{hash}', [UserController::class, 'verify'])->middleware(['auth', 'signed'])->name('verification.verify');
        Route::post('/email/verification-notification', [UserController::class, 'resend'])->middleware(['auth', 'throttle:6,1'])->name('verification.send');
        Route::get('attendance/list', [AttendanceController::class, 'showList']);
        Route::get('attendance/detail/{id}', [AttendanceController::class, 'showDetail']);
        Route::post('attendance/detail/{id}', [RequestController::class, 'modificationRequest']);
        Route::get('/stamp_correction_request/list', [RequestController::class, 'showRequest']);
        Route::get('/attendance', [AttendanceController::class, 'showAttendance']);
        Route::post('/attendance', [AttendanceController::class, 'registerAttendance']);
});