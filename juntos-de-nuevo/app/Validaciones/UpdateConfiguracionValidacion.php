<?php

namespace App\Validaciones;

use Illuminate\Foundation\Http\FormRequest;

class UpdateConfiguracionValidacion extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->esAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'MAIL_MAILER' => 'sometimes|string|in:smtp,mailjet,brevo,log',
            'MAILJET_API_KEY' => 'sometimes|string|nullable',
            'MAILJET_SECRET_KEY' => 'sometimes|string|nullable',
            'BREVO_API_KEY' => 'nullable|string|max:255',
            'MAIL_FROM_ADDRESS' => 'sometimes|email|nullable',
            'MAIL_FROM_NAME' => 'sometimes|string|max:100|nullable',
            'MAIL_HOST' => 'sometimes|string|nullable',
            'MAIL_PORT' => 'sometimes|integer|min:1|max:65535|nullable',
        ];
    }

    public function messages(): array
    {
        return [
            'MAIL_MAILER.in' => 'El driver debe ser: smtp, mailjet, brevo o log',
            'MAIL_FROM_ADDRESS.email' => 'El email remitente no es válido',
            'MAIL_PORT.integer' => 'El puerto debe ser un número',
            'MAIL_PORT.min' => 'El puerto debe ser mayor a 0',
            'MAIL_PORT.max' => 'El puerto no puede exceder 65535',
        ];
    }
}
