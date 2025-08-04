<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AssetStateResource\Pages;
use App\Filament\Resources\AssetStateResource\RelationManagers;
use App\Models\AssetState;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AssetStateResource extends Resource
{
    protected static ?string $model = AssetState::class;

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    
    protected static ?string $navigationGroup = 'Activos';
    protected static ?string $navigationLabel = 'Estados';
    protected static ?string $modelLabel = 'Estado';
    protected static ?string $pluralModelLabel = 'Estados';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('codigo')
                    ->required()
                    ->maxLength(12),
                Forms\Components\TextInput::make('nombre')
                    ->required()
                    ->maxLength(255),
                
                Forms\Components\TextInput::make('orden')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\Toggle::make('activo')
                    ->helperText('Al desactivar esta opción, no será posible registrar nuevos elementos; sin embargo, los registros existentes permanecerán disponibles para su visualización.')
                    ->default(true)
                    ->required(),
                
                 Forms\Components\Select::make('color')
                            ->options([
                                'danger'  => 'Crítico (danger)',          // Casos de alto riesgo o falla grave
                                'gray'    => 'Neutro (gray)',             // Sin relevancia especial o estado base
                                'info'    => 'Informativo (info)',        // Estado con valor referencial
                                'primary' => 'Principal (primary)',       // Valor estándar o resaltado general
                                'success' => 'Correcto (success)',        // Algo que fue aprobado o exitoso
                                'warning' => 'Advertencia (warning)',     // Algo que requiere atención pero no es crítico
                            ])
                            ->required()
                            ->searchable()
                            ->native(false),

                // Forms\Components\Fieldset::make('Color de visualización')
                //     ->schema([
                //         Forms\Components\Toggle::make('usar_clase_color')
                //             ->label('Usar clase de color estándar')
                //             ->reactive() // 👈 Esto hace que el formulario se actualice cuando se cambia este campo
                //             ->dehydrated(false), // 👈 Esto lo excluye del guardado
                            
                        
                //         Forms\Components\Select::make('color')
                //             ->label('Clase de color')
                //             ->options([
                //                 'danger'=> 'Peligro',
                //                 'gray'=> 'Base',
                //                 'info'=> 'Información',
                //                 'primary'=> 'Primario',
                //                 'success'=> 'Satisfactorio',
                //                 'warning'=> 'Advertencia',
                //             ])
                //             ->visible(fn ($get) => $get('usar_clase_color') === true)
                //             ->requiredIf('usar_clase_color', true),
                        
                //         Forms\Components\ColorPicker::make('color')
                //             ->label('Color personalizado')
                //             ->default('#ffffff')
                //             ->regex('/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})\b$/')
                //             ->visible(fn ($get) => $get('usar_clase_color') === false)
                //             ->requiredIf('usar_clase_color', false),
                //     ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('codigo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('orden')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('color')
                    ->badge()
                    ->color(fn ($state) => $state), // Usa el valor guardado directamente
                Tables\Columns\IconColumn::make('activo')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListAssetStates::route('/'),
            'create' => Pages\CreateAssetState::route('/create'),
            'edit' => Pages\EditAssetState::route('/{record}/edit'),
        ];
    }
}
