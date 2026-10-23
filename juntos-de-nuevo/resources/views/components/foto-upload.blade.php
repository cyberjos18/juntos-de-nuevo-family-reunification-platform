@props([
    'name' => 'foto',
    'label' => 'Fotografía',
    'required' => false,
    'capture' => false,
    'aspect' => '4/3',
])

<div x-data="imageUpload('{{ $name }}', {{ $capture ? 'true' : 'false' }}, {{ $required ? 'true' : 'false' }})"
     x-init="init()"
     class="space-y-2">
    <label class="block text-sm font-medium text-on-surface-variant mb-1">{{ $label }}@if($required) <span class="text-red-600">*</span>@endif</label>

    {{-- Zona de subida con preview --}}
    <div class="relative">

        {{-- -------- Placeholder (sin imagen, sin cámara activa) -------- --}}
        <div x-show="!previewUrl && !cameraActive && !capturing"
             class="border-2 border-dashed border-outline-variant rounded-xl overflow-hidden cursor-pointer hover:border-primary hover:bg-surface-variant/30 transition-all"
             style="aspect-ratio: {{ $aspect }}; min-height: 200px;">
            <div class="w-full h-full flex flex-col items-center justify-center gap-3">
                <span class="material-symbols-outlined text-5xl text-on-surface-variant/50">photo_camera</span>
                <div class="flex flex-col sm:flex-row gap-2">
                    <button type="button"
                            @click="openCamera()"
                            :disabled="loading"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-primary-container text-primary rounded-full text-sm font-medium hover:opacity-80 transition-opacity disabled:opacity-50">
                        <span x-show="!loading" class="material-symbols-outlined text-lg">photo_camera</span>
                        <span x-show="loading" class="material-symbols-outlined text-lg animate-spin">hourglass_empty</span>
                        <span x-text="loading ? 'Abriendo cámara…' : 'Tomar foto'"></span>
                    </button>
                    <button type="button"
                            @click="$refs.fileInput.click()"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-surface-variant text-on-surface-variant rounded-full text-sm font-medium hover:opacity-80 transition-opacity">
                        <span class="material-symbols-outlined text-lg">image</span>
                        Galería
                    </button>
                </div>
            </div>
        </div>

        {{-- -------- Cámara en vivo -------- --}}
        <div x-show="cameraActive" x-cloak
             class="relative rounded-xl overflow-hidden shadow-sm bg-black"
             style="aspect-ratio: {{ $aspect }}; min-height: 200px;">
            <video x-ref="videoEl"
                   autoplay
                   playsinline
                   muted
                   class="w-full h-full object-cover"
                   :style="isFrontCamera ? 'transform: scaleX(-1);' : ''"></video>

            {{-- Camera selector (shown when multiple cameras are available) --}}
            <div x-show="cameras.length > 1" x-cloak
                 class="absolute top-3 right-3 z-10">
                <select x-model="selectedCameraId"
                        @change="switchCamera(selectedCameraId)"
                        class="bg-black/60 backdrop-blur-sm text-white text-sm rounded-full px-3 py-1.5 pr-8 border border-white/20 focus:outline-none focus:ring-2 focus:ring-primary appearance-none cursor-pointer">
                    <template x-for="camera in cameras" :key="camera.deviceId">
                        <option :value="camera.deviceId"
                                x-text="camera.label"
                                :selected="camera.deviceId === selectedCameraId"></option>
                    </template>
                </select>
            </div>

            <div class="absolute bottom-4 left-0 right-0 flex justify-center gap-3">
                <button type="button"
                        @click="capturePhoto()"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary text-on-primary rounded-full text-sm font-semibold shadow-lg hover:opacity-90 transition-opacity">
                    <span class="material-symbols-outlined text-lg">camera</span>
                    Capturar
                </button>
                <button type="button"
                        @click="stopCamera()"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-red-600 text-white rounded-full text-sm font-semibold shadow-lg hover:opacity-90 transition-opacity">
                    <span class="material-symbols-outlined text-lg">close</span>
                    Cancelar
                </button>
            </div>
        </div>

        {{-- -------- Imagen capturada (confirmar / retomar) -------- --}}
        <div x-show="capturing && previewUrl" x-cloak
             class="relative rounded-xl overflow-hidden shadow-sm"
             style="aspect-ratio: {{ $aspect }}; min-height: 200px;">
            <img :src="previewUrl" alt="Foto capturada" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent flex items-end justify-center pb-4 gap-3">
                <button type="button"
                        @click="confirmPhoto()"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-green-600 text-white rounded-full text-sm font-semibold shadow-lg hover:bg-green-700 transition-colors">
                    <span class="material-symbols-outlined text-lg">check</span>
                    Confirmar
                </button>
                <button type="button"
                        @click="retakePhoto()"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-surface-variant/90 text-on-surface-variant rounded-full text-sm font-semibold shadow-lg hover:opacity-80 transition-opacity">
                    <span class="material-symbols-outlined text-lg">refresh</span>
                    Tomar otra
                </button>
            </div>
        </div>

        {{-- -------- Preview final (imagen confirmada) -------- --}}
        <div x-show="previewUrl && !cameraActive && !capturing" x-cloak
             class="relative rounded-xl overflow-hidden shadow-sm"
             style="aspect-ratio: {{ $aspect }}; min-height: 200px;">
            <img :src="previewUrl" alt="Preview" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-black/0 hover:bg-black/30 transition-colors flex items-center justify-center opacity-0 hover:opacity-100">
                <button type="button"
                        @click="clearImage()"
                        class="bg-red-600 text-white px-4 py-2 rounded-full text-sm font-medium flex items-center gap-2 shadow-lg">
                    <span class="material-symbols-outlined text-lg">delete</span>
                    Cambiar foto
                </button>
            </div>
        </div>

        {{-- -------- Error de cámara -------- --}}
        <div x-show="cameraError" x-cloak
             class="mt-3 p-3 bg-red-50 border border-red-200 rounded-xl text-red-600 text-sm flex items-start gap-2">
            <span class="material-symbols-outlined text-lg shrink-0">error</span>
            <span x-text="cameraError"></span>
        </div>

        {{-- -------- Error: foto obligatoria no subida -------- --}}
        <div x-show="showRequiredError" x-cloak x-transition
             class="mt-3 p-3 bg-red-50 border border-red-200 rounded-xl text-red-600 text-sm flex items-start gap-2">
            <span class="material-symbols-outlined text-lg shrink-0">error</span>
            <span>Debes subir una foto de identificación para completar el registro.</span>
        </div>
    </div>

    {{-- Canvas oculto para captura de frames --}}
    <canvas x-ref="canvasEl" class="hidden"></canvas>

    {{-- Input file oculto (galería / fallback cámara) --}}
    <input type="file"
           x-ref="fileInput"
           name="{{ $name }}"
           accept="image/*"
           @change="handleFile($event)"
           class="hidden">

    {{-- Input file oculto con capture (fallback para móviles sin getUserMedia) --}}
    <input type="file"
           x-ref="cameraInput"
           accept="image/*"
           capture="environment"
           @change="handleFile($event)"
           class="hidden">

    {{-- Error de validación de Laravel --}}
    @error($name)
        <p class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</p>
    @enderror
</div>
