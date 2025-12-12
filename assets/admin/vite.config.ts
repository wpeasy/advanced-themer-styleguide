import { defineConfig } from 'vite';
import { svelte } from '@sveltejs/vite-plugin-svelte';
import { resolve } from 'path';

export default defineConfig({
  plugins: [svelte()],
  resolve: {
    alias: {
      '$lib': resolve(__dirname, '../../lib/wpea/svelte-app/src/lib')
    }
  },
  build: {
    outDir: 'dist',
    emptyOutDir: true,
    cssCodeSplit: false,
    rollupOptions: {
      input: 'src/main.ts',
      output: {
        entryFileNames: 'main.js',
        assetFileNames: 'main[extname]',
        format: 'iife',
        name: 'ATStyleGuideAdmin'
      }
    }
  }
});
