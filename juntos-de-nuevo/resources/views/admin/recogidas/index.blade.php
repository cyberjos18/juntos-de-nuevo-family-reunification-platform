@extends('layouts.app')

@section('title', 'Recogidas')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 pb-28 md:pb-8 space-y-6">

    {{-- Header --}}
    <header>
        <h1 class="text-2xl sm:text-3xl font-bold text-on-surface">Recogidas registradas</h1>
        <p class="text-on-surface-variant text-sm mt-1">Historial de recogidas realizadas en todos los centros</p>
    </header>

    {{-- Lista de recogidas --}}
    @if($recogidas->isEmpty())
        <div class="bg-surface rounded-xl border border-outline-variant p-12 text-center">
            <span class="material-symbols-outlined text-5xl text-on-surface-variant/30 mb-3 block">local_shipping</span>
            <p class="text-on-surface-variant">No hay recogidas registradas aún.</p>
        </div>
    @else
        {{-- Vista desktop: tabla --}}
        <div class="hidden md:block bg-surface rounded-xl border border-outline-variant overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-surface-variant/30 border-b border-outline-variant">
                    <tr>
                        <th class="text-left px-4 py-3 text-xs font-medium text-on-surface-variant uppercase tracking-wider">Niño</th>
                        <th class="text-left px-4 py-3 text-xs font-medium text-on-surface-variant uppercase tracking-wider">Familiar</th>
                        <th class="text-left px-4 py-3 text-xs font-medium text-on-surface-variant uppercase tracking-wider">Centro</th>
                        <th class="text-left px-4 py-3 text-xs font-medium text-on-surface-variant uppercase tracking-wider">Fecha</th>
                        <th class="text-left px-4 py-3 text-xs font-medium text-on-surface-variant uppercase tracking-wider">Registrado por</th>
                        <th class="text-left px-4 py-3 text-xs font-medium text-on-surface-variant uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/20">
                    @foreach($recogidas as $recogida)
                        <tr class="hover:bg-surface-variant/20 transition-colors">
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.ninos.show', $recogida->nino) }}" class="font-medium text-on-surface hover:text-primary transition-colors">
                                    {{ $recogida->nino->nombre }} {{ $recogida->nino->apellido }}
                                </a>
                            </td>
                            <td class="px-4 py-3">
                                <div>
                                    <p class="font-medium text-on-surface">{{ $recogida->familiar_nombre }} {{ $recogida->familiar_apellido }}</p>
                                    <p class="text-xs text-on-surface-variant">{{ $recogida->familiar_parentesco }}</p>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-on-surface-variant">
                                {{ $recogida->nino->centro->nombre ?? 'N/A' }}
                            </td>
                            <td class="px-4 py-3 text-on-surface-variant">
                                {{ $recogida->fecha_recogida->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-4 py-3 text-on-surface-variant">
                                {{ $recogida->registradoPor->nombre ?? 'Sistema' }}
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.recogidas.show', $recogida->id) }}"
                                   class="inline-flex items-center gap-1 text-sm font-medium text-primary hover:text-primary/80 transition-colors">
                                    <span class="material-symbols-outlined text-lg">visibility</span>
                                    Ver
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Vista mobile: cards --}}
        <div class="md:hidden space-y-3">
            @foreach($recogidas as $recogida)
                <div class="bg-surface rounded-xl border border-outline-variant p-4 hover:shadow-md transition-shadow">
                    <a href="{{ route('admin.ninos.show', $recogida->nino) }}"
                       class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-lg bg-tertiary-container/30 flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined text-tertiary text-xl">task_alt</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <h3 class="text-sm font-semibold text-on-surface truncate">
                                    {{ $recogida->nino->nombre }} {{ $recogida->nino->apellido }}
                                </h3>
                                <span class="text-xs text-on-surface-variant whitespace-nowrap">
                                    {{ $recogida->fecha_recogida->format('d/m/Y') }}
                                </span>
                            </div>
                            <p class="text-xs text-on-surface-variant mt-0.5">
                                Familiar: {{ $recogida->familiar_nombre }} {{ $recogida->familiar_apellido }} ({{ $recogida->familiar_parentesco }})
                            </p>
                            @if($recogida->nino->centro)
                                <p class="text-xs text-secondary mt-0.5">{{ $recogida->nino->centro->nombre }}</p>
                            @endif
                        </div>
                    </a>
                    <div class="mt-3 pt-3 border-t border-outline-variant/20">
                        <a href="{{ route('admin.recogidas.show', $recogida->id) }}"
                           class="inline-flex items-center gap-1 text-sm font-medium text-primary">
                            <span class="material-symbols-outlined text-lg">visibility</span>
                            Ver detalle
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Paginación --}}
        @if($recogidas->hasPages())
            <div class="mt-6">
                {{ $recogidas->links() }}
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
