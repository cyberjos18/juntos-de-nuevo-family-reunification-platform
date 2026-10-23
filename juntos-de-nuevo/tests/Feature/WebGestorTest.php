<?php

use App\Models\Nino;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| WebGestorTest - Pruebas de vistas de gestor (Blade)
|--------------------------------------------------------------------------
*/

describe('GET /gestor/dashboard', function () {

    it('gestor autenticado ve su dashboard', function () {
        $user = gestorUser();

        $response = $this->actingAs($user)->get(route('gestor.dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('gestor.dashboard.index');
        $response->assertViewHas('stats');
        $response->assertViewHas('centro');
        $response->assertViewHas('ultimosNinos');
    });

    it('redirige a login sin autenticación', function () {
        $response = $this->get(route('gestor.dashboard'));

        $response->assertRedirect('/login');
    });

    it('admin no puede acceder a dashboard de gestor', function () {
        $user = adminUser();

        $response = $this->actingAs($user)->get(route('gestor.dashboard'));

        // CheckRole middleware redirige a home cuando el rol no coincide
        $response->assertRedirect(route('home'));
    });

    it('dashboard muestra stats del centro', function () {
        $user = gestorUser();

        $response = $this->actingAs($user)->get(route('gestor.dashboard'));

        $response->assertStatus(200);
        $stats = $response->viewData('stats');
        expect($stats)->toHaveKey('activos');
        expect($stats)->toHaveKey('recogidos');
        expect($stats)->toHaveKey('familia_localizada');
        expect($stats)->toHaveKey('total');
    });
});

describe('GET /gestor/ninos', function () {

    it('gestor ve listado de niños de su centro', function () {
        $user = gestorUser();

        $response = $this->actingAs($user)->get(route('gestor.ninos.index'));

        $response->assertStatus(200);
        $response->assertViewIs('gestor.ninos.index');
        $response->assertViewHas('ninos');
        $response->assertViewHas('estado');
    });

    it('soporta filtro por estado', function () {
        $user = gestorUser();

        $response = $this->actingAs($user)->get(route('gestor.ninos.index', ['estado' => 'encontrado']));

        $response->assertStatus(200);
        $response->assertViewIs('gestor.ninos.index');
        $response->assertViewHas('estado', 'encontrado');
    });

    it('redirige a login sin autenticación', function () {
        $response = $this->get(route('gestor.ninos.index'));

        $response->assertRedirect('/login');
    });
});

describe('GET /gestor/ninos/crear', function () {

    it('gestor ve formulario de registro de niño', function () {
        $user = gestorUser();

        $response = $this->actingAs($user)->get(route('gestor.ninos.create'));

        $response->assertStatus(200);
        $response->assertViewIs('gestor.ninos.create');
    });

    it('redirige a login sin autenticación', function () {
        $response = $this->get(route('gestor.ninos.create'));

        $response->assertRedirect('/login');
    });
});

describe('POST /gestor/ninos', function () {

    it('gestor puede registrar niño con foto', function () {
        Storage::fake('public');
        $user = gestorUser();

        $response = $this->actingAs($user)->post(route('gestor.ninos.store'), [
            'foto' => UploadedFile::fake()->image('nino.jpg'),
            'nombre' => 'TestWeb',
            'apellido' => 'NiñoWeb',
            'edad' => 5,
        ]);

        $response->assertRedirect(route('gestor.ninos.index'));
        $this->assertDatabaseHas('ninos', ['nombre' => 'TestWeb', 'apellido' => 'NiñoWeb']);
    });

    it('gestor puede registrar niño con familiares', function () {
        Storage::fake('public');
        $user = gestorUser();

        $response = $this->actingAs($user)->post(route('gestor.ninos.store'), [
            'foto' => UploadedFile::fake()->image('nino.jpg'),
            'nombre' => 'TestWebFam',
            'apellido' => 'ConFamiliar',
            'edad' => 3,
            'familiares' => [
                ['parentesco' => 'Tía', 'nombre' => 'Rosa', 'telefono' => '+58-414-5551234'],
            ],
        ]);

        $response->assertRedirect(route('gestor.ninos.index'));
        $this->assertDatabaseHas('ninos', ['nombre' => 'TestWebFam', 'apellido' => 'ConFamiliar']);
    });

    it('rechaza sin foto', function () {
        $user = gestorUser();

        $response = $this->actingAs($user)->post(route('gestor.ninos.store'), [
            'nombre' => 'TestWeb',
            'apellido' => 'SinFoto',
            'edad' => 5,
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('foto');
    });

    it('rechaza edad mayor a 17', function () {
        Storage::fake('public');
        $user = gestorUser();

        $response = $this->actingAs($user)->post(route('gestor.ninos.store'), [
            'foto' => UploadedFile::fake()->image('nino.jpg'),
            'nombre' => 'TestWeb',
            'apellido' => 'Mayor',
            'edad' => 25,
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('edad');
    });

    it('redirige a login sin autenticación', function () {
        $response = $this->post(route('gestor.ninos.store'), [
            'nombre' => 'Test',
            'apellido' => 'Test',
        ]);

        $response->assertRedirect('/login');
    });
});

describe('GET /gestor/ninos/{nino}', function () {

    it('gestor ve ficha de niño de su centro', function () {
        $user = gestorUser();

        // Niño #1 pertenece al centro 1 (Protección Civil)
        $response = $this->actingAs($user)->get(route('gestor.ninos.show', 1));

        $response->assertStatus(200);
        $response->assertViewIs('gestor.ninos.show');
        $response->assertViewHas('nino');
    });

    it('gestor NO puede ver niño de otro centro (403)', function () {
        $user = gestorHospitalUser();

        // Niño #1 pertenece al centro 1, no al hospital
        $response = $this->actingAs($user)->get(route('gestor.ninos.show', 1));

        $response->assertStatus(403);
    });

    it('redirige a login sin autenticación', function () {
        $response = $this->get(route('gestor.ninos.show', 1));

        $response->assertRedirect('/login');
    });
});

describe('GET /gestor/ninos/{nino}/recogida', function () {

    it('gestor ve formulario de recogida', function () {
        $user = gestorUser();

        // Niño #3 está en centro 1 y NO está recogido
        $response = $this->actingAs($user)->get(route('gestor.recogidas.create', 3));

        $response->assertStatus(200);
        $response->assertViewIs('gestor.recogidas.create');
        $response->assertViewHas('nino');
    });

    it('redirige si el niño ya fue recogido', function () {
        $user = gestorUser();

        // Poner niño #3 como recogido temporalmente
        $nino = Nino::find(3);
        $estadoOriginal = $nino->estado;
        $nino->update(['estado' => 'recogido']);

        $response = $this->actingAs($user)->get(route('gestor.recogidas.create', 3));

        $response->assertRedirect(route('gestor.ninos.show', 3));
        $response->assertSessionHas('error');

        // Restaurar
        $nino->update(['estado' => $estadoOriginal]);
    });

    it('gestor NO puede recoger niño de otro centro', function () {
        $user = gestorHospitalUser();

        // Niño #1 pertenece al centro 1, no al hospital
        $response = $this->actingAs($user)->get(route('gestor.recogidas.create', 1));

        $response->assertStatus(403);
    });

    it('redirige a login sin autenticación', function () {
        $response = $this->get(route('gestor.recogidas.create', 3));

        $response->assertRedirect('/login');
    });
});

describe('POST /gestor/ninos/{nino}/recogida', function () {

    it('gestor puede registrar recogida correctamente', function () {
        Storage::fake('public');
        $user = gestorUser();

        $response = $this->actingAs($user)->post(route('gestor.recogidas.store', 3), [
            'familiar_nombre' => 'Carmen',
            'familiar_apellido' => 'López',
            'familiar_parentesco' => 'Madre',
            'familiar_telefono' => '+58-412-5553344',
            'foto_recogida' => UploadedFile::fake()->image('recogida.jpg'),
        ]);

        $response->assertRedirect(route('gestor.ninos.index'));
        $response->assertSessionHas('success');
    });

    it('recogida cambia estado del niño a recogido', function () {
        Storage::fake('public');
        $user = gestorUser();

        $this->actingAs($user)->post(route('gestor.recogidas.store', 3), [
            'familiar_nombre' => 'Carmen',
            'familiar_apellido' => 'López',
            'familiar_parentesco' => 'Madre',
            'familiar_telefono' => '+58-412-5553344',
            'foto_recogida' => UploadedFile::fake()->image('recogida.jpg'),
        ]);

        expect(Nino::find(3)->estado)->toBe('recogido');
    });

    it('rechaza recogida de niño ya recogido', function () {
        Storage::fake('public');
        $user = gestorUser();

        // Poner niño #3 como recogido
        $nino = Nino::find(3);
        $estadoOriginal = $nino->estado;
        $nino->update(['estado' => 'recogido']);

        $response = $this->actingAs($user)->post(route('gestor.recogidas.store', 3), [
            'familiar_nombre' => 'Test',
            'familiar_apellido' => 'Test',
            'familiar_parentesco' => 'Padre',
            'familiar_telefono' => '+58-414-5551234',
            'foto_recogida' => UploadedFile::fake()->image('recogida.jpg'),
        ]);

        $response->assertRedirect(route('gestor.ninos.show', 3));
        $response->assertSessionHas('error');

        // Restaurar
        $nino->update(['estado' => $estadoOriginal]);
    });

    it('rechaza sin foto de recogida', function () {
        $user = gestorUser();

        $response = $this->actingAs($user)->post(route('gestor.recogidas.store', 3), [
            'familiar_nombre' => 'Carmen',
            'familiar_apellido' => 'López',
            'familiar_parentesco' => 'Madre',
            'familiar_telefono' => '+58-412-5553344',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('foto_recogida');
    });

    it('rechaza sin teléfono del familiar', function () {
        Storage::fake('public');
        $user = gestorUser();

        $response = $this->actingAs($user)->post(route('gestor.recogidas.store', 3), [
            'familiar_nombre' => 'Carmen',
            'familiar_apellido' => 'López',
            'familiar_parentesco' => 'Madre',
            'foto_recogida' => UploadedFile::fake()->image('recogida.jpg'),
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('familiar_telefono');
    });

    it('gestor NO puede recoger niño de otro centro', function () {
        Storage::fake('public');
        $user = gestorHospitalUser();

        // Niño #1 pertenece al centro 1, no al hospital
        $response = $this->actingAs($user)->post(route('gestor.recogidas.store', 1), [
            'familiar_nombre' => 'Hack',
            'familiar_apellido' => 'Intento',
            'familiar_parentesco' => 'Tío',
            'familiar_telefono' => '+58-414-5551234',
            'foto_recogida' => UploadedFile::fake()->image('recogida.jpg'),
        ]);

        $response->assertStatus(403);
    });

    it('redirige a login sin autenticación', function () {
        $response = $this->post(route('gestor.recogidas.store', 3), [
            'familiar_nombre' => 'Test',
            'familiar_apellido' => 'Test',
            'familiar_parentesco' => 'Test',
            'familiar_telefono' => '123',
        ]);

        $response->assertRedirect('/login');
    });
});
