<template>
  <Teleport to="body">
    <!-- Backdrop -->
    <div
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/90 backdrop-blur-sm"
      @click="handleBackdropClick"
      @keydown="handleKeydown"
      tabindex="0"
      ref="lightboxRef"
    >
      <!-- Loading Indicator -->
      <div
        v-if="loading"
        class="absolute inset-0 flex items-center justify-center"
      >
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-white"></div>
      </div>

      <!-- Lightbox Modal -->
      <div 
        class="relative bg-white rounded-xl shadow-2xl max-w-7xl w-full max-h-[95vh] overflow-hidden"
        :class="loading ? 'opacity-0' : 'opacity-100 animate-in zoom-in-95 duration-300'"
      >
        
        <!-- Header -->
        <div class="flex items-center justify-between p-4 border-b border-gray-200 bg-gray-50">
          <div class="flex items-center space-x-3 min-w-0 flex-1">
            <!-- Image counter -->
            <span v-if="images.length > 1" class="text-sm font-medium text-gray-600 flex-shrink-0">
              {{ currentIndex + 1 }} of {{ images.length }}
            </span>
            <div class="h-4 w-px bg-gray-300 flex-shrink-0" v-if="images.length > 1"></div>
            <h3 class="text-lg font-semibold text-gray-900 truncate">
              {{ currentImage.title || 'Untitled' }}
            </h3>
          </div>
          
          <!-- Header Actions -->
          <div class="flex items-center space-x-2 flex-shrink-0">
            <!-- Zoom Controls -->
            <div class="flex items-center space-x-1">
              <button
                @click="zoomOut"
                :disabled="zoomLevel <= 0.5"
                class="p-2 rounded-lg hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                title="Zoom Out (-)"
              >
                <MinusIcon class="h-4 w-4 text-gray-600" />
              </button>
              <span class="text-xs text-gray-500 min-w-[3rem] text-center">{{ Math.round(zoomLevel * 100) }}%</span>
              <button
                @click="zoomIn"
                :disabled="zoomLevel >= 3"
                class="p-2 rounded-lg hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                title="Zoom In (+)"
              >
                <PlusIcon class="h-4 w-4 text-gray-600" />
              </button>
              <button
                @click="resetZoom"
                class="px-2 py-1 rounded hover:bg-gray-100 transition-colors"
                title="Reset Zoom (0)"
              >
                <span class="text-xs text-gray-600 font-mono">1:1</span>
              </button>
            </div>

            <div class="h-6 w-px bg-gray-300"></div>

            <!-- Navigation -->
            <div v-if="images.length > 1" class="flex items-center space-x-1">
              <button
                @click="previousImage"
                :disabled="currentIndex === 0"
                class="p-2 rounded-lg hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                title="Previous (←)"
              >
                <ChevronLeftIcon class="h-5 w-5 text-gray-600" />
              </button>
              <button
                @click="nextImage"
                :disabled="currentIndex >= images.length - 1"
                class="p-2 rounded-lg hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                title="Next (→)"
              >
                <ChevronRightIcon class="h-5 w-5 text-gray-600" />
              </button>
            </div>

            <div class="h-6 w-px bg-gray-300"></div>
            
            <!-- Actions Menu -->
            <Dropdown align="right" width="48">
              <template #trigger>
                <button class="p-2 rounded-lg hover:bg-gray-100 transition-colors" title="More Actions">
                  <EllipsisVerticalIcon class="h-5 w-5 text-gray-600" />
                </button>
              </template>
              <template #content>
                <DropdownLink :href="route('images.show', currentImage.slug)">
                  <EyeIcon class="h-4 w-4 mr-2" />
                  View Full Page
                </DropdownLink>
                <DropdownLink
                  v-if="currentImage.can?.update"
                  :href="route('images.edit', currentImage.slug)"
                >
                  <PencilIcon class="h-4 w-4 mr-2" />
                  Edit Image
                </DropdownLink>
                <DropdownLink
                  v-if="currentImage.can?.download"
                  @click="downloadImage"
                >
                  <ArrowDownTrayIcon class="h-4 w-4 mr-2" />
                  Download
                </DropdownLink>
                <div class="border-t border-gray-100"></div>
                <DropdownLink @click="copyLink">
                  <LinkIcon class="h-4 w-4 mr-2" />
                  Copy Link
                </DropdownLink>
                <DropdownLink @click="shareImage">
                  <ShareIcon class="h-4 w-4 mr-2" />
                  Share
                </DropdownLink>
              </template>
            </Dropdown>
            
            <!-- Close Button -->
            <button
              @click="$emit('close')"
              class="p-2 rounded-lg hover:bg-gray-100 transition-colors"
              title="Close (Esc)"
            >
              <XMarkIcon class="h-5 w-5 text-gray-600" />
            </button>
          </div>
        </div>

        <!-- Main Content -->
        <div class="flex flex-col lg:flex-row" :style="{ height: contentHeight }">
          <!-- Image Display -->
          <div 
            class="flex-1 flex items-center justify-center bg-gray-900 relative overflow-hidden group"
            @wheel="handleWheel"
            @mousedown="startPan"
            @mousemove="handlePan"
            @mouseup="endPan"
            @mouseleave="endPan"
            ref="imageContainer"
            :class="{ 'cursor-grab': !isPanning && zoomLevel > 1, 'cursor-grabbing': isPanning }"
          >
            <img
              :src="currentImageUrl"
              :alt="currentImage.alt_text || currentImage.title"
              class="max-w-none transition-transform duration-200 select-none"
              :style="imageStyle"
              @click.stop
              @load="handleImageLoad"
              @error="handleImageError"
              draggable="false"
            >
            
            <!-- Navigation Overlays for Large Screens -->
            <button
              v-if="images.length > 1 && currentIndex > 0"
              @click="previousImage"
              class="absolute left-4 top-1/2 -translate-y-1/2 p-3 rounded-full bg-black/50 hover:bg-black/70 text-white transition-all duration-200 opacity-0 group-hover:opacity-100"
              title="Previous Image (←)"
            >
              <ChevronLeftIcon class="h-8 w-8" />
            </button>
            
            <button
              v-if="images.length > 1 && currentIndex < images.length - 1"
              @click="nextImage"
              class="absolute right-4 top-1/2 -translate-y-1/2 p-3 rounded-full bg-black/50 hover:bg-black/70 text-white transition-all duration-200 opacity-0 group-hover:opacity-100"
              title="Next Image (→)"
            >
              <ChevronRightIcon class="h-8 w-8" />
            </button>

            <!-- Image Info Overlay -->
            <div 
              v-if="showInfo"
              class="absolute top-4 left-4 bg-black/70 text-white px-3 py-2 rounded-lg text-sm"
            >
              <div>{{ currentImage.dimensions || 'Unknown' }}</div>
              <div>{{ currentImage.formatted_size || 'Unknown size' }}</div>
            </div>
          </div>

          <!-- Sidebar (toggleable on mobile) -->
          <div 
            v-if="showSidebar"
            class="lg:w-80 bg-white border-t lg:border-t-0 lg:border-l border-gray-200 overflow-y-auto"
            :class="{ 'absolute inset-x-0 bottom-0 top-auto max-h-1/2 lg:relative lg:max-h-none': isMobile }"
          >
            <div class="p-6">
              <!-- Toggle Sidebar Button (Mobile) -->
              <button
                v-if="isMobile"
                @click="showSidebar = false"
                class="absolute top-4 right-4 p-1 rounded-lg hover:bg-gray-100 lg:hidden"
              >
                <XMarkIcon class="h-5 w-5 text-gray-600" />
              </button>

              <!-- Owner Info -->
              <div class="flex items-center space-x-3 mb-4">
                <UserAvatar v-if="currentImage.owner" :user="currentImage.owner" size="sm" />
                <div>
                  <p class="text-sm font-medium text-gray-900">{{ currentImage.owner?.name || 'Unknown' }}</p>
                  <p class="text-xs text-gray-500">{{ formatDate(currentImage.created_at) }}</p>
                </div>
              </div>

              <!-- Caption -->
              <div v-if="currentImage.caption" class="mb-4">
                <p class="text-gray-700 text-sm leading-relaxed">
                  {{ currentImage.caption }}
                </p>
              </div>

              <!-- Actions -->
              <div class="flex items-center space-x-2 mb-6">
                <button
                  v-if="currentImage.can?.download"
                  @click="downloadImage"
                  class="flex items-center space-x-2 px-3 py-2 rounded-lg border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 transition-colors text-sm"
                >
                  <ArrowDownTrayIcon class="h-4 w-4" />
                  <span class="font-medium">Download</span>
                </button>
              </div>

              <!-- Metadata -->
              <div class="space-y-3 text-sm border-t border-gray-200 pt-4">
                <h4 class="font-medium text-gray-900">Details</h4>
                
                <div class="flex items-center justify-between">
                  <span class="text-gray-500">Dimensions</span>
                  <span class="text-gray-900">{{ currentImage.dimensions || 'N/A' }}</span>
                </div>
                
                <div class="flex items-center justify-between">
                  <span class="text-gray-500">Size</span>
                  <span class="text-gray-900">{{ currentImage.formatted_size || 'N/A' }}</span>
                </div>
                
                <div class="flex items-center justify-between">
                  <span class="text-gray-500">Views</span>
                  <span class="text-gray-900">{{ formatCount(currentImage.views_count || 0) }}</span>
                </div>

                <div v-if="currentImage.camera_make" class="flex items-center justify-between">
                  <span class="text-gray-500">Camera</span>
                  <span class="text-gray-900">{{ currentImage.camera_make }} {{ currentImage.camera_model }}</span>
                </div>
              </div>

              <!-- Tags -->
              <div v-if="currentImage.tags && currentImage.tags.length" class="mt-6">
                <h4 class="text-sm font-medium text-gray-900 mb-3">Tags</h4>
                <div class="flex flex-wrap gap-2">
                  <Link
                    v-for="tag in currentImage.tags"
                    :key="tag.id"
