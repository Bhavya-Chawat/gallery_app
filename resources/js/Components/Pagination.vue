<template>
  <nav class="flex items-center justify-between border-t border-gray-200 px-4 py-3 sm:px-6" aria-label="Pagination">
    <div class="hidden sm:block">
      <p class="text-sm text-gray-700">
        Showing
        <span class="font-medium">{{ meta.from || 0 }}</span>
        to
        <span class="font-medium">{{ meta.to || 0 }}</span>
        of
        <span class="font-medium">{{ meta.total || 0 }}</span>
        results
      </p>
    </div>
    <div class="flex flex-1 justify-between sm:justify-end">

      <Link
  v-if="links.find(l => (l.label || '').includes('Previous') || (l.label || '').includes('&laquo;'))?.url"
  :href="links.find(l => (l.label || '').includes('Previous') || (l.label || '').includes('&laquo;')).url"
  class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
>
  <ChevronLeftIcon class="h-5 w-5 mr-1" />
  Previous
</Link>

      <!-- Page Numbers (for larger screens) -->
      <div class="hidden md:flex">
        <button
          v-for="link in pageLinks"
          :key="link.label"
          @click="navigate(link.url)"
          :disabled="!link.url"
          :class="[
            'relative inline-flex items-center px-4 py-2 text-sm font-medium border',
            link.active
              ? 'bg-blue-50 border-blue-500 text-blue-600 z-10'
              : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
            !link.url && 'cursor-not-allowed opacity-50'
          ]"
          v-html="link.label"
        ></button>
      </div>

<!-- Next Button -->  
<Link
  v-if="links.find(l => (l.label || '').includes('Next') || (l.label || '').includes('&raquo;'))?.url"
  :href="links.find(l => (l.label || '').includes('Next') || (l.label || '').includes('&raquo;')).url"
  class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
>
  Next
  <ChevronRightIcon class="h-5 w-5 ml-1" />
</Link>
    </div>
  </nav>
</template>

<script setup>
import { computed } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import {
  ChevronLeftIcon,
  ChevronRightIcon,
} from '@heroicons/vue/20/solid'

const props = defineProps({
  links: {
    type: Object,
    required: true
  },
  meta: {
    type: Object,
    required: true
  }
})

const pageLinks = computed(() => {
  // Check if links exists and is an array
  if (!props.links || !Array.isArray(props.links)) {
    return []
  }
  
  // Filter out previous/next buttons to get just page numbers
  return props.links.filter(link => {
    const label = link.label || ''
    return !label.includes('Previous') && !label.includes('Next') && !label.includes('&laquo;') && !label.includes('&raquo;')
  })
})



const navigate = (url) => {
  if (url) {
    router.visit(url, {
      preserveState: true,
      preserveScroll: true,
    })
  }
}
</script>
