<template>
  <Teleport to="body">
    <!-- Enhanced Backdrop with Animated Mesh Background -->
    <div
      class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-xl"
      @click="handleBackdropClick"
      @keydown="handleKeydown"
      tabindex="0"
      ref="lightboxRef"
      style="background: linear-gradient(135deg, rgba(15, 23, 42, 0.95) 0%, rgba(30, 27, 75, 0.95) 50%, rgba(15, 23, 42, 0.95) 100%);"
    >
      <!-- Animated Mesh Background -->
      <div class="absolute inset-0 opacity-30">
        <svg class="w-full h-full animate-mesh-shift" viewBox="0 0 100 100" preserveAspectRatio="none">
          <defs>
            <linearGradient id="meshGrad" x1="0%" y1="0%" x2="100%" y2="100%" gradientUnits="objectBoundingBox">
              <stop offset="0%" style="stop-color:#8b5cf6;stop-opacity:0.3" class="animate-gradient-shift-1">
                <animate attributeName="stop-color" values="#8b5cf6;#06b6d4;#a855f7;#8b5cf6" dur="8s" repeatCount="indefinite"/>
              </stop>
              <stop offset="50%" style="stop-color:#06b6d4;stop-opacity:0.2" class="animate-gradient-shift-2">
                <animate attributeName="stop-color" values="#06b6d4;#a855f7;#8b5cf6;#06b6d4" dur="8s" repeatCount="indefinite"/>
              </stop>
              <stop offset="100%" style="stop-color:#a855f7;stop-opacity:0.3" class="animate-gradient-shift-3">
                <animate attributeName="stop-color" values="#a855f7;#8b5cf6;#06b6d4;#a855f7" dur="8s" repeatCount="indefinite"/>
              </stop>
            </linearGradient>
          </defs>
          <rect width="100%" height="100%" fill="url(#meshGrad)"/>
          <path d="M0,0 Q50,30 100,0 L100,100 Q50,70 0,100 Z" fill="url(#meshGrad)" opacity="0.4">
            <animateTransform attributeName="transform" type="translate" values="0,0; 5,3; 0,0" dur="12s" repeatCount="indefinite"/>
          </path>
        </svg>
      </div>

      <!-- Floating Particles -->
      <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div v-for="i in 20" :key="i" 
             class="absolute w-1 h-1 rounded-full animate-float-particle"
             :style="{
               left: Math.random() * 100 + '%',
               top: Math.random() * 100 + '%',
               background: ['#8b5cf6', '#06b6d4', '#a855f7'][Math.floor(Math.random() * 3)],
               animationDelay: Math.random() * 8 + 's',
               animationDuration: (8 + Math.random() * 4) + 's'
             }">
        </div>
      </div>

      <!-- Loading Indicator -->
      <div
        v-if="loading"
        class="absolute inset-0 flex items-center justify-center z-10"
      >
        <div class="relative">
          <div class="w-16 h-16 border-4 border-violet-500/30 border-t-violet-500 rounded-full animate-spin"></div>
          <div class="absolute inset-0 w-16 h-16 border-4 border-transparent border-t-cyan-500 rounded-full animate-spin animation-delay-150"></div>
        </div>
      </div>

      <!-- Enhanced Lightbox Modal -->
      <div 
        class="relative bg-gradient-to-br from-slate-900/90 via-slate-800/90 to-slate-900/90 backdrop-blur-2xl rounded-3xl shadow-2xl border border-white/10 max-w-7xl w-full max-h-[95vh] overflow-hidden transition-all duration-700 ease-out"
        :class="loading ? 'opacity-0 scale-95' : 'opacity-100 scale-100 animate-slide-up'"
        style="box-shadow: 0 25px 50px -12px rgba(139, 92, 246, 0.25), 0 0 0 1px rgba(255, 255, 255, 0.05);"
      >
        
        <!-- Enhanced Header -->
        <div class="flex items-center justify-between p-6 border-b border-white/10 bg-gradient-to-r from-slate-800/50 via-slate-700/50 to-slate-800/50 backdrop-blur-xl">
          <div class="flex items-center space-x-4 min-w-0 flex-1">
            <!-- Image counter with glow -->
            <span v-if="images && images.length > 1" class="text-sm font-medium bg-gradient-to-r from-violet-400 to-cyan-400 bg-clip-text text-transparent flex-shrink-0 px-3 py-1 rounded-full bg-white/5 border border-white/10">
              {{ currentIndex + 1 }} of {{ images.length }}
            </span>
            <div class="h-6 w-px bg-gradient-to-b from-violet-500/50 to-cyan-500/50 flex-shrink-0" v-if="images && images.length > 1"></div>
            <h3 class="text-xl font-bold bg-gradient-to-r from-white via-slate-200 to-white bg-clip-text text-transparent truncate animate-text-shimmer">
              {{ currentImage?.title || 'Untitled' }}
            </h3>
          </div>
          
          <!-- Enhanced Header Actions -->
          <div class="flex items-center space-x-3 flex-shrink-0">
            <!-- Zoom Controls with glassmorphism -->
            <div class="flex items-center space-x-2 bg-white/5 backdrop-blur-xl border border-white/10 rounded-xl p-2">
              <button
                @click="zoomOut"
                :disabled="zoomLevel <= 0.5"
                class="p-2 rounded-lg bg-white/10 hover:bg-violet-500/20 disabled:opacity-30 disabled:cursor-not-allowed transition-all duration-300 hover:scale-110 group"
                title="Zoom Out (-)"
              >
                <MinusIcon class="h-4 w-4 text-slate-300 group-hover:text-violet-300 transition-colors" />
              </button>
              <span class="text-xs text-slate-300 min-w-[3rem] text-center font-mono bg-slate-800/50 px-2 py-1 rounded">{{ Math.round(zoomLevel * 100) }}%</span>
              <button
                @click="zoomIn"
                :disabled="zoomLevel >= 3"
                class="p-2 rounded-lg bg-white/10 hover:bg-cyan-500/20 disabled:opacity-30 disabled:cursor-not-allowed transition-all duration-300 hover:scale-110 group"
                title="Zoom In (+)"
              >
                <PlusIcon class="h-4 w-4 text-slate-300 group-hover:text-cyan-300 transition-colors" />
              </button>
              <button
                @click="resetZoom"
                class="px-3 py-2 rounded-lg bg-white/10 hover:bg-purple-500/20 transition-all duration-300 hover:scale-105"
                title="Reset Zoom (0)"
              >
                <span class="text-xs text-slate-300 font-mono">1:1</span>
              </button>
            </div>

            <div class="h-8 w-px bg-gradient-to-b from-transparent via-white/20 to-transparent"></div>

            <!-- Navigation with enhanced styling -->
            <div v-if="images && images.length > 1" class="flex items-center space-x-2 bg-white/5 backdrop-blur-xl border border-white/10 rounded-xl p-2">
              <button
                @click="previousImage"
                :disabled="currentIndex === 0"
                class="p-2 rounded-lg bg-white/10 hover:bg-violet-500/20 disabled:opacity-30 disabled:cursor-not-allowed transition-all duration-300 hover:scale-110 group"
                title="Previous (←)"
              >
                <ChevronLeftIcon class="h-5 w-5 text-slate-300 group-hover:text-violet-300 transition-colors" />
              </button>
              <button
                @click="nextImage"
                :disabled="currentIndex >= images.length - 1"
                class="p-2 rounded-lg bg-white/10 hover:bg-cyan-500/20 disabled:opacity-30 disabled:cursor-not-allowed transition-all duration-300 hover:scale-110 group"
                title="Next (→)"
              >
                <ChevronRightIcon class="h-5 w-5 text-slate-300 group-hover:text-cyan-300 transition-colors" />
              </button>
            </div>

            <div class="h-8 w-px bg-gradient-to-b from-transparent via-white/20 to-transparent"></div>
            
            <!-- Enhanced Actions Menu -->
            <Dropdown align="right" width="48">
              <template #trigger>
                <button class="p-3 rounded-xl bg-white/10 hover:bg-purple-500/20 backdrop-blur-xl border border-white/10 transition-all duration-300 hover:scale-110 hover:border-purple-400/30 group" title="More Actions">
                  <EllipsisVerticalIcon class="h-5 w-5 text-slate-300 group-hover:text-purple-300 transition-colors" />
                </button>
              </template>
              <template #content>
                <div class="bg-slate-800/95 backdrop-blur-2xl border border-white/10 rounded-2xl shadow-2xl overflow-hidden">
                  <DropdownLink v-if="currentImage?.id" @click="viewFullPage" class="flex items-center px-4 py-3 text-slate-300 hover:bg-violet-500/20 hover:text-violet-300 transition-all duration-300">
                    <EyeIcon class="h-4 w-4 mr-3" />
                    View Full Page
                  </DropdownLink>
                  <DropdownLink
                    v-if="currentImage?.can?.update"
                    @click="editImage"
                    class="flex items-center px-4 py-3 text-slate-300 hover:bg-cyan-500/20 hover:text-cyan-300 transition-all duration-300"
                  >
                    <PencilIcon class="h-4 w-4 mr-3" />
                    Edit Image
                  </DropdownLink>
                  <DropdownLink
                    v-if="currentImage?.can?.download"
                    @click="downloadImage"
                    class="flex items-center px-4 py-3 text-slate-300 hover:bg-purple-500/20 hover:text-purple-300 transition-all duration-300"
                  >
                    <ArrowDownTrayIcon class="h-4 w-4 mr-3" />
                    Download
                  </DropdownLink>
                  <div class="border-t border-white/10"></div>
                  <DropdownLink @click="copyLink" class="flex items-center px-4 py-3 text-slate-300 hover:bg-violet-500/20 hover:text-violet-300 transition-all duration-300">
                    <LinkIcon class="h-4 w-4 mr-3" />
                    Copy Link
                  </DropdownLink>
                  <DropdownLink @click="shareImage" class="flex items-center px-4 py-3 text-slate-300 hover:bg-cyan-500/20 hover:text-cyan-300 transition-all duration-300">
                    <ShareIcon class="h-4 w-4 mr-3" />
                    Share
                  </DropdownLink>
                </div>
              </template>
            </Dropdown>
            
            <!-- Enhanced Close Button -->
            <button
              @click="$emit('close')"
              class="p-3 rounded-xl bg-red-500/10 hover:bg-red-500/20 backdrop-blur-xl border border-red-400/20 hover:border-red-400/40 transition-all duration-300 hover:scale-110 group"
              title="Close (Esc)"
            >
              <XMarkIcon class="h-5 w-5 text-red-300 group-hover:text-red-200 transition-colors" />
            </button>
          </div>
        </div>

        <!-- Main Content -->
        <div class="flex flex-col lg:flex-row" :style="{ height: contentHeight }">
          <!-- Enhanced Image Display -->
          <div 
            class="flex-1 flex items-center justify-center bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 relative overflow-hidden group"
            @wheel="handleWheel"
            @mousedown="startPan"
            @mousemove="handlePan"
            @mouseup="endPan"
            @mouseleave="endPan"
            ref="imageContainer"
            :class="{ 'cursor-grab': !isPanning && zoomLevel > 1, 'cursor-grabbing': isPanning }"
          >
            <img
              v-if="currentImage"
              :src="currentImageUrl"
              :alt="currentImage.alt_text || currentImage.title"
              class="max-w-none transition-all duration-500 ease-out select-none rounded-lg shadow-2xl"
              :style="imageStyle"
              @click.stop
              @load="handleImageLoad"
              @error="handleImageError"
              draggable="false"
            >
            
            <!-- Enhanced Navigation Overlays -->
            <button
              v-if="images && images.length > 1 && currentIndex > 0"
              @click="previousImage"
              class="absolute left-6 top-1/2 -translate-y-1/2 p-4 rounded-2xl bg-black/30 hover:bg-violet-500/30 backdrop-blur-xl border border-white/10 hover:border-violet-400/50 text-white transition-all duration-300 opacity-0 group-hover:opacity-100 hover:scale-110 shadow-2xl"
              title="Previous Image (←)"
            >
              <ChevronLeftIcon class="h-8 w-8" />
            </button>
            
            <button
              v-if="images && images.length > 1 && currentIndex < images.length - 1"
              @click="nextImage"
              class="absolute right-6 top-1/2 -translate-y-1/2 p-4 rounded-2xl bg-black/30 hover:bg-cyan-500/30 backdrop-blur-xl border border-white/10 hover:border-cyan-400/50 text-white transition-all duration-300 opacity-0 group-hover:opacity-100 hover:scale-110 shadow-2xl"
              title="Next Image (→)"
            >
              <ChevronRightIcon class="h-8 w-8" />
            </button>

            <!-- Enhanced Image Info Overlay -->
            <div 
              v-if="showInfo && currentImage"
              class="absolute top-6 left-6 bg-black/50 backdrop-blur-xl border border-white/20 text-white px-4 py-3 rounded-xl text-sm animate-fade-in shadow-2xl"
            >
              <div class="text-violet-300 font-medium">{{ getImageDimensions(currentImage) }}</div>
              <div class="text-cyan-300">{{ getImageSize(currentImage) }}</div>
            </div>
          </div>

          <!-- Enhanced Sidebar -->
          <div 
            v-if="showSidebar && currentImage"
            class="lg:w-80 bg-gradient-to-b from-slate-800/80 via-slate-700/80 to-slate-800/80 backdrop-blur-2xl border-t lg:border-t-0 lg:border-l border-white/10 overflow-y-auto transition-all duration-500"
            :class="{ 'absolute inset-x-0 bottom-0 top-auto max-h-1/2 lg:relative lg:max-h-none animate-slide-up': isMobile }"
          >
            <div class="p-6">
              <!-- Enhanced Toggle Sidebar Button (Mobile) -->
              <button
                v-if="isMobile"
                @click="showSidebar = false"
                class="absolute top-4 right-4 p-2 rounded-xl bg-white/10 hover:bg-red-500/20 backdrop-blur-xl border border-white/10 hover:border-red-400/30 transition-all duration-300 lg:hidden group"
              >
                <XMarkIcon class="h-5 w-5 text-slate-300 group-hover:text-red-300 transition-colors" />
              </button>

              <!-- Enhanced Owner Info -->
              <div v-if="currentImage.owner" class="flex items-center space-x-4 mb-6 animate-fade-in-up">
                <div class="w-12 h-12 bg-gradient-to-br from-violet-500 to-cyan-500 rounded-full flex items-center justify-center shadow-lg border border-white/20">
                  <span class="text-sm font-bold text-white">
                    {{ currentImage.owner.name?.charAt(0)?.toUpperCase() || '?' }}
                  </span>
                </div>
                <div>
                  <p class="text-base font-semibold bg-gradient-to-r from-slate-200 to-slate-300 bg-clip-text text-transparent">{{ currentImage.owner.name || 'Unknown' }}</p>
                  <p class="text-sm text-slate-400">{{ formatDate(currentImage.created_at) }}</p>
                </div>
              </div>

              <!-- Enhanced Caption -->
              <div v-if="currentImage.caption" class="mb-6 animate-fade-in-up animation-delay-100">
                <p class="text-slate-300 text-sm leading-relaxed bg-white/5 p-4 rounded-xl border border-white/10 backdrop-blur-sm">
                  {{ currentImage.caption }}
                </p>
              </div>

              <!-- Enhanced Actions -->
              <div class="flex items-center space-x-3 mb-8 animate-fade-in-up animation-delay-200">
                <button
                  v-if="currentImage.can?.download"
                  @click="downloadImage"
                  class="flex items-center space-x-2 px-4 py-3 rounded-xl bg-gradient-to-r from-violet-500/20 to-purple-500/20 border border-violet-400/30 text-slate-200 hover:from-violet-500/30 hover:to-purple-500/30 hover:border-violet-400/50 hover:text-white transition-all duration-300 text-sm font-medium backdrop-blur-sm hover:scale-105 shadow-lg"
                >
                  <ArrowDownTrayIcon class="h-4 w-4" />
                  <span>Download</span>
                </button>
              </div>

              <!-- Enhanced Metadata -->
              <div class="space-y-4 text-sm border-t border-white/10 pt-6 animate-fade-in-up animation-delay-300">
                <h4 class="font-semibold bg-gradient-to-r from-violet-300 to-cyan-300 bg-clip-text text-transparent text-base">Details</h4>
                
                <div class="flex items-center justify-between py-2 px-3 rounded-lg bg-white/5 border border-white/10">
                  <span class="text-slate-400">Dimensions</span>
                  <span class="text-slate-200 font-medium">{{ getImageDimensions(currentImage) }}</span>
                </div>
                
                <div class="flex items-center justify-between py-2 px-3 rounded-lg bg-white/5 border border-white/10">
                  <span class="text-slate-400">Size</span>
                  <span class="text-slate-200 font-medium">{{ getImageSize(currentImage) }}</span>
                </div>
                
                <div class="flex items-center justify-between py-2 px-3 rounded-lg bg-white/5 border border-white/10">
                  <span class="text-slate-400">Views</span>
                  <span class="text-slate-200 font-medium">{{ formatCount(currentImage.views_count || 0) }}</span>
                </div>

                <div v-if="currentImage.camera_make" class="flex items-center justify-between py-2 px-3 rounded-lg bg-white/5 border border-white/10">
                  <span class="text-slate-400">Camera</span>
                  <span class="text-slate-200 font-medium">{{ currentImage.camera_make }} {{ currentImage.camera_model || '' }}</span>
                </div>

                <div v-if="currentImage.mime_type" class="flex items-center justify-between py-2 px-3 rounded-lg bg-white/5 border border-white/10">
                  <span class="text-slate-400">Format</span>
                  <span class="text-slate-200 font-medium">{{ currentImage.mime_type?.split('/')?.pop()?.toUpperCase() || 'Unknown' }}</span>
                </div>
              </div>

              <!-- Enhanced Tags -->
              <div v-if="currentImage.tags && currentImage.tags.length" class="mt-8 animate-fade-in-up animation-delay-400">
                <h4 class="text-base font-semibold bg-gradient-to-r from-violet-300 to-cyan-300 bg-clip-text text-transparent mb-4">Tags</h4>
                <div class="flex flex-wrap gap-2">
                  <button
                    v-for="tag in currentImage.tags"
                    :key="tag.id"
                    @click="searchByTag(tag.name)"
                    class="inline-flex items-center px-3 py-2 rounded-full text-xs font-medium bg-gradient-to-r from-blue-500/20 to-cyan-500/20 text-cyan-300 border border-cyan-400/30 hover:from-blue-500/30 hover:to-cyan-500/30 hover:border-cyan-400/50 hover:text-cyan-200 transition-all duration-300 backdrop-blur-sm hover:scale-105"
                  >
                    {{ tag.name }}
                  </button>
                </div>
              </div>

              <!-- Enhanced Album Info -->
              <div v-if="currentImage.album" class="mt-8 animate-fade-in-up animation-delay-500">
                <h4 class="text-base font-semibold bg-gradient-to-r from-violet-300 to-cyan-300 bg-clip-text text-transparent mb-4">Album</h4>
                <button
                  @click="goToAlbum"
                  class="flex items-center space-x-2 text-sm bg-gradient-to-r from-purple-500/20 to-violet-500/20 border border-purple-400/30 px-4 py-2 rounded-xl hover:from-purple-500/30 hover:to-violet-500/30 hover:border-purple-400/50 text-purple-300 hover:text-purple-200 transition-all duration-300 backdrop-blur-sm hover:scale-105"
                >
                  <span>{{ currentImage.album.title }}</span>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Enhanced Bottom Controls (Mobile) -->
        <div v-if="isMobile" class="lg:hidden border-t border-white/10 bg-gradient-to-r from-slate-800/50 via-slate-700/50 to-slate-800/50 backdrop-blur-xl p-4">
          <div class="flex items-center justify-between">
            <button
              @click="showSidebar = !showSidebar"
              class="flex items-center space-x-2 px-4 py-3 rounded-xl bg-white/10 border border-white/20 text-slate-300 hover:bg-violet-500/20 hover:border-violet-400/40 hover:text-violet-300 transition-all duration-300 backdrop-blur-sm"
            >
              <InformationCircleIcon class="h-4 w-4" />
              <span class="text-sm font-medium">Info</span>
            </button>

            <div class="flex items-center space-x-3">
              <button
                @click="shareImage"
                class="p-3 rounded-xl bg-white/10 border border-white/20 text-slate-300 hover:bg-cyan-500/20 hover:border-cyan-400/40 hover:text-cyan-300 transition-all duration-300 backdrop-blur-sm hover:scale-110"
              >
                <ShareIcon class="h-5 w-5" />
              </button>

              <button
                v-if="currentImage?.can?.download"
                @click="downloadImage"
                class="p-3 rounded-xl bg-white/10 border border-white/20 text-slate-300 hover:bg-purple-500/20 hover:border-purple-400/40 hover:text-purple-300 transition-all duration-300 backdrop-blur-sm hover:scale-110"
              >
                <ArrowDownTrayIcon class="h-5 w-5" />
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Enhanced Keyboard Shortcuts Help -->
      <div 
        v-if="showHelp"
        class="absolute bottom-4 left-4 bg-black/80 backdrop-blur-2xl border border-white/20 text-white px-6 py-4 rounded-2xl text-sm space-y-2 animate-fade-in shadow-2xl max-w-xs"
      >
        <div class="font-bold mb-3 bg-gradient-to-r from-violet-300 to-cyan-300 bg-clip-text text-transparent">Keyboard Shortcuts:</div>
        <div class="text-slate-300">← → : Navigate images</div>
        <div class="text-slate-300">+ - : Zoom in/out</div>
        <div class="text-slate-300">0 : Reset zoom</div>
        <div class="text-slate-300">F : Toggle fullscreen</div>
        <div class="text-slate-300">I : Toggle image info</div>
        <div class="text-slate-300">H : Toggle this help</div>
        <div class="text-slate-300">Esc : Close lightbox</div>
      </div>

      <!-- Enhanced Help Toggle -->
      <div class="absolute bottom-4 right-4 flex flex-col items-end space-y-3">
        <!-- Enhanced Help Notice -->
        <div 
          v-if="showHelpNotice"
          class="bg-black/80 backdrop-blur-2xl border border-violet-400/30 text-violet-300 px-4 py-2 rounded-xl text-sm animate-pulse shadow-2xl"
        >
          Press H for help
        </div>
        
        <button
          @click="toggleHelp"
          class="p-3 rounded-2xl bg-black/50 hover:bg-violet-500/30 backdrop-blur-xl border border-white/20 hover:border-violet-400/50 text-white transition-all duration-300 hover:scale-110 shadow-2xl group"
          title="Keyboard Shortcuts (H)"
        >
          <QuestionMarkCircleIcon class="h-6 w-6 group-hover:text-violet-300 transition-colors" />
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

