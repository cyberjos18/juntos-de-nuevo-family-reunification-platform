<?php

namespace App\Validaciones;

use Illuminate\Foundation\Http\FormRequest;

class StoreRecogidaValidacion extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'familiar_nombre' => 'required|string|max:200',
            'familiar_apellido' => 'required|string|max:200',
            'familiar_parentesco' => 'required|string|max:50',
            'familiar_telefono' => 'required|string|max:20',
            'familiar_email' => 'nullable|email|max:200',
            'familiar_documento' => 'nullable|string|max:50',
            'foto_recogida' => 'required|image|mimes:jpeg,png,jpg|max:5120',
            'notas' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'familiar_nombre.required' => 'El nombre del familiar es obligatorio',
            'familiar_apellido.required' => 'El apellido del familiar es obligatorio',
            'familiar_parentesco.required' => 'El parentesco es obligatorio',
            'familiar_telefono.required' => 'El teléfono del familiar es obligatorio',
            'familiar_email.email' => 'El email no tiene formato válido',
            'foto_recogida.required' => 'Debes subir una foto de evidencia antes de confirmar',
            'foto_recogida.image' => 'El archivo debe ser una imagen',
            'foto_recogida.mimes' => 'Formatos permitidos: JPG, PNG',
            'foto_recogida.max' => 'La imagen no debe superar 5MB',
        ];
    }
}
