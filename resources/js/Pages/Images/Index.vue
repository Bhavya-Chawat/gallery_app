<template>
  <AppLayout>
    <Head :title="pageTitle" />

    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ pageTitle }}
          </h2>
          <p class="text-sm text-gray-600 mt-1">
            {{ pageDescription }}
          </p>
        </div>
        <div class="flex items-center space-x-4">
          <Link
            v-if="canUpload"
            :href="route('upload')"
            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700"
          >
            <PlusIcon class="h-4 w-4 mr-2" />
            Upload Images
          </Link>
        </div>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Filters -->
        <div class="bg-white shadow-sm rounded-lg mb-6">
          <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
              <!-- Search -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <input
                  v-model="searchForm.search"
                  @keyup.enter="search"
                  type="text"
                  :placeholder="isMyImages ? 'Search your images...' : 'Search images...'"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                />
              </div>

              <!-- Album Filter -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Album</label>
                <select
                  v-model="searchForm.album"
                  @change="search"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                >
                  <option value="">All Albums</option>
                  <option value="none">No Album</option>
                  <option v-for="album in albums" :key="album.id" :value="album.id">
                    {{ album.title }}
                  </option>
                </select>
              </div>

              <!-- Privacy Filter (My Images only) -->
              <div v-if="isMyImages">
                <label class="block text-sm font-medium text-gray-700 mb-2">Privacy</label>
                <select
                  v-model="searchForm.privacy"
                  @change="search"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                >
                  <option value="">All Privacy Levels</option>
                  <option value="public">Public</option>
                  <option value="unlisted">Unlisted</option>
                  <option value="private">Private</option>
                </select>
              </div>

              <!-- Published Filter (My Images only) -->
              <div v-if="isMyImages">
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select
                  v-model="searchForm.published"
                  @change="search"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                >
                  <option value="">All Statuses</option>
                  <option value="published">Published</option>
                  <option value="unpublished">Unpublished</option>
                </select>
              </div>

              <!-- Clear Filters -->
              <div class="flex items-end">
                <button
                  @click="clearFilters"
                  class="w-full px-4 py-2 text-sm text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50"
                >
                  Clear Filters
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Bulk Actions Bar (My Images only) -->
        <div v-if="isMyImages && selectedImages.length > 0" class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
          <div class="flex items-center justify-between">
            <span class="text-sm font-medium text-blue-900">
              {{ selectedImages.length }} image(s) selected
            </span>
            <div class="flex items-center space-x-2">
              <select
                v-model="bulkAction"
                class="text-sm border border-blue-300 rounded-md px-3 py-1 focus:ring-blue-500 focus:border-blue-500"
              >
                <option value="">Select action...</option>
                <option value="publish">Publish</option>
                <option value="unpublish">Unpublish</option>
                <option value="privacy">Change Privacy</option>
                <option value="move_to_album">Move to Album</option>
                <option value="delete" class="text-red-600">Delete</option>
              </select>
              
              <select
                v-if="bulkAction === 'privacy'"
                v-model="privacyLevel"
                class="text-sm border border-blue-300 rounded-md px-3 py-1 focus:ring-blue-500 focus:border-blue-500"
              >
                <option value="public">Public</option>
                <option value="unlisted">Unlisted</option>
                <option value="private">Private</option>
              </select>

              <select
                v-if="bulkAction === 'move_to_album'"
                v-model="selectedAlbumId"
                class="text-sm border border-blue-300 rounded-md px-3 py-1 focus:ring-blue-500 focus:border-blue-500"
              >
                <option value="">Remove from Album</option>
                <option v-for="album in albums" :key="album.id" :value="album.id">
                  {{ album.title }}
                </option>
              </select>

              <button
                @click="executeBulkAction"
                :disabled="!bulkAction || bulkActionLoading"
                class="px-4 py-1 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700 disabled:opacity-50"
              >
                {{ bulkActionLoading ? 'Processing...' : 'Apply' }}
              </button>
              
              <button
                @click="clearSelection"
                class="px-4 py-1 bg-gray-300 text-gray-700 text-sm rounded-md hover:bg-gray-400"
              >
                Clear
              </button>
            </div>
          </div>
        </div>

        <!-- Images Grid -->
        <div class="bg-white shadow-sm rounded-lg">
          <div class="p-6">
            <!-- View Controls -->
            <div class="flex items-center justify-between mb-6">
              <div class="flex items-center space-x-4">
                <span class="text-sm text-gray-500">
                  {{ images.meta?.total || 0 }} image{{ (images.meta?.total !== 1) ? 's' : '' }}
                </span>
                
                <select
                  v-model="searchForm.sort"
                  @change="search"
                  class="text-sm border border-gray-300 rounded-md px-3 py-1 focus:ring-blue-500 focus:border-blue-500"
                >
                  <option value="created_at">Recently Added</option>
                  <option value="title">Title</option>
                  <option value="views">Most Viewed</option>
                  <option value="size">File Size</option>
                </select>

                <select
                  v-model="searchForm.direction"
                  @change="search"
                  class="text-sm border border-gray-300 rounded-md px-3 py-1 focus:ring-blue-500 focus:border-blue-500"
                >
                  <option value="desc">Descending</option>
                  <option value="asc">Ascending</option>
                </select>
              </div>

              <!-- Select All (My Images only) -->
              <div v-if="isMyImages && images.data?.length > 0" class="flex items-center space-x-2">
                <input
                  id="select-all"
                  v-model="selectAll"
                  @change="toggleSelectAll"
                  type="checkbox"
                  class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                />
                <label for="select-all" class="text-sm text-gray-700">Select All</label>
              </div>
            </div>

            <!-- Images Grid -->
            <div v-if="images.data && images.data.length > 0" 
                 class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
              <div
                v-for="image in images.data"
                :key="image.id"
                class="relative group bg-gray-100 rounded-lg overflow-hidden aspect-square"
              >
                <!-- Selection Checkbox (My Images only) -->
                <div v-if="isMyImages" class="absolute top-2 left-2 z-10">
                  <input
                    v-model="selectedImages"
                    :value="image.id"
                    type="checkbox"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded bg-white/80"
                  />
                </div>

                <!-- Privacy Badge (My Images only) -->
                <div v-if="isMyImages" class="absolute top-2 right-2 z-10">
                  <span
                    class="px-2 py-1 text-xs font-medium rounded"
                    :class="{
                      'bg-green-100 text-green-800': image.privacy === 'public',
                      'bg-yellow-100 text-yellow-800': image.privacy === 'unlisted',
                      'bg-red-100 text-red-800': image.privacy === 'private'
                    }"
                  >
                    {{ image.privacy }}
                  </span>
                </div>

                <!-- Published Status Badge (My Images only) -->
                <div v-if="isMyImages && !image.is_published" class="absolute bottom-2 left-2 z-10">
                  <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded">
                    Draft
                  </span>
                </div>

                <!-- Image -->
                <img
                  :src="getImageUrl(image)"
                  :alt="image.alt_text || image.title"
                  class="w-full h-full object-cover cursor-pointer hover:scale-105 transition-transform"
                  @click="openLightbox(image)"
                />

                <!-- Title overlay -->
                <div class="absolute bottom-0 left-0 right-0 bg-black/50 text-white p-2 opacity-0 group-hover:opacity-100 transition-opacity">
                  <p class="text-xs font-medium truncate">{{ image.title || 'Untitled' }}</p>
                </div>
              </div>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-12">
              <PhotoIcon class="mx-auto h-12 w-12 text-gray-400" />
              <h3 class="mt-2 text-sm font-medium text-gray-900">{{ getEmptyMessage() }}</h3>
              <div v-if="canUpload && isMyImages" class="mt-6">
                <Link
                  :href="route('upload')"
                  class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700"
                >
                  <PlusIcon class="-ml-1 mr-2 h-5 w-5" />
                  Upload Images
                </Link>
              </div>
            </div>

            <!-- Pagination -->
            <Pagination
              v-if="images.data && images.data.length > 0 && images.meta"
              :links="images.links || []"
              :meta="images.meta || {}"
              class="mt-6"
            />
          </div>
        </div>
      </div>
    </div>

    <!-- Lightbox -->
    <Lightbox
      v-if="showLightbox"
      :images="images.data"
      :initial-index="currentImageIndex"
      @close="showLightbox = false"
    />
  </AppLayout>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import route from 'ziggy-js'
