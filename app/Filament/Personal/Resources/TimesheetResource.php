<?php

namespace App\Filament\Personal\Resources;

use App\Filament\Personal\Resources\TimesheetResource\Pages;
use App\Models\Timesheet;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class TimesheetResource extends Resource
{
    protected static ?string $model = Timesheet::class;

    protected static ?string $navigationIcon = 'heroicon-m-arrows-pointing-in';

    protected static ?string $navigationGroup = 'Employee Management';

    protected static ?int $navigationSort = 2;

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any',
        ];
    }


    public static function getEloquentQuery(): Builder {
        return parent::getEloquentQuery()->where('user_id', auth()->id())->orderBy('day_in', 'desc');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Forms\Components\Select::make('calendar_id')
                    ->relationship(name: 'calendar', titleAttribute: 'name')
                    ->required(),
                Forms\Components\Select::make('type')
                    ->options([
                            'work' => 'Working',
                            'pause' => 'In pause',
                    ])
                    ->required(),
                // Forms\Components\Select::make('user_id')
                //     ->relationship(name: 'user', titleAttribute: 'name')
                //     ->required(),
                Forms\Components\DateTimePicker::make('day_in'),
                Forms\Components\DateTimePicker::make('day_out'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //Nombre de la relación más el campo que queremos que se muestre
                Tables\Columns\TextColumn::make('calendar.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('day_in')
                    ->datetime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('day_out')->datetime()
                    ->datetime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->datetime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('update_at')->datetime()
                    ->datetime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
                SelectFilter::make('type')
                    ->options([
                        'work' => 'Working',
                        'pause' => 'In pause'
                    ]),
                Filter::make('Dated')
                    ->form([
                        DatePicker::make('From'),
                        DatePicker::make('Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        //dd($data);
                        return $query
                            ->when(
                                $data['From'],
                                fn (Builder $query, $date): Builder => $query->whereDate('day_in', '>=', $date),
                            )
                            ->when(
                                $data['Until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('day_in', '<=', $date),
                            );
                    })
                
                
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    //ExportBulkAction::make(), // Si lo metemos aquí se guarda dentro de BulkActions    
                ]),
                ExportBulkAction::make()->exports([
                    ExcelExport::make('table')
                        ->fromTable()
                        ->withFilename('Table '. date('Y-m-d') . ' - export')
                        ->withColumns([
                            Column::make('User'),
                            Column::make('created_at'),
                            Column::make('updated_at'),
                        ]),
                    ExcelExport::make('form')
                        ->fromForm()
                        ->askForFilename()
                        ->askForWriterType(),
                    
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTimesheets::route('/'),
            'create' => Pages\CreateTimesheet::route('/create'),
            'edit' => Pages\EditTimesheet::route('/{record}/edit'),
        ];
    }
}
