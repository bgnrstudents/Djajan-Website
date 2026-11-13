import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/css/admin.css', 'resources/css/login.css', 'resources/js/app.js', 'resources/js/home.js'],
            refresh: true,
        }),

        tailwindcss(),
    ],
});