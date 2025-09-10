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
      <button
        v-if="links.prev"
        @click="navigate(links.prev)"
        class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
      >
        <ChevronLeftIcon class="h-5 w-5 mr-1" />
        Previous
      </button>
      <div v-else class="relative inline-flex items-center rounded-md border border-gray-300 bg-gray-100 px-4 py-2 text-sm font-medium text-gray-400 cursor-not-allowed">
        <ChevronLeftIcon class="h-5 w-5 mr-1" />
        Previous
      </div>

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

      <button
        v-if="links.next"
        @click="navigate(links.next)"
        class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
      >
        Next
        <ChevronRightIcon class="h-5 w-5 ml-1" />
      </button>
      <div v-else class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-gray-100 px-4 py-2 text-sm font-medium text-gray-400 cursor-not-allowed">
        Next
        <ChevronRightIcon class="h-5 w-5 ml-1" />
      </div>
    </div>
  </nav>
</template>

<script setup>
import { computed } from 'vue'
import { router } from '@inertiajs/vue3'
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
  if (!props.links.data) return []
  
  return props.links.data.filter(link => {
    return link.label !== '&laquo; Previous' && link.label !== 'Next &raquo;'
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
