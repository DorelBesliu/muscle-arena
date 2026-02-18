<div class="min-h-screen bg-[#000000] text-white">
    <div class="max-w-xl mx-auto px-4 py-16">
        <div class="bg-[#111111] border-2 border-[#333333] rounded-2xl p-8">
            <div class="w-16 h-16 mx-auto mb-6 rounded-full bg-[#F97316]/20 flex items-center justify-center">
                <x-lucide-lock class="w-8 h-8 text-[#F97316]" />
            </div>
            <h1 class="text-xl font-bold text-white text-center mb-2">
                {{ __('ui.force_change_password_title') }}
            </h1>
            <p class="text-[#CCCCCC] text-center mb-8">
                {{ __('ui.force_change_password_message') }}
            </p>

            <form wire:submit="savePassword" class="space-y-4">
                @csrf
                <div>
                    <x-input-label for="force_current_password" :value="__('ui.profile_current_password')" class="text-sm text-white" />
                    <x-text-input
                        wire:model="current_password"
                        id="force_current_password"
                        name="current_password"
                        type="password"
                        class="mt-1.5 h-9 w-full text-sm bg-[#000000] border-2 border-[#333333] text-white focus:border-[#F97316] focus:ring-0 rounded-xl"
                        autocomplete="current-password"
                    />
                    <x-input-error :messages="$errors->get('current_password')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="force_password" :value="__('ui.profile_new_password')" class="text-sm text-white" />
                    <x-text-input
                        wire:model="password"
                        id="force_password"
                        name="password"
                        type="password"
                        class="mt-1.5 h-9 w-full text-sm bg-[#000000] border-2 border-[#333333] text-white focus:border-[#F97316] focus:ring-0 rounded-xl"
                        autocomplete="new-password"
                    />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="force_password_confirmation" :value="__('ui.profile_confirm_password')" class="text-sm text-white" />
                    <x-text-input
                        wire:model="password_confirmation"
                        id="force_password_confirmation"
                        name="password_confirmation"
                        type="password"
                        class="mt-1.5 h-9 w-full text-sm bg-[#000000] border-2 border-[#333333] text-white focus:border-[#F97316] focus:ring-0 rounded-xl"
                        autocomplete="new-password"
                    />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <div class="flex flex-col sm:flex-row gap-3 pt-2">
                    <x-app.components.loading-button
                        type="submit"
                        wire-target="savePassword"
                        class="inline-flex items-center justify-center gap-2 px-6 py-3 text-sm font-medium text-white bg-[#F97316] hover:bg-[#ea580c] rounded-xl transition-colors disabled:cursor-wait flex-1"
                    >
                        <x-lucide-save class="w-4 h-4 shrink-0" />
                        <x-slot:label>{{ __('ui.force_change_password_save') }}</x-slot:label>
                    </x-app.components.loading-button>
                    <button
                        type="button"
                        wire:click="logout"
                        class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-[#333333] hover:bg-[#444444] text-white font-medium transition-colors"
                    >
                        <x-lucide-log-out class="w-5 h-5" />
                        {{ __('ui.force_change_password_logout') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
