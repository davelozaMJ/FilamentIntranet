<?php

namespace App\Filament\Resources\HolidayResource\Pages;

use App\Filament\Resources\HolidayResource;
use Filament\Actions;
use App\Mail\HolidaysApproved;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;

class EditHoliday extends EditRecord
{
    protected static string $resource = HolidayResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model {

        $record->update($data);

        if($record->type == 'approved'){
            $user = User::find($record->user_id);
            $data = [
                'name' => $user->name,
                'email' => $user->email,
                'day' => $record->day
            ];

            //Mail::to($user)->send(new HolidaysApproved($data));

            $recipient = $user;
            
            //Notificación más sencilla de mostrar
            /*Notification::make()
            ->title('Solicitud de vacaciones')
            ->body('El día ' . $data['day'] . ' está aprobado')
            ->success()
            ->send();*/

            Notification::make()
            ->title('Solicitud de vacaciones')
            ->body('El día ' . $data['day'] . ' está aprobado')
            ->sendToDatabase($recipient);
        }

        return $record;

    }
}
