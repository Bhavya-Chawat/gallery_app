<script setup>
defineProps({ collection: { type: Object, required: true } });
</script>

<template>
  <div class="max-w-6xl mx-auto p-6">
    <h1 class="text-2xl font-semibold mb-2">{{ collection.title }}</h1>
    <p class="text-gray-600 mb-6">{{ collection.description }}</p>
  </div>
</template>


