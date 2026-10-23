@extends('layouts.app')

@section('title', 'Gestión de Usuarios')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 pb-28 md:pb-8 space-y-6"
     x-data="{ tab: '{{ $rol }}' }">

    {{-- Header --}}
    <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-on-surface">Gestión de Usuarios</h1>
            <p class="text-on-surface-variant text-sm mt-1">Administra administradores y gestores del sistema</p>
        </div>
        <a href="{{ route('admin.usuarios.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-primary text-on-primary rounded-xl text-sm font-medium hover:bg-primary/90 transition-colors shadow-sm">
            <span class="material-symbols-outlined text-lg">person_add</span>
            Crear Administrador
        </a>
    </header>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center gap-2">
            <span class="material-symbols-outlined text-emerald-600">check_circle</span>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl flex items-center gap-2">
            <span class="material-symbols-outlined text-red-600">error</span>
            {{ session('error') }}
        </div>
    @endif

    {{-- Tabs --}}
    <div class="flex items-center border-b border-outline-variant">
        <a href="{{ route('admin.usuarios.index', ['rol' => 'todos']) }}"
           class="px-6 py-3 text-sm font-medium flex items-center gap-2 transition-colors
                  {{ $rol === 'todos' ? 'text-primary border-b-2 border-primary' : 'text-on-surface-variant hover:text-on-surface' }}">
            Todos
        </a>
        <a href="{{ route('admin.usuarios.index', ['rol' => 'gestor']) }}"
           class="px-6 py-3 text-sm font-medium flex items-center gap-2 transition-colors
                  {{ $rol === 'gestor' ? 'text-primary border-b-2 border-primary' : 'text-on-surface-variant hover:text-on-surface' }}">
            Gestores
        </a>
        <a href="{{ route('admin.usuarios.index', ['rol' => 'admin']) }}"
           class="px-6 py-3 text-sm font-medium flex items-center gap-2 transition-colors
                  {{ $rol === 'admin' ? 'text-primary border-b-2 border-primary' : 'text-on-surface-variant hover:text-on-surface' }}">
            Administradores
        </a>
    </div>

    {{-- Tabla --}}
    <div class="bg-surface rounded-xl border border-outline-variant/20 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-surface-variant/30 border-b border-outline-variant">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-on-surface-variant">Nombre</th>
                        <th class="px-4 py-3 text-left font-semibold text-on-surface-variant">Email</th>
                        <th class="px-4 py-3 text-left font-semibold text-on-surface-variant">Rol</th>
                        <th class="px-4 py-3 text-left font-semibold text-on-surface-variant hidden sm:table-cell">Centro</th>
                        <th class="px-4 py-3 text-left font-semibold text-on-surface-variant">Estado</th>
                        <th class="px-4 py-3 text-right font-semibold text-on-surface-variant">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/10">
                    @forelse($usuarios as $usuario)
                        <tr class="hover:bg-surface-variant/10 transition-colors">
                            <td class="px-4 py-3">
                                <div class="font-medium text-on-surface">{{ $usuario->nombre }}</div>
                                @if($usuario->cargo)
                                    <div class="text-xs text-on-surface-variant">{{ $usuario->cargo }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-on-surface-variant">{{ $usuario->email }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                    {{ $usuario->rol === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                                    {{ $usuario->rol === 'admin' ? 'Admin' : 'Gestor' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-on-surface-variant hidden sm:table-cell">
                                {{ $usuario->centro?->nombre ?? '—' }}
                            </td>
                            <td class="px-4 py-3">
                                <x-status-badge :activo="$usuario->activo" />
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.usuarios.edit', $usuario) }}"
                                       class="p-1.5 rounded-lg text-on-surface-variant hover:bg-surface-variant transition-colors"
                                       title="Editar">
                                        <span class="material-symbols-outlined text-lg">edit</span>
                                    </a>
                                    <button type="button"
                                            x-data
                                            @click="$dispatch('open-password-modal', { userId: {{ $usuario->id }}, userName: '{{ addslashes($usuario->nombre) }}' })"
                                            class="p-1.5 rounded-lg text-on-surface-variant hover:bg-surface-variant transition-colors"
                                            title="Cambiar contraseña">
                                        <span class="material-symbols-outlined text-lg">key</span>
                                    </button>
                                    <form action="{{ route('admin.usuarios.destroy', $usuario) }}" method="POST"
                                          x-data="{ confirmar: false }"
                                          @submit.prevent="if(!confirmar){ confirmar = true; $el.submit() }"
                                          class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="p-1.5 rounded-lg text-red-500 hover:bg-red-50 transition-colors"
                                                title="Eliminar"
                                                onclick="return confirm('¿Estás seguro de eliminar a {{ addslashes($usuario->nombre) }}?')">
                                            <span class="material-symbols-outlined text-lg">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-on-surface-variant">
                                <span class="material-symbols-outlined text-5xl text-on-surface-variant/30 block mb-3">people_outline</span>
                                <p>No hay usuarios que mostrar.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        @if($usuarios->hasPages())
            <div class="px-4 py-3 border-t border-outline-variant/20">
                {{ $usuarios->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
