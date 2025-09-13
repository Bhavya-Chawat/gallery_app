<template>
  <div class="relative">
    <!-- Animated Background Mesh for Container -->
    <div class="absolute inset-0 opacity-5 pointer-events-none">
      <svg class="w-full h-full animate-float-grid" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
        <defs>
          <linearGradient id="grid-mesh" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" class="animate-gradient-shift">
              <animate attributeName="stop-color" values="#8b5cf6;#06b6d4;#a855f7;#8b5cf6" dur="10s" repeatCount="indefinite"/>
            </stop>
            <stop offset="100%" class="animate-gradient-shift">
              <animate attributeName="stop-color" values="#06b6d4;#a855f7;#8b5cf6;#06b6d4" dur="10s" repeatCount="indefinite"/>
            </stop>
          </linearGradient>
        </defs>
        <rect x="10" y="10" width="80" height="20" fill="none" stroke="url(#grid-mesh)" stroke-width="0.2" opacity="0.4" rx="4"/>
        <rect x="10" y="35" width="80" height="50" fill="none" stroke="url(#grid-mesh)" stroke-width="0.2" opacity="0.4" rx="6"/>
        <circle cx="20" cy="20" r="0.8" fill="url(#grid-mesh)" opacity="0.6"/>
        <circle cx="80" cy="20" r="0.8" fill="url(#grid-mesh)" opacity="0.6"/>
        <circle cx="50" cy="60" r="0.8" fill="url(#grid-mesh)" opacity="0.6"/>
      </svg>
    </div>

    <!-- Floating Particles -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
      <div class="particle absolute w-1 h-1 bg-gradient-to-r from-violet-400 to-cyan-400 rounded-full opacity-30 animate-float-grid-1" style="left: 5%; top: 15%;"></div>
      <div class="particle absolute w-0.5 h-0.5 bg-gradient-to-r from-cyan-400 to-purple-400 rounded-full opacity-40 animate-float-grid-2" style="left: 90%; top: 20%;"></div>
      <div class="particle absolute w-1 h-1 bg-gradient-to-r from-purple-400 to-violet-400 rounded-full opacity-25 animate-float-grid-3" style="left: 20%; top: 80%;"></div>
      <div class="particle absolute w-0.5 h-0.5 bg-gradient-to-r from-violet-500 to-cyan-500 rounded-full opacity-50 animate-float-grid-4" style="left: 85%; top: 85%;"></div>
    </div>

    <div class="relative z-10">
      <!-- Count display with sort controls -->
      <div class="flex items-center justify-between mb-8 p-6 bg-gradient-to-br from-slate-900/60 via-slate-800/60 to-slate-900/60 backdrop-blur-xl border border-white/10 rounded-2xl shadow-2xl shadow-violet-500/10">
        <div class="flex items-center space-x-6">
          <span class="text-sm font-medium bg-gradient-to-r from-slate-200 to-slate-300 bg-clip-text text-transparent animate-shimmer-count">
            {{ images.length }} image{{ images.length !== 1 ? 's' : '' }}
          </span>
          
          <select 
            v-model="sortBy" 
            @change="handleSortChange"
            class="text-sm bg-slate-800/50 backdrop-blur-sm border border-white/10 rounded-xl px-4 py-2.5 text-slate-300 focus:ring-2 focus:ring-violet-500/30 focus:border-violet-500/50 transition-all duration-300 hover:bg-slate-700/50"
          >
            <option value="created_at">Recently Added</option>
            <option value="title">Title</option>
            <option value="views">Most Viewed</option>
            <option value="size">File Size</option>
          </select>

          <select 
            v-model="sortDirection" 
            @change="handleSortChange"
            class="text-sm bg-slate-800/50 backdrop-blur-sm border border-white/10 rounded-xl px-4 py-2.5 text-slate-300 focus:ring-2 focus:ring-violet-500/30 focus:border-violet-500/50 transition-all duration-300 hover:bg-slate-700/50"
          >
            <option value="desc">Descending</option>
            <option value="asc">Ascending</option>
          </select>
        </div>

        <!-- Select All checkbox (if bulk actions enabled) -->
        <div v-if="showBulkActions && images.length > 0" class="flex items-center space-x-3">
          <input
            id="select-all"
            v-model="selectAll"
            @change="toggleSelectAll"
            type="checkbox"
            class="h-4 w-4 text-violet-500 focus:ring-violet-500/30 focus:ring-2 border-white/20 rounded bg-slate-800/50 backdrop-blur-sm transition-all duration-300"
          />
          <label for="select-all" class="text-sm font-medium text-slate-300 cursor-pointer hover:text-white transition-colors duration-300">Select All</label>
        </div>
      </div>

      <!-- Bulk Actions Bar -->
      <div v-if="selectedImages.length > 0" class="mb-6 transform animate-fade-in-up">
        <BulkActionsBar
          :selected-count="selectedImages.length"
          :permissions="bulkPermissions"
          @delete="handleBulkDelete"
          @move="handleBulkMove"
          @edit="handleBulkEdit"
          @download="handleBulkDownload"
          @clear-selection="clearSelection"
        />
      </div>

      <!-- Images Grid -->
      <div :class="['grid gap-6 transition-all duration-500', gridClasses]">
        <div
          v-for="(image, index) in images"
          :key="image.id"
          class="animate-fade-in-up"
          :style="{ animationDelay: `${index * 50}ms` }"
        >
          <ImageCard
            :image="image"
            :size="cardSize"
            :show-checkbox="showBulkActions"
            :show-info="showInfo"
            :selected="selectedImages.includes(image.id)"
            :can-edit="image.can?.edit || false"
            :can-delete="image.can?.delete || false"
            :can-download="image.can?.download || false"
            @select="toggleSelection"
            @open="openImage"
          />
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="images.length === 0" class="text-center py-16 animate-fade-in-up">
        <div class="relative inline-flex items-center justify-center w-24 h-24 mx-auto mb-6">
          <!-- Enhanced Empty State Icon -->
          <div class="absolute inset-0 rounded-full bg-gradient-to-br from-slate-700/50 to-slate-800/50 backdrop-blur-sm border border-white/10"></div>
          <div class="absolute inset-0 rounded-full bg-gradient-to-r from-violet-500/0 to-cyan-500/0 animate-pulse-glow"></div>
          <PhotoIcon class="relative h-12 w-12 text-slate-400" />
        </div>
        
        <h3 class="text-lg font-semibold bg-gradient-to-r from-slate-200 to-slate-300 bg-clip-text text-transparent mb-2 animate-shimmer-empty">
          No images
        </h3>
        <p class="text-sm text-slate-400 mb-8 leading-relaxed">{{ emptyMessage }}</p>
        
        <div v-if="showUploadLink" class="inline-block">
          <Link
            :href="route('upload')"
            class="group inline-flex items-center px-6 py-3 bg-gradient-to-r from-violet-600 to-cyan-600 border border-transparent rounded-xl shadow-lg shadow-violet-500/20 text-sm font-medium text-white transition-all duration-300 hover:scale-105 hover:from-violet-500 hover:to-cyan-500 hover:shadow-violet-500/30"
          >
            <div class="relative mr-2">
              <div class="absolute inset-0 rounded-lg bg-white/20 blur-sm opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
              <PlusIcon class="relative h-5 w-5" />
            </div>
            Upload Images
          </Link>
        </div>
      </div>

      <!-- Lightbox -->
      <Teleport to="body">
        <Lightbox
          v-if="lightboxOpen"
          :images="images"
          :initial-index="currentImageIndex"
          @close="closeLightbox"
          @next="nextImage"
          @prev="prevImage"
        />
      </Teleport>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import route from 'ziggy-js'
