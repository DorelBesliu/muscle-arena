@php
    $user = auth()->user();
@endphp

<div x-data="confirm2fa('{{ route('two-factor.confirm') }}')">
    <x-app-dialog :title="__('ui.profile_setup_2fa')" maxWidth="md">
        <x-slot:close>
            <button type="button" wire:click="$set('showTwoFactorSetupModal', false)" class="h-8 w-8 flex items-center justify-center rounded-lg border-2 border-[#333333] text-[#CCCCCC] hover:text-white">
                <x-lucide-x class="w-4 h-4" />
            </button>
        </x-slot:close>

        <div class="space-y-6">
            <p class="text-sm text-[#CCCCCC]">
                {{ __('ui.profile_recommended_apps') }}
            </p>

            <div class="flex justify-center p-4 bg-white rounded-lg [&_svg]:max-h-40 [&_svg]:w-auto [&_svg]:mx-auto">
                {!! $user->twoFactorQrCodeSvg() !!}
            </div>

            <div class="text-sm text-[#CCCCCC]">
                {{ __('ui.profile_setup_2fa_manual')}}

                <div
                    x-data="{ secret: @js($user->rawTwoFactorSecret) }"
                    x-on:click="copyToClipboard(secret, @js(__('ui.copied')))"
                    x-on:keydown.enter.prevent="copyToClipboard(secret, @js(__('ui.copied')))"
                    role="button"
                    tabindex="0"
                    class="bg-[#000000] border border-[#333333] rounded-lg p-3 text-center mt-2 cursor-pointer select-all hover:border-[#555555] transition-colors focus:outline-none focus:ring-2 focus:ring-[#F97316] focus:ring-offset-0"
                    title="{{ __('ui.click_to_copy') }}"
                >
                    {!! implode(' ', str_split($user->rawTwoFactorSecret, 4)) !!}
                </div>
            </div>

            <div x-data="recoveryCodesDownload(@js($user->recoveryCodes()))" class="space-y-0">
                <div x-data="{ expanded: false }" @click.outside="expanded = false">
                    <div class="flex items-center gap-5">
                        <button
                            type="button"
                            x-on:click="download()"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-white bg-[#333333] hover:bg-[#444444] border border-[#555555] rounded-lg transition-colors w-full"
                        >
                            <x-lucide-download class="w-4 h-4 shrink-0" />
                            {{ __('ui.profile_download_codes') }}
                        </button>
                        <button
                            type="button"
                            @click="expanded = ! expanded"
                            class="inline-flex shrink-0 text-[#999999] hover:text-white focus:outline-none focus:ring-2 focus:ring-[#F97316] focus:ring-offset-0 rounded-full p-0.5"
                            :aria-expanded="expanded"
                            aria-label="{{ __('ui.profile_backup_codes') }}"
                        >
                            <x-lucide-info class="w-4 h-4 shrink-0" />
                        </button>
                    </div>
                    <div
                        x-show="expanded"
                        x-collapse
                        class="overflow-hidden"
                    >
                        <p class="mt-2 p-3 rounded-lg bg-[#0a0a0a] border border-[#333333] text-xs text-[#CCCCCC]">
                            {{ __('ui.profile_backup_codes_desc') }}
                        </p>
                    </div>
                </div>
            </div>

            <form id="two-factor-confirm-form" @submit.prevent="confirm2fa()">
                <label for="two-factor-code" class="block text-sm font-medium text-white mb-2">{{ __('ui.profile_enter_code') }}</label>
                <input
                    type="text"
                    id="two-factor-code"
                    x-model="code"
                    inputmode="numeric"
                    autocomplete="one-time-code"
                    maxlength="6"
                    placeholder="000000"
                    class="w-full bg-[#000000] border-2 border-[#333333] rounded-xl py-3 px-4 text-white focus:border-[#F97316] focus:outline-none"
                />
                <x-input-error :messages="$errors->get('code')" class="mt-2" />
            </form>
        </div>
        <x-slot:footer>
            <button
                type="button"
                wire:click="$set('showTwoFactorSetupModal', false)"
                class="h-9 px-4 rounded-xl border-2 border-[#333333] text-sm font-medium text-[#CCCCCC] hover:text-white"
            >
                {{ __('ui.close') }}
            </button>
            <button
                type="button"
                @click="confirm2fa()"
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-[#F97316] hover:bg-[#ea580c] rounded-lg transition-colors disabled:opacity-70 disabled:cursor-wait"
                :disabled="loading"
            >
                <span x-show="loading" class="inline-flex items-center gap-2">
                    <x-lucide-loader-2 class="w-4 h-4 animate-spin" />
                    {{ __('ui.profile_verify') }}
                </span>
                <span x-show="!loading" x-cloak>{{ __('ui.profile_verify') }}</span>
            </button>
        </x-slot:footer>
    </x-app-dialog>
</div>
