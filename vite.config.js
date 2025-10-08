import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/js/addon.js',
                'resources/css/addon.css'
            ],
            publicDirectory: 'resources',
        }),

        vue(),
    ],
    server: {
        host: '0.0.0.0',
        cors: true,
        hmr: {
            host: 'localhost',
        }
    }
});