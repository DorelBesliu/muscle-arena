<?php

namespace App\Livewire\SiteContent;

use App\Models\SiteContent;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class PrivacyContent extends Component
{
    public string $privacyContent = '';

    /** @var 'ro'|'en'|'ru' */
    public string $contentLocale = 'ro';

    public function mount(): void
    {
        $this->contentLocale = app()->getLocale() ?? 'ro';
        $requested = request()->query('locale');
        if (in_array($requested, ['ro', 'en', 'ru'], true)) {
            $this->contentLocale = $requested;
        }

        $data = SiteContent::get('privacy_content_' . $this->contentLocale);
        $this->privacyContent = (is_array($data) ? (string) ($data['content'] ?? '') : '');
    }

    public function savePrivacy(): void
    {
        SiteContent::set('privacy_content_' . $this->contentLocale, [
            'content' => $this->privacyContent,
        ]);
        $this->dispatch('toast', message: __('ui.admins_update_success'));
    }

    public function render()
    {
        return view('app.pages.site-content.privacy');
    }
}
