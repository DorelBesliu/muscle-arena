{{--
    Reusable dialog for app panel (dark theme).
    Usage: render when open (e.g. @if($showModal)) and pass title, optional close slot, default slot (body), optional footer slot.
    Props: title (optional), maxWidth ('sm'|'md'|'lg'|'xl'|'2xl').
    Slots: close (e.g. button with wire:click to close), default (body), footer (optional), header (optional full custom header).
--}}
@props([
    'title' => null,
    'maxWidth' => 'lg',
])

@php
$maxWidthClass = match($maxWidth) {
    'sm' => 'max-w-sm',
    'md' => 'max-w-md',
    'lg' => 'max-w-lg',
    'xl' => 'max-w-xl',
    '2xl' => 'max-w-2xl',
    default => 'max-w-lg',
};
@endphp

<div
    class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4"
    role="dialog"
    aria-modal="true"
    {{ $attributes->except(['title', 'maxWidth'])->merge(['aria-label' => $title]) }}
>
    <div class="w-full {{ $maxWidthClass }} bg-[#111111] border-2 border-[#333333] rounded-xl shadow-xl">
        <div class="p-6">
            @if(isset($header))
                {{ $header }}
            @elseif($title || isset($close))
                <div class="flex justify-between items-center mb-6">
                    @if($title)
                        <h3 class="text-xl font-bold text-white">{{ $title }}</h3>
                    @endif
                    @if(isset($close))
                        <div class="{{ $title ? '' : 'ml-auto' }}">{{ $close }}</div>
                    @endif
                </div>
            @endif

            {{ $slot }}

            @if(isset($footer))
                <div class="flex justify-between items-center gap-2 mt-6">{{ $footer }}</div>
            @endif
        </div>
    </div>
</div>
