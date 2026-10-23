@extends('layouts.app')

@section('title', 'Registrar Niño')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 pb-28 md:pb-8">

    {{-- Header --}}
    <header class="mb-6">
        <a href="{{ route('gestor.ninos.index') }}" class="inline-flex items-center gap-1 text-sm text-on-surface-variant hover:text-primary transition-colors mb-3">
            <span class="material-symbols-outlined text-lg">arrow_back</span>
            Volver al listado
        </a>
        <h1 class="text-2xl sm:text-3xl font-bold text-primary">Registrar Nuevo Niño</h1>
        <p class="text-on-surface-variant text-sm mt-1">
            Ingrese la información detallada para facilitar el proceso de búsqueda y reunificación familiar.
        </p>
    </header>

    {{-- Formulario --}}
    <form method="POST" action="{{ route('gestor.ninos.store') }}" enctype="multipart/form-data" novalidate class="space-y-8">
        @csrf

        {{-- Sección: Foto y Datos Básicos --}}
        <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-start">

            {{-- Foto del niño --}}
            <div class="md:col-span-5">
                <x-foto-upload name="foto" label="Fotografía del menor" :required="true" aspect="4/3" />
            </div>

            {{-- Datos básicos --}}
            <div class="md:col-span-7 space-y-4">
                {{-- Nombre y Apellido --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="nombre" class="block text-sm font-medium text-on-surface-variant mb-1">Nombre <span class="text-error">*</span></label>
                        <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}" required
                               class="w-full rounded-xl {{ $errors->has('nombre') ? 'border-red-500 focus:border-red-500 focus:ring-1 focus:ring-red-500' : 'border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary' }} bg-surface px-4 py-3 text-on-surface outline-none transition-all"
                               placeholder="Nombre del niño">
                        @error('nombre')
                            <p class="text-red-600 text-sm font-medium mt-1 block">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="apellido" class="block text-sm font-medium text-on-surface-variant mb-1">Apellido <span class="text-error">*</span></label>
                        <input type="text" name="apellido" id="apellido" value="{{ old('apellido') }}" required
                               class="w-full rounded-xl {{ $errors->has('apellido') ? 'border-red-500 focus:border-red-500 focus:ring-1 focus:ring-red-500' : 'border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary' }} bg-surface px-4 py-3 text-on-surface outline-none transition-all"
                               placeholder="Apellido del niño">
                        @error('apellido')
                            <p class="text-red-600 text-sm font-medium mt-1 block">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Edad y Fecha de nacimiento --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="edad" class="block text-sm font-medium text-on-surface-variant mb-1">Edad (estimada)</label>
                        <input type="number" name="edad" id="edad" value="{{ old('edad') }}" min="0" max="17"
                               class="w-full rounded-xl {{ $errors->has('edad') ? 'border-red-500 focus:border-red-500 focus:ring-1 focus:ring-red-500' : 'border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary' }} bg-surface px-4 py-3 text-on-surface outline-none transition-all"
                               placeholder="Años">
                        @error('edad')
                            <p class="text-red-600 text-sm font-medium mt-1 block">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="fecha_nacimiento" class="block text-sm font-medium text-on-surface-variant mb-1">Fecha de nacimiento</label>
                        <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" value="{{ old('fecha_nacimiento') }}"
                               class="w-full rounded-xl {{ $errors->has('fecha_nacimiento') ? 'border-red-500 focus:border-red-500 focus:ring-1 focus:ring-red-500' : 'border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary' }} bg-surface px-4 py-3 text-on-surface outline-none transition-all">
                        @error('fecha_nacimiento')
                            <p class="text-red-600 text-sm font-medium mt-1 block">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Sexo --}}
                <div class="p-3 bg-surface-variant/30 rounded-xl border border-outline-variant">
                    <span class="block text-xs font-medium text-on-surface-variant mb-2">Sexo</span>
                    <div class="flex gap-6">
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="radio" name="sexo" value="M" {{ old('sexo') === 'M' ? 'checked' : '' }}
                                   class="w-4 h-4 text-primary focus:ring-primary border-outline">
                            <span class="text-sm text-on-surface group-hover:text-primary transition-colors">Masculino</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="radio" name="sexo" value="F" {{ old('sexo') === 'F' ? 'checked' : '' }}
                                   class="w-4 h-4 text-primary focus:ring-primary border-outline">
                            <span class="text-sm text-on-surface group-hover:text-primary transition-colors">Femenino</span>
                        </label>
                    </div>
                    @error('sexo')
                        <p class="text-red-600 text-sm font-medium mt-1 block">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Información adicional --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="direccion_previa" class="block text-sm font-medium text-on-surface-variant mb-1">Dirección previa (Edificio/Casa)</label>
                <textarea name="direccion_previa" id="direccion_previa" rows="3"
                          class="w-full rounded-xl {{ $errors->has('direccion_previa') ? 'border-red-500 focus:border-red-500 focus:ring-1 focus:ring-red-500' : 'border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary' }} bg-surface px-4 py-3 text-on-surface outline-none transition-all resize-none"
                          placeholder="Última ubicación conocida donde vivía">{{ old('direccion_previa') }}</textarea>
                @error('direccion_previa')
                    <p class="text-red-600 text-sm font-medium mt-1 block">{{ $message }}</p>
                @enderror
            </div>
            <div class="space-y-4">
                <div>
                    <label for="nombre_madre" class="block text-sm font-medium text-on-surface-variant mb-1">Nombre de la madre</label>
                    <input type="text" name="nombre_madre" id="nombre_madre" value="{{ old('nombre_madre') }}"
                           class="w-full rounded-xl {{ $errors->has('nombre_madre') ? 'border-red-500 focus:border-red-500 focus:ring-1 focus:ring-red-500' : 'border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary' }} bg-surface px-4 py-3 text-on-surface outline-none transition-all"
                           placeholder="Nombre completo de la madre">
                    @error('nombre_madre')
                        <p class="text-red-600 text-sm font-medium mt-1 block">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="nombre_padre" class="block text-sm font-medium text-on-surface-variant mb-1">Nombre del padre</label>
                    <input type="text" name="nombre_padre" id="nombre_padre" value="{{ old('nombre_padre') }}"
                           class="w-full rounded-xl {{ $errors->has('nombre_padre') ? 'border-red-500 focus:border-red-500 focus:ring-1 focus:ring-red-500' : 'border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary' }} bg-surface px-4 py-3 text-on-surface outline-none transition-all"
                           placeholder="Nombre completo del padre">
                    @error('nombre_padre')
                        <p class="text-red-600 text-sm font-medium mt-1 block">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Notas --}}
        <div>
            <label for="notas" class="block text-sm font-medium text-on-surface-variant mb-1">Notas adicionales</label>
            <textarea name="notas" id="notas" rows="3"
                      class="w-full rounded-xl {{ $errors->has('notas') ? 'border-red-500 focus:border-red-500 focus:ring-1 focus:ring-red-500' : 'border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary' }} bg-surface px-4 py-3 text-on-surface outline-none transition-all resize-none"
                      placeholder="Observaciones, condiciones de salud, objetos personales...">{{ old('notas') }}</textarea>
            @error('notas')
                <p class="text-red-600 text-sm font-medium mt-1 block">{{ $message }}</p>
            @enderror
        </div>

        {{-- Sección de familiares (Alpine.js dinámica) --}}
        <div x-data="{ familiares: {{ old('familiares') ? count(old('familiares')) : 0 }} }"
             class="rounded-xl border border-outline-variant overflow-hidden bg-surface">

            <div class="flex items-center justify-between p-4 bg-surface-variant/20 border-b border-outline-variant">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">groups</span>
                    <h3 class="text-lg font-semibold text-on-surface">Familiares conocidos</h3>
                </div>
            </div>

            <div class="p-4 space-y-4">
                {{-- Template para agregar familiares dinámicamente --}}
                <template x-for="(index, i) in Array.from({length: familiares}, (_, i) => i)" :key="i">
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end p-4 bg-surface-variant/20 rounded-xl border border-outline-variant/50">
                        <div class="sm:col-span-3">
                            <label class="block text-xs font-medium text-on-surface-variant mb-1">Parentesco <span class="text-error">*</span></label>
                            <select :name="'familiares[' + index + '][parentesco]'"
                                    class="w-full rounded-lg border-outline-variant bg-surface px-3 py-2.5 text-sm text-on-surface focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all">
                                <option value="">Seleccione...</option>
                                <option value="Madre">Madre</option>
                                <option value="Padre">Padre</option>
                                <option value="Tío/a">Tío/a</option>
                                <option value="Abuelo/a">Abuelo/a</option>
                                <option value="Hermano/a">Hermano/a</option>
                                <option value="Primo/a">Primo/a</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>
                        <div class="sm:col-span-3">
                            <label class="block text-xs font-medium text-on-surface-variant mb-1">Nombre <span class="text-error">*</span></label>
                            <input type="text" :name="'familiares[' + index + '][nombre]'"
                                   class="w-full rounded-lg border-outline-variant bg-surface px-3 py-2.5 text-sm text-on-surface focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all"
                                   placeholder="Nombre">
                        </div>
                        <div class="sm:col-span-3">
                            <label class="block text-xs font-medium text-on-surface-variant mb-1">Apellido</label>
                            <input type="text" :name="'familiares[' + index + '][apellido]'"
                                   class="w-full rounded-lg border-outline-variant bg-surface px-3 py-2.5 text-sm text-on-surface focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all"
                                   placeholder="Apellido">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-medium text-on-surface-variant mb-1">Teléfono</label>
                            <input type="tel" :name="'familiares[' + index + '][telefono]'"
                                   class="w-full rounded-lg border-outline-variant bg-surface px-3 py-2.5 text-sm text-on-surface focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all"
                                   placeholder="+58...">
                        </div>
                        <div class="sm:col-span-1 flex justify-end">
                            <button type="button" @click="familiares = Math.max(0, familiares - 1)"
                                    class="p-2 text-error hover:bg-error-container/30 rounded-full transition-colors"
                                    title="Quitar familiar">
                                <span class="material-symbols-outlined text-lg">close</span>
                            </button>
                        </div>
                    </div>
                </template>

                <button type="button" @click="familiares++"
                        class="flex items-center gap-2 text-primary text-sm font-medium py-2 px-4 hover:bg-primary/5 rounded-full transition-colors">
                    <span class="material-symbols-outlined text-lg">add</span>
                    Agregar familiar
                </button>
            </div>
        </div>

        {{-- Botón Submit --}}
        <div class="pt-4 border-t border-outline-variant/30">
            <button type="submit"
                    class="w-full py-4 bg-primary text-on-primary rounded-full text-base font-bold shadow-lg hover:shadow-xl hover:opacity-90 active:scale-[0.98] transition-all uppercase tracking-wide">
                Registrar Niño
            </button>
        </div>
    </form>
</div>

{{-- Bottom Navigation Bar (Mobile) --}}
<nav class="md:hidden fixed bottom-0 left-0 w-full z-50 bg-surface shadow-[0_-2px_10px_rgba(0,0,0,0.05)] rounded-t-xl flex justify-around items-center h-16 border-t border-outline-variant/20">
    <a href="{{ route('gestor.dashboard') }}" class="flex flex-col items-center justify-center py-1 px-4 text-on-surface-variant">
        <span class="material-symbols-outlined text-xl">home</span>
        <span class="text-[10px] font-medium">Inicio</span>
    </a>
    <a href="{{ route('gestor.ninos.index') }}" class="flex flex-col items-center justify-center py-1 px-4 text-primary">
        <span class="material-symbols-outlined text-xl">child_care</span>
        <span class="text-[10px] font-medium">Niños</span>
    </a>
    <a href="#" class="flex flex-col items-center justify-center py-1 px-4 text-on-surface-variant">
        <span class="material-symbols-outlined text-xl">settings</span>
        <span class="text-[10px] font-medium">Ajustes</span>
    </a>
</nav>
@endsection
