<?php

namespace App\Models;

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

    
}
