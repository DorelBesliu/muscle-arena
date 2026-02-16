<?php

use App\Models\SiteContent;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component
{
    public string $aboutDescription = '';

    /** @var 'ro'|'en'|'ru' */
    public string $contentLocale = 'ro';

    /** @var array<int, array{id: int, title: string, description: string, sort_order: int, icon: string}> */
    public array $features = [];

    public ?int $editingFeatureId = null;

    public function mount(): void
    {
        $requested = request()->query('locale');
        if (in_array($requested, ['ro', 'en', 'ru'], true)) {
            $this->contentLocale = $requested;
        } else {
            $this->contentLocale = app()->getLocale();
            if (! in_array($this->contentLocale, ['ro', 'en', 'ru'], true)) {
                $this->contentLocale = 'ro';
            }
        }

        $about = SiteContent::get('about_' . $this->contentLocale);
        if (is_array($about)) {
            $this->aboutDescription = (string) ($about['description'] ?? '');
        } else {
            $previousLocale = app()->getLocale();
            app()->setLocale($this->contentLocale);
            $this->aboutDescription = (string) __('ui.about_description') . "\n\n" . (string) __('ui.about_description_2');
            app()->setLocale($previousLocale);
        }

        $iconsConfig = config('icons', ['default' => 'list-checks', 'list-checks' => 'List checks']);
        $allowedIcons = array_keys(array_filter($iconsConfig, fn ($v, $k) => $k !== 'default', ARRAY_FILTER_USE_KEY));
        $defaultIcon = $iconsConfig['default'] ?? 'list-checks';
        if (! in_array($defaultIcon, $allowedIcons, true)) {
            $defaultIcon = 'list-checks';
        }
        $stored = SiteContent::get('about_features_' . $this->contentLocale, []);
        if (is_array($stored)) {
            $this->features = array_values(array_map(function ($f) use ($allowedIcons, $defaultIcon) {
                $icon = (string) ($f['icon'] ?? $defaultIcon);
                if (! in_array($icon, $allowedIcons, true)) {
                    $icon = $defaultIcon;
                }
                return [
                    'id' => (int) ($f['id'] ?? 0),
                    'title' => (string) ($f['title'] ?? ''),
                    'description' => (string) ($f['description'] ?? ''),
                    'sort_order' => (int) ($f['sort_order'] ?? 0),
                    'icon' => $icon,
                ];
            }, $stored));
            usort($this->features, fn ($a, $b) => $a['sort_order'] <=> $b['sort_order']);
        }
    }

    public function saveAbout(): void
    {
        SiteContent::set('about_' . $this->contentLocale, [
            'title' => '',
            'description' => $this->aboutDescription,
        ]);
        $this->dispatch('toast', message: __('ui.admins_update_success'));
    }

    public function addFeature(): void
    {
        $maxId = 0;
        foreach ($this->features as $f) {
            if (($f['id'] ?? 0) > $maxId) {
                $maxId = (int) $f['id'];
            }
        }
        $iconsConfig = config('icons', ['default' => 'list-checks', 'list-checks' => 'List checks']);
        $allowedIcons = array_keys(array_filter($iconsConfig, fn ($v, $k) => $k !== 'default', ARRAY_FILTER_USE_KEY));
        $defaultIcon = $iconsConfig['default'] ?? 'list-checks';
        if (! in_array($defaultIcon, $allowedIcons, true)) {
            $defaultIcon = 'list-checks';
        }
        $this->features[] = [
            'id' => $maxId + 1,
            'title' => '',
            'description' => '',
            'sort_order' => count($this->features),
            'icon' => $defaultIcon,
        ];
        $this->editingFeatureId = $maxId + 1;
    }

    public function editFeature(int $id): void
    {
        $this->editingFeatureId = $id;
    }

    public function saveFeature(int $id): void
    {
        foreach ($this->features as $i => $f) {
            if (($f['id'] ?? 0) === $id) {
                $this->features[$i]['title'] = trim($this->features[$i]['title']);
                $this->features[$i]['description'] = trim($this->features[$i]['description']);
                break;
            }
        }
        $this->persistFeatures();
        $this->editingFeatureId = null;
        $this->dispatch('toast', message: __('ui.admins_update_success'));
    }

    public function deleteFeature(int $id): void
    {
        $this->features = array_values(array_filter($this->features, fn ($f) => ($f['id'] ?? 0) !== $id));
        foreach ($this->features as $i => $f) {
            $this->features[$i]['sort_order'] = $i;
        }
        $this->persistFeatures();
        if ($this->editingFeatureId === $id) {
            $this->editingFeatureId = null;
        }
        $this->dispatch('toast', message: __('ui.admins_delete_success'));
    }

    protected function persistFeatures(): void
    {
        SiteContent::set('about_features_' . $this->contentLocale, $this->features);
    }

    public function getHasAboutChangesProperty(): bool
    {
        $about = SiteContent::get('about_' . $this->contentLocale);
        $currentDesc = is_array($about) ? (string) ($about['description'] ?? '') : '';

        return $this->aboutDescription !== $currentDesc;
    }
}; ?>

