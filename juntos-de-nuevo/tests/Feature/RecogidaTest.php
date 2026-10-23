<?php

use App\Models\Nino;
use App\Models\Recogida;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| RecogidaTest - Pruebas del módulo de recogidas
|--------------------------------------------------------------------------
*/

describe('POST /api/ninos/{nino}/recogida', function () {

    it('gestor puede registrar recogida correctamente', function () {
        Storage::fake('public');
        $user = gestorUser();

        // Niño #3 (Miguel Ángel López) está en centro 1 y NO está recogido
        $response = $this->postJson('/api/ninos/3/recogida', [
            'familiar_nombre' => 'Carmen',
            'familiar_apellido' => 'López',
            'familiar_parentesco' => 'Madre',
            'familiar_telefono' => '+58-412-5553344',
            'familiar_email' => 'carmen@email.com',
            'familiar_documento' => 'V-12.345.678',
            'foto_recogida' => UploadedFile::fake()->image('recogida.jpg', 400, 400),
            'notas' => 'Madre verificada con cédula',
        ], authHeaders($user));

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Recogida registrada exitosamente',
            ])
            ->assertJsonStructure([
                'data' => ['recogida_id', 'nino', 'familiar', 'parentesco', 'fecha'],
            ]);
    });

    it('recogida cambia estado del niño a recogido', function () {
        Storage::fake('public');
        $user = gestorUser();

        $this->postJson('/api/ninos/3/recogida', [
            'familiar_nombre' => 'Carmen',
            'familiar_apellido' => 'López',
            'familiar_parentesco' => 'Madre',
            'familiar_telefono' => '+58-412-5553344',
            'foto_recogida' => UploadedFile::fake()->image('recogida.jpg'),
        ], authHeaders($user));

        $nino = Nino::find(3);
        expect($nino->estado)->toBe('recogido');
    });

    it('rechaza recogida de niño ya recogido', function () {
        Storage::fake('public');
        $user = gestorHospitalUser();

        // Niño #4 (Valentina) ya está recogida
        $response = $this->postJson('/api/ninos/4/recogida', [
            'familiar_nombre' => 'Test',
            'familiar_apellido' => 'Test',
            'familiar_parentesco' => 'Padre',
            'familiar_telefono' => '+58-414-5551234',
            'foto_recogida' => UploadedFile::fake()->image('recogida.jpg'),
        ], authHeaders($user));

        $response->assertStatus(409)
            ->assertJson([
                'success' => false,
                'error' => ['code' => 'NINO_YA_RECOGIDO'],
            ]);
    });

    it('gestor NO puede recoger niño de otro centro', function () {
        Storage::fake('public');
        $user = gestorHospitalUser();

        // Niño #1 pertenece a Protección Civil (centro 1)
        $response = $this->postJson('/api/ninos/1/recogida', [
            'familiar_nombre' => 'Hack',
            'familiar_apellido' => 'Intento',
            'familiar_parentesco' => 'Tío',
            'familiar_telefono' => '+58-414-5551234',
            'foto_recogida' => UploadedFile::fake()->image('recogida.jpg'),
        ], authHeaders($user));

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'error' => ['code' => 'FORBIDDEN'],
            ]);
    });

    it('rechaza recogida sin foto', function () {
        $user = gestorUser();

        $response = $this->postJson('/api/ninos/3/recogida', [
            'familiar_nombre' => 'Carmen',
            'familiar_apellido' => 'López',
            'familiar_parentesco' => 'Madre',
            'familiar_telefono' => '+58-412-5553344',
        ], authHeaders($user));

        $response->assertStatus(422);
    });

    it('rechaza recogida sin teléfono del familiar', function () {
        Storage::fake('public');
        $user = gestorUser();

        $response = $this->postJson('/api/ninos/3/recogida', [
            'familiar_nombre' => 'Carmen',
            'familiar_apellido' => 'López',
            'familiar_parentesco' => 'Madre',
            'foto_recogida' => UploadedFile::fake()->image('recogida.jpg'),
        ], authHeaders($user));

        $response->assertStatus(422);
    });

    it('rechaza recogida sin autenticación', function () {
        $response = $this->postJson('/api/ninos/3/recogida', [
            'familiar_nombre' => 'Test',
            'familiar_apellido' => 'Test',
            'familiar_parentesco' => 'Test',
            'familiar_telefono' => '123',
        ]);

        $response->assertStatus(401);
    });

    it('admin NO puede registrar recogida vía API (ruta exclusiva de gestor)', function () {
        Storage::fake('public');
        $user = adminUser();

        $response = $this->postJson('/api/ninos/3/recogida', [
            'familiar_nombre' => 'Carmen',
            'familiar_apellido' => 'López',
            'familiar_parentesco' => 'Madre',
            'familiar_telefono' => '+58-412-5553344',
            'foto_recogida' => UploadedFile::fake()->image('recogida.jpg'),
        ], authHeaders($user));

        $response->assertStatus(403);
    });
});

describe('GET /api/admin/recogidas', function () {

    it('admin puede listar todas las recogidas', function () {
        $user = adminUser();

        $response = $this->getJson('/api/admin/recogidas', authHeaders($user));

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'data',
                'meta' => ['current_page', 'last_page', 'total'],
            ]);
    });

    it('gestor NO puede listar recogidas de admin', function () {
        $user = gestorUser();

        $response = $this->getJson('/api/admin/recogidas', authHeaders($user));

        $response->assertStatus(403);
    });
});

describe('GET /admin/recogidas/{recogida}', function () {

    it('admin puede ver detalle de una recogida', function () {
        $user = adminUser();

        $response = $this->actingAs($user)->get(route('admin.recogidas.show', ['recogida' => 1]));

        $response->assertStatus(200);
        $response->assertViewIs('admin.recogidas.show');
        $response->assertViewHas('recogida');
    });

    it('admin ve los datos del niño en la vista de recogida', function () {
        $user = adminUser();

        $response = $this->actingAs($user)->get(route('admin.recogidas.show', ['recogida' => 1]));

        $response->assertStatus(200);
        $recogida = $response->viewData('recogida');
        expect($recogida->nino)->not->toBeNull();
    });

    it('admin ve los datos del familiar en la vista de recogida', function () {
        $user = adminUser();

        $response = $this->actingAs($user)->get(route('admin.recogidas.show', ['recogida' => 1]));

        $response->assertStatus(200);
        $recogida = $response->viewData('recogida');
        expect($recogida->familiar_nombre)->not->toBeNull();
    });

    it('devuelve 404 para recogida inexistente', function () {
        $user = adminUser();

        $response = $this->actingAs($user)->get(route('admin.recogidas.show', ['recogida' => 99999]));

        $response->assertStatus(404);
    });

    it('redirige a login sin autenticación', function () {
        $response = $this->get(route('admin.recogidas.show', ['recogida' => 1]));

        $response->assertRedirect('/login');
    });

    it('gestor no puede acceder al detalle de recogida de admin', function () {
        $user = gestorUser();

        $response = $this->actingAs($user)->get(route('admin.recogidas.show', ['recogida' => 1]));

        $response->assertRedirect(route('home'));
    });
});
