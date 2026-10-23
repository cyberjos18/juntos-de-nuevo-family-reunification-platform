<?php

use App\Models\Nino;

/*
|--------------------------------------------------------------------------
| WebPublicTest - Pruebas de vistas públicas (Blade)
|--------------------------------------------------------------------------
*/

describe('GET / (Home)', function () {

    it('muestra listado público de niños', function () {
        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertViewIs('home.index');
        $response->assertViewHas('ninos');
    });

    it('no incluye niños recogidos', function () {
        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $ninos = $response->viewData('ninos');

        foreach ($ninos as $nino) {
            expect($nino->estado)->not->toBe('recogido');
        }
    });

    it('soporta filtro por estado', function () {
        $response = $this->get(route('home', ['estado' => 'encontrado']));

        $response->assertStatus(200);
        $response->assertViewIs('home.index');
        $response->assertViewHas('ninos');
    });
});

describe('GET /ninos/{nino}', function () {

    it('muestra ficha pública de niño', function () {
        $response = $this->get(route('ninos.show', 1));

        $response->assertStatus(200);
        $response->assertViewIs('ninos.show');
        $response->assertViewHas('nino');
    });

    it('retorna 404 para niño inexistente', function () {
        $response = $this->get(route('ninos.show', 9999));

        $response->assertStatus(404);
    });
});