import {
  PhotoIcon,
  PlusIcon,
} from '@heroicons/vue/24/outline'

import ImageCard from './ImageCard.vue'
import BulkActionsBar from './BulkActionsBar.vue'
import Lightbox from './Lightbox.vue'

const props = defineProps({
  images: { type: Array, default: () => [] },
  columns: { type: [Number, String], default: 'auto' },
  layout: { type: String, default: 'grid' },
  cardSize: { type: String, default: 'medium' },
  showBulkActions: { type: Boolean, default: false },
  showInfo: { type: Boolean, default: true },
  showUploadLink: { type: Boolean, default: false },
  emptyMessage: { type: String, default: 'No images found.' },
  bulkPermissions: { type: Object, default: () => ({}) },
})

const emit = defineEmits(['bulk-select', 'open-image', 'sort-change'])

// State
const selectedImages = ref([])
const lightboxOpen = ref(false)
const currentImageIndex = ref(0)
const selectAll = ref(false)
const sortBy = ref('created_at')
const sortDirection = ref('desc')

// Computed
const gridClasses = computed(() => {
  if (props.columns === 'auto') {
    switch (props.cardSize) {
      case 'small': return 'grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8'
      case 'medium': return 'grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5'
      case 'large': return 'grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4'
      default: return 'grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4'
    }
  }
  return `grid-cols-${props.columns}`
})

// Methods
const toggleSelection = (imageId) => {
  const index = selectedImages.value.indexOf(imageId)
  if (index > -1) {
    selectedImages.value.splice(index, 1)
  } else {
    selectedImages.value.push(imageId)
  }
  emit('bulk-select', selectedImages.value)
}

const toggleSelectAll = () => {
  if (selectAll.value) {
    selectedImages.value = props.images.map(img => img.id)
  } else {
    selectedImages.value = []
  }
  emit('bulk-select', selectedImages.value)
}

const clearSelection = () => {
  selectedImages.value = []
  selectAll.value = false
  emit('bulk-select', [])
}

const handleSortChange = () => {
  emit('sort-change', {
    sort: sortBy.value,
    direction: sortDirection.value
  })
}

