import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/images/favicons/favicon-96x96.png',
                'resources/images/favicons/favicon.svg',
                'resources/images/favicons/favicon.ico',
                'resources/images/favicons/apple-touch-icon.png',
                'resources/images/favicons/web-app-manifest-192x192.png',
                'resources/images/favicons/web-app-manifest-512x512.png',
                'resources/images/favicons/site.webmanifest'

            ],
            refresh: true,
        }),
    ],
});
