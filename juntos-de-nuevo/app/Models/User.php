<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'centro_id',
        'nombre',
        'email',
        'password',
        'rol',
        'cargo',
        'activo',
        'token_activacion',
        'foto_identificacion',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'token_activacion',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'activo' => 'boolean',
        ];
    }

    /**
     * Relaciones
     */
    public function centro(): BelongsTo
    {
        return $this->belongsTo(Centro::class);
    }

    public function ninosRegistrados(): HasMany
    {
        return $this->hasMany(Nino::class, 'creado_por');
    }

    public function recogidasRegistradas(): HasMany
    {
        return $this->hasMany(Recogida::class, 'registrado_por');
    }

    /**
     * Métodos helper
     */
    public function esAdmin(): bool
    {
        return $this->rol === 'admin';
    }

    public function esGestor(): bool
    {
        return $this->rol === 'gestor';
    }

    public function estaActivo(): bool
    {
        return $this->activo === true;
    }
}
