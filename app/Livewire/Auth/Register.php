<?php

namespace App\Livewire\Auth;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.public')]
class Register extends Component
{
    public function render()
    {
        return view('app.pages.auth.register');
    }
}
