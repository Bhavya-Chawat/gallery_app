<template>
  <AppLayout>
    <Head :title="album.title || 'Album'" />

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
                <span class="ml-4 text-sm font-medium text-gray-900">{{ album.title }}</span>
              </li>
            </ol>
          </nav>
          <h2 class="mt-2 font-semibold text-xl text-gray-800 leading-tight">
            {{ album.title }}
          </h2>
        </div>
        
        <!-- Album Management Actions -->
        <div v-if="can.update" class="flex items-center space-x-2">
          <Link
            :href="route('albums.add-images-form', album.id)"
            class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
          >
            <PlusIcon class="h-4 w-4 mr-2" />
            Add Images
          </Link>
          <Link
            :href="route('albums.edit', album.id)"
            class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
          >
            <PencilIcon class="h-4 w-4 mr-2" />
            Edit
          </Link>
        </div>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Album Info -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
          <div class="p-6">
            <div class="flex items-start justify-between">
              <div class="flex-1">
                <h1 class="text-3xl font-bold text-gray-900">{{ album.title }}</h1>
                <p v-if="album.description" class="mt-2 text-gray-600">{{ album.description }}</p>
                
                <div class="mt-4 flex items-center space-x-6">
                  <div class="text-sm text-gray-500">
                    <span class="font-medium">{{ album.images_count || 0 }}</span> images
                  </div>
                  <div class="text-sm text-gray-500">
                    <span class="font-medium">{{ album.privacy }}</span> album
                  </div>
                  <div class="text-sm text-gray-500">
                    Created {{ formatDate(album.created_at) }}
                  </div>
                  <div class="text-sm text-gray-500">
                    by {{ album.owner?.name }}
                  </div>
                </div>
              </div>
              
              <!-- Privacy Badge -->
              <div>
                <span
                  class="px-3 py-1 text-sm font-medium rounded-full"
                  :class="{
                    'bg-green-100 text-green-800': album.privacy === 'public',
                    'bg-yellow-100 text-yellow-800': album.privacy === 'unlisted',
                    'bg-red-100 text-red-800': album.privacy === 'private'
                  }"
                >
                  {{ album.privacy }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Image Management Controls (for album owners) -->
        <div v-if="can.manageImages && selectedImages.length > 0" class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
          <div class="flex items-center justify-between">
            <span class="text-sm font-medium text-blue-900">
              {{ selectedImages.length }} image(s) selected
            </span>
            <div class="flex items-center space-x-2">
              <button
                @click="removeSelectedImages"
                :disabled="removeLoading"
                class="px-4 py-1 bg-red-600 text-white text-sm rounded-md hover:bg-red-700 disabled:opacity-50"
              >
                {{ removeLoading ? 'Removing...' : 'Remove from Album' }}
              </button>
              
              <button
                @click="clearImageSelection"
                class="px-4 py-1 bg-gray-300 text-gray-700 text-sm rounded-md hover:bg-gray-400"
              >
                Clear
              </button>
            </div>
          </div>
        </div>

        <!-- Images Grid -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6">
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-lg font-medium text-gray-900">Images</h3>
              
              <!-- Select All (for album owners) -->
              <div v-if="can.manageImages && images.data?.length > 0" class="flex items-center space-x-2">
                <input
                  id="select-all-images"
                  v-model="selectAllImages"
                  @change="toggleSelectAllImages"
                  type="checkbox"
                  class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                />
                <label for="select-all-images" class="text-sm text-gray-700">Select All</label>
              </div>
            </div>
            
            <div v-if="images.data && images.data.length > 0" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
              <div
                v-for="image in images.data"
                :key="image.id"
                class="relative group bg-gray-100 rounded-lg overflow-hidden aspect-square"
              >
                <!-- Selection Checkbox (for album owners) -->
                <div v-if="can.manageImages" class="absolute top-2 left-2 z-10">
                  <input
                    v-model="selectedImages"
                    :value="image.id"
                    type="checkbox"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded bg-white/80"
                  />
                </div>

                <!-- Image -->
                <img
                  :src="getImageUrl(image)"
                  :alt="image.alt_text || image.title"
                  class="w-full h-full object-cover cursor-pointer hover:scale-105 transition-transform"
                  @click="openImage(image)"
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
              <h3 class="mt-2 text-sm font-medium text-gray-900">No images</h3>
              <p class="mt-1 text-sm text-gray-500">
                This album doesn't have any images yet.
              </p>
              <div v-if="can.manageImages" class="mt-6">
                <Link
                  :href="route('albums.add-images-form', album.id)"
                  class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700"
                >
                  <PlusIcon class="-ml-1 mr-2 h-5 w-5" />
                  Add Images
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
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import route from 'ziggy-js'
import { ChevronRightIcon, PhotoIcon, PlusIcon, PencilIcon } from '@heroicons/vue/24/outline'

import AppLayout from '@/Layouts/AppLayout.vue'
import Pagination from '@/Components/Pagination.vue'

const props = defineProps({
  album: { type: Object, default: () => ({}) },
  images: { type: Object, default: () => ({ data: [], links: [], meta: {} }) },
  filters: { type: Object, default: () => ({}) },
  can: { type: Object, default: () => ({}) },
})

// Image selection for management
const selectedImages = ref([])
const selectAllImages = ref(false)
const removeLoading = ref(false)

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  })
}

const getImageUrl = (image) => {
  if (image.storage_path) {
    return `http://localhost:9000/gallery-images/${image.storage_path}`
  }
  return '/images/placeholder.jpg'
}

const openImage = (image) => {
  router.visit(route('images.show', image.slug))
}

const toggleSelectAllImages = () => {
  if (selectAllImages.value) {
    selectedImages.value = props.images.data.map(img => img.id)
  } else {
    selectedImages.value = []
  }
}

const clearImageSelection = () => {
  selectedImages.value = []
  selectAllImages.value = false
}

const removeSelectedImages = () => {
  if (selectedImages.value.length === 0) return
  
  if (!confirm(`Remove ${selectedImages.value.length} image(s) from this album? Images will not be deleted.`)) {
    return
  }

  removeLoading.value = true

  router.delete(route('albums.remove-images', props.album.id), {
    data: { image_ids: selectedImages.value },
    onSuccess: () => {
      clearImageSelection()
      removeLoading.value = false
    },
    onError: () => {
      removeLoading.value = false
    }
  })
}
</script>
