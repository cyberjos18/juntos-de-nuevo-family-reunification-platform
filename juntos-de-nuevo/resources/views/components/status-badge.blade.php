@props(['activo' => false])

<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold
    {{ $activo ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-red-50 text-red-700 border border-red-200' }}">
    <span class="w-1.5 h-1.5 rounded-full {{ $activo ? 'bg-emerald-500' : 'bg-red-500' }}"></span>
    {{ $activo ? 'Activo' : 'Inactivo' }}
</span>
