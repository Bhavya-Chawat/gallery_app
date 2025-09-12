<template>
  <div>
    <!-- Count display with sort controls -->
    <div class="flex items-center justify-between mb-6">
      <div class="flex items-center space-x-4">
        <span class="text-sm text-gray-500">
          {{ images.length }} image{{ images.length !== 1 ? 's' : '' }}
        </span>
        
        <select 
          v-model="sortBy" 
          @change="handleSortChange"
          class="text-sm border border-gray-300 rounded-md px-3 py-1 focus:ring-blue-500 focus:border-blue-500"
        >
          <option value="created_at">Recently Added</option>
          <option value="title">Title</option>
          <option value="views">Most Viewed</option>
          <option value="size">File Size</option>
        </select>

        <select 
          v-model="sortDirection" 
          @change="handleSortChange"
          class="text-sm border border-gray-300 rounded-md px-3 py-1 focus:ring-blue-500 focus:border-blue-500"
        >
          <option value="desc">Descending</option>
          <option value="asc">Ascending</option>
        </select>
      </div>

      <!-- Select All checkbox (if bulk actions enabled) -->
      <div v-if="showBulkActions && images.length > 0" class="flex items-center space-x-2">
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

    <!-- Bulk Actions Bar -->
    <div v-if="selectedImages.length > 0" class="mb-4 p-4 bg-blue-50 rounded-lg">
      <BulkActionsBar
        :selected-count="selectedImages.length"
        :permissions="bulkPermissions"
        @delete="handleBulkDelete"
        @move="handleBulkMove"
        @edit="handleBulkEdit"
        @download="handleBulkDownload"
        @clear-selection="clearSelection"
      />
    </div>

    <!-- Images Grid -->
    <div :class="['grid gap-4', gridClasses]">
      <ImageCard
        v-for="image in images"
        :key="image.id"
        :image="image"
        :size="cardSize"
        :show-checkbox="showBulkActions"
        :show-info="showInfo"
        :selected="selectedImages.includes(image.id)"
        :can-edit="image.can?.edit || false"
        :can-delete="image.can?.delete || false"
        :can-download="image.can?.download || false"
        @select="toggleSelection"
        @open="openImage"
      />
    </div>

    <!-- Empty State -->
    <div v-if="images.length === 0" class="text-center py-12">
      <PhotoIcon class="mx-auto h-12 w-12 text-gray-400" />
      <h3 class="mt-2 text-sm font-medium text-gray-900">No images</h3>
      <p class="mt-1 text-sm text-gray-500">{{ emptyMessage }}</p>
      <div class="mt-6" v-if="showUploadLink">
        <Link
          :href="route('upload')"
          class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700"
        >
          <PlusIcon class="-ml-1 mr-2 h-5 w-5" />
          Upload Images
        </Link>
      </div>
    </div>

    <!-- Lightbox -->
    <Teleport to="body">
      <Lightbox
        v-if="lightboxOpen"
        :images="images"
        :initial-index="currentImageIndex"
        @close="closeLightbox"
        @next="nextImage"
        @prev="prevImage"
      />
    </Teleport>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import route from 'ziggy-js'
import {
  PhotoIcon,
  PlusIcon,
} from '@heroicons/vue/24/outline'

import ImageCard from './ImageCard.vue'
import BulkActionsBar from './BulkActionsBar.vue'
import Lightbox from './Lightbox.vue'

const props = defineProps({
  images: { type: Array, default: () => [] },
  columns: { type: [Number, String], default: 'auto' },
  layout: { type: String, default: 'grid' },
  cardSize: { type: String, default: 'medium' },
  showBulkActions: { type: Boolean, default: false },
  showInfo: { type: Boolean, default: true },
  showUploadLink: { type: Boolean, default: false },
  emptyMessage: { type: String, default: 'No images found.' },
  bulkPermissions: { type: Object, default: () => ({}) },
})

const emit = defineEmits(['bulk-select', 'open-image', 'sort-change'])

// State
const selectedImages = ref([])
const lightboxOpen = ref(false)
const currentImageIndex = ref(0)
const selectAll = ref(false)
const sortBy = ref('created_at')
const sortDirection = ref('desc')

// Computed
const gridClasses = computed(() => {
  if (props.columns === 'auto') {
    switch (props.cardSize) {
      case 'small': return 'grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8'
      case 'medium': return 'grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5'
      case 'large': return 'grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4'
      default: return 'grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4'
    }
  }
  return `grid-cols-${props.columns}`
})

// Methods
const toggleSelection = (imageId) => {
  const index = selectedImages.value.indexOf(imageId)
  if (index > -1) {
    selectedImages.value.splice(index, 1)
  } else {
    selectedImages.value.push(imageId)
  }
  emit('bulk-select', selectedImages.value)
}

const toggleSelectAll = () => {
  if (selectAll.value) {
    selectedImages.value = props.images.map(img => img.id)
  } else {
    selectedImages.value = []
  }
  emit('bulk-select', selectedImages.value)
}

const clearSelection = () => {
  selectedImages.value = []
  selectAll.value = false
  emit('bulk-select', [])
}

const handleSortChange = () => {
  emit('sort-change', {
    sort: sortBy.value,
    direction: sortDirection.value
  })
}

const openImage = (imageId) => {
  const index = props.images.findIndex(img => img.id === imageId)
  if (index !== -1) {
    currentImageIndex.value = index
    lightboxOpen.value = true
  }
  emit('open-image', imageId)
}

const closeLightbox = () => {
  lightboxOpen.value = false
}

const nextImage = () => {
  if (currentImageIndex.value < props.images.length - 1) {
    currentImageIndex.value++
  }
}

const prevImage = () => {
  if (currentImageIndex.value > 0) {
    currentImageIndex.value--
  }
}

// Bulk actions
const handleBulkDelete = () => {
  if (confirm(`Are you sure you want to delete ${selectedImages.value.length} images?`)) {
    router.post(route('images.bulk-action'), {
      action: 'delete',
      image_ids: selectedImages.value,
    }, {
      onSuccess: () => clearSelection()
    })
  }
}

const handleBulkMove = (albumId) => {
  router.post(route('images.bulk-action'), {
    action: 'move_to_album',
    image_ids: selectedImages.value,
    album_id: albumId,
  }, {
    onSuccess: () => clearSelection()
  })
}

const handleBulkEdit = (data) => {
  router.post(route('images.bulk-action'), {
    action: 'privacy',
    image_ids: selectedImages.value,
    privacy_level: data.privacy,
  }, {
    onSuccess: () => clearSelection()
  })
}

const handleBulkDownload = () => {
  console.log('Bulk download:', selectedImages.value)
}
</script>
