{{--
    Reusable dialog for password confirmation. Use it in a parent with Alpine x-data that has:
    password, passwordError, loading and methods for close and confirm.
    Slots: close (X button), footer (Close + Confirm buttons).
    On Enter in password field, dispatch to 'confirm-password'; parent listens with @confirm-password.window="confirmMethod()"
--}}
<x-app-dialog :title="__('ui.confirm_password_title')" maxWidth="md">
    <x-slot:close>
        {{ $close ?? '' }}
    </x-slot:close>
    <div>
        <p class="text-sm text-[#CCCCCC] mb-4">{{ __('ui.confirm_password_message_1') }} <br> {{ __('ui.confirm_password_message_2') }}</p>
        <input
            type="password"
            x-model="password"
            @keydown.enter.prevent="$dispatch('confirm-password')"
            class="w-full bg-[#000000] border-2 border-[#333333] rounded-xl py-3 px-4 text-white focus:border-[#F97316] focus:outline-none mt-1"
            placeholder="{{ __('ui.confirm_password_label') }}"
        />
        <p x-show="passwordError" x-text="passwordError" class="mt-2 text-sm text-red-400"></p>
    </div>
    <x-slot:footer>
        {{ $footer ?? '' }}
    </x-slot:footer>
</x-app-dialog>
