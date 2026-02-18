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
<p>MUSCLE ARENA ("noi", "noastru" sau "ne") suntem angajați să protejăm confidențialitatea ta. Această Politică de Confidențialitate explică cum colectăm, folosim și protejăm informațiile tale personale când folosești site-ul nostru web.</p>

<h2>Informații pe care le colectăm</h2>
<p>Când folosești site-ul nostru web, colectăm următoarele informații:</p>
<ul>
<li><strong>Informații personale:</strong> Nume, prenume, număr de telefon și adresă de email</li>
<li><strong>Informații tehnice:</strong> Tipul de browser, informații despre dispozitiv și date de utilizare prin cookie-uri</li>
</ul>

<h2>Cum folosim informațiile tale</h2>
<p>Folosim informațiile tale pentru următoarele scopuri:</p>
<ul>
<li>Contactarea ta cu privire la actualizările despre progresul proiectului MUSCLE ARENA</li>
<li>Îmbunătățirea site-ului nostru web și experienței utilizatorului</li>
<li>Respectarea obligațiilor legale și reglementare în conformitate cu legislația Republicii Moldova</li>
</ul>

<h2>Stocarea și securitatea datelor</h2>
<p>Informațiile tale personale sunt stocate local în localStorage-ul browserului tău. Aceste date rămân pe dispozitivul tău și nu sunt transmise automat către servere externe decât dacă trimiți explicit un formular. Implementăm măsuri de securitate adecvate pentru a proteja informațiile tale personale și pentru a preveni accesul neautorizat, modificarea sau divulgarea datelor tale.</p>

<h2>Drepturile tale</h2>
<p>În conformitate cu legislația Republicii Moldova privind protecția datelor personale, ai următoarele drepturi:</p>
<ul>
<li>Să accesezi datele tale personale</li>
<li>Să ceri corecția informațiilor incorecte sau incomplete</li>
<li>Să ceri ștergerea datelor tale (cu excepția cazurilor în care avem obligații legale de păstrare)</li>
<li>Să îți retragi consimțământul pentru procesarea datelor în orice moment</li>
<li>Să soliciți informații despre cum sunt procesate datele tale personale</li>
</ul>

<h2>Reținerea de date</h2>
<p>Menținem informațiile tale personale pentru perioada necesară pentru a îndeplini scopurile menționate în această politică și pentru a respecta obligațiile legale. Datele sunt menținute pentru respectarea cerințelor de audit și conformare legală, precum și pentru comunicarea cu utilizatorii privind progresul proiectului MUSCLE ARENA.</p>

<h2>Divulgarea către terți</h2>
<p>Ne angajăm să nu vindem, să nu tranzacționăm sau să nu transferăm informațiile tale personale către terți fără consimțământul tău, cu următoarele excepții:</p>
<ul>
<li>Când este necesar în baza legii sau prin ordin al autorităților competente</li>
<li>Pentru a proteja drepturile, proprietatea sau siguranța MUSCLE ARENA, a utilizatorilor noștri sau a publicului</li>
</ul>

<h2>Cookie-uri și tehnologii similare</h2>
<p>Site-ul nostru folosește cookie-uri pentru a îmbunătăți experiența utilizatorului și pentru a analiza traficul pe site. Prin continuarea utilizării site-ului, consimți la utilizarea cookie-urilor conform acestei politici.</p>

