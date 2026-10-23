@extends('layouts.app')

@section('title', 'Niños del Centro')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 pb-28 md:pb-8 space-y-6"
     x-data="{ filtro: '{{ $estado ?? '' }}' }">

    {{-- Header --}}
    <header class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-on-surface">Niños del centro</h1>
            <p class="text-sm text-on-surface-variant mt-1">
                {{ $ninos->total() }} {{ $ninos->total() === 1 ? 'registro' : 'registros' }}
            </p>
        </div>
        <a href="{{ route('gestor.ninos.create') }}"
           class="hidden sm:inline-flex items-center gap-2 px-5 py-2.5 bg-primary text-on-primary rounded-full text-sm font-medium hover:opacity-90 transition-opacity shadow-sm">
            <span class="material-symbols-outlined text-lg">add</span>
            Registrar Niño
        </a>
    </header>

    {{-- Chips de filtro --}}
    <div class="flex gap-2 overflow-x-auto pb-2 scrollbar-hide">
        <a href="{{ route('gestor.ninos.index') }}"
           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap transition-colors
                  {{ !$estado ? 'bg-primary text-on-primary' : 'bg-surface-variant text-on-surface-variant hover:bg-outline-variant/40' }}">
            <span class="material-symbols-outlined text-base">apps</span>
            Todos
        </a>
        <a href="{{ route('gestor.ninos.index', ['estado' => 'encontrado']) }}"
           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap transition-colors
                  {{ $estado === 'encontrado' ? 'bg-primary text-on-primary' : 'bg-surface-variant text-on-surface-variant hover:bg-outline-variant/40' }}">
            <span class="material-symbols-outlined text-base">person_search</span>
            Encontrados
        </a>
        <a href="{{ route('gestor.ninos.index', ['estado' => 'familia_localizada']) }}"
           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap transition-colors
                  {{ $estado === 'familia_localizada' ? 'bg-primary text-on-primary' : 'bg-surface-variant text-on-surface-variant hover:bg-outline-variant/40' }}">
            <span class="material-symbols-outlined text-base">family_restroom</span>
            Familia localizada
        </a>
        <a href="{{ route('gestor.ninos.index', ['estado' => 'recogido']) }}"
           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap transition-colors
                  {{ $estado === 'recogido' ? 'bg-primary text-on-primary' : 'bg-surface-variant text-on-surface-variant hover:bg-outline-variant/40' }}">
            <span class="material-symbols-outlined text-base">check_circle</span>
            Recogidos
        </a>
    </div>

    {{-- Grid de niños --}}
    @if($ninos->isEmpty())
        <div class="bg-surface rounded-xl border border-outline-variant p-12 text-center">
            <span class="material-symbols-outlined text-6xl text-on-surface-variant/30 mb-4 block">child_care</span>
            <h3 class="text-lg font-medium text-on-surface mb-2">No se encontraron niños</h3>
            <p class="text-on-surface-variant text-sm mb-4">
                @if($estado)
                    No hay niños con estado "{{ $estado }}" en tu centro.
                @else
                    Aún no hay niños registrados en tu centro.
                @endif
            </p>
            <a href="{{ route('gestor.ninos.create') }}"
               class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-on-primary rounded-full text-sm font-medium hover:opacity-90 transition-opacity">
                <span class="material-symbols-outlined text-lg">add</span>
                Registrar niño
            </a>
        </div>
    @else
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($ninos as $nino)
                <a href="{{ route('gestor.ninos.show', $nino->id) }}"
                   class="block bg-surface rounded-xl shadow-sm border border-outline-variant overflow-hidden hover:shadow-md transition-shadow group">
                    {{-- Foto --}}
                    <div class="aspect-square bg-primary-container relative overflow-hidden">
                        @if($nino->foto_url)
                            <img src="{{ asset('storage/' . $nino->foto_url) }}"
                                 alt="Foto de {{ $nino->nombre }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <span class="material-symbols-outlined text-4xl text-primary/40">child_care</span>
                            </div>
                        @endif
                    </div>

                    {{-- Info --}}
                    <div class="p-3 space-y-1">
                        <h3 class="text-sm font-medium text-on-surface truncate">{{ $nino->nombre }} {{ $nino->apellido }}</h3>
                        @if($nino->edad)
                            <p class="text-xs text-on-surface-variant">{{ $nino->edad }} {{ $nino->edad === 1 ? 'año' : 'años' }}</p>
                        @endif
                        <div class="pt-1">
                            <x-badge-estado :estado="$nino->estado" />
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        {{-- Paginación --}}
        @if($ninos->hasPages())
            <div class="pt-4">
                {{ $ninos->withQueryString()->links() }}
            </div>
        @endif
    @endif
</div>

{{-- FAB móvil --}}
<a href="{{ route('gestor.ninos.create') }}"
   class="sm:hidden fixed bottom-24 right-6 z-40 flex items-center gap-2 px-5 py-3.5 bg-primary text-on-primary rounded-2xl shadow-xl hover:shadow-2xl active:scale-95 transition-all">
    <span class="material-symbols-outlined">add</span>
    <span class="text-sm font-bold pr-1">Registrar</span>
</a>

{{-- Bottom Navigation Bar (Mobile) --}}
<nav class="md:hidden fixed bottom-0 left-0 w-full z-50 bg-surface shadow-[0_-2px_10px_rgba(0,0,0,0.05)] rounded-t-xl flex justify-around items-center h-16 border-t border-outline-variant/20">
    <a href="{{ route('gestor.dashboard') }}"
       class="flex flex-col items-center justify-center py-1 px-4 text-on-surface-variant">
        <span class="material-symbols-outlined text-xl">home</span>
        <span class="text-[10px] font-medium">Inicio</span>
    </a>
    <a href="{{ route('gestor.ninos.index') }}"
       class="flex flex-col items-center justify-center py-1 px-4 text-primary">
        <span class="material-symbols-outlined text-xl">child_care</span>
        <span class="text-[10px] font-medium">Niños</span>
    </a>
    <a href="#"
       class="flex flex-col items-center justify-center py-1 px-4 text-on-surface-variant">
        <span class="material-symbols-outlined text-xl">settings</span>
        <span class="text-[10px] font-medium">Ajustes</span>
    </a>
</nav>
@endsection
