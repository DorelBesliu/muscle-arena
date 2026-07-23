<?php

namespace Database\Seeders;

use App\Models\PageAboutProjectFeature;
use App\Models\PageAboutProjectFeatureTranslation;
use App\Models\SiteContent;
use Illuminate\Support\Facades\App;
use Illuminate\Database\Seeder;

class AboutProjectSeeder extends Seeder
{
    /**
     * Seed the "Despre proiect" (about) section and features for all locales.
     */
    public function run(): void
    {
        $locales = config('locales.supported', ['ro', 'ru', 'en']);

        foreach ($locales as $locale) {
            App::setLocale($locale);
            $part1 = (string) __('ui.about_description');
            $part2 = (string) __('ui.about_description_2');
            $description = $part1 . "\n\n" . $part2;
            SiteContent::set('about_' . $locale, [
                'title' => '',
                'description' => $description,
            ]);
        }

        if (PageAboutProjectFeature::count() > 0) {
            return;
        }

        $featuresData = [
            ['key' => 'feature1', 'keyDesc' => 'feature1Desc', 'icon' => 'dumbbell'],
            ['key' => 'feature2', 'keyDesc' => 'feature2Desc', 'icon' => 'users'],
            ['key' => 'feature3', 'keyDesc' => 'feature3Desc', 'icon' => 'clock'],
        ];

        foreach ($featuresData as $sortOrder => $data) {
            $feature = PageAboutProjectFeature::create(['sort_order' => $sortOrder, 'icon' => $data['icon']]);
            foreach ($locales as $locale) {
                App::setLocale($locale);
                PageAboutProjectFeatureTranslation::create([
                    'page_about_project_feature_id' => $feature->id,
                    'locale' => $locale,
                    'title' => __('ui.' . $data['key']),
                    'description' => __('ui.' . $data['keyDesc']),
                ]);
            }
        }
    }
}
