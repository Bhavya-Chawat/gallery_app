<template>
  <div v-if="pagination && pagination.last_page > 1" class="flex items-center justify-between px-4 py-3 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg">
    <!-- Mobile View -->
    <div class="flex justify-between flex-1 sm:hidden">
      <button
        @click="changePage(pagination.current_page - 1)"
        :disabled="pagination.current_page <= 1"
        class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-md hover:bg-gray-50 dark:hover:bg-slate-700 disabled:opacity-50 disabled:cursor-not-allowed"
      >
        Previous
      </button>
      <span class="text-sm text-gray-700 dark:text-gray-200">
        Page {{ pagination.current_page }} of {{ pagination.last_page }}
      </span>
      <button
        @click="changePage(pagination.current_page + 1)"
        :disabled="pagination.current_page >= pagination.last_page"
        class="relative ml-3 inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-md hover:bg-gray-50 dark:hover:bg-slate-700 disabled:opacity-50 disabled:cursor-not-allowed"
      >
        Next
      </button>
    </div>

    <!-- Desktop View -->
    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
      <!-- Results Info -->
      <div>
        <p class="text-sm text-gray-700 dark:text-gray-300">
          Showing
          <span class="font-medium">{{ pagination.from || 0 }}</span>
          to
          <span class="font-medium">{{ pagination.to || 0 }}</span>
          of
          <span class="font-medium">{{ pagination.total || 0 }}</span>
          results
        </p>
      </div>

      <!-- Page Numbers -->
      <div>
        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
          <!-- Previous Button -->
          <button
            @click="changePage(pagination.current_page - 1)"
            :disabled="pagination.current_page <= 1"
            class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-sm font-medium text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-slate-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
          >
            <span class="sr-only">Previous</span>
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
            </svg>
          </button>

          <!-- Page Numbers -->
          <template v-for="page in visiblePages" :key="page">
            <button
              v-if="page !== '...'"
              @click="changePage(page)"
              :class="[
                'relative inline-flex items-center px-4 py-2 border text-sm font-medium transition-colors',
                page === pagination.current_page
                  ? 'z-10 bg-gradient-to-r from-blue-600 via-cyan-500 to-blue-400 border-blue-500 text-white'
                  : 'bg-white dark:bg-slate-800 border-gray-300 dark:border-slate-600 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-slate-700'
              ]"
            >
              {{ page }}
            </button>
            <span
              v-else
              class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-sm font-medium text-gray-700 dark:text-gray-300"
            >
              ...
            </span>
          </template>

          <!-- Next Button -->
          <button
            @click="changePage(pagination.current_page + 1)"
            :disabled="pagination.current_page >= pagination.last_page"
            class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-sm font-medium text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-slate-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
          >
            <span class="sr-only">Next</span>
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
            </svg>
          </button>
        </nav>
      </div>

      <!-- Per Page Selector -->
      <div v-if="showPerPageSelector" class="flex items-center space-x-2">
        <span class="text-sm text-gray-700 dark:text-gray-300">Show:</span>
        <select
          :value="pagination.per_page"
          @change="changePerPage($event.target.value)"
          class="border border-gray-300 dark:border-slate-600 rounded-md text-sm bg-white dark:bg-slate-800 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
        >
          <option v-for="option in perPageOptions" :key="option" :value="option">
            {{ option }}
          </option>
        </select>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  pagination: {
    type: Object,
    required: true,
    validator: (pagination) => {
      return pagination && 
        typeof pagination.current_page === 'number' && 
        typeof pagination.last_page === 'number'
    }
  },
  maxVisible: {
    type: Number,
    default: 7
  },
  showPerPageSelector: {
    type: Boolean,
    default: false
  },
  perPageOptions: {
    type: Array,
    default: () => [10, 25, 50, 100]
  }
})

const emit = defineEmits(['change-page', 'change-per-page'])

const changePage = (page) => {
  if (page >= 1 && page <= props.pagination.last_page && page !== props.pagination.current_page) {
    emit('change-page', page)
  }
}

const changePerPage = (perPage) => {
  emit('change-per-page', parseInt(perPage))
}

const visiblePages = computed(() => {
  const current = props.pagination.current_page
  const last = props.pagination.last_page
  const max = props.maxVisible
  
  if (last <= max) {
    return Array.from({ length: last }, (_, i) => i + 1)
  }
  
  const pages = []
  const half = Math.floor(max / 2)
  
  // Always show first page
  pages.push(1)
  
  let start = Math.max(2, current - half)
  let end = Math.min(last - 1, current + half)
  
  // Adjust if we're near the beginning
  if (current <= half + 1) {
    end = Math.min(last - 1, max - 1)
  }
  
  // Adjust if we're near the end
  if (current >= last - half) {
    start = Math.max(2, last - max + 2)
  }
  
  // Add ellipsis after first page if needed
  if (start > 2) {
    pages.push('...')
  }
  
  // Add middle pages
  for (let i = start; i <= end; i++) {
    pages.push(i)
  }
  
  // Add ellipsis before last page if needed
  if (end < last - 1) {
    pages.push('...')
  }
  
  // Always show last page (unless it's the first page)
  if (last > 1) {
    pages.push(last)
  }
  
  return pages
})
</script>