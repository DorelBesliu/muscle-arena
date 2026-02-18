<?php

namespace App\Livewire;

use App\Models\Member;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Clients extends Component
{
    public string $searchTerm = '';

    public string $statusFilter = '';

    public string $sortBy = 'registration';

    public string $sortDir = 'desc';

    public bool $isModalOpen = false;

    public ?int $editingId = null;

    public ?int $deleteConfirmId = null;

    public string $firstName = '';

    public string $lastName = '';

    public string $phone = '';

    public string $email = '';

    public string $status = 'active';

    public ?string $registrationDate = null;

    public ?string $subscriptionExpiryDate = null;

    #[Computed]
    public function members()
    {
        return Member::query()->orderBy('created_at', 'desc')->get();
    }

    #[Computed]
    public function filteredAndSortedMembers()
    {
        $search = strtolower($this->searchTerm);
        $members = $this->members->filter(function (Member $m) use ($search) {
            if ($search !== '') {
                $matchSearch = str_contains(strtolower($m->first_name), $search)
                    || str_contains(strtolower($m->last_name), $search)
                    || str_contains(strtolower((string) $m->email), $search)
                    || str_contains((string) $m->phone, $this->searchTerm);
                if (! $matchSearch) {
                    return false;
                }
            }
            if ($this->statusFilter !== '' && ($m->status ?? 'active') !== $this->statusFilter) {
                return false;
            }
            return true;
        });

        $key = match ($this->sortBy) {
            'name' => fn (Member $m) => strtolower($m->last_name . ' ' . $m->first_name),
            'expiry' => fn (Member $m) => [
                $m->subscription_expiry?->getTimestamp() ?? 0,
                $m->id,
            ],
            'registration' => fn (Member $m) => [
                $m->created_at?->getTimestamp() ?? 0,
                $m->id,
            ],
            default => fn (Member $m) => [
                $m->created_at?->getTimestamp() ?? 0,
                $m->id,
            ],
        };

        return $this->sortDir === 'asc'
            ? $members->sortBy($key)->values()
            : $members->sortByDesc($key)->values();
    }

    public function sortByColumn(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDir = $column === 'name' ? 'asc' : 'desc';
        }
    }

    public function openAddModal(): void
    {
        $this->resetForm();
        $this->editingId = null;
        $this->isModalOpen = true;
    }

    public function openEditModal(int $id): void
    {
        $member = Member::findOrFail($id);
        $this->editingId = $id;
        $this->firstName = $member->first_name;
        $this->lastName = $member->last_name;
        $this->phone = $member->phone ?? '';
        $this->email = $member->email ?? '';
        $this->status = $member->status ?? 'active';
        $this->registrationDate = $member->created_at?->format('Y-m-d');
        $this->subscriptionExpiryDate = $member->subscription_expiry?->format('Y-m-d');
        $this->isModalOpen = true;
    }

    public function saveMember(): void
    {
        $this->validate([
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'registrationDate' => 'required|date',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email',
            'status' => 'in:active,inactive',
        ]);

        $createdAt = Carbon::parse($this->registrationDate)->startOfDay();

        $data = [
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'phone' => trim($this->phone) !== '' ? $this->phone : null,
            'email' => trim($this->email) !== '' ? $this->email : null,
            'status' => $this->status,
            'subscription_expiry' => $this->subscriptionExpiryDate ?: null,
        ];

        $wasAdding = $this->editingId === null;
        if ($this->editingId) {
            Member::where('id', $this->editingId)->update(array_merge($data, ['created_at' => $createdAt]));
        } else {
            $member = Member::create($data);
            Member::where('id', $member->id)->update(['created_at' => $createdAt]);
        }

        $this->resetForm();
        $this->isModalOpen = false;
        $this->editingId = null;
        $this->dispatch('toast', message: $wasAdding ? __('ui.added_success') : __('ui.members_saved_success'));
        $this->dispatch('$refresh');
    }

    public function cancelModal(): void
    {
        $this->isModalOpen = false;
        $this->editingId = null;
        $this->resetForm();
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteConfirmId = $id;
    }

    public function doDelete(): void
    {
        if ($this->deleteConfirmId) {
            Member::findOrFail($this->deleteConfirmId)->delete();
            $this->deleteConfirmId = null;
            $this->dispatch('toast', message: __('ui.members_delete_success'));
            $this->dispatch('$refresh');
        }
    }

    public function cancelDelete(): void
    {
        $this->deleteConfirmId = null;
    }

    public function toggleStatus(): void
    {
        $this->status = $this->status === 'active' ? 'inactive' : 'active';
    }

    public function toggleMemberStatus(int $id): void
    {
        $member = Member::findOrFail($id);
        $member->status = ($member->status ?? 'active') === 'active' ? 'inactive' : 'active';
        $member->save();
        $this->dispatch('toast', message: __('ui.members_status_updated'));
        $this->dispatch('$refresh');
    }

    protected function resetForm(): void
    {
        $this->firstName = '';
        $this->lastName = '';
        $this->phone = '';
        $this->email = '';
        $this->status = 'active';
        $this->registrationDate = now()->format('Y-m-d');
        $this->subscriptionExpiryDate = null;
    }

    public function render()
    {
        return view('app.pages.clients');
    }
}
