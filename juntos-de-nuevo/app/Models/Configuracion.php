<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    protected $table = 'configuracion';

    protected $fillable = [
        'clave',
        'valor',
        'grupo',
        'descripcion',
        'es_secreto',
        'creado_por',
    ];

    protected $casts = [
        'es_secreto' => 'boolean',
    ];

    protected $hidden = [
        'creado_por',
    ];

    /**
     * Scopes
     */
    public function scopeGrupo($query, string $grupo)
    {
        return $query->where('grupo', $grupo);
    }

    public function scopeMailjet($query)
    {
        return $query->whereIn('grupo', ['mailjet', 'brevo']);
    }

    /**
     * Obtener valor por clave
     */
    public static function obtener(string $clave, $default = null)
    {
        $config = self::where('clave', $clave)->first();
        return $config ? $config->valor : $default;
    }

    /**
     * Actualizar o crear configuración
     */
    public static function establecer(string $clave, $valor, ?int $usuarioId = null): self
    {
        return self::updateOrCreate(
            ['clave' => $clave],
            ['valor' => $valor, 'creado_por' => $usuarioId]
        );
    }
}
