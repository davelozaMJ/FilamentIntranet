<?php

namespace App\Filament\Personal\Widgets;

use App\Models\Holiday;
use App\Models\Timesheet;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Support\Enums\IconPosition;

class PersonalWidgetStats extends BaseWidget
{
    protected function getStats(): array
    {

        return [
            Stat::make('Pending Holidays', $this->getPendingHoliday(auth()->user()))
                ->description('Total pending holidays')
                ->descriptionIcon('heroicon-m-user-group', IconPosition::Before)
                ->chart([30,5,30,5,30,5,30,5])
                ->color('success'),
            Stat::make('Aprobved Holidays', $this->getApprovedHoliday(auth()->user()))
                ->description('Total approved holidays')
                ->descriptionIcon('heroicon-o-rocket-launch', IconPosition::Before)
                ->chart([30,5,30,5,30,5,30,5])
                ->color('primary'),
            Stat::make('Total Work', $this->getTotallyWork(auth()->user()))
                ->description('Total computed time')
                ->descriptionIcon('heroicon-o-arrows-pointing-in', IconPosition::Before)
                ->chart([30,5,30,5,30,5,30,5])
                ->color('info'),
            Stat::make('Total Work Today', $this->getTotallyWorkDiary(auth()->user()))
                ->description('Total computed time')
                ->descriptionIcon('heroicon-o-arrows-pointing-in', IconPosition::Before)
                ->chart([30,5,30,5,30,5,30,5])
                ->color('info'),
        ];
    }

    protected function getPendingHoliday(User $user) {
        $totalPendingHolidays = Holiday::where('user_id', $user->id)->where('type', 'pending')->get()->count();

        return $totalPendingHolidays;
    }

    protected function getApprovedHoliday(User $user) {
        $totalApprovedHolidays = Holiday::where('user_id', $user->id)->where('type', 'approved')->get()->count();

        return $totalApprovedHolidays;
    }

    protected function getTotallyWork(User $user) {
        $timeSheets = TimeSheet::where('user_id', $user->id)->where('type', 'work')->get();
        $sumHours = 0;
        foreach ($timeSheets as $timesheet) {
            $startTime = Carbon::parse($timesheet->day_in);
            $endTime = Carbon::parse($timesheet->day_out);

            $totalDuration = $endTime->diffInSeconds($startTime);
            $sumHours = $sumHours + $totalDuration;
        }

        //dd($user);
        //Otra manera de hacerlo
        $tiempoCarbon = CarbonInterval::seconds($sumHours)->cascade()->forHumans();
        //No me está dando correctamente el resultado
        //$tiempoCarbon = gmdate("H:i:s", $sumHours);

        return $tiempoCarbon;
    }

    protected function getTotallyWorkDiary(User $user) {
        $timeSheets = TimeSheet::where('user_id', $user->id)->where('type', 'work')->whereDate('created_at', Carbon::now()->toDateString())->get();
        $sumHours = 0;
        foreach ($timeSheets as $timesheet) {
            $startTime = Carbon::parse($timesheet->day_in);
            $endTime = Carbon::parse($timesheet->day_out);

            $totalDuration = $endTime->diffInSeconds($startTime);
            $sumHours = $sumHours + $totalDuration;
        }

        //dd($user);
        //Otra manera de hacerlo
        $tiempoCarbon = CarbonInterval::seconds($sumHours)->cascade()->forHumans();
        //No me está dando correctamente el resultado
        //$tiempoCarbon = gmdate("H:i:s", $sumHours);

        return $tiempoCarbon;
    }
}
