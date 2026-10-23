<?php

namespace App\Validaciones;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNinoValidacion extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => 'sometimes|string|max:100',
            'apellido' => 'sometimes|string|max:100',
            'edad' => 'sometimes|integer|min:0|max:17',
            'fecha_nacimiento' => 'sometimes|date|before:today',
            'sexo' => 'sometimes|in:M,F',
            'direccion_previa' => 'sometimes|nullable|string|max:500',
            'nombre_madre' => 'sometimes|nullable|string|max:200',
            'nombre_padre' => 'sometimes|nullable|string|max:200',
            'notas' => 'sometimes|nullable|string|max:1000',
            'estado' => 'sometimes|in:encontrado,familia_localizada,recogido,otro_centro',
        ];
    }

    public function messages(): array
    {
        return [
            'edad.min' => 'La edad no puede ser negativa',
            'edad.max' => 'La edad no puede superar 17 años',
            'estado.in' => 'Estado no válido',
        ];
    }
}
