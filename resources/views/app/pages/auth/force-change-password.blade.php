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

            <form wire:submit="updatePassword" class="space-y-4">
                <div>
                    <label for="force_password" class="block text-sm text-white mb-1.5">{{ __('ui.profile_new_password') }}</label>
                    <input type="password" id="force_password" wire:model="password" required autocomplete="new-password"
                        class="w-full h-9 text-sm bg-[#000000] border-2 border-[#333333] text-white focus:border-[#F97316] focus:outline-none rounded-xl px-3" />
                    @error('password')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="force_password_confirmation" class="block text-sm text-white mb-1.5">{{ __('ui.profile_confirm_password') }}</label>
                    <input type="password" id="force_password_confirmation" wire:model="password_confirmation" required autocomplete="new-password"
                        class="w-full h-9 text-sm bg-[#000000] border-2 border-[#333333] text-white focus:border-[#F97316] focus:outline-none rounded-xl px-3" />
                    @error('password_confirmation')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror
                </div>

                <div class="flex flex-col sm:flex-row gap-3 pt-2">
                    <x-app.components.loading-button
                        type="submit"
                        wire-target="updatePassword"
                        class="px-4 py-3 rounded-xl bg-[#F97316] hover:bg-[#ea580c] text-sm font-medium text-white flex-1"
                    >
                        <x-lucide-save class="w-4 h-4 shrink-0" />
                        <x-slot:label>{{ __('ui.admins_save') }}</x-slot:label>
                    </x-app.components.loading-button>
                </div>
            </form>
        </div>
    </div>
</div>
