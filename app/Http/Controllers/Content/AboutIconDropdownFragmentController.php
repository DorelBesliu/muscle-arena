<?php

namespace App\Http\Controllers\Content;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AboutIconDropdownFragmentController extends Controller
{
    /**
     * Return the icon list fragment for the about feature dropdown (labels in user's profile locale).
     */
    public function __invoke(Request $request): View
    {
        $locale = $request->user()?->locale ?? config('locales.default', 'ro');
        if (in_array($locale, ['ro', 'en', 'ru'], true)) {
            app()->setLocale($locale);
        }

        $iconsConfig = config('icons', []);
        $icons = [];

        foreach ($iconsConfig as $key => $defaultLabel) {
            if ($key === 'default') {
                continue;
            }

            $label = __('icons.' . $key);
            if ($label === 'icons.' . $key) {
                $label = $defaultLabel;
            }
            $icons[$key] = $label;
        }

        return view('components.about-feature-icon-list-fragment', [
            'icons' => $icons,
        ]);
    }
}
