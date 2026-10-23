@props(['type' => 'success', 'message' => ''])

@php
    $colors = [
        'success' => 'bg-tertiary-container text-tertiary',
        'error' => 'bg-error-container text-error',
        'warning' => 'bg-amber-100 text-amber-800',
    ];
    $icons = [
        'success' => 'check_circle',
        'error' => 'error',
        'warning' => 'warning',
    ];
@endphp

<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 -translate-y-2"
     x-transition:enter-end="opacity-100 translate-y-0"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 translate-y-0"
     x-transition:leave-end="opacity-0 -translate-y-2"
     class="{{ $colors[$type] ?? $colors['success'] }} rounded-xl px-4 py-3 flex items-center gap-3 shadow-sm"
     role="alert">
    <span class="material-symbols-outlined text-xl">{{ $icons[$type] ?? 'info' }}</span>
    <p class="text-sm font-medium flex-1">{{ $message }}</p>
    <button @click="show = false" class="opacity-70 hover:opacity-100 transition-opacity">
        <span class="material-symbols-outlined text-lg">close</span>
    </button>
</div>
