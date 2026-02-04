<?php

use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\Route;

// Redirect root to default locale (SEO: one canonical home URL)
Route::redirect('/', '/'.config('locales.default', 'ro'), 302);

// Locale-prefixed home (SEO-friendly: /ro, /ru, /en)
Route::get('/{locale}', fn () => view('home'))
    ->where('locale', 'ro|ru|en')
    ->middleware(SetLocale::class)
    ->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
