<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class RevenueAnalytics extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static string $view = 'filament.pages.revenue-analytics';
    
    protected static ?string $navigationGroup = 'Analytics';

    protected static ?int $navigationSort = 1;

    public function getHeaderWidgets(): array
    {
        return [
            // We would place ApexChart widgets here, e.g. ChurnRateChart::class, LtvChart::class
            // \App\Filament\Widgets\ChurnRateChart::class,
            // \App\Filament\Widgets\LtvChart::class,
        ];
    }
}
