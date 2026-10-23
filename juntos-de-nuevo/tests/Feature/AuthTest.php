<?php

use App\Mail\ActivarCentroMail;
use App\Models\User;
use App\Models\Centro;
use Illuminate\Support\Facades\Mail;

/*
|--------------------------------------------------------------------------
| AuthTest - Pruebas de autenticación
|--------------------------------------------------------------------------
*/

describe('POST /api/auth/register', function () {

    it('registra centro y usuario gestor correctamente', function () {
        Mail::fake();

        $foto = \Illuminate\Http\UploadedFile::fake()->image('identificacion.jpg', 400, 400);

        $response = $this->postJson('/api/auth/register', [
            'tipo_centro' => 'hospital',
            'nombre_centro' => 'Hospital de Prueba',
            'nombre' => 'Juan Pérez',
            'email' => 'juan@hospitalprueba.org',
            'cargo' => 'Director',
            'password' => 'password123',
            'telefono' => '+58-212-5551234',
            'foto_identificacion' => $foto,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['centro_id', 'user_id', 'token_activacion'],
            ]);

        $this->assertDatabaseHas('centros', [
            'nombre' => 'Hospital de Prueba',
            'activo' => false,
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'juan@hospitalprueba.org',
            'rol' => 'gestor',
            'activo' => false,
        ]);

        Mail::assertSent(ActivarCentroMail::class, function ($mail) {
            return $mail->hasTo('juan@hospitalprueba.org');
        });
    });

    it('rechaza registro con email duplicado', function () {
        $response = $this->postJson('/api/auth/register', [
            'tipo_centro' => 'hospital',
            'nombre_centro' => 'Hospital Duplicado',
            'nombre' => 'Test',
            'email' => 'admin@findchild.org', // ya existe
            'cargo' => 'Director',
            'password' => 'password123',
        ]);

        $response->assertStatus(422);
    });

    it('rechaza registro sin campos obligatorios', function () {
        $response = $this->postJson('/api/auth/register', []);

        $response->assertStatus(422);
    });

    it('rechaza tipo de centro inválido', function () {
        $response = $this->postJson('/api/auth/register', [
            'tipo_centro' => 'invalido',
            'nombre_centro' => 'Test',
            'nombre' => 'Test',
            'email' => 'test@test.com',
            'cargo' => 'Test',
            'password' => 'password123',
        ]);

        $response->assertStatus(422);
    });

    it('rechaza contraseña menor a 8 caracteres', function () {
        $response = $this->postJson('/api/auth/register', [
            'tipo_centro' => 'hospital',
            'nombre_centro' => 'Test',
            'nombre' => 'Test',
            'email' => 'test@test.com',
            'cargo' => 'Test',
            'password' => '123',
        ]);

        $response->assertStatus(422);
    });
});

describe('POST /api/auth/activate/{token}', function () {

    it('activa usuario con token válido', function () {
        $user = User::where('email', 'maria.gonzalez@proteccion.org')->first();
        $user->update([
            'activo' => false,
            'token_activacion' => 'test-token-123',
        ]);

        $response = $this->postJson('/api/auth/activate/test-token-123');

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $user->refresh();
        expect($user->activo)->toBeTrue();
        expect($user->token_activacion)->toBeNull();
        expect($user->email_verified_at)->not->toBeNull();
    });

    it('rechaza token inválido', function () {
        $response = $this->postJson('/api/auth/activate/token-no-existe');

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'error' => ['code' => 'INVALID_TOKEN'],
            ]);
    });
});

describe('POST /api/auth/login', function () {

    it('login exitoso con credenciales correctas', function () {
        // Asegurar que el usuario está activo
        $user = User::where('email', 'admin@findchild.org')->first();
        $user->update(['activo' => true]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'admin@findchild.org',
            'password' => 'admin123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'token',
                    'user' => ['id', 'nombre', 'email', 'rol'],
                ],
            ])
            ->assertJson(['data' => ['user' => ['rol' => 'admin']]]);
    });

    it('rechaza login con credenciales incorrectas', function () {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'admin@findchild.org',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'error' => ['code' => 'INVALID_CREDENTIALS'],
            ]);
    });

    it('rechaza login con usuario inactivo', function () {
        $user = User::where('email', 'admin@findchild.org')->first();
        $user->update(['activo' => false]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'admin@findchild.org',
            'password' => 'admin123',
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'error' => ['code' => 'ACCOUNT_INACTIVE'],
            ]);

        // Restaurar
        $user->update(['activo' => true]);
    });

    it('rechaza login sin campos', function () {
        $response = $this->postJson('/api/auth/login', []);

        $response->assertStatus(422);
    });
});

describe('POST /api/auth/logout', function () {

    it('logout exitoso revoca token', function () {
        $user = adminUser();

        $response = $this->postJson('/api/auth/logout', [], authHeaders($user));

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    });

    it('rechaza logout sin autenticación', function () {
        $response = $this->postJson('/api/auth/logout');

        $response->assertStatus(401);
    });
});

describe('GET /api/auth/me', function () {

    it('retorna datos del usuario autenticado', function () {
        $user = adminUser();

        $response = $this->getJson('/api/auth/me', authHeaders($user));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $user->id,
                    'nombre' => $user->nombre,
                    'email' => $user->email,
                    'rol' => 'admin',
                ],
            ]);
    });

    it('gestor autenticado ve datos de su centro', function () {
        $user = gestorUser();

        $response = $this->getJson('/api/auth/me', authHeaders($user));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => ['id', 'nombre', 'email', 'rol', 'centro'],
            ])
            ->assertJson(['data' => ['rol' => 'gestor']]);

        expect($response->json('data.centro'))->not->toBeNull();
    });

    it('rechaza /me sin autenticación', function () {
        $response = $this->getJson('/api/auth/me');

        $response->assertStatus(401);
    });
});
