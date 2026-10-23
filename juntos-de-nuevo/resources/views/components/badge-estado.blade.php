@props(['estado' => 'encontrado'])

@php
    $colores = [
        'encontrado' => 'bg-[#1565C0]/10 text-[#1565C0]',
        'familia_localizada' => 'bg-[#F57C00]/10 text-[#F57C00]',
        'recogido' => 'bg-[#2E7D32]/10 text-[#2E7D32]',
        'otro_centro' => 'bg-[#78909C]/10 text-[#78909C]',
    ];
    $labels = [
        'encontrado' => 'Encontrado',
        'familia_localizada' => 'Familia localizada',
        'recogido' => 'Recogido',
        'otro_centro' => 'Otro centro',
    ];
@endphp

<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium {{ $colores[$estado] ?? $colores['encontrado'] }}">
    <span class="w-1.5 h-1.5 rounded-full {{ $estado === 'encontrado' ? 'bg-[#1565C0]' : ($estado === 'familia_localizada' ? 'bg-[#F57C00]' : ($estado === 'recogido' ? 'bg-[#2E7D32]' : 'bg-[#78909C]')) }}"></span>
    {{ $labels[$estado] ?? ucfirst($estado) }}
</span>
