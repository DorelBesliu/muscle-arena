@props([
    /** Livewire action name for wire:target and wire:loading (e.g. "saveAbout", "savePhase") */
    'wireTarget' => null,
    /** Icon size class for spinner and for slot when icon-only (e.g. "w-4 h-4", "w-3.5 h-3.5") */
    'iconSize' => 'w-4 h-4',
])

<button
    type="{{ $attributes->get('type', 'button') }}"
    wire:loading.attr="disabled"
    @if($wireTarget) wire:target="{{ $wireTarget }}" @endif
    {{ $attributes->except('type')->merge([
        'class' => 'inline-flex items-center justify-center gap-2 disabled:opacity-70 disabled:cursor-not-allowed whitespace-nowrap',
    ]) }}
>
    @if($wireTarget)
        <span wire:loading.remove wire:target="{{ $wireTarget }}" class="shrink-0">{{ $slot }}</span>
        <span wire:loading wire:target="{{ $wireTarget }}" class="shrink-0 inline-flex items-center"><x-lucide-loader-2 class="{{ $iconSize }} animate-spin" /></span>
    @else
        <span class="shrink-0 inline-flex items-center gap-2">{{ $slot }}</span>
    @endif
    @isset($label)
        <span class="shrink-0">{{ $label }}</span>
    @endisset
</button>
