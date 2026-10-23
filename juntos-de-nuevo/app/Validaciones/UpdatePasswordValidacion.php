<?php

namespace App\Validaciones;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePasswordValidacion extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->esAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'nueva_password' => 'required|string|min:8|confirmed',
        ];
    }

    public function messages(): array
    {
        return [
            'nueva_password.required' => 'La nueva contraseña es obligatoria',
            'nueva_password.min' => 'La contraseña debe tener al menos 8 caracteres',
            'nueva_password.confirmed' => 'Las contraseñas no coinciden',
        ];
    }
}
