<script setup>
defineProps({
  tags: { type: Array, default: () => [] }
});
</script>

<template>
  <div class="max-w-6xl mx-auto p-6">
    <h1 class="text-2xl font-semibold mb-4">Popular Tags</h1>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
      <div v-for="tag in tags" :key="tag.id" class="px-3 py-2 bg-gray-100 rounded">
        <span class="text-gray-800">#{{ tag.name }}</span>
      </div>
    </div>
  </div>
  </template>


