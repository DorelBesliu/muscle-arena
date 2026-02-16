@props([
    'wireProperty',
    'content' => '',
    'placeholder' => null,
])

@php
    $initialContentId = 'tiptap-initial-' . $wireProperty;
    $placeholder = $placeholder ?? __('ui.editor_placeholder');
@endphp

<div
    wire:ignore
    x-data="tiptapEditor({ wireProperty: {{ json_encode($wireProperty) }}, initialContentId: {{ json_encode($initialContentId) }}, placeholder: {{ json_encode($placeholder) }} })"
    class="tiptap-wrapper-dark"
    {{ $attributes->except(['wireProperty', 'content', 'placeholder']) }}
>
    <textarea id="{{ $initialContentId }}" class="hidden" readonly>{!! str_replace('</textarea>', '<\/textarea>', $content) !!}</textarea>
    <div class="tiptap-toolbar flex flex-wrap items-center gap-0.5 p-1.5 bg-[#111111] border-2 border-[#333333] border-b-0 rounded-t-xl">
        <button type="button" x-on:click="toggleBold()" :class="{ 'bg-[#333333] text-[#F97316]': isActive('bold') }" class="h-8 w-8 flex items-center justify-center rounded-lg text-[#CCCCCC] hover:bg-[#1a1a1a] hover:text-white transition-colors" title="{{ __('ui.editor_bold') }}">
            <x-lucide-type class="w-4 h-4" />
        </button>
        <button type="button" x-on:click="toggleItalic()" :class="{ 'bg-[#333333] text-[#F97316]': isActive('italic') }" class="h-8 w-8 flex items-center justify-center rounded-lg text-[#CCCCCC] hover:bg-[#1a1a1a] hover:text-white transition-colors" title="{{ __('ui.editor_italic') }}">
            <x-lucide-italic class="w-4 h-4" />
        </button>
        <button type="button" x-on:click="toggleStrike()" :class="{ 'bg-[#333333] text-[#F97316]': isActive('strike') }" class="h-8 w-8 flex items-center justify-center rounded-lg text-[#CCCCCC] hover:bg-[#1a1a1a] hover:text-white transition-colors" title="{{ __('ui.editor_strike') }}">
            <x-lucide-strikethrough class="w-4 h-4" />
        </button>
        <span class="w-px h-5 bg-[#333333] mx-0.5" aria-hidden="true"></span>
        <button type="button" x-on:click="toggleHeading(1)" :class="{ 'bg-[#333333] text-[#F97316]': isActive('heading', { level: 1 }) }" class="h-8 w-8 flex items-center justify-center rounded-lg text-[#CCCCCC] hover:bg-[#1a1a1a] hover:text-white transition-colors" title="{{ __('ui.editor_heading') }}">
            <x-lucide-heading-1 class="w-4 h-4" />
        </button>
        <button type="button" x-on:click="setParagraph()" :class="{ 'bg-[#333333] text-[#F97316]': isActive('paragraph') }" class="h-8 w-8 flex items-center justify-center rounded-lg text-[#CCCCCC] hover:bg-[#1a1a1a] hover:text-white transition-colors" title="{{ __('ui.editor_paragraph') }}">
            <x-lucide-pilcrow class="w-4 h-4" />
        </button>
        <button type="button" x-on:click="setLink()" :class="{ 'bg-[#333333] text-[#F97316]': isActive('link') }" class="h-8 w-8 flex items-center justify-center rounded-lg text-[#CCCCCC] hover:bg-[#1a1a1a] hover:text-white transition-colors" title="{{ __('ui.editor_link') }}">
            <x-lucide-link class="w-4 h-4" />
        </button>
        <span class="w-px h-5 bg-[#333333] mx-0.5" aria-hidden="true"></span>
        <button type="button" x-on:click="toggleBulletList()" :class="{ 'bg-[#333333] text-[#F97316]': isActive('bulletList') }" class="h-8 w-8 flex items-center justify-center rounded-lg text-[#CCCCCC] hover:bg-[#1a1a1a] hover:text-white transition-colors" title="{{ __('ui.editor_bullets') }}">
            <x-lucide-list class="w-4 h-4" />
        </button>
        <button type="button" x-on:click="toggleOrderedList()" :class="{ 'bg-[#333333] text-[#F97316]': isActive('orderedList') }" class="h-8 w-8 flex items-center justify-center rounded-lg text-[#CCCCCC] hover:bg-[#1a1a1a] hover:text-white transition-colors" title="{{ __('ui.editor_numbers') }}">
            <x-lucide-list-ordered class="w-4 h-4" />
        </button>
        <button type="button" x-on:click="toggleBlockquote()" :class="{ 'bg-[#333333] text-[#F97316]': isActive('blockquote') }" class="h-8 w-8 flex items-center justify-center rounded-lg text-[#CCCCCC] hover:bg-[#1a1a1a] hover:text-white transition-colors" title="{{ __('ui.editor_quote') }}">
            <x-lucide-quote class="w-4 h-4" />
        </button>
        <button type="button" x-on:click="setCode()" :class="{ 'bg-[#333333] text-[#F97316]': isActive('code') }" class="h-8 w-8 flex items-center justify-center rounded-lg text-[#CCCCCC] hover:bg-[#1a1a1a] hover:text-white transition-colors" title="{{ __('ui.editor_code') }}">
            <x-lucide-code class="w-4 h-4" />
        </button>
    </div>
    <div x-ref="element" class="tiptap-editor-wrapper rounded-b-xl border-2 border-[#333333] bg-[#000000] px-3 py-2"></div>
</div>
