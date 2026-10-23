@extends('layouts.app')

@section('title', $success ? 'Cuenta activada' : 'Error de activación')

@section('content')
    <div class="min-h-[calc(100vh-200px)] flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-sm text-center">
            {{-- Logo --}}
            <div class="mb-8">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-4xl">diversity_3</span>
                    <span class="text-primary font-bold text-2xl">Juntos de nuevo</span>
                </a>
            </div>

            @if($success)
                {{-- Estado de éxito --}}
                <div class="bg-surface rounded-2xl shadow-sm border border-outline-variant p-8">
                    <div class="w-16 h-16 mx-auto rounded-full bg-tertiary-container flex items-center justify-center mb-6">
                        <span class="material-symbols-outlined text-tertiary text-4xl">check_circle</span>
                    </div>

                    <h1 class="text-2xl font-bold text-on-surface mb-3">¡Cuenta activada!</h1>

                    <p class="text-sm text-on-surface-variant mb-4">
                        Tu cuenta ha sido activada exitosamente. Ya puedes iniciar sesión en la plataforma.
                    </p>

                    <div class="bg-secondary-container rounded-xl p-4 mb-6">
                        <p class="text-xs text-on-surface-variant">
                            <strong>Nota:</strong> Tu centro debe ser validado por un administrador antes de poder registrar niños.
                        </p>
                    </div>

                    <a href="{{ route('login') }}" class="inline-flex items-center gap-2 bg-primary text-on-primary px-8 py-3 rounded-full text-sm font-medium hover:opacity-90 transition-opacity">
                        <span class="material-symbols-outlined text-base">login</span>
                        Iniciar sesión
                    </a>
                </div>
            @else
                {{-- Estado de error --}}
                <div class="bg-surface rounded-2xl shadow-sm border border-outline-variant p-8">
                    <div class="w-16 h-16 mx-auto rounded-full bg-error-container flex items-center justify-center mb-6">
                        <span class="material-symbols-outlined text-error text-4xl">cancel</span>
                    </div>

                    <h1 class="text-2xl font-bold text-on-surface mb-3">Token inválido</h1>

                    <p class="text-sm text-on-surface-variant mb-6">
                        El enlace de activación no es válido o ha expirado. Contacta al administrador si necesitas ayuda.
                    </p>

                    <a href="{{ route('home') }}" class="inline-flex items-center gap-2 bg-surface-variant text-on-surface-variant px-8 py-3 rounded-full text-sm font-medium hover:bg-secondary-container transition-colors">
                        <span class="material-symbols-outlined text-base">home</span>
                        Volver al inicio
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection
