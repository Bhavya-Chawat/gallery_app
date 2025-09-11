<script setup>
const props = defineProps({
  links: {
    type: Array,
    required: true
  }
})
</script>

<template>
  <div v-if="links.length > 3" class="flex flex-wrap justify-center gap-1">
    <template v-for="(link, key) in links" :key="key">
      <div
        v-if="link.url === null"
        class="px-4 py-2 text-sm text-gray-500 bg-white border rounded dark:bg-gray-800 dark:border-gray-600"
        v-html="link.label"
      />
      <Link
        v-else
        :href="link.url"
        class="px-4 py-2 text-sm border rounded"
        :class="{'bg-blue-600 text-white': link.active}"
        v-html="link.label"
      />
    </template>
  </div>
</template>