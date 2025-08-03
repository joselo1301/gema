<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\Wizard;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Roles y usuarios';
    protected static ?string $navigationLabel = 'Usuarios';
    protected static ?string $modelLabel = 'Usuario';
    protected static ?string $pluralModelLabel = 'Usuarios';
    


    //  public static function canViewAny(): bool
    // {
    //     return true; // Temporalmente permitir a todos para debug
    // }

    public static function form(Form $form): Form
    {
        return $form
            ->columns(1)
            ->schema([
                

                Wizard::make([
                     
                    Wizard\Step::make('Datos basicos')
                        
                        ->schema([
                            Fieldset::make('Datos principales')
                            ->columns(3)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('email')
                                    ->email()
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('puesto')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('empresa')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('password')
                                    ->helperText("La contraseña asignada por defecto es 'C0ntraseña'")
                                    ->password()
                                    ->visibleOn('create')
                                    ->default("C0ntraseña")
                                    ->readOnly(true)
                                    ->maxLength(255),
                            ]),
                        
                            Fieldset::make('Estado Inicial')
                                    
                                ->schema([
                                    Forms\Components\Toggle::make('password_change_required')
                                        ->required()
                                        ->default(true),
                                    Forms\Components\Toggle::make('activo')
                                        ->required()
                                        ->default(false),
                                ]),
                        ]),
                    Wizard\Step::make('Centros de trabajo')
                        ->schema([
                            Forms\Components\CheckboxList::make('locations')
                                ->label('Plantas o Terminales')
                                ->relationship('locations', 'nombre') // Asumiendo que el modelo User tiene una relación 'locations' y el campo visible es 'nombre'
                                ->required()
                                ->columns(2)
                                ->helperText('Selecciona uno o varios centros de trabajo a los que puede pertenecer el usuario'),
                        ]),
                    Wizard\Step::make('Roles')
                        ->schema([
                            Forms\Components\CheckboxList::make('roles')
                                ->relationship('roles', 'name')
                                ->required()
                                ->columns(3)
                                ->searchable(),
                        ]),
                    ]),



                
                
                    
                
                


               
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('puesto')
                    ->searchable(),
                Tables\Columns\TextColumn::make('empresa')
                    ->searchable(),
                Tables\Columns\IconColumn::make('activo')
                    ->boolean(),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
