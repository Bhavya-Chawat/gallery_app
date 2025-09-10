<template>
  <div class="bg-white shadow-sm rounded-lg p-6">
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-lg font-medium text-gray-900">Filters</h3>
      <button
        v-if="hasActiveFilters"
        @click="clearFilters"
        class="text-sm text-blue-600 hover:text-blue-500"
      >
        Clear all
      </button>
    </div>

    <div class="space-y-4">
      <!-- Search -->
      <div>
        <label class="block text-sm font-medium text-gray-700">Search</label>
        <div class="mt-1 relative">
          <MagnifyingGlassIcon class="absolute left-3 top-3 h-4 w-4 text-gray-400" />
          <input
            v-model="localFilters.search"
            type="text"
            placeholder="Search..."
            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
          />
        </div>
      </div>

      <!-- Category/Type Filter -->
      <div v-if="categories && categories.length">
        <label class="block text-sm font-medium text-gray-700">Category</label>
        <select
          v-model="localFilters.category"
          class="mt-1 block w-full border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
        >
          <option value="">All Categories</option>
          <option
            v-for="category in categories"
            :key="category.value"
            :value="category.value"
          >
            {{ category.label }}
          </option>
        </select>
      </div>

      <!-- Privacy Filter -->
      <div>
        <label class="block text-sm font-medium text-gray-700">Privacy</label>
        <select
          v-model="localFilters.privacy"
          class="mt-1 block w-full border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
        >
          <option value="">All</option>
          <option value="public">Public</option>
          <option value="unlisted">Unlisted</option>
          <option value="private">Private</option>
        </select>
      </div>

      <!-- Date Range -->
      <div>
        <label class="block text-sm font-medium text-gray-700">Date Range</label>
        <select
          v-model="localFilters.dateRange"
          class="mt-1 block w-full border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
        >
          <option value="">All Time</option>
          <option value="today">Today</option>
          <option value="week">This Week</option>
          <option value="month">This Month</option>
          <option value="year">This Year</option>
        </select>
      </div>

      <!-- Sort Options -->
      <div>
        <label class="block text-sm font-medium text-gray-700">Sort By</label>
        <select
          v-model="localFilters.sortBy"
          class="mt-1 block w-full border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
        >
          <option value="created_at">Date Created</option>
          <option value="updated_at">Last Updated</option>
          <option value="title">Title</option>
          <option value="views_count">Views</option>
          <option value="likes_count">Likes</option>
        </select>
      </div>

      <!-- Sort Direction -->
      <div>
        <label class="block text-sm font-medium text-gray-700">Order</label>
        <select
          v-model="localFilters.sortOrder"
          class="mt-1 block w-full border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
        >
          <option value="desc">Newest First</option>
          <option value="asc">Oldest First</option>
        </select>
      </div>
    </div>

    <!-- Apply Button -->
    <div class="mt-6">
      <button
        @click="applyFilters"
        class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors"
      >
        Apply Filters
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { MagnifyingGlassIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  filters: {
    type: Object,
    default: () => ({})
  },
  categories: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['update:filters'])

const localFilters = ref({
  search: '',
  category: '',
  privacy: '',
  dateRange: '',
  sortBy: 'created_at',
  sortOrder: 'desc',
  ...props.filters
})

const hasActiveFilters = computed(() => {
  return Object.values(localFilters.value).some(value => 
    value !== '' && value !== 'created_at' && value !== 'desc'
  )
})

const clearFilters = () => {
  localFilters.value = {
    search: '',
    category: '',
    privacy: '',
    dateRange: '',
    sortBy: 'created_at',
    sortOrder: 'desc'
  }
  applyFilters()
}

const applyFilters = () => {
  emit('update:filters', { ...localFilters.value })
}

// Auto-apply search filter with debounce
let searchTimeout = null
watch(() => localFilters.value.search, () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    applyFilters()
  }, 300)
})
</script>
