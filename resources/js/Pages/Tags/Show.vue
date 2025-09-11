<script setup>
defineProps({
  tag: { type: Object, required: true },
  images: { type: Object, required: true }
});
</script>

<template>
  <div class="max-w-6xl mx-auto p-6">
    <h1 class="text-2xl font-semibold mb-4">Tag: {{ tag.name }}</h1>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
      <div v-for="img in images.data" :key="img.id" class="gallery-card">
        <div class="p-3">
          <div class="text-sm font-medium">{{ img.title }}</div>
          <div class="text-xs text-gray-500">{{ img.width }}×{{ img.height }}</div>
        </div>
      </div>
    </div>
  </div>
</template>


