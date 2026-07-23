<?php

namespace App\Livewire\SiteContent;

use App\Models\SiteContent;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class TermsContent extends Component
{
    public string $termsContent = '';

    /** @var 'ro'|'en'|'ru' */
    public string $contentLocale = 'ro';

    public function mount(): void
    {
        $this->contentLocale = app()->getLocale() ?? 'ro';
        $requested = request()->query('locale');
        if (in_array($requested, ['ro', 'en', 'ru'], true)) {
            $this->contentLocale = $requested;
        }

        $data = SiteContent::get('terms_content_' . $this->contentLocale);
        $this->termsContent = (is_array($data) ? (string) ($data['content'] ?? '') : '');
    }

    public function saveTerms(): void
    {
        SiteContent::set('terms_content_' . $this->contentLocale, [
            'content' => $this->termsContent,
        ]);
        $this->dispatch('toast', message: __('ui.admins_update_success'));
    }

    public function render()
    {
        return view('app.pages.site-content.terms');
    }
}
