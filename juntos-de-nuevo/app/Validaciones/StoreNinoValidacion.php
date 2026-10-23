<?php

namespace App\Validaciones;

use Illuminate\Foundation\Http\FormRequest;

class StoreNinoValidacion extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:5120',
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'edad' => 'nullable|integer|min:0|max:17',
            'fecha_nacimiento' => 'nullable|date|before:today',
            'sexo' => 'nullable|in:M,F',
            'direccion_previa' => 'nullable|string|max:500',
            'nombre_madre' => 'nullable|string|max:200',
            'nombre_padre' => 'nullable|string|max:200',
            'notas' => 'nullable|string|max:1000',
            'familiares' => 'nullable|array',
            'familiares.*.parentesco' => 'required_with:familiares|string|max:50',
            'familiares.*.nombre' => 'required_with:familiares|string|max:200',
            'familiares.*.apellido' => 'nullable|string|max:200',
            'familiares.*.telefono' => 'nullable|string|max:20',
        ];
    }

    public function messages(): array
    {
        return [
            'foto.required' => 'La foto del niño es obligatoria',
            'foto.image' => 'El archivo debe ser una imagen',
            'foto.mimes' => 'Formatos permitidos: JPG, PNG',
            'foto.max' => 'La imagen no debe superar 5MB',
            'nombre.required' => 'El nombre del niño es obligatorio',
            'apellido.required' => 'El apellido del niño es obligatorio',
            'edad.min' => 'La edad no puede ser negativa',
            'edad.max' => 'La edad no puede superar 17 años',
            'fecha_nacimiento.before' => 'La fecha de nacimiento debe ser anterior a hoy',
        ];
    }
}
