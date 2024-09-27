<?php

namespace App\Filament\Personal\Resources\TimesheetResource\Pages;

use App\Filament\Personal\Resources\TimesheetResource;
use App\Models\Timesheet;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\Builder;
use Filament\Resources\Pages\ListRecords;
use Barryvdh\DomPDF\Facade\Pdf;

class ListTimesheets extends ListRecords
{
    protected static string $resource = TimesheetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            //Quedaría pendiente que cuando se pulse sobre el botón de terminar jornada, se deshabilite este
            Action::make('inWork')
                ->label(function() {
                    $labelReturn = '';
                    $user = auth()->user();
                    $ahora = Carbon::now();

                    $registrosHoy = Timesheet::where('user_id', $user->id)->whereDate('day_in', $ahora->toDateString())->whereNull('day_out')->where('type', 'work')->get()->count();
                    
                    if($registrosHoy > 0) {
                        $labelReturn = 'Start pause';
                    } else {
                        //$registrosHoy = Timesheet::where('user_id', $user->id)->whereDate('day_in', $ahora->toDateString())->get()->count();
                        $labelReturn = 'Start working';
                        if($registrosHoy > 0) {
                            $labelReturn = 'Start pause';
                        }
                        
                    }
                    return $labelReturn;
                })
                ->color('success')
                ->keyBindings(['command+s', 'ctrl+s'])
                ->requiresConfirmation()
                ->action(function () {
                    $user = auth()->user();
                    $ahora = Carbon::now();

                    $registrosHoy = Timesheet::where('user_id', $user->id)->whereDate('day_in', $ahora->toDateString())->whereNull('day_out')->where('type', 'work')->get()->count();

                    if($registrosHoy > 0) {
                        //Ya hay registro, tenemos que actualizar
                        //Buscamos cuál es el registro que no tiene día_out
                        $registrosHoy = Timesheet::where('user_id', $user->id)->whereDate('day_in', $ahora->toDateString())->whereNull('day_out')->where('type', 'work')->first();
                        $registrosHoy->day_out = $ahora;
                        $registrosHoy->save();

                        Timesheet::create([
                            'user_id' => $user->id,
                            'calendar_id' => 1,
                            'type' => 'pause',
                            'day_in' => $ahora,
                        ]);

                    } else {
                        Timesheet::create([
                            'user_id' => $user->id,
                            'calendar_id' => 1,
                            'type' => 'work',
                            'day_in' => $ahora,
                        ]);

                        $registrosHoy = Timesheet::where('user_id', $user->id)->whereDate('day_in', $ahora->toDateString())->whereNull('day_out')->where('type', 'pause')->first();
                        //Comprobamos que haya un registro de pause para actualizar, si no, es el primer registro de la jornada
                        if(!is_null($registrosHoy)) {
                            $registrosHoy->day_out = $ahora;
                            $registrosHoy->save();
                        }
                    }
                    //Hay que controlar que si ya está el día de hoy debe controlar que actualice el timesheet                    
                }),
            Action::make('finishWork')
                ->label('Finish work day')
                ->color('gray')
                ->keyBindings(['command+f', 'ctrl+f'])
                ->requiresConfirmation()
                ->action(function () {
                    $user = auth()->user();
                    $ahora = Carbon::now();

                    $registrosHoy = Timesheet::where('user_id', $user->id)->whereDate('day_in', $ahora->toDateString())->whereNull('day_out')->where('type', 'work')->get()->count();

                    if($registrosHoy > 0) {
                        //Ya hay registro, tenemos que actualizar
                        //Buscamos cuál es el registro que no tiene día_out
                        $registrosHoy = Timesheet::where('user_id', $user->id)->whereDate('day_in', $ahora->toDateString())->whereNull('day_out')->where('type', 'work')->first();
                        $registrosHoy->day_out = $ahora;
                        $registrosHoy->save();
                    } else {
                        $registrosHoy = Timesheet::where('user_id', $user->id)->whereDate('day_in', $ahora->toDateString())->whereNull('day_out')->where('type', 'pause')->get()->count();
                        
                        if($registrosHoy > 0) {
                            //Ya hay registro, tenemos que actualizar
                            //Buscamos cuál es el registro que no tiene día_out
                            $registrosHoy = Timesheet::where('user_id', $user->id)->whereDate('day_in', $ahora->toDateString())->whereNull('day_out')->where('type', 'pause')->first();
                            $registrosHoy->day_out = $ahora;
                            $registrosHoy->save();
                        }
                    }
                }),
            Action::make('createPDF')
                ->label('Create PDF')
                ->color('primary')
                ->requiresConfirmation()
                //->url(fn ():string => route('exportPDF', ['user' => auth()->user()]), shouldOpenInNewTab:true) //Funciona perfectamente
                ->url(fn ():string => route('exportPDF2', ['user' => auth()->user()]), shouldOpenInNewTab:true)
                /*->action(function (){//Esta funciona perfectamente
                    $pdf = Pdf::loadView('welcome');
                    return response()->streamDownload(function () use ($pdf) {
                        echo $pdf->stream();
                        }, 'invoice.pdf');
                    //return $pdf->download('invoice.pdf');
                }),*/
        ];
    }
}
