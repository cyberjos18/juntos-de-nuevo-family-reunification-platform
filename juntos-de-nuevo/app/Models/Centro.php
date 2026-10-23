<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Centro extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'tipo',
        'email',
        'telefono',
        'direccion',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    /**
     * Relaciones
     */
    public function usuarios(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Usuario responsable (primer gestor registrado del centro)
     */
    public function usuarioResponsable(): HasOne
    {
        return $this->hasOne(User::class)->orderBy('id', 'asc');
    }

    public function ninos(): HasMany
    {
        return $this->hasMany(Nino::class);
    }

    /**
     * Scopes
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopePorTipo($query, string $tipo)
    {
        return $query->where('tipo', $tipo);
    }
}
