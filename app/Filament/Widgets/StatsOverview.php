<?php

namespace App\Filament\Widgets;

use App\Models\Holiday;
use App\Models\Timesheet;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Support\Enums\IconPosition;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalEmployees = User::all()->count();
        $totalHolidays = Holiday::where('type', 'pending')->count();
        $totaltimesheets = Timesheet::all()->count();

        return [
            Stat::make('Employees', $totalEmployees)
                ->description('Total employees')
                ->descriptionIcon('heroicon-m-user-group', IconPosition::Before)
                ->chart([30,5,30,5,30,5,30,5])
                ->color('success'),
            Stat::make('Pending holidays', $totalHolidays)
                ->description('Total pending holidays')
                ->descriptionIcon('heroicon-o-rocket-launch', IconPosition::Before)
                ->chart([30,5,30,5,30,5,30,5])
                ->color('primary'),
            Stat::make('TimeSheets', $totaltimesheets)
                ->description('Total timesheets')
                ->descriptionIcon('heroicon-o-arrows-pointing-in', IconPosition::Before)
                ->chart([30,5,30,5,30,5,30,5])
                ->color('info'),
        ];
    }
}
