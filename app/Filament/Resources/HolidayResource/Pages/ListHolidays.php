<?php

namespace App\Filament\Resources\HolidayResource\Pages;

use App\Filament\Resources\HolidayResource;
use App\Models\Holiday;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListHolidays extends ListRecords
{
    protected static string $resource = HolidayResource::class;

    public $defaultAction = 'pendingHolidays';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function pendingHolidays() {
        $pendingHolidays = Holiday::where('type', 'pending')->whereDate('day', Carbon::now())->count();
        return Action::make('pendingHolidays')
            ->visible($pendingHolidays > 0)
            ->modalSubmitActionLabel('Verlos')
            ->action(null)
            ->color('success')
            ->modalCancelAction(null)
            ->modalHeading('Total pending days')
            ->modalDescription("There are {$pendingHolidays} days of vacation pending approval today")
            ->modalWidth('lm');



    }
}
