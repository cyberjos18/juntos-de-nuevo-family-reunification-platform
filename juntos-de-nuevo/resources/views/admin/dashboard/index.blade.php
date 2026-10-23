@extends('layouts.app')

@section('title', 'Panel de Administración')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 pb-28 md:pb-8 space-y-8">

    {{-- Header --}}
    <header>
        <h1 class="text-2xl sm:text-3xl font-bold text-on-surface">Panel de Administración</h1>
        <p class="text-on-surface-variant text-sm mt-1">Resumen general del sistema Juntos de nuevo</p>
    </header>

    {{-- Stats Grid (2x3 móvil, 3x3 desktop) --}}
    <section class="grid grid-cols-2 md:grid-cols-3 gap-4">
        <a href="{{ route('admin.centros.index') }}" class="block hover:shadow-lg transition-shadow">
            <x-stat-card icon="apartment" :value="$stats['total_centros']" label="Centros totales" />
        </a>
        <a href="{{ route('admin.centros.index', ['estado' => 'activos']) }}" class="block hover:shadow-lg transition-shadow">
            <x-stat-card icon="check_circle" :value="$stats['centros_activos']" label="Centros activos" />
        </a>
        <a href="{{ route('admin.centros.index', ['estado' => 'pendientes']) }}" class="block hover:shadow-lg transition-shadow">
            <x-stat-card icon="pending_actions" :value="$stats['centros_pendientes']" label="Centros pendientes" />
        </a>
        <a href="{{ route('admin.ninos.index') }}" class="block hover:shadow-lg transition-shadow">
            <x-stat-card icon="child_care" :value="$stats['total_ninos']" label="Total niños" />
        </a>
        <a href="{{ route('admin.ninos.index', ['estado' => 'encontrado']) }}" class="block hover:shadow-lg transition-shadow">
            <x-stat-card icon="person_search" :value="$stats['ninos_encontrados']" label="Niños encontrados" />
        </a>
        <a href="{{ route('admin.ninos.index', ['estado' => 'recogido']) }}" class="block hover:shadow-lg transition-shadow">
            <x-stat-card icon="family_restroom" :value="$stats['ninos_recogidos']" label="Niños recogidos" />
        </a>
        <a href="{{ route('admin.ninos.index', ['estado' => 'familia_localizada']) }}" class="block hover:shadow-lg transition-shadow">
            <x-stat-card icon="groups" :value="$stats['ninos_familia_localizada']" label="Familia localizada" />
        </a>
        <a href="{{ route('admin.recogidas.index') }}" class="block hover:shadow-lg transition-shadow">
            <x-stat-card icon="local_shipping" :value="$stats['total_recogidas']" label="Total recogidas" />
        </a>
        <a href="{{ route('admin.usuarios.index') }}" class="block hover:shadow-lg transition-shadow">
            <x-stat-card icon="manage_accounts" :value="$stats['usuarios_gestores']" label="Gestores" />
        </a>
    </section>

    {{-- Acciones Rápidas --}}
    <section>
        <h2 class="text-xl font-semibold text-on-surface mb-4">Acciones rápidas</h2>
        <div class="space-y-3">
            {{-- Validar centros --}}
            <a href="{{ route('admin.centros.index') }}"
               class="flex items-center gap-4 p-4 bg-surface hover:bg-surface-variant/50 rounded-xl border border-outline-variant/20 transition-all group">
                <div class="w-12 h-12 rounded-xl bg-primary-container/30 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-primary text-2xl">verified_user</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-base font-medium text-on-surface">🏠 Validar centros</p>
                    <p class="text-sm text-on-surface-variant">
                        {{ $stats['centros_pendientes'] }} {{ $stats['centros_pendientes'] === 1 ? 'centro pendiente' : 'centros pendientes' }}
                    </p>
                </div>
                <span class="material-symbols-outlined text-outline group-hover:text-primary transition-colors">chevron_right</span>
            </a>

            {{-- Configuración Mailjet --}}
            <a href="{{ route('admin.configuracion.mailjet') }}"
               class="flex items-center gap-4 p-4 bg-surface hover:bg-surface-variant/50 rounded-xl border border-outline-variant/20 transition-all group">
                <div class="w-12 h-12 rounded-xl bg-secondary-container/50 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-secondary text-2xl">settings</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-base font-medium text-on-surface">⚙️ Configuración Mailjet</p>
                    <p class="text-sm text-on-surface-variant">Gestionar credenciales de email</p>
                </div>
                <span class="material-symbols-outlined text-outline group-hover:text-primary transition-colors">chevron_right</span>
            </a>

            {{-- Ver todos los niños --}}
            <a href="{{ route('home') }}"
               class="flex items-center gap-4 p-4 bg-surface hover:bg-surface-variant/50 rounded-xl border border-outline-variant/20 transition-all group">
                <div class="w-12 h-12 rounded-xl bg-tertiary-container/30 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-tertiary text-2xl">people</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-base font-medium text-on-surface">👥 Ver todos los niños</p>
                    <p class="text-sm text-on-surface-variant">
                        {{ $stats['total_ninos'] }} {{ $stats['total_ninos'] === 1 ? 'niño registrado' : 'niños registrados' }}
                    </p>
                </div>
                <span class="material-symbols-outlined text-outline group-hover:text-primary transition-colors">chevron_right</span>
            </a>
        </div>
    </section>
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
