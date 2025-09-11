<template>
  <div class="bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 p-6">
    <div class="flex items-center justify-between mb-6">
      <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Filters</h3>
      <button
        v-if="hasActiveFilters"
        @click="clearAllFilters"
        class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200 transition-colors"
      >
        Clear All
      </button>
    </div>

    <div class="space-y-6">
      <!-- Search -->
      <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
          Search Images
        </label>
        <div class="relative">
          <input
            v-model="localFilters.search"
            type="text"
            placeholder="Search by title, tags, or description..."
            class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            @keyup.enter="applyFilters"
          >
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </div>
          <button
            v-if="localFilters.search"
            @click="localFilters.search = ''; applyFilters()"
            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>

      <!-- Albums -->
      <div v-if="filters.albums && filters.albums.length > 0">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
          Album
        </label>
        <select
          v-model="localFilters.album_id"
          class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          @change="applyFilters"
        >
          <option value="">All Albums</option>
          <option
            v-for="album in filters.albums"
            :key="album.id"
            :value="album.id"
          >
            {{ album.title }} ({{ album.images_count || 0 }})
          </option>
        </select>
      </div>

      <!-- Tags -->
      <div v-if="filters.tags && filters.tags.length > 0">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
          Tags
        </label>
        <div class="space-y-2 max-h-48 overflow-y-auto">
          <label
            v-for="tag in filters.tags"
            :key="tag.slug"
            class="flex items-center space-x-2 cursor-pointer hover:bg-gray-50 dark:hover:bg-slate-700 p-2 rounded-md transition-colors"
          >
            <input
              :value="tag.slug"
              v-model="localFilters.tags"
              type="checkbox"
              class="w-4 h-4 text-blue-600 border-gray-300 dark:border-gray-500 rounded focus:ring-blue-500 focus:ring-2"
              @change="applyFilters"
            >
            <span class="text-sm text-gray-700 dark:text-gray-300 flex-1">{{ tag.name }}</span>
            <span class="text-xs text-gray-500 dark:text-gray-400">({{ tag.count || 0 }})</span>
          </label>
        </div>
      </div>

      <!-- Date Range -->
      <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
          Date Range
        </label>
        <div class="grid grid-cols-1 gap-2">
          <input
            v-model="localFilters.date_from"
            type="date"
            class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            @change="applyFilters"
          >
          <input
            v-model="localFilters.date_to"
            type="date"
            class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            @change="applyFilters"
          >
        </div>
      </div>

      <!-- Sort Options -->
      <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
          Sort By
        </label>
        <select
          v-model="localFilters.sort"
          class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          @change="applyFilters"
        >
          <option value="created_at_desc">Newest First</option>
          <option value="created_at_asc">Oldest First</option>
          <option value="likes_desc">Most Liked</option>
          <option value="views_desc">Most Viewed</option>
          <option value="title_asc">Title A-Z</option>
          <option value="title_desc">Title Z-A</option>
        </select>
      </div>

      <!-- Camera Info -->
      <div v-if="filters.cameras && filters.cameras.length > 0">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
          Camera
        </label>
        <select
          v-model="localFilters.camera"
          class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          @change="applyFilters"
        >
          <option value="">All Cameras</option>
          <option
            v-for="camera in filters.cameras"
            :key="camera.value"
            :value="camera.value"
          >
            {{ camera.label }} ({{ camera.count || 0 }})
          </option>
        </select>
      </div>

      <!-- License Type -->
      <div v-if="filters.licenses && filters.licenses.length > 0">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
          License
        </label>
        <select
          v-model="localFilters.license"
          class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          @change="applyFilters"
        >
          <option value="">All Licenses</option>
          <option
            v-for="license in filters.licenses"
            :key="license.value"
            :value="license.value"
          >
            {{ license.label }} ({{ license.count || 0 }})
          </option>
        </select>
      </div>

      <!-- Color Filter -->
      <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
          Dominant Color
        </label>
        <div class="grid grid-cols-6 gap-2">
          <button
            v-for="color in colorOptions"
            :key="color.value"
            @click="toggleColorFilter(color.value)"
            class="w-8 h-8 rounded-full border-2 transition-all duration-200 hover:scale-110"
            :class="[
              color.class,
              localFilters.color === color.value 
                ? 'border-gray-900 dark:border-white ring-2 ring-blue-500' 
                : 'border-gray-300 dark:border-gray-600'
            ]"
            :title="color.label"
          />
        </div>
      </div>

      <!-- Advanced Filters Toggle -->
      <div>
        <button
          @click="showAdvanced = !showAdvanced"
          class="flex items-center space-x-2 text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200 transition-colors"
        >
          <svg 
            class="w-4 h-4 transition-transform duration-200"
            :class="{ 'rotate-180': showAdvanced }"
            fill="none" 
            stroke="currentColor" 
            viewBox="0 0 24 24"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
          </svg>
          <span>{{ showAdvanced ? 'Hide' : 'Show' }} Advanced Filters</span>
        </button>
      </div>

      <!-- Advanced Filters -->
      <div v-show="showAdvanced" class="space-y-4 pt-4 border-t border-gray-200 dark:border-slate-700">
        <!-- Aspect Ratio -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Aspect Ratio
          </label>
          <div class="grid grid-cols-2 gap-2">
            <button
              v-for="ratio in aspectRatios"
              :key="ratio.value"
              @click="toggleAspectRatio(ratio.value)"
              class="px-3 py-2 text-sm border rounded-lg transition-colors"
              :class="localFilters.aspect_ratio === ratio.value 
                ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300'
                : 'border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-600'"
            >
              {{ ratio.label }}
            </button>
          </div>
        </div>

        <!-- File Size Range -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            File Size
          </label>
          <select
            v-model="localFilters.file_size"
            class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            @change="applyFilters"
          >
            <option value="">Any Size</option>
            <option value="small">Small (&lt; 1MB)</option>
            <option value="medium">Medium (1-5MB)</option>
            <option value="large">Large (5-20MB)</option>
            <option value="xlarge">Extra Large (&gt; 20MB)</option>
          </select>
        </div>

        <!-- Minimum Resolution -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Minimum Resolution
          </label>
          <select
            v-model="localFilters.min_resolution"
            class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            @change="applyFilters"
          >
            <option value="">Any Resolution</option>
            <option value="hd">HD (1280x720)</option>
            <option value="fhd">Full HD (1920x1080)</option>
            <option value="2k">2K (2048x1080)</option>
            <option value="4k">4K (3840x2160)</option>
            <option value="8k">8K (7680x4320)</option>
          </select>
        </div>
      </div>

      <!-- Active Filters Summary -->
      <div v-if="activeFiltersList.length > 0" class="pt-4 border-t border-gray-200 dark:border-slate-700">
        <div class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Active Filters:</div>
        <div class="flex flex-wrap gap-2">
          <span
            v-for="filter in activeFiltersList"
            :key="filter.key"
            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200"
          >
            {{ filter.label }}
            <button
              @click="removeFilter(filter.key)"
              class="ml-1 flex-shrink-0 h-4 w-4 rounded-full inline-flex items-center justify-center text-blue-400 hover:bg-blue-200 dark:hover:bg-blue-800 hover:text-blue-500 focus:outline-none focus:bg-blue-500 focus:text-white"
            >
              <svg class="h-2 w-2" stroke="currentColor" fill="none" viewBox="0 0 8 8">
                <path stroke-linecap="round" stroke-width="1.5" d="m1 1 6 6m0-6-6 6" />
              </svg>
            </button>
          </span>
        </div>
      </div>

      <!-- Apply Filters Button -->
      <div class="pt-4">
        <Button
          @click="applyFilters"
          variant="primary"
          :full-width="true"
          class="w-full"
        >
          Apply Filters
          <template #icon-left>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.707A1 1 0 013 7V4z" />
            </svg>
          </template>
        </Button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, watch } from 'vue'
