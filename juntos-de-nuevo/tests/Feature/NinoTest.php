<?php

use App\Models\Nino;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| NinoTest - Pruebas del módulo de niños
|--------------------------------------------------------------------------
*/

describe('GET /api/ninos (público)', function () {

    it('retorna listado público de niños', function () {
        $response = $this->getJson('/api/ninos');

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'success',
                'data',
                'meta' => ['current_page', 'last_page', 'per_page', 'total'],
            ]);

        // Verificar que hay datos
        $data = $response->json('data');
        expect(count($data))->toBeGreaterThan(0);
    });

    it('listado público no incluye niños recogidos', function () {
        $response = $this->getJson('/api/ninos');
        $data = $response->json('data');

        foreach ($data as $nino) {
            expect($nino['estado'])->not->toBe('recogido');
        }
    });

    it('listado público no muestra datos sensibles sin auth', function () {
        $response = $this->getJson('/api/ninos');
        $data = $response->json('data');

        if (count($data) > 0) {
            $primer = $data[0];
            expect($primer)->not->toHaveKey('sexo');
            expect($primer)->not->toHaveKey('direccion_previa');
            expect($primer)->not->toHaveKey('nombre_madre');
            expect($primer)->not->toHaveKey('nombre_padre');
        }
    });

    it('listado muestra datos sensibles con auth', function () {
        $user = adminUser();

        $response = $this->getJson('/api/ninos', authHeaders($user));

        $response->assertStatus(200);
        $data = $response->json('data');

        if (count($data) > 0) {
            $primer = $data[0];
            expect($primer)->toHaveKey('sexo');
            expect($primer)->toHaveKey('direccion_previa');
        }
    });

    it('soporta filtro por centro_id', function () {
        $response = $this->getJson('/api/ninos?centro_id=1');

        $response->assertStatus(200);
    });
});

describe('GET /api/ninos/{id} (público)', function () {

    it('retorna ficha de niño existente', function () {
        $response = $this->getJson('/api/ninos/1');

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'data' => ['id', 'nombre', 'apellido', 'edad', 'estado', 'centro'],
            ]);
    });

    it('ficha pública oculta datos sensibles', function () {
        $response = $this->getJson('/api/ninos/1');
        $data = $response->json('data');

        expect($data)->not->toHaveKey('sexo');
        expect($data)->not->toHaveKey('familiares');
    });

    it('ficha con auth muestra familiares y datos sensibles', function () {
        $user = adminUser();

        $response = $this->getJson('/api/ninos/1', authHeaders($user));

        $response->assertStatus(200);
        $data = $response->json('data');

        expect($data)->toHaveKey('sexo');
        expect($data)->toHaveKey('familiares');
        expect($data)->toHaveKey('notas');
    });

    it('retorna 404 para niño inexistente', function () {
        $response = $this->getJson('/api/ninos/9999');

        $response->assertStatus(404);
    });
});

describe('POST /api/ninos (gestor)', function () {

    it('gestor puede registrar niño con foto', function () {
        Storage::fake('public');
        $user = gestorUser();

        $response = $this->postJson('/api/ninos', [
            'foto' => UploadedFile::fake()->image('nino.jpg', 400, 400),
            'nombre' => 'Test',
            'apellido' => 'Niño',
            'edad' => 5,
            'sexo' => 'M',
            'direccion_previa' => 'Calle Principal #10',
            'nombre_madre' => 'María Test',
            'nombre_padre' => 'Pedro Test',
        ], authHeaders($user));

        $response->assertStatus(201)
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'data' => ['id', 'nombre'],
            ]);
    });

    it('gestor puede registrar niño con familiares', function () {
        Storage::fake('public');
        $user = gestorUser();

        $response = $this->postJson('/api/ninos', [
            'foto' => UploadedFile::fake()->image('nino.jpg'),
            'nombre' => 'Test',
            'apellido' => 'ConFamiliar',
            'edad' => 3,
            'familiares' => [
                ['parentesco' => 'Tía', 'nombre' => 'Rosa', 'telefono' => '+58-414-5551234'],
            ],
        ], authHeaders($user));

        $response->assertStatus(201);
    });

    it('rechaza registro sin foto', function () {
        $user = gestorUser();

        $response = $this->postJson('/api/ninos', [
            'nombre' => 'Test',
            'apellido' => 'SinFoto',
            'edad' => 5,
        ], authHeaders($user));

        $response->assertStatus(422);
    });

    it('rechaza edad mayor a 17', function () {
        Storage::fake('public');
        $user = gestorUser();

        $response = $this->postJson('/api/ninos', [
            'foto' => UploadedFile::fake()->image('nino.jpg'),
            'nombre' => 'Test',
            'apellido' => 'Mayor',
            'edad' => 25,
        ], authHeaders($user));

        $response->assertStatus(422);
    });

    it('rechaza sin autenticación', function () {
        $response = $this->postJson('/api/ninos', [
            'nombre' => 'Test',
            'apellido' => 'Test',
        ]);

        $response->assertStatus(401);
    });

    it('admin NO puede registrar niño vía API (ruta exclusiva de gestor)', function () {
        Storage::fake('public');
        $user = adminUser();

        $response = $this->postJson('/api/ninos', [
            'foto' => UploadedFile::fake()->image('nino.jpg'),
            'nombre' => 'Test',
            'apellido' => 'PorAdmin',
            'edad' => 5,
        ], authHeaders($user));

        $response->assertStatus(403);
    });
});

