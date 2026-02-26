<?php

namespace Database\Seeders;

use App\Models\SiteContent;
use Illuminate\Database\Seeder;

class TermsContentSeeder extends Seeder
{
    /**
     * Seed the Terms & Conditions page content (Conținut site → Termeni și condiții).
     * Stores in site_content: terms_content_ro, terms_content_en, terms_content_ru
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
            SiteContent::set('terms_content_' . $locale, [
                'content' => $content,
            ]);
        }
    }

    private function getRomanianContent(): string
    {
        return <<<'HTML'
<h1>Termeni și Condiții</h1>

<h2>1. Acceptarea Termenilor</h2>
<p>Prin accesarea site-ului MUSCLE ARENA, acceptați acești Termeni și Condiții. Dacă nu sunteți de acord, vă rugăm să nu utilizați site-ul.</p>

<h2>2. Scopul site-ului</h2>
<p>Site-ul are scop informativ și prezintă detalii despre proiectul MUSCLE ARENA. Informațiile publicate nu constituie o ofertă contractuală și pot fi modificate fără notificare prealabilă.</p>

<h2>3. Proprietate intelectuală</h2>
<p>Conținutul site-ului (texte, imagini, logo-uri, elemente grafice) aparține MUSCLE ARENA și este protejat conform legislației aplicabile. Este interzisă reproducerea sau distribuirea fără acord scris.</p>

<h2>4. Limitarea răspunderii</h2>
<p>Nu garantăm că informațiile sunt complet lipsite de erori sau că site-ul va funcționa neîntrerupt. Utilizarea site-ului se face pe propria răspundere.</p>

<h2>5. Cookie-uri și date</h2>
<p>Utilizarea cookie-urilor este descrisă în Politica de Confidențialitate.</p>

<h2>6. Legea aplicabilă</h2>
<p>Acești termeni sunt guvernați de legislația Republicii Moldova. Orice litigiu va fi soluționat de instanțele competente din Republica Moldova.</p>
HTML;
    }

    private function getEnglishContent(): string
    {
        return <<<'HTML'
<h1>Terms and Conditions</h1>

<h2>1. Acceptance of Terms</h2>
<p>By accessing the MUSCLE ARENA website, you accept these Terms and Conditions. If you do not agree, please do not use the site.</p>

<h2>2. Purpose of the site</h2>
<p>The site is for informational purposes and presents details about the MUSCLE ARENA project. The information published does not constitute a contractual offer and may be modified without prior notice.</p>

<h2>3. Intellectual property</h2>
<p>The content of the site (texts, images, logos, graphic elements) belongs to MUSCLE ARENA and is protected under applicable law. Reproduction or distribution without written consent is prohibited.</p>

<h2>4. Limitation of liability</h2>
<p>We do not guarantee that the information is completely error-free or that the site will operate without interruption. Use of the site is at your own risk.</p>

<h2>5. Cookies and data</h2>
<p>The use of cookies is described in the Privacy Policy.</p>

<h2>6. Governing law</h2>
<p>These terms are governed by the legislation of the Republic of Moldova. Any dispute shall be resolved by the competent courts of the Republic of Moldova.</p>
HTML;
    }

    private function getRussianContent(): string
    {
        return <<<'HTML'
<h1>Условия использования</h1>

<h2>1. Принятие условий</h2>
<p>Получая доступ к сайту MUSCLE ARENA, вы принимаете данные Условия использования. Если вы не согласны, пожалуйста, не используйте сайт.</p>

<h2>2. Назначение сайта</h2>
<p>Сайт носит информационный характер и содержит сведения о проекте MUSCLE ARENA. Опубликованная информация не является договорным предложением и может быть изменена без предварительного уведомления.</p>

<h2>3. Интеллектуальная собственность</h2>
<p>Контент сайта (тексты, изображения, логотипы, графические элементы) принадлежит MUSCLE ARENA и охраняется в соответствии с применимым законодательством. Воспроизведение или распространение без письменного согласия запрещено.</p>

<h2>4. Ограничение ответственности</h2>
<p>Мы не гарантируем полное отсутствие ошибок в информации или бесперебойную работу сайта. Использование сайта осуществляется на ваш собственный риск.</p>

<h2>5. Cookie и данные</h2>
<p>Использование cookie описано в Политике конфиденциальности.</p>

<h2>6. Применимое право</h2>
<p>Эти условия регулируются законодательством Республики Молдова. Любой спор будет разрешён компетентными судами Республики Молдова.</p>
HTML;
    }
}
