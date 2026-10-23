<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Nino extends Model
{
    use HasFactory;

    protected $fillable = [
        'centro_id',
        'foto_url',
        'nombre',
        'apellido',
        'edad',
        'fecha_nacimiento',
        'sexo',
        'direccion_previa',
        'nombre_madre',
        'nombre_padre',
        'estado',
        'notas',
        'creado_por',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
    ];

    /**
     * Relaciones
     */
    public function centro(): BelongsTo
    {
        return $this->belongsTo(Centro::class);
    }

    public function creadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function familiares(): HasMany
    {
        return $this->hasMany(NinoFamiliar::class, 'nino_id');
    }

    public function recogida(): HasOne
    {
        return $this->hasOne(Recogida::class);
    }

    /**
     * Scopes
     */
    public function scopeEncontrados($query)
    {
        return $query->where('estado', 'encontrado');
    }

    public function scopeFamiliaLocalizada($query)
    {
        return $query->where('estado', 'familia_localizada');
    }

    public function scopeRecogidos($query)
    {
        return $query->where('estado', 'recogido');
    }

    public function scopePorCentro($query, int $centroId)
    {
        return $query->where('centro_id', $centroId);
    }

    /**
     * Métodos helper
     */
    public function estaRecogido(): bool
    {
        return $this->estado === 'recogido';
    }

    public function puedeSerRecogido(): bool
    {
        return !$this->estaRecogido() && !$this->recogida()->exists();
    }

    public function marcarComoRecogido(): bool
    {
        return $this->update(['estado' => 'recogido']);
    }
}
