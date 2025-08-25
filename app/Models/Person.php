<?php

namespace App\Models;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;

class Person extends Model
{
    /** @use HasFactory<\Database\Factories\PersonFactory> */
    use HasRoles, HasFactory, SoftDeletes;

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function failureReports()
    {
        return $this->belongsToMany(FailureReport::class);
        // ->withTimestamps();
    }

    public static function getForm($location_id = null): array
    {
        return [
            TextInput::make('nombres')
                ->required()
                ->maxLength(255),
            TextInput::make('apellidos')
                ->required()
                ->maxLength(255),
            TextInput::make('cargo')
                ->required()    
                ->maxLength(255),
            TextInput::make('empresa')
                ->required()
                ->maxLength(255),
            Select::make('location_id')
                ->required()
                ->label('Planta o Terminal')
                ->relationship('location', 'nombre')
                ->default($location_id)
                ->disabled($location_id !== null),
        ];
    }    

}
