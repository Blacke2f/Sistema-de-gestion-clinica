/**
 * SISGECLIN — Configuración de Vite
 *
 * El sistema usa CSS y JS propios en public/css/.
 * Vite se mantiene como parte del stack de Laravel
 * pero no compila assets activos en este proyecto.
 *
 * @version 2.5
 */
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: false,
        }),
    ],
});
