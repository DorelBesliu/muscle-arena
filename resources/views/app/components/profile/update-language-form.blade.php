<div class="flex items-center gap-2 bg-[#000000] border border-[#333333] rounded-xl p-1" wire:ignore>
    <form action="{{ route('user.locale.update') }}" method="POST" class="flex-1" x-data="{ loading: false }" x-on:submit="loading = true">
        @csrf
        <input type="hidden" name="locale" value="ro">
        <button type="submit" class="w-full px-4 py-2 rounded-lg transition-all text-sm font-medium inline-flex items-center justify-center gap-2 {{ app()->getLocale() === 'ro' ? 'bg-[#F97316] text-white' : 'text-[#CCCCCC] hover:text-white hover:bg-[#111111]' }}" :disabled="loading">
            <span x-show="loading" class="inline-flex items-center gap-2">
                <x-lucide-loader-2 class="w-4 h-4 animate-spin" />
                {{ __('ui.profile_romanian') }}
            </span>
            <span x-show="!loading" x-cloak>{{ __('ui.profile_romanian') }}</span>
        </button>
    </form>
    <form action="{{ route('user.locale.update') }}" method="POST" class="flex-1" x-data="{ loading: false }" x-on:submit="loading = true">
        @csrf
        <input type="hidden" name="locale" value="en">
        <button type="submit" class="w-full px-4 py-2 rounded-lg transition-all text-sm font-medium inline-flex items-center justify-center gap-2 {{ app()->getLocale() === 'en' ? 'bg-[#F97316] text-white' : 'text-[#CCCCCC] hover:text-white hover:bg-[#111111]' }}" :disabled="loading">
            <span x-show="loading" class="inline-flex items-center gap-2">
                <x-lucide-loader-2 class="w-4 h-4 animate-spin" />
                {{ __('ui.profile_english') }}
            </span>
            <span x-show="!loading" x-cloak>{{ __('ui.profile_english') }}</span>
        </button>
    </form>
    <form action="{{ route('user.locale.update') }}" method="POST" class="flex-1" x-data="{ loading: false }" x-on:submit="loading = true">
        @csrf
        <input type="hidden" name="locale" value="ru">
        <button type="submit" class="w-full px-4 py-2 rounded-lg transition-all text-sm font-medium inline-flex items-center justify-center gap-2 {{ app()->getLocale() === 'ru' ? 'bg-[#F97316] text-white' : 'text-[#CCCCCC] hover:text-white hover:bg-[#111111]' }}" :disabled="loading">
            <span x-show="loading" class="inline-flex items-center gap-2">
                <x-lucide-loader-2 class="w-4 h-4 animate-spin" />
                {{ __('ui.profile_russian') }}
            </span>
            <span x-show="!loading" x-cloak>{{ __('ui.profile_russian') }}</span>
        </button>
    </form>
</div>
