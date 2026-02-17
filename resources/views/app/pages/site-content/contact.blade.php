<?php

use App\Models\SiteContent;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component
{
    /** @var array<int, array{id: string, type: string, icon: string, description: string}> */
    public array $contactCards = [];

    private const CARD_TYPES = ['phone', 'email'];

    private const DEFAULT_ICONS = ['phone' => 'phone', 'email' => 'mail'];

    /** @var string|null ID-ul cardului în editare */
    public ?string $editingContactCardId = null;

    /** @var 'ro'|'en'|'ru' */
    public string $contentLocale = 'ro';

    /** @var array<string, string> icon key => label (pentru select) */
    public array $iconsConfig = [];

    public function mount(): void
    {
        $this->contentLocale = app()->getLocale() ?? 'ro';
        $requested = request()->query('locale');
        if (in_array($requested, ['ro', 'en', 'ru'], true)) {
            $this->contentLocale = $requested;
        }

        $defaultIcon = config('icons.default', 'list-checks');
        $iconsRaw = config('icons', ['default' => 'list-checks']);
        $this->iconsConfig = [];
        foreach ($iconsRaw as $key => $defaultLabel) {
            if ($key === 'default') {
                $this->iconsConfig[$key] = $defaultLabel;
                continue;
            }
            $label = __('icons.' . $key);
            $this->iconsConfig[$key] = ($label === 'icons.' . $key) ? $defaultLabel : $label;
        }
        if (! isset($this->iconsConfig[$defaultIcon])) {
            $defaultIcon = 'phone';
        }
        if (! isset($this->iconsConfig[$defaultIcon])) {
            $defaultIcon = array_key_first(array_filter($this->iconsConfig, fn ($k) => $k !== 'default', ARRAY_FILTER_USE_KEY)) ?: 'list-checks';
        }

        $data = SiteContent::get('contact_' . $this->contentLocale);
        $loadedByType = [];
        if (is_array($data) && isset($data['cards']) && is_array($data['cards'])) {
            foreach ($data['cards'] as $card) {
                if (is_array($card) && isset($card['type']) && in_array($card['type'], self::CARD_TYPES, true)) {
                    $icon = (string) ($card['icon'] ?? self::DEFAULT_ICONS[$card['type']] ?? $defaultIcon);
                    if (! isset($this->iconsConfig[$icon]) || $icon === 'default') {
                        $icon = self::DEFAULT_ICONS[$card['type']] ?? $defaultIcon;
                    }
                    $loadedByType[$card['type']] = [
                        'id' => (string) ($card['id'] ?? Str::ulid()),
                        'type' => $card['type'],
                        'icon' => $icon,
                        'description' => (string) ($card['description'] ?? $card['value'] ?? ''),
                    ];
                }
            }
        }

        $cards = [];
        foreach (self::CARD_TYPES as $type) {
            $cards[] = $loadedByType[$type] ?? [
                'id' => (string) Str::ulid(),
                'type' => $type,
                'icon' => self::DEFAULT_ICONS[$type] ?? $defaultIcon,
                'description' => '',
            ];
        }
        $this->contactCards = $cards;
    }

    public function getTypeLabel(string $type): string
    {
        return match ($type) {
            'phone' => __('ui.contactPhone'),
            'email' => __('ui.contactEmail'),
            default => $type,
        };
    }

    public function persistCards(): void
    {
        $cards = [];
        foreach ($this->contactCards as $card) {
            $cards[] = [
                'id' => $card['id'],
                'type' => $card['type'],
                'icon' => self::DEFAULT_ICONS[$card['type']] ?? 'phone',
                'description' => $card['description'] ?? '',
            ];
        }
        SiteContent::set('contact_' . $this->contentLocale, [
            'cards' => $cards,
        ]);
    }

    public function editContactCard(string $id): void
    {
        $this->editingContactCardId = $id;
    }

    public function cancelEditContactCard(): void
    {
        $this->editingContactCardId = null;
    }

    public function saveContactCard(string $id): void
    {
        $this->editingContactCardId = null;
        $this->persistCards();
        $this->dispatch('toast', message: __('ui.admins_update_success'));
    }

    public function moveContactCardUp(string $id): void
    {
        $index = null;
        foreach (array_keys($this->contactCards) as $i) {
            if ($this->contactCards[$i]['id'] === $id) {
                $index = $i;
                break;
            }
        }
        if ($index === null || $index === 0) {
            return;
        }
        $swap = $this->contactCards[$index];
        $this->contactCards[$index] = $this->contactCards[$index - 1];
        $this->contactCards[$index - 1] = $swap;
        $this->persistCards();
    }

    public function moveContactCardDown(string $id): void
    {
        $index = null;
        foreach (array_keys($this->contactCards) as $i) {
            if ($this->contactCards[$i]['id'] === $id) {
                $index = $i;
                break;
            }
        }
        if ($index === null || $index === array_key_last($this->contactCards)) {
            return;
        }
        $swap = $this->contactCards[$index];
        $this->contactCards[$index] = $this->contactCards[$index + 1];
        $this->contactCards[$index + 1] = $swap;
        $this->persistCards();
    }
}; ?>

