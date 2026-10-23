<?php

use App\Models\User;

/*
|--------------------------------------------------------------------------
| Helpers de Testing
|--------------------------------------------------------------------------
*/

/**
 * Obtener el usuario Admin de prueba
 */
function adminUser(): User
{
    return User::where('email', 'admin@findchild.org')->first();
}

/**
 * Obtener un usuario Gestor de prueba (Protección Civil)
 */
function gestorUser(): User
{
    return User::where('email', 'maria.gonzalez@proteccion.org')->first();
}

/**
 * Obtener un usuario Gestor de otro centro (Hospital)
 */
function gestorHospitalUser(): User
{
    return User::where('email', 'carlos.ramirez@hospital.org')->first();
}

/**
 * Autenticar un usuario y retornar el token
 */
function authAs(User $user): string
{
    $token = $user->createToken('test-token')->plainTextToken;
    return $token;
}

/**
 * Headers de autorización con token Bearer
 */
function authHeaders(User $user): array
{
    return [
        'Authorization' => 'Bearer ' . authAs($user),
        'Accept' => 'application/json',
    ];
}
