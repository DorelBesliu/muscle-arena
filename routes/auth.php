<?php

use App\Livewire\Auth\ConfirmPassword;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\ResetPassword;
use App\Livewire\Auth\VerifyEmail;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\TwoFactorAuthenticatedSessionController;

Route::middleware('guest')->group(function () {
    Route::get('register', Register::class)->name('register');
    Route::get('reset-password/{token}', ResetPassword::class)->name('password.reset');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', VerifyEmail::class)->name('verification.notice');
    Route::get('confirm-password', ConfirmPassword::class)->name('password.confirm');
});

Route::get('/two-factor-challenge', [TwoFactorAuthenticatedSessionController::class, 'create'])
    ->middleware(['guest:'.config('fortify.guard')])
    ->name('two-factor.login');