<div class="min-h-screen bg-[#000000] text-white">
    <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="space-y-6 mb-8">
            <div class="space-y-4">
                <div class="flex items-center justify-between gap-4">
                    <label class="text-sm font-medium text-[#CCCCCC]">{{ __('ui.contactCards') }}</label>
                    <x-app.components.content-view-on-site-button :content-locale="$contentLocale" hash="#contact" class="!h-8" />
                </div>
                <div class="space-y-3">
                    @foreach($contactCards as $index => $card)
                        @php
                            $iconKey = $card['icon'] ?? config('icons.default', 'phone');
                            if (!isset($iconsConfig[$iconKey]) || $iconKey === 'default') {
                                $iconKey = config('icons.default', 'phone');
                            }
                        @endphp
                        <div class="bg-[#111111] border-2 border-[#333333] rounded-xl" wire:key="contact-card-{{ $card['id'] }}">
                            <div class="p-3">
                                @if($editingContactCardId === $card['id'])
                                    <div class="flex gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-[#333333] flex items-center justify-center shrink-0 text-[#F97316]">
                                            <x-dynamic-component :component="'lucide-' . $iconKey" class="w-4 h-4" />
                                        </div>
                                        <div class="flex-1 space-y-2">
                                            <h5 class="text-sm font-semibold text-white">{{ $this->getTypeLabel($card['type']) }}</h5>
                                            <input
                                                type="text"
                                                wire:model="contactCards.{{ $index }}.description"
                                                class="w-full h-9 px-3 bg-[#111111] border-2 border-[#333333] rounded-xl text-sm text-white placeholder-[#666666] focus:border-[#F97316] focus:outline-none"
                                                placeholder="{{ __('ui.contactCardDescription') }}"
                                            />
                                        </div>
                                        <div class="flex flex-col gap-2">
                                            <x-app.components.loading-button
                                                wire-target="saveContactCard"
                                                wire:click="saveContactCard('{{ $card['id'] }}')"
                                                class="h-8 w-8 rounded-lg text-[#666666] hover:text-green-500 hover:bg-[#111111] transition-colors"
                                                title="{{ __('ui.content_roadmap_save') }}"
                                            >
                                                <x-lucide-save class="w-4 h-4" />
                                            </x-app.components.loading-button>
                                            <button
                                                type="button"
                                                wire:click="cancelEditContactCard"
                                                class="h-8 w-8 flex items-center justify-center rounded-lg text-[#666666] hover:text-[#F97316] hover:bg-[#111111] transition-colors"
                                                title="{{ __('ui.admins_cancel') }}"
                                            >
                                                <x-lucide-x class="w-4 h-4" />
                                            </button>
                                        </div>
                                    </div>
                                @else
                                    <div class="flex gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-[#333333] flex items-center justify-center shrink-0 text-[#F97316]">
                                            <x-dynamic-component :component="'lucide-' . $iconKey" class="w-4 h-4" />
                                        </div>
                                        <div class="flex-1 space-y-1 min-w-0">
                                            <h5 class="text-sm font-semibold text-white">{{ $this->getTypeLabel($card['type']) }}</h5>
                                            <p class="text-sm text-[#999999]">{{ $card['description'] ?: '—' }}</p>
                                        </div>
                                        <button
                                            type="button"
                                            wire:click="editContactCard('{{ $card['id'] }}')"
                                            class="h-8 w-8 flex items-center justify-center rounded-lg text-[#666666] hover:text-[#F97316] hover:bg-[#111111] transition-colors flex-shrink-0"
                                        >
                                            <x-lucide-pencil class="w-4 h-4" />
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
