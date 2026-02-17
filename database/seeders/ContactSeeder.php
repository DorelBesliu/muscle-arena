<?php

namespace Database\Seeders;

use App\Models\SiteContent;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ContactSeeder extends Seeder
{
    /**
     * Seed the Contact page data for all locales.
     * Stores in site_content: contact_ro, contact_en, contact_ru
     * with cards (phone + email). Values appear as contacts_phone and contacts_email on the Contact page.
     */
    public function run(): void
    {
        $locales = config('locales.supported', ['ro', 'en', 'ru']);

        $defaultPhone = '+373 68 097 384';
        $defaultEmail = 'support@muscle-arena.md';

        foreach ($locales as $locale) {
            SiteContent::set('contact_' . $locale, [
                'cards' => [
                    [
                        'id' => (string) Str::ulid(),
                        'type' => 'phone',
                        'icon' => 'phone',
                        'description' => $defaultPhone,
                    ],
                    [
                        'id' => (string) Str::ulid(),
                        'type' => 'email',
                        'icon' => 'mail',
                        'description' => $defaultEmail,
                    ],
                ],
            ]);
        }
    }
}
