@extends('layouts.app')

@section('title', 'Configuración de Email')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 pb-28 md:pb-8 space-y-6">

    {{-- Header --}}
    <header>
        <h1 class="text-2xl sm:text-3xl font-bold text-on-surface">Configuración de Email</h1>
        <p class="text-on-surface-variant text-sm mt-1">Gestiona las credenciales de Mailjet para las notificaciones</p>
    </header>

    {{-- Card de Estado Actual --}}
    @if($configurado)
        <div class="bg-tertiary rounded-xl p-5 flex items-center gap-4">
            <span class="material-symbols-outlined text-3xl text-white">check_circle</span>
            <div>
                <p class="text-base font-bold text-white">Correo configurado</p>
                <p class="text-sm text-white/80">
                    Driver actual: {{ $config->has('MAIL_MAILER') ? $config->get('MAIL_MAILER')->valor : 'log' }}
                </p>
            </div>
        </div>
    @else
        <div class="bg-error rounded-xl p-5 flex items-center gap-4">
            <span class="material-symbols-outlined text-3xl text-white">warning</span>
            <div>
                <p class="text-base font-bold text-white">Correo NO configurado</p>
                <p class="text-sm text-white/80">
                    Complete las credenciales para habilitar las notificaciones por email.
                </p>
            </div>
        </div>
    @endif

    {{-- Formulario de Configuración --}}
    <form method="POST" action="{{ route('admin.configuracion.mailjet.update') }}" class="space-y-6" x-data="{ mailer: '{{ old('MAIL_MAILER', $config->has('MAIL_MAILER') ? $config->get('MAIL_MAILER')->valor : 'log') }}' }">
        @csrf
        @method('PUT')

        {{-- Driver --}}
        <div class="flex flex-col gap-1">
            <label for="MAIL_MAILER" class="text-sm font-medium text-on-surface-variant ml-1">Driver de correo</label>
            <select
                name="MAIL_MAILER"
                id="MAIL_MAILER"
                x-model="mailer"
                class="w-full h-14 px-4 rounded-xl bg-surface-container border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all text-base text-on-surface"
            >
                <option value="log" {{ old('MAIL_MAILER', $config->has('MAIL_MAILER') ? $config->get('MAIL_MAILER')->valor : 'log') === 'log' ? 'selected' : '' }}>log (desarrollo)</option>
                <option value="smtp" {{ old('MAIL_MAILER', $config->has('MAIL_MAILER') ? $config->get('MAIL_MAILER')->valor : '') === 'smtp' ? 'selected' : '' }}>smtp</option>
                <option value="mailjet" {{ old('MAIL_MAILER', $config->has('MAIL_MAILER') ? $config->get('MAIL_MAILER')->valor : '') === 'mailjet' ? 'selected' : '' }}>mailjet (API v3.1)</option>
                <option value="brevo" {{ old('MAIL_MAILER', $config->has('MAIL_MAILER') ? $config->get('MAIL_MAILER')->valor : '') === 'brevo' ? 'selected' : '' }}>brevo (SMTP)</option>
            </select>
            <p class="text-xs text-on-surface-variant mt-1 ml-1">
                <span x-show="mailer === 'mailjet'">✅ Con API v3.1 — Solo necesitas API Key y Secret Key.</span>
                <span x-show="mailer === 'brevo'">📬 Brevo SMTP — Solo necesitas API Key v3. Obtener en app.brevo.com</span>
                <span x-show="mailer === 'smtp'">🔧 Requiere servidor SMTP, puerto, usuario y contraseña.</span>
                <span x-show="mailer === 'log'">📝 Los correos se guardan en el log (solo desarrollo).</span>
            </p>
            @error('MAIL_MAILER')
                <p class="text-sm text-error mt-0.5 ml-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Credenciales API Mailjet --}}
        <div x-show="mailer === 'mailjet'" x-cloak class="bg-surface rounded-xl border border-outline-variant/20 p-5 space-y-4">
            <div class="flex items-center gap-2 mb-2 border-b border-outline-variant/30 pb-3">
                <span class="material-symbols-outlined text-primary">key</span>
                <h3 class="text-base font-bold text-on-surface">Credenciales de API</h3>
            </div>

            <x-password-field
                name="MAILJET_API_KEY"
                label="API Key"
                placeholder="••••••••••••••••••••"
                :value="$config->has('MAILJET_API_KEY') ? $config->get('MAILJET_API_KEY')->valor : ''"
            />

            <x-password-field
                name="MAILJET_SECRET_KEY"
                label="Secret Key"
                placeholder="••••••••••••••••••••"
                :value="$config->has('MAILJET_SECRET_KEY') ? $config->get('MAILJET_SECRET_KEY')->valor : ''"
            />
        </div>

        {{-- Credenciales Brevo --}}
        <div x-show="mailer === 'brevo'" x-cloak class="bg-surface rounded-xl border border-outline-variant/20 p-5 space-y-4">
            <div class="flex items-center gap-2 mb-2 border-b border-outline-variant/30 pb-3">
                <span class="material-symbols-outlined text-secondary">key</span>
                <h3 class="text-base font-bold text-on-surface">Credenciales de Brevo</h3>
            </div>
            
            <div class="bg-primary-container/30 rounded-lg p-3 text-sm text-on-primary-container mb-3">
                <p>ℹ️ Con <strong>Brevo</strong> solo necesitas tu <strong>API Key v3</strong>.</p>
                <p class="mt-1">Obtenla en: <strong>Brevo > Account > API Keys</strong></p>
            </div>
            
            <x-password-field
                name="BREVO_API_KEY"
                label="Brevo API Key (v3)"
                placeholder="xkeysib-..."
                :value="$config->has('BREVO_API_KEY') ? $config->get('BREVO_API_KEY')->valor : ''"
            />
        </div>

        {{-- SMTP y Remitente --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- SMTP Config --}}
            <div class="bg-surface rounded-xl border border-outline-variant/20 p-5 space-y-4" x-show="mailer === 'smtp'" x-cloak>
                <div class="flex items-center gap-2 mb-2 border-b border-outline-variant/30 pb-3">
                    <span class="material-symbols-outlined text-secondary">dns</span>
                    <h3 class="text-sm font-bold text-on-surface uppercase tracking-wider">Configuración SMTP</h3>
                </div>

                <div class="flex flex-col gap-1">
                    <label for="MAIL_HOST" class="text-sm font-medium text-on-surface-variant ml-1">Servidor SMTP</label>
                    <input
                        type="text"
                        name="MAIL_HOST"
                        id="MAIL_HOST"
                        value="{{ old('MAIL_HOST', $config->has('MAIL_HOST') ? $config->get('MAIL_HOST')->valor : 'in-v3.mailjet.com') }}"
                        placeholder="in-v3.mailjet.com"
                        class="w-full h-12 px-4 rounded-lg border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all text-sm text-on-surface"
                    >
                    @error('MAIL_HOST')
                        <p class="text-sm text-error mt-0.5 ml-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-col gap-1">
                    <label for="MAIL_PORT" class="text-sm font-medium text-on-surface-variant ml-1">Puerto</label>
                    <input
                        type="number"
                        name="MAIL_PORT"
                        id="MAIL_PORT"
                        value="{{ old('MAIL_PORT', $config->has('MAIL_PORT') ? $config->get('MAIL_PORT')->valor : '587') }}"
                        placeholder="587"
                        min="1"
                        max="65535"
                        class="w-full h-12 px-4 rounded-lg border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all text-sm text-on-surface"
                    >
                    @error('MAIL_PORT')
                        <p class="text-sm text-error mt-0.5 ml-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Remitente --}}
            <div class="bg-surface rounded-xl border border-outline-variant/20 p-5 space-y-4">
                <div class="flex items-center gap-2 mb-2 border-b border-outline-variant/30 pb-3">
                    <span class="material-symbols-outlined text-secondary">person_pin</span>
                    <h3 class="text-sm font-bold text-on-surface uppercase tracking-wider">Remitente</h3>
                </div>

                {{-- Info cuando se usa mailjet API --}}
                <div x-show="mailer === 'mailjet'" x-cloak class="bg-primary-container/30 rounded-lg p-3 text-sm text-on-primary-container">
                    <p>ℹ️ Con el driver <strong>mailjet</strong> (API v3.1) <strong>NO se necesita configurar SMTP</strong>.</p>
                    <p class="mt-1">Solo se requieren: <strong>API Key</strong> y <strong>Secret Key</strong>.</p>
                </div>

                {{-- Info cuando se usa brevo --}}
                <div x-show="mailer === 'brevo'" x-cloak class="bg-primary-container/30 rounded-lg p-3 text-sm text-on-primary-container">
                    <p>ℹ️ Con el driver <strong>brevo</strong> (SMTP) <strong>NO se necesita configurar SMTP</strong>.</p>
                    <p class="mt-1">Solo se requiere: <strong>API Key v3</strong> de Brevo.</p>
                </div>

                <div class="flex flex-col gap-1">
                    <label for="MAIL_FROM_ADDRESS" class="text-sm font-medium text-on-surface-variant ml-1">Email remitente</label>
                    <input
                        type="email"
                        name="MAIL_FROM_ADDRESS"
                        id="MAIL_FROM_ADDRESS"
                        value="{{ old('MAIL_FROM_ADDRESS', $config->has('MAIL_FROM_ADDRESS') ? $config->get('MAIL_FROM_ADDRESS')->valor : '') }}"
                        placeholder="noreply@findchild.org"
                        class="w-full h-12 px-4 rounded-lg border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all text-sm text-on-surface"
                    >
                    @error('MAIL_FROM_ADDRESS')
                        <p class="text-sm text-error mt-0.5 ml-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-col gap-1">
                    <label for="MAIL_FROM_NAME" class="text-sm font-medium text-on-surface-variant ml-1">Nombre remitente</label>
                    <input
                        type="text"
                        name="MAIL_FROM_NAME"
                        id="MAIL_FROM_NAME"
                        value="{{ old('MAIL_FROM_NAME', $config->has('MAIL_FROM_NAME') ? $config->get('MAIL_FROM_NAME')->valor : 'Juntos de nuevo') }}"
                        placeholder="Juntos de nuevo"
                        maxlength="100"
                        class="w-full h-12 px-4 rounded-lg border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all text-sm text-on-surface"
                    >
                    @error('MAIL_FROM_NAME')
                        <p class="text-sm text-error mt-0.5 ml-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Botón Guardar --}}
        <button type="submit"
                class="w-full py-3.5 bg-primary text-on-primary rounded-full text-base font-bold hover:opacity-90 active:scale-[0.98] transition-all flex items-center justify-center gap-2 shadow-md">
            <span class="material-symbols-outlined text-xl">save</span>
            Guardar configuración
        </button>
    </form>

    {{-- Botón Enviar Email de Prueba (AJAX) --}}
    <div class="bg-surface rounded-xl border border-outline-variant/20 p-5 space-y-4">
        <div class="flex items-center gap-2 mb-2">
            <span class="material-symbols-outlined text-primary">mail</span>
            <h3 class="text-base font-bold text-on-surface">Enviar correo de prueba</h3>
        </div>
        <p class="text-sm text-on-surface-variant">Ingresa un email de destino para recibir un correo de prueba usando la configuración actual.</p>

        <div class="space-y-4" x-data="{
            loading: false,
            mensaje: '',
            error: false,
            async enviarPrueba(e) {
                e.preventDefault();
                const form = e.target;
                const email = form.querySelector('#test_email').value;
                if (!email) return;
                
                loading = true;
                mensaje = '';
                error = false;
                
                try {
                    const formData = new FormData();
                    formData.append('test_email', email);
                    formData.append('_token', form.querySelector('[name=_token]').value);
                    
                    const resp = await fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                    });
                    const data = await resp.json();
                    
                    if (data.success) {
                        mensaje = data.message || 'Email enviado correctamente';
                        error = false;
                    } else {
                        mensaje = data.error?.message || 'Error al enviar el email';
                        error = true;
                    }
                } catch (err) {
                    mensaje = 'Error de conexión. Intente nuevamente.';
                    error = true;
                } finally {
                    loading = false;
                }
            }
        }">
            {{-- Mensajes de resultado (DENTRO del scope de Alpine) --}}
            <div x-show="mensaje" x-transition
                 :class="error ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-green-50 text-green-700 border border-green-200'"
                 class="rounded-lg p-4 text-sm flex items-center gap-3">
                <span class="material-symbols-outlined text-xl shrink-0" x-text="error ? 'error' : 'check_circle'"></span>
                <span x-text="mensaje" class="font-medium"></span>
            </div>

            <form method="POST" action="{{ route('admin.configuracion.mailjet.test') }}" @submit.prevent="enviarPrueba">
                @csrf
                <div class="flex flex-col gap-1">
                    <label for="test_email" class="text-sm font-medium text-on-surface-variant ml-1">Email de destino</label>
                    <input
                        type="email"
                        name="test_email"
                        id="test_email"
                        value="{{ old('test_email') }}"
                        placeholder="correo@ejemplo.com"
                        required
                        class="w-full h-12 px-4 rounded-lg border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all text-sm text-on-surface"
                    >
                </div>
                <button type="submit"
                        :disabled="loading"
                        class="w-full mt-4 py-3.5 border border-primary text-primary rounded-full text-base font-bold hover:bg-primary/5 active:scale-[0.98] transition-all flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                    <template x-if="!loading">
                        <span class="material-symbols-outlined text-xl">send</span>
                    </template>
                    <template x-if="loading">
                        <span class="material-symbols-outlined text-xl animate-spin">sync</span>
                    </template>
                    <span x-text="loading ? 'Enviando...' : '📧 Enviar email de prueba'"></span>
                </button>
            </form>
        </div>
    </div>
</div>

{{-- Bottom Navigation Bar (Mobile) --}}
<nav class="md:hidden fixed bottom-0 left-0 w-full z-50 bg-surface shadow-[0_-2px_10px_rgba(0,0,0,0.05)] rounded-t-xl flex justify-around items-center h-16 border-t border-outline-variant/20">
    <a href="{{ route('admin.dashboard') }}"
       class="flex flex-col items-center justify-center py-1 px-4 text-on-surface-variant">
        <span class="material-symbols-outlined text-xl">analytics</span>
        <span class="text-[10px] font-medium">Stats</span>
    </a>
    <a href="{{ route('admin.centros.index') }}"
       class="flex flex-col items-center justify-center py-1 px-4 text-on-surface-variant">
        <span class="material-symbols-outlined text-xl">apartment</span>
        <span class="text-[10px] font-medium">Centros</span>
    </a>
    <a href="{{ route('admin.configuracion.mailjet') }}"
       class="flex flex-col items-center justify-center py-1 px-4 {{ request()->routeIs('admin.configuracion.*') ? 'text-primary' : 'text-on-surface-variant' }}">
        <span class="material-symbols-outlined text-xl">settings</span>
        <span class="text-[10px] font-medium">Config</span>
    </a>
</nav>
@endsection
