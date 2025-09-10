import { defineConfig } from 'vite';
import laravel, { refreshPaths } from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import { fileURLToPath, URL } from 'node:url';

export default defineConfig({
  plugins: [
    laravel({
      input: ['resources/css/app.css', 'resources/js/app.js'],
      refresh: [
        ...refreshPaths,
        'app/Livewire/**',
        'resources/views/**',
        'routes/**',
      ]
    }),
    vue({
      template: {
        transformAssetUrls: {
          base: null,
          includeAbsolute: false,
        },
      },
    }),
  ],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./resources/js', import.meta.url)),
    },
  },
  define: {
    __VUE_OPTIONS_API__: false,
    __VUE_PROD_DEVTOOLS__: false,
  },
  build: {
    rollupOptions: {
      output: {
        manualChunks: {
          vendor: ['vue', '@inertiajs/vue3'],
          ui: ['@headlessui/vue', '@heroicons/vue'],
          utils: ['@vueuse/core', 'date-fns', 'lodash'],
        },
      },
    },
    chunkSizeWarningLimit: 1000,
  },
  server: {
    host: true,
    port: Number(process.env.VITE_PORT) || 5174,
    hmr: {
      host: 'localhost',
    },
  },
  optimizeDeps: {
    include: [
      'vue',
      '@inertiajs/vue3',
      '@headlessui/vue',
      '@heroicons/vue/24/outline',
      '@heroicons/vue/24/solid',
      '@vueuse/core',
    ],
  },
});
