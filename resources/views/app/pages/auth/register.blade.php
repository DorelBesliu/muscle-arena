<main class="min-h-[calc(100vh-72px)] pt-[72px] flex flex-col items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="bg-[#111111] border-2 border-[#333333] rounded-3xl p-8 md:p-10">
            <div class="text-center mb-8">
                <h1 class="text-4xl font-black text-white mb-2">{{ __('ui.register_title') }}</h1>
                <p class="text-[#CCCCCC] mt-2">{{ __('ui.register_subtitle') }}</p>
            </div>

            <form action="{{ route('register.store') }}" method="POST" class="space-y-6" x-data="{ loading: false }" x-on:submit="loading = true">
                @csrf
                <div>
                    <label for="name" class="block text-sm font-medium text-white mb-2">{{ __('ui.register_name') }}</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                        class="w-full bg-[#000000] border-2 border-[#333333] rounded-xl py-3 px-4 text-white placeholder-[#666666] focus:border-[#F97316] focus:outline-none transition-colors" />
                    @error('name')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-white mb-2">{{ __('ui.register_email') }}</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                        class="w-full bg-[#000000] border-2 border-[#333333] rounded-xl py-3 px-4 text-white placeholder-[#666666] focus:border-[#F97316] focus:outline-none transition-colors" />
                    @error('email')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-white mb-2">{{ __('ui.register_password') }}</label>
                    <input type="password" id="password" name="password" required autocomplete="new-password"
                        class="w-full bg-[#000000] border-2 border-[#333333] rounded-xl py-3 px-4 text-white placeholder-[#666666] focus:border-[#F97316] focus:outline-none transition-colors" />
                    @error('password')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-white mb-2">{{ __('ui.register_confirm') }}</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password"
                        class="w-full bg-[#000000] border-2 border-[#333333] rounded-xl py-3 px-4 text-white placeholder-[#666666] focus:border-[#F97316] focus:outline-none transition-colors" />
                </div>
                <button type="submit" x-bind:disabled="loading"
                    class="w-full inline-flex items-center justify-center gap-2 bg-[#F97316] hover:bg-[#EF4444] disabled:opacity-70 text-white font-bold py-4 rounded-xl transition-all shadow-xl">
                    <span x-show="!loading">{{ __('ui.register_button') }}</span>
                    <span x-show="loading" x-cloak><x-lucide-loader-2 class="w-6 h-6 animate-spin" /></span>
                </button>
            </form>

            <p class="mt-6 text-center text-sm text-[#CCCCCC]">
                <a href="{{ route('signin', ['locale' => app()->getLocale()]) }}" wire:navigate class="text-[#F97316] hover:text-[#EF4444]">{{ __('ui.register_already') }}</a>
            </p>
        </div>
    </div>
</main>
