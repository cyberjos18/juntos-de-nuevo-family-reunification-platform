@extends('layouts.app')

@section('title', 'Recogida #' . $recogida->id . ' - ' . $recogida->nino->nombre . ' ' . $recogida->nino->apellido)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 pb-28 md:pb-8">

    {{-- Back link --}}
    <a href="{{ route('admin.recogidas.index') }}" class="inline-flex items-center gap-1 text-sm text-on-surface-variant hover:text-primary transition-colors mb-4">
        <span class="material-symbols-outlined text-lg">arrow_back</span>
        Volver al listado
    </a>

    {{-- Header --}}
    <header class="mb-6">
        <h1 class="text-2xl sm:text-3xl font-bold text-on-surface">
            Detalle de Recogida <span class="text-on-surface-variant text-lg font-normal">#{{ $recogida->id }}</span>
        </h1>
        <p class="text-on-surface-variant text-sm mt-1">
            {{ $recogida->fecha_recogida->format('d/m/Y \a \l\a\s H:i') }}
        </p>
    </header>

    {{-- Main Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        {{-- Left Column: Photo --}}
        <div class="lg:col-span-5 flex flex-col gap-4">
            {{-- Foto de evidencia --}}
            <section class="bg-surface rounded-xl border border-outline-variant shadow-sm overflow-hidden">
                <div class="flex items-center gap-2 p-5 pb-0 text-primary">
                    <span class="material-symbols-outlined text-xl">photo_camera</span>
                    <h2 class="text-lg font-semibold">Foto de evidencia</h2>
                </div>
                <div class="p-5">
                    @if($recogida->foto_recogida_url)
                        <div class="aspect-[4/3] overflow-hidden rounded-lg shadow-md bg-surface-variant">
                            <img src="{{ asset('storage/' . $recogida->foto_recogida_url) }}"
                                 alt="Foto de recogida del niño {{ $recogida->nino->nombre }}"
                                 class="w-full h-full object-cover"
                                 loading="lazy">
                        </div>
                    @else
                        <div class="aspect-[4/3] rounded-lg border-2 border-dashed border-outline-variant flex flex-col items-center justify-center bg-surface-variant/30">
                            <span class="material-symbols-outlined text-6xl text-on-surface-variant/30 mb-2">photo_off</span>
                            <p class="text-sm text-on-surface-variant">Sin foto de evidencia</p>
                        </div>
                    @endif
                </div>
            </section>
        </div>

        {{-- Right Column: Details --}}
        <div class="lg:col-span-7 flex flex-col gap-4">

            {{-- Datos del Niño --}}
            <section class="bg-surface rounded-xl border border-outline-variant shadow-sm p-5">
                <div class="flex items-center gap-2 mb-4 text-primary">
                    <span class="material-symbols-outlined text-xl">child_care</span>
                    <h2 class="text-lg font-semibold">Datos del Niño</h2>
                </div>
                <div class="grid grid-cols-2 gap-y-4 gap-x-4">
                    <div>
                        <span class="block text-xs text-on-surface-variant uppercase tracking-wider mb-0.5">Nombre completo</span>
                        <span class="text-sm font-medium text-on-surface">{{ $recogida->nino->nombre }} {{ $recogida->nino->apellido }}</span>
                    </div>
                    <div>
                        <span class="block text-xs text-on-surface-variant uppercase tracking-wider mb-0.5">Edad</span>
                        <span class="text-sm font-medium text-on-surface">
                            @if($recogida->nino->edad)
                                {{ $recogida->nino->edad }} {{ $recogida->nino->edad === 1 ? 'año' : 'años' }}
                            @else
                                No registrada
                            @endif
                        </span>
                    </div>
                    <div>
                        <span class="block text-xs text-on-surface-variant uppercase tracking-wider mb-0.5">Centro</span>
                        <span class="text-sm font-medium text-on-surface">{{ $recogida->nino->centro->nombre ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="block text-xs text-on-surface-variant uppercase tracking-wider mb-0.5">Estado</span>
                        <x-badge-estado :estado="$recogida->nino->estado" />
                    </div>
                </div>

                {{-- Link al niño --}}
                <div class="mt-4 pt-4 border-t border-outline-variant/20">
                    <a href="{{ route('admin.ninos.show', $recogida->nino->id) }}"
                       class="inline-flex items-center gap-1 text-sm font-medium text-primary hover:text-primary/80 transition-colors">
                        <span class="material-symbols-outlined text-lg">open_in_new</span>
                        Ver ficha completa del niño
                    </a>
                </div>
            </section>

            {{-- Datos del Familiar --}}
            <section class="bg-surface rounded-xl border border-outline-variant shadow-sm p-5">
                <div class="flex items-center gap-2 mb-4 text-tertiary">
                    <span class="material-symbols-outlined text-xl">family_restroom</span>
                    <h2 class="text-lg font-semibold">Datos del Familiar</h2>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-4 gap-x-4">
                    <div>
                        <span class="block text-xs text-on-surface-variant uppercase tracking-wider mb-0.5">Nombre</span>
                        <span class="text-sm font-medium text-on-surface">{{ $recogida->familiar_nombre }}</span>
                    </div>
                    <div>
                        <span class="block text-xs text-on-surface-variant uppercase tracking-wider mb-0.5">Apellido</span>
                        <span class="text-sm font-medium text-on-surface">{{ $recogida->familiar_apellido }}</span>
                    </div>
                    <div>
                        <span class="block text-xs text-on-surface-variant uppercase tracking-wider mb-0.5">Parentesco</span>
                        <span class="text-sm font-medium text-on-surface">{{ $recogida->familiar_parentesco }}</span>
                    </div>
                    <div>
                        <span class="block text-xs text-on-surface-variant uppercase tracking-wider mb-0.5">Teléfono</span>
                        <span class="text-sm font-medium text-on-surface">
                            @if($recogida->familiar_telefono)
                                <a href="tel:{{ $recogida->familiar_telefono }}" class="hover:text-primary transition-colors">
                                    {{ $recogida->familiar_telefono }}
                                </a>
                            @else
                                No proporcionado
                            @endif
                        </span>
                    </div>
                    @if($recogida->familiar_email)
                        <div>
                            <span class="block text-xs text-on-surface-variant uppercase tracking-wider mb-0.5">Email</span>
                            <span class="text-sm font-medium text-on-surface">
                                <a href="mailto:{{ $recogida->familiar_email }}" class="hover:text-primary transition-colors break-all">
                                    {{ $recogida->familiar_email }}
                                </a>
                            </span>
                        </div>
                    @endif
                    @if($recogida->familiar_documento)
                        <div>
                            <span class="block text-xs text-on-surface-variant uppercase tracking-wider mb-0.5">Documento de identidad</span>
                            <span class="text-sm font-medium text-on-surface">{{ $recogida->familiar_documento }}</span>
                        </div>
                    @endif
                </div>
            </section>

            {{-- Metadata / Auditoría --}}
            <section class="bg-primary-container/20 rounded-xl border border-primary/20 p-5">
                <div class="flex items-center gap-2 mb-4 text-primary">
                    <span class="material-symbols-outlined text-xl">admin_panel_settings</span>
                    <h3 class="text-sm font-semibold">Información del registro</h3>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <span class="block uppercase tracking-wider text-xs text-on-surface-variant">Fecha y hora exacta</span>
                        <span class="font-medium text-on-surface text-sm">{{ $recogida->fecha_recogida->format('d/m/Y H:i:s') }}</span>
                    </div>
                    <div>
                        <span class="block uppercase tracking-wider text-xs text-on-surface-variant">Registrado por</span>
                        <span class="font-medium text-on-surface text-sm">{{ $recogida->registradoPor->nombre ?? 'Sistema' }}</span>
                    </div>
                    <div>
                        <span class="block uppercase tracking-wider text-xs text-on-surface-variant">ID de recogida</span>
                        <span class="font-medium text-on-surface text-sm">#{{ $recogida->id }}</span>
                    </div>
                    <div>
                        <span class="block uppercase tracking-wider text-xs text-on-surface-variant">Foto de evidencia</span>
                        <span class="font-medium text-sm {{ $recogida->foto_recogida_url ? 'text-success' : 'text-on-surface-variant' }}">
                            {{ $recogida->foto_recogida_url ? 'Disponible' : 'No disponible' }}
                        </span>
                    </div>
                </div>
            </section>

            {{-- Notas adicionales --}}
            @if($recogida->notas)
                <section class="bg-surface rounded-xl border border-outline-variant shadow-sm p-5">
                    <div class="flex items-center gap-2 mb-3 text-primary">
                        <span class="material-symbols-outlined text-xl">notes</span>
                        <h2 class="text-lg font-semibold">Notas adicionales</h2>
                    </div>
                    <p class="text-sm text-on-surface leading-relaxed whitespace-pre-line">{{ $recogida->notas }}</p>
                </section>
            @endif
        </div>
    </div>
</div>

{{-- Bottom Navigation Bar (Mobile) --}}
<nav class="md:hidden fixed bottom-0 left-0 w-full z-50 bg-surface shadow-[0_-2px_10px_rgba(0,0,0,0.05)] rounded-t-xl flex justify-around items-center h-16 border-t border-outline-variant/20">
    <a href="{{ route('admin.dashboard') }}"
       class="flex flex-col items-center justify-center py-1 px-4 {{ request()->routeIs('admin.dashboard') ? 'text-primary' : 'text-on-surface-variant' }}">
        <span class="material-symbols-outlined text-xl">analytics</span>
        <span class="text-[10px] font-medium">Stats</span>
    </a>
    <a href="{{ route('admin.centros.index') }}"
       class="flex flex-col items-center justify-center py-1 px-4 {{ request()->routeIs('admin.centros.*') ? 'text-primary' : 'text-on-surface-variant' }}">
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
