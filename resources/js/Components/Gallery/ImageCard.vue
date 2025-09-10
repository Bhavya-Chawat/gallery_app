<template>
  <div
    class="group relative bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-200"
    :class="{ 'ring-2 ring-blue-500': selected }"
  >
    <!-- Selection checkbox -->
    <div v-if="showCheckbox" class="absolute top-2 left-2 z-10">
      <input
        type="checkbox"
        :checked="selected"
        @change="$emit('select', image.id)"
        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
      >
    </div>

    <!-- Image -->
    <div
      class="aspect-square bg-gray-100 overflow-hidden cursor-pointer"
      @click="$emit('open', image.id)"
    >
      <img
        :src="getImageUrl('medium')"
        :alt="image.alt_text || image.title"
        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200"
        loading="lazy"
        @error="onImageError"
      >
    </div>

    <!-- Overlay info -->
    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-200 flex items-end">
      <div class="w-full p-3 text-white transform translate-y-full group-hover:translate-y-0 transition-transform duration-200">
        <h3 class="text-sm font-medium truncate">
          {{ image.title || 'Untitled' }}
        </h3>
        <div class="flex items-center justify-between mt-1">
          <span class="text-xs opacity-75">
            {{ formatDate(image.created_at) }}
          </span>
          <div class="flex items-center space-x-2 text-xs">
            <span v-if="image.views_count" class="flex items-center">
              <EyeIcon class="h-3 w-3 mr-1" />
              {{ formatCount(image.views_count) }}
            </span>
            <span v-if="image.likes_count" class="flex items-center">
              <HeartIcon class="h-3 w-3 mr-1" />
              {{ formatCount(image.likes_count) }}
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- Privacy indicator -->
    <div v-if="image.privacy === 'private'" class="absolute top-2 right-2">
      <LockClosedIcon class="h-4 w-4 text-gray-600 bg-white bg-opacity-75 rounded p-0.5" />
    </div>

    <!-- Action menu -->
    <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
      <Dropdown align="right" width="48" v-if="canEdit">
        <template #trigger>
          <button class="p-1 bg-white bg-opacity-75 rounded-full hover:bg-opacity-100">
            <EllipsisVerticalIcon class="h-4 w-4 text-gray-600" />
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
          <div class="border-t border-gray-100" v-if="canEdit"></div>
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
import { route } from 'ziggy-js'
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
  if (props.image.versions) {
    const version = props.image.versions.find(v => v.variant === variant)
    if (version) return version.url
  }
  return props.image.url || props.image.storage_path
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