const openImage = (imageId) => {
  const index = props.images.findIndex(img => img.id === imageId)
  if (index !== -1) {
    currentImageIndex.value = index
    lightboxOpen.value = true
  }
  emit('open-image', imageId)
}

const closeLightbox = () => {
  lightboxOpen.value = false
}

const nextImage = () => {
  if (currentImageIndex.value < props.images.length - 1) {
    currentImageIndex.value++
  }
}

const prevImage = () => {
  if (currentImageIndex.value > 0) {
    currentImageIndex.value--
  }
}

// Bulk actions
const handleBulkDelete = () => {
  if (confirm(`Are you sure you want to delete ${selectedImages.value.length} images?`)) {
    router.post(route('images.bulk-action'), {
      action: 'delete',
      image_ids: selectedImages.value,
    }, {
      onSuccess: () => clearSelection()
    })
  }
}

const handleBulkMove = (albumId) => {
  router.post(route('images.bulk-action'), {
    action: 'move_to_album',
    image_ids: selectedImages.value,
    album_id: albumId,
  }, {
    onSuccess: () => clearSelection()
  })
}

const handleBulkEdit = (data) => {
  router.post(route('images.bulk-action'), {
    action: 'privacy',
    image_ids: selectedImages.value,
    privacy_level: data.privacy,
  }, {
    onSuccess: () => clearSelection()
  })
}

const handleBulkDownload = () => {
  console.log('Bulk download:', selectedImages.value)
}
</script>

<style scoped>
@keyframes float-grid {
  0%, 100% { transform: translateY(0px) rotate(0deg); }
  25% { transform: translateY(-2px) rotate(0.2deg); }
  50% { transform: translateY(-4px) rotate(0deg); }
  75% { transform: translateY(-2px) rotate(-0.2deg); }
}

@keyframes float-grid-1 {
  0%, 100% { transform: translate(0px, 0px); opacity: 0.3; }
  25% { transform: translate(4px, -4px); opacity: 0.6; }
  50% { transform: translate(8px, 2px); opacity: 0.1; }
  75% { transform: translate(2px, 6px); opacity: 0.4; }
}

@keyframes float-grid-2 {
  0%, 100% { transform: translate(0px, 0px); opacity: 0.4; }
  33% { transform: translate(-6px, 4px); opacity: 0.7; }
  66% { transform: translate(4px, -2px); opacity: 0.2; }
}

@keyframes float-grid-3 {
  0%, 100% { transform: translate(0px, 0px); opacity: 0.25; }
  40% { transform: translate(8px, -8px); opacity: 0.5; }
  80% { transform: translate(-4px, 4px); opacity: 0.1; }
}

@keyframes float-grid-4 {
  0%, 100% { transform: translate(0px, 0px); opacity: 0.5; }
  30% { transform: translate(-4px, -3px); opacity: 0.8; }
  70% { transform: translate(3px, 4px); opacity: 0.2; }
}

@keyframes shimmer-count {
  0% { background-position: -200% 0; }
  100% { background-position: 200% 0; }
}

@keyframes shimmer-empty {
  0% { background-position: -200% 0; }
  100% { background-position: 200% 0; }
}

@keyframes fade-in-up {
  0% { 
    opacity: 0;
    transform: translateY(20px);
  }
  100% { 
    opacity: 1;
    transform: translateY(0px);
  }
}

@keyframes pulse-glow {
  0%, 100% { 
    background-image: linear-gradient(45deg, transparent, transparent);
    opacity: 0;
  }
  50% { 
    background-image: linear-gradient(45deg, rgba(139, 92, 246, 0.1), rgba(6, 182, 212, 0.1));
    opacity: 1;
  }
}

@keyframes gradient-shift {
  0%, 100% { stop-color: #8b5cf6; }
  25% { stop-color: #06b6d4; }
  50% { stop-color: #a855f7; }
  75% { stop-color: #8b5cf6; }
}

.animate-float-grid {
  animation: float-grid 12s ease-in-out infinite;
}

.animate-float-grid-1 {
  animation: float-grid-1 8s ease-in-out infinite;
}

.animate-float-grid-2 {
  animation: float-grid-2 10s ease-in-out infinite 1s;
}

.animate-float-grid-3 {
  animation: float-grid-3 9s ease-in-out infinite 2s;
}

.animate-float-grid-4 {
  animation: float-grid-4 7s ease-in-out infinite 3s;
}

.animate-shimmer-count {
  background-image: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
  background-size: 200% 100%;
  animation: shimmer-count 4s ease-in-out infinite;
}

.animate-shimmer-empty {
  background-image: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
  background-size: 200% 100%;
  animation: shimmer-empty 3s ease-in-out infinite;
}

.animate-fade-in-up {
  animation: fade-in-up 0.6s ease-out forwards;
}

.animate-pulse-glow {
  animation: pulse-glow 3s ease-in-out infinite;
}

.animate-gradient-shift {
  animation: gradient-shift 10s ease-in-out infinite;
}
</style>