<?php

namespace App\Filament\Resources\PlanResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;


class UsersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';

    public static function getTitle(Model $ownerRecord, string $pageClass): string 
    {
        return __('Usuarios del plan :plan', ['plan' => $ownerRecord->name]);
    }

    protected static function getRecordLabel(): ?string
    {
        return __('Usuario');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Nombre')),
                ToggleColumn::make('pivot.active')
                    ->label(__('Activo'))
                    ->updateStateUsing(function ($record, $state) {        //Define la lógica para actualizar el estado de la relación
                        $record->pivot->active = $state;
                        $record->pivot->save();
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make(),
            ])
            ->actions([
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\AttachAction::make(),
            ])
            ->emptyStateDescription(__('No hay usuarios para este plan'));
    }
}
