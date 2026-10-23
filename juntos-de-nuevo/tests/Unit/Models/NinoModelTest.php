<?php

use App\Models\Nino;
use App\Models\Recogida;

/*
|--------------------------------------------------------------------------
| NinoModelTest - Scopes y helpers de estado del modelo Nino
|--------------------------------------------------------------------------
*/

function crearNinoDePrueba(string $estado = 'encontrado'): Nino
{
    return Nino::create([
        'centro_id' => 1,
        'nombre' => 'Unit',
        'apellido' => 'Test' . uniqid(),
        'edad' => 7,
        'estado' => $estado,
        'creado_por' => adminUser()->id,
    ]);
}

describe('Scopes de estado', function () {
    it('scopeEncontrados solo retorna niños en estado encontrado', function () {
        $nino = crearNinoDePrueba('encontrado');

        $ids = Nino::encontrados()->pluck('id');

        expect($ids)->toContain($nino->id);
    });

    it('scopeRecogidos solo retorna niños en estado recogido', function () {
        $nino = crearNinoDePrueba('recogido');

        $ids = Nino::recogidos()->pluck('id');

        expect($ids)->toContain($nino->id);
        expect(Nino::recogidos()->pluck('id'))->not->toContain(crearNinoDePrueba('encontrado')->id);
    });

    it('scopeFamiliaLocalizada solo retorna niños con familia localizada', function () {
        $nino = crearNinoDePrueba('familia_localizada');

        $ids = Nino::familiaLocalizada()->pluck('id');

        expect($ids)->toContain($nino->id);
    });

    it('scopePorCentro filtra por centro_id', function () {
        $nino = crearNinoDePrueba();

        expect(Nino::porCentro(1)->pluck('id'))->toContain($nino->id);
        expect(Nino::porCentro(999)->pluck('id'))->not->toContain($nino->id);
    });
});

describe('estaRecogido()', function () {
    it('retorna true cuando el estado es recogido', function () {
        $nino = crearNinoDePrueba('recogido');

        expect($nino->estaRecogido())->toBeTrue();
    });

    it('retorna false para cualquier otro estado', function () {
        $nino = crearNinoDePrueba('encontrado');

        expect($nino->estaRecogido())->toBeFalse();
    });
});

describe('puedeSerRecogido()', function () {
    it('retorna true para un niño encontrado sin recogida asociada', function () {
        $nino = crearNinoDePrueba('encontrado');

        expect($nino->puedeSerRecogido())->toBeTrue();
    });

    it('retorna false si el estado ya es recogido', function () {
        $nino = crearNinoDePrueba('recogido');

        expect($nino->puedeSerRecogido())->toBeFalse();
    });

    it('retorna false si ya existe un registro de recogida asociado', function () {
        $nino = crearNinoDePrueba('familia_localizada');

        Recogida::create([
            'nino_id' => $nino->id,
            'familiar_nombre' => 'Test',
            'familiar_apellido' => 'Test',
            'familiar_parentesco' => 'Madre',
            'familiar_telefono' => '+58-412-0000000',
            'foto_recogida_url' => 'recogidas/test.jpg',
            'registrado_por' => adminUser()->id,
        ]);

        expect($nino->puedeSerRecogido())->toBeFalse();
    });
});

describe('marcarComoRecogido()', function () {
    it('cambia el estado a recogido y persiste el cambio', function () {
        $nino = crearNinoDePrueba('encontrado');

        $resultado = $nino->marcarComoRecogido();

        expect($resultado)->toBeTrue();
        expect($nino->fresh()->estado)->toBe('recogido');
    });
});
