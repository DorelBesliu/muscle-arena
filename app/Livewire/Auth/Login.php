<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.public')]
class Login extends Component
{
    public array $form = [
        'email' => '',
        'password' => '',
        'remember' => false,
    ];

    public function login(): void
    {
        $this->validate([
            'form.email' => ['required', 'string', 'email'],
            'form.password' => ['required', 'string'],
        ]);

        if (! Auth::attempt(
            ['email' => $this->form['email'], 'password' => $this->form['password']],
            $this->form['remember']
        )) {
            $this->addError('form.email', __('auth.failed'));
            return;
        }

        Session::regenerate();

        if (Auth::user()->must_change_password) {
            $this->redirect(route('password.change'), navigate: true);
            return;
        }

        $this->redirectIntended(config('fortify.home', '/dashboard'), navigate: true);
    }

    public function render()
    {
        return view('app.pages.auth.login');
    }
}
