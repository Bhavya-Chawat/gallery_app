<template>
  <AppLayout title="Gallery">
    <div class="min-h-screen bg-gray-50 dark:bg-slate-900">
      <!-- Header -->
      <div class="bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl border-b border-gray-200/50 dark:border-slate-700/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ pageTitle }}
              </h1>
              <p v-if="meta.total" class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                {{ formatResultsText(meta) }}
              </p>
            </div>
            
            <!-- View Toggle -->
            <div class="flex items-center space-x-2">
              <div class="flex rounded-lg border border-gray-300 dark:border-slate-600 p-1 bg-white dark:bg-slate-800">
                <button
                  @click="viewMode = 'grid'"
                  :class="[
                    'p-2 rounded-md transition-colors',
                    viewMode === 'grid' 
                      ? 'bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400' 
                      : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200'
                  ]"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                  </svg>
                </button>
                <button
                  @click="viewMode = 'masonry'"
                  :class="[
                    'p-2 rounded-md transition-colors',
                    viewMode === 'masonry' 
                      ? 'bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400' 
                      : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200'
                  ]"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="lg:flex lg:space-x-8">
          <!-- Filters Sidebar -->
          <aside class="lg:w-64 flex-shrink-0 mb-8 lg:mb-0">
            <FiltersPanel
              :filters="availableFilters"
              :active-filters="currentFilters"
              @apply="applyFilters"
              @clear="clearFilters"
            />
          </aside>

          <!-- Main Content -->
          <div class="flex-1 min-w-0">
            <!-- Bulk Actions Bar -->
            <BulkActionsBar
              v-if="selectedImages.length > 0 && canPerformBulkActions"
              :selected-count="selectedImages.length"
              :permissions="bulkPermissions"
              @delete="handleBulkDelete"
              @move-to-album="handleBulkMoveToAlbum"
              @download="handleBulkDownload"
              @clear-selection="clearSelection"
              class="mb-6"
            />

            <!-- Search Results Info -->
            <div v-if="currentFilters.search" class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
              <div class="flex items-center space-x-2">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <span class="text-blue-800 dark:text-blue-200">
                  Search results for "<strong>{{ currentFilters.search }}</strong>"
                </span>
                <button
                  @click="clearSearch"
                  class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
            </div>

            <!-- Image Grid -->
            <ImageGrid
              :images="images.data || images"
              :layout="viewMode"
              :show-selection="canPerformBulkActions"
              :selected-images="selectedImages"
              @image-click="openImage"
              @image-select="toggleImageSelection"
              @image-like="handleImageLike"
              @image-unlike="handleImageUnlike"
            />

            <!-- Empty State -->
            <div
              v-if="(!images.data || images.data.length === 0) && !isLoading"
              class="text-center py-16"
            >
              <div class="mx-auto w-24 h-24 bg-gray-100 dark:bg-slate-700 rounded-full flex items-center justify-center mb-6">
                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
              </div>
              <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                {{ emptyStateTitle }}
              </h3>
              <p class="text-gray-600 dark:text-gray-400 mb-6 max-w-sm mx-auto">
                {{ emptyStateMessage }}
              </p>
              <Button
                v-if="hasActiveFilters"
                @click="clearFilters"
                variant="outline"
              >
                Clear Filters
              </Button>
              <Button
                v-else-if="$page.props.auth?.user"
                href="/upload"
                variant="primary"
              >
                Upload Your First Image
              </Button>
            </div>

            <!-- Loading State -->
            <div v-if="isLoading" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
              <div
                v-for="n in 12"
                :key="n"
                class="aspect-square bg-gray-200 dark:bg-slate-700 rounded-lg animate-pulse"
              />
            </div>

            <!-- Pagination -->
            <div v-if="images.last_page > 1" class="mt-8">
              <Pagination
                :pagination="images"
                @change-page="changePage"
                @change-per-page="changePerPage"
                :show-per-page-selector="true"
              />
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import FiltersPanel from '@/Components/Gallery/FiltersPanel.vue'
import ImageGrid from '@/Components/Gallery/ImageGrid.vue'
import BulkActionsBar from '@/Components/Gallery/BulkActionsBar.vue'
import Pagination from '@/Components/UI/Pagination.vue'
import Button from '@/Components/UI/Button.vue'


const props = defineProps({
  images: {
    type: Object,
    required: true
  },
  availableFilters: {
    type: Object,
    default: () => ({})
  },
  currentFilters: {
    type: Object,
    default: () => ({})
  },
  meta: {
    type: Object,
    default: () => ({})
  }
})

const page = usePage()
const viewMode = ref('grid')
const selectedImages = ref([])
const isLoading = ref(false)

// Computed properties
const pageTitle = computed(() => {
  if (props.currentFilters.search) {
    return `Search Results`
  }
  if (props.currentFilters.album_id) {
    return `Album: ${props.currentFilters.album_name || 'Unknown'}`
  }
  if (props.currentFilters.tag) {
    return `Tag: ${props.currentFilters.tag}`
  }
  return 'Browse Gallery'
})

const hasActiveFilters = computed(() => {
  return Object.keys(props.currentFilters).some(key => 
    props.currentFilters[key] && key !== 'page' && key !== 'per_page'
  )
})

const emptyStateTitle = computed(() => {
  if (props.currentFilters.search) {
    return 'No images found'
  }
  if (hasActiveFilters.value) {
    return 'No images match your filters'
  }
  return 'No images yet'
})

