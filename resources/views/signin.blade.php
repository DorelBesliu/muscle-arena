@extends('layouts.public')

@section('title', __('ui.sign_in_title') . ' - ' . __('ui.site_name'))

@push('meta')
    <meta name="description" content="{{ __('ui.sign_in_subtitle') }}">
    <link rel="canonical" href="{{ url()->current() }}">
@endpush

@section('content')
<main class="flex-1 flex items-center justify-center p-4 mt-18 min-h-[calc(100vh-72px)]">
    <div class="w-full max-w-md">
        <div class="bg-[#111111] border-2 border-[#333333] rounded-3xl p-8 md:p-10" x-data="{ showPassword: false, loading: false }">
            {{-- Header --}}
            <div class="text-center mb-8">
                <h1 class="text-4xl font-black text-white mb-2">
                    {{ __('ui.sign_in_title') }}
                </h1>
            </div>

            {{-- Session Status --}}
            @if (session('status'))
                <div class="mb-4 p-4 bg-green-500/10 border border-green-500/50 rounded-xl text-green-500 text-sm">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Form --}}
            <form method="POST" action="{{ route('login') }}" class="space-y-6" @submit="loading = true">
                @csrf

                {{-- Email --}}
                <div>
                    <label
                        for="email"
                        class="block text-sm font-medium text-white mb-2"
                    >
                        {{ __('ui.sign_in_email') }}
                    </label>
                    <div class="relative">
                        <x-lucide-mail class="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-[#CCCCCC]" />
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            class="w-full bg-[#000000] border-2 border-[#333333] rounded-xl py-3 pl-12 pr-4 text-white placeholder-[#666666] focus:border-[#333333] focus:outline-none focus:ring-0 @error('email') border-red-500 @enderror"
                            style="-webkit-tap-highlight-color: transparent;"
                            placeholder="your@email.com"
                            required
                            autofocus
                            autocomplete="username"
                        />
                    </div>
                    @error('email')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div>
                    <label
                        for="password"
                        class="block text-sm font-medium text-white mb-2"
                    >
                        {{ __('ui.sign_in_password') }}
                    </label>
                    <div class="relative">
                        <x-lucide-lock class="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-[#CCCCCC]" />
                        <input
                            :type="showPassword ? 'text' : 'password'"
                            id="password"
                            name="password"
                            class="w-full bg-[#000000] border-2 border-[#333333] rounded-xl py-3 pl-12 pr-12 text-white placeholder-[#666666] focus:border-[#333333] focus:outline-none focus:ring-0 @error('password') border-red-500 @enderror"
                            style="-webkit-tap-highlight-color: transparent;"
                            placeholder="••••••••"
                            required
                            autocomplete="current-password"
                        />
                        <button
                            type="button"
                            @click="showPassword = !showPassword"
                            class="absolute right-4 top-1/2 transform -translate-y-1/2 text-[#CCCCCC] hover:text-white transition-colors"
                        >
                            <x-lucide-eye-off x-show="showPassword" class="w-5 h-5" style="display: none;" />
                            <x-lucide-eye x-show="!showPassword" class="w-5 h-5" style="display: none;" />
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Remember Me & Forgot Password --}}
                <div class="flex items-center justify-between">
                    <label for="remember" class="flex items-center cursor-pointer">
                        <input
                            type="checkbox"
                            id="remember"
                            name="remember"
                            class="rounded border-[#333333] bg-[#000000] text-[#F97316] focus:ring-[#F97316] focus:ring-offset-0"
                        />
                        <span class="ms-2 text-sm text-[#CCCCCC]">{{ __('ui.sign_in_remember_me') }}</span>
                    </label>
                    @if (Route::has('password.request'))
                        <a
                            href="{{ route('password.request') }}"
                            class="text-sm text-[#F97316] hover:text-[#EF4444] transition-colors"
                        >
                            {{ __('ui.sign_in_forgot_password') }}
                        </a>
                    @endif
                </div>

                {{-- Submit Button --}}
                <button
                    type="submit"
                    :disabled="loading"
                    class="w-full bg-[#F97316] hover:bg-[#EF4444] disabled:opacity-80 disabled:cursor-not-allowed disabled:hover:scale-100 text-white font-bold py-4 rounded-xl transition-all hover:scale-105 shadow-xl flex items-center justify-center gap-2"
                >
                    <svg
                        x-show="loading"
                        class="animate-spin h-5 w-5 text-white"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        aria-hidden="true"
                        x-cloak
                        style="display: none;"
                    >
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span x-show="!loading">{{ __('ui.sign_in_button') }}</span>
                    <span x-show="loading" x-cloak style="display: none;">{{ __('ui.sign_in_loading') }}</span>
                </button>
            </form>

            {{-- Divider --}}
            <div class="relative my-8">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-[#333333]"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-4 bg-[#111111] text-[#CCCCCC]">
                        {{ __('ui.sign_in_or_continue_with') }}
                    </span>
                </div>
            </div>

            {{-- Social Login --}}
            <div class="space-y-4">
                <button
                    type="button"
                    class="w-full bg-[#000000] border-2 border-[#333333] hover:border-[#F97316] rounded-xl py-3 flex items-center justify-center gap-2 transition-colors group"
                >
                    <svg
                        class="w-5 h-5"
                        viewBox="0 0 24 24"
                        fill="currentColor"
                    >
                        <path
                            d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
                            fill="#4285F4"
                        />
                        <path
                            d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                            fill="#34A853"
                        />
                        <path
                            d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"
                            fill="#FBBC05"
                        />
                        <path
                            d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
                            fill="#EA4335"
                        />
                    </svg>
                    <span class="text-white text-sm font-medium">
                        {{ __('ui.sign_in_google') }}
                    </span>
                </button>
                <button
                    type="button"
                    class="w-full bg-[#000000] border-2 border-[#333333] hover:border-[#F97316] rounded-xl py-3 flex items-center justify-center gap-2 transition-colors group"
                >
                    <svg
                        class="w-5 h-5 text-[#1877F2]"
                        fill="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                    </svg>
                    <span class="text-white text-sm font-medium">
                        {{ __('ui.sign_in_facebook') }}
                    </span>
                </button>
            </div>

            {{-- Sign Up Link --}}
            <div class="mt-8 text-center">
                <p class="text-[#CCCCCC]">
                    {{ __('ui.sign_in_no_account') }}
                    @if (Route::has('register'))
                        <a
                            href="{{ route('register') }}"
                            class="text-[#F97316] hover:text-[#EF4444] font-medium transition-colors"
                        >
                            {{ __('ui.sign_in_sign_up') }}
                        </a>
                    @endif
                </p>
            </div>
        </div>
    </div>
</main>

{{-- Footer --}}
<footer class="bg-[#111111] border-t border-[#333333] py-8 mt-auto">
    <div class="container mx-auto px-4">
        <div class="flex flex-col md:flex-row items-center justify-between gap-4">
            <p class="text-sm text-[#CCCCCC]">
                {{ __('ui.footerText') }}
            </p>
            <div class="flex items-center gap-6">
                <a
                    href="{{ route('privacy', ['locale' => app()->getLocale()]) }}"
                    class="text-sm text-[#CCCCCC] hover:text-[#F97316] transition-colors underline underline-offset-2"
                >
                    {{ __('ui.privacyPolicy') }}
                </a>
                <a
                    href="{{ route('terms', ['locale' => app()->getLocale()]) }}"
                    class="text-sm text-[#CCCCCC] hover:text-[#F97316] transition-colors underline underline-offset-2"
                >
                    {{ __('ui.termsConditions') }}
                </a>
            </div>
        </div>
    </div>
</footer>
@endsection
