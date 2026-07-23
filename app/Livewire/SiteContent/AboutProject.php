<?php

namespace App\Livewire\SiteContent;

use App\Models\PageAboutProjectFeature;
use App\Models\PageAboutProjectFeatureTranslation;
use App\Models\SiteContent;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class AboutProject extends Component
{
    public string $aboutDescription = '';

    /** @var 'ro'|'en'|'ru' */
    public string $contentLocale = 'ro';

    public array $features = [];

    /** @var array<string, string> Icon key => label (from config, set once in mount). */
    public array $iconsConfig = [];

    public function mount(): void
    {
        $this->contentLocale = app()->getLocale() ?? 'ro';

        $requested = request()->query('locale');
        if (in_array($requested, ['ro', 'en', 'ru'], true)) {
            $this->contentLocale = $requested;
        }

        $about = SiteContent::get('about_' . $this->contentLocale);

        $this->aboutDescription = is_array($about) ? (string) ($about['description'] ?? '') : '';

        $profileLocale = auth()->user()?->locale ?? session('locale', 'ro');
        $profileLocale = in_array($profileLocale, ['ro', 'en', 'ru'], true) ? $profileLocale : 'ro';
        $previousLocale = app()->getLocale();
        app()->setLocale($profileLocale);
        $iconsRaw = config('icons', ['default' => 'list-checks', 'list-checks' => 'List checks']);
        $this->iconsConfig = [];
        foreach ($iconsRaw as $key => $defaultLabel) {
            if ($key === 'default') {
                $this->iconsConfig[$key] = $defaultLabel;
                continue;
            }
            $label = __('icons.' . $key);
            $this->iconsConfig[$key] = ($label === 'icons.' . $key) ? $defaultLabel : $label;
        }
        app()->setLocale($previousLocale);

        $defaultIcon = config('icons.default', 'list-checks');
        $featureModels = PageAboutProjectFeature::with([
            'translations' => fn ($q) => $q->where('locale', $this->contentLocale),
        ])->orderBy('sort_order')->get();

        foreach ($featureModels as $index => $feature) {
            $trans = $feature->translations->first();
            $this->features[] = [
                'id' => $feature->id,
                'title' => $trans?->title ?? '',
                'description' => $trans?->description ?? '',
                'sort_order' => $index,
                'icon' => $feature->icon ?? $defaultIcon,
            ];
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
        $allowedIcons = array_keys(array_filter($this->iconsConfig, fn ($k) => $k !== 'default', ARRAY_FILTER_USE_KEY));
        $defaultIcon = $this->iconsConfig['default'] ?? 'list-checks';
        if (! in_array($defaultIcon, $allowedIcons, true)) {
            $defaultIcon = 'list-checks';
        }
        $locales = config('locales.supported', ['ro', 'en', 'ru']);

        PageAboutProjectFeature::query()->increment('sort_order');
        $feature = PageAboutProjectFeature::create(['sort_order' => 0, 'icon' => $defaultIcon]);
        foreach ($locales as $locale) {
            PageAboutProjectFeatureTranslation::create([
                'page_about_project_feature_id' => $feature->id,
                'locale' => $locale,
                'title' => '',
                'description' => '',
            ]);
        }
        $newItem = [
            'id' => $feature->id,
            'title' => '',
            'description' => '',
            'sort_order' => 0,
            'icon' => $defaultIcon,
        ];
        foreach ($this->features as $i => $f) {
            $this->features[$i]['sort_order'] = $i + 1;
        }
        $this->features = array_merge([$newItem], $this->features);
        $this->dispatch('feature-added', featureId: $feature->id, featureIndex: 0, editingIcon: $defaultIcon);
    }

    public function saveFeature(int $id, ?string $icon = null): void
    {
        $key = array_search($id, array_column($this->features, 'id'));
        if ($key === false) {
            return;
        }

        $feature = $this->features[$key];
        $this->features[$key]['title'] = trim($feature['title'] ?? '');
        $this->features[$key]['description'] = trim($feature['description'] ?? '');
        if ($icon !== null && $icon !== '') {
            $this->features[$key]['icon'] = $icon;
        }

        $trans = PageAboutProjectFeatureTranslation::where('page_about_project_feature_id', $id)
            ->where('locale', $this->contentLocale)
            ->first();

        if ($trans) {
            $trans->update([
                'title' => $this->features[$key]['title'] ?? '',
                'description' => $this->features[$key]['description'] ?? '',
            ]);
        }

        $feat = PageAboutProjectFeature::find($id);
        if ($feat) {
            $feat->update(['icon' => $this->features[$key]['icon'] ?? config('icons.default', 'list-checks')]);
        }

        $this->dispatch('toast', message: __('ui.admins_update_success'));
        $this->dispatch('feature-saved', [
            'featureId' => $id,
            'icon' => $this->features[$key]['icon'] ?? config('icons.default', 'list-checks'),
        ]);
    }

    public function deleteFeature(int $id): void
    {
        PageAboutProjectFeature::where('id', $id)->delete();
        $this->features = array_values(array_filter($this->features, fn ($f) => ($f['id'] ?? 0) !== $id));
        foreach ($this->features as $i => $f) {
            $this->features[$i]['sort_order'] = $i;
            PageAboutProjectFeature::where('id', $f['id'])->update(['sort_order' => $i]);
        }
        $this->dispatch('toast', message: __('ui.admins_delete_success'));
    }

    public function reorderFeatures(array $orderedIds): void
    {
        $orderedIds = array_values(array_filter(array_map('intval', $orderedIds)));
        $byId = [];
        foreach ($this->features as $f) {
            $byId[(int) ($f['id'] ?? 0)] = $f;
        }
        $newFeatures = [];
        foreach ($orderedIds as $i => $id) {
            if (isset($byId[$id])) {
                $byId[$id]['sort_order'] = $i;
                $newFeatures[] = $byId[$id];
            }
        }
        $this->features = $newFeatures;
        $this->persistFeatureOrder();
        $this->dispatch('toast', message: __('ui.admins_update_success'));
    }

    private function persistFeatureOrder(): void
    {
        foreach ($this->features as $i => $f) {
            $this->features[$i]['sort_order'] = $i;
            PageAboutProjectFeature::where('id', $f['id'])->update(['sort_order' => $i]);
        }
    }

    #[Computed]
    public function hasAboutChanges(): bool
    {
        $about = SiteContent::get('about_' . $this->contentLocale);
        $currentDesc = is_array($about) ? (string) ($about['description'] ?? '') : '';

        return $this->aboutDescription !== $currentDesc;
    }

    public function render()
    {
        return view('app.pages.site-content.about-project');
    }
}
