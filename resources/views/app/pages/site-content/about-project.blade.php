<div class="min-h-screen bg-[#000000] text-white">
    <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="space-y-6">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <x-app.components.content-editing-language-bar :content-locale="$contentLocale" route-name="content.about" />
                <x-app.components.content-view-on-site-button :content-locale="$contentLocale" hash="#about" />
            </div>

            {{-- About block --}}
            <div class="w-full rounded-xl">
                <div class="space-y-3">
                    <div class="w-full">
                        <label for="about-description-editor" class="block text-sm font-medium text-[#CCCCCC] mb-1.5">{{ __('ui.content_about_description_label') }}</label>
                        @php
                            $editorDescription = $aboutDescription;
                            if ($editorDescription !== '' && ! str_contains($editorDescription, '<')) {
                                $paragraphs = array_filter(array_map('trim', explode("\n\n", $editorDescription)));
                                $editorDescription = implode('', array_map(fn ($p) => '<p>' . e($p) . '</p>', $paragraphs));
                            }
                        @endphp
                        <x-tiptap-editor wireProperty="aboutDescription" :content="$editorDescription" class="w-full" />
                    </div>
                    <div class="flex justify-end pt-2">
                        <x-app.components.loading-button
                            wire-target="saveAbout"
                            wire:click="saveAbout"
                            icon-size="w-3.5 h-3.5"
                            class="h-8 px-4 rounded-xl bg-[#F97316] hover:bg-[#ea580c] text-sm font-medium text-white min-w-[5rem]"
                        >
                            <x-lucide-save class="w-3.5 h-3.5" />
                            <x-slot:label><span class="text-xs">{{ __('ui.admins_save') }}</span></x-slot:label>
                        </x-app.components.loading-button>
                    </div>
                </div>
            </div>

            {{-- Features (per language) --}}
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <label class="text-sm font-medium text-[#CCCCCC]">{{ __('ui.content_about_features') }}</label>
                    <button
                        type="button"
                        wire:click="addFeature"
                        class="inline-flex items-center gap-2 h-8 px-4 rounded-lg border-2 border-[#333333] hover:border-[#F97316] hover:bg-[#111111] text-white text-sm font-medium transition-colors"
                    >
                        <x-lucide-plus class="w-4 h-4" />
                        <span>{{ __('ui.content_about_add_feature') }}</span>
                    </button>
                </div>
                @php
                    $profileLocale = auth()->user()?->locale ?? session('locale', 'ro');
                    $profileLocale = in_array($profileLocale, ['ro', 'en', 'ru'], true) ? $profileLocale : 'ro';
                @endphp
                <div
                    class="space-y-3"
                    x-data
                    x-init="initAboutFeatureEditing({{ Js::from($this->iconsConfig) }}, $wire, {{ Js::from($profileLocale) }}); loadAboutFeatureIconDropdownContent()"
                >
                    @if(count($this->features) === 0)
                        <div class="flex flex-col items-center justify-center py-12 px-4 bg-[#111111] outline outline-2 outline-[#333333] rounded-xl text-[#666666]">
                            <x-lucide-inbox class="w-12 h-12 mb-3 text-[#666666] opacity-50" />
                            <p class="text-sm">{{ __('ui.content_about_features_empty') }}</p>
                        </div>
                    @else
                        <div id="about-features-sortable" class="space-y-3">
                        @foreach($this->features as $index => $feature)
                            @php $id = $feature['id']; @endphp
                            <div class="bg-[#111111] border-2 border-[#333333] rounded-xl about-feature-sortable-item" wire:key="feature-{{ $id }}" data-feature-id="{{ $id }}">
                                <div class="p-3">
                                    <div class="relative" data-about-feature-dropdown-anchor data-feature-id="{{ $id }}">
                                        <div>
                                    <template x-if="Alpine.store('aboutFeatureEditing')?.id === {{ $id }}">
                                        <div class="flex gap-3">
                                            <div class="flex items-start pt-2 about-feature-drag-handle cursor-grab active:cursor-grabbing" title="{{ __('ui.content_about_drag_to_reorder') }}">
                                                <x-lucide-grip-vertical class="w-4 h-4 text-[#666666]" />
                                            </div>
                                            <div class="flex-1 space-y-2">
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    @php
                                                        $defaultIconKey = $this->iconsConfig['default'] ?? 'list-checks';
                                                        $currentIconKey = $feature['icon'] ?? $defaultIconKey;
                                                        if ($currentIconKey === 'default' || !isset($this->iconsConfig[$currentIconKey])) {
                                                            $currentIconKey = $defaultIconKey;
                                                        }
                                                    @endphp
                                                    <input type="hidden" data-feature-icon="{{ $index }}" :value="Alpine.store('aboutFeatureEditing')?.id === {{ $id }} ? (Alpine.store('aboutFeatureEditing')?.editingIcon || '{{ $currentIconKey }}') : '{{ $currentIconKey }}'">
                                                    <div class="relative" x-data="aboutFeatureIconButton({{ $id }}, {{ Js::from($currentIconKey) }})">
                                                        <button
                                                            type="button"
                                                            x-on:click="openAboutFeatureIconDropdown({{ $id }}, {{ $index }}, initialKey, $event)"
                                                            class="h-9 px-3 flex items-center gap-2 bg-[#000000] border-2 border-[#333333] rounded-xl text-sm text-white hover:border-[#444444] focus:border-[#F97316] focus:outline-none min-w-[10rem]"
                                                        >
                                                            <span id="about-feature-icon-display-{{ $id }}" x-ref="iconDisplay" class="w-5 h-5 flex items-center justify-center shrink-0 text-[#F97316]">
                                                                <x-dynamic-component :component="'lucide-' . $currentIconKey" class="w-4 h-4" />
                                                            </span>
                                                            <span class="truncate" x-text="Alpine.store('aboutFeatureEditing')?.iconsConfig?.[Alpine.store('aboutFeatureEditing')?.id === featId ? (Alpine.store('aboutFeatureEditing')?.editingIcon || initialKey) : initialKey] || initialKey"></span>
                                                            <x-lucide-chevron-down class="w-4 h-4 shrink-0 opacity-70" />
                                                        </button>
                                                    </div>
                                                    <input
                                                        type="text"
                                                        wire:model="features.{{ $index }}.title"
                                                        placeholder="{{ __('ui.content_about_feature_title_placeholder') }}"
                                                        class="flex-1 h-9 px-3 bg-[#000000] border-2 border-[#333333] rounded-xl text-sm text-white placeholder-[#666666] focus:outline-none"
                                                    />
                                                </div>
                                                <textarea
                                                    wire:model="features.{{ $index }}.description"
                                                    rows="2"
                                                    placeholder="{{ __('ui.content_about_feature_description_placeholder') }}"
                                                    class="w-full bg-[#111111] border-2 border-[#333333] rounded-xl px-3 py-2 text-sm text-white placeholder-[#666666] focus:border-[#F97316] focus:outline-none resize-none"
                                                ></textarea>
                                            </div>
                                            <div class="flex flex-col gap-2">
                                                <x-app.components.loading-button
                                                    wire-target="saveFeature"
                                                    x-on:click="Alpine.store('aboutFeatureEditing').openIconDropdown = false"
                                                    wire:click="saveFeature({{ $id }}, document.querySelector('input[data-feature-icon=&quot;{{ $index }}&quot;]')?.value || 'list-checks')"
                                                    class="h-8 w-8 rounded-lg text-[#666666] hover:text-green-500 hover:bg-[#111111] transition-colors"
                                                >
                                                    <x-lucide-save class="w-4 h-4" />
                                                </x-app.components.loading-button>
                                                <button
                                                    type="button"
                                                    x-on:click="closeAboutFeatureIconDropdown(true)"
                                                    class="h-8 w-8 flex items-center justify-center rounded-lg text-[#666666] hover:text-[#F97316] hover:bg-[#111111] transition-colors"
                                                    title="{{ __('ui.admins_cancel') }}"
                                                >
                                                    <x-lucide-x class="w-4 h-4" />
                                                </button>
                                            </div>
                                        </div>
                                    </template>
                                    <div class="flex gap-3" x-show="Alpine.store('aboutFeatureEditing')?.id !== {{ $id }}" x-cloak>
                                            <div class="flex items-start pt-2 about-feature-drag-handle cursor-grab active:cursor-grabbing" title="{{ __('ui.content_about_drag_to_reorder') }}">
                                                <x-lucide-grip-vertical class="w-4 h-4 text-[#666666]" />
                                            </div>
                                            @php
                                                $allowedIconsList = array_keys(array_filter($this->iconsConfig, fn ($k) => $k !== 'default', ARRAY_FILTER_USE_KEY));
                                                $defaultIcon = $this->iconsConfig['default'] ?? 'list-checks';
                                                $featIcon = $feature['icon'] ?? $defaultIcon;
                                                if ($featIcon === 'default' || !in_array($featIcon, $allowedIconsList, true)) {
                                                    $featIcon = $defaultIcon;
                                                }
                                            @endphp
                                            <div class="w-8 h-8 rounded-lg bg-[#333333] flex items-center justify-center shrink-0 text-[#F97316]" id="about-feature-collapsed-icon-{{ $id }}">
                                                <x-dynamic-component :component="'lucide-' . $featIcon" class="w-4 h-4" />
                                            </div>
                                            <div class="flex-1 space-y-1 min-w-0">
                                                <h5 class="text-sm font-semibold text-white">{{ $feature['title'] ?: '—' }}</h5>
                                                <p class="text-sm text-[#999999]">{{ $feature['description'] ?: '—' }}</p>
                                            </div>
                                            <div class="flex gap-2">
                                                <button
                                                    type="button"
                                                    x-on:click="Alpine.store('aboutFeatureEditing').id = {{ $id }}; Alpine.store('aboutFeatureEditing').index = {{ $index }}; Alpine.store('aboutFeatureEditing').editingIcon = '{{ addslashes($feature['icon'] ?? 'list-checks') }}'"
                                                    class="h-8 w-8 flex items-center justify-center rounded-lg text-[#666666] hover:text-[#F97316] hover:bg-[#111111] transition-colors"
                                                >
                                                    <x-lucide-pencil class="w-4 h-4" />
                                                </button>
                                                <button
                                                    type="button"
                                                    wire:click="deleteFeature({{ $id }})"
                                                    wire:confirm="{{ __('ui.admins_delete_confirm') }}"
                                                    class="h-8 w-8 flex items-center justify-center rounded-lg text-[#666666] hover:text-red-500 hover:bg-[#111111] transition-colors"
                                                >
                                                    <x-lucide-trash-2 class="w-4 h-4" />
                                                </button>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        </div>
                        <x-about-feature-icon-dropdown :icons="$this->iconsConfig" />
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let aboutSortableInstance = null;
    function initAboutFeaturesSortable() {
        const container = document.getElementById('about-features-sortable');
        if (!container) return;
        if (aboutSortableInstance) {
            aboutSortableInstance.destroy();
            aboutSortableInstance = null;
        }
        aboutSortableInstance = new Sortable(container, {
            handle: '.about-feature-drag-handle',
            animation: 150,
            ghostClass: 'opacity-50',
            onEnd: function(evt) {
                const items = container.querySelectorAll('.about-feature-sortable-item');
                const orderedIds = Array.from(items).map(function(el) { return parseInt(el.getAttribute('data-feature-id'), 10); });
                const root = container.closest('[wire\\:id]');
                if (root && typeof Livewire !== 'undefined') {
                    const comp = Livewire.find(root.getAttribute('wire:id'));
                    if (comp && typeof comp.reorderFeatures === 'function') comp.reorderFeatures(orderedIds);
                }
            }
        });
    }
    initAboutFeaturesSortable();
    document.addEventListener('livewire:navigated', initAboutFeaturesSortable);
    Livewire.hook('morph.updated', ({ el }) => {
        if (el.querySelector && el.querySelector('#about-features-sortable')) initAboutFeaturesSortable();
        if (el.id === 'about-features-sortable') initAboutFeaturesSortable();
    });
});
</script>
@endpush
