<?php

use App\Mail\ActivarCentroMail;
use App\Mail\NuevoCentroPendienteMail;
use App\Models\Centro;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

/*
|--------------------------------------------------------------------------
| WebAuthTest - Pruebas de autenticación web (Blade)
|--------------------------------------------------------------------------
*/

describe('GET /login', function () {

    it('muestra formulario de login', function () {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
    });

    it('redirige a dashboard si ya está autenticado (gestor)', function () {
        $user = gestorUser();

        $response = $this->actingAs($user)->get('/login');

        $response->assertStatus(302);
        $response->assertRedirect('/');
    });
});

describe('POST /login', function () {

    it('login exitoso de gestor redirige a dashboard', function () {
        $user = gestorUser();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'gestor123',
        ]);

        $response->assertRedirect(route('gestor.dashboard'));
    });

    it('login exitoso de admin redirige a admin dashboard', function () {
        $user = adminUser();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'admin123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
    });

    it('rechaza login con credenciales incorrectas', function () {
        $response = $this->post('/login', [
            'email' => 'admin@findchild.org',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('email');
    });

    it('rechaza login con usuario inactivo', function () {
        $user = gestorUser();
        $user->update(['activo' => false]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'gestor123',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('email');

        // Restaurar
        $user->update(['activo' => true]);
    });

    it('rechaza login sin campos', function () {
        $response = $this->post('/login', []);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['email', 'password']);
    });
});

describe('POST /logout', function () {

    it('logout exitoso redirige a home', function () {
        $user = gestorUser();

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect(route('home'));
    });

    it('rechaza logout sin autenticación', function () {
        $response = $this->post('/logout');

        $response->assertRedirect('/login');
    });
});

describe('GET /registro', function () {

    it('muestra formulario de registro', function () {
        $response = $this->get('/registro');

        $response->assertStatus(200);
        $response->assertViewIs('auth.register');
    });

    it('redirige a dashboard si ya está autenticado', function () {
        $user = gestorUser();

        $response = $this->actingAs($user)->get('/registro');

        $response->assertStatus(302);
        $response->assertRedirect('/');
    });
});

describe('POST /registro', function () {

    it('registro exitoso de centro y usuario', function () {
        Mail::fake();

        $foto = \Illuminate\Http\UploadedFile::fake()->image('identificacion.jpg', 400, 400);

        $response = $this->post('/registro', [
            'tipo_centro' => 'hospital',
            'nombre_centro' => 'Centro de Test Web',
            'nombre' => 'Usuario Test Web',
            'email' => 'testweb@nuevocentro.org',
            'cargo' => 'Director',
            'password' => 'password123',
            'telefono' => '+58-212-5559999',
            'direccion' => 'Av. Test #123',
            'foto_identificacion' => $foto,
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('centros', [
            'nombre' => 'Centro de Test Web',
            'activo' => false,
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'testweb@nuevocentro.org',
            'rol' => 'gestor',
            'activo' => false,
        ]);

        Mail::assertSent(ActivarCentroMail::class, function ($mail) {
            return $mail->hasTo('testweb@nuevocentro.org');
        });

        Mail::assertSent(NuevoCentroPendienteMail::class, function ($mail) {
            return $mail->hasTo(adminUser()->email);
        });
    });

    it('rechaza registro con email duplicado', function () {
        $response = $this->post('/registro', [
            'tipo_centro' => 'hospital',
            'nombre_centro' => 'Centro Duplicado',
            'nombre' => 'Test',
            'email' => 'admin@findchild.org', // ya existe
            'cargo' => 'Director',
            'password' => 'password123',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('email');
    });

    it('rechaza registro sin campos obligatorios', function () {
        $response = $this->post('/registro', []);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['tipo_centro', 'nombre_centro', 'nombre', 'email', 'cargo', 'password']);
    });
});

describe('GET /activar/{token}', function () {

    it('activa usuario con token válido', function () {
        $user = gestorUser();
        $user->update([
            'activo' => false,
            'token_activacion' => 'test-web-token-activar',
        ]);

        $response = $this->get('/activar/test-web-token-activar');

        $response->assertStatus(200);
        $response->assertViewIs('auth.activate');
        $response->assertViewHas('success', true);

        $user->refresh();
        expect($user->activo)->toBeTrue();
        expect($user->token_activacion)->toBeNull();
    });

    it('muestra error con token inválido', function () {
        $response = $this->get('/activar/token-no-existe-web');

        $response->assertStatus(200);
        $response->assertViewIs('auth.activate');
        $response->assertViewHas('success', false);
    });
});
