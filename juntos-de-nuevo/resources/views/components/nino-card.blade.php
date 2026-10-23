@props(['nino'])

<a href="{{ route('ninos.show', $nino) }}" class="block bg-surface rounded-xl shadow-sm border border-outline-variant overflow-hidden hover:shadow-md transition-shadow group">
    {{-- Foto --}}
    <div class="aspect-square bg-primary-container relative overflow-hidden">
        @if($nino->foto_url)
            <img src="{{ asset('storage/' . $nino->foto_url) }}" alt="Foto de {{ $nino->nombre }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
        @else
            <div class="w-full h-full flex items-center justify-center">
                <span class="material-symbols-outlined text-5xl text-primary/40">child_care</span>
            </div>
        @endif
    </div>

    {{-- Info --}}
    <div class="p-3 space-y-1">
        <h3 class="text-sm font-medium text-on-surface truncate">{{ $nino->nombre }} {{ $nino->apellido }}</h3>
        @if($nino->edad)
            <p class="text-xs text-on-surface-variant">{{ $nino->edad }} {{ $nino->edad === 1 ? 'año' : 'años' }}</p>
        @endif
        @if($nino->centro)
            <p class="text-xs text-secondary truncate">{{ $nino->centro->nombre }}</p>
        @endif
        <div class="pt-1">
            <x-badge-estado :estado="$nino->estado" />
        </div>
    </div>
</a>
