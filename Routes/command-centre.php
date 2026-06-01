<?php

use Illuminate\Support\Facades\Route;
use Modules\NexcoreClientManager\Http\Controllers\CommandCentreController;
use Modules\NexcoreClientManager\Http\Controllers\OtpController;

Route::get('/', [CommandCentreController::class, 'index'])->name('command-centre');

// OTP Verification Routes
Route::get('/otp', [OtpController::class, 'show'])->name('otp.show');
Route::post('/otp/verify', [OtpController::class, 'verify'])->name('otp.verify');
Route::post('/otp/resend', [OtpController::class, 'resend'])->name('otp.resend');
