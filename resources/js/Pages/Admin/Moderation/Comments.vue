<script setup>
defineProps({ comments: { type: Object, default: () => ({ data: [] }) } });
</script>

<template>
  <div class="max-w-6xl mx-auto p-6">
    <h1 class="text-2xl font-semibold mb-4">Moderate Comments</h1>
    <div v-for="c in comments.data" :key="c.id" class="p-3 border-b">
      <div class="text-sm">{{ c.body }}</div>
    </div>
  </div>
</template>


