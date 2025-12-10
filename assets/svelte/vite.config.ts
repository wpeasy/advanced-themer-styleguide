import { defineConfig } from 'vite';
import { svelte } from '@sveltejs/vite-plugin-svelte';

export default defineConfig({
  plugins: [svelte()],
  build: {
    outDir: 'dist',
    emptyOutDir: true,
    rollupOptions: {
      input: 'src/main.ts',
      output: {
        entryFileNames: 'main.js',
        assetFileNames: '[name][extname]',
        format: 'iife', // WordPress adds ?ver= which breaks ES module imports
        name: 'ATStyleGuide'
      }
    }
  }
});
