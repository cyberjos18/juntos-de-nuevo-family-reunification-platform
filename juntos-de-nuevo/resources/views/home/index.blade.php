@extends('layouts.app')

@section('title', 'Inicio')

@section('content')
    {{-- Hero Banner con imágenes responsive --}}
    <section class="relative min-h-[700px] md:min-h-[700px] lg:min-h-[750px]">
        {{-- Imágenes de fondo --}}
        <img src="{{ asset('images/banner-mobile.png') }}" alt="Banner móvil" class="absolute inset-0 w-full h-full object-cover md:hidden">
        <img src="{{ asset('images/banner-desktop.png') }}" alt="Banner escritorio" class="absolute inset-0 w-full h-full object-cover hidden md:block">

        {{-- Overlay ligero solo en la zona superior para reforzar legibilidad --}}
        <div class="absolute inset-x-0 top-0 h-2/3 bg-gradient-to-b from-white/80 to-transparent md:from-white/60"></div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 md:pt-20 pb-16 md:pb-20">
            <div class="max-w-md md:max-w-lg lg:max-w-xl">
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-on-background mb-4 leading-tight">
                    Reunificando familias en momentos de emergencia
                </h1>
                <p class="text-base sm:text-lg text-on-surface-variant mb-8">
                    Plataforma de búsqueda de niños encontrados tras los terremotos en Venezuela
                </p>
                <div class="flex flex-col sm:flex-row gap-4">
                    @if(!auth()->check())
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center gap-2 bg-[#1565C0] text-white px-7 py-3.5 rounded-full text-sm font-semibold hover:bg-[#0D47A1] transition-colors shadow-lg">
                            <span class="material-symbols-outlined text-lg">apartment</span>
                            Soy un centro de acogida
                        </a>
                    @endif
                </div>
    </section>

    {{-- Filtros y listado --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Chips de filtro --}}
        <div class="flex flex-wrap gap-2 mb-6" x-data="{ filtro: '{{ request('estado', 'todos') }}' }">
            <a href="{{ route('home') }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-sm font-medium transition-colors {{ !request('estado') || request('estado') === 'todos' ? 'bg-primary text-on-primary' : 'bg-surface-variant text-on-surface-variant hover:bg-secondary-container' }}">
                <span class="material-symbols-outlined text-base">apps</span>
                Todos
            </a>
            <a href="{{ route('home', ['estado' => 'encontrado']) }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-sm font-medium transition-colors {{ request('estado') === 'encontrado' ? 'bg-[#1565C0] text-white' : 'bg-surface-variant text-on-surface-variant hover:bg-secondary-container' }}">
                <span class="w-2 h-2 rounded-full bg-[#1565C0]"></span>
                Encontrados
            </a>
            <a href="{{ route('home', ['estado' => 'familia_localizada']) }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-sm font-medium transition-colors {{ request('estado') === 'familia_localizada' ? 'bg-[#F57C00] text-white' : 'bg-surface-variant text-on-surface-variant hover:bg-secondary-container' }}">
                <span class="w-2 h-2 rounded-full bg-[#F57C00]"></span>
                Familia localizada
            </a>
        </div>

        {{-- Grid de niños --}}
        @if($ninos->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($ninos as $nino)
                    <x-nino-card :nino="$nino" />
                @endforeach
            </div>

            {{-- Paginación --}}
            <div class="mt-8">
                {{ $ninos->links() }}
            </div>
        @else
            <div class="text-center py-16">
                <span class="material-symbols-outlined text-6xl text-on-surface-variant/30 mb-4">search_off</span>
                <h2 class="text-lg font-medium text-on-surface-variant mb-2">No se encontraron niños</h2>
                <p class="text-sm text-on-surface-variant/70">No hay registros que coincidan con el filtro seleccionado.</p>
            </div>
        @endif
    </section>
@endsection
