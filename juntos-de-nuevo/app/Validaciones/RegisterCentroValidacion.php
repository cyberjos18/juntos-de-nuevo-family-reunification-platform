<?php

namespace App\Validaciones;

use Illuminate\Foundation\Http\FormRequest;

class RegisterCentroValidacion extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tipo_centro' => 'required|in:proteccion_civil,hospital,casa_acogida,otro',
            'nombre_centro' => 'required|string|max:200',
            'nombre' => 'required|string|max:100',
            'email' => 'required|email|unique:centros,email|unique:users,email',
            'cargo' => 'required|string|max:100',
            'password' => 'required|string|min:8',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:500',
            'foto_identificacion' => 'required|image|mimes:jpeg,png,jpg|max:5120',
        ];
    }

    public function messages(): array
    {
        return [
            'tipo_centro.required' => 'El tipo de centro es obligatorio',
            'tipo_centro.in' => 'Tipo de centro no válido',
            'nombre_centro.required' => 'El nombre del centro es obligatorio',
            'nombre.required' => 'Tu nombre es obligatorio',
            'email.required' => 'El email es obligatorio',
            'email.email' => 'El email no tiene formato válido',
            'email.unique' => 'Este email ya está registrado',
            'cargo.required' => 'Tu cargo es obligatorio',
            'password.required' => 'La contraseña es obligatoria',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres',
            'foto_identificacion.required' => 'Debes subir una foto de identificación para completar el registro.',
            'foto_identificacion.image' => 'El archivo debe ser una imagen válida.',
            'foto_identificacion.mimes' => 'La imagen debe ser formato JPEG, PNG o JPG.',
            'foto_identificacion.max' => 'La imagen no debe superar los 5 MB.',
        ];
    }
}
