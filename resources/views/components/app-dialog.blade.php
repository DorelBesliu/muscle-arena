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

{{-- Full screen only on mobile (max-md); from md up: centered box with padding --}}
<div
    class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4 max-md:p-0 overflow-y-auto overscroll-contain"
    role="dialog"
    aria-modal="true"
    {{ $attributes->except(['title', 'maxWidth'])->merge(['aria-label' => $title]) }}
>
    <div class="w-full {{ $maxWidthClass }} max-md:max-w-none max-md:h-full md:max-h-[min(90vh,calc(100vh-2rem))] bg-[#111111] border-2 border-[#333333] rounded-xl max-md:border-0 max-md:rounded-none shadow-xl flex flex-col my-auto">
        <div class="p-4 md:p-6 py-2 flex flex-col min-h-0 flex-1 overflow-hidden">
            @if(isset($header))
                <div class="flex-shrink-0">{{ $header }}</div>
            @elseif($title || isset($close))
                <div class="flex justify-between items-center py-1 mb-2 flex-shrink-0">
                    @if($title)
                        <h3 class="text-xl font-bold text-white">{{ $title }}</h3>
                    @endif
                    @if(isset($close))
                        <div class="{{ $title ? '' : 'ml-auto' }}">{{ $close }}</div>
                    @endif
                </div>
            @endif

            <div class="min-h-0 flex-1 overflow-y-auto -mx-4 px-4 md:-mx-6 md:px-6 overscroll-contain">
                {{ $slot }}
            </div>

            @if(isset($footer))
                <div class="sticky bottom-0 left-0 right-0 flex justify-between items-center gap-2 pt-2 flex-shrink-0 border-t border-[#333333] bg-[#111111] -mx-4 px-4 md:-mx-6 md:px-6 md:mt-6 md:pt-6">{{ $footer }}</div>
            @endif
        </div>
    </div>
</div>
