@php
    $user = auth()->user();
    $twoFactorPending = $user->two_factor_secret && ! $user->two_factor_confirmed_at;
@endphp

<div class="bg-[#111111] border border-[#333333] rounded-xl transition-all">
    <div class="p-4 md:p-5">
        <h3 class="text-lg font-black text-white mb-4 flex items-center gap-2">
            <x-lucide-shield class="w-5 h-5 text-[#F97316]" />
            {{ __('ui.profile_two_factor') }}
        </h3>
        <div class="flex items-center justify-between p-3 bg-[#000000] border border-[#333333] rounded-xl">
            <div class="flex items-center gap-3">
                <div class="w-3 h-3 rounded-full {{ $user->two_factor_confirmed_at ? 'bg-green-500' : 'bg-[#666666]' }}"></div>
                <div>
                    <p class="text-sm font-bold text-white">{{ __('ui.profile_two_factor_status') }}</p>
                    <p class="text-xs text-[#999999]">
                        {{ $user->two_factor_confirmed_at ? __('ui.profile_enabled') : __('ui.profile_disabled') }}
                    </p>
                </div>
            </div>

            @if (! $user->two_factor_confirmed_at)
                @if (! $twoFactorPending)
                    <div class="inline" x-data="enable2fa('{{ route('two-factor.enable') }}')">
                        <button
                            type="button"
                            @click="enable2fa()"
                            :disabled="loading"
                            class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium text-white bg-[#F97316] hover:bg-[#ea580c] rounded-lg transition-colors disabled:opacity-70 disabled:cursor-wait"
                        >
                            <span x-show="loading" class="inline-flex items-center gap-2">
                                <x-lucide-loader-2 class="w-4 h-4 animate-spin" />
                                {{ __('ui.profile_enable_2fa') }}
                            </span>
                            <span x-show="!loading" x-cloak>{{ __('ui.profile_enable_2fa') }}</span>
                        </button>
                    </div>
                @else
                    <button
                        type="button"
                        wire:click="$set('showTwoFactorSetupModal', true)"
                        class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium text-white bg-[#F97316] hover:bg-[#ea580c] rounded-lg transition-colors"
                    >
                        <x-lucide-shield class="w-4 h-4" />
                        {{ __('ui.profile_setup_2fa') }}
                    </button>
                @endif
            @else
                <div
                    class="inline"
                    x-data="disable2fa('{{ route('two-factor.disable') }}', '{{ addslashes(__('ui.are_you_sure_want_to_disable_2fa')) }}')"
                >
                    <button
                        type="button"
                        @click="disable2fa()"
                        :disabled="loading"
                        class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium text-red-500 border border-red-500 rounded-lg hover:bg-red-500 hover:text-white transition-colors disabled:opacity-70 disabled:cursor-wait"
                    >
                        <span x-show="loading" class="inline-flex items-center gap-2">
                            <x-lucide-loader-2 class="w-4 h-4 animate-spin" />
                            {{ __('ui.profile_disable_2fa') }}
                        </span>
                        <span x-show="!loading" x-cloak>{{ __('ui.profile_disable_2fa') }}</span>
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>
