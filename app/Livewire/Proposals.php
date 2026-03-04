<?php

namespace App\Livewire;

use App\Models\Proposal;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Proposals extends Component
{
    public string $sortBy = 'date';

    public string $sortDir = 'desc';

    public ?int $deleteConfirmId = null;

    #[Computed]
    public function proposals()
    {
        return Proposal::query()->orderBy('created_at', 'desc')->get();
    }

    #[Computed]
    public function filteredAndSortedProposals()
    {
        $proposals = $this->proposals;

        $key = match ($this->sortBy) {
            'title' => fn (Proposal $p) => strtolower($p->title),
            'date' => fn (Proposal $p) => $p->created_at?->getTimestamp() ?? 0,
            default => fn (Proposal $p) => $p->created_at?->getTimestamp() ?? 0,
        };

        return $this->sortDir === 'asc'
            ? $proposals->sortBy($key)->values()
            : $proposals->sortByDesc($key)->values();
    }

    public function sortByColumn(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDir = $column === 'title' ? 'asc' : 'desc';
        }
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteConfirmId = $id;
    }

    public function cancelDelete(): void
    {
        $this->deleteConfirmId = null;
    }

    public function doDelete(): void
    {
        if ($this->deleteConfirmId) {
            Proposal::findOrFail($this->deleteConfirmId)->delete();
            $this->deleteConfirmId = null;
            $this->dispatch('toast', message: __('ui.proposals_delete_success'));
            $this->dispatch('$refresh');
        }
    }

    public function render()
    {
        return view('app.pages.proposals');
    }
}
