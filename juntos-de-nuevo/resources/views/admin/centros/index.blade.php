@extends('layouts.app')

@section('title', 'Centros de Acogida')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 pb-28 md:pb-8 space-y-6"
     x-data="{
         confirmarValidar: false,
         centroAVAlidar: null,
         centroNombre: '',
         fotoLightbox: false,
         fotoUrl: '',
         abrirModal(centro) {
             this.centroAVAlidar = centro;
             this.confirmarValidar = true;
         },
         confirmar() {
             if (this.centroAVAlidar) {
                 const form = document.getElementById('form-validar-' + this.centroAVAlidar);
                 if (form) form.submit();
             }
             this.confirmarValidar = false;
         },
         abrirLightbox(url) {
             this.fotoUrl = url;
             this.fotoLightbox = true;
         }
     }">

    {{-- Header --}}
    <header>
        <h1 class="text-2xl sm:text-3xl font-bold text-on-surface">Centros de acogida</h1>
        <p class="text-on-surface-variant text-sm mt-1">Gestión y validación de centros registrados</p>
    </header>

    {{-- Tabs --}}
    <div class="flex items-center border-b border-outline-variant">
        <a href="{{ route('admin.centros.index', ['estado' => 'pendientes']) }}"
           class="px-6 py-3 text-sm font-medium flex items-center gap-2 transition-colors
                  {{ $estado === 'pendientes' ? 'text-primary border-b-2 border-primary' : 'text-on-surface-variant hover:text-on-surface' }}">
            Pendientes
            <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $totalPendientes > 0 ? 'bg-error text-on-error' : 'bg-surface-variant text-on-surface-variant' }}">
                {{ $totalPendientes }}
            </span>
        </a>
        <a href="{{ route('admin.centros.index', ['estado' => 'activos']) }}"
           class="px-6 py-3 text-sm font-medium flex items-center gap-2 transition-colors
                  {{ $estado === 'activos' ? 'text-primary border-b-2 border-primary' : 'text-on-surface-variant hover:text-on-surface' }}">
            Activos
            <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-primary-container/30 text-primary">
                {{ $totalActivos }}
            </span>
        </a>
    </div>

    {{-- Lista de centros --}}
    @if($centros->isEmpty())
        <div class="bg-surface rounded-xl border border-outline-variant p-12 text-center">
            <span class="material-symbols-outlined text-5xl text-on-surface-variant/30 mb-3 block">apartment</span>
            <p class="text-on-surface-variant">
                @if($estado === 'pendientes')
                    No hay centros pendientes de validación.
                @else
                    No hay centros activos aún.
                @endif
            </p>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            @foreach($centros as $centro)
                <div class="bg-surface rounded-xl p-5 shadow-sm border border-outline-variant/20 hover:shadow-md transition-shadow">
                    <div class="flex items-start gap-4">
                        {{-- Foto del responsable + indicador de tipo --}}
                        <div class="flex flex-col items-center gap-1.5 flex-shrink-0">
                            <div class="relative group cursor-pointer"
                                 @click="abrirLightbox('{{ $centro->usuarioResponsable && $centro->usuarioResponsable->foto_identificacion ? asset('storage/' . $centro->usuarioResponsable->foto_identificacion) : '' }}')"
                                 title="Foto del responsable">
                                @if($centro->usuarioResponsable && $centro->usuarioResponsable->foto_identificacion)
                                    <img src="{{ asset('storage/' . $centro->usuarioResponsable->foto_identificacion) }}"
                                         alt="Foto de {{ $centro->usuarioResponsable->nombre }}"
                                         class="w-12 h-12 rounded-full object-cover border-2 border-outline-variant/30 group-hover:border-primary transition-colors"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="w-12 h-12 rounded-full bg-primary-container flex items-center justify-center text-primary text-sm font-bold hidden">
                                        {{ substr($centro->usuarioResponsable->nombre, 0, 1) }}
                                    </div>
                                @else
                                    <div class="w-12 h-12 rounded-full bg-surface-variant flex items-center justify-center text-on-surface-variant"
                                         title="Sin foto disponible">
                                        <span class="material-symbols-outlined text-2xl">person</span>
                                    </div>
                                @endif
                            </div>
                            {{-- Tipo de centro --}}
                            <span class="text-base leading-none" title="{{ $centro->tipo }}">
                                @if($centro->tipo === 'hospital')
                                    🏥
                                @elseif($centro->tipo === 'casa_acogida')
                                    🏠
                                @else
                                    🏢
                                @endif
                            </span>
                        </div>

                        {{-- Info del centro --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <h3 class="text-base font-bold text-on-surface truncate">{{ $centro->nombre }}</h3>
                                @if($centro->activo)
                                    <span class="px-2 py-0.5 rounded-lg bg-tertiary-container/30 text-tertiary text-xs font-medium flex-shrink-0">
                                        Activo
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-lg bg-error-container/50 text-error text-xs font-medium flex-shrink-0">
                                        Pendiente
                                    </span>
                                @endif
                            </div>
                            <p class="text-sm text-on-surface-variant mt-0.5 capitalize">{{ $centro->tipo }}</p>
                            <p class="text-sm text-on-surface-variant truncate">{{ $centro->email }}</p>

                            {{-- Conteos --}}
                            <div class="flex gap-4 mt-3">
                                <div class="flex items-center gap-1">
                                    <span class="material-symbols-outlined text-primary text-base">child_care</span>
                                    <span class="text-sm font-bold text-on-surface">{{ $centro->ninos_count ?? 0 }}</span>
                                    <span class="text-xs text-on-surface-variant">niños</span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <span class="material-symbols-outlined text-secondary text-base">group</span>
                                    <span class="text-sm font-bold text-on-surface">{{ $centro->usuarios_count ?? 0 }}</span>
                                    <span class="text-xs text-on-surface-variant">usuarios</span>
                                </div>
                            </div>

                            {{-- Fecha de registro --}}
                            <p class="text-xs text-on-surface-variant mt-2">
                                Registrado: {{ $centro->created_at->format('d/m/Y') }}
                            </p>

                            {{-- Acciones --}}
                            @if(!$centro->activo)
                                <div class="flex gap-2 mt-4">
                                    <button
                                        type="button"
                                        @click="centroNombre = '{{ $centro->nombre }}'; abrirModal({{ $centro->id }})"
                                        class="flex-1 inline-flex items-center justify-center gap-1 px-4 py-2 bg-tertiary text-on-tertiary rounded-lg text-sm font-medium hover:opacity-90 active:scale-[0.98] transition-all">
                                        <span class="material-symbols-outlined text-base">check_circle</span>
                                        Validar
                                    </button>
                                    <span class="flex-1 inline-flex items-center justify-center gap-1 px-4 py-2 border border-error text-error rounded-lg text-sm font-medium opacity-50 cursor-not-allowed">
                                        <span class="material-symbols-outlined text-base">block</span>
                                        Rechazar
                                    </span>
                                </div>
                                {{-- Form oculto para validar --}}
                                <form id="form-validar-{{ $centro->id }}" method="POST" action="{{ route('admin.centros.validar', $centro) }}" class="hidden">
                                    @csrf
                                    @method('PUT')
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Paginación --}}
        @if($centros->hasPages())
            <div class="mt-6">
                {{ $centros->links() }}
            </div>
        @endif
    @endif

    {{-- Lightbox para foto del responsable (dentro del x-data scope) --}}
    <div x-show="fotoLightbox"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[110] flex items-center justify-center p-4"
         style="display: none;">
        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/60" @click="fotoLightbox = false; fotoUrl = ''"></div>

        {{-- Imagen ampliada --}}
        <div x-show="fotoLightbox"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="relative z-10 max-w-md max-h-[80vh] rounded-2xl overflow-hidden shadow-2xl">
            <img :src="fotoUrl" alt="Foto del responsable ampliada"
                 class="w-full h-full object-contain bg-surface"
                 onerror="this.parentElement.querySelector('button').click()">
            {{-- Botón cerrar --}}
            <button @click="fotoLightbox = false; fotoUrl = ''"
                    class="absolute top-3 right-3 w-8 h-8 rounded-full bg-black/50 text-white flex items-center justify-center hover:bg-black/70 transition-colors">
                <span class="material-symbols-outlined text-lg">close</span>
            </button>
        </div>
    </div>

    {{-- Modal de Confirmación (dentro del x-data scope) --}}
    <div x-show="confirmarValidar"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[100] flex items-center justify-center p-4"
         style="display: none;">
        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/40" @click="confirmarValidar = false"></div>

        {{-- Modal card --}}
        <div x-show="confirmarValidar"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="relative bg-surface rounded-2xl shadow-xl p-6 w-full max-w-sm z-10">
            <div class="text-center">
                <span class="material-symbols-outlined text-5xl text-tertiary mb-3 block">verified_user</span>
                <h3 class="text-lg font-bold text-on-surface">¿Validar <span x-text="centroNombre"></span>?</h3>
                <p class="text-sm text-on-surface-variant mt-2">Se activará el centro y todos sus usuarios.</p>
            </div>
            <div class="flex gap-3 mt-6">
                <button type="button"
                        @click="confirmarValidar = false"
                        class="flex-1 px-4 py-2.5 border border-outline text-on-surface rounded-full text-sm font-medium hover:bg-surface-variant transition-colors">
                    Cancelar
                </button>
                <button type="button"
                        @click="confirmar()"
                        class="flex-1 px-4 py-2.5 bg-tertiary text-on-tertiary rounded-full text-sm font-bold hover:opacity-90 transition-opacity">
                    Validar
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Bottom Navigation Bar (Mobile) --}}
<nav class="md:hidden fixed bottom-0 left-0 w-full z-50 bg-surface shadow-[0_-2px_10px_rgba(0,0,0,0.05)] rounded-t-xl flex justify-around items-center h-16 border-t border-outline-variant/20">
    <a href="{{ route('admin.dashboard') }}"
       class="flex flex-col items-center justify-center py-1 px-4 text-on-surface-variant">
        <span class="material-symbols-outlined text-xl">analytics</span>
        <span class="text-[10px] font-medium">Stats</span>
    </a>
    <a href="{{ route('admin.centros.index') }}"
       class="flex flex-col items-center justify-center py-1 px-4 {{ request()->routeIs('admin.centros.*') ? 'text-primary' : 'text-on-surface-variant' }}">
        <span class="material-symbols-outlined text-xl">apartment</span>
        <span class="text-[10px] font-medium">Centros</span>
    </a>
    <a href="{{ route('admin.configuracion.mailjet') }}"
       class="flex flex-col items-center justify-center py-1 px-4 text-on-surface-variant">
        <span class="material-symbols-outlined text-xl">settings</span>
        <span class="text-[10px] font-medium">Config</span>
    </a>
</nav>
@endsection
