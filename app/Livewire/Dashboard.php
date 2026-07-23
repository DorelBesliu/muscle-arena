<?php

namespace App\Livewire;

use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Spatie\Analytics\Facades\Analytics;
use Spatie\Analytics\Period;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    #[Computed]
    public function totalUsers(): int
    {
        return 0;
    }

    #[Computed]
    public function analyticsData(): array
    {
        $defaults = [
            'pageViews' => 0,
            'visitors' => 0,
            'avgSessionDuration' => null,
            'bounceRate' => null,
        ];

        if (! config('analytics.property_id')) {
            return $defaults;
        }

        try {
            $period = Period::days(7);

            $totals = Analytics::fetchTotalVisitorsAndPageViews($period, 31);
            $pageViews = $totals->sum('screenPageViews');
            $visitors = $totals->sum('activeUsers');

            $sessionMetrics = Analytics::get(
                $period,
                ['averageSessionDuration', 'bounceRate'],
                [],
                1
            );

            $avgSessionSeconds = null;
            $bounceRate = null;
            if ($sessionMetrics->isNotEmpty()) {
                $row = $sessionMetrics->first();
                $avgSessionSeconds = isset($row['averageSessionDuration']) ? (float) $row['averageSessionDuration'] : null;
                $bounceRate = isset($row['bounceRate']) ? (float) $row['bounceRate'] : null;
            }

            return [
                'pageViews' => $pageViews,
                'visitors' => $visitors,
                'avgSessionDuration' => $avgSessionSeconds,
                'bounceRate' => $bounceRate,
            ];
        } catch (\Throwable $e) {
            report($e);

            return $defaults;
        }
    }

    public function render()
    {
        return view('app.pages.dashboard');
    }
}
