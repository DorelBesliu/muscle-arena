@props([
    'contentLocale' => 'ro',
    'routeName' => 'content.about',
])

<div class="flex items-center gap-3 p-3 bg-[#111111] rounded-xl border-2 border-[#333333]">
    <span class="text-sm text-[#999999]">{{ __('ui.content_editing_language') }}:</span>
    <div class="flex gap-2">
        <a
            href="{{ route($routeName, ['locale' => 'ro']) }}"
            wire:navigate
            class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all {{ $contentLocale === 'ro' ? 'bg-[#F97316] text-white' : 'bg-[#000000] text-[#666666] hover:text-white hover:bg-[#333333]' }}"
        >
            RO
        </a>
        <a
            href="{{ route($routeName, ['locale' => 'en']) }}"
            wire:navigate
            class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all {{ $contentLocale === 'en' ? 'bg-[#F97316] text-white' : 'bg-[#000000] text-[#666666] hover:text-white hover:bg-[#333333]' }}"
        >
            EN
        </a>
        <a
            href="{{ route($routeName, ['locale' => 'ru']) }}"
            wire:navigate
            class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all {{ $contentLocale === 'ru' ? 'bg-[#F97316] text-white' : 'bg-[#000000] text-[#666666] hover:text-white hover:bg-[#333333]' }}"
        >
            RU
        </a>
    </div>
</div>
