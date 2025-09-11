<template>
  <div>
    <!-- Grid Layout -->
    <div 
      v-if="layout === 'grid'" 
      class="grid gap-4"
      :class="gridClasses"
    >
      <ImageCard
        v-for="image in images"
        :key="image.id"
        :image="image"
        :size="cardSize"
        :show-checkbox="showSelection"
        :show-metadata="true"
        :can-like="canLike"
        :is-selected="isImageSelected(image.id)"
        @click="$emit('image-click', image)"
        @select="$emit('image-select', $event)"
        @like="$emit('image-like', image)"
        @unlike="$emit('image-unlike', image)"
      />
    </div>

    <!-- Masonry Layout -->
    <div 
      v-else-if="layout === 'masonry'" 
      ref="masonryContainer"
      class="columns-2 md:columns-3 lg:columns-4 xl:columns-5 gap-4 space-y-4"
    >
      <div
        v-for="image in images"
        :key="image.id"
        class="break-inside-avoid mb-4"
      >
        <div
          class="group relative bg-white dark:bg-slate-800 rounded-lg overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 cursor-pointer"
          @click="$emit('image-click', image)"
        >
          <!-- Selection Checkbox -->
          <div 
            v-if="showSelection" 
            class="absolute top-2 left-2 z-10 opacity-0 group-hover:opacity-100 transition-opacity"
            @click.stop
          >
            <input
              :id="`select-${image.id}`"
              type="checkbox"
              :checked="isImageSelected(image.id)"
              @change="handleSelect(image, $event)"
              class="w-4 h-4 text-blue-600 bg-white dark:bg-slate-700 border-gray-300 dark:border-gray-500 rounded focus:ring-blue-500 focus:ring-2"
            >
          </div>

          <!-- Like Button -->
          <button
            v-if="canLike"
            @click.stop="toggleLike(image)"
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

          <!-- Image -->
          <div class="relative overflow-hidden">
            <img
              :src="image.thumb_url || image.url"
              :alt="image.alt_text || image.title || 'Image'"
              class="w-full h-auto object-cover group-hover:scale-105 transition-transform duration-300"
              :style="{ aspectRatio: image.aspect_ratio || 'auto' }"
              loading="lazy"
              @error="handleImageError"
            />
            
            <!-- Loading Placeholder -->
            <div 
              v-if="!imageLoaded[image.id]"
              class="absolute inset-0 flex items-center justify-center bg-gray-100 dark:bg-slate-700"
            >
              <svg class="w-8 h-8 text-gray-400 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
              </svg>
            </div>
          </div>

          <!-- Metadata Overlay -->
          <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent p-3 text-white transform translate-y-full group-hover:translate-y-0 transition-transform duration-300">
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
            :class="{ 'left-8': showSelection }"
          >
            <svg v-if="image.privacy === 'private'" class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
            </svg>
            {{ image.privacy }}
          </div>
        </div>
      </div>
    </div>

    <!-- List Layout -->
    <div v-else-if="layout === 'list'" class="space-y-4">
      <div
        v-for="image in images"
        :key="image.id"
        class="flex items-center space-x-4 p-4 bg-white dark:bg-slate-800 rounded-lg shadow-sm hover:shadow-md transition-shadow cursor-pointer"
        @click="$emit('image-click', image)"
      >
        <!-- Thumbnail -->
        <div class="w-16 h-16 flex-shrink-0 overflow-hidden rounded-lg bg-gray-100 dark:bg-slate-700">
          <img
            :src="image.thumb_url || image.url"
            :alt="image.alt_text || image.title || 'Image'"
            class="w-full h-full object-cover"
            loading="lazy"
          />
        </div>

        <!-- Content -->
        <div class="flex-1 min-w-0">
          <h3 class="font-medium text-gray-900 dark:text-white truncate">
            {{ image.title || 'Untitled' }}
          </h3>
          <p v-if="image.user?.name" class="text-sm text-gray-600 dark:text-gray-400">
            by {{ image.user.name }}
          </p>
          <div class="flex items-center space-x-4 mt-2 text-xs text-gray-500 dark:text-gray-400">
            <span v-if="image.likes_count">{{ formatCount(image.likes_count) }} likes</span>
            <span v-if="image.views_count">{{ formatCount(image.views_count) }} views</span>
            <span v-if="image.created_at">{{ formatDate(image.created_at) }}</span>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center space-x-2">
          <!-- Selection Checkbox -->
          <input
            v-if="showSelection"
            type="checkbox"
            :checked="isImageSelected(image.id)"
            @change="handleSelect(image, $event)"
            @click.stop
            class="w-4 h-4 text-blue-600 bg-white dark:bg-slate-700 border-gray-300 dark:border-gray-500 rounded focus:ring-blue-500 focus:ring-2"
          >

          <!-- Like Button -->
          <button
            v-if="canLike"
            @click.stop="toggleLike(image)"
            class="p-2 text-gray-400 hover:text-red-500 transition-colors"
          >
            <svg 
              class="w-5 h-5"
              :class="image.is_liked ? 'text-red-500 fill-current' : ''"
              fill="none" 
              stroke="currentColor" 
              viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
            </svg>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import ImageCard from './ImageCard.vue'

const props = defineProps({
  images: {
    type: Array,
    required: true
  },
  layout: {
    type: String,
    default: 'grid',
    validator: (value) => ['grid', 'masonry', 'list'].includes(value)
  },
  columns: {
    type: Number,
    default: 4
  },
  showSelection: {
    type: Boolean,
    default: false
  },
  selectedImages: {
    type: Array,
    default: () => []
  },
  canLike: {
    type: Boolean,
    default: true
  }
})

const emit = defineEmits(['image-click', 'image-select', 'image-like', 'image-unlike'])

const masonryContainer = ref(null)
const imageLoaded = ref({})

const gridClasses = computed(() => {
  const baseClasses = {
    2: 'grid-cols-2',
    3: 'grid-cols-2 md:grid-cols-3',
    4: 'grid-cols-2 md:grid-cols-3 lg:grid-cols-4',
    5: 'grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5',
    6: 'grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6'
  }
  return baseClasses[props.columns] || baseClasses[4]
})

const cardSize = computed(() => {
  if (props.columns <= 3) return 'large'
  if (props.columns <= 4) return 'medium'
  return 'small'
})

const isImageSelected = (imageId) => {
  return props.selectedImages.some(img => img.id === imageId)
}

const handleSelect = (image, event) => {
  emit('image-select', {
    image,
    selected: event.target.checked
  })
}

const toggleLike = (image) => {
  if (image.is_liked) {
    emit('image-unlike', image)
  } else {
    emit('image-like', image)
  }
}

const formatCount = (count) => {
  if (!count) return 0
  if (count >= 1000000) {
    return (count / 1000000).toFixed(1) + 'M'
  }
  if (count >= 1000) {
    return (count / 1000).toFixed(1) + 'K'
  }
  return count.toString()
}

const formatDate = (dateString) => {
  try {
    return new Date(dateString).toLocaleDateString()
  } catch {
    return ''
  }
}

const handleImageError = (event) => {
  // Handle broken image - could set fallback src
  event.target.style.display = 'none'
}

// Initialize image loaded state
onMounted(() => {
  props.images.forEach(image => {
    imageLoaded.value[image.id] = false
  })
})
</script>