import { PlusIcon, PhotoIcon } from '@heroicons/vue/24/outline'

import AppLayout from '@/Layouts/AppLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import Lightbox from '@/Components/Gallery/Lightbox.vue'

const page = usePage()

const props = defineProps({
  images: { type: Object, default: () => ({ data: [], links: [], meta: {} }) },
  albums: { type: Array, default: () => [] },
  filters: { type: Object, default: () => ({}) },
  canUpload: { type: Boolean, default: false },
  isMyImages: { type: Boolean, default: false },
})

// FIXED: Reactive state without conflicting parameters
const searchForm = reactive({
  search: props.filters.search || '',
  album: props.filters.album || '',
  privacy: props.filters.privacy || '',
  published: props.filters.published || '',
  date_from: props.filters.date_from || '',
  date_to: props.filters.date_to || '',
  sort: props.filters.sort || 'created_at',
  direction: props.filters.direction || 'desc',
})

// Bulk operations (My Images only)
const selectedImages = ref([])
const selectAll = ref(false)
const bulkAction = ref('')
const privacyLevel = ref('public')
const selectedAlbumId = ref('')
const bulkActionLoading = ref(false)

// Lightbox
const showLightbox = ref(false)
const currentImageIndex = ref(0)

// Computed properties
const pageTitle = computed(() => {
  return props.isMyImages ? 'My Images' : 'Image Gallery'
})

