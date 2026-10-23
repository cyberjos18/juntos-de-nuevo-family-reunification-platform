@extends('layouts.app')

@section('title', 'Registrar Recogida')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6 pb-28 md:pb-8"
     x-data="recogidaForm()">

    {{-- ============================================================
         HEADER
         ============================================================ --}}
    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('gestor.ninos.show', $nino->id) }}"
           class="p-2 rounded-full hover:bg-surface-variant transition-colors text-on-surface">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-on-surface">Registrar Recogida</h1>
            <p class="text-sm text-on-surface-variant">Completa el proceso de entrega segura del menor.</p>
        </div>
    </div>

    {{-- ============================================================
         INFO NIÑO — Card horizontal superior (full-width)
         ============================================================ --}}
    <section class="bg-surface rounded-xl p-4 sm:p-5 shadow-sm border border-outline-variant/30 mb-6">
        <div class="flex items-center gap-4">
            {{-- Foto del niño --}}
            <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-xl overflow-hidden border-2 border-outline-variant/20 bg-surface-variant flex-shrink-0">
                @if($nino->foto_url)
                    <img src="{{ asset('storage/' . $nino->foto_url) }}"
                         alt="Foto de {{ $nino->nombre }}"
                         class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center">
                        <span class="material-symbols-outlined text-3xl text-on-surface-variant/40">child_care</span>
                    </div>
                @endif
            </div>

            {{-- Datos del niño --}}
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 flex-wrap">
                    <h2 class="text-lg font-bold text-on-surface truncate">
                        {{ $nino->nombre }} {{ $nino->apellido }}
                    </h2>
                    {{-- Badge de estado — inline, no superpuesto --}}
                    <x-badge-estado :estado="$nino->estado" />
                </div>
                <p class="text-sm text-on-surface-variant mt-0.5">
                    {{ $nino->edad ? $nino->edad . ' años' : 'Edad N/D' }}
                    @if($nino->sexo)
                        · {{ $nino->sexo === 'M' ? 'Masculino' : 'Femenino' }}
                    @endif
                </p>
                <div class="flex items-center gap-1 mt-1 text-on-surface-variant">
                    <span class="material-symbols-outlined text-sm">location_on</span>
                    <span class="text-xs">{{ $nino->centro->nombre ?? 'Centro no asignado' }}</span>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================================
         FORMULARIO PRINCIPAL — Flujo vertical: Datos -> Foto -> Confirmar
         ============================================================ --}}
    <form method="POST"
          action="{{ route('gestor.recogidas.store', $nino->id) }}"
          enctype="multipart/form-data"
          @submit.prevent="showConfirmModal = true"
          x-ref="recogidaForm"
          class="bg-surface rounded-xl p-5 sm:p-6 shadow-sm border border-outline-variant/30">
        @csrf

                <h3 class="text-lg font-semibold text-on-surface border-b border-outline-variant pb-3 mb-5">
                    Datos del Familiar
                </h3>

                <div class="space-y-4">
                    {{-- Nombre y Apellido --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="familiar_nombre" class="block text-sm font-medium text-on-surface-variant mb-1">
                                Nombre <span class="text-error">*</span>
                            </label>
                            <input type="text" name="familiar_nombre" id="familiar_nombre"
                                   value="{{ old('familiar_nombre') }}" required
                                   class="w-full rounded-xl {{ $errors->has('familiar_nombre') ? 'border-red-500 focus:border-red-500 focus:ring-1 focus:ring-red-500' : 'border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary' }} bg-surface px-4 py-3 text-on-surface outline-none transition-all"
                                   placeholder="Ej. Carmen"
                                   x-model="familiarNombre">
                            @error('familiar_nombre')
                                <p class="text-red-600 text-sm font-medium mt-1 block">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="familiar_apellido" class="block text-sm font-medium text-on-surface-variant mb-1">
                                Apellido <span class="text-error">*</span>
                            </label>
                            <input type="text" name="familiar_apellido" id="familiar_apellido"
                                   value="{{ old('familiar_apellido') }}" required
                                   class="w-full rounded-xl {{ $errors->has('familiar_apellido') ? 'border-red-500 focus:border-red-500 focus:ring-1 focus:ring-red-500' : 'border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary' }} bg-surface px-4 py-3 text-on-surface outline-none transition-all"
                                   placeholder="Ej. Pérez"
                                   x-model="familiarApellido">
                            @error('familiar_apellido')
                                <p class="text-red-600 text-sm font-medium mt-1 block">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Parentesco y Teléfono --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="familiar_parentesco" class="block text-sm font-medium text-on-surface-variant mb-1">
                                Parentesco <span class="text-error">*</span>
                            </label>
                            <select name="familiar_parentesco" id="familiar_parentesco" required
                                    class="w-full rounded-xl {{ $errors->has('familiar_parentesco') ? 'border-red-500 focus:border-red-500 focus:ring-1 focus:ring-red-500' : 'border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary' }} bg-surface px-4 py-3 text-on-surface outline-none transition-all"
                                    x-model="familiarParentesco">
                                <option value="">Seleccione parentesco</option>
                                <option value="Padre" {{ old('familiar_parentesco') === 'Padre' ? 'selected' : '' }}>Padre</option>
                                <option value="Madre" {{ old('familiar_parentesco') === 'Madre' ? 'selected' : '' }}>Madre</option>
                                <option value="Tío/a" {{ old('familiar_parentesco') === 'Tío/a' ? 'selected' : '' }}>Tío/a</option>
                                <option value="Abuelo/a" {{ old('familiar_parentesco') === 'Abuelo/a' ? 'selected' : '' }}>Abuelo/a</option>
                                <option value="Hermano/a" {{ old('familiar_parentesco') === 'Hermano/a' ? 'selected' : '' }}>Hermano/a</option>
                                <option value="Otro" {{ old('familiar_parentesco') === 'Otro' ? 'selected' : '' }}>Otro</option>
                            </select>
                            @error('familiar_parentesco')
                                <p class="text-red-600 text-sm font-medium mt-1 block">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="familiar_telefono" class="block text-sm font-medium text-on-surface-variant mb-1">
                                Teléfono <span class="text-error">*</span>
                            </label>
                            <input type="tel" name="familiar_telefono" id="familiar_telefono"
                                   value="{{ old('familiar_telefono') }}" required
                                   class="w-full rounded-xl {{ $errors->has('familiar_telefono') ? 'border-red-500 focus:border-red-500 focus:ring-1 focus:ring-red-500' : 'border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary' }} bg-surface px-4 py-3 text-on-surface outline-none transition-all"
                                   placeholder="+58 412 000 0000">
                            @error('familiar_telefono')
                                <p class="text-red-600 text-sm font-medium mt-1 block">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Documento y Email --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="familiar_documento" class="block text-sm font-medium text-on-surface-variant mb-1">
                                Documento (Cédula/DNI)
                            </label>
                            <input type="text" name="familiar_documento" id="familiar_documento"
                                   value="{{ old('familiar_documento') }}"
                                   class="w-full rounded-xl {{ $errors->has('familiar_documento') ? 'border-red-500 focus:border-red-500 focus:ring-1 focus:ring-red-500' : 'border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary' }} bg-surface px-4 py-3 text-on-surface outline-none transition-all"
                                   placeholder="Ej. V-12.345.678">
                            @error('familiar_documento')
                                <p class="text-red-600 text-sm font-medium mt-1 block">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="familiar_email" class="block text-sm font-medium text-on-surface-variant mb-1">
                                Correo Electrónico
                            </label>
                            <input type="email" name="familiar_email" id="familiar_email"
                                   value="{{ old('familiar_email') }}"
                                   class="w-full rounded-xl {{ $errors->has('familiar_email') ? 'border-red-500 focus:border-red-500 focus:ring-1 focus:ring-red-500' : 'border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary' }} bg-surface px-4 py-3 text-on-surface outline-none transition-all"
                                   placeholder="nombre@ejemplo.com">
                            @error('familiar_email')
                                <p class="text-red-600 text-sm font-medium mt-1 block">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Observaciones --}}
                    <div>
                        <label for="notas" class="block text-sm font-medium text-on-surface-variant mb-1">Observaciones</label>
                        <textarea name="notas" id="notas" rows="3"
                                  class="w-full rounded-xl {{ $errors->has('notas') ? 'border-red-500 focus:border-red-500 focus:ring-1 focus:ring-red-500' : 'border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary' }} bg-surface px-4 py-3 text-on-surface outline-none transition-all resize-none"
                                  placeholder="Notas adicionales sobre la recogida...">{{ old('notas') }}</textarea>
                        @error('notas')
                            <p class="text-red-600 text-sm font-medium mt-1 block">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- ============================================================
                     FOTO DE EVIDENCIA — entre datos del familiar y confirmación
                     ============================================================ --}}
                <div class="pt-6 mt-6 border-t border-outline-variant">
                    <h3 class="text-sm font-bold text-on-surface uppercase tracking-wide flex items-center gap-2 mb-1">
                        <span class="material-symbols-outlined text-primary text-lg">camera_alt</span>
                        Evidencia de Entrega
                    </h3>
                    <p class="text-xs text-on-surface-variant mb-4">Toma una foto del niño con el familiar.</p>

                    <x-foto-upload name="foto_recogida" label="" :required="true" :capture="true" aspect="4/3" />

                    <p class="text-xs text-on-surface-variant mt-3 italic flex items-start gap-1">
                        <span class="material-symbols-outlined text-[14px] flex-shrink-0 mt-px">info</span>
                        Esta foto es obligatoria como comprobante legal de la entrega.
                    </p>
                </div>

                {{-- ============================================================
                     FOOTER DEL FORMULARIO: Advertencia + Botones
                     ============================================================ --}}
                <div class="pt-6 mt-6 border-t border-outline-variant">
                    {{-- Advertencia compacta --}}
                    <div class="flex gap-3 items-start mb-5 p-3 bg-error-container/50 rounded-xl border border-error/10">
                        <span class="material-symbols-outlined text-error flex-shrink-0 text-lg mt-0.5">warning</span>
                        <p class="text-sm text-on-error-container">
                            Esta acción es <strong>irreversible</strong>. El niño será marcado como <strong>Recogido</strong> y saldrá del sistema de seguimiento activo.
                        </p>
                    </div>

                    {{-- Botones de acción --}}
                    <div class="flex flex-col sm:flex-row gap-3 items-center justify-end">
                        <a href="{{ route('gestor.ninos.show', $nino->id) }}"
                           class="w-full sm:w-auto px-6 py-3 rounded-full text-sm font-medium text-on-surface-variant hover:bg-surface-variant transition-colors text-center">
                            Cancelar
                        </a>
                        <button type="submit"
                                class="w-full sm:w-auto px-8 py-3 bg-[#2E7D32] text-white rounded-full text-sm font-bold shadow-md hover:opacity-90 active:scale-[0.98] transition-all flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-lg">task_alt</span>
                            Confirmar Recogida
                        </button>
                    </div>
                </div>
            </form>

    {{-- ============================================================
         MODAL DE CONFIRMACIÓN
         ============================================================ --}}
    <div x-show="showConfirmModal"
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/50"
         @click.self="showConfirmModal = false"
         @keydown.escape.window="showConfirmModal = false">
        <div x-show="showConfirmModal"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="bg-surface rounded-2xl shadow-2xl max-w-md w-full p-6 space-y-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-tertiary-container flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-tertiary">help</span>
                </div>
                <h3 class="text-lg font-bold text-on-surface">¿Confirmar recogida?</h3>
            </div>
            <p class="text-sm text-on-surface-variant leading-relaxed">
                <strong class="text-on-surface">{{ $nino->nombre }} {{ $nino->apellido }}</strong>
                será recogido por
                <strong class="text-on-surface" x-text="familiarNombre + ' ' + familiarApellido">el familiar</strong>.
                Esta acción no se puede deshacer.
            </p>
            <div class="flex gap-3 justify-end pt-2">
                <button type="button"
                        @click="showConfirmModal = false"
                        class="px-5 py-2.5 rounded-full text-sm font-medium text-on-surface-variant hover:bg-surface-variant transition-colors">
                    Cancelar
                </button>
                <button type="button"
                        @click="$refs.recogidaForm.submit()"
                        class="px-6 py-2.5 bg-[#2E7D32] text-white rounded-full text-sm font-bold shadow-md hover:opacity-90 active:scale-[0.98] transition-all flex items-center gap-2">
                    <span class="material-symbols-outlined text-lg">check</span>
                    Confirmar
                </button>
            </div>
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
