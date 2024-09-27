<?php

namespace App\Filament\Personal\Resources\HolidayResource\Pages;

use App\Filament\Personal\Resources\HolidayResource;
use App\Mail\HolidaysPending;
use App\Models\User;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Mail;

class CreateHoliday extends CreateRecord
{
    protected static string $resource = HolidayResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        $date['type'] = 'pending';
        $userAdmin = User::find(2);
        $dataToSend = [
            'day' => $data['day'],
            'nameEmployee' => User::find($data['user_id'])->name,
            'email' => User::find($data['user_id'])->email,
        ];
        //Enviar el correo al usuario
        //Mail::to($userAdmin)->send(new HolidaysPending($dataToSend));

        //Notificación más sencilla de realizar
        /*Notification::make()
            ->title('Saved Succesfully')
            ->body('El día ' . $data['day'] . ' está pendiente de aprobar')
            ->success()
            ->send();*/

        

        $recipient = auth()->user();

        $recipient->notify(
            Notification::make()
                ->title('Saved successfully')
                ->toDatabase(),
        );

        Notification::make()
            ->title('Saved Succesfully')
            //->body('El día ' . $data['day'] . ' está pendiente de aprobar')
            ->sendToDatabase($recipient);
            
        return $data;
    }
}
