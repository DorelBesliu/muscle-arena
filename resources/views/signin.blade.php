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
                            class="w-full bg-[#000000] border-2 border-[#333333] rounded-xl py-3 pl-12 pr-4 text-white placeholder-[#666666] focus:border-[#333333] focus:outline-none focus:ring-0"
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
                            class="w-full bg-[#000000] border-2 border-[#333333] rounded-xl py-3 pl-12 pr-12 text-white placeholder-[#666666] focus:border-[#333333] focus:outline-none focus:ring-0"
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