<!--                     :href="route('tags.show', tag.slug)" -->
                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 hover:bg-blue-100 transition-colors"
                    @click="$emit('close')"
                  >
                    {{ tag.name }}
                  </Link>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Bottom Controls (Mobile) -->
        <div v-if="isMobile" class="lg:hidden border-t border-gray-200 bg-gray-50 p-4">
          <div class="flex items-center justify-between">
            <button
              @click="showSidebar = !showSidebar"
              class="flex items-center space-x-2 px-3 py-2 rounded-lg bg-white border border-gray-200 text-gray-600"
            >
              <InformationCircleIcon class="h-4 w-4" />
              <span class="text-sm">Info</span>
            </button>

            <div class="flex items-center space-x-2">
              <button
                @click="shareImage"
                class="p-2 rounded-lg text-gray-600 hover:bg-gray-100 transition-colors"
              >
                <ShareIcon class="h-5 w-5" />
              </button>

              <button
                v-if="currentImage.can?.download"
                @click="downloadImage"
                class="p-2 rounded-lg text-gray-600 hover:bg-gray-100 transition-colors"
              >
                <ArrowDownTrayIcon class="h-5 w-5" />
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Keyboard Shortcuts Help -->
      <div 
        v-if="showHelp"
        class="absolute bottom-4 left-4 bg-black/80 text-white px-4 py-3 rounded-lg text-sm space-y-1"
      >
        <div class="font-medium mb-2">Keyboard Shortcuts:</div>
        <div>← → : Navigate images</div>
        <div>+ - : Zoom in/out</div>
        <div>0 : Reset zoom</div>
        <div>F : Toggle fullscreen</div>
        <div>I : Toggle image info</div>
        <div>H : Toggle this help</div>
        <div>Esc : Close lightbox</div>
      </div>

      <!-- Help Toggle with Notice -->
      <div class="absolute bottom-4 right-4 flex flex-col items-end space-y-2">
        <!-- Help Notice (shows on first load) -->
        <div 
          v-if="showHelpNotice"
          class="bg-black/80 text-white px-3 py-2 rounded-lg text-sm animate-pulse"
        >
          Press H for help
        </div>
        
        <button
          @click="toggleHelp"
          class="p-2 rounded-full bg-black/50 hover:bg-black/70 text-white transition-colors"
          title="Keyboard Shortcuts (H)"
        >
          <QuestionMarkCircleIcon class="h-5 w-5" />
        </button>
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
  EllipsisVerticalIcon,
  PlusIcon,
  MinusIcon,
  EyeIcon,
  LinkIcon,
  ShareIcon,
  InformationCircleIcon,
  QuestionMarkCircleIcon,
} from '@heroicons/vue/24/outline'

