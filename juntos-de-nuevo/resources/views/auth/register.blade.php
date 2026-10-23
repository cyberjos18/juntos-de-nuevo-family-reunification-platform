@extends('layouts.app')

@section('title', 'Registrar centro')

@section('content')
    <div class="max-w-lg mx-auto px-4 sm:px-6 py-8 sm:py-12">
        {{-- Back --}}
        <a href="{{ route('home') }}" class="inline-flex items-center gap-1 text-sm text-on-surface-variant hover:text-primary transition-colors mb-6">
            <span class="material-symbols-outlined text-lg">arrow_back</span>
            Volver al inicio
        </a>

        {{-- Card intro --}}
        <div class="bg-primary-container rounded-xl p-5 mb-6">
            <div class="flex items-start gap-3">
                <span class="material-symbols-outlined text-primary text-3xl">apartment</span>
                <div>
                    <h2 class="text-base font-medium text-on-surface mb-1">¿Eres un centro de acogida?</h2>
                    <p class="text-sm text-on-surface-variant">
                        Regístrate para poder registrar niños encontrados y ayudar a reunir familias.
                    </p>
                </div>
            </div>
        </div>

        {{-- Formulario --}}
        <div class="bg-surface rounded-2xl shadow-sm border border-outline-variant p-6 sm:p-8">
            <h1 class="text-2xl font-bold text-on-surface mb-6">Registrar centro</h1>

            {{-- Resumen de errores --}}
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl">
                    <p class="font-bold text-red-800 mb-2 flex items-center gap-2">
                        <span class="material-symbols-outlined text-red-600 text-xl">error</span>
                        Por favor, corrige los siguientes errores:
                    </p>
                    <ul class="list-disc pl-5 space-y-1 text-red-700 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" novalidate class="space-y-5">
                @csrf

                {{-- Tipo de centro --}}
                <div>
                    <label for="tipo_centro" class="block text-sm font-medium text-on-surface-variant mb-1.5">Tipo de centro *</label>
                    <select id="tipo_centro" name="tipo_centro" required
                            class="w-full px-4 py-3 rounded-xl border {{ $errors->has('tipo_centro') ? 'border-red-500 focus:ring-2 focus:ring-red-500 focus:border-red-500' : 'border-outline focus:border-primary focus:ring-2 focus:ring-primary/20' }} bg-surface text-on-surface text-sm outline-none transition-all">
                        <option value="">Seleccionar...</option>
                        <option value="proteccion_civil" {{ old('tipo_centro') == 'proteccion_civil' ? 'selected' : '' }}>Protección Civil</option>
                        <option value="hospital" {{ old('tipo_centro') == 'hospital' ? 'selected' : '' }}>Hospital</option>
                        <option value="casa_acogida" {{ old('tipo_centro') == 'casa_acogida' ? 'selected' : '' }}>Casa de Acogida</option>
                        <option value="otro" {{ old('tipo_centro') == 'otro' ? 'selected' : '' }}>Otro</option>
                    </select>
                    @error('tipo_centro')
                        <p class="text-red-600 text-sm font-medium mt-1 block">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nombre del centro --}}
                <div>
                    <label for="nombre_centro" class="block text-sm font-medium text-on-surface-variant mb-1.5">Nombre del centro *</label>
                    <input type="text" id="nombre_centro" name="nombre_centro" value="{{ old('nombre_centro') }}" required
                           class="w-full px-4 py-3 rounded-xl border {{ $errors->has('nombre_centro') ? 'border-red-500 focus:ring-2 focus:ring-red-500 focus:border-red-500' : 'border-outline focus:border-primary focus:ring-2 focus:ring-primary/20' }} bg-surface text-on-surface text-sm outline-none transition-all"
                           placeholder="Ej: Protección Civil Central">
                    @error('nombre_centro')
                        <p class="text-red-600 text-sm font-medium mt-1 block">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nombre del gestor --}}
                <div>
                    <label for="nombre" class="block text-sm font-medium text-on-surface-variant mb-1.5">Tu nombre *</label>
                    <input type="text" id="nombre" name="nombre" value="{{ old('nombre') }}" required
                           class="w-full px-4 py-3 rounded-xl border {{ $errors->has('nombre') ? 'border-red-500 focus:ring-2 focus:ring-red-500 focus:border-red-500' : 'border-outline focus:border-primary focus:ring-2 focus:ring-primary/20' }} bg-surface text-on-surface text-sm outline-none transition-all"
                           placeholder="Tu nombre completo">
                    @error('nombre')
                        <p class="text-red-600 text-sm font-medium mt-1 block">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-on-surface-variant mb-1.5">Email *</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                           class="w-full px-4 py-3 rounded-xl border {{ $errors->has('email') ? 'border-red-500 focus:ring-2 focus:ring-red-500 focus:border-red-500' : 'border-outline focus:border-primary focus:ring-2 focus:ring-primary/20' }} bg-surface text-on-surface text-sm outline-none transition-all"
                           placeholder="email@centro.org">
                    @error('email')
                        <p class="text-red-600 text-sm font-medium mt-1 block">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Cargo --}}
                <div>
                    <label for="cargo" class="block text-sm font-medium text-on-surface-variant mb-1.5">Cargo en el centro *</label>
                    <input type="text" id="cargo" name="cargo" value="{{ old('cargo') }}" required
                           class="w-full px-4 py-3 rounded-xl border {{ $errors->has('cargo') ? 'border-red-500 focus:ring-2 focus:ring-red-500 focus:border-red-500' : 'border-outline focus:border-primary focus:ring-2 focus:ring-primary/20' }} bg-surface text-on-surface text-sm outline-none transition-all"
                           placeholder="Ej: Director, Coordinador">
                    @error('cargo')
                        <p class="text-red-600 text-sm font-medium mt-1 block">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="block text-sm font-medium text-on-surface-variant mb-1.5">Contraseña *</label>
                    <div class="relative" x-data="{ showPw: false }">
                        <input :type="showPw ? 'text' : 'password'" id="password" name="password" required
                               class="w-full px-4 py-3 pr-12 rounded-xl border {{ $errors->has('password') ? 'border-red-500 focus:ring-2 focus:ring-red-500 focus:border-red-500' : 'border-outline focus:border-primary focus:ring-2 focus:ring-primary/20' }} bg-surface text-on-surface text-sm outline-none transition-all"
                               placeholder="Mínimo 8 caracteres">
                        <button type="button" @click="showPw = !showPw" class="absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant hover:text-on-surface">
                            <span class="material-symbols-outlined text-xl" x-text="showPw ? 'visibility_off' : 'visibility'"></span>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-red-600 text-sm font-medium mt-1 block">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Teléfono --}}
                <div>
                    <label for="telefono" class="block text-sm font-medium text-on-surface-variant mb-1.5">Teléfono (opcional)</label>
                    <input type="text" id="telefono" name="telefono" value="{{ old('telefono') }}"
                           class="w-full px-4 py-3 rounded-xl border {{ $errors->has('telefono') ? 'border-red-500 focus:ring-2 focus:ring-red-500 focus:border-red-500' : 'border-outline focus:border-primary focus:ring-2 focus:ring-primary/20' }} bg-surface text-on-surface text-sm outline-none transition-all"
                           placeholder="+58 414 555 1234">
                    @error('telefono')
                        <p class="text-red-600 text-sm font-medium mt-1 block">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Dirección --}}
                <div>
                    <label for="direccion" class="block text-sm font-medium text-on-surface-variant mb-1.5">Dirección (opcional)</label>
                    <textarea id="direccion" name="direccion" rows="2"
                              class="w-full px-4 py-3 rounded-xl border {{ $errors->has('direccion') ? 'border-red-500 focus:ring-2 focus:ring-red-500 focus:border-red-500' : 'border-outline focus:border-primary focus:ring-2 focus:ring-primary/20' }} bg-surface text-on-surface text-sm outline-none transition-all resize-none"
                              placeholder="Dirección del centro">{{ old('direccion') }}</textarea>
                    @error('direccion')
                        <p class="text-red-600 text-sm font-medium mt-1 block">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Foto de Identificación --}}
                <x-foto-upload name="foto_identificacion" label="Foto de Identificación" :required="true" />

                {{-- Submit --}}
                <button type="submit" class="w-full bg-primary text-on-primary py-3 rounded-full text-sm font-medium hover:opacity-90 transition-opacity focus:ring-4 focus:ring-primary/20">
                    Registrar centro
                </button>

                <p class="text-xs text-on-surface-variant text-center">* Campos obligatorios</p>
            </form>
        </div>

        {{-- Link a login --}}
        <p class="text-center text-sm text-on-surface-variant mt-6">
            ¿Ya tienes cuenta?
            <a href="{{ route('login') }}" class="text-primary font-medium hover:underline">Iniciar sesión</a>
        </p>
    </div>
@endsection
