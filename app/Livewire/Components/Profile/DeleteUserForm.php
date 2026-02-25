<?php

namespace App\Livewire\Components\Profile;

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class DeleteUserForm extends Component
{
    public function deleteUser(Logout $logout): void
    {
        if (! Auth::user()->hasRecentlyConfirmedPassword()) {
            throw ValidationException::withMessages([
                'password' => [__('ui.confirm_password_title')],
            ]);
        }

        tap(Auth::user(), $logout(...))->delete();
        $this->redirect('/', navigate: true);
    }

    public function render()
    {
        return view('app.components.profile.delete-user-form');
    }
}
