@extends('layouts.app')

@section('title', 'Registro no disponible')

@section('content')
    <div class="max-w-lg mx-auto px-4 sm:px-6 py-8 sm:py-12">
        {{-- Back --}}
        <a href="{{ route('home') }}" class="inline-flex items-center gap-1 text-sm text-on-surface-variant hover:text-primary transition-colors mb-6">
            <span class="material-symbols-outlined text-lg">arrow_back</span>
            Volver al inicio
        </a>

        {{-- Card de error --}}
        <div class="bg-surface rounded-2xl shadow-sm border border-outline-variant p-6 sm:p-8 text-center space-y-6">
            <div class="flex justify-center">
                <div class="w-20 h-20 bg-error-container/30 rounded-full flex items-center justify-center">
                    <span class="material-symbols-outlined text-5xl text-error">block</span>
                </div>
            </div>

            <div>
                <h1 class="text-2xl font-bold text-on-surface mb-2">Registro no disponible</h1>
                <p class="text-on-surface-variant text-sm leading-relaxed">
                    El registro de nuevos centros está temporalmente deshabilitado por el administrador.
                    <br>Por favor, intenta más tarde o contacta con soporte si necesitas acceso urgente.
                </p>
            </div>

            <div class="flex flex-col gap-3 pt-2">
                <a href="{{ route('login') }}"
                   class="w-full bg-primary text-on-primary py-3 rounded-full text-sm font-medium hover:opacity-90 transition-opacity">
                    Ir al login
                </a>
                <a href="{{ route('home') }}"
                   class="w-full border border-outline-variant text-on-surface-variant py-3 rounded-full text-sm font-medium hover:bg-surface-variant transition-colors">
                    Volver al inicio
                </a>
            </div>
        </div>
    </div>
@endsection
