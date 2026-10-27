import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import path from 'path';

export default defineConfig({
  plugins: [vue()],
  resolve: {
    alias: {
      '@': path.resolve(__dirname, './src')
    }
  },
  server: {
    host: true,
    port: 3000,
    strictPort: false
  },
  preview: {
    host: true,
    port: 4173
  },
  build: {
    target: 'esnext',
    chunkSizeWarningLimit: 1000
  },
  test: {
    environment: 'jsdom',
    globals: true
  }
});

