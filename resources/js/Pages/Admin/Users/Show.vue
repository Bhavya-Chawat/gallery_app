<script setup>
defineProps({ user: { type: Object, required: true } });
</script>

<template>
  <div class="max-w-3xl mx-auto p-6">
    <h1 class="text-2xl font-semibold mb-2">User: {{ user.name }}</h1>
    <p class="text-gray-600">{{ user.email }}</p>
  </div>
</template>


