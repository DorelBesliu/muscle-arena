<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component
{
    public function mount(): void
    {
        if (! session()->has('locale')) {
            session()->put('locale', app()->getLocale());
        }
    }
}; ?>

<div class="min-h-screen bg-[#000000] text-white">
    <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="space-y-4">
            {{-- Personal Information --}}
            <div class="bg-[#111111] border border-[#333333] rounded-xl transition-all">
                <div class="p-4 md:p-5">
                    <h3 class="text-lg font-black text-white mb-4 flex items-center gap-2">
                        <x-lucide-user class="w-5 h-5 text-[#F97316]" />
                        {{ __('ui.profile_personal_info') }}
                    </h3>
                    <livewire:components.profile.update-profile-information-form />
                </div>
            </div>

            {{-- Language Preference --}}
            <div class="bg-[#111111] border border-[#333333] rounded-xl transition-all">
                <div class="p-4 md:p-5">
                    <h3 class="text-lg font-black text-white mb-4 flex items-center gap-2">
                        <x-lucide-globe class="w-5 h-5 text-[#F97316]" />
                        {{ __('ui.profile_language_preference') }}
                    </h3>
                    <p class="text-xs text-[#666666] mb-2">{{ __('ui.profile_language_desc') }}</p>
                    <div class="flex items-center gap-2 bg-[#000000] border border-[#333333] rounded-xl p-1" wire:ignore>
                        <form action="{{ route('user.locale.update') }}" method="POST" class="flex-1" x-data="{ loading: false }" x-on:submit="loading = true">
                            @csrf
                            <input type="hidden" name="locale" value="ro">
                            <button type="submit" class="w-full px-4 py-2 rounded-lg transition-all text-sm font-medium inline-flex items-center justify-center gap-2 {{ app()->getLocale() === 'ro' ? 'bg-[#F97316] text-white' : 'text-[#CCCCCC] hover:text-white hover:bg-[#111111]' }}" :disabled="loading">
                                <span x-show="loading" class="inline-flex items-center gap-2">
                                    <x-lucide-loader-2 class="w-4 h-4 animate-spin" />
                                    {{ __('ui.profile_romanian') }}
                                </span>
                                <span x-show="!loading" x-cloak>{{ __('ui.profile_romanian') }}</span>
                            </button>
                        </form>
                        <form action="{{ route('user.locale.update') }}" method="POST" class="flex-1" x-data="{ loading: false }" x-on:submit="loading = true">
                            @csrf
                            <input type="hidden" name="locale" value="en">
                            <button type="submit" class="w-full px-4 py-2 rounded-lg transition-all text-sm font-medium inline-flex items-center justify-center gap-2 {{ app()->getLocale() === 'en' ? 'bg-[#F97316] text-white' : 'text-[#CCCCCC] hover:text-white hover:bg-[#111111]' }}" :disabled="loading">
                                <span x-show="loading" class="inline-flex items-center gap-2">
                                    <x-lucide-loader-2 class="w-4 h-4 animate-spin" />
                                    {{ __('ui.profile_english') }}
                                </span>
                                <span x-show="!loading" x-cloak>{{ __('ui.profile_english') }}</span>
                            </button>
                        </form>
                        <form action="{{ route('user.locale.update') }}" method="POST" class="flex-1" x-data="{ loading: false }" x-on:submit="loading = true">
                            @csrf
                            <input type="hidden" name="locale" value="ru">
                            <button type="submit" class="w-full px-4 py-2 rounded-lg transition-all text-sm font-medium inline-flex items-center justify-center gap-2 {{ app()->getLocale() === 'ru' ? 'bg-[#F97316] text-white' : 'text-[#CCCCCC] hover:text-white hover:bg-[#111111]' }}" :disabled="loading">
                                <span x-show="loading" class="inline-flex items-center gap-2">
                                    <x-lucide-loader-2 class="w-4 h-4 animate-spin" />
                                    {{ __('ui.profile_russian') }}
                                </span>
                                <span x-show="!loading" x-cloak>{{ __('ui.profile_russian') }}</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Change Password --}}
            <div class="bg-[#111111] border border-[#333333] rounded-xl transition-all">
                <div class="p-4 md:p-5">
                    <h3 class="text-lg font-black text-white mb-4 flex items-center gap-2">
                        <x-lucide-key class="w-5 h-5 text-[#F97316]" />
                        {{ __('ui.profile_change_password') }}
                    </h3>
                    <p class="text-xs text-[#666666] mb-3">{{ __('ui.profile_ensure_password') }}</p>
                    <livewire:components.profile.update-password-form />
                </div>
            </div>

            {{-- Two-Factor Authentication --}}
            @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::twoFactorAuthentication()))
                <div class="bg-[#111111] border border-[#333333] rounded-xl transition-all">
                    <div class="p-4 md:p-5">
                        <h3 class="text-lg font-black text-white mb-4 flex items-center gap-2">
                            <x-lucide-shield class="w-5 h-5 text-[#F97316]" />
                            {{ __('ui.profile_two_factor') }}
                        </h3>
                        <div class="flex items-center justify-between p-3 bg-[#000000] border border-[#333333] rounded-xl">
                            <div class="flex items-center gap-3">
                                <div class="w-3 h-3 rounded-full {{ auth()->user()->two_factor_confirmed_at ? 'bg-green-500' : 'bg-[#666666]' }}"></div>
                                <div>
                                    <p class="text-sm font-bold text-white">{{ __('ui.profile_two_factor_status') }}</p>
                                    <p class="text-xs text-[#999999]">
                                        {{ auth()->user()->two_factor_confirmed_at ? __('ui.profile_enabled') : __('ui.profile_disabled') }}
                                    </p>
                                </div>
                            </div>
                            @if (! auth()->user()->two_factor_confirmed_at)
                                <form action="{{ route('two-factor.enable') }}" method="POST" class="inline" x-data="{ loading: false }" x-on:submit="loading = true">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium text-white bg-[#F97316] hover:bg-[#ea580c] rounded-lg transition-colors disabled:opacity-70 disabled:cursor-wait" :disabled="loading">
                                        <span x-show="loading" class="inline-flex items-center gap-2">
                                            <x-lucide-loader-2 class="w-4 h-4 animate-spin" />
                                            {{ __('ui.profile_enable_2fa') }}
                                        </span>
                                        <span x-show="!loading" x-cloak>{{ __('ui.profile_enable_2fa') }}</span>
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('two-factor.disable') }}" method="POST" class="inline" onsubmit="return confirm('{{ __('ui.profile_disable_2fa') }}?');" x-data="{ loading: false }" x-on:submit="loading = true">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium text-red-500 border border-red-500 rounded-lg hover:bg-red-500 hover:text-white transition-colors disabled:opacity-70 disabled:cursor-wait" :disabled="loading">
                                        <span x-show="loading" class="inline-flex items-center gap-2">
                                            <x-lucide-loader-2 class="w-4 h-4 animate-spin" />
                                            {{ __('ui.profile_disable_2fa') }}
                                        </span>
                                        <span x-show="!loading" x-cloak>{{ __('ui.profile_disable_2fa') }}</span>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            {{-- Danger Zone --}}
            <div class="bg-[#111111] border border-red-500 rounded-xl transition-all">
                <div class="p-4 md:p-5">
                    <h3 class="text-lg font-black text-red-500 mb-4 flex items-center gap-2">
                        <x-lucide-alert-triangle class="w-5 h-5" />
                        {{ __('ui.profile_danger_zone') }}
                    </h3>
                    <div class="flex items-center justify-between p-3 bg-[#000000] border border-red-500/30 rounded-xl">
                        <div>
                            <p class="text-sm font-bold text-white mb-1">{{ __('ui.profile_delete_account') }}</p>
                            <p class="text-xs text-[#999999]">{{ __('ui.profile_delete_account_desc') }}</p>
                        </div>
                        <livewire:components.profile.delete-user-form />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
