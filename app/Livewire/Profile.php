<?php

namespace App\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Profile extends Component
{
    public $showTwoFactorSetupModal = false;
    public $twoFactorPending = false;

    public function mount(): void
    {
        if (! session()->has('locale')) {
            session()->put('locale', app()->getLocale());
        }

        if (session()->pull('open_2fa_setup_modal')) {
            $this->showTwoFactorSetupModal = true;
        }

        $this->twoFactorPending = auth()->user()->two_factor_secret && ! auth()->user()->two_factor_confirmed_at;
    }

    public function render()
    {
        return view('app.pages.profile');
    }
}
