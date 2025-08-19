<?php

namespace App\Models;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;

class People extends Model
{
    use HasRoles, SoftDeletes;

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function failureReport(): BelongsToMany
    {
        return $this->belongsToMany(FailureReport::class);
    }

    public function failureReports(): BelongsToMany
    {
        return $this->belongsToMany(FailureReport::class, 'failure_report_people')
            ->withTimestamps();
    }

     public static function getForm(): array
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
                ->relationship('location', 'nombre'),
        ];
    }
}
