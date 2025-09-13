<template>
  <AppLayout>
    <Head :title="`Add Images to ${album.title}`" />

    <!-- Animated Background -->
    <div class="fixed inset-0 -z-10 overflow-hidden">
      <!-- Animated Mesh Pattern -->
      <div class="absolute inset-0 opacity-30">
        <svg class="absolute inset-0 h-full w-full animate-pulse" viewBox="0 0 400 400">
          <defs>
            <linearGradient id="meshGradient" x1="0%" y1="0%" x2="100%" y2="100%" gradientTransform="rotate(45)">
              <stop offset="0%" style="stop-color:#8b5cf6;stop-opacity:0.3">
                <animateTransform
                  attributeName="gradientTransform"
                  type="rotate"
                  values="0 50 50;360 50 50;0 50 50"
                  dur="8s"
                  repeatCount="indefinite"
                />
              </stop>
              <stop offset="50%" style="stop-color:#06b6d4;stop-opacity:0.3" />
              <stop offset="100%" style="stop-color:#a855f7;stop-opacity:0.3" />
            </linearGradient>
          </defs>
          <rect width="100%" height="100%" fill="url(#meshGradient)" />
          <g stroke="url(#meshGradient)" stroke-width="0.5" fill="none" opacity="0.4">
            <rect x="0" y="0" width="100" height="100" />
            <rect x="100" y="100" width="100" height="100" />
            <rect x="200" y="0" width="100" height="100" />
            <rect x="300" y="100" width="100" height="100" />
            <rect x="0" y="200" width="100" height="100" />
            <rect x="100" y="300" width="100" height="100" />
            <rect x="200" y="200" width="100" height="100" />
            <rect x="300" y="300" width="100" height="100" />
          </g>
        </svg>
      </div>

      <!-- Floating Particles -->
      <div class="absolute inset-0">
        <div 
          v-for="i in 20" 
          :key="i"
          class="absolute w-1 h-1 rounded-full bg-gradient-to-r from-violet-400 to-cyan-400 opacity-60"
          :style="{
            left: Math.random() * 100 + '%',
            top: Math.random() * 100 + '%',
            animationDelay: Math.random() * 8 + 's',
            animationDuration: (8 + Math.random() * 4) + 's'
          }"
          style="animation: float 12s ease-in-out infinite;"
        ></div>
      </div>

      <!-- Base gradient background -->
      <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-purple-900/50 to-slate-900"></div>
    </div>

    <template #header>
      <div class="flex items-center justify-between animate-fade-in-up" style="animation-delay: 0.1s;">
        <div>
          <h2 class="font-bold text-2xl text-transparent bg-clip-text bg-gradient-to-r from-violet-400 via-purple-400 to-cyan-400 leading-tight animate-shimmer">
            Add Images to Album
          </h2>
          <p class="text-sm text-slate-400 mt-1">
            {{ album.title }}
          </p>
        </div>
        <div class="flex items-center space-x-4">
          <Link
            :href="route('albums.show', album.slug)"
            class="group inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-violet-600 to-purple-600 hover:from-violet-500 hover:to-purple-500 border border-violet-500/20 rounded-xl font-semibold text-sm text-white shadow-lg hover:shadow-violet-500/25 hover:shadow-xl transition-all duration-300 hover:scale-105"
          >
            Back to Album
          </Link>
        </div>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl shadow-2xl animate-fade-in-up" style="animation-delay: 0.2s;">
          <div class="p-8">
            <!-- Selection Controls -->
            <div class="flex items-center justify-between mb-8 p-6 bg-white/5 backdrop-blur-xl rounded-xl border border-white/10">
              <div class="flex items-center space-x-6">
                <span class="text-sm text-slate-400">
                  <span class="text-cyan-400 font-bold">{{ availableImages.length }}</span> available images
                </span>
                <div class="flex items-center space-x-3">
                  <div class="relative">
                    <input
                      id="select-all"
                      v-model="selectAll"
                      @change="toggleSelectAll"
                      type="checkbox"
                      class="h-5 w-5 text-violet-600 focus:ring-violet-500 border-white/30 rounded bg-white/10 backdrop-blur-xl transition-all duration-300"
                    />
                  </div>
                  <label for="select-all" class="text-sm text-slate-300 font-medium cursor-pointer">Select All</label>
                </div>
              </div>
              <div class="flex items-center space-x-4">
                <span v-if="selectedImages.length > 0" class="text-sm font-bold text-transparent bg-clip-text bg-gradient-to-r from-violet-400 to-cyan-400">
                  {{ selectedImages.length }} selected
                </span>
                <button
                  @click="addSelectedImages"
                  :disabled="selectedImages.length === 0 || form.processing"
                  class="group relative px-6 py-3 bg-gradient-to-r from-violet-600 to-purple-600 hover:from-violet-500 hover:to-purple-500 text-white text-sm font-bold rounded-xl shadow-lg hover:shadow-violet-500/25 hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-300 hover:scale-105 disabled:hover:scale-100"
                >
                  <span class="relative z-10">
                    {{ form.processing ? 'Adding...' : `Add ${selectedImages.length} Images` }}
                  </span>
                  <div class="absolute inset-0 bg-gradient-to-r from-violet-400 to-purple-400 rounded-xl blur opacity-0 group-hover:opacity-30 transition-opacity duration-300"></div>
                </button>
              </div>
            </div>

            <!-- Images Grid -->
            <div v-if="availableImages.length > 0"
                 class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-6">
              <div
                v-for="(image, index) in availableImages"
                :key="image.id"
                class="group relative bg-slate-800/30 backdrop-blur-xl border border-white/10 rounded-2xl overflow-hidden aspect-square hover:border-violet-400/50 transition-all duration-500 hover:scale-105 hover:shadow-2xl hover:shadow-violet-500/20 animate-fade-in-up"
                :style="{ animationDelay: (0.1 * (index % 12)) + 's' }"
              >
                <!-- FIXED: Selection Checkbox with proper z-index and pointer events -->
                <div class="absolute top-3 left-3 z-30">
                  <label class="relative block cursor-pointer group/checkbox">
                    <input
                      v-model="selectedImages"
                      :value="image.id"
                      type="checkbox"
                      class="relative z-10 h-5 w-5 text-violet-600 focus:ring-violet-500 border-white/40 rounded bg-white/20 backdrop-blur-xl transition-all duration-300 hover:scale-110 focus:scale-110"
                    />
                    <!-- Enhanced clickable area -->
                    <div class="absolute inset-0 -m-2 bg-transparent rounded-lg group-hover/checkbox:bg-violet-400/10 transition-colors duration-300"></div>
                    <!-- Glow effect -->
                    <div class="absolute inset-0 bg-gradient-to-r from-violet-400/20 to-cyan-400/20 rounded blur opacity-0 group-hover/checkbox:opacity-100 transition-opacity duration-300"></div>
                  </label>
                </div>

                <!-- FIXED: Current Album Badge with proper z-index -->
                <div v-if="image.album_id" class="absolute top-3 right-3 z-30">
                  <span class="px-3 py-1.5 text-xs font-bold bg-amber-500/20 border border-amber-500/30 text-amber-300 rounded-full backdrop-blur-xl">
                    In Album
                  </span>
                </div>

                <!-- FIXED: Image with click handler for selection -->
                <div 
                  class="relative w-full h-full overflow-hidden rounded-2xl cursor-pointer"
                  @click="toggleImageSelection(image.id)"
                >
                  <img
                    :src="getImageUrl(image)"
                    :alt="image.title || 'Untitled'"
                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 pointer-events-none"
                  />
                  <!-- FIXED: Gradient overlay with pointer-events-none -->
                  <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent pointer-events-none"></div>
                </div>

                <!-- FIXED: Title overlay with proper pointer events -->
                <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent backdrop-blur-sm text-white p-4 opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-y-2 group-hover:translate-y-0 pointer-events-none z-20">
                  <p class="text-sm font-bold truncate text-white">{{ image.title || 'Untitled' }}</p>
                  <p v-if="image.album_id" class="text-xs text-violet-300 truncate mt-1">
                    Current: {{ getAlbumName(image.album_id) }}
                  </p>
                </div>

                <!-- FIXED: Hover glow effect with pointer-events-none -->
                <div class="absolute inset-0 bg-gradient-to-r from-violet-600/20 to-cyan-600/20 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none z-10"></div>
              </div>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-20 animate-fade-in-up" style="animation-delay: 0.3s;">
              <div class="relative inline-block mb-6">
                <PhotoIcon class="mx-auto h-20 w-20 text-slate-600" />
                <div class="absolute inset-0 bg-gradient-to-r from-violet-400/30 to-cyan-400/30 blur-2xl rounded-full"></div>
              </div>
              <h3 class="text-xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-violet-400 to-cyan-400 mb-2">
                No Available Images
              </h3>
              <p class="text-slate-400 mb-8 max-w-md mx-auto leading-relaxed">
                All your images are already in albums or you haven't uploaded any images yet.
              </p>
              <div class="mt-8">
                <Link
                  :href="route('upload')"
                  class="group inline-flex items-center px-8 py-4 bg-gradient-to-r from-violet-600 to-cyan-600 hover:from-violet-500 hover:to-cyan-500 border border-violet-500/20 shadow-lg text-sm font-bold rounded-2xl text-white hover:shadow-violet-500/25 hover:shadow-2xl transition-all duration-300 hover:scale-105"
                >
                  <PlusIcon class="-ml-1 mr-3 h-6 w-6 group-hover:rotate-90 transition-transform duration-300" />
                  Upload Images
                  <div class="absolute inset-0 bg-gradient-to-r from-violet-400/20 to-cyan-400/20 rounded-2xl blur opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import route from 'ziggy-js'
