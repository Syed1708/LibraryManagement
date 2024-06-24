<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use App\Models\Author;
use Filament\Widgets\ChartWidget;

class AuthorsChart extends ChartWidget
{
    protected static ?string $heading = 'Authors';
    protected static ?int $sort = 1;
    protected static string $color = 'danger';
    protected static bool $isLazy = true;
    protected static ?string $pollingInterval = '10s';

    protected function getData(): array
    {
        $data = $this->getUserPerMonth();
        // dd($data);
        return [
            'datasets' => [
                [
                    'label' => 'Authors created',
                    'data' => $data['usersPerMonth'],
                    // 'backgroundColor' => '#36A2EB',
                    // 'borderColor' => '#9BD0F5',
                ],
            ],
            'labels' => $data['months'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    private function getUserPerMonth()
    {
        $now = Carbon::now();

        $data = collect(range(1, 12))->map(function ($month) use ($now) {
            $currentMonth = Carbon::createFromDate($now->year, $month, 1);

            // Count the number of users created in the current month
            $count = Author::whereMonth('created_at', $currentMonth->month)
                ->whereYear('created_at', $currentMonth->year)
                ->count();

            // Return both the count and the month name
            return [
                'count' => $count,
                'month' => $currentMonth->format('M')
            ];
        });

        // Split the results into separate arrays for counts and months
        $usersPerMonth = $data->pluck('count')->toArray();
        $months = $data->pluck('month')->toArray();

        return [
            'usersPerMonth' => $usersPerMonth,
            'months' => $months,
        ];
    }
}
