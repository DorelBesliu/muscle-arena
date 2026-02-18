<div>
    <form wire:submit="updateProfileInformation" id="profile-information-form" class="space-y-3">
        @csrf
        <div>
            <x-input-label for="name" :value="__('ui.profile_name')" class="text-sm text-white" />
            <x-text-input
                wire:model="name"
                id="name"
                name="name"
                type="text"
                class="mt-1.5 h-9 w-full text-sm bg-[#000000] border-[#333333] text-white focus:border-[#333333] focus:ring-0 rounded-md"
                required
                autofocus
                autocomplete="name"
            />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('ui.profile_email')" class="text-sm text-white" />
            <x-text-input
                wire:model="email"
                id="email"
                name="email"
                type="email"
                class="mt-1.5 h-9 w-full text-sm bg-[#000000] border-[#333333] text-white focus:border-[#333333] focus:ring-0 rounded-md"
                required
                autocomplete="username"
            />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if (auth()->user() instanceof MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                <div class="mt-2">
                    <p class="text-sm text-[#CCCCCC]">
                        {{ __('Your email address is unverified.') }}
                        <button wire:click.prevent="sendVerification" type="button" class="underline text-[#F97316] hover:text-white">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>
                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 text-sm text-green-500">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center justify-end gap-3 mt-4">
            <x-app.components.loading-button
                type="submit"
                wire-target="updateProfileInformation"
                class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-white bg-[#F97316] hover:bg-[#ea580c] rounded-lg transition-colors disabled:cursor-wait min-w-[7rem]"
            >
                <x-lucide-save class="w-4 h-4 shrink-0" />
                <x-slot:label>{{ __('ui.profile_save') }}</x-slot:label>
            </x-app.components.loading-button>
        </div>
    </form>
</div>
