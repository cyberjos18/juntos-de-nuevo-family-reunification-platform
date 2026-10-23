@extends('layouts.app')

@section('title', 'Ficha de ' . $nino->nombre)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 pb-28 md:pb-8">

    {{-- Back link --}}
    <a href="{{ route('gestor.ninos.index') }}" class="inline-flex items-center gap-1 text-sm text-on-surface-variant hover:text-primary transition-colors mb-4">
        <span class="material-symbols-outlined text-lg">arrow_back</span>
        Volver al listado
    </a>

    {{-- Bento Grid Layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        {{-- Left Column: Photo + Identity --}}
        <div class="lg:col-span-5 flex flex-col gap-4">
            {{-- Foto grande --}}
            <div class="relative aspect-[4/3] overflow-hidden rounded-xl shadow-md bg-primary-container">
                @if($nino->foto_url)
                    <img src="{{ asset('storage/' . $nino->foto_url) }}"
                         alt="Foto de {{ $nino->nombre }}"
                         class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center">
                        <span class="material-symbols-outlined text-7xl text-primary/30">child_care</span>
                    </div>
                @endif
                {{-- Badge de estado --}}
                <div class="absolute top-4 right-4">
                    <x-badge-estado :estado="$nino->estado" />
                </div>
            </div>

            {{-- Nombre (desktop) --}}
            <div class="hidden lg:block">
                <h1 class="text-2xl font-bold text-on-surface">{{ $nino->nombre }} {{ $nino->apellido }}</h1>
                <p class="text-sm text-on-surface-variant mt-1">ID: #{{ $nino->id }} · Registrado {{ $nino->created_at->format('d/m/Y') }}</p>
            </div>
        </div>

        {{-- Right Column: Details --}}
        <div class="lg:col-span-7 flex flex-col gap-4">

            {{-- Nombre (mobile) --}}
            <div class="lg:hidden">
                <h1 class="text-2xl font-bold text-on-surface">{{ $nino->nombre }} {{ $nino->apellido }}</h1>
                <p class="text-sm text-on-surface-variant mt-1">ID: #{{ $nino->id }} · Registrado {{ $nino->created_at->format('d/m/Y') }}</p>
            </div>

            {{-- Información básica --}}
            <section class="bg-surface rounded-xl border border-outline-variant shadow-sm p-5">
                <div class="flex items-center gap-2 mb-4 text-primary">
                    <span class="material-symbols-outlined text-xl">info</span>
                    <h2 class="text-lg font-semibold">Información básica</h2>
                </div>
                <div class="grid grid-cols-2 gap-y-4 gap-x-4">
                    <div>
                        <span class="block text-xs text-on-surface-variant uppercase tracking-wider mb-0.5">Edad</span>
                        <span class="text-sm font-medium text-on-surface">
                            {{ $nino->edad ? $nino->edad . ' ' . ($nino->edad === 1 ? 'año' : 'años') : 'No registrada' }}
                        </span>
                    </div>
                    <div>
                        <span class="block text-xs text-on-surface-variant uppercase tracking-wider mb-0.5">Fecha de nacimiento</span>
                        <span class="text-sm font-medium text-on-surface">
                            {{ $nino->fecha_nacimiento ? $nino->fecha_nacimiento->format('d/m/Y') : 'No registrada' }}
                        </span>
                    </div>
                    <div>
                        <span class="block text-xs text-on-surface-variant uppercase tracking-wider mb-0.5">Sexo</span>
                        <span class="text-sm font-medium text-on-surface">
                            @if($nino->sexo === 'M') Masculino @elseif($nino->sexo === 'F') Femenino @else No especificado @endif
                        </span>
                    </div>
                    <div>
                        <span class="block text-xs text-on-surface-variant uppercase tracking-wider mb-0.5">Centro</span>
                        <span class="text-sm font-medium text-on-surface">{{ $nino->centro->nombre ?? 'N/A' }}</span>
                    </div>
                    <div class="col-span-2">
                        <span class="block text-xs text-on-surface-variant uppercase tracking-wider mb-0.5">Dirección previa</span>
                        <span class="text-sm font-medium text-on-surface">{{ $nino->direccion_previa ?? 'No registrada' }}</span>
                    </div>
                </div>
            </section>

            {{-- Datos familiares --}}
            <section class="bg-surface rounded-xl border border-outline-variant shadow-sm p-5">
                <div class="flex items-center gap-2 mb-4 text-primary">
                    <span class="material-symbols-outlined text-xl">family_restroom</span>
                    <h2 class="text-lg font-semibold">Datos familiares</h2>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <span class="block text-xs text-on-surface-variant uppercase tracking-wider mb-0.5">Nombre de la madre</span>
                        <span class="text-sm font-medium text-on-surface">{{ $nino->nombre_madre ?? 'No registrado' }}</span>
                    </div>
                    <div>
                        <span class="block text-xs text-on-surface-variant uppercase tracking-wider mb-0.5">Nombre del padre</span>
                        <span class="text-sm font-medium text-on-surface">{{ $nino->nombre_padre ?? 'No registrado' }}</span>
                    </div>
                </div>

                {{-- Familiares conocidos --}}
                @if($nino->familiares && $nino->familiares->count() > 0)
                    <div class="mt-4 pt-4 border-t border-outline-variant">
                        <h3 class="text-sm font-semibold text-on-surface mb-3">Familiares registrados</h3>
                        <div class="space-y-2">
                            @foreach($nino->familiares as $familiar)
                                <div class="flex items-center gap-3 p-3 bg-surface-variant/30 rounded-lg">
                                    <span class="material-symbols-outlined text-lg text-secondary">person</span>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-on-surface">
                                            {{ $familiar->nombre }} {{ $familiar->apellido }}
                                        </p>
                                        <p class="text-xs text-on-surface-variant">
                                            {{ $familiar->parentesco }}
                                            @if($familiar->telefono)
                                                · {{ $familiar->telefono }}
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <p class="text-sm text-on-surface-variant mt-4 italic">No hay familiares registrados.</p>
                @endif
            </section>

            {{-- Notas --}}
            @if($nino->notas)
                <section class="bg-surface rounded-xl border border-outline-variant shadow-sm p-5">
                    <div class="flex items-center gap-2 mb-3 text-primary">
                        <span class="material-symbols-outlined text-xl">notes</span>
                        <h2 class="text-lg font-semibold">Notas</h2>
                    </div>
                    <p class="text-sm text-on-surface leading-relaxed">{{ $nino->notas }}</p>
                </section>
            @endif

            {{-- Datos de auditoría --}}
            <section class="bg-surface-variant/20 rounded-xl border border-outline-variant/50 p-4">
                <div class="flex items-center gap-2 mb-2 text-on-surface-variant">
                    <span class="material-symbols-outlined text-lg">schedule</span>
                    <h3 class="text-sm font-semibold">Datos de registro</h3>
                </div>
                <div class="grid grid-cols-2 gap-2 text-xs text-on-surface-variant">
                    <div>
                        <span class="block uppercase tracking-wider">Registrado por</span>
                        <span class="font-medium text-on-surface">{{ $nino->creadoPor->nombre ?? 'Sistema' }}</span>
                    </div>
                    <div>
                        <span class="block uppercase tracking-wider">Fecha de registro</span>
                        <span class="font-medium text-on-surface">{{ $nino->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
            </section>

            {{-- Botones de acción --}}
            <div class="flex flex-col sm:flex-row gap-3 pt-2">
                {{-- Registrar recogida (solo si puede ser recogido) --}}
                @if($nino->puedeSerRecogido())
                    <a href="{{ route('gestor.recogidas.create', $nino->id) }}"
                       class="flex-1 flex items-center justify-center gap-2 px-6 py-3 bg-tertiary text-on-tertiary rounded-full text-sm font-bold shadow-md hover:opacity-90 active:scale-[0.98] transition-all">
                        <span class="material-symbols-outlined text-lg">task_alt</span>
                        Registrar recogida
                    </a>
                @endif

                {{-- Editar datos (futuro) --}}
                <button disabled
                        class="flex-1 flex items-center justify-center gap-2 px-6 py-3 bg-surface-variant text-on-surface-variant rounded-full text-sm font-medium opacity-60 cursor-not-allowed">
                    <span class="material-symbols-outlined text-lg">edit</span>
                    Editar datos
                </button>
            </div>

            {{-- Datos de recogida (si ya fue recogido) --}}
            @if($nino->estaRecogido() && $nino->recogida)
                <section class="bg-tertiary-container/30 rounded-xl border border-tertiary/20 p-5 mt-2">
                    <div class="flex items-center gap-2 mb-4 text-tertiary">
                        <span class="material-symbols-outlined text-xl">check_circle</span>
                        <h2 class="text-lg font-semibold">Datos de la recogida</h2>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <span class="block text-xs text-on-surface-variant uppercase tracking-wider mb-0.5">Familiar</span>
                            <span class="text-sm font-medium text-on-surface">
                                {{ $nino->recogida->familiar_nombre }} {{ $nino->recogida->familiar_apellido }}
                            </span>
                        </div>
                        <div>
                            <span class="block text-xs text-on-surface-variant uppercase tracking-wider mb-0.5">Parentesco</span>
                            <span class="text-sm font-medium text-on-surface">{{ $nino->recogida->familiar_parentesco }}</span>
                        </div>
                        <div>
                            <span class="block text-xs text-on-surface-variant uppercase tracking-wider mb-0.5">Teléfono</span>
                            <span class="text-sm font-medium text-on-surface">{{ $nino->recogida->familiar_telefono }}</span>
                        </div>
                        <div>
                            <span class="block text-xs text-on-surface-variant uppercase tracking-wider mb-0.5">Fecha de recogida</span>
                            <span class="text-sm font-medium text-on-surface">{{ $nino->recogida->fecha_recogida->format('d/m/Y H:i') }}</span>
                        </div>
                        @if($nino->recogida->familiar_email)
                            <div>
                                <span class="block text-xs text-on-surface-variant uppercase tracking-wider mb-0.5">Email</span>
                                <span class="text-sm font-medium text-on-surface">{{ $nino->recogida->familiar_email }}</span>
                            </div>
                        @endif
                        @if($nino->recogida->familiar_documento)
                            <div>
                                <span class="block text-xs text-on-surface-variant uppercase tracking-wider mb-0.5">Documento</span>
                                <span class="text-sm font-medium text-on-surface">{{ $nino->recogida->familiar_documento }}</span>
                            </div>
                        @endif
                        @if($nino->recogida->registradoPor)
                            <div>
                                <span class="block text-xs text-on-surface-variant uppercase tracking-wider mb-0.5">Registrado por</span>
                                <span class="text-sm font-medium text-on-surface">{{ $nino->recogida->registradoPor->nombre }}</span>
                            </div>
                        @endif
                    </div>

                    {{-- Foto de recogida --}}
                    @if($nino->recogida->foto_recogida_url)
                        <div class="mt-4">
                            <span class="block text-xs text-on-surface-variant uppercase tracking-wider mb-2">Foto de recogida</span>
                            <img src="{{ asset('storage/' . $nino->recogida->foto_recogida_url) }}"
                                 alt="Foto de recogida"
                                 class="w-full max-w-sm rounded-lg shadow-sm">
                        </div>
                    @endif

                    @if($nino->recogida->notas)
                        <div class="mt-4">
                            <span class="block text-xs text-on-surface-variant uppercase tracking-wider mb-1">Observaciones</span>
                            <p class="text-sm text-on-surface">{{ $nino->recogida->notas }}</p>
                        </div>
                    @endif
                </section>
            @endif
        </div>
    </div>
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
