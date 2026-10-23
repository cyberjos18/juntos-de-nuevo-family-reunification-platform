@extends('layouts.app')

@section('title', 'Editar Usuario')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-6 pb-28 md:pb-8 space-y-6"
     x-data="{ tab: 'info' }">

    {{-- Header --}}
    <header>
        <div class="flex items-center gap-2 mb-2">
            <a href="{{ route('admin.usuarios.index') }}" class="text-on-surface-variant hover:text-on-surface transition-colors">
                <span class="material-symbols-outlined">arrow_back</span>
            </a>
        </div>
        <h1 class="text-2xl sm:text-3xl font-bold text-on-surface">Editar Usuario</h1>
        <p class="text-on-surface-variant text-sm mt-1">{{ $usuario->nombre }} — {{ $usuario->email }}</p>
    </header>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center gap-2">
            <span class="material-symbols-outlined text-emerald-600">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    {{-- Tabs --}}
    <div class="flex items-center border-b border-outline-variant">
        <button type="button"
                @click="tab = 'info'"
                class="px-6 py-3 text-sm font-medium transition-colors"
                :class="tab === 'info' ? 'text-primary border-b-2 border-primary' : 'text-on-surface-variant hover:text-on-surface'">
            Información
        </button>
        <button type="button"
                @click="tab = 'password'"
                class="px-6 py-3 text-sm font-medium transition-colors"
                :class="tab === 'password' ? 'text-primary border-b-2 border-primary' : 'text-on-surface-variant hover:text-on-surface'">
            Cambiar contraseña
        </button>
    </div>

    {{-- Tab: Información --}}
    <div x-show="tab === 'info" class="bg-surface rounded-xl border border-outline-variant/20 p-6 shadow-sm">
        <form action="{{ route('admin.usuarios.update', $usuario) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            {{-- Nombre --}}
            <div>
                <label for="nombre" class="block text-sm font-medium text-on-surface mb-1">Nombre completo</label>
                <input type="text"
                       id="nombre"
                       name="nombre"
                       value="{{ old('nombre', $usuario->nombre) }}"
                       class="w-full px-4 py-2.5 rounded-xl border border-outline-variant bg-surface text-on-surface focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-shadow
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
                       value="{{ old('email', $usuario->email) }}"
                       class="w-full px-4 py-2.5 rounded-xl border border-outline-variant bg-surface text-on-surface focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-shadow
                              {{ $errors->has('email') ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : '' }}">
                @error('email')
                    <p class="text-red-600 text-sm font-medium mt-1 block">{{ $message }}</p>
                @enderror
            </div>

            {{-- Cargo --}}
            <div>
                <label for="cargo" class="block text-sm font-medium text-on-surface mb-1">Cargo</label>
                <input type="text"
                       id="cargo"
                       name="cargo"
                       value="{{ old('cargo', $usuario->cargo ?? '') }}"
                       placeholder="Ej: Coordinador, Administrador..."
                       class="w-full px-4 py-2.5 rounded-xl border border-outline-variant bg-surface text-on-surface placeholder-on-surface-variant/50 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-shadow
                              {{ $errors->has('cargo') ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : '' }}">
                @error('cargo')
                    <p class="text-red-600 text-sm font-medium mt-1 block">{{ $message }}</p>
                @enderror
            </div>

            {{-- Activo --}}
            <div class="flex items-center gap-3">
                <input type="hidden" name="activo" value="0">
                <input type="checkbox"
                       id="activo"
                       name="activo"
                       value="1"
                       {{ old('activo', $usuario->activo) ? 'checked' : '' }}
                       class="w-4 h-4 rounded border-outline-variant text-primary focus:ring-primary">
                <label for="activo" class="text-sm text-on-surface">Usuario activo</label>
            </div>

            {{-- Botones --}}
            <div class="flex items-center gap-3 pt-4">
                <button type="submit"
                        class="px-6 py-2.5 bg-primary text-on-primary rounded-xl text-sm font-medium hover:bg-primary/90 transition-colors shadow-sm">
                    Guardar cambios
                </button>
                <a href="{{ route('admin.usuarios.index') }}"
                   class="px-6 py-2.5 bg-surface-variant text-on-surface-variant rounded-xl text-sm font-medium hover:bg-surface-variant/80 transition-colors">
                    Cancelar
                </a>
            </div>
        </form>
    </div>

    {{-- Tab: Cambiar contraseña --}}
    <div x-show="tab === 'password" class="bg-surface rounded-xl border border-outline-variant/20 p-6 shadow-sm">
        <form action="{{ route('admin.usuarios.updatePassword', $usuario) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            {{-- Nueva contraseña --}}
            <div>
                <label for="nueva_password" class="block text-sm font-medium text-on-surface mb-1">Nueva contraseña</label>
                <input type="password"
                       id="nueva_password"
                       name="nueva_password"
                       placeholder="Mínimo 8 caracteres"
                       class="w-full px-4 py-2.5 rounded-xl border border-outline-variant bg-surface text-on-surface placeholder-on-surface-variant/50 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-shadow
                              {{ $errors->has('nueva_password') ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : '' }}">
                @error('nueva_password')
                    <p class="text-red-600 text-sm font-medium mt-1 block">{{ $message }}</p>
                @enderror
            </div>

            {{-- Confirmar nueva contraseña --}}
            <div>
                <label for="nueva_password_confirmation" class="block text-sm font-medium text-on-surface mb-1">Confirmar nueva contraseña</label>
                <input type="password"
                       id="nueva_password_confirmation"
                       name="nueva_password_confirmation"
                       placeholder="Repite la nueva contraseña"
                       class="w-full px-4 py-2.5 rounded-xl border border-outline-variant bg-surface text-on-surface placeholder-on-surface-variant/50 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-shadow">
            </div>

            {{-- Botones --}}
            <div class="flex items-center gap-3 pt-4">
                <button type="submit"
                        class="px-6 py-2.5 bg-primary text-on-primary rounded-xl text-sm font-medium hover:bg-primary/90 transition-colors shadow-sm">
                    Actualizar contraseña
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
