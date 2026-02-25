@php
    $locale = app()->getLocale();
    $isDashboard = request()->routeIs('dashboard');
    $isClients = request()->routeIs('clients');
    $isProfile = request()->routeIs('profile');
    $isAdministrators = request()->routeIs('administrators');
    $isContentAbout = request()->routeIs('content.about');
    $isContentRoadmap = request()->routeIs('content.roadmap');
    $isContentContact = request()->routeIs('content.contact');
    $isContentPrivacy = request()->routeIs('content.privacy');
    $isContentTerms = request()->routeIs('content.terms');
    $isContentActive = $isContentAbout || $isContentRoadmap || $isContentContact || $isContentPrivacy || $isContentTerms;
    $isAdmin = auth()->user()?->role === 'admin';
@endphp

{{-- Desktop Sidebar --}}
<aside class="hidden md:flex flex-col w-[270px] bg-[#111111] border-r border-[#333333] shrink-0">
    {{-- Logo + company name (link to home); user name not clickable --}}
    <div class="p-4 border-b border-[#333333] flex items-center gap-3">
        <a
            href="{{ route('home', ['locale' => $locale]) }}"
            class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 overflow-hidden hover:opacity-90 transition-opacity"
            aria-label="{{ __('ui.site_name') }}"
        >
            <img
                src="{{ asset('images/logo-white.svg') }}"
                alt="{{ __('ui.site_name') }} Logo"
                class="w-full h-full object-contain"
            />
        </a>
        <div class="min-w-0">
            <a
                href="{{ route('home', ['locale' => $locale]) }}"
                class="text-lg font-black text-white block truncate hover:underline"
            >
                {{ __('ui.site_name') }}
            </a>
            <a href="{{ route('profile') }}" wire:navigate class="text-xs text-[#CCCCCC] hover:underline block truncate">{{ auth()->user()?->name }}</a>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 p-3 space-y-1 overflow-y-auto">
        {{-- Dashboard --}}
        <a
            href="{{ route('dashboard') }}"
            wire:navigate
            class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-all {{ $isDashboard ? 'bg-[#F97316] text-white' : 'text-[#CCCCCC] hover:bg-[#333333] hover:text-white' }}"
        >
            <x-lucide-gauge class="w-4 h-4 shrink-0" />
            <span class="font-medium">{{ __('ui.dashboard') }}</span>
        </a>

        {{-- Clients --}}
        <a
            href="{{ route('clients') }}"
            wire:navigate
            class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-all {{ $isClients ? 'bg-[#F97316] text-white' : 'text-[#CCCCCC] hover:bg-[#333333] hover:text-white' }}"
        >
            <x-lucide-users class="w-4 h-4 shrink-0" />
            <span class="font-medium">{{ __('ui.sidebar_clients') }}</span>
        </a>

        @if($isAdmin)
        {{-- Administrators --}}
        <a
            href="{{ route('administrators') }}"
            wire:navigate
            class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-all {{ $isAdministrators ? 'bg-[#F97316] text-white' : 'text-[#CCCCCC] hover:bg-[#333333] hover:text-white' }}"
        >
            <x-lucide-shield class="w-4 h-4 shrink-0" />
            <span class="font-medium">{{ __('ui.sidebar_administrators') }}</span>
        </a>
        @endif

        {{-- Site Content – Expandable --}}
        <div x-data="{ contentExpanded: {{ $isContentActive ? 'true' : 'false' }} }">
            <button
                type="button"
                @click="contentExpanded = !contentExpanded"
                class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-all {{ $isContentActive ? 'bg-[#F97316] text-white' : 'text-[#CCCCCC] hover:bg-[#333333] hover:text-white' }}"
            >
                <x-lucide-file-text class="w-4 h-4 shrink-0" />
                <span class="font-medium flex-1 text-left">{{ __('ui.sidebar_content_site') }}</span>
                <span x-show="contentExpanded" class="shrink-0"><x-lucide-chevron-down class="w-4 h-4" /></span>
                <span x-show="!contentExpanded" class="shrink-0" x-cloak style="display: none;"><x-lucide-chevron-right class="w-4 h-4" /></span>
            </button>

            <div x-show="contentExpanded" class="mt-1 ml-4 space-y-1 pl-2 border-l border-[#333333]">
                <a
                    href="{{ route('content.about') }}"
                    wire:navigate
                    class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-all {{ $isContentAbout ? 'bg-[#333333] text-white' : 'text-[#CCCCCC] hover:bg-[#222222] hover:text-white' }}"
                >
                    <x-lucide-info class="w-3.5 h-3.5 shrink-0" />
                    <span class="font-medium text-xs">{{ __('ui.sidebar_content_about') }}</span>
                </a>
                <a
                    href="{{ route('content.roadmap') }}"
                    wire:navigate
                    class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-all {{ $isContentRoadmap ? 'bg-[#333333] text-white' : 'text-[#CCCCCC] hover:bg-[#222222] hover:text-white' }}"
                >
                    <x-lucide-line-squiggle class="w-3.5 h-3.5 shrink-0" />
                    <span class="font-medium text-xs">{{ __('ui.sidebar_content_roadmap') }}</span>
                </a>
                <a
                    href="{{ route('content.contact') }}"
                    wire:navigate
                    class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-all {{ $isContentContact ? 'bg-[#333333] text-white' : 'text-[#CCCCCC] hover:bg-[#222222] hover:text-white' }}"
                >
                    <x-lucide-phone class="w-3.5 h-3.5 shrink-0" />
                    <span class="font-medium text-xs">{{ __('ui.sidebar_content_contact') }}</span>
                </a>
                <a
                    href="{{ route('content.privacy') }}"
                    wire:navigate
                    class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-all {{ $isContentPrivacy ? 'bg-[#333333] text-white' : 'text-[#CCCCCC] hover:bg-[#222222] hover:text-white' }}"
                >
                    <x-lucide-shield-check class="w-3.5 h-3.5 shrink-0" />
                    <span class="font-medium text-xs">{{ __('ui.sidebar_privacy') }}</span>
                </a>
                <a
                    href="{{ route('content.terms') }}"
                    wire:navigate
                    class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-all {{ $isContentTerms ? 'bg-[#333333] text-white' : 'text-[#CCCCCC] hover:bg-[#222222] hover:text-white' }}"
                >
                    <x-lucide-file-text class="w-3.5 h-3.5 shrink-0" />
                    <span class="font-medium text-xs">{{ __('ui.sidebar_terms') }}</span>
                </a>
            </div>
        </div>
    </nav>
