<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourseResource\Pages;
use App\Filament\Resources\CourseResource\RelationManagers;
use App\Models\Course;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

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
                            FileUpload::make('image')
                                ->label(__('Imagen del Curso'))
                                ->image()
                                ->required()
                                ->directory('courses')  
                                ->columnSpanFull(),
                            Grid::make(3)
                                ->schema([
                                    Select::make('user_id')
                                        ->label(__('Profesor'))
                                        ->required()
                                        ->options(
                                            User::teachers()
                                                ->active()
                                                ->get()
                                                ->pluck('name', 'id')
                                        ),
                                    TextInput::make('name')
                                        ->label(__('Nombre'))
                                        ->autofocus()
                                        ->minLength(6)
                                        ->maxLength(200)
                                        ->unique(static::getModel(), 'name', ignoreRecord: true)
                                        ->live(debounce: 500)
                                        ->afterStateUpdated(function (Set $set, ?string $old, ?string $state){
                                            $set('slug', Str::slug($state));  
                                        })
                                        ->required(),
                                        TextInput::make('slug')
                                            ->label(__('Slug'))
                                ]),
                                RichEditor::make('description')
                                    ->toolbarButtons([
                                        'attachFiles',
                                        'blockquote',
                                        'bold',
                                        'bulletList',
                                        'codeBlock',
                                        'h2',
                                        'h3',
                                        'italic',
                                        'link',
                                        'orderedList',
                                        'redo',
                                        'strike',
                                        'undo'
                                        
                                    ])
                                    ->label(__('Descripción'))
                                    ->required()
                                    ->minLength(10)
                                    ->maxLength(5000)
                                    ->columnSpanFull(),
                                    
                        ]),
                    Step::make(__('Configuración'))
                        ->schema([
                            Checkbox::make('published')
                                ->label(__('Publicado')),
                            Checkbox::make('featured')
                                ->label(__('Destacado')),
                        ]),
                    Step::make(__('Unidades'))
                        ->schema([
                            Repeater::make('units')
                                ->relationship()
                                ->label(__('Unidades'))
                                ->addActionLabel(__('Agregar Unidad'))
                                ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                                ->reorderableWithButtons()
                                ->collapsible()
                                ->cloneable()
                                ->orderColumn()
                                ->schema([
                                    Grid::make()
                                        ->schema([
                                            TextInput::make('name')
                                                ->label(__('Nombre'))
                                                ->autofocus()
                                                ->minLength(6)
                                                ->maxLength(200)
                                                ->unique(static::getModel(), 'name', ignoreRecord: true)
                                                ->live(debounce: 500)
                                                ->afterStateUpdated(function (Set $set, ?string $old, ?string $state){
                                                    $set('slug', Str::slug($state));  
                                                })
                                                ->required(),
                                            TextInput::make('slug')
                                                ->label(__('Slug')),
                                        ]),
                                    RichEditor::make('content')
                                        ->toolbarButtons([
                                            'attachFiles',
                                            'blockquote',
                                            'bold',
                                            'bulletList',
                                            'codeBlock',
                                            'h2',
                                            'h3',
                                            'italic',
                                            'link',
                                            'orderedList',
                                            'redo',
                                            'strike',
                                            'undo'
                                            
                                        ])
                                        ->label(__('Contenido de la unidad'))
                                        ->required()
                                        ->minLength(10)
                                        ->maxLength(5000)
                                        ->columnSpanFull(),
                                    Checkbox::make('published')
                                        ->label(__('Publicado')),
                                    Checkbox::make('featured')
                                        ->label(__('Gratuito')),
                                ]),
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
                ImageColumn::make('image')
                    ->label(__('Imagen')),
                TextColumn::make('name')
                    ->label(__('Nombre'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('teacher.name')
                    ->label(__('Profesor'))
                    ->searchable()
                    ->sortable(),
                ToggleColumn::make('published')
                    ->label(__('Publicado'))
                    ->sortable(),
                ToggleColumn::make('featured')
                    ->label(__('Destacado'))
                    ->sortable(),
                TextColumn::make('units_count')
                    ->label(__('Unidades'))
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color('success')
                    ->counts('units'),
                TextColumn::make('created_at')
                    ->label(__('Creado'))
                    ->date('d/m/Y H:i')
                    ->sortable(),
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
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
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