import Dropdown from '@/Components/Dropdown.vue'
import DropdownLink from '@/Components/DropdownLink.vue'
import UserAvatar from '@/Components/UserAvatar.vue'

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

// Refs
const lightboxRef = ref(null)
const imageContainer = ref(null)
const currentIndex = ref(props.initialIndex)
const loading = ref(true)
const showSidebar = ref(true)
const showInfo = ref(false)
const showHelp = ref(false)
const isFullscreen = ref(false)
const showHelpNotice = ref(true)

// Zoom and Pan
const zoomLevel = ref(1)
const panX = ref(0)
const panY = ref(0)
const isPanning = ref(false)
const lastPanPoint = ref({ x: 0, y: 0 })

// Responsive
const isMobile = ref(window.innerWidth < 1024)

// Computed
const currentImage = computed(() => props.images[currentIndex.value])

const currentImageUrl = computed(() => {
  const img = currentImage.value
  if (img.storage_path) {
    return `http://localhost:9000/gallery-images/${img.storage_path}`
  }
  return img.url || '/images/placeholder.jpg'
})

const contentHeight = computed(() => {
  return isFullscreen.value ? '100vh' : 'calc(95vh - 4rem)'
})

const imageStyle = computed(() => ({
  transform: `scale(${zoomLevel.value}) translate(${panX.value}px, ${panY.value}px)`,
  transformOrigin: 'center center',
}))

