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

Volt::route('administrators', 'pages.administrators')
    ->middleware(['auth'])
    ->name('administrators');

Volt::route('content/about', 'pages.site-content.about-project')
    ->middleware(['auth'])
    ->name('content.about');

Volt::route('content/roadmap', 'pages.site-content.roadmap')
    ->middleware(['auth'])
    ->name('content.roadmap');

Volt::route('content/contact', 'pages.site-content.contact')
    ->middleware(['auth'])
    ->name('content.contact');

Volt::route('content/privacy', 'pages.site-content.privacy')
    ->middleware(['auth'])
    ->name('content.privacy');

Volt::route('content/terms', 'pages.site-content.terms')
    ->middleware(['auth'])
    ->name('content.terms');

Route::get('content/about/icon-dropdown-fragment', function (\Illuminate\Http\Request $request) {
    $iconsConfig = config('icons', []);
    // Etichetele iconițelor în limba setată în profil (nu din query)
    $locale = $request->user()?->locale ?? config('locales.default', 'ro');

    app('log')->info('locale', ['locale' => $locale]);

    $icons = [];
    foreach ($iconsConfig as $key => $defaultLabel) {
        if ($key === 'default') continue;

        $label = __('icons.' . $key);
        if ($label === 'icons.' . $key) {
            $label = $defaultLabel;
        }
        $icons[$key] = $label;
    }
    return response()->view('components.about-feature-icon-list-fragment', [
        'icons' => $icons,
    ]);
})->middleware(['auth'])->name('content.about.icon-dropdown-fragment');

Route::get('clients/export', function (\Illuminate\Http\Request $request) {
    $query = \App\Models\Member::query();
    $search = $request->string('q')->trim()->toString();
    if ($search !== '') {
        $searchLower = strtolower($search);
        $query->where(function ($q) use ($searchLower, $search) {
            $q->whereRaw('LOWER(first_name) LIKE ?', ['%' . $searchLower . '%'])
                ->orWhereRaw('LOWER(last_name) LIKE ?', ['%' . $searchLower . '%'])
                ->orWhereRaw('LOWER(email) LIKE ?', ['%' . $searchLower . '%'])
                ->orWhere('phone', 'like', '%' . $search . '%');
        });
    }
    $statusFilter = $request->string('status')->toString();
    if (in_array($statusFilter, ['active', 'inactive'], true)) {
        $query->where('status', $statusFilter);
    }
    $sortBy = $request->string('sort')->toString();
    $sortDir = $request->string('dir')->toString() === 'asc' ? 'asc' : 'desc';
    $orderDir = $sortDir === 'asc' ? 'asc' : 'desc';
    match ($sortBy) {
        'name' => $query->orderBy('last_name', $orderDir)->orderBy('first_name', $orderDir),
        'expiry' => $query->orderBy('subscription_expiry', $orderDir === 'asc' ? 'asc' : 'desc'),
        default => $query->orderBy('created_at', $orderDir),
    };
    $members = $query->get();
    $headers = ['Name', 'Email', 'Phone', 'Status', 'Registration Date', 'Subscription Expiry'];
    $rows = $members->map(fn (\App\Models\Member $m) => [
        $m->full_name,
        $m->email,
        $m->phone,
        $m->status ?? 'active',
        $m->created_at->format('Y-m-d'),
        $m->subscription_expiry ? $m->subscription_expiry->format('Y-m-d') : '',
    ]);
    $csv = implode("\n", [implode(',', $headers), ...$rows->map(fn ($row) => implode(',', $row))]);
    return response()->streamDownload(
        fn () => print($csv),
        'members-' . now()->format('Y-m-d') . '.csv',
        ['Content-Type' => 'text/csv; charset=UTF-8']
    );
})->middleware(['auth'])->name('clients.export');

Volt::route('profile', 'pages.profile')
    ->middleware(['auth'])
    ->name('profile');

Route::post('/user/locale', function (\Illuminate\Http\Request $request, \App\Actions\UpdateUserLocale $updateUserLocale) {
    $updateUserLocale(auth()->user(), $request);
    return redirect()->back();
})->middleware(['auth', 'web'])->name('user.locale.update');

require __DIR__.'/auth.php';
