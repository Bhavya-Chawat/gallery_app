<template>
  <div 
    class="group relative bg-white dark:bg-slate-800 rounded-lg overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 cursor-pointer"
    :class="sizeClasses"
    @click="handleClick"
  >
    <!-- Selection Checkbox -->
    <div 
      v-if="showCheckbox" 
      class="absolute top-2 left-2 z-10 opacity-0 group-hover:opacity-100 transition-opacity"
      @click.stop
    >
      <input
        :id="`select-${image.id}`"
        type="checkbox"
        :checked="isSelected"
        @change="handleSelect"
        class="w-4 h-4 text-blue-600 bg-white dark:bg-slate-700 border-gray-300 dark:border-gray-500 rounded focus:ring-blue-500 focus:ring-2"
      >
    </div>

    <!-- Like Button -->
    <button
      v-if="canLike"
      @click.stop="toggleLike"
      class="absolute top-2 right-2 z-10 p-2 bg-black/20 backdrop-blur-sm rounded-full opacity-0 group-hover:opacity-100 transition-all duration-200 hover:bg-black/30 hover:scale-110"
    >
      <svg 
        class="w-4 h-4 transition-colors"
        :class="image.is_liked ? 'text-red-500 fill-current' : 'text-white'"
        fill="none" 
        stroke="currentColor" 
        viewBox="0 0 24 24"
      >
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
      </svg>
    </button>

    <!-- Image Container -->
    <div class="aspect-square overflow-hidden bg-gray-100 dark:bg-slate-700">
      <img
        :src="image.thumb_url || image.url"
        :alt="image.alt_text || image.title || 'Image'"
        :loading="loading"
        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
        @error="handleImageError"
        @load="handleImageLoad"
      />
      
      <!-- Loading Placeholder -->
      <div 
        v-if="isLoading"
        class="absolute inset-0 flex items-center justify-center bg-gray-100 dark:bg-slate-700"
      >
        <svg class="w-8 h-8 text-gray-400 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
        </svg>
      </div>
    </div>

    <!-- Metadata Overlay -->
    <div 
      v-if="showMetadata"
      class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent p-3 text-white transform translate-y-full group-hover:translate-y-0 transition-transform duration-300"
    >
      <h3 v-if="image.title" class="font-medium text-sm truncate mb-1">
        {{ image.title }}
      </h3>
      <div class="flex items-center justify-between text-xs text-gray-200">
        <span v-if="image.user?.name" class="truncate">
          by {{ image.user.name }}
        </span>
        <div class="flex items-center space-x-3">
          <span v-if="image.likes_count" class="flex items-center space-x-1">
            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
            </svg>
            <span>{{ formatCount(image.likes_count) }}</span>
          </span>
          <span v-if="image.views_count" class="flex items-center space-x-1">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
            <span>{{ formatCount(image.views_count) }}</span>
          </span>
        </div>
      </div>
    </div>

    <!-- Privacy Badge -->
    <div 
      v-if="image.privacy && image.privacy !== 'public'"
      class="absolute top-2 left-2 px-2 py-1 bg-black/50 backdrop-blur-sm text-white text-xs rounded-md"
    >
      <svg v-if="image.privacy === 'private'" class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
      </svg>
      {{ image.privacy }}
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  image: {
    type: Object,
    required: true,
    validator: (image) => image && image.id
  },
  size: {
    type: String,
    default: 'medium',
    validator: (value) => ['small', 'medium', 'large'].includes(value)
  },
  showCheckbox: {
    type: Boolean,
    default: false
  },
  showMetadata: {
    type: Boolean,
    default: true
  },
  canLike: {
    type: Boolean,
    default: true
  },
  isSelected: {
    type: Boolean,
    default: false
  },
  loading: {
    type: String,
    default: 'lazy'
  }
})

const emit = defineEmits(['click', 'select', 'like', 'unlike'])

const isLoading = ref(true)

const sizeClasses = computed(() => {
  const sizes = {
    small: 'w-32 h-32',
    medium: 'w-48 h-48',
    large: 'w-64 h-64'
  }
  return sizes[props.size]
})

const handleClick = () => {
  emit('click', props.image)
}

const handleSelect = (event) => {
  emit('select', {
    image: props.image,
    selected: event.target.checked
  })
}

const toggleLike = async () => {
  if (props.image.is_liked) {
    emit('unlike', props.image)
  } else {
    emit('like', props.image)
  }
}

const handleImageError = () => {
  isLoading.value = false
  // Could emit error event or set fallback image
}

const handleImageLoad = () => {
  isLoading.value = false
}

const formatCount = (count) => {
  if (count >= 1000000) {
    return (count / 1000000).toFixed(1) + 'M'
  }
  if (count >= 1000) {
    return (count / 1000).toFixed(1) + 'K'
  }
  return count.toString()
}
</script>