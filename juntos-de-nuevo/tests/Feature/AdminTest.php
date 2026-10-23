<?php

use App\Models\Centro;

/*
|--------------------------------------------------------------------------
| AdminTest - Pruebas del módulo de administración
|--------------------------------------------------------------------------
*/

describe('GET /api/admin/centros', function () {

    it('admin puede listar todos los centros', function () {
        $user = adminUser();

        $response = $this->getJson('/api/admin/centros', authHeaders($user));

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'data',
                'meta' => ['current_page', 'last_page', 'total'],
            ]);

        $data = $response->json('data');
        expect(count($data))->toBeGreaterThanOrEqual(2);

        // Verificar que incluye conteos
        $primer = $data[0];
        expect($primer)->toHaveKey('total_ninos');
        expect($primer)->toHaveKey('total_usuarios');
    });

    it('gestor NO puede listar centros (admin only)', function () {
        $user = gestorUser();

        $response = $this->getJson('/api/admin/centros', authHeaders($user));

        $response->assertStatus(403);
    });

    it('sin autenticación retorna 401', function () {
        $response = $this->getJson('/api/admin/centros');

        $response->assertStatus(401);
    });
});

describe('PUT /api/admin/centros/{centro}/validar', function () {

    it('admin puede validar centro pendiente', function () {
        // Crear centro inactivo para el test
        $centro = Centro::create([
            'nombre' => 'Centro de Prueba',
            'tipo' => 'otro',
            'email' => 'test_validar@test.org',
            'activo' => false,
        ]);

        $user = adminUser();

        $response = $this->putJson(
            "/api/admin/centros/{$centro->id}/validar",
            [],
            authHeaders($user)
        );

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Centro validado y activado exitosamente',
            ]);

        $centro->refresh();
        expect($centro->activo)->toBeTrue();
    });

    it('rechaza validar centro ya activo', function () {
        $user = adminUser();
        // Centro #1 ya está activo

        $response = $this->putJson(
            '/api/admin/centros/1/validar',
            [],
            authHeaders($user)
        );

        $response->assertStatus(409)
            ->assertJson([
                'success' => false,
                'error' => ['code' => 'CENTRO_YA_ACTIVO'],
            ]);
    });

    it('gestor NO puede validar centros', function () {
        $user = gestorUser();

        $response = $this->putJson(
            '/api/admin/centros/1/validar',
            [],
            authHeaders($user)
        );

        $response->assertStatus(403);
    });
});

describe('GET /api/admin/estadisticas', function () {

    it('admin obtiene estadísticas correctas', function () {
        $user = adminUser();

        $response = $this->getJson('/api/admin/estadisticas', authHeaders($user));

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'data' => [
                    'total_centros',
                    'centros_activos',
                    'centros_pendientes',
                    'total_ninos',
                    'ninos_encontrados',
                    'ninos_recogidos',
                    'ninos_familia_localizada',
                    'total_recogidas',
                    'total_usuarios',
                    'usuarios_gestores',
                ],
            ]);

        $data = $response->json('data');

        // Verificar valores conocidos de los seeders
        expect($data['total_centros'])->toBeGreaterThanOrEqual(2);
        expect($data['total_ninos'])->toBeGreaterThanOrEqual(6);
        expect($data['total_recogidas'])->toBeGreaterThanOrEqual(1);
        expect($data['total_usuarios'])->toBeGreaterThanOrEqual(3);
    });

    it('gestor NO puede ver estadísticas', function () {
        $user = gestorUser();

        $response = $this->getJson('/api/admin/estadisticas', authHeaders($user));

        $response->assertStatus(403);
    });

    it('sin autenticación retorna 401', function () {
        $response = $this->getJson('/api/admin/estadisticas');

        $response->assertStatus(401);
    });
});
