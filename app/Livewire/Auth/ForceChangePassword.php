<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ForceChangePassword extends Component
{
    public string $current_password = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function rules(): array
    {
        return [
            'current_password' => ['required', 'string', 'current_password'],
            'password' => ['required', 'string', Password::defaults(), 'confirmed'],
        ];
    }

    public function savePassword(): void
    {
        try {
            $validated = $this->validate();
        } catch (ValidationException $e) {
            return;
        }

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
            'must_change_password' => false,
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');
        $this->redirect(route('dashboard'), navigate: true);
    }

    public function logout(): void
    {
        Auth::guard('web')->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        $this->redirect(route('signin', ['locale' => app()->getLocale()]), navigate: true);
    }

    public function render()
    {
        return view('app.pages.auth.force-change-password');
    }
}
