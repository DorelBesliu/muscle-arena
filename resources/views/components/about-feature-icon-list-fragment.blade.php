@props([
    'icons' => [],
])

@php
    $icons = is_array($icons) ? $icons : [];
@endphp

@foreach($icons as $iconKey => $iconLabel)
    @if($iconKey === 'default') @continue @endif
    <button
        type="button"
        data-label="{{ e($iconLabel) }}"
        data-icon-key="{{ e($iconKey) }}"
        onclick="(function(){ var s = window.Alpine && window.Alpine.store && window.Alpine.store('aboutFeatureEditing'); if(s){ var key = this.dataset.iconKey; s.editingIcon = key; var iconEl = document.getElementById('about-feature-icon-' + key); if(iconEl){ window._aboutFeatureIconCache = window._aboutFeatureIconCache || {}; window._aboutFeatureIconCache[key] = iconEl.innerHTML; var display = document.getElementById('about-feature-icon-display-' + s.id); if(display) display.innerHTML = iconEl.innerHTML; } } if(typeof window.closeAboutFeatureIconDropdown === 'function') window.closeAboutFeatureIconDropdown(false); }).call(this)"
        class="about-feature-icon-option w-full flex items-center gap-2 px-3 py-2 rounded-lg text-left text-sm text-[#CCCCCC] hover:bg-[#333333] hover:text-white transition-colors"
    >
        <span id="about-feature-icon-{{ $iconKey }}" class="w-6 h-6 flex items-center justify-center shrink-0 text-[#F97316]">
            <x-dynamic-component :component="'lucide-' . $iconKey" class="w-4 h-4" />
        </span>
        <span class="truncate">{{ $iconLabel }}</span>
    </button>
@endforeach