import { PlusIcon, PhotoIcon } from '@heroicons/vue/24/outline'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  album: { type: Object, required: true },
  availableImages: { type: Array, default: () => [] },
  userAlbums: { type: Array, default: () => [] }
})

// Form and selection state
const selectedImages = ref([])
const selectAll = ref(false)

const form = useForm({
  image_ids: []
})

// FIXED: Method to toggle individual image selection
const toggleImageSelection = (imageId) => {
  const index = selectedImages.value.indexOf(imageId)
  if (index > -1) {
    selectedImages.value.splice(index, 1)
  } else {
    selectedImages.value.push(imageId)
  }
  
  // Update select all state
  selectAll.value = selectedImages.value.length === props.availableImages.length
}

// Methods
const toggleSelectAll = () => {
  if (selectAll.value) {
    selectedImages.value = props.availableImages.map(img => img.id)
  } else {
    selectedImages.value = []
  }
}

const addSelectedImages = () => {
  if (selectedImages.value.length === 0) return

  form.image_ids = selectedImages.value
  form.post(route('albums.add-images', props.album.slug || props.album.id), {
    onSuccess: () => {
      selectedImages.value = []
      selectAll.value = false
    },
    onError: (errors) => {
      console.error('Failed to add images:', errors)
    }
  })
}