const pageDescription = computed(() => {
  return props.isMyImages 
    ? 'Manage your uploaded images and privacy settings'
    : 'Discover and explore amazing photography'
})

const hasFilters = computed(() => {
  return Object.entries(searchForm).some(([key, value]) => 
    value && value !== '' && !['sort', 'direction'].includes(key)
  )
})

// FIXED: Methods with proper route handling
const search = () => {
  const cleanForm = Object.fromEntries(
    Object.entries(searchForm).filter(([key, value]) => value !== '')
  )
  
  // FIXED: Use correct route based on context
  const routeName = props.isMyImages ? 'my.images' : 'gallery.index'
  
  router.get(route(routeName), cleanForm, {
    preserveState: true,
    replace: true,
  })
}

const clearFilters = () => {
  Object.keys(searchForm).forEach(key => {
    if (!['sort', 'direction'].includes(key)) {
      searchForm[key] = ''
    }
  })
  search()
}

const getImageUrl = (image) => {
  if (image.storage_path) {
    return `http://localhost:9000/gallery-images/${image.storage_path}`
  }
  return '/images/placeholder.jpg'
}

const openLightbox = (image) => {
  currentImageIndex.value = props.images.data.findIndex(img => img.id === image.id)
  showLightbox.value = true
}

const toggleSelectAll = () => {
  if (selectAll.value) {
    selectedImages.value = props.images.data.map(img => img.id)
  } else {
    selectedImages.value = []
  }
}

const clearSelection = () => {
  selectedImages.value = []
  selectAll.value = false
  bulkAction.value = ''
}

const executeBulkAction = () => {
  if (!bulkAction.value || selectedImages.value.length === 0) return

  if (bulkAction.value === 'delete') {
    if (!confirm(`Are you sure you want to delete ${selectedImages.value.length} image(s)? This action cannot be undone.`)) {
      return
    }
  }

  bulkActionLoading.value = true

  const data = {
    action: bulkAction.value,
    image_ids: selectedImages.value,
  }

  if (bulkAction.value === 'privacy') {
    data.privacy_level = privacyLevel.value
  }

  if (bulkAction.value === 'move_to_album') {
    data.album_id = selectedAlbumId.value
  }

  router.post(route('images.bulk'), data, {
    onSuccess: () => {
      clearSelection()
      bulkActionLoading.value = false
    },
    onError: () => {
      bulkActionLoading.value = false
    }
  })
}

const getEmptyMessage = () => {
  if (hasFilters.value) {
    return 'No images found matching your criteria.'
  }
  
  if (props.isMyImages) {
    return 'You haven\'t uploaded any images yet.'
  }
  
  return 'No images available yet.'
}
</script>
