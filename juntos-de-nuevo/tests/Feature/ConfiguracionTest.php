<?php

use App\Models\Configuracion;

/*
|--------------------------------------------------------------------------
| ConfiguracionTest - Pruebas del módulo de configuración
|--------------------------------------------------------------------------
*/

describe('GET /api/admin/configuracion/mailjet', function () {

    it('admin puede obtener configuración de Mailjet', function () {
        $user = adminUser();

        $response = $this->getJson('/api/admin/configuracion/mailjet', authHeaders($user));

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'data',
                'configurado',
            ]);

        $data = $response->json('data');
        expect(count($data))->toBeGreaterThanOrEqual(7);

        // Verificar claves esperadas
        $claves = array_column($data, 'clave');
        expect($claves)->toContain('MAILJET_API_KEY');
        expect($claves)->toContain('MAILJET_SECRET_KEY');
        expect($claves)->toContain('MAIL_FROM_ADDRESS');
    });

    it('gestor NO puede obtener configuración de Mailjet', function () {
        $user = gestorUser();

        $response = $this->getJson('/api/admin/configuracion/mailjet', authHeaders($user));

        $response->assertStatus(403);
    });

    it('sin autenticación retorna 401', function () {
        $response = $this->getJson('/api/admin/configuracion/mailjet');

        $response->assertStatus(401);
    });
});

describe('PUT /api/admin/configuracion/mailjet', function () {

    it('admin puede actualizar configuración de Mailjet', function () {
        $user = adminUser();

        $response = $this->putJson('/api/admin/configuracion/mailjet', [
            'MAIL_MAILER' => 'smtp',
            'MAILJET_API_KEY' => 'test_api_key_12345',
            'MAILJET_SECRET_KEY' => 'test_secret_key_67890',
            'MAIL_FROM_ADDRESS' => 'test@findchild.org',
            'MAIL_FROM_NAME' => 'Reencuentro Test',
        ], authHeaders($user));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Configuración de Mailjet actualizada exitosamente',
                'configurado' => true,
            ]);

        // Verificar que se guardó en la BD
        expect(Configuracion::obtener('MAILJET_API_KEY'))->toBe('test_api_key_12345');
        expect(Configuracion::obtener('MAILJET_SECRET_KEY'))->toBe('test_secret_key_67890');
    });

    it('admin puede actualizar configuración parcial', function () {
        $user = adminUser();

        $response = $this->putJson('/api/admin/configuracion/mailjet', [
            'MAIL_FROM_NAME' => 'Nuevo Nombre',
        ], authHeaders($user));

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        expect(Configuracion::obtener('MAIL_FROM_NAME'))->toBe('Nuevo Nombre');
    });

    it('rechaza MAIL_MAILER inválido', function () {
        $user = adminUser();

        $response = $this->putJson('/api/admin/configuracion/mailjet', [
            'MAIL_MAILER' => 'invalido',
        ], authHeaders($user));

        $response->assertStatus(422);
    });

    it('rechaza MAIL_PORT fuera de rango', function () {
        $user = adminUser();

        $response = $this->putJson('/api/admin/configuracion/mailjet', [
            'MAIL_PORT' => 99999,
        ], authHeaders($user));

        $response->assertStatus(422);
    });

    it('rechaza MAIL_FROM_ADDRESS sin formato email', function () {
        $user = adminUser();

        $response = $this->putJson('/api/admin/configuracion/mailjet', [
            'MAIL_FROM_ADDRESS' => 'no-es-email',
        ], authHeaders($user));

        $response->assertStatus(422);
    });

    it('gestor NO puede actualizar configuración', function () {
        $user = gestorUser();

        $response = $this->putJson('/api/admin/configuracion/mailjet', [
            'MAIL_FROM_NAME' => 'Hack',
        ], authHeaders($user));

        $response->assertStatus(403);
    });
});

describe('POST /api/admin/configuracion/mailjet/test', function () {

    it('rechaza test cuando Mailjet no está configurado', function () {
        $user = adminUser();

        // Limpiar configuración y forzar driver mailjet
        Configuracion::where('clave', 'MAILJET_API_KEY')->update(['valor' => '']);
        Configuracion::where('clave', 'MAILJET_SECRET_KEY')->update(['valor' => '']);
        Configuracion::where('clave', 'BREVO_API_KEY')->update(['valor' => '']);
        Configuracion::establecer('MAIL_MAILER', 'mailjet', $user->id);

        $response = $this->postJson('/api/admin/configuracion/mailjet/test', [
            'test_email' => 'test@ejemplo.com',
        ], authHeaders($user));

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);
    });

    it('gestor NO puede enviar email de prueba', function () {
        $user = gestorUser();

        $response = $this->postJson('/api/admin/configuracion/mailjet/test', [], authHeaders($user));

        $response->assertStatus(403);
    });

    it('sin autenticación retorna 401', function () {
        $response = $this->postJson('/api/admin/configuracion/mailjet/test');

        $response->assertStatus(401);
    });
});

