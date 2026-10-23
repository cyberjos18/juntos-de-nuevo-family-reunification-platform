/**
 * Image Upload - Alpine.js component for photo upload with
 * live camera access (getUserMedia), capture, and file-upload fallback.
 *
 * Works on:
 *   - Desktop (Chrome, Firefox, Edge, Safari) → webcam
 *   - Mobile Android (Chrome) → rear camera via facingMode: "environment"
 *   - Mobile iOS (Safari 11+) → rear camera via facingMode: "environment"
 *
 * If getUserMedia fails (denied / unsupported) it falls back to
 * <input type="file" accept="image/*" capture="environment">.
 *
 * Usage in Blade:
 *   x-data="imageUpload('foto', false)"
 */
export function imageUpload(fieldName, preferCamera = false, isRequired = false) {
    return {
        // --- State ----------------------------------------------------------
        previewUrl: null,      // dataURL shown to user
        file: null,             // File object ready for form submission
        fieldName: fieldName,
        preferCamera: preferCamera,
        isRequired: isRequired,
        showRequiredError: false,

        // Camera state
        stream: null,           // MediaStream from getUserMedia
        cameraActive: false,    // true while video preview is showing
        capturing: false,       // true between "capturar" and "confirmar"
        cameraError: null,      // error message string or null
        loading: false,         // generic loading spinner

        // Camera selection
        cameras: [],            // Array of { deviceId, label, facingMode }
        selectedCameraId: null, // Currently selected deviceId (null = default/facingMode)

        // References (set via x-ref)
        videoEl: null,
        canvasEl: null,

        /**
         * true si la cámara actualmente seleccionada es la frontal.
         * Se usa para solo espejar la vista previa en ese caso (efecto selfie);
         * la trasera debe verse sin espejar para poder encuadrar bien.
         */
        get isFrontCamera() {
            const current = this.cameras.find(c => c.deviceId === this.selectedCameraId);
            return current ? current.facingMode === 'user' : false;
        },

        // --- Lifecycle ------------------------------------------------------
        init() {
            if (this.isRequired) {
                // Attach validation listener to the closest form
                this.$nextTick(() => {
                    const form = this.$el?.closest('form');
                    if (form) {
                        form.addEventListener('submit', (e) => {
                            if (!this.previewUrl) {
                                e.preventDefault();
                                this.showRequiredError = true;
                                // Auto-hide after 5 seconds
                                setTimeout(() => { this.showRequiredError = false; }, 5000);
                            }
                        });
                    }
                });
            }
        },

        // ==================== CAMERA ========================================

        /**
         * Enumerate available cameras and populate the `cameras` array.
         * Requires an active camera permission in some browsers to get labels.
         * Strategy: try to enumerate first; if labels are blank, we still
         * create friendly entries and the user can switch.
         */
        async enumerateCameras() {
            try {
                const devices = await navigator.mediaDevices.enumerateDevices();
                const videoInputs = devices.filter(d => d.kind === 'videoinput');

                if (videoInputs.length === 0) {
                    this.cameras = [];
                    return;
                }

                this.cameras = videoInputs.map((device, index) => {
                    // Determine facingMode from label or fall back to defaults
                    const label = device.label.toLowerCase();
                    let facingMode = 'environment'; // default assumption
                    let friendlyLabel = device.label;

                    if (!friendlyLabel) {
                        // No label available (permission not yet granted)
                        // Guess based on position or provide generic labels
                        if (index === 0) {
                            friendlyLabel = 'Cámara Trasera';
                            facingMode = 'environment';
                        } else {
                            friendlyLabel = 'Cámara Frontal';
                            facingMode = 'user';
                        }
                    } else if (label.includes('front') || label.includes('frontal') || label.includes('selfie') || label.includes('face')) {
                        facingMode = 'user';
                    } else if (label.includes('back') || label.includes('rear') || label.includes('trasera') || label.includes('world') || label.includes('environment')) {
                        facingMode = 'environment';
                    }

                    return {
                        deviceId: device.deviceId,
                        label: friendlyLabel,
                        facingMode: facingMode,
                    };
                });

                // Solo fijamos una cámara por defecto la primera vez (o si la
                // seleccionada ya no existe, p.ej. se desconectó un dispositivo).
                // En llamadas posteriores respetamos lo que el usuario eligió.
                const stillExists = this.cameras.some(c => c.deviceId === this.selectedCameraId);
                if (!stillExists) {
                    const rearCamera = this.cameras.find(c => c.facingMode === 'environment');
                    if (rearCamera) {
                        this.selectedCameraId = rearCamera.deviceId;
                    } else if (this.cameras.length > 0) {
                        this.selectedCameraId = this.cameras[0].deviceId;
                    }
                }
            } catch (err) {
                console.warn('Error enumerating cameras:', err);
                this.cameras = [];
            }
        },

        /**
         * Request camera access and start live preview in <video>.
         * Falls back to file-picker if permission denied or unsupported.
         * Uses selectedCameraId if set, otherwise falls back to facingMode: environment.
         */
        async openCamera() {
            this.cameraError = null;
            this.capturing = false;

            // Try native camera API
            if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                try {
                    this.loading = true;

                    // Build video constraints: prefer deviceId if one is selected
                    let videoConstraints = {
                        width: { ideal: 1280 },
                        height: { ideal: 720 },
                    };

                    if (this.selectedCameraId) {
                        videoConstraints.deviceId = { exact: this.selectedCameraId };
                    } else {
                        videoConstraints.facingMode = 'environment';
                    }

                    this.stream = await navigator.mediaDevices.getUserMedia({
                        video: videoConstraints,
                        audio: false,
                    });

                    // Enumerate cameras after permission is granted (labels become available)
                    await this.enumerateCameras();

                    // Bind stream to video element (small delay for Alpine ref binding)
                    await this.$nextTick();
                    if (this.$refs.videoEl) {
                        this.videoEl = this.$refs.videoEl;
                        this.videoEl.srcObject = this.stream;
                        await this.videoEl.play();
                    }

                    this.cameraActive = true;
                    this.loading = false;
                    return;
                } catch (err) {
                    this.loading = false;
                    this.cameraError = this._cameraErrorMessage(err);
                    console.warn('getUserMedia failed, falling back to file input:', err);
                }
            }

            // Fallback: native file picker with capture attribute
            this._fallbackCapture();
        },

        /**
         * Switch to a different camera by deviceId.
         * Stops the current stream and restarts with the new device.
         */
        async switchCamera(deviceId) {
            this.selectedCameraId = deviceId;

            // If camera is currently active, restart it with the new device
            if (this.cameraActive) {
                this.stopCamera();
                // Brief delay to ensure tracks are fully released
                await this.$nextTick();
                await this.openCamera();
            }
        },

        /**
         * Capture the current video frame into the hidden <canvas>,
         * stop the camera stream, and produce a compressed JPEG preview.
         */
        capturePhoto() {
            if (!this.videoEl || !this.videoEl.videoWidth) return;

            this.capturing = true;
            this.cameraActive = false;

            const canvas = this.$refs.canvasEl || document.createElement('canvas');
            canvas.width = this.videoEl.videoWidth;
            canvas.height = this.videoEl.videoHeight;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(this.videoEl, 0, 0, canvas.width, canvas.height);

            // Stop camera to free hardware
            this.stopCamera();

            // Compress through image loader (reuses existing pipeline)
            canvas.toBlob((blob) => {
                if (!blob) return;
                const capturedFile = new File([blob], 'foto_capturada.jpg', { type: 'image/jpeg' });
                this.file = capturedFile;
                this.compressAndPreview(capturedFile);
            }, 'image/jpeg', 0.9);
        },

        /**
         * Stop camera stream and reset camera state so user can retake.
         */
        stopCamera() {
            if (this.stream) {
                this.stream.getTracks().forEach((track) => track.stop());
                this.stream = null;
            }
            if (this.videoEl) {
                this.videoEl.srcObject = null;
            }
            this.cameraActive = false;
            this.capturing = false;
        },

        /**
         * Discard the captured photo and restart camera for another attempt.
         */
        retakePhoto() {
            this.previewUrl = null;
            this.file = null;
            this._clearFileInputs();
            this.openCamera();
        },

        /**
         * Accept the captured photo — nothing extra needed; previewUrl + file
         * are already set and the hidden input has been updated.
         */
        confirmPhoto() {
            this.capturing = false;
            // previewUrl and file are already set by capturePhoto → compressAndPreview
        },

        // ==================== FILE HANDLING =================================

        handleFile(event) {
            const input = event.target;
            if (!input.files || !input.files[0]) return;

            const file = input.files[0];

            if (!file.type.startsWith('image/')) {
                alert('Por favor seleccione una imagen válida.');
                return;
            }
            if (file.size > 5 * 1024 * 1024) {
                alert('La imagen no debe superar 5 MB.');
                return;
            }

            this.file = file;
            this.compressAndPreview(file);
        },

        compressAndPreview(file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                const img = new Image();
                img.onload = () => {
                    const maxWidth = 1200;
                    const maxHeight = 1200;

                    if (img.width > maxWidth || img.height > maxHeight || file.size > 2 * 1024 * 1024) {
                        const canvas = document.createElement('canvas');
                        let width = img.width;
                        let height = img.height;

                        if (width > height) {
                            if (width > maxWidth) {
                                height = Math.round((height * maxWidth) / width);
                                width = maxWidth;
                            }
                        } else {
                            if (height > maxHeight) {
                                width = Math.round((width * maxHeight) / height);
                                height = maxHeight;
                            }
                        }

                        canvas.width = width;
                        canvas.height = height;
                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(img, 0, 0, width, height);

                        canvas.toBlob((blob) => {
                            if (blob) {
                                this.file = new File([blob], file.name || 'foto.jpg', { type: 'image/jpeg' });
                                this.updateFileInput(this.file);
                            }
                            this.previewUrl = canvas.toDataURL('image/jpeg', 0.85);
                        }, 'image/jpeg', 0.85);
                    } else {
                        this.previewUrl = e.target.result;
                        this.updateFileInput(file);
                    }
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        },

        updateFileInput(file) {
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            if (this.$refs.fileInput) {
                this.$refs.fileInput.files = dataTransfer.files;
            }
            // Clear validation error when a photo is added
            this.showRequiredError = false;
        },

        clearImage() {
            this.previewUrl = null;
            this.file = null;
            this.cameraActive = false;
            this.capturing = false;
            this.cameraError = null;
            this.stopCamera();
            this._clearFileInputs();
        },

        // ==================== INTERNAL ======================================

        _fallbackCapture() {
            const input = this.$refs.cameraInput || this.$refs.fileInput;
            if (input) {
                input.click();
            }
        },

        _clearFileInputs() {
            if (this.$refs.fileInput)  this.$refs.fileInput.value = '';
            if (this.$refs.cameraInput) this.$refs.cameraInput.value = '';
        },

        _cameraErrorMessage(err) {
            switch (err.name) {
                case 'NotAllowedError':
                    return 'Permiso de cámara denegado. Habilítalo en la configuración del navegador o usa "Galería".';
                case 'NotFoundError':
                    return 'No se encontró ninguna cámara en este dispositivo.';
                case 'NotReadableError':
                    return 'La cámara está en uso por otra aplicación. Ciérrala e intenta de nuevo.';
                case 'OverconstrainedError':
                    return 'La cámara no soporta las opciones solicitadas. Usando modo alternativo…';
                case 'SecurityError':
                    return 'Acceso a cámara bloqueado por seguridad (requiere HTTPS o localhost).';
                default:
                    return 'No se pudo acceder a la cámara: ' + err.message;
            }
        },
    };
}
