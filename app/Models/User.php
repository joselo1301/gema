<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;



class User extends Authenticatable implements CanResetPasswordContract
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasRoles, Notifiable, CanResetPassword, SoftDeletes;

    
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'puesto',
        'empresa',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
    
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function locations(): BelongsToMany
    {
        return $this->belongsToMany(Location::class, 'location_users')
                    ->withTimestamps();
    }

    /**
     * Verificar si el usuario tiene acceso a un asset basado en su locación
     */
    public function canAccessAsset(Asset $asset): bool
    {
        return $this->locations()->where('location_id', $asset->location_id)->exists();
    }

    /**
     * Obtener los IDs de las locaciones asignadas al usuario
     */
    public function getLocationIds(): array
    {
        return $this->locations->pluck('id')->toArray();
    }

    
}

