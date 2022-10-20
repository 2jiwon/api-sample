import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';

const env = loadEnv('', process.cwd(), '');

export default defineConfig({
    server: {
        host: env.SERVER_IP,
        port: env.VITE_PORT,
        strictPort: true,
    },
    plugins: [
        laravel({
            input: 'resources/js/app.jsx',
            refresh: true,
        }),
        react(),
    ],
});
