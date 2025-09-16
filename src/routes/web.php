<?php

use App\Http\Controllers\UserController;
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

Route::get('/admin/login', [UserController::class, 'showAdminLogin']);

// ミドルウェア予定
        Route::get('/email/verify', [UserController::class, 'emailAuth'])->name('verification.notice');
        Route::get('/email/verify/{id}/{hash}', [UserController::class, 'verify'])->middleware(['auth', 'signed'])->name('verification.verify');
        Route::post('/email/verification-notification', [UserController::class, 'resend'])->middleware(['auth', 'throttle:6,1'])->name('verification.send');
        Route::get('attendance/list', [UserController::class, 'showList']);
        Route::get('attendance/detail/{id}', [UserController::class, 'showDetail']);
        Route::post('attendance/detail/{id}', [UserController::class, 'modificationRequest']);
        Route::get('/stamp_correction_request/list', [UserController::class, 'showRequest']);
        Route::get('/attendance', [UserController::class, 'showAttendance']);
        Route::post('/attendance', [UserController::class, 'registerAttendance']);
        Route::get('/admin/users', [UserController::class, 'showUsers']);