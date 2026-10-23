<?php

use App\Models\Centro;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| CentroActivoTest - Middleware EnsureCentroActivo
|--------------------------------------------------------------------------
|
| Un gestor cuyo centro todavía no ha sido validado por un admin no debe
| poder operar (crear niños, ver "sus niños", registrar recogidas), ni por
| web ni por API, aunque su propia cuenta de usuario esté activa.
|
*/

function gestorDeCentroInactivo(): User
{
    $centro = Centro::create([
        'nombre' => 'Centro Pendiente ' . uniqid(),
        'tipo' => 'otro',
        'email' => 'centro_pendiente_' . uniqid() . '@test.org',
        'activo' => false,
    ]);

    return User::create([
        'centro_id' => $centro->id,
        'nombre' => 'Gestor Centro Pendiente',
        'email' => 'gestor_pendiente_' . uniqid() . '@test.org',
        'password' => Hash::make('password123'),
        'rol' => 'gestor',
        'cargo' => 'Coordinador',
        'activo' => true,
        'email_verified_at' => now(),
    ]);
}

describe('EnsureCentroActivo - Web (gestor)', function () {

    it('bloquea el dashboard de gestor si el centro no está activo', function () {
        $user = gestorDeCentroInactivo();

        $response = $this->actingAs($user)->get(route('gestor.dashboard'));

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('error');
    });

    it('bloquea el listado de niños del gestor si el centro no está activo', function () {
        $user = gestorDeCentroInactivo();

        $response = $this->actingAs($user)->get(route('gestor.ninos.index'));

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('error');
    });

    it('bloquea el registro de niños si el centro no está activo', function () {
        Storage::fake('public');
        $user = gestorDeCentroInactivo();

        $response = $this->actingAs($user)->post(route('gestor.ninos.store'), [
            'foto' => UploadedFile::fake()->image('nino.jpg'),
            'nombre' => 'Test',
            'apellido' => 'CentroInactivo',
            'edad' => 5,
        ]);

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('ninos', ['apellido' => 'CentroInactivo']);
    });
});

describe('EnsureCentroActivo - API (gestor)', function () {

    it('rechaza creación de niño con código CENTRO_INACTIVO', function () {
        Storage::fake('public');
        $user = gestorDeCentroInactivo();

        $response = $this->postJson('/api/ninos', [
            'foto' => UploadedFile::fake()->image('nino.jpg'),
            'nombre' => 'Test',
            'apellido' => 'ApiCentroInactivo',
        ], authHeaders($user));

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'error' => ['code' => 'CENTRO_INACTIVO'],
            ]);
    });

    it('rechaza listado de "mis niños" con código CENTRO_INACTIVO', function () {
        $user = gestorDeCentroInactivo();

        $response = $this->getJson('/api/gestor/ninos', authHeaders($user));

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'error' => ['code' => 'CENTRO_INACTIVO'],
            ]);
    });

    it('rechaza registro de recogida con código CENTRO_INACTIVO', function () {
        Storage::fake('public');
        $user = gestorDeCentroInactivo();

        $response = $this->postJson('/api/ninos/3/recogida', [
            'familiar_nombre' => 'Test',
            'familiar_apellido' => 'Test',
            'familiar_parentesco' => 'Madre',
            'familiar_telefono' => '+58-412-5550000',
            'foto_recogida' => UploadedFile::fake()->image('recogida.jpg'),
        ], authHeaders($user));

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'error' => ['code' => 'CENTRO_INACTIVO'],
            ]);
    });
});

describe('EnsureCentroActivo - Admin (sin centro)', function () {

    it('el admin no se ve afectado por la validación de centro', function () {
        $user = adminUser();

        // PUT /api/ninos/{id} está protegido por 'role:gestor,admin' + 'centro.activo'.
        // El admin no tiene centro_id y debe poder pasar igualmente.
        $response = $this->putJson('/api/ninos/1', [
            'nombre' => 'Nombre Actualizado Por Admin',
        ], authHeaders($user));

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    });
});
