<?php

namespace App\Livewire\Auth;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.public')]
class ForgotPassword extends Component
{
    public bool $linkSent = false;

    public function mount(): void
    {
        $this->linkSent = (bool) session('status');
    }

    public function showFormAgain(): void
    {
        $this->linkSent = false;
    }

    public function render()
    {
        return view('app.pages.auth.forgot-password');
    }
}
