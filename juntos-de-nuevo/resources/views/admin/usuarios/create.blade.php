@extends('layouts.app')

@section('title', 'Crear Administrador')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-6 pb-28 md:pb-8 space-y-6">

    {{-- Header --}}
    <header>
        <div class="flex items-center gap-2 mb-2">
            <a href="{{ route('admin.usuarios.index') }}" class="text-on-surface-variant hover:text-on-surface transition-colors">
                <span class="material-symbols-outlined">arrow_back</span>
            </a>
        </div>
        <h1 class="text-2xl sm:text-3xl font-bold text-on-surface">Crear Nuevo Administrador</h1>
        <p class="text-on-surface-variant text-sm mt-1">Agrega un nuevo administrador al sistema</p>
    </header>

    {{-- Formulario --}}
    <div class="bg-surface rounded-xl border border-outline-variant/20 p-6 shadow-sm">
        <form action="{{ route('admin.usuarios.store') }}" method="POST" class="space-y-5">
            @csrf

            {{-- Nombre --}}
            <div>
                <label for="nombre" class="block text-sm font-medium text-on-surface mb-1">Nombre completo</label>
                <input type="text"
                       id="nombre"
                       name="nombre"
                       value="{{ old('nombre') }}"
                       placeholder="Nombre del administrador"
                       class="w-full px-4 py-2.5 rounded-xl border border-outline-variant bg-surface text-on-surface placeholder-on-surface-variant/50 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-shadow
                              {{ $errors->has('nombre') ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : '' }}">
                @error('nombre')
                    <p class="text-red-600 text-sm font-medium mt-1 block">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email --}}
            <div>
                <label for="email" class="block text-sm font-medium text-on-surface mb-1">Correo electrónico</label>
                <input type="email"
                       id="email"
                       name="email"
                       value="{{ old('email') }}"
                       placeholder="admin@ejemplo.org"
                       class="w-full px-4 py-2.5 rounded-xl border border-outline-variant bg-surface text-on-surface placeholder-on-surface-variant/50 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-shadow
                              {{ $errors->has('email') ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : '' }}">
                @error('email')
                    <p class="text-red-600 text-sm font-medium mt-1 block">{{ $message }}</p>
                @enderror
            </div>

            {{-- Contraseña --}}
            <div>
                <label for="password" class="block text-sm font-medium text-on-surface mb-1">Contraseña</label>
                <input type="password"
                       id="password"
                       name="password"
                       placeholder="Mínimo 8 caracteres"
                       class="w-full px-4 py-2.5 rounded-xl border border-outline-variant bg-surface text-on-surface placeholder-on-surface-variant/50 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-shadow
                              {{ $errors->has('password') ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : '' }}">
                @error('password')
                    <p class="text-red-600 text-sm font-medium mt-1 block">{{ $message }}</p>
                @enderror
            </div>

            {{-- Confirmar contraseña --}}
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-on-surface mb-1">Confirmar contraseña</label>
                <input type="password"
                       id="password_confirmation"
                       name="password_confirmation"
                       placeholder="Repite la contraseña"
                       class="w-full px-4 py-2.5 rounded-xl border border-outline-variant bg-surface text-on-surface placeholder-on-surface-variant/50 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-shadow">
            </div>

            {{-- Botones --}}
            <div class="flex items-center gap-3 pt-4">
                <button type="submit"
                        class="px-6 py-2.5 bg-primary text-on-primary rounded-xl text-sm font-medium hover:bg-primary/90 transition-colors shadow-sm">
                    Crear Administrador
                </button>
                <a href="{{ route('admin.usuarios.index') }}"
                   class="px-6 py-2.5 bg-surface-variant text-on-surface-variant rounded-xl text-sm font-medium hover:bg-surface-variant/80 transition-colors">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
