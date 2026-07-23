<?php

namespace App\Livewire\SiteContent;

use App\Models\PagePathToOpeningStep;
use App\Models\PagePathToOpeningStepTranslation;
use App\Models\SiteContent;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class RoadmapContent extends Component
{
    public string $roadmapDescription = '';

    /** @var 'ro'|'en'|'ru' */
    public string $contentLocale = 'ro';

    public array $phases = [];

    public ?int $editingPhaseId = null;

    public function mount(): void
    {
        $this->contentLocale = app()->getLocale() ?? 'ro';
        $requested = request()->query('locale');
        if (in_array($requested, ['ro', 'en', 'ru'], true)) {
            $this->contentLocale = $requested;
        }

        $data = SiteContent::get('roadmap_' . $this->contentLocale);
        $this->roadmapDescription = (is_array($data) ? (string) ($data['description'] ?? '') : '');
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
            $step->update(['status' => in_array($p['status'] ?? '', ['completed', 'in-progress', 'upcoming'], true) ? $p['status'] : 'upcoming']);
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

    public function reorderPhases(array $orderedIds): void
    {
        $orderedIds = array_values(array_filter(array_map('intval', $orderedIds)));
        $byId = [];
        foreach ($this->phases as $p) {
            $byId[(int) ($p['id'] ?? 0)] = $p;
        }
        $newPhases = [];
        foreach ($orderedIds as $i => $id) {
            if (isset($byId[$id])) {
                $byId[$id]['sort_order'] = $i;
                $newPhases[] = $byId[$id];
            }
        }
        $this->phases = $newPhases;
        foreach ($this->phases as $i => $p) {
            PagePathToOpeningStep::where('id', $p['id'])->update(['sort_order' => $i]);
        }
        $this->dispatch('toast', message: __('ui.admins_update_success'));
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

    public function render()
    {
        return view('app.pages.site-content.roadmap');
    }
}
