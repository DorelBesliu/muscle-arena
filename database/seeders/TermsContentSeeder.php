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
<p>Prin accesarea și utilizarea site-ului web MUSCLE ARENA, acceptați și sunteți de acord să fiți legați de termenii și prevederile acestui acord. Dacă nu sunteți de acord cu acești termeni, vă rugăm să nu utilizați site-ul nostru web.</p>

<h2>2. Conturi de Utilizator</h2>
<p>Când creați un cont, sunteți de acord să:</p>
<ul>
<li>Furnizați informații precise și complete</li>
<li>Mențineți securitatea datelor de autentificare ale contului dvs.</li>
<li>Acceptați responsabilitatea pentru toate activitățile din contul dvs.</li>
<li>Ne notificați imediat despre orice utilizare neautorizată</li>
</ul>

<h2>3. Cronologia Proiectului</h2>
<p>Deși ne propunem să urmăm cronologia publicată, datele efective de finalizare pot varia din cauza:</p>
<ul>
<li>Întârzierilor în construcție și aprobărilor de permise</li>
<li>Disponibilității echipamentelor și programelor de livrare</li>
<li>Circumstanțelor neprevăzute sau evenimentelor de forță majoră</li>
</ul>

<h2>4. Limitarea Răspunderii</h2>
<p>MUSCLE ARENA și fondatorii săi nu vor fi responsabili pentru:</p>
<ul>
<li>Întârzieri în finalizarea proiectului sau deschiderea sălii</li>
<li>Modificări ale facilităților, echipamentelor sau serviciilor sălii față de planurile inițiale</li>
<li>Imposibilitatea de a deschide sala din cauza unor circumstanțe dincolo de controlul nostru</li>
<li>Orice daune indirecte, accidentale sau consecvențiale</li>
</ul>

<h2>5. Modificări ale Termenilor</h2>
<p>Ne rezervăm dreptul de a modifica acești termeni în orice moment. Modificările vor intra în vigoare imediat după publicarea pe site. Utilizarea continuă a serviciilor noastre după modificări constituie acceptarea termenilor modificați.</p>

<h2>6. Legea Aplicabilă</h2>
<p>Acești termeni vor fi guvernați și interpretați în conformitate cu legile Republicii Moldova. Orice dispute care decurg din acești termeni vor fi supuse jurisdicției exclusive a instanțelor din Sîngerei, Moldova.</p>
HTML;
    }

    private function getEnglishContent(): string
    {
        return <<<'HTML'
<h1>Terms and Conditions</h1>

<h2>1. Acceptance of Terms</h2>
<p>By accessing and using the MUSCLE ARENA website, you accept and agree to be bound by the terms and provisions of this agreement. If you do not agree with these terms, please do not use our website.</p>

<h2>2. User Accounts</h2>
<p>When you create an account, you agree to:</p>
<ul>
<li>Provide accurate and complete information</li>
<li>Maintain the security of your account login credentials</li>
<li>Accept responsibility for all activities under your account</li>
<li>Notify us immediately of any unauthorized use</li>
</ul>

<h2>3. Project Timeline</h2>
<p>Although we aim to follow the published timeline, actual completion dates may vary due to:</p>
<ul>
<li>Construction delays and permit approvals</li>
<li>Equipment availability and delivery schedules</li>
<li>Unforeseen circumstances or force majeure events</li>
</ul>

<h2>4. Limitation of Liability</h2>
<p>MUSCLE ARENA and its founders shall not be liable for:</p>
<ul>
<li>Delays in project completion or gym opening</li>
<li>Changes to facilities, equipment or gym services compared to initial plans</li>
<li>Inability to open the gym due to circumstances beyond our control</li>
<li>Any indirect, incidental or consequential damages</li>
</ul>

<h2>5. Changes to Terms</h2>
<p>We reserve the right to modify these terms at any time. Changes will take effect immediately upon publication on the site. Continued use of our services after changes constitutes acceptance of the modified terms.</p>

<h2>6. Governing Law</h2>
<p>These terms shall be governed by and construed in accordance with the laws of the Republic of Moldova. Any disputes arising from these terms shall be subject to the exclusive jurisdiction of the courts of Sîngerei, Moldova.</p>
HTML;
    }

    private function getRussianContent(): string
    {
        return <<<'HTML'
<h1>Условия использования</h1>

<h2>1. Принятие условий</h2>
<p>Получая доступ к веб-сайту MUSCLE ARENA и используя его, вы принимаете и соглашаетесь соблюдать условия и положения данного соглашения. Если вы не согласны с этими условиями, пожалуйста, не используйте наш веб-сайт.</p>

<h2>2. Учётные записи пользователей</h2>
<p>Создавая учётную запись, вы соглашаетесь:</p>
<ul>
<li>Предоставлять точную и полную информацию</li>
<li>Обеспечивать безопасность данных для входа в учётную запись</li>
<li>Нести ответственность за все действия в вашей учётной записи</li>
<li>Немедленно уведомлять нас о любом несанкционированном использовании</li>
</ul>

<h2>3. Хронология проекта</h2>
<p>Хотя мы стремимся следовать опубликованному графику, фактические сроки завершения могут меняться из-за:</p>
<ul>
<li>Задержек в строительстве и согласовании разрешений</li>
<li>Доступности оборудования и графиков поставок</li>
<li>Непредвиденных обстоятельств или форс-мажорных событий</li>
</ul>

<h2>4. Ограничение ответственности</h2>
<p>MUSCLE ARENA и её основатели не несут ответственности за:</p>
<ul>
<li>Задержки в завершении проекта или открытии зала</li>
<li>Изменения в оборудовании, объектах или услугах зала по сравнению с первоначальными планами</li>
<li>Невозможность открыть зал из-за обстоятельств, не зависящих от нас</li>
<li>Любой косвенный, случайный или последующий ущерб</li>
</ul>

<h2>5. Изменение условий</h2>
<p>Мы оставляем за собой право изменять эти условия в любое время. Изменения вступают в силу немедленно после публикации на сайте. Продолжение использования наших услуг после изменений означает принятие изменённых условий.</p>

<h2>6. Применимое право</h2>
<p>Эти условия регулируются и толкуются в соответствии с законодательством Республики Молдова. Любые споры, возникающие из этих условий, подлежат исключительной юрисдикции судов Сынгерей, Молдова.</p>
HTML;
    }
}
