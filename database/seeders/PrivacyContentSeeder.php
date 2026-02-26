<?php

namespace Database\Seeders;

use App\Models\SiteContent;
use Illuminate\Database\Seeder;

class PrivacyContentSeeder extends Seeder
{
    /**
     * Seed the Privacy Policy page content (Conținut site → Politica de confidențialitate).
     * Stores in site_content: privacy_content_ro, privacy_content_en, privacy_content_ru
     */
    public function run(): void
    {
        $locales = config('locales.supported', ['ro', 'en', 'ru']);

        foreach ($locales as $locale) {
            $content = match ($locale) {
                'ro' => $this->getRomanianContent(),
                'en' => $this->getEnglishContent(),
                'ru' => $this->getRussianContent(),
                default => $this->getRomanianContent(),
            };
            SiteContent::set('privacy_content_' . $locale, [
                'content' => $content,
            ]);
        }
    }

    private function getRomanianContent(): string
    {
        return <<<'HTML'
<h1>Politica de Confidențialitate</h1>

<h2>Introducere</h2>
<p>Site-ul MUSCLE ARENA este un site informativ. Nu colectăm date personale prin formulare sau conturi de utilizator.</p>

<h2>Date colectate automat</h2>
<p>Utilizăm Google Analytics 4 pentru a analiza modul în care este utilizat site-ul. Acest serviciu poate colecta date tehnice precum adresa IP (anonimizată), tipul dispozitivului, browserul utilizat, paginile vizitate și durata vizitei. Datele sunt utilizate exclusiv în scop statistic.</p>

<h2>Cookie-uri</h2>
<p>Google Analytics utilizează cookie-uri pentru a funcționa. Aceste cookie-uri sunt activate doar după exprimarea consimțământului prin bannerul de cookies.</p>

<h2>Transferul datelor</h2>
<p>Datele colectate prin Google Analytics pot fi procesate de Google pe servere situate în afara Republicii Moldova sau Uniunii Europene.</p>

<h2>Drepturile utilizatorului</h2>
<p>Poți retrage oricând consimțământul pentru cookie-uri din setările browserului sau ne poți contacta pentru informații suplimentare.</p>
HTML;
    }

    private function getEnglishContent(): string
    {
        return <<<'HTML'
<h1>Privacy Policy</h1>

<h2>Introduction</h2>
<p>The MUSCLE ARENA website is an informational site. We do not collect personal data through forms or user accounts.</p>

<h2>Data collected automatically</h2>
<p>We use Google Analytics 4 to analyze how the site is used. This service may collect technical data such as IP address (anonymized), device type, browser used, pages visited and visit duration. The data is used exclusively for statistical purposes.</p>

<h2>Cookies</h2>
<p>Google Analytics uses cookies to function. These cookies are only activated after you give your consent via the cookie banner.</p>

<h2>Data transfer</h2>
<p>Data collected through Google Analytics may be processed by Google on servers located outside the Republic of Moldova or the European Union.</p>

<h2>User rights</h2>
<p>You can withdraw your consent for cookies at any time from your browser settings or contact us for further information.</p>
HTML;
    }

    private function getRussianContent(): string
    {
        return <<<'HTML'
<h1>Политика конфиденциальности</h1>

<h2>Введение</h2>
<p>Сайт MUSCLE ARENA является информационным. Мы не собираем персональные данные через формы или учётные записи пользователей.</p>

<h2>Данные, собираемые автоматически</h2>
<p>Мы используем Google Analytics 4 для анализа использования сайта. Этот сервис может собирать технические данные, такие как IP-адрес (анонимизированный), тип устройства, используемый браузер, посещённые страницы и длительность визита. Данные используются исключительно в статистических целях.</p>

<h2>Cookie</h2>
<p>Google Analytics использует cookie для работы. Эти cookie активируются только после выражения согласия через баннер cookie.</p>

<h2>Передача данных</h2>
<p>Данные, собираемые через Google Analytics, могут обрабатываться Google на серверах, расположенных за пределами Республики Молдова или Европейского союза.</p>

<h2>Права пользователя</h2>
<p>Вы можете в любое время отозвать согласие на cookie в настройках браузера или связаться с нами для получения дополнительной информации.</p>
HTML;
    }
}
