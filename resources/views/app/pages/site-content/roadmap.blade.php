<?php

use App\Models\PagePathToOpeningStep;
use App\Models\PagePathToOpeningStepTranslation;
use App\Models\SiteContent;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component
{
    public string $roadmapDescription = '';

    /** @var 'ro'|'en'|'ru' */
    public string $contentLocale = 'ro';

    /** @var array<int, array{id: int, title: string, description: string, status: string, sort_order: int}> */
    public array $phases = [];

    /** @var int|null ID-ul pasului în editare */
    public ?int $editingPhaseId = null;

    public function mount(): void
    {
        $this->contentLocale = app()->getLocale() ?? 'ro';
        $requested = request()->query('locale');
        if (in_array($requested, ['ro', 'en', 'ru'], true)) {
            $this->contentLocale = $requested;
        }

        $data = SiteContent::get('roadmap_' . $this->contentLocale);
        if (is_array($data)) {
            $this->roadmapDescription = (string) ($data['description'] ?? '');
        }

        $this->loadPhasesFromDb();
    }

    public function saveRoadmap(): void
    {
        SiteContent::set('roadmap_' . $this->contentLocale, [
            'description' => $this->roadmapDescription,
            'phases' => [],
        ]);
        $this->dispatch('toast', message: __('ui.admins_update_success'));
    }

    public function addPhase(): void
    {
        $this->editingPhaseId = null;
        $maxOrder = PagePathToOpeningStep::max('sort_order') ?? -1;
        $step = PagePathToOpeningStep::create([
            'status' => 'upcoming',
            'sort_order' => $maxOrder + 1,
        ]);
        $locales = config('locales.supported', ['ro', 'en', 'ru']);
        foreach ($locales as $locale) {
            PagePathToOpeningStepTranslation::create([
                'page_path_to_opening_step_id' => $step->id,
                'locale' => $locale,
                'title' => '',
                'description' => '',
            ]);
        }
        $this->loadPhasesFromDb();
    }

    public function editPhase(int $id): void
    {
        $this->editingPhaseId = $id;
    }

    public function savePhase(int $id): void
    {
        $step = PagePathToOpeningStep::find($id);
        if (! $step) {
            return;
        }
        $index = array_search($id, array_column($this->phases, 'id'));
        if ($index !== false && isset($this->phases[$index])) {
            $p = $this->phases[$index];
            $step->update(['status' => in_array($p['status'], ['completed', 'in-progress', 'upcoming'], true) ? $p['status'] : 'upcoming']);
            $trans = $step->translation($this->contentLocale);
            if ($trans) {
                $trans->update(['title' => $p['title'] ?? '', 'description' => $p['description'] ?? '']);
            } else {
                PagePathToOpeningStepTranslation::create([
                    'page_path_to_opening_step_id' => $step->id,
                    'locale' => $this->contentLocale,
                    'title' => $p['title'] ?? '',
                    'description' => $p['description'] ?? '',
                ]);
            }
        }
        $this->editingPhaseId = null;
        $this->loadPhasesFromDb();
        $this->dispatch('toast', message: __('ui.admins_update_success'));
    }

    public function deletePhase(int $id): void
    {
        PagePathToOpeningStep::find($id)?->delete();
        $this->editingPhaseId = null;
        $this->loadPhasesFromDb();
    }

    private function loadPhasesFromDb(): void
    {
        $steps = PagePathToOpeningStep::with([
            'translations' => fn ($q) => $q->where('locale', $this->contentLocale),
        ])->orderBy('sort_order')->get();

        $this->phases = [];
        foreach ($steps as $step) {
            $trans = $step->translations->first();
            $this->phases[] = [
                'id' => $step->id,
                'title' => $trans?->title ?? '',
                'description' => $trans?->description ?? '',
                'status' => in_array($step->status, ['completed', 'in-progress', 'upcoming'], true) ? $step->status : 'upcoming',
                'sort_order' => $step->sort_order,
            ];
        }
    }
}; ?>