const props = defineProps({
  images: {
    type: Array,
    required: true,
    default: () => []
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
const isMobile = ref(false)

// Computed
const currentImage = computed(() => {
  if (!props.images || props.images.length === 0) return null
  return props.images[currentIndex.value] || null
})

const currentImageUrl = computed(() => {
  if (!currentImage.value) return '/images/placeholder.jpg'
  
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

// FIXED: Watch for changes with proper validation
watch(() => props.initialIndex, (newIndex) => {
  if (newIndex >= 0 && newIndex < props.images.length) {
    currentIndex.value = newIndex
    resetZoomAndPan()
  }
})

watch(currentImage, () => {
  loading.value = true
  resetZoomAndPan()
}, { immediate: true })

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

// FIXED: Helper methods for image info
const getImageDimensions = (image) => {
  if (!image) return 'Unknown'
  if (image.width && image.height) {
    return `${image.width} × ${image.height}`
  }
  return image.dimensions || 'Unknown'
}

const getImageSize = (image) => {
  if (!image) return 'Unknown'
  if (image.formatted_size) {
    return image.formatted_size
  }
  if (image.size_bytes) {
    return formatBytes(image.size_bytes)
  }
  return 'Unknown'
}

const formatBytes = (bytes) => {
  if (bytes === 0) return '0 B'
  const k = 1024
  const sizes = ['B', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i]
}

const formatDate = (date) => {
  if (!date) return 'Unknown'
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  })
}

const formatCount = (count) => {
  if (!count || count === 0) return '0'
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
    lightboxRef.value?.requestFullscreen?.()
    isFullscreen.value = true
  } else {
    document.exitFullscreen()
    isFullscreen.value = false
  }
}

// FIXED: Action methods with proper error handling
const viewFullPage = () => {
  if (currentImage.value?.slug) {
    emit('close')
    router.visit(route('images.show', currentImage.value.slug))
  } else if (currentImage.value?.id) {
    // Fallback to ID if slug not available
    emit('close')
    router.visit(route('images.show', currentImage.value.id))
  }
}


const editImage = () => {
  if (currentImage.value?.id) {
    emit('close')
    router.visit(route('images.edit', currentImage.value.id))
  }
}

const downloadImage = () => {
  if (!currentImage.value?.id) return
  
  const link = document.createElement('a')
  link.href = route('images.download', currentImage.value.id)
  link.download = currentImage.value.original_filename || 'image'
  link.click()
}

const copyLink = () => {
  if (!currentImage.value?.id) return
  
  try {
    const imageRoute = route('images.show', currentImage.value.id)
    const fullUrl = imageRoute.startsWith('http') ? imageRoute : (window.location.origin + imageRoute)
    
    navigator.clipboard.writeText(fullUrl).then(() => {
      alert('Link copied to clipboard!')
    }).catch(() => {
      prompt('Copy this link:', fullUrl)
    })
  } catch (error) {
    console.error('Failed to copy link:', error)
  }
}

const shareImage = () => {
  if (!currentImage.value?.id) return
  
  if (navigator.share) {
    navigator.share({
      title: currentImage.value.title,
      text: currentImage.value.caption,
      url: window.location.origin + route('images.show', currentImage.value.id),
    }).catch(() => {
      copyLink()
    })
  } else {
    copyLink()
  }
}

const searchByTag = (tagName) => {
  emit('close')
  router.visit(route('gallery.index', { tag: tagName }))
}

const goToAlbum = () => {
  if (currentImage.value?.album?.slug) {
    emit('close')
    router.visit(route('albums.show', currentImage.value.album.slug))
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
  // Set initial mobile state
  isMobile.value = window.innerWidth < 1024
  
  // Hide help notice after 3 seconds
  setTimeout(() => {
    showHelpNotice.value = false
  }, 3000)
  
  // Focus the lightbox for keyboard events
  nextTick(() => {
    lightboxRef.value?.focus()
  })
  
  // Prevent body scroll
  document.body.style.overflow = 'hidden'
  
  // Add event listeners
  window.addEventListener('resize', handleResize)
  
  document.addEventListener('fullscreenchange', () => {
    isFullscreen.value = !!document.fullscreenElement
  })
})

onUnmounted(() => {
  // Restore body scroll
  document.body.style.overflow = ''
  
  // Remove event listeners
  window.removeEventListener('resize', handleResize)
  
  // Exit fullscreen if active
  if (document.fullscreenElement) {
    document.exitFullscreen()
  }
})
</script>

<style scoped>
@keyframes mesh-shift {
  0%, 100% { transform: translate(0, 0) rotate(0deg); }
  33% { transform: translate(30px, -30px) rotate(1deg); }
  66% { transform: translate(-20px, 20px) rotate(-1deg); }
}

@keyframes float-particle {
  0%, 100% { 
    transform: translateY(0px) rotate(0deg);
    opacity: 0.4;
  }
  50% { 
    transform: translateY(-20px) rotate(180deg);
    opacity: 0.8;
  }
}

@keyframes text-shimmer {
  0% { background-position: -200% center; }
  100% { background-position: 200% center; }
}

@keyframes slide-up {
  from {
    opacity: 0;
    transform: translateY(50px) scale(0.95);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

@keyframes fade-in {
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes fade-in-up {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-mesh-shift {
  animation: mesh-shift 20s ease-in-out infinite;
}

.animate-float-particle {
  animation: float-particle 8s ease-in-out infinite;
}

.animate-text-shimmer {
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
  background-size: 200% 100%;
  animation: text-shimmer 3s ease-in-out infinite;
}

.animate-slide-up {
  animation: slide-up 0.7s ease-out;
}

.animate-fade-in {
  animation: fade-in 0.5s ease-out;
}

.animate-fade-in-up {
  animation: fade-in-up 0.6s ease-out forwards;
  opacity: 0;
}

.animation-delay-100 {
  animation-delay: 0.1s;
}

.animation-delay-150 {
  animation-delay: 0.15s;
}

.animation-delay-200 {
  animation-delay: 0.2s;
}

.animation-delay-300 {
  animation-delay: 0.3s;
}

.animation-delay-400 {
  animation-delay: 0.4s;
}

.animation-delay-500 {
  animation-delay: 0.5s;
}
</style>