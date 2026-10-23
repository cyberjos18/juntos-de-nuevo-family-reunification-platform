<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NinoFamiliar extends Model
{
    use HasFactory;

    protected $table = 'ninos_familiares';

    protected $fillable = [
        'nino_id',
        'parentesco',
        'nombre',
        'apellido',
        'telefono',
        'notas',
    ];

    /**
     * Relaciones
     */
    public function nino(): BelongsTo
    {
        return $this->belongsTo(Nino::class);
    }
}
