import Alpine from 'alpinejs';
import { imageUpload } from './image-upload';
import { recogidaForm } from './recogida-form';

window.Alpine = Alpine;

// Register Alpine data components
Alpine.data('imageUpload', imageUpload);
Alpine.data('recogidaForm', recogidaForm);

Alpine.start();
