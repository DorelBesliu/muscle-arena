<?php

use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

// Redirect root to default locale (SEO: one canonical home URL)
Route::redirect('/', '/'.config('locales.default', 'ro'), 302);

// Locale-prefixed home (SEO-friendly: /ro, /ru, /en)
Route::get('/{locale}', fn () => view('home'))
    ->where('locale', 'ro|ru|en')
    ->middleware(SetLocale::class)
    ->name('home');

// Privacy Policy page
Route::get('/{locale}/privacy', fn () => view('privacy'))
    ->where('locale', 'ro|ru|en')
    ->middleware(SetLocale::class)
    ->name('privacy');

// Terms & Conditions page
Route::get('/{locale}/terms', fn () => view('terms'))
    ->where('locale', 'ro|ru|en')
    ->middleware(SetLocale::class)
    ->name('terms');

// Sign In page (locale-prefixed)
Route::get('/{locale}/signin', fn () => view('signin'))
    ->where('locale', 'ro|ru|en')
    ->middleware([SetLocale::class, 'guest'])
    ->name('signin');

Volt::route('dashboard', 'pages.dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Volt::route('clients', 'pages.clients')
    ->middleware(['auth'])
    ->name('clients');

Volt::route('profile', 'pages.profile')
    ->middleware(['auth'])
    ->name('profile');

Route::post('/user/locale', function (\Illuminate\Http\Request $request, \App\Actions\UpdateUserLocale $updateUserLocale) {
    $updateUserLocale(auth()->user(), $request);
    return redirect()->back();
})->middleware(['auth', 'web'])->name('user.locale.update');

require __DIR__.'/auth.php';