const emptyStateMessage = computed(() => {
  if (props.currentFilters.search) {
    return 'Try adjusting your search terms or browse all images.'
  }
  if (hasActiveFilters.value) {
    return 'Try removing some filters or browse all images.'
  }
  return 'Be the first to share beautiful photography with the community.'
})

const canPerformBulkActions = computed(() => {
  return page.props.auth?.user && 
    (page.props.auth.user.role === 'admin' || page.props.auth.user.role === 'moderator')
})

const bulkPermissions = computed(() => ({
  canDelete: canPerformBulkActions.value,
  canMove: canPerformBulkActions.value,
  canDownload: true,
  canEdit: canPerformBulkActions.value
}))

// Methods
const formatResultsText = (meta) => {
  const { from, to, total } = meta
  if (!total) return 'No results'
  return `Showing ${from || 1}-${to || total} of ${total} images`
}

const applyFilters = (filters) => {
  const params = {
    ...props.currentFilters,
    ...filters,
    page: 1 // Reset to first page when applying filters
  }
  
  // Remove empty filters
  Object.keys(params).forEach(key => {
    if (!params[key] || params[key] === '') {
      delete params[key]
    }
  })
  
  router.get('/gallery', params, {
    preserveState: true,
    replace: true,
    onStart: () => isLoading.value = true,
    onFinish: () => isLoading.value = false
  })
}

const clearFilters = () => {
  router.get('/gallery', {}, {
    preserveState: true,
    replace: true,
    onStart: () => isLoading.value = true,
    onFinish: () => isLoading.value = false
  })
}

const clearSearch = () => {
  const params = { ...props.currentFilters }
  delete params.search
  delete params.page
  
  router.get('/gallery', params, {
    preserveState: true,
    replace: true,
    onStart: () => isLoading.value = true,
    onFinish: () => isLoading.value = false
  })
}

const changePage = (page) => {
  router.get('/gallery', {
    ...props.currentFilters,
    page
  }, {
    preserveState: true,
    replace: true,
    onStart: () => isLoading.value = true,
    onFinish: () => isLoading.value = false
  })
}

const changePerPage = (perPage) => {
  router.get('/gallery', {
    ...props.currentFilters,
    per_page: perPage,
    page: 1
  }, {
    preserveState: true,
    replace: true,
    onStart: () => isLoading.value = true,
    onFinish: () => isLoading.value = false
  })
}

const openImage = (image) => {
  router.visit(`/gallery/images/${image.id}`)
}

const toggleImageSelection = (data) => {
  const { image, selected } = data
  
  if (selected) {
    if (!selectedImages.value.some(img => img.id === image.id)) {
      selectedImages.value.push(image)
    }
  } else {
    selectedImages.value = selectedImages.value.filter(img => img.id !== image.id)
  }
}

const clearSelection = () => {
  selectedImages.value = []
}

const handleImageLike = async (image) => {
  try {
    await axios.post(`/api/v1/images/${image.id}/like`)
    
    // Update image in the current data
    const images = props.images.data || props.images
    const index = images.findIndex(img => img.id === image.id)
    if (index !== -1) {
      images[index].is_liked = true
      images[index].likes_count = (images[index].likes_count || 0) + 1
    }
  } catch (error) {
    console.error('Failed to like image:', error)
    // Could show toast notification here
  }
}

const handleImageUnlike = async (image) => {
  try {
    await axios.delete(`/api/v1/images/${image.id}/like`)
    
    // Update image in the current data
    const images = props.images.data || props.images
    const index = images.findIndex(img => img.id === image.id)
    if (index !== -1) {
      images[index].is_liked = false
      images[index].likes_count = Math.max((images[index].likes_count || 1) - 1, 0)
    }
  } catch (error) {
    console.error('Failed to unlike image:', error)
    // Could show toast notification here
  }
}

const handleBulkDelete = async (imageIds) => {
  if (!confirm(`Are you sure you want to delete ${imageIds.length} images? This action cannot be undone.`)) {
    return
  }
  
  try {
    await axios.post('/api/v1/images/bulk', {
      action: 'delete',
      image_ids: imageIds
    })
    
    // Refresh the page to show updated results
    router.reload()
    clearSelection()
  } catch (error) {
    console.error('Failed to delete images:', error)
    // Could show toast notification here
  }
}

const handleBulkMoveToAlbum = async (data) => {
  const { imageIds, albumId } = data
  
  try {
    await axios.post('/api/v1/images/bulk', {
      action: 'move_to_album',
      image_ids: imageIds,
      album_id: albumId
    })
    
    // Refresh the page to show updated results
    router.reload()
    clearSelection()
  } catch (error) {
    console.error('Failed to move images:', error)
    // Could show toast notification here
  }
}

const handleBulkDownload = async (imageIds) => {
  try {
    // This would typically create a zip file or initiate multiple downloads
    const response = await axios.post('/api/v1/images/bulk', {
      action: 'download',
      image_ids: imageIds
    }, {
      responseType: 'blob'
    })
    
    // Create download link
    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', `images-${Date.now()}.zip`)
    document.body.appendChild(link)
    link.click()
    link.remove()
    
    clearSelection()
  } catch (error) {
    console.error('Failed to download images:', error)
    // Could show toast notification here
  }
}

// Load saved view mode from localStorage
onMounted(() => {
  const savedViewMode = localStorage.getItem('gallery-view-mode')
  if (savedViewMode && ['grid', 'masonry'].includes(savedViewMode)) {
    viewMode.value = savedViewMode
  }
})

// Save view mode to localStorage when changed
watch(viewMode, (newMode) => {
  localStorage.setItem('gallery-view-mode', newMode)
})
</script>