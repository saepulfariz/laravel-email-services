<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/', '/api/documentation');

use App\Models\EmailLog;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        $logs = EmailLog::orderBy('created_at', 'desc')->take(10)->get();
        return view('dashboard', compact('logs'));
    });
    
    Route::get('users/export', [UserController::class, 'export'])->name('users.export');
    Route::resource('users', UserController::class)->except(['show']);
});