<div class="min-h-screen bg-[#000000] text-white">
    <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="space-y-6">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-3 p-3 bg-[#111111] rounded-xl border-2 border-[#333333]">
                    <span class="text-sm text-[#999999]">{{ __('ui.content_editing_language') }}:</span>
                    <div class="flex gap-2">
                    <a
                        href="{{ route('content.about', ['locale' => 'ro']) }}"
                        class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all {{ $contentLocale === 'ro' ? 'bg-[#F97316] text-white' : 'bg-[#000000] text-[#666666] hover:text-white hover:bg-[#333333]' }}"
                    >
                        RO
                    </a>
                    <a
                        href="{{ route('content.about', ['locale' => 'en']) }}"
                        class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all {{ $contentLocale === 'en' ? 'bg-[#F97316] text-white' : 'bg-[#000000] text-[#666666] hover:text-white hover:bg-[#333333]' }}"
                    >
                        EN
                    </a>
                    <a
                        href="{{ route('content.about', ['locale' => 'ru']) }}"
                        class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all {{ $contentLocale === 'ru' ? 'bg-[#F97316] text-white' : 'bg-[#000000] text-[#666666] hover:text-white hover:bg-[#333333]' }}"
                    >
                        RU
                    </a>
                </div>
                </div>
                <a
                    href="{{ route('home', ['locale' => $contentLocale]) }}#about"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex items-center gap-2 h-9 px-4 rounded-lg border-2 border-[#333333] hover:border-[#F97316] hover:bg-[#111111] text-white text-sm font-medium transition-colors"
                >
                    <x-lucide-external-link class="w-4 h-4" />
                    <span>{{ __('ui.sidebar_content_view_about') }}</span>
                </a>
            </div>

            {{-- About block --}}
            <div class="bg-[#111111] border-2 border-[#333333] rounded-xl p-4 md:p-5">
                <div class="space-y-3">
                    <div>
                        <label for="about-description-editor" class="block text-sm font-medium text-[#CCCCCC] mb-1.5">{{ __('ui.content_about_description_label') }}</label>
                        <x-tiptap-editor wireProperty="aboutDescription" :content="$aboutDescription" />
                    </div>
                    <div class="flex justify-end pt-2">
                        <button
                            type="button"
                            wire:click="saveAbout"
                            wire:loading.attr="disabled"
                            wire:target="saveAbout"
                            class="inline-flex items-center justify-center gap-2 h-8 px-4 rounded-xl bg-[#F97316] hover:bg-[#ea580c] text-sm font-medium text-white min-w-[5rem] disabled:opacity-70 disabled:cursor-not-allowed whitespace-nowrap"
                        >
                            <span wire:loading.remove wire:target="saveAbout" class="shrink-0"><x-lucide-save class="w-3.5 h-3.5" /></span>
                            <span wire:loading wire:target="saveAbout" class="shrink-0 inline-flex items-center"><x-lucide-loader-2 class="w-3.5 h-3.5 animate-spin" /></span>
                            <span class="text-xs">{{ __('ui.admins_save') }}</span>
                        </button>
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
                <div class="space-y-3">
                    @if(count($this->features) === 0)
                        <div class="flex flex-col items-center justify-center py-12 px-4 bg-[#111111] outline outline-2 outline-[#333333] rounded-xl text-[#666666]">
                            <x-lucide-inbox class="w-12 h-12 mb-3 text-[#666666] opacity-50" />
                            <p class="text-sm">{{ __('ui.content_about_features_empty') }}</p>
                        </div>
                    @else
                    @foreach($this->features as $index => $feature)
                        @php $id = $feature['id']; $isEditing = $editingFeatureId === $id; @endphp
                        <div class="bg-[#111111] border-2 border-[#333333] rounded-xl" wire:key="feature-{{ $id }}">
                            <div class="p-3">
                                @if($isEditing)
                                    <div class="flex gap-3">
                                        <div class="flex items-start pt-2">
                                            <x-lucide-grip-vertical class="w-4 h-4 text-[#666666]" />
                                        </div>
                                        <div class="flex-1 space-y-2">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                @php
                                                    $iconsConfig = config('icons', ['default' => 'list-checks', 'list-checks' => 'List checks']);
                                                    $defaultIcon = $iconsConfig['default'] ?? 'list-checks';
                                                    $currentIconKey = $feature['icon'] ?? $defaultIcon;
                                                    if ($currentIconKey === 'default' || !isset($iconsConfig[$currentIconKey])) {
                                                        $currentIconKey = $defaultIcon;
                                                    }
                                                @endphp
                                                <div
                                                    class="relative"
                                                    x-data="{ open: false, search: '', featureIndex: {{ $index }} }"
                                                    x-on:click.outside="open = false"
                                                    x-on:keydown.escape.window="open = false"
                                                >
                                                    <button
                                                        type="button"
                                                        x-on:click="open = !open"
                                                        class="h-9 px-3 flex items-center gap-2 bg-[#000000] border-2 border-[#333333] rounded-xl text-sm text-white hover:border-[#444444] focus:border-[#F97316] focus:outline-none min-w-[10rem]"
                                                    >
                                                        <span class="w-5 h-5 flex items-center justify-center shrink-0 text-[#F97316]">
                                                            <x-dynamic-component :component="'lucide-' . $currentIconKey" class="w-4 h-4" />
                                                        </span>
                                                        <span class="truncate">{{ $iconsConfig[$currentIconKey] ?? $currentIconKey }}</span>
                                                        <x-lucide-chevron-down class="w-4 h-4 shrink-0 opacity-70" />
                                                    </button>
                                                    <div
                                                        x-show="open"
                                                        x-transition
                                                        x-cloak
                                                        class="absolute left-0 top-full mt-1 z-50 w-72 max-h-80 flex flex-col bg-[#111111] border-2 border-[#333333] rounded-xl shadow-xl"
                                                    >
                                                        <input
                                                            type="text"
                                                            x-model="search"
                                                            placeholder="{{ __('ui.content_about_icon_search') }}..."
                                                            class="m-2 px-3 py-2 bg-[#000000] border border-[#333333] rounded-lg text-sm text-white placeholder-[#666666] focus:border-[#F97316] focus:outline-none"
                                                        />
                                                        <div class="overflow-y-auto flex-1 min-h-0 p-2 space-y-0.5">
                                                            @foreach($iconsConfig as $iconKey => $iconLabel)
                                                                @if($iconKey === 'default') @continue @endif
                                                                <button
                                                                    type="button"
                                                                    data-label="{{ e($iconLabel) }}"
                                                                    data-icon-key="{{ e($iconKey) }}"
                                                                    x-show="!search || $el.dataset.label.toLowerCase().includes(search.toLowerCase())"
                                                                    x-on:click="$wire.set('features.' + featureIndex + '.icon', $el.dataset.iconKey); open = false"
                                                                    class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-left text-sm text-[#CCCCCC] hover:bg-[#333333] hover:text-white transition-colors"
                                                                >
                                                                    <span class="w-6 h-6 flex items-center justify-center shrink-0 text-[#F97316]">
                                                                        <x-dynamic-component :component="'lucide-' . $iconKey" class="w-4 h-4" />
                                                                    </span>
                                                                    <span class="truncate">{{ $iconLabel }}</span>
                                                                </button>
                                                            @endforeach
                                                        </div>
                                                    </div>
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
                                            <button
                                                type="button"
                                                wire:click="saveFeature({{ $id }})"
                                                wire:loading.attr="disabled"
                                                wire:target="saveFeature"
                                                class="h-8 w-8 flex items-center justify-center rounded-lg text-[#666666] hover:text-green-500 hover:bg-[#111111] transition-colors disabled:opacity-70 disabled:cursor-not-allowed"
                                            >
                                                <span wire:loading.remove wire:target="saveFeature">
                                                    <x-lucide-save class="w-4 h-4" />
                                                </span>
                                                <span wire:loading wire:target="saveFeature">
                                                    <x-lucide-loader-2 class="w-4 h-4 animate-spin" />
                                                </span>
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
                                @else
                                    <div class="flex gap-3">
                                        <div class="flex items-start pt-2">
                                            <x-lucide-grip-vertical class="w-4 h-4 text-[#666666]" />
                                        </div>
                                        @php
                                            $iconsList = config('icons', ['default' => 'list-checks', 'list-checks' => 'List checks']);
                                            $allowedIconsList = array_keys(array_filter($iconsList, fn ($v, $k) => $k !== 'default', ARRAY_FILTER_USE_KEY));
                                            $defaultIcon = $iconsList['default'] ?? 'list-checks';
                                            $featIcon = $feature['icon'] ?? $defaultIcon;
                                            if ($featIcon === 'default' || !in_array($featIcon, $allowedIconsList, true)) {
                                                $featIcon = $defaultIcon;
                                            }
                                        @endphp
                                        <div class="w-8 h-8 rounded-lg bg-[#333333] flex items-center justify-center shrink-0 text-[#F97316]">
                                            <x-dynamic-component :component="'lucide-' . $featIcon" class="w-4 h-4" />
                                        </div>
                                        <div class="flex-1 space-y-1 min-w-0">
                                            <h5 class="text-sm font-semibold text-white">{{ $feature['title'] ?: '—' }}</h5>
                                            <p class="text-sm text-[#999999]">{{ $feature['description'] ?: '—' }}</p>
                                        </div>
                                        <div class="flex gap-2">
                                            <button
                                                type="button"
                                                wire:click="editFeature({{ $id }})"
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
                                @endif
                            </div>
                        </div>
                    @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