<h2>Modificări ale politicii</h2>
<p>Ne rezervăm dreptul de a actualiza această Politică de Confidențialitate periodic pentru a reflecta schimbările în practicile noastre sau cerințele legale. Orice modificări vor fi publicate pe această pagină cu data actualizării revizuite.</p>
HTML;
    }

    private function getEnglishContent(): string
    {
        return <<<'HTML'
<h1>Privacy Policy</h1>

<h2>Introduction</h2>
<p>MUSCLE ARENA ("we", "our" or "us") are committed to protecting your privacy. This Privacy Policy explains how we collect, use and protect your personal information when you use our website.</p>

<h2>Information We Collect</h2>
<p>When you use our website, we collect the following information:</p>
<ul>
<li><strong>Personal information:</strong> Name, surname, phone number and email address</li>
<li><strong>Technical information:</strong> Browser type, device information and usage data through cookies</li>
</ul>

<h2>How We Use Your Information</h2>
<p>We use your information for the following purposes:</p>
<ul>
<li>Contacting you regarding updates about the progress of the MUSCLE ARENA project</li>
<li>Improving our website and user experience</li>
<li>Complying with legal and regulatory obligations in accordance with the legislation of the Republic of Moldova</li>
</ul>

<h2>Data Storage and Security</h2>
<p>Your personal information is stored locally in your browser's localStorage. This data remains on your device and is not automatically transmitted to external servers unless you explicitly submit a form. We implement appropriate security measures to protect your personal information and to prevent unauthorized access, modification or disclosure of your data.</p>

<h2>Your Rights</h2>
<p>In accordance with the legislation of the Republic of Moldova on personal data protection, you have the following rights:</p>
<ul>
<li>Access your personal data</li>
<li>Request correction of incorrect or incomplete information</li>
<li>Request deletion of your data (except in cases where we have legal obligations to retain)</li>
<li>Withdraw your consent for data processing at any time</li>
<li>Request information about how your personal data is processed</li>
</ul>

<h2>Data Retention</h2>
<p>We retain your personal information for the period necessary to fulfill the purposes mentioned in this policy and to comply with legal obligations. Data is maintained for audit and legal compliance requirements, as well as for communicating with users regarding the progress of the MUSCLE ARENA project.</p>

<h2>Disclosure to Third Parties</h2>
<p>We commit not to sell, trade or transfer your personal information to third parties without your consent, with the following exceptions:</p>
<ul>
<li>When required by law or by order of competent authorities</li>
<li>To protect the rights, property or safety of MUSCLE ARENA, our users or the public</li>
</ul>

<h2>Cookies and Similar Technologies</h2>
<p>Our website uses cookies to improve user experience and to analyze site traffic. By continuing to use the site, you consent to the use of cookies in accordance with this policy.</p>

<h2>Policy Changes</h2>
<p>We reserve the right to update this Privacy Policy periodically to reflect changes in our practices or legal requirements. Any changes will be published on this page with the revised update date.</p>
HTML;
    }

    private function getRussianContent(): string
    {
        return <<<'HTML'
<h1>Политика конфиденциальности</h1>

<h2>Введение</h2>
<p>MUSCLE ARENA («мы», «наш» или «нас») обязуемся защищать вашу конфиденциальность. Эта Политика конфиденциальности объясняет, как мы собираем, используем и защищаем вашу личную информацию при использовании нашего веб-сайта.</p>

<h2>Информация, которую мы собираем</h2>
<p>Когда вы используете наш веб-сайт, мы собираем следующую информацию:</p>
<ul>
<li><strong>Личная информация:</strong> Имя, фамилия, номер телефона и адрес электронной почты</li>
<li><strong>Техническая информация:</strong> Тип браузера, информация об устройстве и данные об использовании через cookie</li>
</ul>

<h2>Как мы используем вашу информацию</h2>
<p>Мы используем вашу информацию для следующих целей:</p>
<ul>
<li>Связь с вами относительно обновлений о прогрессе проекта MUSCLE ARENA</li>
<li>Улучшение нашего веб-сайта и пользовательского опыта</li>
<li>Соблюдение правовых и нормативных обязательств в соответствии с законодательством Республики Молдова</li>
</ul>

<h2>Хранение и безопасность данных</h2>
<p>Ваша личная информация хранится локально в localStorage вашего браузера. Эти данные остаются на вашем устройстве и не передаются автоматически на внешние серверы, если вы явно не отправляете форму. Мы применяем соответствующие меры безопасности для защиты вашей личной информации и предотвращения несанкционированного доступа, изменения или раскрытия ваших данных.</p>

<h2>Ваши права</h2>
<p>В соответствии с законодательством Республики Молдова о защите персональных данных вы имеете следующие права:</p>
<ul>
<li>Доступ к вашим персональным данным</li>
<li>Запросить исправление неверной или неполной информации</li>
<li>Запросить удаление ваших данных (за исключением случаев, когда у нас есть юридические обязательства по хранению)</li>
<li>Отозвать ваше согласие на обработку данных в любое время</li>
<li>Запросить информацию о том, как обрабатываются ваши персональные данные</li>
</ul>

<h2>Хранение данных</h2>
<p>Мы храним вашу личную информацию в течение периода, необходимого для выполнения целей, упомянутых в этой политике, и для соблюдения правовых обязательств. Данные хранятся для соблюдения требований аудита и правового соответствия, а также для общения с пользователями относительно прогресса проекта MUSCLE ARENA.</p>

<h2>Раскрытие третьим лицам</h2>
<p>Мы обязуемся не продавать, не обменивать и не передавать вашу личную информацию третьим лицам без вашего согласия, за следующими исключениями:</p>
<ul>
<li>Когда это требуется по закону или по распоряжению компетентных органов</li>
<li>Для защиты прав, собственности или безопасности MUSCLE ARENA, наших пользователей или общественности</li>
</ul>

<h2>Cookie и аналогичные технологии</h2>
<p>Наш веб-сайт использует cookie для улучшения пользовательского опыта и анализа трафика сайта. Продолжая использовать сайт, вы соглашаетесь на использование cookie в соответствии с этой политикой.</p>

<h2>Изменения в политике</h2>
<p>Мы оставляем за собой право периодически обновлять эту Политику конфиденциальности для отражения изменений в наших практиках или правовых требованиях. Любые изменения будут опубликованы на этой странице с пересмотренной датой обновления.</p>
HTML;
    }
}
