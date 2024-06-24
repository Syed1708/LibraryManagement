<?php

namespace App\Filament\Widgets;

use App\Models\Book;
use App\Models\User;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class UsersChart extends ChartWidget
{
    protected static ?string $heading = 'Users';
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $data = $this->getUserPerMonth();
        // dd($data);

        return [
            'datasets' => [
                [
                    'label' => 'Users created',
                    'data' => $data['usersPerMonth'],
                ],
            ],
            'labels' => $data['months'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
    private function getUserPerMonth()
{
    $now = Carbon::now();
    
    $data = collect(range(1, 12))->map(function($month) use($now) {
        $currentMonth = Carbon::createFromDate($now->year, $month, 1);

        // Count the number of users created in the current month
        $count = User::whereMonth('created_at', $currentMonth->month)
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

    // private function getUserPerMonth(){
         
    //     $now = Carbon::now();
        
    //     $usersPerMonth = [];
    //     // dd($usersPerMonth);

    //     $months = collect(range(1, 12))->map(function($month) use($now, $usersPerMonth){

    //         $count = User::whereMonth('created_at', Carbon::parse($now->month($month)->format('Y-m')))
    //         ->count();
    //         dd($count);

    //         $usersPerMonth[] = $count;

    //         return $now->month($month)->format('M');

    //     })->toArray();

    //     return [
    //         'usersPerMonth' => $usersPerMonth,
    //         'months' => $months,
    //     ];
    // }

    // private function getUserPerMonth()
    // {
    //     $now = Carbon::now();
        
    //     // Initialize arrays
    //     $usersPerMonth = [];
    //     $months = [];

    //     for ($month = 1; $month <= 12; $month++) {
    //         // Set the current month
    //         $currentMonth = Carbon::createFromDate($now->year, $month, 1);
            
    //         // Count users created in the current month
    //         $count = User::whereMonth('created_at', $currentMonth->month)
    //                      ->whereYear('created_at', $currentMonth->year)
    //                      ->count();
                         
    //         // Add the count to the array
    //         $usersPerMonth[] = $count;
            
    //         // Add the month name to the array
    //         $months[] = $currentMonth->format('M');
    //     }

    //     return [
    //         'usersPerMonth' => $usersPerMonth,
    //         'months' => $months,
    //     ];
    // }

    // private function getUserPerMonth()
    // {
    //     $now = Carbon::now();
        
    //     $usersPerMonth = [];
    //     $months = [];

    //     // Use the range to get the months and populate both arrays
    //     collect(range(1, 12))->each(function($month) use($now, &$usersPerMonth, &$months) {
    //         $currentMonth = Carbon::createFromDate($now->year, $month, 1);

    //         // Count the number of users created in the current month
    //         $count = User::whereMonth('created_at', $currentMonth->month)
    //                      ->whereYear('created_at', $currentMonth->year)
    //                      ->count();

    //         // Add the count and month name to the respective arrays
    //         $usersPerMonth[] = $count;
    //         $months[] = $currentMonth->format('M');
    //     });

    //     return [
    //         'usersPerMonth' => $usersPerMonth,
    //         'months' => $months,
    //     ];
    // }
}
