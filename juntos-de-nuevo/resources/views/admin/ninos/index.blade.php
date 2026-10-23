@extends('layouts.app')

@section('title', 'Niños registrados')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 pb-28 md:pb-8 space-y-6"
     x-data="{
         confirmarEliminar: false,
         ninoAEliminar: null,
         ninoNombre: ''
     }">

    {{-- Header --}}
    <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-on-surface">
                @if($estado)
                    Niños — {{ str_replace('_', ' ', ucfirst($estado)) }}
                @else
                    Niños registrados
                @endif
            </h1>
            <p class="text-on-surface-variant text-sm mt-1">
                @if($estado)
                    Mostrando {{ $ninos->total() }} {{ $ninos->total() === 1 ? 'niño con estado' : 'niños con estado' }}: {{ $estado }}
                @else
                    Todos los niños registrados en el sistema
                @endif
            </p>
        </div>
        @if($estado)
            <a href="{{ route('admin.ninos.index') }}"
               class="inline-flex items-center gap-1 px-4 py-2 bg-surface border border-outline-variant rounded-lg text-sm font-medium text-on-surface hover:bg-surface-variant transition-colors">
                <span class="material-symbols-outlined text-base">filter_list_off</span>
                Ver todos
            </a>
        @endif
    </header>

    {{-- Filtros de estado --}}
    <div class="flex items-center border-b border-outline-variant overflow-x-auto">
        <a href="{{ route('admin.ninos.index') }}"
           class="px-4 py-3 text-sm font-medium flex items-center gap-2 transition-colors whitespace-nowrap
                  {{ !$estado ? 'text-primary border-b-2 border-primary' : 'text-on-surface-variant hover:text-on-surface' }}">
            Todos
            <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-surface-variant text-on-surface-variant">
                {{ $ninos->total() }}
            </span>
        </a>
        <a href="{{ route('admin.ninos.index', ['estado' => 'encontrado']) }}"
           class="px-4 py-3 text-sm font-medium flex items-center gap-2 transition-colors whitespace-nowrap
                  {{ $estado === 'encontrado' ? 'text-primary border-b-2 border-primary' : 'text-on-surface-variant hover:text-on-surface' }}">
            Encontrados
        </a>
        <a href="{{ route('admin.ninos.index', ['estado' => 'recogido']) }}"
           class="px-4 py-3 text-sm font-medium flex items-center gap-2 transition-colors whitespace-nowrap
                  {{ $estado === 'recogido' ? 'text-primary border-b-2 border-primary' : 'text-on-surface-variant hover:text-on-surface' }}">
            Recogidos
        </a>
        <a href="{{ route('admin.ninos.index', ['estado' => 'familia_localizada']) }}"
           class="px-4 py-3 text-sm font-medium flex items-center gap-2 transition-colors whitespace-nowrap
                  {{ $estado === 'familia_localizada' ? 'text-primary border-b-2 border-primary' : 'text-on-surface-variant hover:text-on-surface' }}">
            Familia localizada
        </a>
    </div>

    {{-- Lista de niños --}}
    @if($ninos->isEmpty())
        <div class="bg-surface rounded-xl border border-outline-variant p-12 text-center">
            <span class="material-symbols-outlined text-5xl text-on-surface-variant/30 mb-3 block">child_care</span>
            <p class="text-on-surface-variant">
                @if($estado)
                    No hay niños con estado "{{ $estado }}".
                @else
                    No hay niños registrados aún.
                @endif
            </p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @foreach($ninos as $nino)
                <div class="bg-surface rounded-xl shadow-sm border border-outline-variant overflow-hidden hover:shadow-md transition-shadow group">
                    {{-- Foto --}}
                    <a href="{{ route('admin.ninos.show', $nino) }}" class="block aspect-square bg-primary-container relative overflow-hidden">
                        @if($nino->foto_url)
                            <img src="{{ asset('storage/' . $nino->foto_url) }}" alt="Foto de {{ $nino->nombre }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <span class="material-symbols-outlined text-5xl text-primary/40">child_care</span>
                            </div>
                        @endif
                    </a>

                    {{-- Info --}}
                    <div class="p-3 space-y-1">
                        <a href="{{ route('admin.ninos.show', $nino) }}" class="block">
                            <h3 class="text-sm font-medium text-on-surface truncate">{{ $nino->nombre }} {{ $nino->apellido }}</h3>
                        </a>
                        @if($nino->edad)
                            <p class="text-xs text-on-surface-variant">{{ $nino->edad }} {{ $nino->edad === 1 ? 'año' : 'años' }}</p>
                        @endif
                        @if($nino->centro)
                            <p class="text-xs text-secondary truncate">{{ $nino->centro->nombre }}</p>
                        @endif
                        <div class="pt-1">
                            <x-badge-estado :estado="$nino->estado" />
                        </div>
                        <p class="text-xs text-on-surface-variant mt-1">
                            {{ $nino->created_at->format('d/m/Y') }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Paginación --}}
        @if($ninos->hasPages())
            <div class="mt-6">
                {{ $ninos->links() }}
            </div>
        @endif
    @endif
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
