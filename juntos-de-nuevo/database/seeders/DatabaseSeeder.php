<?php

namespace Database\Seeders;

use App\Models\Configuracion;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Configuración por defecto
        Configuracion::updateOrCreate(
            ['clave' => 'HABILITAR_REGISTRO_CENTRO'],
            ['valor' => '1', 'grupo' => 'registro', 'descripcion' => 'Habilitar registro de nuevos centros']
        );

        Configuracion::updateOrCreate(
            ['clave' => 'BREVO_API_KEY'],
            ['valor' => '', 'grupo' => 'brevo', 'descripcion' => 'API Key v3 de Brevo para envío de correos', 'es_secreto' => true]
        );

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
