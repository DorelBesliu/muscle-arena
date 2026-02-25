<?php

namespace App\Livewire\Auth;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

#[Layout('layouts.app')]
class ForceChangePassword extends Component
{
    public $password = '';
    public $password_confirmation = '';

    public function updatePassword()
    {
        app('log')->info('Updating password', ['password' => $this->password, 'password_confirmation' => $this->password_confirmation]);

        $this->validate([
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = auth()->user();

        $user->password = Hash::make($this->password);
        $user->must_change_password = false;
        $user->save();

        return redirect()->route('dashboard');
    }

    public function render()
    {
        return view('app.pages.auth.force-change-password');
    }
}
