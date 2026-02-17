<?php

namespace Database\Seeders;

use App\Models\PagePathToOpeningStep;
use App\Models\PagePathToOpeningStepTranslation;
use App\Models\SiteContent;
use Illuminate\Support\Facades\App;
use Illuminate\Database\Seeder;

class PathToOpeningSeeder extends Seeder
{
    /**
     * Seed the "Drumul spre deschidere" (path to opening) section: roadmap content and steps with translations for all locales.
     */
    public function run(): void
    {
        $locales = config('locales.supported', ['ro', 'ru', 'en']);

        foreach ($locales as $locale) {
            App::setLocale($locale);
            $roadmapDescription = (string) __('ui.roadmap_description_default');
            SiteContent::set('roadmap_' . $locale, [
                'description' => $roadmapDescription,
                'phases' => [],
            ]);
        }

        if (PagePathToOpeningStep::count() > 0) {
            return;
        }

        $stepStatuses = ['completed', 'in-progress', 'upcoming', 'upcoming', 'upcoming'];

        for ($n = 1; $n <= 5; $n++) {
            $step = PagePathToOpeningStep::create([
                'status' => $stepStatuses[$n - 1],
                'sort_order' => $n - 1,
            ]);

            foreach ($locales as $locale) {
                App::setLocale($locale);
                PagePathToOpeningStepTranslation::create([
                    'page_path_to_opening_step_id' => $step->id,
                    'locale' => $locale,
                    'title' => (string) __('ui.timeline_step' . $n . '_title'),
                    'description' => (string) __('ui.timeline_step' . $n . '_description'),
                ]);
            }
        }
    }
}
