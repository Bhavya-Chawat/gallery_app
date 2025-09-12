<template>
  <AppLayout>
    <Head :title="`Add Images to ${album.title}`" />

    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <nav class="flex" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-4">
              <li>
                <Link :href="route('albums.index')" class="text-gray-400 hover:text-gray-500">
                  Albums
                </Link>
              </li>
              <li class="flex">
                <ChevronRightIcon class="flex-shrink-0 h-5 w-5 text-gray-400" />
                <Link :href="route('albums.show', album.slug)" class="text-gray-400 hover:text-gray-500">
                  {{ album.title }}
                </Link>
              </li>
              <li class="flex">
                <ChevronRightIcon class="flex-shrink-0 h-5 w-5 text-gray-400" />
                <span class="ml-4 text-sm font-medium text-gray-900">Add Images</span>
              </li>
            </ol>
          </nav>
          <h2 class="mt-2 font-semibold text-xl text-gray-800 leading-tight">
            Add Images to {{ album.title }}
          </h2>
        </div>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Selection Summary -->
        <div v-if="selectedImages.length > 0" class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
          <div class="flex items-center justify-between">
            <span class="text-sm font-medium text-blue-900">
              {{ selectedImages.length }} image(s) selected
            </span>
            <div class="flex items-center space-x-2">
              <button
                @click="addSelectedImages"
                :disabled="addLoading"
                class="px-4 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700 disabled:opacity-50"
              >
                {{ addLoading ? 'Adding...' : 'Add to Album' }}
              </button>
              
              <button
                @click="clearSelection"
                class="px-4 py-2 bg-gray-300 text-gray-700 text-sm rounded-md hover:bg-gray-400"
              >
                Clear Selection
              </button>
            </div>
          </div>
        </div>

        <!-- Available Images -->
        <div class="bg-white shadow-sm rounded-lg">
          <div class="p-6">
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-lg font-medium text-gray-900">Available Images</h3>
              
              <!-- Select All -->
              <div v-if="availableImages.length > 0" class="flex items-center space-x-2">
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
            
            <div v-if="availableImages.length > 0" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
              <div
                v-for="image in availableImages"
                :key="image.id"
                class="relative group bg-gray-100 rounded-lg overflow-hidden aspect-square"
              >
                <!-- Selection Checkbox -->
                <div class="absolute top-2 left-2 z-10">
                  <input
                    v-model="selectedImages"
                    :value="image.id"
                    type="checkbox"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded bg-white/80"
                  />
                </div>

                <!-- Current Album Badge -->
                <div v-if="image.album_id" class="absolute top-2 right-2 z-10">
                  <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded">
                    In Album
                  </span>
                </div>

                <!-- Image -->
                <img
                  :src="getImageUrl(image)"
                  :alt="image.alt_text || image.title"
                  class="w-full h-full object-cover cursor-pointer"
                  @click="toggleImageSelection(image.id)"
                />

                <!-- Title overlay -->
                <div class="absolute bottom-0 left-0 right-0 bg-black/50 text-white p-2 opacity-0 group-hover:opacity-100 transition-opacity">
                  <p class="text-xs font-medium truncate">{{ image.title || 'Untitled' }}</p>
                </div>
              </div>
            </div>
            
            <!-- No Images State -->
            <div v-else class="text-center py-12">
              <PhotoIcon class="mx-auto h-12 w-12 text-gray-400" />
              <h3 class="mt-2 text-sm font-medium text-gray-900">No available images</h3>
              <p class="mt-1 text-sm text-gray-500">
                All your images are already in albums or you haven't uploaded any images yet.
              </p>
              <div class="mt-6">
                <Link
                  :href="route('upload')"
                  class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700"
                >
                  <PlusIcon class="-ml-1 mr-2 h-5 w-5" />
                  Upload Images
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import route from 'ziggy-js'
import { ChevronRightIcon, PhotoIcon, PlusIcon } from '@heroicons/vue/24/outline'

import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  album: Object,
  availableImages: {
    type: Array,
    default: () => [],
  },
})

const selectedImages = ref([])
const selectAll = ref(false)
const addLoading = ref(false)

const getImageUrl = (image) => {
  if (image.storage_path) {
    return `http://localhost:9000/gallery-images/${image.storage_path}`
  }
  return '/images/placeholder.jpg'
}

const toggleSelectAll = () => {
  if (selectAll.value) {
    selectedImages.value = props.availableImages.map(img => img.id)
  } else {
    selectedImages.value = []
  }
}

const toggleImageSelection = (imageId) => {
  const index = selectedImages.value.indexOf(imageId)
  if (index > -1) {
    selectedImages.value.splice(index, 1)
  } else {
    selectedImages.value.push(imageId)
  }
  
  selectAll.value = selectedImages.value.length === props.availableImages.length
}

const clearSelection = () => {
  selectedImages.value = []
  selectAll.value = false
}

const addSelectedImages = () => {
  if (selectedImages.value.length === 0) return

  addLoading.value = true

  router.post(route('albums.add-images', props.album.id), {
    image_ids: selectedImages.value
  }, {
    onSuccess: () => {
      addLoading.value = false
    },
    onError: () => {
      addLoading.value = false
    }
  })
}
</script>
