<?php

namespace App\Persistencia;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserRepository
{
    /**
     * Buscar usuario por email
     */
    public function buscarPorEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    /**
     * Buscar usuario por token de activación
     */
    public function buscarPorTokenActivacion(string $token): ?User
    {
        return User::where('token_activacion', $token)->first();
    }

    /**
     * Buscar usuario por ID con centro
     */
    public function buscarPorId(int $id): ?User
    {
        return User::with('centro')->find($id);
    }

    /**
     * Crear usuario gestor para un centro
     */
    public function crearGestor(int $centroId, string $nombre, string $email, string $password, string $cargo, ?string $fotoIdentificacion = null): User
    {
        return User::create([
            'centro_id' => $centroId,
            'nombre' => $nombre,
            'email' => $email,
            'password' => $password,
            'rol' => 'gestor',
            'cargo' => $cargo,
            'activo' => false,
            'token_activacion' => Str::random(64),
            'foto_identificacion' => $fotoIdentificacion,
        ]);
    }

    /**
     * Activar cuenta por token
     */
    public function activarPorToken(string $token): bool
    {
        \Log::info('Intento de activación por token', ['token_length' => strlen($token)]);
        
        $user = $this->buscarPorTokenActivacion($token);
        
        if (!$user) {
            \Log::warning('Token no encontrado en BD', ['token_length' => strlen($token)]);
            return false;
        }
        
        \Log::info('Token encontrado, activando usuario', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);

        $user->update([
            'activo' => true,
            'email_verified_at' => now(),
            'token_activacion' => null,
        ]);
        
        \Log::info('Usuario activado exitosamente', ['user_id' => $user->id]);

        return true;
    }

    /**
     * Verificar credenciales de login
     */
    public function verificarCredenciales(string $email, string $password): ?User
    {
        $user = $this->buscarPorEmail($email);

        if (!$user || !Hash::check($password, $user->password)) {
            return null;
        }

        return $user;
    }

    /**
     * Activar todos los usuarios de un centro
     */
    public function activarUsuariosDeCentro(int $centroId): int
    {
        return User::where('centro_id', $centroId)->update([
            'activo' => true,
            'email_verified_at' => now(),
            'token_activacion' => null,
        ]);
    }

    /**
     * Contar total de usuarios
     */
    public function contarTotal(): int
    {
        return User::count();
    }

    /**
     * Contar usuarios por rol
     */
    public function contarPorRol(string $rol): int
    {
        return User::where('rol', $rol)->count();
    }

    /**
     * Obtener todos los usuarios con rol admin
     */
    public function obtenerAdmins(): \Illuminate\Database\Eloquent\Collection
    {
        return User::where('rol', 'admin')->get();
    }
}
