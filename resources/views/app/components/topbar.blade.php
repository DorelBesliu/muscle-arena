<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    /**
     * Get the current page title for the topbar.
     */
    public function getCurrentPageTitle(): string
    {
        if (request()->routeIs('dashboard')) {
            return __('ui.dashboard');
        }
        if (request()->routeIs('clients')) {
            return __('ui.sidebar_clients');
        }
        if (request()->routeIs('profile')) {
            return __('Profile');
        }

        return config('app.name');
    }

    /**
     * Get the user's initial(s) for the avatar.
     */
    public function getInitial(string $name): string
    {
        return strtoupper(mb_substr(trim($name), 0, 1));
    }

    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect(route('signin', ['locale' => app()->getLocale()]), navigate: false);
    }
}; ?>

<header
    x-data="{ userMenuOpen: false }"
    x-on:click.outside="userMenuOpen = false"
    class="bg-[#111111] border-b border-[#333333] px-4 md:px-6 py-3"
>
    <div class="flex items-center justify-between">
        {{-- Mobile Menu Button --}}
        <button
            type="button"
            class="md:hidden text-white hover:text-[#F97316] transition-colors"
            aria-label="{{ __('ui.menu_label') }}"
            @click="$dispatch('open-sidebar')"
        >
            <x-lucide-menu class="w-5 h-5" />
        </button>

        {{-- Page Title --}}
        <div class="flex-1 md:flex-none">
            <h2 class="text-base md:text-lg font-bold text-white text-center md:text-left">
                {{ $this->getCurrentPageTitle() }}
            </h2>
        </div>

        {{-- User Menu - Desktop --}}
        <div class="hidden md:block relative">
            <button
                type="button"
                @click="userMenuOpen = !userMenuOpen"
                class="flex items-center gap-2 px-2 py-1 rounded-lg hover:bg-[#333333] transition-all"
            >
                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-[#F97316] to-[#EF4444] flex items-center justify-center">
                    <span class="text-white font-bold text-sm">{{ $this->getInitial(auth()->user()->name ?? '') }}</span>
                </div>
            </button>

            <div
                x-show="userMenuOpen"
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="absolute right-0 top-full mt-2 w-56 bg-[#111111] border border-[#333333] rounded-xl shadow-xl overflow-hidden z-50"
                x-cloak
                style="display: none;"
            >
                <div class="py-1">
                    <a
                        href="{{ route('profile') }}"
                        wire:navigate
                        class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-[#CCCCCC] hover:bg-[#333333] hover:text-white transition-all"
                    >
                        <x-lucide-settings class="w-4 h-4 shrink-0" />
                        <span>{{ __('Profile') }}</span>
                    </a>
                    <button
                        type="button"
                        wire:click="logout"
                        class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-[#CCCCCC] hover:bg-red-500/10 hover:text-red-500 transition-all text-left"
                    >
                        <x-lucide-log-out class="w-4 h-4 shrink-0" />
                        <span>{{ __('Log Out') }}</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Mobile User Menu --}}
        <div class="md:hidden relative">
            <button
                type="button"
                @click="userMenuOpen = !userMenuOpen"
                class="flex items-center justify-center"
            >
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-[#F97316] to-[#EF4444] flex items-center justify-center">
                    <span class="text-white font-bold text-xs">{{ $this->getInitial(auth()->user()->name ?? '') }}</span>
                </div>
            </button>

            <div
                x-show="userMenuOpen"
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="absolute right-0 top-full mt-2 w-48 bg-[#111111] border border-[#333333] rounded-xl shadow-xl overflow-hidden z-50"
                x-cloak
                style="display: none;"
            >
                <div class="py-1">
                    <a
                        href="{{ route('profile') }}"
                        wire:navigate
                        class="w-full flex items-center gap-2 px-3 py-2 text-sm text-[#CCCCCC] hover:bg-[#333333] hover:text-white transition-all"
                    >
                        <x-lucide-settings class="w-4 h-4 shrink-0" />
                        <span>{{ __('Profile') }}</span>
                    </a>
                    <button
                        type="button"
                        wire:click="logout"
                        class="w-full flex items-center gap-2 px-3 py-2 text-sm text-[#CCCCCC] hover:bg-red-500/10 hover:text-red-500 transition-all text-left"
                    >
                        <x-lucide-log-out class="w-4 h-4 shrink-0" />
                        <span>{{ __('Log Out') }}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</header>
