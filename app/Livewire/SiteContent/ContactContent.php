<?php

namespace App\Livewire\SiteContent;

use App\Models\SiteContent;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ContactContent extends Component
{
    /** @var array<int, array{id: string, type: string, icon: string, description: string}> */
    public array $contactCards = [];

    private const CARD_TYPES = ['phone', 'email'];

    private const DEFAULT_ICONS = ['phone' => 'phone', 'email' => 'mail'];

    public ?string $editingContactCardId = null;

    /** @var 'ro'|'en'|'ru' */
    public string $contentLocale = 'ro';

    /** @var array<string, string> */
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
                'icon' => $card['icon'] ?? self::DEFAULT_ICONS[$card['type']] ?? 'phone',
                'description' => $card['description'] ?? '',
            ];
        }
        SiteContent::set('contact_' . $this->contentLocale, ['cards' => $cards]);
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

    public function render()
    {
        return view('app.pages.site-content.contact');
    }
}
