<template>
  <Teleport to="body">
    <!-- Backdrop -->
    <div
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/75 backdrop-blur-sm p-4"
      @click="handleBackdropClick"
      @keydown="handleKeydown"
      tabindex="0"
      ref="lightboxRef"
    >
      <!-- Lightbox Modal -->
      <div class="relative bg-white rounded-xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden animate-in zoom-in-95 duration-300">
        
        <!-- Header -->
        <div class="flex items-center justify-between p-4 border-b border-gray-200 bg-gray-50">
          <div class="flex items-center space-x-3">
            <!-- Image counter -->
            <span v-if="images.length > 1" class="text-sm font-medium text-gray-600">
              {{ currentIndex + 1 }} of {{ images.length }}
            </span>
            <div class="h-4 w-px bg-gray-300" v-if="images.length > 1"></div>
            <h3 class="text-lg font-semibold text-gray-900 truncate">
              {{ currentImage.title || 'Untitled' }}
            </h3>
          </div>
          
          <!-- Header Actions -->
          <div class="flex items-center space-x-2">
            <!-- Navigation -->
            <div v-if="images.length > 1" class="flex items-center space-x-1 mr-3">
              <button
                @click="previousImage"
                :disabled="currentIndex === 0"
                class="p-2 rounded-lg hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
              >
                <ChevronLeftIcon class="h-5 w-5 text-gray-600" />
              </button>
              <button
                @click="nextImage"
                :disabled="currentIndex >= images.length - 1"
                class="p-2 rounded-lg hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
              >
                <ChevronRightIcon class="h-5 w-5 text-gray-600" />
              </button>
            </div>
            
            <!-- Actions Menu -->
            <Dropdown align="right" width="48">
              <template #trigger>
                <button class="p-2 rounded-lg hover:bg-gray-100 transition-colors">
                  <EllipsisVerticalIcon class="h-5 w-5 text-gray-600" />
                </button>
              </template>
              <template #content>
                <DropdownLink :href="route('images.show', currentImage.slug)">
                  View Full Page
                </DropdownLink>
                <DropdownLink
                  v-if="currentImage.can?.edit"
                  :href="route('images.edit', currentImage.slug)"
                >
                  Edit Image
                </DropdownLink>
                <DropdownLink
                  v-if="currentImage.can?.download"
                  @click="downloadImage"
                  href="#"
                >
                  Download
                </DropdownLink>
                <div class="border-t border-gray-100"></div>
                <DropdownLink href="#" @click="copyLink">
                  Copy Link
                </DropdownLink>
                <DropdownLink href="#" @click="shareImage">
                  Share
                </DropdownLink>
              </template>
            </Dropdown>
            
            <!-- Close Button -->
            <button
              @click="$emit('close')"
              class="p-2 rounded-lg hover:bg-gray-100 transition-colors"
            >
              <XMarkIcon class="h-5 w-5 text-gray-600" />
            </button>
          </div>
        </div>

        <!-- Image Content -->
        <div class="flex flex-col lg:flex-row max-h-[calc(90vh-4rem)]">
          <!-- Image Display -->
          <div class="flex-1 flex items-center justify-center bg-gray-100 min-h-[300px] lg:min-h-[500px] relative">
            <img
              :src="currentImage.storage_path ? `http://localhost:9000/gallery-images/${currentImage.storage_path}` : (currentImage.url || '/images/placeholder.jpg')"
              :alt="currentImage.alt_text || currentImage.title"
              class="max-w-full max-h-full object-contain"
              @click.stop
            >
            
            <!-- Navigation Overlays for Large Screens -->
            <button
              v-if="images.length > 1 && currentIndex > 0"
              @click="previousImage"
              class="absolute left-4 top-1/2 -translate-y-1/2 p-3 rounded-full bg-black/20 hover:bg-black/40 text-white transition-all duration-200 opacity-0 hover:opacity-100 lg:opacity-100"
            >
              <ChevronLeftIcon class="h-6 w-6" />
            </button>
            
            <button
              v-if="images.length > 1 && currentIndex < images.length - 1"
              @click="nextImage"
              class="absolute right-4 top-1/2 -translate-y-1/2 p-3 rounded-full bg-black/20 hover:bg-black/40 text-white transition-all duration-200 opacity-0 hover:opacity-100 lg:opacity-100"
            >
              <ChevronRightIcon class="h-6 w-6" />
            </button>
          </div>

          <!-- Image Info Sidebar -->
          <div class="lg:w-80 bg-white border-t lg:border-t-0 lg:border-l border-gray-200 p-6 overflow-y-auto">
            <!-- Caption -->
            <div v-if="currentImage.caption" class="mb-4">
              <p class="text-gray-700 text-sm leading-relaxed">
                {{ currentImage.caption }}
              </p>
            </div>

            <!-- Metadata -->
            <div class="space-y-3 text-sm">
              <div class="flex items-center justify-between">
                <span class="text-gray-500">Uploaded</span>
                <span class="text-gray-900">{{ formatDate(currentImage.created_at) }}</span>
              </div>
              
              <div v-if="currentImage.dimensions" class="flex items-center justify-between">
                <span class="text-gray-500">Dimensions</span>
                <span class="text-gray-900">{{ currentImage.dimensions }}</span>
              </div>
              
              <div v-if="currentImage.formatted_size" class="flex items-center justify-between">
                <span class="text-gray-500">Size</span>
                <span class="text-gray-900">{{ currentImage.formatted_size }}</span>
              </div>
              
              <div class="flex items-center justify-between">
                <span class="text-gray-500">Views</span>
                <span class="text-gray-900">{{ formatCount(currentImage.views_count || 0) }}</span>
              </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center space-x-3 mt-6 pt-4 border-t border-gray-200">
              <button
                @click="toggleLike"
                class="flex items-center space-x-2 px-4 py-2 rounded-lg border transition-colors"
                :class="isLiked 
                  ? 'border-red-200 bg-red-50 text-red-600 hover:bg-red-100' 
                  : 'border-gray-200 bg-white text-gray-600 hover:bg-gray-50'"
              >
                <HeartIcon class="h-4 w-4" :class="isLiked ? 'fill-current' : ''" />
                <span class="text-sm font-medium">{{ formatCount(currentImage.likes_count || 0) }}</span>
              </button>
              
              <button
                v-if="currentImage.can?.download"
                @click="downloadImage"
                class="flex items-center space-x-2 px-4 py-2 rounded-lg border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 transition-colors"
              >
                <ArrowDownTrayIcon class="h-4 w-4" />
                <span class="text-sm font-medium">Download</span>
              </button>
            </div>

            <!-- Tags -->
            <div v-if="currentImage.tags && currentImage.tags.length" class="mt-6">
              <h4 class="text-sm font-medium text-gray-900 mb-3">Tags</h4>
              <div class="flex flex-wrap gap-2">
                <Link
                  v-for="tag in currentImage.tags"
                  :key="tag.id"
                  :href="route('tags.show', tag.slug)"
                  class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 hover:bg-blue-100 transition-colors"
                >
                  {{ tag.name }}
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick, watch } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import route from 'ziggy-js'
import {
  XMarkIcon,
  ChevronLeftIcon,
  ChevronRightIcon,
  ArrowDownTrayIcon,
  PencilIcon,
  HeartIcon,
  EllipsisVerticalIcon,
} from '@heroicons/vue/24/outline'

