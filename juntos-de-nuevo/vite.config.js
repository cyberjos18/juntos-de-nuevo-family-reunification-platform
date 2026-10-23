import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            // Este proyecto no tiene carpeta public/: el document root real es
            // el directorio padre (findchild/), así que el build debe salir ahí.
            publicDirectory: '../',
            buildDirectory: 'build',
        }),
        tailwindcss(),
    ],
    build: {
        // outDir (../build) queda fuera de la raíz del proyecto, así que Vite
        // no lo vacía por defecto: forzamos el borrado para no acumular
        // assets de builds anteriores con hash distinto.
        emptyOutDir: true,
    },
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
