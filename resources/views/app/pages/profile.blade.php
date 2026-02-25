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
                    <x-app.components.profile.update-language-form />
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
                <x-app.components.profile.two-factor-card />
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

    @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::twoFactorAuthentication()) && $twoFactorPending)
        @if($showTwoFactorSetupModal)
            <x-app.components.profile.two-factor-setup-dialog />
        @endif
    @endif
</div>