describe('Modelo Configuracion', function () {

    it('obtener retorna valor por clave', function () {
        Configuracion::establecer('TEST_KEY', 'test_value', adminUser()->id);

        expect(Configuracion::obtener('TEST_KEY'))->toBe('test_value');
    });

    it('obtener retorna default si clave no existe', function () {
        expect(Configuracion::obtener('NO_EXISTE', 'default'))->toBe('default');
    });

    it('establecer crea o actualiza configuración', function () {
        Configuracion::establecer('NUEVA_CLAVE', 'valor1', adminUser()->id);
        expect(Configuracion::obtener('NUEVA_CLAVE'))->toBe('valor1');

        Configuracion::establecer('NUEVA_CLAVE', 'valor2', adminUser()->id);
        expect(Configuracion::obtener('NUEVA_CLAVE'))->toBe('valor2');
    });
});

describe('Toggle Registro de Centros', function () {

    it('admin puede ver la página de configuración de registro', function () {
        $user = adminUser();

        $response = $this->actingAs($user)
            ->get('/admin/configuracion/registro-centros');

        $response->assertStatus(200)
            ->assertSee('Habilitar registro de centro');
    });

    it('gestor NO puede ver la página de configuración de registro', function () {
        $user = gestorUser();

        $response = $this->actingAs($user)
            ->get('/admin/configuracion/registro-centros');

        $response->assertRedirect(route('home'))
            ->assertSessionHas('error');

        // Verificar que no puede ver el contenido protegido
        $response->assertDontSee('Habilitar registro de centro');
    });

    it('sin autenticación retorna redirect a login', function () {
        $response = $this->get('/admin/configuracion/registro-centros');

        $response->assertRedirect(route('login'));
    });

    it('admin puede habilitar el registro de centros', function () {
        $user = adminUser();

        // Primero deshabilitar
        Configuracion::establecer('HABILITAR_REGISTRO_CENTRO', '0', $user->id);

        $response = $this->actingAs($user)
            ->put('/admin/configuracion/registro-centros', [
                'habilitado' => '1',
            ]);

        $response->assertRedirect(route('admin.configuracion.registro-centros'))
            ->assertSessionHas('success', 'Registro de centros habilitado correctamente.');

        expect(Configuracion::obtener('HABILITAR_REGISTRO_CENTRO'))->toBe('1');
    });

    it('admin puede deshabilitar el registro de centros', function () {
        $user = adminUser();

        // Primero habilitar
        Configuracion::establecer('HABILITAR_REGISTRO_CENTRO', '1', $user->id);

        $response = $this->actingAs($user)
            ->put('/admin/configuracion/registro-centros', [
                'habilitado' => '0',
            ]);

        $response->assertRedirect(route('admin.configuracion.registro-centros'))
            ->assertSessionHas('success', 'Registro de centros deshabilitado correctamente.');

        expect(Configuracion::obtener('HABILITAR_REGISTRO_CENTRO'))->toBe('0');
    });

    it('rechaza valor no booleano para habilitado', function () {
        $user = adminUser();

        $response = $this->actingAs($user)
            ->put('/admin/configuracion/registro-centros', [
                'habilitado' => 'invalido',
            ]);

        $response->assertSessionHasErrors('habilitado');
    });

    it('gestor NO puede actualizar la configuración de registro', function () {
        $user = gestorUser();

        $response = $this->actingAs($user)
            ->put('/admin/configuracion/registro-centros', [
                'habilitado' => '0',
            ]);

        $response->assertRedirect(route('home'))
            ->assertSessionHas('error');

        // Verificar que no modificó la configuración
        expect(Configuracion::obtener('HABILITAR_REGISTRO_CENTRO', 'original'))->not->toBe('0');
    });
});

describe('Bloqueo de registro público', function () {

    it('muestra formulario de registro cuando está habilitado', function () {
        Configuracion::establecer('HABILITAR_REGISTRO_CENTRO', '1', adminUser()->id);

        $response = $this->get('/registro');

        $response->assertStatus(200)
            ->assertSee('Registrar centro')
            ->assertSee('nombre_centro');
    });

    it('muestra mensaje de bloqueo cuando registro está deshabilitado', function () {
        Configuracion::establecer('HABILITAR_REGISTRO_CENTRO', '0', adminUser()->id);

        $response = $this->get('/registro');

        $response->assertStatus(200)
            ->assertSee('Registro no disponible')
            ->assertSee('temporalmente deshabilitado')
            ->assertDontSee('nombre_centro');
    });

    it('bloquea el POST de registro cuando está deshabilitado', function () {
        Configuracion::establecer('HABILITAR_REGISTRO_CENTRO', '0', adminUser()->id);

        $foto = \Illuminate\Http\UploadedFile::fake()->image('identificacion.jpg', 400, 400);

        $response = $this->post('/registro', [
            'tipo_centro' => 'hospital',
            'nombre_centro' => 'Centro de Prueba',
            'nombre' => 'Test User',
            'email' => 'test@prueba.org',
            'cargo' => 'Director',
            'password' => 'password123',
            'foto_identificacion' => $foto,
        ]);

        $response->assertRedirect(route('login'))
            ->assertSessionHas('error');
    });

    it('permite el POST de registro cuando está habilitado', function () {
        Configuracion::establecer('HABILITAR_REGISTRO_CENTRO', '1', adminUser()->id);

        $response = $this->post('/registro', [
            'tipo_centro' => 'hospital',
            'nombre_centro' => 'Centro Test Valid',
            'nombre' => 'Valid User',
            'email' => 'valid@test.org',
            'cargo' => 'Coordinador',
            'password' => 'password123',
        ]);

        // Should not be redirected to login with error
        expect(session('error'))->toBeNull();
    });
});
