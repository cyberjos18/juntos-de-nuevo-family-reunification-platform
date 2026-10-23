@props(['icon' => 'info', 'value' => 0, 'label' => ''])

<div class="bg-surface rounded-xl border border-outline-variant p-4 flex items-center gap-4">
    <div class="w-12 h-12 rounded-full bg-primary-container flex items-center justify-center flex-shrink-0">
        <span class="material-symbols-outlined text-primary text-2xl">{{ $icon }}</span>
    </div>
    <div>
        <p class="text-2xl font-bold text-on-surface">{{ $value }}</p>
        <p class="text-sm text-on-surface-variant">{{ $label }}</p>
    </div>
</div>