</aside>

{{-- Mobile Sidebar --}}
<div
    x-show="sidebarOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 md:hidden"
        x-cloak
        style="display: none;"
    >
        {{-- Backdrop --}}
        <div
            class="absolute inset-0 backdrop-blur-md"
            @click="sidebarOpen = false"
            aria-hidden="true"
        ></div>

        {{-- Sidebar Panel --}}
        <aside
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="-translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full"
            class="absolute left-0 top-0 bottom-0 w-64 bg-[#111111] border-r border-[#333333] flex flex-col"
        >
            <div class="p-4 border-b border-[#333333] flex items-center gap-3">
                <a
                    href="{{ route('home', ['locale' => $locale]) }}"
                    @click="sidebarOpen = false"
                    class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 overflow-hidden hover:opacity-90 transition-opacity"
                    aria-label="{{ __('ui.site_name') }}"
                >
                    <img
                        src="{{ asset('images/logo-white.svg') }}"
                        alt="{{ __('ui.site_name') }} Logo"
                        class="w-full h-full object-contain"
                    />
                </a>
                <div class="min-w-0">
                    <a
                        href="{{ route('home', ['locale' => $locale]) }}"
                        @click="sidebarOpen = false"
                        class="text-lg font-black text-white block truncate hover:underline"
                    >
                        {{ __('ui.site_name') }}
                    </a>
                    <a href="{{ route('profile') }}" wire:navigate @click="sidebarOpen = false" class="text-xs text-[#CCCCCC] hover:underline block truncate">{{ __('ui.sidebar_admin') }}</a>
                </div>
            </div>

            <nav class="flex-1 p-3 space-y-1 overflow-y-auto">
                <a
                    href="{{ route('dashboard') }}"
                    wire:navigate
                    @click="sidebarOpen = false"
                    class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-all {{ $isDashboard ? 'bg-[#F97316] text-white' : 'text-[#CCCCCC] hover:bg-[#333333] hover:text-white' }}"
                >
                    <x-lucide-gauge class="w-4 h-4 shrink-0" />
                    <span class="font-medium">{{ __('ui.dashboard') }}</span>
                </a>
                <a
                    href="{{ route('clients') }}"
                    wire:navigate
                    @click="sidebarOpen = false"
                    class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-all {{ $isClients ? 'bg-[#F97316] text-white' : 'text-[#CCCCCC] hover:bg-[#333333] hover:text-white' }}"
                >
                    <x-lucide-users class="w-4 h-4 shrink-0" />
                    <span class="font-medium">{{ __('ui.sidebar_clients') }}</span>
                </a>
                @if($isAdmin)
                <a
                    href="{{ route('administrators') }}"
                    wire:navigate
                    @click="sidebarOpen = false"
                    class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-all {{ $isAdministrators ? 'bg-[#F97316] text-white' : 'text-[#CCCCCC] hover:bg-[#333333] hover:text-white' }}"
                >
                    <x-lucide-shield class="w-4 h-4 shrink-0" />
                    <span class="font-medium">{{ __('ui.sidebar_administrators') }}</span>
                </a>
                @endif

                <div x-data="{ contentExpandedMobile: {{ $isContentActive ? 'true' : 'false' }} }">
                    <button
                        type="button"
                        @click="contentExpandedMobile = !contentExpandedMobile"
                        class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-all {{ $isContentActive ? 'bg-[#F97316] text-white' : 'text-[#CCCCCC] hover:bg-[#333333] hover:text-white' }}"
                    >
                        <x-lucide-file-text class="w-4 h-4 shrink-0" />
                        <span class="font-medium flex-1 text-left">{{ __('ui.sidebar_content_site') }}</span>
                        <span x-show="contentExpandedMobile"><x-lucide-chevron-down class="w-4 h-4 shrink-0" /></span>
                        <span x-show="!contentExpandedMobile" x-cloak style="display: none;"><x-lucide-chevron-right class="w-4 h-4 shrink-0" /></span>
                    </button>
                    <div x-show="contentExpandedMobile" class="mt-1 ml-4 space-y-1 pl-2 border-l border-[#333333]">
                        <a
                            href="{{ route('content.about') }}"
                            wire:navigate
                            @click="sidebarOpen = false"
                            class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-all {{ $isContentAbout ? 'bg-[#333333] text-white' : 'text-[#CCCCCC] hover:bg-[#222222] hover:text-white' }}"
                        >
                            <x-lucide-info class="w-3.5 h-3.5 shrink-0" />
                            <span class="font-medium text-xs">{{ __('ui.sidebar_content_about') }}</span>
                        </a>
                        <a
                            href="{{ route('content.roadmap') }}"
                            wire:navigate
                            @click="sidebarOpen = false"
                            class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-all {{ $isContentRoadmap ? 'bg-[#333333] text-white' : 'text-[#CCCCCC] hover:bg-[#222222] hover:text-white' }}"
                        >
                            <x-lucide-line-squiggle class="w-3.5 h-3.5 shrink-0" />
                            <span class="font-medium text-xs">{{ __('ui.sidebar_content_roadmap') }}</span>
                        </a>
                        <a
                            href="{{ route('content.contact') }}"
                            wire:navigate
                            @click="sidebarOpen = false"
                            class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-all {{ $isContentContact ? 'bg-[#333333] text-white' : 'text-[#CCCCCC] hover:bg-[#222222] hover:text-white' }}"
                        >
                            <x-lucide-phone class="w-3.5 h-3.5 shrink-0" />
                            <span class="font-medium text-xs">{{ __('ui.sidebar_content_contact') }}</span>
                        </a>
                        <a
                            href="{{ route('content.privacy') }}"
                            wire:navigate
                            @click="sidebarOpen = false"
                            class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-all {{ $isContentPrivacy ? 'bg-[#333333] text-white' : 'text-[#CCCCCC] hover:bg-[#222222] hover:text-white' }}"
                        >
                            <x-lucide-shield-check class="w-3.5 h-3.5 shrink-0" />
                            <span class="font-medium text-xs">{{ __('ui.sidebar_privacy') }}</span>
                        </a>
                        <a
                            href="{{ route('content.terms') }}"
                            wire:navigate
                            @click="sidebarOpen = false"
                            class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-all {{ $isContentTerms ? 'bg-[#333333] text-white' : 'text-[#CCCCCC] hover:bg-[#222222] hover:text-white' }}"
                        >
                            <x-lucide-file-text class="w-3.5 h-3.5 shrink-0" />
                            <span class="font-medium text-xs">{{ __('ui.sidebar_terms') }}</span>
                        </a>
                    </div>
                </div>
            </nav>
        </aside>
    </div>
