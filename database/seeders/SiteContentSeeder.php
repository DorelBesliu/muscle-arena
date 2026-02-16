<?php

namespace Database\Seeders;

use App\Models\SiteContent;
use Illuminate\Support\Facades\App;
use Illuminate\Database\Seeder;

class SiteContentSeeder extends Seeder
{
    /**
     * Seed the "Despre proiect" (about) section and features for all locales.
     */
    public function run(): void
    {
        $locales = config('locales.supported', ['ro', 'ru', 'en']);

        foreach ($locales as $locale) {
            App::setLocale($locale);

            $description = __('ui.about_description') . "\n\n" . __('ui.about_description_2');
            SiteContent::set('about_' . $locale, [
                'title' => '',
                'description' => $description,
            ]);

            if (SiteContent::get('about_features_' . $locale) === null) {
                $features = [
                    [
                        'id' => 1,
                        'title' => __('ui.feature1'),
                        'description' => __('ui.feature1Desc'),
                        'sort_order' => 0,
                        'icon' => 'dumbbell',
                    ],
                    [
                        'id' => 2,
                        'title' => __('ui.feature2'),
                        'description' => __('ui.feature2Desc'),
                        'sort_order' => 1,
                        'icon' => 'users',
                    ],
                    [
                        'id' => 3,
                        'title' => __('ui.feature3'),
                        'description' => __('ui.feature3Desc'),
                        'sort_order' => 2,
                        'icon' => 'clock',
                    ],
                ];
                SiteContent::set('about_features_' . $locale, $features);
            }
        }
    }
}
