<template>
  <div class="gallery-card group cursor-pointer" @click="handleClick">
    <!-- Cover Image -->
    <div class="aspect-w-16 aspect-h-9 bg-gray-200 rounded-t-lg overflow-hidden">
      <img
        v-if="album.cover_image"
        :src="album.cover_image.url"
        :alt="album.title"
        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
      >
      <div v-else class="flex items-center justify-center">
        <FolderIcon class="h-12 w-12 text-gray-400" />
      </div>
    </div>

    <!-- Content -->
    <div class="p-4">
      <div class="flex items-start justify-between">
        <div class="flex-1 min-w-0">
          <h3 class="text-lg font-semibold text-gray-900 truncate group-hover:text-blue-600">
            {{ album.title }}
          </h3>
          <p class="mt-1 text-sm text-gray-500 line-clamp-2">
            {{ album.description }}
          </p>
        </div>
        <div class="ml-2 flex-shrink-0">
          <span :class="[
            'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
            privacyClasses[album.privacy]
          ]">
            {{ album.privacy }}
          </span>
        </div>
      </div>

      <!-- Stats -->
      <div class="mt-3 flex items-center justify-between text-sm text-gray-500">
        <span class="flex items-center">
          <PhotoIcon class="h-4 w-4 mr-1" />
          {{ album.images_count || 0 }} image{{ (album.images_count || 0) !== 1 ? 's' : '' }}
        </span>
        <span>
          {{ formatDate(album.updated_at) }}
        </span>
      </div>

      <!-- Owner -->
      <div class="mt-2 flex items-center">
        <div class="flex-shrink-0 h-6 w-6 rounded-full bg-gray-300"></div>
        <span class="ml-2 text-sm text-gray-600">
          by {{ album.owner?.name || 'Unknown' }}
        </span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { router } from '@inertiajs/vue3'
import route from 'ziggy-js'
import {
  FolderIcon,
  PhotoIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
  album: {
    type: Object,
    required: true
  }
})

const privacyClasses = {
  public: 'bg-green-100 text-green-800',
  unlisted: 'bg-yellow-100 text-yellow-800',
  private: 'bg-red-100 text-red-800'
}

const handleClick = () => {
  router.visit(route('albums.show', props.album.slug))
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString()
}
</script>
