<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/', '/api/documentation');

use App\Models\EmailLog;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SsoController;

// Endpoint untuk login/redirect
Route::get('auth/{provider}', [SsoController::class, 'redirectToProvider'])->name('sso.redirect');
Route::get('auth/{provider}/callback', [SsoController::class, 'handleProviderCallback'])->name('sso.callback');

// Endpoint yang butuh autentikasi (untuk melepas SSO)
Route::middleware('auth')->group(function () {
    Route::delete('auth/{provider}/unlink', [SsoController::class, 'unlinkProvider'])->name('sso.unlink');
});

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

    Route::resource('roles', \App\Http\Controllers\RoleController::class)->except(['show']);
    Route::resource('permissions', \App\Http\Controllers\PermissionController::class)->except(['show']);
    Route::resource('sso-providers', \App\Http\Controllers\SsoProviderController::class)->except(['show']);

    // API Management CRUD (Services, Keys, Logs)
    Route::resource('services', \App\Http\Controllers\ServiceController::class)->except(['show']);
    Route::resource('api-keys', \App\Http\Controllers\ApiKeyController::class)->except(['show']);
    Route::get('/api-logs', [\App\Http\Controllers\ApiLogController::class, 'index'])->name('api-logs.index');

    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password.update');
});