import Dropdown from '@/Components/Dropdown.vue'
import DropdownLink from '@/Components/DropdownLink.vue'

const props = defineProps({
  images: {
    type: Array,
    required: true,
  },
  initialIndex: {
    type: Number,
    default: 0,
  },
})

const emit = defineEmits(['close', 'next', 'prev'])

const lightboxRef = ref(null)
const currentIndex = ref(props.initialIndex)
const isLiked = ref(false)

// Watch for external index changes
watch(() => props.initialIndex, (newIndex) => {
  currentIndex.value = newIndex
})

const currentImage = computed(() => {
  return props.images[currentIndex.value]
})

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  })
}

const formatCount = (count) => {
  if (count >= 1000000) {
    return Math.floor(count / 1000000) + 'M'
  } else if (count >= 1000) {
    return Math.floor(count / 1000) + 'K'
  }
  return count.toString()
}

const handleBackdropClick = (event) => {
  if (event.target === event.currentTarget) {
    emit('close')
  }
}

const handleKeydown = (event) => {
  switch (event.key) {
    case 'Escape':
      emit('close')
      break
    case 'ArrowLeft':
      if (currentIndex.value > 0) {
        previousImage()
      }
      break
    case 'ArrowRight':
      if (currentIndex.value < props.images.length - 1) {
        nextImage()
      }
      break
  }
}

const previousImage = () => {
  if (currentIndex.value > 0) {
    currentIndex.value--
    emit('prev')
  }
}

const nextImage = () => {
  if (currentIndex.value < props.images.length - 1) {
    currentIndex.value++
    emit('next')
  }
}

const downloadImage = () => {
  window.open(route('images.download', currentImage.value.slug), '_blank')
}

const toggleLike = async () => {
  try {
    await router.post(
      route('images.like', currentImage.value.slug),
      {},
      {
        preserveState: true,
        preserveScroll: true,
      }
    )
    isLiked.value = !isLiked.value
  } catch (error) {
    console.error('Failed to toggle like:', error)
  }
}

const copyLink = () => {
  // Use current page URL if we're in lightbox, or construct the image URL
  const imageUrl = route('images.show', currentImage.value.slug)
  const fullUrl = window.location.origin + imageUrl
  
  navigator.clipboard.writeText(fullUrl).then(() => {
    console.log('Link copied to clipboard:', fullUrl)
    // TODO: Add a toast notification here
  }).catch(err => {
    console.error('Failed to copy link:', err)
  })
}


const shareImage = () => {
  if (navigator.share) {
    navigator.share({
      title: currentImage.value.title,
      text: currentImage.value.caption,
      url: window.location.origin + route('images.show', currentImage.value.slug),
    })
  } else {
    copyLink()
  }
}

onMounted(() => {
  nextTick(() => {
    lightboxRef.value?.focus()
  })
  // Prevent body scroll
  document.body.style.overflow = 'hidden'
})

onUnmounted(() => {
  // Restore body scroll
  document.body.style.overflow = ''
})
</script>
