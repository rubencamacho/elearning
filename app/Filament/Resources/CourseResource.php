<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourseResource\Pages;
use App\Filament\Resources\CourseResource\RelationManagers;
use App\Models\Course;
use Filament\Forms;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CourseResource extends Resource
{
    protected static ?string $model = Course::class;

    protected static ?string $navigationIcon = 'eos-machine-learning-o';

    protected static ?int $navigationSort = 30;

    public static function getLabel(): ?string
    {
        return __('Curso');
    }

    public static function getNavigationLabel(): string
    {
        return __('Cursos');
    }



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Step::make(__('Datos del curso'))
                        ->schema([
                            
                        ]),
                    Step::make(__('Configuración'))
                        ->schema([
                            
                        ]),
                    Step::make(__('Unidades'))
                        ->schema([
                            
                        ]),
                ])
                    ->columnSpanFull()
                    ->persistStepInQueryString('course-wizard-step'),	//Al refrescar la página nos mantendremos en el mismo paso
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
            'index' => Pages\ListCourses::route('/'),
            'create' => Pages\CreateCourse::route('/create'),
            'edit' => Pages\EditCourse::route('/{record}/edit'),
        ];
    }
}
