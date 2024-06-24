<?php

namespace App\Filament\Widgets;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Genre;
use App\Models\User;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Users', User::count())            
            ->description('Total User')
            ->descriptionIcon('heroicon-o-users',IconPosition::Before)
            ->color('success'),
            Stat::make('Authors', Author::count())            
            ->description('Total Authors')
            ->descriptionIcon('heroicon-o-users',IconPosition::Before)
            ->color('success'),
            Stat::make('Books', Book::count())            
            ->description('Total Books')
            ->descriptionIcon('heroicon-o-users',IconPosition::Before)
            ->color('success'),
            Stat::make('Categorirs', Category::count())            
            ->description('Total Categorirs')
            ->descriptionIcon('heroicon-o-users',IconPosition::Before)
            ->color('success'),
            Stat::make('Genres', Genre::count())            
            ->description('Total Genres')
            ->descriptionIcon('heroicon-o-users',IconPosition::Before)
            ->color('success'),
            
        ];
    }
}
