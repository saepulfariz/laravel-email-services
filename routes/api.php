<?php

// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmailServiceController;
use App\Http\Controllers\QueueEmailServiceController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('/email-services', [EmailServiceController::class, 'send']);
Route::post('/q/email-services', [QueueEmailServiceController::class, 'send']);
