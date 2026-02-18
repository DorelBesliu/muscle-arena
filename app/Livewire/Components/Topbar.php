<?php

namespace App\Livewire\Components;

use App\Livewire\Actions\Logout;
use Livewire\Component;

class Topbar extends Component
{
    public function getCurrentPageTitle(): string
    {
        if (request()->routeIs('dashboard')) {
            return __('ui.dashboard');
        }
        if (request()->routeIs('clients')) {
            return __('ui.sidebar_clients');
        }
        if (request()->routeIs('administrators')) {
            return __('ui.sidebar_administrators');
        }
        if (request()->routeIs('content.about')) {
            return __('ui.sidebar_content_about');
        }
        if (request()->routeIs('content.roadmap')) {
            return __('ui.sidebar_content_roadmap');
        }
        if (request()->routeIs('content.contact')) {
            return __('ui.sidebar_content_contact');
        }
        if (request()->routeIs('content.privacy')) {
            return __('ui.sidebar_privacy');
        }
        if (request()->routeIs('content.terms')) {
            return __('ui.sidebar_terms');
        }
        if (request()->routeIs('profile')) {
            return __('Profile');
        }

        return config('app.name');
    }

    public function getCurrentPageSubtitle(): ?string
    {
        if (request()->routeIs('administrators')) {
            return __('ui.admins_subtitle');
        }
        if (request()->routeIs('clients')) {
            return __('ui.members_subtitle');
        }

        return null;
    }

    public function getInitial(string $name): string
    {
        return strtoupper(mb_substr(trim($name), 0, 1));
    }

    public function logout(Logout $logout): void
    {
        $logout();
        $this->redirect(route('signin', ['locale' => app()->getLocale()]), navigate: false);
    }

    public function render()
    {
        return view('app.components.topbar');
    }
}
