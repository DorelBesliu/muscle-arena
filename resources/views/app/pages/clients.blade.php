<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component
{
}; ?>

<div class="min-h-screen bg-[#000000] text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="space-y-5">
            <div>
                <h3 class="text-lg font-black text-white mb-3">{{ __('ui.sidebar_clients') }}</h3>
                <p class="text-[#CCCCCC] text-sm">{{ __('ui.dashboard_overview') }}</p>
            </div>
        </div>
    </div>
</div>
