import { defineConfig } from 'vite';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
  base: '',
  root: import.meta.dirname,
  plugins: [tailwindcss()],
  build: {
    manifest: true,
    outDir: 'assets/build',
    emptyOutDir: true,
    rollupOptions: {
      input: [
        'resources/js/app.js',
        'resources/js/editor.js',
      ],
    },
  },
  server: {
    port: 5173,
    strictPort: true,
    watch: {
      ignored: [
        '**/vendor/**',
        '**/node_modules/**',
        '**/assets/build/**',
        '**/tools/.cache/**',
      ],
    },
  },
});
