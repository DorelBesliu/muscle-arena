<main class="min-h-[calc(100vh-72px)] pt-[72px] flex flex-col items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="bg-[#111111] border-2 border-[#333333] rounded-3xl p-8 md:p-10">
            @if(!$linkSent)
                {{-- Header --}}
                <div class="text-center mb-8">
                    <h1 class="text-4xl font-black text-white mb-2">
                        {{ __('ui.forgot_title') }}
                    </h1>
                    <p class="text-[#CCCCCC] mt-2">{{ __('ui.forgot_subtitle') }}</p>
                </div>

                {{-- Info Message --}}
                <div class="bg-[#F97316]/10 border border-[#F97316]/30 rounded-xl p-4 mb-6">
                    <p class="text-sm text-[#CCCCCC]">{{ __('ui.forgot_info_message') }}</p>
                </div>

                {{-- Form (Fortify: POST to password.email) --}}
                <form action="{{ route('password.email') }}" method="POST" class="space-y-6" x-data="{ loading: false }" x-on:submit="loading = true">
                    @csrf
                    <div>
                        <label for="forgot-email" class="block text-sm font-medium text-white mb-2">
                            {{ __('ui.forgot_email') }}
                        </label>
                        <div class="relative">
                            <x-lucide-mail class="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-[#CCCCCC]" />
                            <input
                                type="email"
                                id="forgot-email"
                                name="email"
                                value="{{ old('email') }}"
                                class="w-full bg-[#000000] border-2 border-[#333333] rounded-xl py-3 pl-12 pr-4 text-white placeholder-[#666666] focus:border-[#F97316] focus:outline-none transition-colors"
                                placeholder="{{ __('ui.forgot_email_placeholder') }}"
                                required
                                autofocus
                            />
                        </div>
                        @error('email')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <button
                        type="submit"
                        x-bind:disabled="loading"
                        class="w-full inline-flex items-center justify-center gap-2 bg-[#F97316] hover:bg-[#EF4444] disabled:opacity-70 disabled:cursor-not-allowed text-white font-bold py-4 rounded-xl transition-all hover:scale-105 shadow-xl"
                    >
                        <span x-show="!loading">{{ __('ui.forgot_send_reset_link') }}</span>
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
            @else
                {{-- Success State --}}
                <div class="text-center">
                    <div class="mb-6 flex justify-center">
                        <div class="w-20 h-20 bg-[#F97316]/10 rounded-full flex items-center justify-center">
                            <x-lucide-circle-check class="w-10 h-10 text-[#F97316]" />
                        </div>
                    </div>

                    <h1 class="text-4xl font-black text-white mb-4">
                        {{ __('ui.forgot_success_title') }}
                    </h1>

                    <p class="text-[#CCCCCC] mb-8">{{ __('ui.forgot_success_message') }}</p>

                    <a
                        href="{{ route('signin', ['locale' => app()->getLocale()]) }}"
                        wire:navigate
                        class="block w-full bg-[#F97316] hover:bg-[#EF4444] text-white font-bold py-4 rounded-xl transition-all hover:scale-105 shadow-xl text-center"
                    >
                        {{ __('ui.forgot_success_button') }}
                    </a>

                    {{-- Resend Option --}}
                    <div class="mt-6">
                        <p class="text-sm text-[#CCCCCC]">
                            {{ __('ui.forgot_resend_prompt') }}
                            <button
                                type="button"
                                wire:click="showFormAgain"
                                class="text-[#F97316] hover:text-[#EF4444] font-medium transition-colors"
                            >
                                {{ __('ui.forgot_resend') }}
                            </button>
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</main>