describe('PUT /api/ninos/{id} (gestor)', function () {

    it('gestor puede actualizar niño de su centro', function () {
        $user = gestorUser();
        // Niño #1 pertenece al centro 1 (Protección Civil)

        $response = $this->putJson('/api/ninos/1', [
            'nombre' => 'Carlos Actualizado',
            'notas' => 'Nota actualizada en test',
        ], authHeaders($user));

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    });

    it('gestor NO puede actualizar niño de otro centro', function () {
        $user = gestorHospitalUser();
        // Niño #1 pertenece al centro 1, no al hospital

        $response = $this->putJson('/api/ninos/1', [
            'nombre' => 'Hack Intento',
        ], authHeaders($user));

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'error' => ['code' => 'FORBIDDEN'],
            ]);
    });

    it('admin puede actualizar cualquier niño', function () {
        $user = adminUser();

        $response = $this->putJson('/api/ninos/1', [
            'nombre' => 'Carlos Por Admin',
        ], authHeaders($user));

        // Admin puede actualizar cualquier niño sin restricción de centro
        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    });

    it('rechaza edad negativa', function () {
        $user = gestorUser();

        $response = $this->putJson('/api/ninos/1', [
            'edad' => -1,
        ], authHeaders($user));

        $response->assertStatus(422);
    });

    it('rechaza edad mayor a 17', function () {
        $user = gestorUser();

        $response = $this->putJson('/api/ninos/1', [
            'edad' => 18,
        ], authHeaders($user));

        $response->assertStatus(422);
    });

    it('rechaza sexo inválido', function () {
        $user = gestorUser();

        $response = $this->putJson('/api/ninos/1', [
            'sexo' => 'X',
        ], authHeaders($user));

        $response->assertStatus(422);
    });

    it('rechaza estado inválido', function () {
        $user = gestorUser();

        $response = $this->putJson('/api/ninos/1', [
            'estado' => 'desaparecido',
        ], authHeaders($user));

        $response->assertStatus(422);
    });

    it('rechaza fecha de nacimiento futura', function () {
        $user = gestorUser();

        $response = $this->putJson('/api/ninos/1', [
            'fecha_nacimiento' => now()->addDay()->toDateString(),
        ], authHeaders($user));

        $response->assertStatus(422);
    });
});

describe('GET /api/gestor/ninos', function () {

    it('gestor ve solo niños de su centro', function () {
        $user = gestorUser();

        $response = $this->getJson('/api/gestor/ninos', authHeaders($user));

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $data = $response->json('data');
        expect(count($data))->toBeGreaterThan(0);
    });

    it('rechaza sin autenticación', function () {
        $response = $this->getJson('/api/gestor/ninos');

        $response->assertStatus(401);
    });

    it('admin NO puede acceder (ruta exclusiva de gestor)', function () {
        $user = adminUser();

        $response = $this->getJson('/api/gestor/ninos', authHeaders($user));

        $response->assertStatus(403);
    });
});
