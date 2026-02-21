<?php

// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmailServiceController;
use App\Http\Controllers\QueueEmailServiceController;
use App\Http\Controllers\TestController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
Route::get('/test', [TestController::class, 'index']);

Route::post('/email-services', [QueueEmailServiceController::class, 'send']);
Route::post('/d/email-services', [EmailServiceController::class, 'send']);
Route::post('/q/email-services', [QueueEmailServiceController::class, 'send']);
