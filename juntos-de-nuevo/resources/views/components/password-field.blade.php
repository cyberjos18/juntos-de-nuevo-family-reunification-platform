@props([
    'name' => '',
    'label' => '',
    'placeholder' => '••••••••••••',
    'value' => '',
    'required' => false,
])

<div x-data="{ show: false }" class="flex flex-col gap-1">
    <label for="{{ $name }}" class="text-sm font-medium text-on-surface-variant ml-1">
        {{ $label }}
        @if($required)
            <span class="text-error">*</span>
        @endif
    </label>
    <div class="relative">
        <input
            :type="show ? 'text' : 'password'"
            name="{{ $name }}"
            id="{{ $name }}"
            value="{{ old($name, $value) }}"
            placeholder="{{ $placeholder }}"
            {{ $required ? 'required' : '' }}
            class="w-full h-14 px-4 pr-12 rounded-xl bg-surface-container border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all text-base text-on-surface placeholder:text-on-surface-variant/50"
        >
        <button
            type="button"
            @click="show = !show"
            class="absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant hover:text-primary transition-colors p-1"
        >
            <span class="material-symbols-outlined text-xl" x-text="show ? 'visibility_off' : 'visibility'"></span>
        </button>
    </div>
    @error($name)
        <p class="text-sm text-error mt-0.5 ml-1">{{ $message }}</p>
    @enderror
</div>
