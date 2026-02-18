<?php

namespace App\Livewire;

use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    #[Computed]
    public function totalUsers(): int
    {
        return 0;
    }

    public function render()
    {
        return view('app.pages.dashboard');
    }
}
