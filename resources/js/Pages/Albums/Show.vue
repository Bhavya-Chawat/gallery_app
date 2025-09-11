<script setup>
defineProps({
  album: { type: Object, default: () => ({}) },
  images: { type: Array, default: () => [] },
  auth: { type: Object, default: () => ({ user: null, roles: [] }) },
  errors: { type: Object, default: () => ({}) },
});
</script>

<template>
  <div class="max-w-6xl mx-auto p-6">
    <h1 class="text-2xl font-semibold mb-4">{{ album.title }}</h1>
    <p class="text-gray-600 mb-6">{{ album.description }}</p>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
      <div v-for="img in images" :key="img.id" class="gallery-card">
        <div class="p-3">
          <div class="text-sm font-medium">{{ img.title }}</div>
        </div>
      </div>
    </div>
  </div>
</template>


