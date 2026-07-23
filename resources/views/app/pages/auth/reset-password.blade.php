<main class="min-h-[calc(100vh-72px)] pt-[72px] flex flex-col items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="bg-[#111111] border-2 border-[#333333] rounded-3xl p-8 md:p-10">
            {{-- Header --}}
            <div class="text-center mb-8">
                <h1 class="text-4xl font-black text-white mb-2">
                    {{ __('ui.reset_page_title') }}
                </h1>
                <p class="text-[#CCCCCC] mt-2">{{ __('ui.reset_page_subtitle') }}</p>
            </div>

            <form action="{{ route('password.update') }}" method="POST" class="space-y-6" x-data="{ loading: false }" x-on:submit="loading = true">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}" />
                {{-- Email (read-only from link) --}}
                <div>
                    <label for="reset-email" class="block text-sm font-medium text-white mb-2">
                        {{ __('ui.reset_page_email') }}
                    </label>
                    <div class="relative">
                        <x-lucide-mail class="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-[#CCCCCC]" />
                        <input
                            type="email"
                            id="reset-email"
                            name="email"
                            value="{{ old('email', $email) }}"
                            class="w-full bg-[#000000] border-2 border-[#333333] rounded-xl py-3 pl-12 pr-4 text-white placeholder-[#666666] focus:border-[#F97316] focus:outline-none transition-colors"
                            required
                            readonly
                            tabindex="-1"
                        />
                    </div>
                    @error('email')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div>
                    <label for="reset-password" class="block text-sm font-medium text-white mb-2">
                        {{ __('ui.reset_page_password') }}
                    </label>
                    <div class="relative">
                        <x-lucide-lock class="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-[#CCCCCC]" />
                        <input
                            type="password"
                            id="reset-password"
                            name="password"
                            class="w-full bg-[#000000] border-2 border-[#333333] rounded-xl py-3 pl-12 pr-4 text-white placeholder-[#666666] focus:border-[#F97316] focus:outline-none transition-colors"
                            placeholder="••••••••"
                            required
                            autocomplete="new-password"
                        />
                    </div>
                    @error('password')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Confirm Password --}}
                <div>
                    <label for="reset-password-confirmation" class="block text-sm font-medium text-white mb-2">
                        {{ __('ui.reset_page_confirm') }}
                    </label>
                    <div class="relative">
                        <x-lucide-lock class="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-[#CCCCCC]" />
                        <input
                            type="password"
                            id="reset-password-confirmation"
                            name="password_confirmation"
                            class="w-full bg-[#000000] border-2 border-[#333333] rounded-xl py-3 pl-12 pr-4 text-white placeholder-[#666666] focus:border-[#F97316] focus:outline-none transition-colors"
                            placeholder="••••••••"
                            required
                            autocomplete="new-password"
                        />
                    </div>
                    @error('password_confirmation')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <button
                    type="submit"
                    x-bind:disabled="loading"
                    class="w-full inline-flex items-center justify-center gap-2 bg-[#F97316] hover:bg-[#EF4444] disabled:opacity-70 disabled:cursor-not-allowed text-white font-bold py-4 rounded-xl transition-all hover:scale-105 shadow-xl"
                >
                    <span x-show="!loading">{{ __('ui.reset_page_button') }}</span>
                    <span x-show="loading" x-cloak class="inline-flex items-center justify-center">
                        <x-lucide-loader-2 class="w-6 h-6 animate-spin" />
                    </span>
                </button>
            </form>

            {{-- Back to Sign In --}}
            <div class="mt-8 text-center">
                <a
                    href="{{ route('signin', ['locale' => app()->getLocale()]) }}"
                    wire:navigate
                    class="inline-flex items-center gap-2 text-[#CCCCCC] hover:text-[#F97316] transition-colors"
                >
                    <x-lucide-arrow-left class="w-4 h-4" />
                    <span>{{ __('ui.forgot_back_to_sign_in') }}</span>
                </a>
            </div>
        </div>
    </div>
</main>