// Watch for changes
watch(() => props.initialIndex, (newIndex) => {
  currentIndex.value = newIndex
  resetZoomAndPan()
})

watch(currentImage, () => {
  loading.value = true
  resetZoomAndPan()
})

// Methods
const resetZoomAndPan = () => {
  zoomLevel.value = 1
  panX.value = 0
  panY.value = 0
}

const handleImageLoad = () => {
  loading.value = false
}

const handleImageError = () => {
  loading.value = false
  console.error('Failed to load image:', currentImageUrl.value)
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  })
}

const formatCount = (count) => {
  if (count >= 1000000) return Math.floor(count / 1000000) + 'M'
  if (count >= 1000) return Math.floor(count / 1000) + 'K'
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
      if (currentIndex.value > 0) previousImage()
      break
    case 'ArrowRight':
      if (currentIndex.value < props.images.length - 1) nextImage()
      break
    case '+':
    case '=':
      zoomIn()
      break
    case '-':
      zoomOut()
      break
    case '0':
      resetZoom()
      break
    case 'f':
    case 'F':
      toggleFullscreen()
      break
    case 'i':
    case 'I':
      showInfo.value = !showInfo.value
      break
    case 'h':
    case 'H':
      toggleHelp()
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

const zoomIn = () => {
  if (zoomLevel.value < 3) {
    zoomLevel.value = Math.min(3, zoomLevel.value + 0.25)
  }
}

const zoomOut = () => {
  if (zoomLevel.value > 0.5) {
    zoomLevel.value = Math.max(0.5, zoomLevel.value - 0.25)
    if (zoomLevel.value <= 1) {
      panX.value = 0
      panY.value = 0
    }
  }
}

const resetZoom = () => {
  zoomLevel.value = 1
  panX.value = 0
  panY.value = 0
}

const handleWheel = (event) => {
  event.preventDefault()
  if (event.deltaY < 0) {
    zoomIn()
  } else {
    zoomOut()
  }
}

const startPan = (event) => {
  if (zoomLevel.value > 1) {
    isPanning.value = true
    lastPanPoint.value = { x: event.clientX, y: event.clientY }
  }
}

const handlePan = (event) => {
  if (isPanning.value && zoomLevel.value > 1) {
    const deltaX = event.clientX - lastPanPoint.value.x
    const deltaY = event.clientY - lastPanPoint.value.y
    
    panX.value += deltaX
    panY.value += deltaY
    
    lastPanPoint.value = { x: event.clientX, y: event.clientY }
  }
}

const endPan = () => {
  isPanning.value = false
}

const toggleFullscreen = () => {
  if (!document.fullscreenElement) {
    lightboxRef.value?.requestFullscreen?.() || document.documentElement.requestFullscreen()
    isFullscreen.value = true
  } else {
    document.exitFullscreen()
    isFullscreen.value = false
  }
}

const downloadImage = () => {
  const link = document.createElement('a')
  link.href = route('images.download', currentImage.value.slug)
  link.download = currentImage.value.original_filename || 'image'
  link.click()
}

const copyLink = () => {
  const imageRoute = route('images.show', currentImage.value.slug)
  const fullUrl = imageRoute.startsWith('http') ? imageRoute : (window.location.origin + imageRoute)
  
  navigator.clipboard.writeText(fullUrl).then(() => {
    alert('Link copied to clipboard!')
  }).catch(err => {
    console.error('Failed to copy link:', err)
    prompt('Copy this link:', fullUrl)
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

const toggleHelp = () => {
  showHelp.value = !showHelp.value
  showHelpNotice.value = false
}

const handleResize = () => {
  isMobile.value = window.innerWidth < 1024
}

onMounted(() => {
  setTimeout(() => {
    showHelpNotice.value = false
  }, 3000)
  
  nextTick(() => {
    lightboxRef.value?.focus()
  })
  
  document.body.style.overflow = 'hidden'
  window.addEventListener('resize', handleResize)
  
  document.addEventListener('fullscreenchange', () => {
    isFullscreen.value = !!document.fullscreenElement
  })
})

onUnmounted(() => {
  document.body.style.overflow = ''
  window.removeEventListener('resize', handleResize)
  if (document.fullscreenElement) {
    document.exitFullscreen()
  }
})
</script>
