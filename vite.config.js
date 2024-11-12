import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    resolve: {
        alias: {
            'vue': 'vue/dist/vue.esm-bundler.js',
        },
    },
    plugins: [
        vue(),
        laravel({
            input: [
                'resources/js/app.js',
                'resources/css/app.css',
            ],
            refresh: true,
        }),
    ],
});
