<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?int $navigationSort = 10;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function getLabel(): ?string
    {
        return __('Usuario');
    }

    public static function getNavigationLabel(): string
    {
        return __('Usuarios');
    }    

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('avatar')
                    ->image()
                    ->disk('public')
                    ->directory('avatars')
                    ->label(__('Avatar'))
                    ->columnSpanFull(),
                Grid::make(3)
                    ->schema([
                        Select::make('role_id')
                            ->relationship('role', 'name')
                            ->required()
                            ->label(__('Role')),
                        TextInput::make('name')
                            ->autofocus()
                            ->required()
                            ->maxLength(200)
                            ->label(__('Name')),
                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(200)
                            ->label(__('Email'))
                            ->unique(static::getModel(), 'email', ignoreRecord: true) // ignoreRecord: true para ignorar la validación del email cuando estemos editando
                    ]),
                TextInput::make('password')
                    ->password()
                    ->required(fn (string $context): bool => $context === 'create') // Solo requerido en el formulario de creación
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))        // Encriptamos la contraseña
                    ->dehydrated(fn ($state) => filled($state))                    // Evitar sobreescribir la contraseña si no viene dada
                    ->confirmed()
                    ->minLength(8)
                    ->maxLength(200)
                    ->label(__('Constraseña')),
                TextInput::make('password_confirmation')
                    ->password()
                    ->label(__('Confirmar contraseña')),
                Checkbox::make('active')
                    ->label(__('Activo')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
