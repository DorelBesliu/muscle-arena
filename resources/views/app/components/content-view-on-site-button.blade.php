@props([
    'contentLocale' => 'ro',
    'hash' => '#about',
])

@php
    $url = route('home', ['locale' => $contentLocale]) . $hash;
@endphp
<a
    href="{{ $url }}"
    target="_blank"
    rel="noopener noreferrer"
    {{ $attributes->merge([
        'class' => 'inline-flex items-center gap-2 h-9 px-4 rounded-lg border-2 border-[#333333] hover:border-[#F97316] hover:bg-[#111111] text-white text-sm font-medium transition-colors',
    ]) }}
>
    <x-lucide-external-link class="w-4 h-4" />
    <span>{{ __('ui.sidebar_content_view_about') }}</span>
</a>
