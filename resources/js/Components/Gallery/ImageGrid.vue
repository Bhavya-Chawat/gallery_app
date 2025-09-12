<template>
  <div>
    <!-- Grid header -->
    <div v-if="selectedImages.length > 0 || showBulkActions" class="mb-4 p-4 bg-blue-50 rounded-lg">
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

    <!-- Grid -->
    <div
      :class="[
        'grid gap-4',
        gridClasses
      ]"
    >
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

    <!-- Empty state -->
    <div v-if="images.length === 0" class="text-center py-12">
      <PhotoIcon class="mx-auto h-12 w-12 text-gray-400" />
      <h3 class="mt-2 text-sm font-medium text-gray-900">No images</h3>
      <p class="mt-1 text-sm text-gray-500">
        {{ emptyMessage }}
      </p>
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


    <div v-if="lightboxOpen" class="fixed top-0 left-0 z-40 bg-red-500 text-white p-2">
  DEBUG: Lightbox should be open! Index: {{ currentImageIndex }}
</div>


    <!-- Lightbox -->
<Lightbox
  v-if="lightboxOpen"
  :images="images"
  :initial-index="currentImageIndex"
  @close="closeLightbox"
  @next="nextImage"
  @prev="prevImage"
/>
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
  images: {
    type: Array,
    default: () => [],
  },
  columns: {
    type: [Number, String],
    default: 'auto',
  },
  layout: {
    type: String,
    default: 'grid',
    validator: (value) => ['grid', 'masonry'].includes(value),
  },
  cardSize: {
    type: String,
    default: 'medium',
    validator: (value) => ['small', 'medium', 'large'].includes(value),
  },
  showBulkActions: {
    type: Boolean,
    default: false,
  },
  showInfo: {
    type: Boolean,
    default: true,
  },
  showUploadLink: {
    type: Boolean,
    default: false,
  },
  emptyMessage: {
    type: String,
    default: 'No images found.',
  },
  bulkPermissions: {
    type: Object,
    default: () => ({}),
  },
})

const emit = defineEmits(['bulk-select', 'open-image'])

const selectedImages = ref([])
const lightboxOpen = ref(false)
const currentImageIndex = ref(0)

const gridClasses = computed(() => {
  if (props.columns === 'auto') {
    switch (props.cardSize) {
      case 'small':
        return 'grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8'
      case 'medium':
        return 'grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5'
      case 'large':
        return 'grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4'
      default:
        return 'grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4'
    }
  }
  return `grid-cols-${props.columns}`
})



const toggleSelection = (imageId) => {
  const index = selectedImages.value.indexOf(imageId)
  if (index > -1) {
    selectedImages.value.splice(index, 1)
  } else {
    selectedImages.value.push(imageId)
  }
  emit('bulk-select', selectedImages.value)
}

const clearSelection = () => {
  selectedImages.value = []
  emit('bulk-select', [])
}

const openImage = (imageId) => {
  console.log('ImageGrid openImage called with:', imageId)
  const index = props.images.findIndex(img => img.id === imageId)
  console.log('Found image at index:', index)
  if (index !== -1) {
    currentImageIndex.value = index
    lightboxOpen.value = true
    console.log('Lightbox should open now, lightboxOpen:', lightboxOpen.value)
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
    router.post(route('api.images.bulk'), {
      action: 'delete',
      ids: selectedImages.value,
    }, {
      onSuccess: () => {
        clearSelection()
      },
    })
  }
}

const handleBulkMove = (albumId) => {
  router.post(route('api.images.bulk'), {
    action: 'move',
    ids: selectedImages.value,
    payload: { album_id: albumId },
  }, {
    onSuccess: () => {
      clearSelection()
    },
  })
}

const handleBulkEdit = (data) => {
  router.post(route('api.images.bulk'), {
    action: 'edit',
    ids: selectedImages.value,
    payload: data,
  }, {
    onSuccess: () => {
      clearSelection()
    },
  })
}

const handleBulkDownload = () => {
  // Implement bulk download logic
  console.log('Bulk download:', selectedImages.value)
}
</script>