<div class="min-h-screen bg-[#000000] text-white">
    <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="space-y-6">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <x-app.components.content-editing-language-bar :content-locale="$contentLocale" route-name="content.roadmap" />
                <x-app.components.content-view-on-site-button :content-locale="$contentLocale" hash="#timeline" />
            </div>

            {{-- Descriere --}}
            <div class="bg-[#111111] border-2 border-[#333333] rounded-xl">
                <div class="p-4 md:p-5">
                    <div class="space-y-3">
                        <div>
                            <label for="roadmap-description" class="block text-sm font-medium text-[#CCCCCC] mb-1.5">{{ __('ui.content_roadmap_description') }}</label>
                            <textarea
                                id="roadmap-description"
                                wire:model="roadmapDescription"
                                rows="3"
                                class="w-full bg-[#000000] border-2 border-[#333333] rounded-xl px-3 py-2 text-sm text-white placeholder-[#666666] focus:border-[#F97316] focus:outline-none transition-colors resize-none"
                            ></textarea>
                        </div>
                        <div class="flex justify-end pt-2">
                            <x-app.components.loading-button
                                wire-target="saveRoadmap"
                                wire:click="saveRoadmap"
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

            {{-- Faze --}}
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <label class="text-sm font-medium text-[#CCCCCC]">{{ __('ui.content_roadmap_phases') }}</label>
                    <button
                        type="button"
                        wire:click="addPhase"
                        class="inline-flex items-center gap-2 h-8 px-4 rounded-lg border-2 border-[#333333] hover:border-[#F97316] hover:bg-[#111111] text-white text-sm font-medium transition-colors"
                    >
                        <x-lucide-plus class="w-4 h-4" />
                        <span>{{ __('ui.content_roadmap_add_phase') }}</span>
                    </button>
                </div>
                <div class="space-y-3">
                    @forelse($phases as $index => $phase)
                        <div class="bg-[#111111] border-2 border-[#333333] rounded-xl" wire:key="phase-{{ $phase['id'] }}">
                            <div class="p-3">
                                @if($editingPhaseId === $phase['id'])
                                    <div class="flex gap-3">
                                        <div class="flex items-start pt-2">
                                            <x-lucide-grip-vertical class="w-4 h-4 text-[#666666]" />
                                        </div>
                                        <div class="flex-1 space-y-2">
                                            <input
                                                type="text"
                                                wire:model="phases.{{ $index }}.title"
                                                class="w-full h-9 px-3 bg-[#000000] border-2 border-[#333333] rounded-xl text-sm text-white placeholder-[#666666] focus:border-[#F97316] focus:outline-none"
                                                placeholder="{{ __('ui.content_roadmap_phase_title') }}"
                                            />
                                            <textarea
                                                wire:model="phases.{{ $index }}.description"
                                                rows="2"
                                                class="w-full bg-[#000000] border-2 border-[#333333] rounded-xl px-3 py-2 text-sm text-white placeholder-[#666666] focus:border-[#F97316] focus:outline-none resize-none"
                                                placeholder="{{ __('ui.content_roadmap_phase_description') }}"
                                            ></textarea>
                                            <div class="flex gap-2">
                                                <select
                                                    wire:model="phases.{{ $index }}.status"
                                                    class="flex-1 bg-[#000000] border-2 border-[#333333] rounded-xl px-3 py-2 text-sm text-white focus:border-[#F97316] focus:outline-none"
                                                >
                                                    <option value="completed">{{ __('ui.content_roadmap_status_completed') }}</option>
                                                    <option value="in-progress">{{ __('ui.content_roadmap_status_in_progress') }}</option>
                                                    <option value="upcoming">{{ __('ui.content_roadmap_status_upcoming') }}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="flex flex-col gap-2">
                                            <x-app.components.loading-button
                                                wire-target="savePhase"
                                                wire:click="savePhase({{ $phase['id'] }})"
                                                class="h-8 w-8 rounded-lg text-[#666666] hover:text-green-500 hover:bg-[#111111] transition-colors"
                                                title="{{ __('ui.content_roadmap_save') }}"
                                            >
                                                <x-lucide-save class="w-4 h-4" />
                                            </x-app.components.loading-button>
                                            <button
                                                type="button"
                                                wire:click="deletePhase({{ $phase['id'] }})"
                                                wire:confirm="{{ __('ui.admins_delete_confirm') }}"
                                                class="h-8 w-8 flex items-center justify-center rounded-lg text-[#666666] hover:text-red-500 hover:bg-[#111111] transition-colors"
                                            >
                                                <x-lucide-trash-2 class="w-4 h-4" />
                                            </button>
                                        </div>
                                    </div>
                                @else
                                    <div class="flex gap-3">
                                        <div class="flex items-start pt-2">
                                            <x-lucide-grip-vertical class="w-4 h-4 text-[#666666]" />
                                        </div>
                                        <div class="flex-1 space-y-1 min-w-0">
                                            <div class="flex items-center gap-2">
                                                <h5 class="text-sm font-semibold text-white">{{ $phase['title'] ?: '—' }}</h5>
                                                @php
                                                    $statusLabel = $phase['status'] === 'completed' ? __('ui.content_roadmap_status_completed') : ($phase['status'] === 'in-progress' ? __('ui.content_roadmap_status_in_progress') : __('ui.content_roadmap_status_upcoming'));
                                                    $statusClass = $phase['status'] === 'completed' ? 'bg-green-500/20 text-green-500' : ($phase['status'] === 'in-progress' ? 'bg-blue-500/20 text-blue-500' : 'bg-[#333333] text-[#999999]');
                                                @endphp
                                                <span class="text-xs px-2 py-0.5 rounded-full {{ $statusClass }}">{{ $statusLabel }}</span>
                                            </div>
                                            <p class="text-sm text-[#999999]">{{ $phase['description'] ?: '—' }}</p>
                                        </div>
                                        <div class="flex gap-2">
                                            <button
                                                type="button"
                                                wire:click="editPhase({{ $phase['id'] }})"
                                                class="h-8 w-8 flex items-center justify-center rounded-lg text-[#666666] hover:text-[#F97316] hover:bg-[#111111] transition-colors"
                                            >
                                                <x-lucide-pencil class="w-4 h-4" />
                                            </button>
                                            <button
                                                type="button"
                                                wire:click="deletePhase({{ $phase['id'] }})"
                                                wire:confirm="{{ __('ui.admins_delete_confirm') }}"
                                                class="h-8 w-8 flex items-center justify-center rounded-lg text-[#666666] hover:text-red-500 hover:bg-[#111111] transition-colors"
                                            >
                                                <x-lucide-trash-2 class="w-4 h-4" />
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center py-12 px-4 bg-[#111111] outline outline-2 outline-[#333333] rounded-xl text-[#666666]">
                            <x-lucide-line-squiggle class="w-12 h-12 mb-3 text-[#666666] opacity-50" />
                            <p class="text-sm">{{ __('ui.content_roadmap_phases') }} – {{ __('ui.content_roadmap_add_phase') }}</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
