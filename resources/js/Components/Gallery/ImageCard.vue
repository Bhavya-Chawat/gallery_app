<template>
  <div
    class="group relative bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-200 cursor-pointer"
    :class="{ 'ring-2 ring-blue-500': selected }"
    @click="handleImageClick"
  >
    <!-- Selection checkbox -->
    <div v-if="showCheckbox" class="absolute top-2 left-2 z-10">
      <input
        type="checkbox"
        :checked="selected"
        @change="$emit('select', image.id)"
        @click.stop
        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
      >
    </div>

    <!-- Image -->
    <div class="aspect-square bg-gray-100 overflow-hidden">
      <img
        :src="getImageUrl('medium')"
        :alt="image.alt_text || image.title"
        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200"
        loading="lazy"
        @error="onImageError"
      >
    </div>

<!-- Privacy Badge -->
<div v-if="image.privacy" class="absolute top-2 right-2 z-10">
  <span
    v-if="image.privacy === 'private'"
    class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-red-100 text-red-700"
  >Private</span>
  <span
    v-else-if="image.privacy === 'unlisted'"
    class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-yellow-100 text-yellow-800"
  >Unlisted</span>
  <span
    v-else
    class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-green-100 text-green-800"
  >Public</span>
</div>



    <!-- Overlay - NO CLICK HANDLER HERE -->
    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-all duration-300 flex items-end pointer-events-none">
      <div class="w-full p-4 text-white transform translate-y-2 group-hover:translate-y-0 transition-transform duration-300">
        <h3 class="text-sm font-semibold truncate mb-1">
          {{ image.title || 'Untitled' }}
        </h3>
        <div class="flex items-center justify-between text-xs">
          <span class="opacity-90">
            {{ formatDate(image.created_at) }}
          </span>
          <div class="flex items-center space-x-3">
            <span v-if="image.views_count" class="flex items-center opacity-90">
              <EyeIcon class="h-3 w-3 mr-1" />
              {{ formatCount(image.views_count) }}
            </span>
            <span v-if="image.likes_count" class="flex items-center opacity-90">
              <HeartIcon class="h-3 w-3 mr-1" />
              {{ formatCount(image.likes_count) }}
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- Action menu -->
    <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-all duration-300 pointer-events-auto" v-if="canEdit || canDelete || canDownload">
      <Dropdown align="right" width="48" @click.stop>
        <template #trigger>
          <button class="p-1.5 bg-black/20 backdrop-blur-sm text-white rounded-full hover:bg-black/40 transition-all duration-200" @click.stop>
            <EllipsisVerticalIcon class="h-4 w-4" />
          </button>
        </template>

    <template #content>
      <DropdownLink :href="route('images.show', image.slug)">
        View Details
      </DropdownLink>
      <DropdownLink :href="route('images.edit', image.slug)" v-if="canEdit">
        Edit
      </DropdownLink>
      <DropdownLink :href="route('images.download', image.slug)" v-if="canDownload">
        Download
      </DropdownLink>
      <div class="border-t border-gray-100" v-if="canEdit && canDelete"></div>
      <DropdownLink
        :href="route('images.destroy', image.slug)"
        method="delete"
        as="button"
        class="text-red-600"
        v-if="canDelete"
        @click="confirmDelete"
      >
        Delete
      </DropdownLink>
    </template>
  </Dropdown>
</div>


    <!-- Image info -->
    <div class="p-3" v-if="showInfo">
      <h3 class="text-sm font-medium text-gray-900 truncate">
        {{ image.title || 'Untitled' }}
      </h3>
      <p v-if="image.caption" class="mt-1 text-xs text-gray-500 line-clamp-2">
        {{ image.caption }}
      </p>
      <div class="flex items-center justify-between mt-2">
        <span class="text-xs text-gray-500">
          {{ formatDate(image.created_at) }}
        </span>
        <div class="flex items-center space-x-2 text-xs text-gray-500">
          <span v-if="image.views_count">
            {{ formatCount(image.views_count) }} views
          </span>
          <span v-if="image.likes_count">
            {{ formatCount(image.likes_count) }} likes
          </span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import route from 'ziggy-js'
import {
  EyeIcon,
  HeartIcon,
  LockClosedIcon,
  EllipsisVerticalIcon,
} from '@heroicons/vue/24/outline'

import Dropdown from '@/Components/Dropdown.vue'
import DropdownLink from '@/Components/DropdownLink.vue'

const props = defineProps({
  image: {
    type: Object,
    required: true,
  },
  size: {
    type: String,
    default: 'medium',
    validator: (value) => ['small', 'medium', 'large'].includes(value),
  },
  showCheckbox: {
    type: Boolean,
    default: false,
  },
  showInfo: {
    type: Boolean,
    default: true,
  },
  selected: {
    type: Boolean,
    default: false,
  },
  canEdit: {
    type: Boolean,
    default: false,
  },
  canDelete: {
    type: Boolean,
    default: false,
  },
  canDownload: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['select', 'open'])

const getImageUrl = (variant = 'medium') => {
  // Use direct MinIO URL since thumbnails aren't processed yet  
  if (props.image.storage_path) {
    return `http://localhost:9000/gallery-images/${props.image.storage_path}`
  }
  return props.image.url || '/images/placeholder.jpg'
}

const handleImageClick = () => {
  console.log('ImageCard clicked!', props.image.id)
  emit('open', props.image.id)
}



const formatDate = (date) => {
  return new Date(date).toLocaleDateString()
}

const formatCount = (count) => {
  if (count >= 1000000) {
    return Math.floor(count / 1000000) + 'M'
  } else if (count >= 1000) {
    return Math.floor(count / 1000) + 'K'
  }
  return count.toString()
}

const onImageError = (event) => {
  // Fallback to a placeholder or retry with original
  event.target.src = '/images/placeholder.jpg'
}

const confirmDelete = (event) => {
  if (!confirm('Are you sure you want to delete this image?')) {
    event.preventDefault()
  }
}
</script>
