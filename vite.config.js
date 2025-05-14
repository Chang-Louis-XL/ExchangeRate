import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.jsx'],
            refresh: true,
        }),
        react({
            // 使用默認配置
            fastRefresh: true,
        }),
    ],
    resolve: {
        extensions: ['.js', '.jsx']
    },
    optimizeDeps: {
        include: ['react', 'react-dom']
    }
});