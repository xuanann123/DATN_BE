import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
//Quan trọng đối với realtime để nó biến chuyển đổi
export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/js/public.js',
                'resources/css/app.css',
                'resources/js/app.js',
                
                // 'resources/js/chat/presence.js',
            ],
            refresh: true,
        }),
    ],
});
