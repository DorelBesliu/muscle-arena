@php
    $locale = app()->getLocale();
    $isDashboard = request()->routeIs('dashboard');
    $isClients = request()->routeIs('clients');
    $isProfile = request()->routeIs('profile');
    $isContentActive = request()->routeIs('privacy') || request()->routeIs('terms');
    $isPrivacy = request()->routeIs('privacy');
    $isTerms = request()->routeIs('terms');
@endphp

{{-- Desktop Sidebar --}}
<aside class="hidden md:flex flex-col w-[270px] bg-[#111111] border-r border-[#333333] shrink-0">
    {{-- Logo --}}
    <div class="p-4 border-b border-[#333333]">
        <h1 class="text-lg font-black text-white">{{ __('ui.site_name') }}</h1>
        <p class="text-xs text-[#CCCCCC]">{{ auth()->user()->name ?? '' }}</p>
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
            wire:show="false"
            href="{{ route('clients') }}"
            wire:navigate
            class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-all {{ $isClients ? 'bg-[#F97316] text-white' : 'text-[#CCCCCC] hover:bg-[#333333] hover:text-white' }}"
        >
            <x-lucide-users class="w-4 h-4 shrink-0" />
            <span class="font-medium">{{ __('ui.sidebar_clients') }}</span>
        </a>

        {{-- Content - Expandable --}}
        <div x-data="{ contentExpanded: {{ $isContentActive ? 'true' : 'false' }} }" wire:show="false">
            <button
                type="button"
                @click="contentExpanded = !contentExpanded"
                class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-all {{ $isContentActive ? 'bg-[#F97316] text-white' : 'text-[#CCCCCC] hover:bg-[#333333] hover:text-white' }}"
            >
                <x-lucide-file-text class="w-4 h-4 shrink-0" />
                <span class="font-medium flex-1 text-left">{{ __('ui.sidebar_content') }}</span>
                <span x-show="contentExpanded" class="shrink-0"><x-lucide-chevron-down class="w-4 h-4" /></span>
                <span x-show="!contentExpanded" class="shrink-0" x-cloak style="display: none;"><x-lucide-chevron-right class="w-4 h-4" /></span>
            </button>

            <div x-show="contentExpanded" class="mt-1 ml-4 space-y-1 pl-2 border-l border-[#333333]">
                <a
                    href="{{ route('home', ['locale' => $locale]) }}#about"
                    class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-all text-[#CCCCCC] hover:bg-[#222222] hover:text-white"
                >
                    <x-lucide-info class="w-3.5 h-3.5 shrink-0" />
                    <span class="font-medium text-xs">{{ __('ui.sidebar_content_about') }}</span>
                </a>
                <a
                    href="{{ route('home', ['locale' => $locale]) }}#timeline"
                    class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-all text-[#CCCCCC] hover:bg-[#222222] hover:text-white"
                >
                    <x-lucide-route class="w-3.5 h-3.5 shrink-0" />
                    <span class="font-medium text-xs">{{ __('ui.sidebar_content_roadmap') }}</span>
                </a>
                <a
                    href="{{ route('home', ['locale' => $locale]) }}#contact"
                    class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-all text-[#CCCCCC] hover:bg-[#222222] hover:text-white"
                >
                    <x-lucide-mail class="w-3.5 h-3.5 shrink-0" />
                    <span class="font-medium text-xs">{{ __('ui.menu_contact') }}</span>
                </a>
                <a
                    href="{{ route('privacy', ['locale' => $locale]) }}"
                    wire:navigate
                    class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-all {{ $isPrivacy ? 'bg-[#333333] text-white' : 'text-[#CCCCCC] hover:bg-[#222222] hover:text-white' }}"
                >
                    <x-lucide-shield class="w-3.5 h-3.5 shrink-0" />
                    <span class="font-medium text-xs">{{ __('ui.sidebar_privacy') }}</span>
                </a>
                <a
                    href="{{ route('terms', ['locale' => $locale]) }}"
                    wire:navigate
                    class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-all {{ $isTerms ? 'bg-[#333333] text-white' : 'text-[#CCCCCC] hover:bg-[#222222] hover:text-white' }}"
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
            <div class="p-4 border-b border-[#333333] flex items-center justify-between">
                <div>
                    <h1 class="text-lg font-black text-white">{{ __('ui.site_name') }}</h1>
                    <p class="text-xs text-[#CCCCCC]">{{ __('ui.sidebar_admin') }}</p>
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

                <div x-data="{ contentExpandedMobile: {{ $isContentActive ? 'true' : 'false' }} }">
                    <button
                        type="button"
                        @click="contentExpandedMobile = !contentExpandedMobile"
                        class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-all {{ $isContentActive ? 'bg-[#F97316] text-white' : 'text-[#CCCCCC] hover:bg-[#333333] hover:text-white' }}"
                    >
                        <x-lucide-file-text class="w-4 h-4 shrink-0" />
                        <span class="font-medium flex-1 text-left">{{ __('ui.sidebar_content') }}</span>
                        <span x-show="contentExpandedMobile"><x-lucide-chevron-down class="w-4 h-4 shrink-0" /></span>
                        <span x-show="!contentExpandedMobile" x-cloak style="display: none;"><x-lucide-chevron-right class="w-4 h-4 shrink-0" /></span>
                    </button>
                    <div x-show="contentExpandedMobile" class="mt-1 ml-4 space-y-1 pl-2 border-l border-[#333333]">
                        <a
                            href="{{ route('home', ['locale' => $locale]) }}#about"
                            @click="sidebarOpen = false"
                            class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-all text-[#CCCCCC] hover:bg-[#222222] hover:text-white"
                        >
                            <x-lucide-info class="w-3.5 h-3.5 shrink-0" />
                            <span class="font-medium text-xs">{{ __('ui.sidebar_content_about') }}</span>
                        </a>
                        <a
                            href="{{ route('home', ['locale' => $locale]) }}#timeline"
                            @click="sidebarOpen = false"
                            class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-all text-[#CCCCCC] hover:bg-[#222222] hover:text-white"
                        >
                            <x-lucide-route class="w-3.5 h-3.5 shrink-0" />
                            <span class="font-medium text-xs">{{ __('ui.sidebar_content_roadmap') }}</span>
                        </a>
                        <a
                            href="{{ route('home', ['locale' => $locale]) }}#contact"
                            @click="sidebarOpen = false"
                            class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-all text-[#CCCCCC] hover:bg-[#222222] hover:text-white"
                        >
                            <x-lucide-mail class="w-3.5 h-3.5 shrink-0" />
                            <span class="font-medium text-xs">{{ __('ui.menu_contact') }}</span>
                        </a>
                        <a
                            href="{{ route('privacy', ['locale' => $locale]) }}"
                            wire:navigate
                            @click="sidebarOpen = false"
                            class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-all {{ $isPrivacy ? 'bg-[#333333] text-white' : 'text-[#CCCCCC] hover:bg-[#222222] hover:text-white' }}"
                        >
                            <x-lucide-shield class="w-3.5 h-3.5 shrink-0" />
                            <span class="font-medium text-xs">{{ __('ui.sidebar_privacy') }}</span>
                        </a>
                        <a
                            href="{{ route('terms', ['locale' => $locale]) }}"
                            wire:navigate
                            @click="sidebarOpen = false"
                            class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-all {{ $isTerms ? 'bg-[#333333] text-white' : 'text-[#CCCCCC] hover:bg-[#222222] hover:text-white' }}"
                        >
                            <x-lucide-file-text class="w-3.5 h-3.5 shrink-0" />
                            <span class="font-medium text-xs">{{ __('ui.sidebar_terms') }}</span>
                        </a>
                    </div>
                </div>
            </nav>
        </aside>
    </div>
