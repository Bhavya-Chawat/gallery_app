<script setup>
defineProps({ stats: { type: Object, default: () => ({}) } });
</script>

<template>
  <div class="max-w-6xl mx-auto p-6">
    <h1 class="text-2xl font-semibold mb-4">System Analytics</h1>
    <pre class="bg-gray-100 p-4 rounded text-sm">{{ stats }}</pre>
  </div>
</template>


