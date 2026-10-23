<?php

use App\Models\User;

/*
|--------------------------------------------------------------------------
| UserModelTest - Helpers de rol y estado del modelo User
|--------------------------------------------------------------------------
*/

describe('esAdmin()', function () {
    it('retorna true para rol admin', function () {
        $user = new User(['rol' => 'admin']);

        expect($user->esAdmin())->toBeTrue();
    });

    it('retorna false para rol gestor', function () {
        $user = new User(['rol' => 'gestor']);

        expect($user->esAdmin())->toBeFalse();
    });
});

describe('esGestor()', function () {
    it('retorna true para rol gestor', function () {
        $user = new User(['rol' => 'gestor']);

        expect($user->esGestor())->toBeTrue();
    });

    it('retorna false para rol admin', function () {
        $user = new User(['rol' => 'admin']);

        expect($user->esGestor())->toBeFalse();
    });
});

describe('estaActivo()', function () {
    it('retorna true cuando activo es true', function () {
        $user = new User(['activo' => true]);

        expect($user->estaActivo())->toBeTrue();
    });

    it('retorna false cuando activo es false', function () {
        $user = new User(['activo' => false]);

        expect($user->estaActivo())->toBeFalse();
    });
});

describe('Datos ocultos en serialización', function () {
    it('nunca expone password, remember_token ni token_activacion', function () {
        $user = adminUser();

        $array = $user->toArray();

        expect($array)->not->toHaveKey('password');
        expect($array)->not->toHaveKey('remember_token');
        expect($array)->not->toHaveKey('token_activacion');
    });
});