import Button from '../UI/Button.vue'

const props = defineProps({
  filters: {
    type: Object,
    default: () => ({})
  },
  activeFilters: {
    type: Object,
    default: () => ({})
  }
})

const emit = defineEmits(['apply', 'clear'])

const showAdvanced = ref(false)

const localFilters = reactive({
  search: props.activeFilters.search || '',
  album_id: props.activeFilters.album_id || '',
  tags: props.activeFilters.tags || [],
  date_from: props.activeFilters.date_from || '',
  date_to: props.activeFilters.date_to || '',
  sort: props.activeFilters.sort || 'created_at_desc',
  camera: props.activeFilters.camera || '',
  license: props.activeFilters.license || '',
  color: props.activeFilters.color || '',
  aspect_ratio: props.activeFilters.aspect_ratio || '',
  file_size: props.activeFilters.file_size || '',
  min_resolution: props.activeFilters.min_resolution || ''
})

const colorOptions = [
  { value: '', label: 'Any', class: 'bg-gradient-to-br from-gray-100 to-gray-200 dark:from-slate-600 dark:to-slate-700' },
  { value: 'red', label: 'Red', class: 'bg-red-500' },
  { value: 'orange', label: 'Orange', class: 'bg-orange-500' },
  { value: 'yellow', label: 'Yellow', class: 'bg-yellow-500' },
  { value: 'green', label: 'Green', class: 'bg-green-500' },
  { value: 'blue', label: 'Blue', class: 'bg-blue-500' },
  { value: 'purple', label: 'Purple', class: 'bg-purple-500' },
  { value: 'pink', label: 'Pink', class: 'bg-pink-500' },
  { value: 'black', label: 'Black', class: 'bg-gray-900' },
  { value: 'white', label: 'White', class: 'bg-white border-gray-300' },
  { value: 'brown', label: 'Brown', class: 'bg-amber-800' },
  { value: 'gray', label: 'Gray', class: 'bg-gray-500' }
]

