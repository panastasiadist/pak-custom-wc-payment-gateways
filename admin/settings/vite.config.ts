import {defineConfig} from 'vite'
import vue from '@vitejs/plugin-vue'
import {v4wp} from '@kucrut/vite-for-wp';

// https://vitejs.dev/config/
export default defineConfig({
    plugins: [
        vue(),
        v4wp( {
            input: 'src/main.ts', // Optional, defaults to 'src/main.js'.
            outDir: 'dist', // Optional, defaults to 'dist'.
        } ),
    ],
    build: {
        manifest: true,
    }
})
