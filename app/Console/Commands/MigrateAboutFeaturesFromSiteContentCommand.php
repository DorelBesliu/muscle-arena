<?php

namespace App\Console\Commands;

use App\Models\PageAboutProjectFeature;
use App\Models\PageAboutProjectFeatureTranslation;
use App\Models\SiteContent;
use Illuminate\Console\Command;

class MigrateAboutFeaturesFromSiteContentCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'about-features:migrate-from-site-content';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate about_features_* from site_content into page_about_project_features and translations';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $locales = config('locales.supported', ['ro', 'en', 'ru']);
        $perLocale = [];
        foreach ($locales as $locale) {
            $raw = SiteContent::get('about_features_' . $locale, []);
            if (! is_array($raw)) {
                $perLocale[$locale] = [];
                continue;
            }
            $items = array_values(array_map(fn ($f) => [
                'id' => (int) ($f['id'] ?? 0),
                'title' => (string) ($f['title'] ?? ''),
                'description' => (string) ($f['description'] ?? ''),
                'sort_order' => (int) ($f['sort_order'] ?? 0),
                'icon' => (string) ($f['icon'] ?? 'list-checks'),
            ], $raw));
            usort($items, fn ($a, $b) => $a['sort_order'] <=> $b['sort_order']);
            $perLocale[$locale] = $items;
        }

        $maxCount = max(array_map('count', $perLocale) ?: [0]);
        if ($maxCount === 0) {
            $this->warn('No about_features_* data found in site_content.');

            return self::SUCCESS;
        }

        if (PageAboutProjectFeature::count() > 0) {
            $this->warn('page_about_project_features already has data. Skipping to avoid duplicates.');

            return self::SUCCESS;
        }

        $defaultIcon = config('icons.default', 'list-checks');
        for ($i = 0; $i < $maxCount; $i++) {
            $firstItem = null;
            foreach ($locales as $locale) {
                $items = $perLocale[$locale] ?? [];
                $firstItem = $items[$i] ?? $firstItem;
                if ($firstItem !== null) {
                    break;
                }
            }
            $icon = (string) ($firstItem['icon'] ?? $defaultIcon);
            $feature = PageAboutProjectFeature::create(['sort_order' => $i, 'icon' => $icon]);
            foreach ($locales as $locale) {
                $items = $perLocale[$locale] ?? [];
                $item = $items[$i] ?? null;
                PageAboutProjectFeatureTranslation::create([
                    'page_about_project_feature_id' => $feature->id,
                    'locale' => $locale,
                    'title' => $item['title'] ?? '',
                    'description' => $item['description'] ?? '',
                ]);
            }
        }

        $this->info("Migrated {$maxCount} about feature(s) with translations for " . count($locales) . ' locale(s).');

        return self::SUCCESS;
    }
}