const aspectRatios = [
  { value: 'square', label: '1:1' },
  { value: 'landscape', label: '3:2' },
  { value: 'portrait', label: '2:3' },
  { value: 'wide', label: '16:9' },
  { value: 'ultrawide', label: '21:9' },
  { value: 'panoramic', label: '3:1' }
]

const hasActiveFilters = computed(() => {
  return Object.keys(localFilters).some(key => {
    const value = localFilters[key]
    if (Array.isArray(value)) return value.length > 0
    return value !== '' && value !== null && value !== undefined
  })
})

const activeFiltersList = computed(() => {
  const filters = []
  
  if (localFilters.search) {
    filters.push({ key: 'search', label: `Search: "${localFilters.search}"` })
  }
  
  if (localFilters.album_id) {
    const album = props.filters.albums?.find(a => a.id == localFilters.album_id)
    filters.push({ key: 'album_id', label: `Album: ${album?.title || 'Unknown'}` })
  }
  
  if (localFilters.tags?.length > 0) {
    localFilters.tags.forEach(tagSlug => {
      const tag = props.filters.tags?.find(t => t.slug === tagSlug)
      if (tag) {
        filters.push({ key: `tag_${tagSlug}`, label: `Tag: ${tag.name}` })
      }
    })
  }
  
  if (localFilters.date_from) {
    filters.push({ key: 'date_from', label: `From: ${localFilters.date_from}` })
  }
  
  if (localFilters.date_to) {
    filters.push({ key: 'date_to', label: `To: ${localFilters.date_to}` })
  }
  
  if (localFilters.color) {
    const color = colorOptions.find(c => c.value === localFilters.color)
    filters.push({ key: 'color', label: `Color: ${color?.label || localFilters.color}` })
  }
  
  if (localFilters.aspect_ratio) {
    const ratio = aspectRatios.find(r => r.value === localFilters.aspect_ratio)
    filters.push({ key: 'aspect_ratio', label: `Ratio: ${ratio?.label || localFilters.aspect_ratio}` })
  }
  
  return filters
})

const applyFilters = () => {
  // Clean up empty values
  const cleanFilters = {}
  Object.keys(localFilters).forEach(key => {
    const value = localFilters[key]
    if (Array.isArray(value)) {
      if (value.length > 0) cleanFilters[key] = value
    } else if (value !== '' && value !== null && value !== undefined) {
      cleanFilters[key] = value
    }
  })
  
  emit('apply', cleanFilters)
}

const clearAllFilters = () => {
  Object.keys(localFilters).forEach(key => {
    if (Array.isArray(localFilters[key])) {
      localFilters[key] = []
    } else {
      localFilters[key] = key === 'sort' ? 'created_at_desc' : ''
    }
  })
  
  emit('clear')
}

const removeFilter = (filterKey) => {
  if (filterKey.startsWith('tag_')) {
    const tagSlug = filterKey.replace('tag_', '')
    localFilters.tags = localFilters.tags.filter(t => t !== tagSlug)
  } else {
    if (Array.isArray(localFilters[filterKey])) {
      localFilters[filterKey] = []
    } else {
      localFilters[filterKey] = filterKey === 'sort' ? 'created_at_desc' : ''
    }
  }
  
  applyFilters()
}

const toggleColorFilter = (color) => {
  localFilters.color = localFilters.color === color ? '' : color
  applyFilters()
}

const toggleAspectRatio = (ratio) => {
  localFilters.aspect_ratio = localFilters.aspect_ratio === ratio ? '' : ratio
  applyFilters()
}

// Watch for changes in active filters from parent
watch(() => props.activeFilters, (newFilters) => {
  Object.keys(localFilters).forEach(key => {
    localFilters[key] = newFilters[key] || (key === 'sort' ? 'created_at_desc' : (Array.isArray(localFilters[key]) ? [] : ''))
  })
}, { deep: true, immediate: true })
</script>