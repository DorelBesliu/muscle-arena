<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component
{
    public function getTotalUsersProperty(): int
    {
        // TODO: Replace with actual database query
        // Example: return User::count();
        return 0;
    }
}; ?>

<div class="min-h-screen bg-[#000000] text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="space-y-5">
            {{-- Overview Section --}}
            <div>
                <h3 class="text-lg font-black text-white mb-3">{{ __('ui.dashboard_overview') }}</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                    {{-- Total Clients --}}
                    <div class="bg-[#111111] border-2 border-[#333333] rounded-xl p-4 hover:border-[#F97316] transition-all">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center flex-shrink-0">
                                    <x-lucide-users class="w-4 h-4 text-white" />
                                </div>
                                <div>
                                    <p class="text-[#CCCCCC] text-xs mb-0.5">{{ __('ui.dashboard_total_users') }}</p>
                                    <p class="text-white text-lg font-black">{{ $this->totalUsers }}</p>
                                </div>
                            </div>
                            <span class="bg-green-500/20 text-green-400 border border-green-500/30 text-xs px-2 py-0 rounded">+12.5%</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Google Analytics Section --}}
            <div>
                <h3 class="text-lg font-black text-white mb-3">{{ __('ui.dashboard_analytics') }}</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                    {{-- Page Views --}}
                    <div class="bg-[#111111] border-2 border-[#333333] rounded-xl p-4 hover:border-[#F97316] transition-all">
                        <div class="flex items-center gap-3 mb-2">
                            <x-lucide-eye class="w-6 h-6 text-blue-500" />
                            <p class="text-[#CCCCCC] text-xs">{{ __('ui.dashboard_page_views') }}</p>
                        </div>
                        <p class="text-white text-2xl font-black">12,547</p>
                    </div>

                    {{-- Unique Visitors --}}
                    <div class="bg-[#111111] border-2 border-[#333333] rounded-xl p-4 hover:border-[#F97316] transition-all">
                        <div class="flex items-center gap-3 mb-2">
                            <x-lucide-mouse-pointer class="w-6 h-6 text-green-500" />
                            <p class="text-[#CCCCCC] text-xs">{{ __('ui.dashboard_unique_visitors') }}</p>
                        </div>
                        <p class="text-white text-2xl font-black">8,934</p>
                    </div>

                    {{-- Avg Session Duration --}}
                    <div class="bg-[#111111] border-2 border-[#333333] rounded-xl p-4 hover:border-[#F97316] transition-all">
                        <div class="flex items-center gap-3 mb-2">
                            <x-lucide-clock class="w-6 h-6 text-[#F97316]" />
                            <p class="text-[#CCCCCC] text-xs">{{ __('ui.dashboard_avg_session_duration') }}</p>
                        </div>
                        <p class="text-white text-2xl font-black">3:42</p>
                    </div>

                    {{-- Bounce Rate --}}
                    <div class="bg-[#111111] border-2 border-[#333333] rounded-xl p-4 hover:border-[#F97316] transition-all">
                        <div class="flex items-center gap-3 mb-2">
                            <x-lucide-globe class="w-6 h-6 text-purple-500" />
                            <p class="text-[#CCCCCC] text-xs">{{ __('ui.dashboard_bounce_rate') }}</p>
                        </div>
                        <p class="text-white text-2xl font-black">42.3%</p>
                    </div>
                </div>

                {{-- Analytics Integration Note --}}
                <div class="mt-4 bg-[#111111] border-2 border-blue-500 rounded-xl p-4">
                    <div class="flex items-start gap-3">
                        <x-lucide-globe class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" />
                        <div>
                            <h4 class="text-white font-bold text-sm mb-1">
                                {{ __('ui.dashboard_analytics_note_title') }}
                            </h4>
                            <p class="text-[#CCCCCC] text-xs leading-relaxed">
                                {{ __('ui.dashboard_analytics_note_text') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
