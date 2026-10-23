@extends('layouts.app')

@section('title', 'Dashboard Gestor')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 pb-28 md:pb-8 space-y-8">

    {{-- Header: Saludo personalizado --}}
    <header>
        <h1 class="text-2xl sm:text-3xl font-bold text-on-surface">
            Bienvenido, {{ auth()->user()->nombre }}
        </h1>
        <p class="text-on-surface-variant text-sm mt-1 flex items-center gap-1">
            <span class="material-symbols-outlined text-base">apartment</span>
            {{ $centro->nombre ?? 'Mi Centro' }}
        </p>
    </header>

    {{-- Stats Cards (2x2 grid) --}}
    <section class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-surface rounded-xl border border-outline-variant/20 p-4 flex flex-col gap-1 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="p-2 bg-primary-container/30 text-primary rounded-lg">
                    <span class="material-symbols-outlined text-xl">person_search</span>
                </span>
            </div>
            <span class="text-3xl font-bold text-on-surface mt-2">{{ $stats['activos'] }}</span>
            <span class="text-sm text-on-surface-variant">Niños activos</span>
        </div>

        <div class="bg-surface rounded-xl border border-outline-variant/20 p-4 flex flex-col gap-1 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="p-2 bg-tertiary-container/30 text-tertiary rounded-lg">
                    <span class="material-symbols-outlined text-xl">check_circle</span>
                </span>
            </div>
            <span class="text-3xl font-bold text-on-surface mt-2">{{ $stats['recogidos'] }}</span>
            <span class="text-sm text-on-surface-variant">Recogidos</span>
        </div>

        <div class="bg-surface rounded-xl border border-outline-variant/20 p-4 flex flex-col gap-1 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="p-2 bg-secondary-container/50 text-secondary rounded-lg">
                    <span class="material-symbols-outlined text-xl">family_restroom</span>
                </span>
            </div>
            <span class="text-3xl font-bold text-on-surface mt-2">{{ $stats['familia_localizada'] }}</span>
            <span class="text-sm text-on-surface-variant">Familia localizada</span>
        </div>

        <div class="bg-surface rounded-xl border border-outline-variant/20 p-4 flex flex-col gap-1 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="p-2 bg-surface-variant text-on-surface-variant rounded-lg">
                    <span class="material-symbols-outlined text-xl">list_alt</span>
                </span>
            </div>
            <span class="text-3xl font-bold text-on-surface mt-2">{{ $stats['total'] }}</span>
            <span class="text-sm text-on-surface-variant">Total registros</span>
        </div>
    </section>

    {{-- Últimos niños del centro --}}
    <section>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-on-surface">Mis niños recientes</h2>
            <a href="{{ route('gestor.ninos.index') }}" class="text-primary text-sm font-medium hover:underline transition-all">
                Ver todos
            </a>
        </div>

        @if($ultimosNinos->isEmpty())
            <div class="bg-surface rounded-xl border border-outline-variant p-8 text-center">
                <span class="material-symbols-outlined text-5xl text-on-surface-variant/30 mb-3 block">child_care</span>
                <p class="text-on-surface-variant">Aún no hay niños registrados en tu centro.</p>
                <a href="{{ route('gestor.ninos.create') }}" class="inline-flex items-center gap-2 mt-4 px-6 py-3 bg-primary text-on-primary rounded-full text-sm font-medium hover:opacity-90 transition-opacity">
                    <span class="material-symbols-outlined text-lg">add</span>
                    Registrar primer niño
                </a>
            </div>
        @else
            <div class="space-y-2">
                @foreach($ultimosNinos as $nino)
                    <a href="{{ route('gestor.ninos.show', $nino->id) }}"
                       class="group flex items-center gap-4 p-3 bg-surface hover:bg-surface-variant/50 rounded-xl border border-outline-variant/10 transition-all">
                        {{-- Thumbnail --}}
                        <div class="w-14 h-14 rounded-lg overflow-hidden flex-shrink-0 bg-primary-container">
                            @if($nino->foto_url)
                                <img src="{{ asset('storage/' . $nino->foto_url) }}"
                                     alt="Foto de {{ $nino->nombre }}"
                                     class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <span class="material-symbols-outlined text-2xl text-primary/40">child_care</span>
                                </div>
                            @endif
                        </div>

                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <p class="text-base font-medium text-on-surface truncate">
                                {{ $nino->nombre }} {{ $nino->apellido }}
                            </p>
                            <p class="text-sm text-on-surface-variant">
                                @if($nino->edad)
                                    {{ $nino->edad }} {{ $nino->edad === 1 ? 'año' : 'años' }}
                                @endif
                                · {{ $nino->created_at->diffForHumans() }}
                            </p>
                        </div>

                        {{-- Badge + Arrow --}}
                        <div class="hidden sm:flex flex-col items-end gap-1">
                            <x-badge-estado :estado="$nino->estado" />
                        </div>
                        <span class="material-symbols-outlined text-outline group-hover:text-primary transition-colors">chevron_right</span>
                    </a>
                @endforeach
            </div>
        @endif
    </section>
</div>

{{-- FAB: Registrar Niño (Extended) --}}
<a href="{{ route('gestor.ninos.create') }}"
   class="fixed bottom-24 md:bottom-8 right-6 z-40 flex items-center gap-2 px-5 py-3.5 bg-primary text-on-primary rounded-2xl shadow-xl hover:shadow-2xl active:scale-95 transition-all">
    <span class="material-symbols-outlined">add</span>
    <span class="text-sm font-bold pr-1">Registrar Niño</span>
</a>

{{-- Bottom Navigation Bar (Mobile) --}}
<nav class="md:hidden fixed bottom-0 left-0 w-full z-50 bg-surface shadow-[0_-2px_10px_rgba(0,0,0,0.05)] rounded-t-xl flex justify-around items-center h-16 border-t border-outline-variant/20">
    <a href="{{ route('gestor.dashboard') }}"
       class="flex flex-col items-center justify-center py-1 px-4 {{ request()->routeIs('gestor.dashboard') ? 'text-primary' : 'text-on-surface-variant' }}">
        <span class="material-symbols-outlined text-xl">home</span>
        <span class="text-[10px] font-medium">Inicio</span>
    </a>
    <a href="{{ route('gestor.ninos.index') }}"
       class="flex flex-col items-center justify-center py-1 px-4 {{ request()->routeIs('gestor.ninos.*') ? 'text-primary' : 'text-on-surface-variant' }}">
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
