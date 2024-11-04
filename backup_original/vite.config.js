import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'

export default defineConfig({
    plugins: [react()],
    base: '/tarawebsite/',
    build: {
        sourcemap: false,
        outDir: 'dist'
    },
    css: {
        devSourcemap: false
    }
})