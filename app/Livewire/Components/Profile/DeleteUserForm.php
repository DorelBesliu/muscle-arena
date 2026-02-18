<?php

namespace App\Livewire\Components\Profile;

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class DeleteUserForm extends Component
{
    public string $password = '';

    public bool $showModal = false;

    public function rules(): array
    {
        return [
            'password' => ['required', 'string', 'current_password'],
        ];
    }

    public function deleteUser(Logout $logout): void
    {
        try {
            $this->validate();
        } catch (ValidationException $e) {
            $this->showModal = true;
            throw $e;
        }

        tap(Auth::user(), $logout(...))->delete();
        $this->redirect('/', navigate: true);
    }

    public function render()
    {
        return view('app.components.profile.delete-user-form');
    }
}
