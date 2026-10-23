<?php

use App\Mail\CentroActivadoMail;
use App\Models\Centro;
use App\Models\Configuracion;
use Illuminate\Support\Facades\Mail;

/*
|--------------------------------------------------------------------------
| WebAdminTest - Pruebas de vistas de admin (Blade)
|--------------------------------------------------------------------------
*/

describe('GET /admin/dashboard', function () {

    it('admin autenticado ve su dashboard', function () {
        $user = adminUser();

        $response = $this->actingAs($user)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.dashboard.index');
        $response->assertViewHas('stats');
    });

    it('redirige a login sin autenticación', function () {
        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect('/login');
    });

    it('gestor no puede acceder a dashboard de admin', function () {
        $user = gestorUser();

        $response = $this->actingAs($user)->get(route('admin.dashboard'));

        $response->assertRedirect(route('home'));
    });

    it('dashboard muestra stats completas', function () {
        $user = adminUser();

        $response = $this->actingAs($user)->get(route('admin.dashboard'));

        $stats = $response->viewData('stats');
        expect($stats)->toHaveKey('total_centros');
        expect($stats)->toHaveKey('centros_activos');
        expect($stats)->toHaveKey('centros_pendientes');
        expect($stats)->toHaveKey('total_ninos');
        expect($stats)->toHaveKey('ninos_encontrados');
        expect($stats)->toHaveKey('ninos_recogidos');
        expect($stats)->toHaveKey('ninos_familia_localizada');
        expect($stats)->toHaveKey('total_recogidas');
        expect($stats)->toHaveKey('total_usuarios');
        expect($stats)->toHaveKey('usuarios_gestores');
    });
});

