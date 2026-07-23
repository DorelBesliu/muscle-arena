<?php

use App\Http\Middleware\SetLocale;
use App\Livewire\Administrators;
use App\Livewire\Auth\ForceChangePassword;
use App\Livewire\Clients;
use App\Livewire\Dashboard;
use App\Livewire\Profile;
use App\Livewire\SiteContent\AboutProject;
use App\Livewire\SiteContent\ContactContent;
use App\Livewire\SiteContent\PrivacyContent;
use App\Livewire\SiteContent\RoadmapContent;
use App\Livewire\SiteContent\TermsContent;
use Illuminate\Support\Facades\Route;

// Redirect root to default locale (SEO: one canonical home URL)
Route::redirect('/', '/'.config('locales.default', 'ro'), 302);

// Redirect Fortify-style auth URLs to locale-prefixed routes (POST /login, POST /forgot-password stay with Fortify)
Route::get('login', function () {
    $locale = session('locale') ?: app()->getLocale() ?: config('locales.default', 'ro');
    $locale = in_array($locale, ['ro', 'en', 'ru'], true) ? $locale : config('locales.default', 'ro');
    return redirect()->route('signin', ['locale' => $locale], 302);
})->name('login')->middleware('guest');

Route::get('forgot-password', function () {
    $locale = session('locale') ?: app()->getLocale() ?: config('locales.default', 'ro');
    $locale = in_array($locale, ['ro', 'en', 'ru'], true) ? $locale : config('locales.default', 'ro');
    return redirect()->route('password.request', ['locale' => $locale], 302);
})->middleware('guest');

Route::post('proposals', \App\Http\Controllers\Proposal\StoreProposalController::class)
    ->name('proposals.store');

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

// Forgot password (locale-prefixed: /ro/forgot-password, /en/forgot-password, etc.)
Route::get('/{locale}/forgot-password', \App\Livewire\Auth\ForgotPassword::class)
    ->where('locale', 'ro|ru|en')
    ->middleware([SetLocale::class, 'guest'])
    ->name('password.request');

Route::get('password/change', ForceChangePassword::class)
    ->middleware(['auth'])
    ->name('password.change');

Route::get('dashboard', Dashboard::class)
    ->middleware(['auth', 'verified', 'ensure.password.changed'])
    ->name('dashboard');

Route::get('clients', Clients::class)
    ->middleware(['auth', 'ensure.password.changed'])
    ->name('clients');

Route::get('proposals', \App\Livewire\Proposals::class)
    ->middleware(['auth', 'ensure.password.changed'])
    ->name('proposals');

Route::get('administrators', Administrators::class)
    ->middleware(['auth', 'ensure.password.changed'])
    ->name('administrators');

Route::get('content/about', AboutProject::class)
    ->middleware(['auth', 'ensure.password.changed'])
    ->name('content.about');

Route::get('content/roadmap', RoadmapContent::class)
    ->middleware(['auth', 'ensure.password.changed'])
    ->name('content.roadmap');

Route::get('content/contact', ContactContent::class)
    ->middleware(['auth', 'ensure.password.changed'])
    ->name('content.contact');

Route::get('content/privacy', PrivacyContent::class)
    ->middleware(['auth', 'ensure.password.changed'])
    ->name('content.privacy');

Route::get('content/terms', TermsContent::class)
    ->middleware(['auth', 'ensure.password.changed'])
    ->name('content.terms');

Route::get('content/about/icon-dropdown-fragment', \App\Http\Controllers\Content\AboutIconDropdownFragmentController::class)
    ->middleware(['auth', 'ensure.password.changed'])
    ->name('content.about.icon-dropdown-fragment');

Route::get('clients/export', \App\Http\Controllers\Clients\ExportMembersController::class)
    ->middleware(['auth', 'ensure.password.changed'])
    ->name('clients.export');

Route::get('profile', Profile::class)
    ->middleware(['auth', 'ensure.password.changed'])
    ->name('profile');

Route::post('/user/locale', function (\Illuminate\Http\Request $request, \App\Actions\UpdateUserLocale $updateUserLocale) {
    $updateUserLocale(auth()->user(), $request);
    return redirect()->back();
})->middleware(['auth', 'web', 'ensure.password.changed'])->name('user.locale.update');


require __DIR__.'/auth.php';
