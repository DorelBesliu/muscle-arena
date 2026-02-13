@php
    $locale = app()->getLocale();
    $supportedLocales = config('locales.supported', ['ro', 'ru', 'en']);
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', $locale) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', __('ui.meta_title'))</title>

        {{-- Favicon --}}
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
        <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">

        {{-- PWA Manifest --}}
        <link rel="manifest" href="{{ asset('manifest.json') }}">
        <meta name="theme-color" content="#F97316">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <meta name="apple-mobile-web-app-title" content="Muscle Arena 3D">

        @stack('meta')

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            .font-arena { font-family: 'Oswald', sans-serif; }
        </style>
        @stack('styles')
    </head>
    <body class="antialiased bg-[#0c0c0c] text-white min-h-screen font-sans" x-cloak data-layout="public">
        {{-- Topbar --}}
        <nav class="bg-[#000000]/95 backdrop-blur-md border-b border-[#333333] fixed top-0 left-0 right-0 z-[70]" aria-label="Principal">
            <div class="container mx-auto px-4">
                <div class="flex items-center justify-between h-[72px]">
                    <a href="{{ route('home', ['locale' => $locale]) }}" class="flex items-center gap-2 md:gap-3 hover:opacity-80 transition-opacity">
                        <div class="w-10 h-10 md:w-14 md:h-14 rounded-xl flex items-center justify-center flex-shrink-0 overflow-hidden">
                            <img src="{{ asset('images/logo-white.svg') }}" alt="{{ __('ui.site_name') }} Logo" class="w-full h-full object-contain" />
                        </div>
                        <div>
                            <h1 class="text-[16px] md:text-[20px] font-bold text-white tracking-tight">{{ __('ui.site_name') }}</h1>
                            <div class="flex items-center gap-1 text-[11px] md:text-sm text-[#CCCCCC]">
                                <x-lucide-map-pin class="w-2.5 h-2.5 md:w-3 md:h-3 text-[rgb(249,115,22)] shrink-0" aria-hidden="true" />
                                <span class="text-[rgb(249,115,22)]">{{ __('ui.location') }}</span>
                            </div>
                        </div>
                    </a>

                    <div class="hidden lg:flex items-center gap-1" x-data>
                        <a href="{{ route('home', ['locale' => $locale]) }}#hero" data-scroll-section :class="$store.public.activeSection === 'hero' ? 'text-white bg-[#333333]' : 'text-[#CCCCCC] hover:text-white hover:bg-[#111111]'" class="px-4 py-2 text-sm font-medium rounded-lg transition-all">{{ __('ui.menu_home') }}</a>
                        <a href="{{ route('home', ['locale' => $locale]) }}#about" data-scroll-section :class="$store.public.activeSection === 'about' ? 'text-white bg-[#333333]' : 'text-[#CCCCCC] hover:text-white hover:bg-[#111111]'" class="px-4 py-2 text-sm font-medium rounded-lg transition-all">{{ __('ui.menu_about') }}</a>
                        <a href="{{ route('home', ['locale' => $locale]) }}#timeline" data-scroll-section :class="$store.public.activeSection === 'timeline' ? 'text-white bg-[#333333]' : 'text-[#CCCCCC] hover:text-white hover:bg-[#111111]'" class="px-4 py-2 text-sm font-medium rounded-lg transition-all">{{ __('ui.menu_timeline') }}</a>
                        <a href="{{ route('home', ['locale' => $locale]) }}#contact" data-scroll-section :class="$store.public.activeSection === 'contact' ? 'text-white bg-[#333333]' : 'text-[#CCCCCC] hover:text-white hover:bg-[#111111]'" class="px-4 py-2 text-sm font-medium rounded-lg transition-all">{{ __('ui.menu_contact') }}</a>
                    </div>

                    <div class="flex items-center gap-2 md:gap-4" x-data>
                        {{-- Language dropdown (mobile) – Alpine.js --}}
                        <div class="relative md:hidden" @click.away="$store.public.langOpen = false">
                            <button type="button" @click="$store.public.langOpen = !$store.public.langOpen" class="flex items-center gap-1 bg-[#111111] border border-[#333333] rounded-full px-3 py-1.5 text-xs font-medium text-white cursor-pointer">
                                {{ strtoupper($locale) }} <x-lucide-chevron-down class="w-3 h-3 shrink-0" />
                            </button>
                            <div x-show="$store.public.langOpen" x-transition class="absolute right-0 top-full mt-2 bg-[#111111] border border-[#333333] rounded-lg shadow-lg overflow-hidden min-w-[80px] z-50" style="display: none;">
                                @foreach ($supportedLocales as $loc)
                                    <a href="{{ route('home', ['locale' => $loc]) }}" class="block w-full px-4 py-2 text-left text-xs font-medium {{ $locale === $loc ? 'bg-[#F97316] text-white' : 'text-[#CCCCCC] hover:bg-[#333333] hover:text-white transition-colors' }}">{{ strtoupper($loc) }}</a>
                                @endforeach
                            </div>
                        </div>

                        @if (Route::has('signin'))
                            @auth
                                <a href="{{ url('/dashboard') }}" class="hidden md:flex text-[#CCCCCC] hover:text-white transition-colors items-center gap-2 text-sm font-medium">
                                    <x-lucide-user class="w-4 h-4 shrink-0" />
                                    <span class="hidden md:inline">{{ __('ui.dashboard') }}</span>
                                </a>
                            @else
                                <a href="{{ route('signin', ['locale' => $locale]) }}" class="hidden md:flex text-[#CCCCCC] hover:text-white transition-colors items-center gap-2 text-sm font-medium">
                                    <x-lucide-log-in class="w-4 h-4 shrink-0" />
                                    <span class="hidden md:inline">{{ __('ui.sign_in') }}</span>
                                </a>
                            @endauth
                        @endif

                        <div class="hidden md:flex items-center gap-1 bg-[#111111] border border-[#333333] rounded-full p-1">
                            @foreach ($supportedLocales as $loc)
                                @if ($locale === $loc)
                                    <span class="px-3 py-1.5 rounded-full text-xs font-medium bg-[#F97316] text-white">{{ strtoupper($loc) }}</span>
                                @else
                                    <a href="{{ route('home', ['locale' => $loc]) }}" class="px-3 py-1.5 rounded-full text-xs font-medium text-[#CCCCCC] hover:text-white transition-all">{{ strtoupper($loc) }}</a>
                                @endif
                            @endforeach
                        </div>

                        {{-- Hamburger menu button (mobile only) – Alpine.js --}}
                        <div class="lg:hidden" data-mobile-menu>
                            <button type="button" @click="$store.public.mobileMenuOpen = !$store.public.mobileMenuOpen" class="lg:hidden text-[#CCCCCC] hover:text-white transition-colors p-2" aria-label="{{ __('ui.menu_label') }}">
                                <span x-show="!$store.public.mobileMenuOpen"><x-lucide-menu class="w-6 h-6 shrink-0" /></span>
                                <span x-show="$store.public.mobileMenuOpen" style="display: none;"><x-lucide-x class="w-6 h-6 shrink-0" /></span>
                            </button>
                            <div x-show="$store.public.mobileMenuOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed top-[72px] left-0 right-0 bg-[#111111] border-b border-[#333333] shadow-xl z-40 lg:hidden" style="display: none;">
                                <div class="container mx-auto px-4 py-4">
                                    <div class="flex flex-col gap-2">
                                        <button type="button" @click="$store.public.mobileMenuOpen = false; setTimeout(() => window.scrollToSection('hero'), 100)" :class="$store.public.activeSection === 'hero' ? 'text-white bg-[#333333]' : 'text-[#CCCCCC] hover:text-white hover:bg-[#333333]'" class="w-full px-4 py-3 text-left text-base font-medium rounded-lg transition-all">
                                            {{ __('ui.menu_home') }}
                                        </button>
                                        <button type="button" @click="$store.public.mobileMenuOpen = false; setTimeout(() => window.scrollToSection('about'), 100)" :class="$store.public.activeSection === 'about' ? 'text-white bg-[#333333]' : 'text-[#CCCCCC] hover:text-white hover:bg-[#333333]'" class="w-full px-4 py-3 text-left text-base font-medium rounded-lg transition-all">
                                            {{ __('ui.menu_about') }}
                                        </button>
                                        <button type="button" @click="$store.public.mobileMenuOpen = false; setTimeout(() => window.scrollToSection('timeline'), 100)" :class="$store.public.activeSection === 'timeline' ? 'text-white bg-[#333333]' : 'text-[#CCCCCC] hover:text-white hover:bg-[#333333]'" class="w-full px-4 py-3 text-left text-base font-medium rounded-lg transition-all">
                                            {{ __('ui.menu_timeline') }}
                                        </button>
                                        <button type="button" @click="$store.public.mobileMenuOpen = false; setTimeout(() => window.scrollToSection('contact'), 100)" :class="$store.public.activeSection === 'contact' ? 'text-white bg-[#333333]' : 'text-[#CCCCCC] hover:text-white hover:bg-[#333333]'" class="w-full px-4 py-3 text-left text-base font-medium rounded-lg transition-all">
                                            {{ __('ui.menu_contact') }}
                                        </button>
                                        <div class="h-px bg-[#333333] my-2"></div>
                                        @guest
                                            <a href="{{ route('signin', ['locale' => $locale]) }}" @click="$store.public.mobileMenuOpen = false" class="w-full px-4 py-3 text-left text-base font-medium text-[#F97316] hover:text-white hover:bg-[#333333] rounded-lg transition-all flex items-center gap-3">
                                                <x-lucide-log-in class="w-5 h-5 shrink-0" />
                                                {{ __('ui.sign_in') }}
                                            </a>
                                        @else
                                            <a href="{{ url('/dashboard') }}" @click="$store.public.mobileMenuOpen = false" class="w-full px-4 py-3 text-left text-base font-medium text-[#F97316] hover:text-white hover:bg-[#333333] rounded-lg transition-all flex items-center gap-3">
                                                <x-lucide-user class="w-5 h-5 shrink-0" />
                                                {{ __('ui.dashboard') }}
                                            </a>
                                        @endguest
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        @yield('content')

        @stack('scripts')
    </body>
</html>