describe('GET /admin/centros', function () {

    it('admin ve listado de centros', function () {
        $user = adminUser();

        $response = $this->actingAs($user)->get(route('admin.centros.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.centros.index');
        $response->assertViewHas('centros');
        $response->assertViewHas('estado');
        $response->assertViewHas('totalPendientes');
        $response->assertViewHas('totalActivos');
    });

    it('soporta filtro por estado pendientes', function () {
        $user = adminUser();

        $response = $this->actingAs($user)->get(route('admin.centros.index', ['estado' => 'pendientes']));

        $response->assertStatus(200);
        $response->assertViewHas('estado', 'pendientes');
    });

    it('soporta filtro por estado activos', function () {
        $user = adminUser();

        $response = $this->actingAs($user)->get(route('admin.centros.index', ['estado' => 'activos']));

        $response->assertStatus(200);
        $response->assertViewHas('estado', 'activos');
    });

    it('redirige a login sin autenticación', function () {
        $response = $this->get(route('admin.centros.index'));

        $response->assertRedirect('/login');
    });

    it('gestor no puede acceder', function () {
        $user = gestorUser();

        $response = $this->actingAs($user)->get(route('admin.centros.index'));

        $response->assertRedirect(route('home'));
    });
});

describe('PUT /admin/centros/{centro}/validar', function () {

    it('admin puede validar centro pendiente', function () {
        $centro = Centro::create([
            'nombre' => 'Centro Test Validar',
            'tipo' => 'otro',
            'email' => 'test_validar_' . uniqid() . '@test.org',
            'activo' => false,
        ]);

        $user = adminUser();

        $response = $this->actingAs($user)->put(route('admin.centros.validar', $centro));

        $response->assertRedirect(route('admin.centros.index'));
        $centro->refresh();
        expect($centro->activo)->toBeTrue();
    });

    it('validar centro envía email de notificación al responsable', function () {
        Mail::fake();

        $centro = Centro::create([
            'nombre' => 'Centro Test Notificacion',
            'tipo' => 'otro',
            'email' => 'test_notif_' . uniqid() . '@test.org',
            'activo' => false,
        ]);

        $responsable = \App\Models\User::create([
            'centro_id' => $centro->id,
            'nombre' => 'Responsable Centro',
            'email' => 'responsable_' . uniqid() . '@test.org',
            'password' => bcrypt('password'),
            'rol' => 'gestor',
            'cargo' => 'Coordinador',
            'activo' => false,
            'token_activacion' => \Illuminate\Support\Str::random(64),
        ]);

        $admin = adminUser();
        $this->actingAs($admin)->put(route('admin.centros.validar', $centro));

        Mail::assertSent(CentroActivadoMail::class, function ($mail) use ($responsable) {
            return $mail->hasTo($responsable->email);
        });
    });

    it('validar centro activa usuarios del centro', function () {
        $centro = Centro::create([
            'nombre' => 'Centro Test Usuarios',
            'tipo' => 'casa_acogida',
            'email' => 'test_usuarios_' . uniqid() . '@test.org',
            'activo' => false,
        ]);

        // Crear usuarios asociados al centro (inactivos)
        $user1 = \App\Models\User::create([
            'centro_id' => $centro->id,
            'nombre' => 'Gestor Test 1',
            'email' => 'gestor1_' . uniqid() . '@test.org',
            'password' => bcrypt('password'),
            'rol' => 'gestor',
            'cargo' => 'Coordinador',
            'activo' => false,
            'token_activacion' => \Illuminate\Support\Str::random(64),
        ]);

        $user2 = \App\Models\User::create([
            'centro_id' => $centro->id,
            'nombre' => 'Gestor Test 2',
            'email' => 'gestor2_' . uniqid() . '@test.org',
            'password' => bcrypt('password'),
            'rol' => 'gestor',
            'cargo' => 'Auxiliar',
            'activo' => false,
            'token_activacion' => \Illuminate\Support\Str::random(64),
        ]);

        $admin = adminUser();
        $this->actingAs($admin)->put(route('admin.centros.validar', $centro));

        // Verificar que el centro se activó
        $centro->refresh();
        expect($centro->activo)->toBeTrue();

        // Verificar que los usuarios se activaron
        $user1->refresh();
        expect($user1->activo)->toBeTrue();
        expect($user1->email_verified_at)->not->toBeNull();
        expect($user1->token_activacion)->toBeNull();

        $user2->refresh();
        expect($user2->activo)->toBeTrue();
        expect($user2->email_verified_at)->not->toBeNull();
        expect($user2->token_activacion)->toBeNull();
    });

    it('rechaza validar centro ya activo', function () {
        $centro = Centro::create([
            'nombre' => 'Centro Ya Activo',
            'tipo' => 'otro',
            'email' => 'test_activo_' . uniqid() . '@test.org',
            'activo' => true,
        ]);

        $user = adminUser();

        $response = $this->actingAs($user)->put(route('admin.centros.validar', $centro));

        $response->assertRedirect();
        $response->assertSessionHas('error');
    });

    it('gestor no puede validar centros', function () {
        $centro = Centro::create([
            'nombre' => 'Centro Test Gestor',
            'tipo' => 'otro',
            'email' => 'test_gestor_' . uniqid() . '@test.org',
            'activo' => false,
        ]);

        $user = gestorUser();

        $response = $this->actingAs($user)->put(route('admin.centros.validar', $centro));

        $response->assertRedirect(route('home'));
    });

    it('redirige a login sin autenticación', function () {
        $centro = Centro::create([
            'nombre' => 'Centro Test Auth',
            'tipo' => 'otro',
            'email' => 'test_auth_' . uniqid() . '@test.org',
            'activo' => false,
        ]);

        $response = $this->put(route('admin.centros.validar', $centro));

        $response->assertRedirect('/login');
    });
});

describe('GET /admin/configuracion/mailjet', function () {

    it('admin ve configuración actual', function () {
        $user = adminUser();

        $response = $this->actingAs($user)->get(route('admin.configuracion.mailjet'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.configuracion.mailjet');
        $response->assertViewHas('config');
        $response->assertViewHas('configurado');
    });

    it('redirige a login sin autenticación', function () {
        $response = $this->get(route('admin.configuracion.mailjet'));

        $response->assertRedirect('/login');
    });

    it('gestor no puede acceder', function () {
        $user = gestorUser();

        $response = $this->actingAs($user)->get(route('admin.configuracion.mailjet'));

        $response->assertRedirect(route('home'));
    });
});

describe('PUT /admin/configuracion/mailjet', function () {

    it('admin puede actualizar configuración', function () {
        $user = adminUser();

        $response = $this->actingAs($user)->put(route('admin.configuracion.mailjet.update'), [
            'MAIL_MAILER' => 'smtp',
            'MAILJET_API_KEY' => 'test_key_123',
            'MAILJET_SECRET_KEY' => 'test_secret_456',
            'MAIL_FROM_ADDRESS' => 'test@findchild.org',
            'MAIL_FROM_NAME' => 'Reencuentro Test',
        ]);

        $response->assertRedirect(route('admin.configuracion.mailjet'));
        expect(Configuracion::obtener('MAILJET_API_KEY'))->toBe('test_key_123');
    });

    it('admin puede actualizar solo el driver', function () {
        $user = adminUser();

        $response = $this->actingAs($user)->put(route('admin.configuracion.mailjet.update'), [
            'MAIL_MAILER' => 'log',
        ]);

        $response->assertRedirect(route('admin.configuracion.mailjet'));
        expect(Configuracion::obtener('MAIL_MAILER'))->toBe('log');
    });

    it('rechaza driver inválido', function () {
        $user = adminUser();

        $response = $this->actingAs($user)->put(route('admin.configuracion.mailjet.update'), [
            'MAIL_MAILER' => 'sendgrid',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('MAIL_MAILER');
    });

    it('rechaza email remitente inválido', function () {
        $user = adminUser();

        $response = $this->actingAs($user)->put(route('admin.configuracion.mailjet.update'), [
            'MAIL_FROM_ADDRESS' => 'no-es-email',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('MAIL_FROM_ADDRESS');
    });

    it('rechaza puerto fuera de rango', function () {
        $user = adminUser();

        $response = $this->actingAs($user)->put(route('admin.configuracion.mailjet.update'), [
            'MAIL_PORT' => 99999,
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('MAIL_PORT');
    });

    it('gestor no puede actualizar', function () {
        $user = gestorUser();

        $response = $this->actingAs($user)->put(route('admin.configuracion.mailjet.update'), [
            'MAIL_MAILER' => 'smtp',
        ]);

        $response->assertRedirect(route('home'));
    });

    it('redirige a login sin autenticación', function () {
        $response = $this->put(route('admin.configuracion.mailjet.update'), [
            'MAIL_MAILER' => 'smtp',
        ]);

        $response->assertRedirect('/login');
    });
});

describe('POST /admin/configuracion/mailjet/test', function () {

    it('rechaza test si Mailjet no está configurado', function () {
        // Asegurar que no hay credenciales y forzar driver mailjet
        Configuracion::where('clave', 'MAILJET_API_KEY')->delete();
        Configuracion::where('clave', 'MAILJET_SECRET_KEY')->delete();
        Configuracion::where('clave', 'BREVO_API_KEY')->delete();
        Configuracion::establecer('MAIL_MAILER', 'mailjet', adminUser()->id);

        $user = adminUser();

        $response = $this->actingAs($user)->post(route('admin.configuracion.mailjet.test'), [
            'test_email' => 'test@ejemplo.com',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    });

    it('gestor no puede enviar prueba', function () {
        $user = gestorUser();

        $response = $this->actingAs($user)->post(route('admin.configuracion.mailjet.test'));

        $response->assertRedirect(route('home'));
    });

    it('redirige a login sin autenticación', function () {
        $response = $this->post(route('admin.configuracion.mailjet.test'));

        $response->assertRedirect('/login');
    });
});

describe('GET /admin/ninos', function () {

    it('admin ve listado de todos los niños', function () {
        $user = adminUser();

        $response = $this->actingAs($user)->get(route('admin.ninos.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.ninos.index');
        $response->assertViewHas('ninos');
    });

    it('admin puede filtrar niños por estado encontrado', function () {
        $user = adminUser();

        $response = $this->actingAs($user)->get(route('admin.ninos.index', ['estado' => 'encontrado']));

        $response->assertStatus(200);
        $response->assertViewHas('estado', 'encontrado');
        $response->assertViewHas('ninos');
    });

    it('admin puede filtrar niños por estado recogido', function () {
        $user = adminUser();

        $response = $this->actingAs($user)->get(route('admin.ninos.index', ['estado' => 'recogido']));

        $response->assertStatus(200);
        $response->assertViewHas('estado', 'recogido');
    });

    it('admin puede filtrar niños por estado familia_localizada', function () {
        $user = adminUser();

        $response = $this->actingAs($user)->get(route('admin.ninos.index', ['estado' => 'familia_localizada']));

        $response->assertStatus(200);
        $response->assertViewHas('estado', 'familia_localizada');
    });

    it('admin ve detalle de un niño', function () {
        $user = adminUser();

        $response = $this->actingAs($user)->get(route('admin.ninos.show', ['nino' => 1]));

        $response->assertStatus(200);
        $response->assertViewIs('admin.ninos.show');
        $response->assertViewHas('nino');
    });

    it('redirige a login sin autenticación', function () {
        $response = $this->get(route('admin.ninos.index'));

        $response->assertRedirect('/login');
    });

    it('gestor no puede acceder', function () {
        $user = gestorUser();

        $response = $this->actingAs($user)->get(route('admin.ninos.index'));

        $response->assertRedirect(route('home'));
    });
});

describe('GET /admin/recogidas', function () {

    it('admin ve listado de recogidas', function () {
        $user = adminUser();

        $response = $this->actingAs($user)->get(route('admin.recogidas.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.recogidas.index');
        $response->assertViewHas('recogidas');
    });

    it('redirige a login sin autenticación', function () {
        $response = $this->get(route('admin.recogidas.index'));

        $response->assertRedirect('/login');
    });

    it('gestor no puede acceder', function () {
        $user = gestorUser();

        $response = $this->actingAs($user)->get(route('admin.recogidas.index'));

        $response->assertRedirect(route('home'));
    });
});

describe('GET /admin/usuarios', function () {

    it('admin ve listado de gestores', function () {
        $user = adminUser();

        $response = $this->actingAs($user)->get(route('admin.usuarios.index', ['rol' => 'gestor']));

        $response->assertStatus(200);
        $response->assertViewIs('admin.usuarios.index');
        $response->assertViewHas('usuarios');
        $response->assertViewHas('rol', 'gestor');
    });

    it('admin puede ver administradores', function () {
        $user = adminUser();

        $response = $this->actingAs($user)->get(route('admin.usuarios.index', ['rol' => 'admin']));

        $response->assertStatus(200);
        $response->assertViewHas('rol', 'admin');
    });

    it('redirige a login sin autenticación', function () {
        $response = $this->get(route('admin.usuarios.index'));

        $response->assertRedirect('/login');
    });

    it('gestor no puede acceder', function () {
        $user = gestorUser();

        $response = $this->actingAs($user)->get(route('admin.usuarios.index'));

        $response->assertRedirect(route('home'));
    });
});

describe('Dashboard stat-cards con enlaces', function () {

    it('dashboard incluye enlace a admin.ninos.index', function () {
        $user = adminUser();

        $response = $this->actingAs($user)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee(route('admin.ninos.index'));
    });

    it('dashboard incluye enlace a admin.recogidas.index', function () {
        $user = adminUser();

        $response = $this->actingAs($user)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee(route('admin.recogidas.index'));
    });

    it('dashboard incluye enlace a admin.usuarios.index', function () {
        $user = adminUser();

        $response = $this->actingAs($user)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee(route('admin.usuarios.index'));
    });

    it('dashboard incluye enlace a niños filtrados por estado', function () {
        $user = adminUser();

        $response = $this->actingAs($user)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee(route('admin.ninos.index', ['estado' => 'encontrado']));
        $response->assertSee(route('admin.ninos.index', ['estado' => 'recogido']));
        $response->assertSee(route('admin.ninos.index', ['estado' => 'familia_localizada']));
    });
});

describe('GET /admin/usuarios/crear', function () {

    it('admin ve formulario de creación de administrador', function () {
        $user = adminUser();

        $response = $this->actingAs($user)->get(route('admin.usuarios.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.usuarios.create');
    });

    it('redirige a login sin autenticación', function () {
        $response = $this->get(route('admin.usuarios.create'));

        $response->assertRedirect('/login');
    });

    it('gestor no puede acceder', function () {
        $user = gestorUser();

        $response = $this->actingAs($user)->get(route('admin.usuarios.create'));

        $response->assertRedirect(route('home'));
    });
});

describe('GET /admin/usuarios/{usuario}/editar', function () {

    it('admin ve formulario de edición con datos del usuario', function () {
        $admin = adminUser();
        $gestor = gestorUser();

        $response = $this->actingAs($admin)->get(route('admin.usuarios.edit', $gestor));

        $response->assertStatus(200);
        $response->assertViewIs('admin.usuarios.edit');
        $response->assertViewHas('usuario', function ($usuario) use ($gestor) {
            return $usuario->id === $gestor->id;
        });
    });

    it('redirige a login sin autenticación', function () {
        $gestor = gestorUser();

        $response = $this->get(route('admin.usuarios.edit', $gestor));

        $response->assertRedirect('/login');
    });

    it('gestor no puede acceder', function () {
        $user = gestorUser();

        $response = $this->actingAs($user)->get(route('admin.usuarios.edit', $user));

        $response->assertRedirect(route('home'));
    });
});

describe('POST /admin/usuarios', function () {
    it('admin puede crear nuevo administrador', function () {
        $user = adminUser();
        $response = $this->actingAs($user)->post(route('admin.usuarios.store'), [
            'nombre' => 'Test Admin',
            'email' => 'test@admin.org',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        $response->assertRedirect(route('admin.usuarios.index'));
        $this->assertDatabaseHas('users', ['email' => 'test@admin.org', 'rol' => 'admin']);
    });

    it('rechaza email duplicado', function () {
        $user = adminUser();
        $response = $this->actingAs($user)->post(route('admin.usuarios.store'), [
            'nombre' => 'Test Admin',
            'email' => $user->email,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        $response->assertStatus(302);
        $response->assertSessionHasErrors('email');
    });

    it('rechaza contraseña menor a 8 caracteres', function () {
        $user = adminUser();
        $response = $this->actingAs($user)->post(route('admin.usuarios.store'), [
            'nombre' => 'Test Admin',
            'email' => 'corto@admin.org',
            'password' => '123',
            'password_confirmation' => '123',
        ]);
        $response->assertStatus(302);
        $response->assertSessionHasErrors('password');
    });

    it('gestor no puede crear administrador', function () {
        $user = gestorUser();
        $response = $this->actingAs($user)->post(route('admin.usuarios.store'), [
            'nombre' => 'Test Admin',
            'email' => 'hack@admin.org',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        $response->assertRedirect(route('home'));
    });
});

describe('PUT /admin/usuarios/{usuario}', function () {
    it('admin puede actualizar un usuario', function () {
        $admin = adminUser();
        $gestor = gestorUser();

        $response = $this->actingAs($admin)->put(route('admin.usuarios.update', $gestor), [
            'nombre' => 'Nombre Actualizado',
            'email' => $gestor->email,
            'cargo' => 'Coordinador',
            'activo' => '1',
        ]);

        $response->assertRedirect(route('admin.usuarios.index'));
        $gestor->refresh();
        expect($gestor->nombre)->toBe('Nombre Actualizado');
        expect($gestor->cargo)->toBe('Coordinador');
    });

    it('rechaza email duplicado de otro usuario', function () {
        $admin = adminUser();
        $gestor = gestorUser();

        $response = $this->actingAs($admin)->put(route('admin.usuarios.update', $gestor), [
            'nombre' => 'Test',
            'email' => $admin->email,
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('email');
    });
});

describe('PUT /admin/usuarios/{usuario}/password', function () {

    it('admin puede cambiar la contraseña de un usuario', function () {
        $admin = adminUser();
        $gestor = gestorUser();

        $response = $this->actingAs($admin)->put(route('admin.usuarios.updatePassword', $gestor), [
            'nueva_password' => 'nuevaPassword123',
            'nueva_password_confirmation' => 'nuevaPassword123',
        ]);

        $response->assertRedirect(route('admin.usuarios.index'));
        $response->assertSessionHas('success');

        expect(\Illuminate\Support\Facades\Hash::check('nuevaPassword123', $gestor->refresh()->password))->toBeTrue();
    });

    it('rechaza contraseña menor a 8 caracteres', function () {
        $admin = adminUser();
        $gestor = gestorUser();

        $response = $this->actingAs($admin)->put(route('admin.usuarios.updatePassword', $gestor), [
            'nueva_password' => '123',
            'nueva_password_confirmation' => '123',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('nueva_password');
    });

    it('rechaza si la confirmación no coincide', function () {
        $admin = adminUser();
        $gestor = gestorUser();

        $response = $this->actingAs($admin)->put(route('admin.usuarios.updatePassword', $gestor), [
            'nueva_password' => 'nuevaPassword123',
            'nueva_password_confirmation' => 'otraDiferente123',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('nueva_password');
    });

    it('gestor no puede cambiar contraseñas', function () {
        $gestor = gestorUser();
        $otro = gestorHospitalUser();

        $response = $this->actingAs($gestor)->put(route('admin.usuarios.updatePassword', $otro), [
            'nueva_password' => 'hackPassword123',
            'nueva_password_confirmation' => 'hackPassword123',
        ]);

        $response->assertRedirect(route('home'));
    });

    it('redirige a login sin autenticación', function () {
        $gestor = gestorUser();

        $response = $this->put(route('admin.usuarios.updatePassword', $gestor), [
            'nueva_password' => 'nuevaPassword123',
            'nueva_password_confirmation' => 'nuevaPassword123',
        ]);

        $response->assertRedirect('/login');
    });
});

describe('DELETE /admin/usuarios/{usuario}', function () {
    it('no puede eliminar el único admin', function () {
        $user = adminUser();
        $response = $this->actingAs($user)->delete(route('admin.usuarios.destroy', $user));
        $response->assertRedirect();
        $response->assertSessionHas('error');
    });

    it('no puede eliminar su propia cuenta', function () {
        $user = adminUser();
        $otherAdmin = \App\Models\User::create([
            'nombre' => 'Otro Admin',
            'email' => 'otro_' . uniqid() . '@admin.org',
            'password' => \Illuminate\Support\Facades\Hash::make('password123'),
            'rol' => 'admin',
            'activo' => true,
        ]);

        $response = $this->actingAs($otherAdmin)->delete(route('admin.usuarios.destroy', $otherAdmin));
        $response->assertRedirect();
        $response->assertSessionHas('error');
    });

    it('admin puede eliminar un gestor', function () {
        $admin = adminUser();
        $gestor = \App\Models\User::create([
            'nombre' => 'Gestor Para Eliminar',
            'email' => 'eliminar_' . uniqid() . '@test.org',
            'password' => \Illuminate\Support\Facades\Hash::make('password123'),
            'rol' => 'gestor',
            'activo' => true,
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.usuarios.destroy', $gestor));
        $response->assertRedirect(route('admin.usuarios.index'));
        $this->assertDatabaseMissing('users', ['email' => $gestor->email]);
    });
});