const getImageUrl = (image) => {
  if (image.storage_path) {
    return `http://localhost:9000/gallery-images/${image.storage_path}`
  }
  return '/images/placeholder.jpg'
}

const getAlbumName = (albumId) => {
  const album = props.userAlbums.find(a => a.id === albumId)
  return album ? album.title : 'Unknown Album'
}
</script>

<style scoped>
@keyframes float {
  0%, 100% { transform: translateY(0px) rotate(0deg); }
  33% { transform: translateY(-20px) rotate(120deg); }
  66% { transform: translateY(-10px) rotate(240deg); }
}

@keyframes shimmer {
  0% { background-position: -200% 0; }
  100% { background-position: 200% 0; }
}

@keyframes fade-in-up {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-shimmer {
  background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
  background-size: 200% 100%;
  animation: shimmer 3s ease-in-out infinite;
  -webkit-background-clip: text;
  background-clip: text;
}

.animate-fade-in-up {
  animation: fade-in-up 0.6s ease-out forwards;
  opacity: 0;
}

/* Enhanced checkbox styling */
input[type="checkbox"]:checked {
  background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='white' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='m13.854 3.646-7.5 7.5a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6 10.293l7.146-7.147a.5.5 0 0 1 .708.708z'/%3e%3c/svg%3e");
  background-color: rgb(124 58 237);
  border-color: rgb(124 58 237);
}

input[type="checkbox"]:focus {
  outline: 2px solid rgb(139 92 246 / 0.5);
  outline-offset: 2px;
}

/* Ensure proper stacking */
.group:hover .z-30 {
  z-index: 31;
}
</style>
