<?php

use App\Models\SiteContent;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component
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
        if (is_array($data)) {
            $this->termsContent = (string) ($data['content'] ?? '');
        } else {
            $this->termsContent = '';
        }
    }

    public function saveTerms(): void
    {
        SiteContent::set('terms_content_' . $this->contentLocale, [
            'content' => $this->termsContent,
        ]);
        $this->dispatch('toast', message: __('ui.admins_update_success'));
    }
}; ?>

<div class="min-h-screen bg-[#000000] text-white">
    <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="space-y-6">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <x-app.components.content-editing-language-bar :content-locale="$contentLocale" route-name="content.terms" />
                <a
                    href="{{ route('terms', ['locale' => $contentLocale]) }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex items-center gap-2 h-9 px-4 rounded-lg border-2 border-[#333333] hover:border-[#F97316] hover:bg-[#111111] text-white text-sm font-medium transition-colors"
                >
                    <x-lucide-external-link class="w-4 h-4" />
                    <span>{{ __('ui.sidebar_content_view_about') }}</span>
                </a>
            </div>

            <div class="w-full rounded-xl">
                <div class="space-y-3">
                    <div class="w-full">
                        @php
                            $editorContent = $termsContent;
                            if ($editorContent !== '' && ! str_contains($editorContent, '<')) {
                                $paragraphs = array_filter(array_map('trim', explode("\n\n", $editorContent)));
                                $editorContent = implode('', array_map(fn ($p) => '<p>' . e($p) . '</p>', $paragraphs));
                            }
                        @endphp
                        <x-tiptap-editor wireProperty="termsContent" :content="$editorContent" class="w-full" />
                    </div>
                    <div class="flex justify-end pt-2">
                        <x-app.components.loading-button
                            wire-target="saveTerms"
                            wire:click="saveTerms"
                            icon-size="w-3.5 h-3.5"
                            class="h-8 px-4 rounded-lg bg-[#F97316] hover:bg-[#ea620c] text-white text-sm font-medium transition-colors min-w-[5rem]"
                        >
                            <x-lucide-save class="w-3.5 h-3.5" />
                            <x-slot:label><span class="text-xs">{{ __('ui.content_roadmap_save') }}</span></x-slot:label>
                        </x-app.components.loading-button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
