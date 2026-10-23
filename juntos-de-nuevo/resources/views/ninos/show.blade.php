@extends('layouts.app')

@section('title', 'Ficha de ' . $nino->nombre . ' ' . $nino->apellido)

@section('content')
    <div class="max-w-3xl mx-auto">
        {{-- Back button --}}
        <div class="px-4 sm:px-6 py-3">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-1 text-sm text-on-surface-variant hover:text-primary transition-colors">
                <span class="material-symbols-outlined text-lg">arrow_back</span>
                Volver al listado
            </a>
        </div>

        {{-- Foto grande --}}
        <div class="mx-4 sm:mx-6 rounded-2xl overflow-hidden bg-primary-container aspect-[4/3] relative">
            @if($nino->foto_url)
                <img src="{{ asset('storage/' . $nino->foto_url) }}" alt="Foto de {{ $nino->nombre }}" class="w-full h-full object-cover">
            @else
                <div class="w-full h-full flex items-center justify-center">
                    <span class="material-symbols-outlined text-8xl text-primary/30">child_care</span>
                </div>
            @endif
        </div>

        {{-- Nombre + Badge --}}
        <div class="px-4 sm:px-6 pt-6 pb-4">
            <h1 class="text-2xl sm:text-3xl font-bold text-on-surface mb-2">
                {{ $nino->nombre }} {{ $nino->apellido }}
            </h1>
            <x-badge-estado :estado="$nino->estado" />
        </div>

        {{-- Datos básicos --}}
        <div class="px-4 sm:px-6 pb-4">
            <div class="bg-surface-variant rounded-xl p-4 space-y-3">
                <h2 class="text-sm font-medium text-on-surface-variant uppercase tracking-wider">Información básica</h2>
                <div class="space-y-2">
                    @if($nino->edad)
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-secondary text-xl">person</span>
                            <span class="text-sm text-on-surface">{{ $nino->edad }} {{ $nino->edad === 1 ? 'año' : 'años' }}</span>
                        </div>
                    @endif
                    @if($nino->fecha_nacimiento)
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-secondary text-xl">calendar_today</span>
                            <span class="text-sm text-on-surface">{{ $nino->fecha_nacimiento->format('d/m/Y') }}</span>
                        </div>
                    @endif
                    @if($nino->sexo)
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-secondary text-xl">{{ $nino->sexo === 'M' ? 'boy' : 'girl' }}</span>
                            <span class="text-sm text-on-surface">{{ $nino->sexo === 'M' ? 'Masculino' : 'Femenino' }}</span>
                        </div>
                    @endif
                    @if($nino->centro)
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-secondary text-xl">location_on</span>
                            <span class="text-sm text-on-surface">{{ $nino->centro->nombre }}</span>
                        </div>
                    @endif
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-secondary text-xl">schedule</span>
                        <span class="text-sm text-on-surface">Registrado: {{ $nino->created_at->format('d/m/Y') }}</span>
                    </div>
                </div>
            </div>
        </div>

        @auth
            {{-- Datos familiares (solo autenticados) --}}
            <div class="px-4 sm:px-6 pb-4">
                <div class="bg-surface rounded-xl border border-outline-variant p-4 space-y-3">
                    <h2 class="text-sm font-medium text-on-surface-variant uppercase tracking-wider">Información familiar</h2>
                    <div class="space-y-2">
                        @if($nino->nombre_madre)
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-outlined text-secondary text-xl">female</span>
                                <span class="text-sm text-on-surface"><strong>Madre:</strong> {{ $nino->nombre_madre }}</span>
                            </div>
                        @endif
                        @if($nino->nombre_padre)
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-outlined text-secondary text-xl">male</span>
                                <span class="text-sm text-on-surface"><strong>Padre:</strong> {{ $nino->nombre_padre }}</span>
                            </div>
                        @endif
                        @if($nino->direccion_previa)
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-outlined text-secondary text-xl">home</span>
                                <span class="text-sm text-on-surface"><strong>Zona:</strong> {{ $nino->direccion_previa }}</span>
                            </div>
                        @endif
                        @if(!$nino->nombre_madre && !$nino->nombre_padre && !$nino->direccion_previa)
                            <p class="text-sm text-on-surface-variant italic">Sin información familiar disponible</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Familiares conocidos (solo autenticados) --}}
            @if($nino->familiares->count() > 0)
                <div class="px-4 sm:px-6 pb-4">
                    <div class="bg-surface rounded-xl border border-outline-variant p-4 space-y-3">
                        <h2 class="text-sm font-medium text-on-surface-variant uppercase tracking-wider">Familiares conocidos</h2>
                        <div class="space-y-3">
                            @foreach($nino->familiares as $familiar)
                                <div class="flex items-start gap-3 p-3 bg-surface-variant rounded-lg">
                                    <span class="material-symbols-outlined text-secondary text-xl mt-0.5">family_restroom</span>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-on-surface">{{ $familiar->parentesco }} — {{ $familiar->nombre }} {{ $familiar->apellido }}</p>
                                        @if($familiar->telefono)
                                            <a href="tel:{{ $familiar->telefono }}" class="inline-flex items-center gap-1 text-sm text-primary hover:underline mt-1">
                                                <span class="material-symbols-outlined text-base">phone</span>
                                                {{ $familiar->telefono }}
                                            </a>
                                        @endif
                                        @if($familiar->notas)
                                            <p class="text-xs text-on-surface-variant mt-1">{{ $familiar->notas }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            {{-- Contacto del centro (solo autenticados) --}}
            @if($nino->centro)
                <div class="px-4 sm:px-6 pb-4">
                    <div class="bg-surface rounded-xl border border-outline-variant p-4 space-y-3">
                        <h2 class="text-sm font-medium text-on-surface-variant uppercase tracking-wider">Contacto del centro</h2>
                        <div class="space-y-2">
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-outlined text-secondary text-xl">apartment</span>
                                <span class="text-sm font-medium text-on-surface">{{ $nino->centro->nombre }}</span>
                            </div>
                            @if($nino->centro->direccion)
                                <div class="flex items-center gap-3">
                                    <span class="material-symbols-outlined text-secondary text-xl">location_on</span>
                                    <span class="text-sm text-on-surface">{{ $nino->centro->direccion }}</span>
                                </div>
                            @endif
                            @if($nino->centro->telefono)
                                <div class="flex items-center gap-3">
                                    <span class="material-symbols-outlined text-secondary text-xl">phone</span>
                                    <a href="tel:{{ $nino->centro->telefono }}" class="text-sm text-primary hover:underline">{{ $nino->centro->telefono }}</a>
                                </div>
                            @endif
                            @if($nino->centro->email)
                                <div class="flex items-center gap-3">
                                    <span class="material-symbols-outlined text-secondary text-xl">mail</span>
                                    <a href="mailto:{{ $nino->centro->email }}" class="text-sm text-primary hover:underline">{{ $nino->centro->email }}</a>
                                </div>
                            @endif
                        </div>
                        {{-- Botones de acción --}}
                        <div class="flex flex-wrap gap-2 pt-2">
                            @if($nino->centro->telefono)
                                <a href="tel:{{ $nino->centro->telefono }}" class="inline-flex items-center gap-2 bg-secondary-container text-on-secondary-container px-4 py-2 rounded-full text-sm font-medium hover:opacity-90 transition-opacity">
                                    <span class="material-symbols-outlined text-base">phone</span>
                                    Llamar
                                </a>
                            @endif
                            @if($nino->centro->email)
                                <a href="mailto:{{ $nino->centro->email }}" class="inline-flex items-center gap-2 bg-secondary-container text-on-secondary-container px-4 py-2 rounded-full text-sm font-medium hover:opacity-90 transition-opacity">
                                    <span class="material-symbols-outlined text-base">mail</span>
                                    Email
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        @endauth
    </div>
@endsection
