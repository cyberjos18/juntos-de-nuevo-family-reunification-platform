<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Recogida extends Model
{
    use HasFactory;

    protected $fillable = [
        'nino_id',
        'familiar_nombre',
        'familiar_apellido',
        'familiar_parentesco',
        'familiar_telefono',
        'familiar_email',
        'familiar_documento',
        'foto_recogida_url',
        'notas',
        'fecha_recogida',
        'registrado_por',
    ];

    protected $casts = [
        'fecha_recogida' => 'datetime',
    ];

    /**
     * Relaciones
     */
    public function nino(): BelongsTo
    {
        return $this->belongsTo(Nino::class);
    }

    public function registradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }
}
