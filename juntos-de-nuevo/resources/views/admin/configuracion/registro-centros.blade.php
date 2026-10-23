@extends('layouts.app')

@section('title', 'Habilitar registro de centro')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-6 pb-28 md:pb-8 space-y-6">

    {{-- Header --}}
    <header>
        <h1 class="text-2xl sm:text-3xl font-bold text-on-surface">Habilitar registro de centro</h1>
        <p class="text-on-surface-variant text-sm mt-1">Controla si nuevos centros pueden registrarse en la plataforma</p>
    </header>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-xl p-4 flex items-center gap-3" x-data x-init="$el.scrollIntoView({ behavior: 'smooth' })">
            <span class="material-symbols-outlined text-xl text-green-600">check_circle</span>
            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
        </div>
    @endif

    {{-- Toggle Card --}}
    <div class="bg-surface rounded-xl border border-outline-variant/20 p-6">
        <div x-data="{ habilitado: {{ $habilitado ? 'true' : 'false' }} }">

            <div class="flex items-center justify-between">
                <div>
                    <p class="text-base font-medium text-on-surface">Registro de centros</p>
                    <p class="text-sm text-on-surface-variant mt-0.5" x-text="habilitado ? 'Los nuevos centros pueden registrarse' : 'No se aceptan nuevos registros'"></p>
                </div>

                {{-- Toggle Switch (Rediseñado: más grande, alto contraste, etiquetas visibles) --}}
                <div class="flex items-center gap-3">
                    <button
                        type="button"
                        @click="habilitado = !habilitado; $refs.hiddenInput.value = habilitado ? '1' : '0'; $refs.form.submit()"
                        :class="habilitado ? 'bg-green-500' : 'bg-gray-200'"
                        class="relative inline-flex h-7 w-14 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus-visible:ring-2 focus-visible:ring-green-500 focus-visible:ring-offset-2"
                        role="switch"
                        :aria-checked="habilitado"
                    >
                        <span class="sr-only">Habilitar registro</span>
                        <span
                            aria-hidden="true"
                            :class="habilitado ? 'translate-x-7' : 'translate-x-0'"
                            class="pointer-events-none inline-block h-6 w-6 transform rounded-full bg-white shadow-lg transition duration-200 ease-in-out"
                        ></span>
                    </button>
                    {{-- Etiqueta de estado visible --}}
                    <span
                        class="text-sm font-semibold transition-colors duration-200 select-none"
                        :class="habilitado ? 'text-green-600' : 'text-gray-400'"
                        x-text="habilitado ? 'Habilitado' : 'Deshabilitado'"
                    ></span>
                </div>
            </div>

            {{-- Estado actual --}}
            <div class="mt-5 pt-4 border-t border-outline-variant/30">
                <div class="flex items-center gap-2">
                    <span
                        class="material-symbols-outlined text-lg"
                        :class="habilitado ? 'text-green-600' : 'text-red-500'"
                        x-text="habilitado ? 'check_circle' : 'block'"
                    ></span>
                    <span
                        class="text-sm font-semibold"
                        :class="habilitado ? 'text-green-600' : 'text-red-500'"
                        x-text="habilitado ? 'Registro activo' : 'Registro bloqueado'"
                    ></span>
                </div>
            </div>

            {{-- Formulario oculto --}}
            <form x-ref="form" method="POST" action="{{ route('admin.configuracion.registro-centros.update') }}" class="hidden">
                @csrf
                @method('PUT')
                <input x-ref="hiddenInput" type="hidden" name="habilitado" value="{{ $habilitado ? '1' : '0' }}">
            </form>

        </div>
    </div>

    {{-- Info Card --}}
    <div class="bg-primary-container/30 border border-primary/10 rounded-xl p-5 flex items-start gap-4">
        <span class="material-symbols-outlined text-2xl text-primary mt-0.5">info</span>
        <div>
            <p class="text-sm font-medium text-on-surface">¿Qué hace esta configuración?</p>
            <p class="text-sm text-on-surface-variant mt-1">
                Cuando el registro está <strong>deshabilitado</strong>, los visitantes verán un mensaje indicando que el registro de nuevos centros está temporalmente cerrado. Los centros ya registrados podrán seguir utilizando la plataforma con normalidad.
            </p>
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
