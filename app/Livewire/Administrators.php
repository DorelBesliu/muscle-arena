<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Administrators extends Component
{
    public string $searchTerm = '';
    public bool $isModalOpen = false;
    public ?int $editingId = null;
    public string $newName = '';
    public string $newEmail = '';
    public string $newPassword = '';
    public string $newPassword_confirmation = '';
    public ?int $deleteConfirmId = null;

    public function mount(): void
    {
        if (auth()->user()?->role !== 'admin') {
            abort(403, 'Access denied.');
        }
    }

    #[Computed]
    public function administrators()
    {
        return User::query()->where('role', 'admin')->orderBy('created_at', 'desc')->get();
    }

    #[Computed]
    public function filteredAdministrators()
    {
        $search = strtolower($this->searchTerm);
        return $this->administrators->filter(function (User $admin) use ($search) {
            if ($search === '') return true;
            return str_contains(strtolower($admin->name), $search)
                || str_contains(strtolower($admin->email), $search);
        })->values();
    }

    public function openAddModal(): void
    {
        $this->reset(['newName', 'newEmail', 'newPassword', 'newPassword_confirmation', 'editingId']);
        $this->isModalOpen = true;
        $this->resetValidation();
    }

    public function openEditModal(int $id): void
    {
        $admin = User::where('id', $id)->where('role', 'admin')->firstOrFail();
        $this->editingId = $id;
        $this->newName = $admin->name;
        $this->newEmail = $admin->email;
        $this->newPassword = '';
        $this->newPassword_confirmation = '';
        $this->isModalOpen = true;
        $this->resetValidation();
    }

    public function saveAdministrator(): void
    {
        $rules = [
            'newName' => 'required|string|max:255',
            'newEmail' => 'required|email|' . ($this->editingId ? 'unique:users,email,' . $this->editingId : 'unique:users,email'),
        ];
        $rules['newPassword'] = $this->editingId
            ? ['nullable', 'string', 'confirmed', Password::defaults()]
            : ['required', 'string', 'confirmed', Password::defaults()];

        $this->validate($rules, [
            'newName.required' => __('ui.admins_name_required'),
            'newEmail.required' => __('ui.admins_email_required'),
            'newEmail.email' => __('ui.admins_email_invalid'),
            'newEmail.unique' => __('ui.admins_email_exists'),
            'newPassword.required' => __('ui.admins_temp_password_required'),
            'newPassword.confirmed' => __('ui.admins_temp_password_confirmed'),
        ]);

        if ($this->editingId) {
            $data = ['name' => $this->newName, 'email' => $this->newEmail];
            if ($this->newPassword !== '') {
                $data['password'] = Hash::make($this->newPassword);
                $data['must_change_password'] = true;
            }
            User::where('id', $this->editingId)->where('role', 'admin')->update($data);
            $this->dispatch('toast', message: __('ui.admins_update_success'));
        } else {
            User::create([
                'name' => $this->newName,
                'email' => $this->newEmail,
                'password' => Hash::make($this->newPassword),
                'role' => 'admin',
                'must_change_password' => true,
            ]);
            $this->dispatch('toast', message: __('ui.added_success'));
        }

        $this->reset(['newName', 'newEmail', 'newPassword', 'newPassword_confirmation', 'editingId']);
        $this->isModalOpen = false;
        $this->dispatch('$refresh');
    }

    public function cancelModal(): void
    {
        $this->isModalOpen = false;
        $this->reset(['newName', 'newEmail', 'newPassword', 'newPassword_confirmation', 'editingId']);
        $this->resetValidation();
    }

    public function confirmDelete(int $id): void
    {
        if ($id !== auth()->id()) $this->deleteConfirmId = $id;
    }

    public function deleteAdministrator(): void
    {
        if ($this->deleteConfirmId === null || $this->deleteConfirmId === auth()->id()) {
            $this->deleteConfirmId = null;
            return;
        }
        User::where('id', $this->deleteConfirmId)->where('role', 'admin')->delete();
        $this->deleteConfirmId = null;
        $this->dispatch('$refresh');
        $this->dispatch('toast', message: __('ui.admins_delete_success'));
    }

    public function cancelDelete(): void
    {
        $this->deleteConfirmId = null;
    }

    public function render()
    {
        return view('app.pages.administrators');
    }
}
