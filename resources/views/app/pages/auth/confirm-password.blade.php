<main class="min-h-[calc(100vh-72px)] pt-[72px] flex flex-col items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="bg-[#111111] border-2 border-[#333333] rounded-3xl p-8 md:p-10">
            <div class="text-center mb-6">
                <h1 class="text-2xl font-black text-white mb-2">{{ __('ui.confirm_password_title') }}</h1>
                <p class="text-[#CCCCCC] text-sm">{{ __('ui.confirm_password_message_1') }} <br> {{ __('ui.confirm_password_message_2') }}</p>
            </div>
            <form action="{{ route('password.confirm.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="password" class="block text-sm font-medium text-white mb-2">{{ __('ui.confirm_password_label') }}</label>
                    <input type="password" id="password" name="password" required autocomplete="current-password"
                        class="w-full bg-[#000000] border-2 border-[#333333] rounded-xl py-3 px-4 text-white focus:border-[#F97316] focus:outline-none" />
                    @error('password')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror
                </div>
                <button type="submit" class="w-full bg-[#F97316] hover:bg-[#EF4444] text-white font-bold py-3 rounded-xl">{{ __('ui.confirm_password_button') }}</button>
            </form>
        </div>
    </div>
</main>
