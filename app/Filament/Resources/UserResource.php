<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\Role;
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
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
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
                ImageColumn::make('avatar')
                    ->label(__('Avatar')),
                TextColumn::make('name')
                    ->label(__('Nombre'))
                    ->sortable()
                    ->searchable()
                    ->description(fn (User $user) => $user->email),
                TextColumn::make('role_id')
                    ->label(__('Role'))
                    ->sortable()
                    ->badge()
                    ->state(fn (User $user) => $user->role->description)
                    ->color(fn (User $user) => match ($user->role_id) {
                        Role::ADMIN => 'danger',
                        Role::TEACHER => 'warning',
                        Role::STUDENT => 'success',
                    }),
                ToggleColumn::make('active')
                    ->label(__('Activo'))
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('Creado'))
                    ->sortable()
                    ->date('d/m/Y H:i'),
            ])
            ->filters([
                SelectFilter::make('role_id')
                    ->label(__('Role'))
                    ->options(Role::pluck('description', 'id')->toArray()),
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
