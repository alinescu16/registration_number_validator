import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/js/addon.js',
                'resources/js/field.js',
                'resources/css/addon.css'
            ],
            publicDirectory: 'resources',
            buildDirectory: 'build',
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