<?php

namespace App\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Profile extends Component
{
    public function mount(): void
    {
        if (! session()->has('locale')) {
            session()->put('locale', app()->getLocale());
        }
    }

    public function render()
    {
        return view('app.pages.profile');
    }
}
