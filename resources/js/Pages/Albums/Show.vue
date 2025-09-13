
<template>
  <AppLayout>
    <Head :title="album.title || 'Album'" />

    <!-- Animated Background -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
      <!-- Mesh Background -->
      <svg class="absolute inset-0 w-full h-full opacity-20" viewBox="0 0 100 100" preserveAspectRatio="none">
        <defs>
          <linearGradient id="meshGradient" x1="0%" y1="0%" x2="100%" y2="100%" gradientTransform="rotate(45)">
            <stop offset="0%" class="animate-pulse" style="stop-color:#8B5CF6;stop-opacity:0.3">
              <animate attributeName="stop-color" values="#8B5CF6;#06B6D4;#A855F7;#8B5CF6" dur="8s" repeatCount="indefinite"/>
            </stop>
            <stop offset="100%" style="stop-color:#06B6D4;stop-opacity:0.2">
              <animate attributeName="stop-color" values="#06B6D4;#A855F7;#8B5CF6;#06B6D4" dur="8s" repeatCount="indefinite"/>
            </stop>
          </linearGradient>
        </defs>
        <rect width="100" height="100" fill="url(#meshGradient)" opacity="0.1">
          <animateTransform attributeName="transform" type="translate" values="0,0;10,5;0,0" dur="15s" repeatCount="indefinite"/>
        </rect>
      </svg>

      <!-- Floating Particles -->
      <div class="absolute inset-0">
        <div v-for="i in 20" :key="i" 
             :class="`absolute w-1 h-1 bg-gradient-to-r from-violet-500 to-cyan-500 rounded-full opacity-30 animate-pulse`"
             :style="{
               left: Math.random() * 100 + '%',
               top: Math.random() * 100 + '%',
               animationDelay: Math.random() * 5 + 's',
               animationDuration: (3 + Math.random() * 4) + 's'
             }">
          <div class="absolute inset-0 animate-ping bg-gradient-to-r from-purple-500 to-cyan-500 rounded-full"></div>
        </div>
      </div>
    </div>

    <template #header>
      <div class="relative z-10 backdrop-blur-xl bg-slate-900/80 border-b border-white/10">
        <div class="flex items-center justify-between px-6 py-4">
          <div class="animate-fade-in-up">
            <nav class="flex mb-3" aria-label="Breadcrumb">
              <ol class="flex items-center space-x-4">
                <li>
                  <Link :href="route('albums.index')" 
                        class="text-slate-300 hover:text-white transition-colors duration-300 hover:bg-white/10 px-3 py-1 rounded-lg">
                    Albums
                  </Link>
                </li>
                <li class="flex items-center">
                  <ChevronRightIcon class="flex-shrink-0 h-4 w-4 text-slate-500 mx-2" />
                  <span class="text-sm font-medium bg-gradient-to-r from-violet-400 to-cyan-400 bg-clip-text text-transparent">
                    {{ album.title }}
                  </span>
                </li>
              </ol>
            </nav>
            <h2 class="text-2xl font-bold bg-gradient-to-r from-violet-400 via-purple-400 to-cyan-400 bg-clip-text text-transparent">
              <span class="inline-block animate-shimmer bg-gradient-to-r from-violet-400 via-white via-purple-400 to-cyan-400 bg-[length:400%_100%] bg-clip-text text-transparent">
                {{ album.title }}
              </span>
            </h2>
          </div>
          
          <!-- Album Management Actions -->
          <div v-if="can.update" class="flex items-center space-x-3 animate-fade-in-up" style="animation-delay: 0.2s;">
            <Link
              :href="route('albums.add-images-form', album.id)"
              class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-cyan-600/80 to-purple-600/80 
                     hover:from-cyan-500 hover:to-purple-500 text-sm font-medium text-white rounded-xl 
                     transition-all duration-300 transform hover:scale-105 hover:shadow-lg hover:shadow-cyan-500/25 
                     backdrop-blur-sm border border-cyan-500/30 group"
            >
              <PlusIcon class="h-4 w-4 mr-2 group-hover:rotate-12 transition-transform duration-300" />
              Add Images
            </Link>
            <Link
              :href="route('albums.edit', album.id)"
              class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-violet-600/80 to-purple-600/80 
                     hover:from-violet-500 hover:to-purple-500 text-sm font-medium text-white rounded-xl 
                     transition-all duration-300 transform hover:scale-105 hover:shadow-lg hover:shadow-violet-500/25 
                     backdrop-blur-sm border border-violet-500/30 group"
            >
              <PencilIcon class="h-4 w-4 mr-2 group-hover:rotate-12 transition-transform duration-300" />
              Edit
            </Link>
          </div>
        </div>
      </div>
    </template>

    <div class="relative z-10 py-8 px-4 sm:px-6 lg:px-8">
      <div class="max-w-7xl mx-auto space-y-8">
        
        <!-- Album Info Hero -->
        <div class="backdrop-blur-xl bg-white/5 border border-white/10 shadow-2xl rounded-2xl overflow-hidden hover:shadow-violet-500/10 transition-all duration-500 hover:border-violet-500/30 animate-fade-in-up">
          <div class="bg-gradient-to-r from-violet-500/10 via-purple-500/10 to-cyan-500/10 p-8 border-b border-white/10">
            <div class="flex items-start justify-between">
              <div class="flex-1 space-y-4">
                <h1 class="text-4xl font-bold bg-gradient-to-r from-white via-violet-200 to-cyan-200 bg-clip-text text-transparent">
                  {{ album.title }}
                </h1>
                <p v-if="album.description" 
                   class="text-lg text-slate-300 leading-relaxed max-w-3xl">
                  {{ album.description }}
                </p>
                
                <!-- Album Stats -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-6 pt-4">
                  <div class="text-center p-4 rounded-xl bg-white/5 backdrop-blur-sm border border-white/10 hover:border-violet-500/30 transition-all duration-300 group">
                    <div class="text-2xl font-bold bg-gradient-to-r from-violet-400 to-cyan-400 bg-clip-text text-transparent">
                      {{ album.images_count || 0 }}
                    </div>
                    <div class="text-sm text-slate-400 group-hover:text-slate-300 transition-colors">Images</div>
                  </div>
                  <div class="text-center p-4 rounded-xl bg-white/5 backdrop-blur-sm border border-white/10 hover:border-cyan-500/30 transition-all duration-300 group">
                    <div class="text-lg font-semibold capitalize bg-gradient-to-r from-cyan-400 to-purple-400 bg-clip-text text-transparent">
                      {{ album.privacy }}
                    </div>
                    <div class="text-sm text-slate-400 group-hover:text-slate-300 transition-colors">Privacy</div>
                  </div>
                  <div class="text-center p-4 rounded-xl bg-white/5 backdrop-blur-sm border border-white/10 hover:border-purple-500/30 transition-all duration-300 group">
                    <div class="text-sm font-medium text-purple-300">
                      {{ formatDate(album.created_at) }}
                    </div>
                    <div class="text-sm text-slate-400 group-hover:text-slate-300 transition-colors">Created</div>
                  </div>
                  <div class="text-center p-4 rounded-xl bg-white/5 backdrop-blur-sm border border-white/10 hover:border-violet-500/30 transition-all duration-300 group">
                    <div class="text-sm font-medium text-violet-300">
                      {{ album.owner?.name }}
                    </div>
                    <div class="text-sm text-slate-400 group-hover:text-slate-300 transition-colors">Owner</div>
                  </div>
                </div>
              </div>
              
              <!-- Privacy Badge -->
              <div class="ml-6">
                <div class="relative group">
                  <span
                    class="px-4 py-2 text-sm font-medium rounded-xl border-2 transition-all duration-300 backdrop-blur-sm"
                    :class="{
                      'bg-emerald-500/20 text-emerald-300 border-emerald-500/40 hover:border-emerald-400 hover:shadow-lg hover:shadow-emerald-500/20': album.privacy === 'public',
                      'bg-amber-500/20 text-amber-300 border-amber-500/40 hover:border-amber-400 hover:shadow-lg hover:shadow-amber-500/20': album.privacy === 'unlisted',
                      'bg-red-500/20 text-red-300 border-red-500/40 hover:border-red-400 hover:shadow-lg hover:shadow-red-500/20': album.privacy === 'private'
                    }"
                  >
                    <span class="flex items-center">
                      <div class="w-2 h-2 rounded-full mr-2 animate-pulse"
                           :class="{
                             'bg-emerald-400': album.privacy === 'public',
                             'bg-amber-400': album.privacy === 'unlisted',
                             'bg-red-400': album.privacy === 'private'
                           }">
                      </div>
                      {{ album.privacy }}
                    </span>
                  </span>
                  <div class="absolute inset-0 rounded-xl opacity-0 group-hover:opacity-100 transition-all duration-300"
                       :class="{
                         'bg-emerald-500/10': album.privacy === 'public',
                         'bg-amber-500/10': album.privacy === 'unlisted',
                         'bg-red-500/10': album.privacy === 'private'
                       }">
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Image Management Controls -->
        <div v-if="can.manageImages && selectedImages.length > 0" 
             class="backdrop-blur-xl bg-blue-500/10 border border-blue-500/30 rounded-2xl p-6 animate-fade-in-up shadow-lg shadow-blue-500/10" 
             style="animation-delay: 0.2s;">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
              <div class="w-3 h-3 bg-gradient-to-r from-blue-400 to-cyan-400 rounded-full animate-pulse"></div>
              <span class="text-sm font-medium text-blue-200">
                {{ selectedImages.length }} image{{ selectedImages.length === 1 ? '' : 's' }} selected
              </span>
            </div>
            <div class="flex items-center space-x-3">
              <button
                @click="removeSelectedImages"
                :disabled="removeLoading"
                class="px-6 py-2 bg-gradient-to-r from-red-600/80 to-red-700/80 hover:from-red-500 hover:to-red-600 
                       disabled:from-slate-600 disabled:to-slate-700 text-white text-sm rounded-xl transition-all duration-300 
                       transform hover:scale-105 hover:shadow-lg hover:shadow-red-500/25 disabled:opacity-50 disabled:cursor-not-allowed 
                       disabled:transform-none backdrop-blur-sm border border-red-500/30 group"
              >
                <span class="flex items-center">
                  <div v-if="removeLoading" class="w-4 h-4 mr-2 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                  <svg v-else class="w-4 h-4 mr-2 group-hover:rotate-12 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                  </svg>
                  {{ removeLoading ? 'Removing...' : 'Remove from Album' }}
                </span>
              </button>
              
              <button
                @click="clearImageSelection"
                class="px-6 py-2 bg-slate-700/50 hover:bg-slate-600/50 text-slate-300 hover:text-white text-sm rounded-xl 
                       transition-all duration-300 backdrop-blur-sm border border-slate-600/50 hover:border-slate-500 
                       transform hover:scale-105 group"
              >
                <span class="flex items-center">
                  <svg class="w-4 h-4 mr-2 group-hover:rotate-12 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                  </svg>
                  Clear
                </span>
              </button>
            </div>
          </div>
        </div>

        <!-- Images Grid Section -->
        <div class="backdrop-blur-xl bg-white/5 border border-white/10 shadow-2xl rounded-2xl overflow-hidden hover:shadow-cyan-500/10 transition-all duration-500 hover:border-cyan-500/30 animate-fade-in-up" style="animation-delay: 0.3s;">
          <div class="bg-gradient-to-r from-cyan-500/10 to-purple-500/10 p-6 border-b border-white/10">
            <div class="flex items-center justify-between">
              <h3 class="text-xl font-semibold text-white flex items-center">
                <div class="w-2 h-2 bg-gradient-to-r from-cyan-500 to-purple-500 rounded-full mr-3 animate-pulse"></div>
                Images
                <span class="ml-3 text-sm text-slate-400 font-normal">
                  {{ images.data?.length || 0 }} {{ images.data?.length === 1 ? 'image' : 'images' }}
                </span>
              </h3>
              
              <!-- Select All Controls -->
              <div v-if="can.manageImages && images.data?.length > 0" class="flex items-center space-x-3">
                <label class="flex items-center space-x-2 cursor-pointer group">
                  <div class="relative">
                    <input
                      id="select-all-images"
                      v-model="selectAllImages"
                      @change="toggleSelectAllImages"
                      type="checkbox"
                      class="h-5 w-5 text-cyan-500 focus:ring-cyan-500 focus:ring-2 focus:ring-offset-0 border-slate-600 
                             rounded bg-slate-800/50 transition-all duration-300"
                    />
                    <div class="absolute inset-0 rounded bg-gradient-to-r from-cyan-500/0 to-purple-500/0 group-hover:from-cyan-500/20 group-hover:to-purple-500/20 transition-all duration-300 pointer-events-none"></div>
                  </div>
                  <span class="text-sm text-slate-300 group-hover:text-white transition-colors duration-300">Select All</span>
                </label>
              </div>
            </div>
          </div>
          
          <div class="p-8">
            <!-- Images Grid -->
            <div v-if="images.data && images.data.length > 0" 
                 class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8 gap-4">
              <div
                v-for="(image, index) in images.data"
                :key="image.id"
                class="relative group bg-gradient-to-br from-slate-800/50 to-slate-900/50 rounded-xl overflow-hidden 
                       aspect-square border border-slate-600/30 hover:border-cyan-500/50 transition-all duration-500 
                       transform hover:scale-105 hover:shadow-2xl hover:shadow-cyan-500/20 backdrop-blur-sm animate-fade-in-up"
                :style="{ animationDelay: (0.05 * index) + 's' }"
              >
                <!-- Selection Checkbox -->
                <div v-if="can.manageImages" class="absolute top-3 left-3 z-20">
                  <label class="relative block cursor-pointer group/checkbox">
                    <input
                      v-model="selectedImages"
                      :value="image.id"
                      type="checkbox"
                      class="h-5 w-5 text-violet-500 focus:ring-violet-500 focus:ring-2 focus:ring-offset-0 border-slate-600 
                             rounded bg-slate-800/80 backdrop-blur-sm transition-all duration-300 group-hover/checkbox:scale-110"
                    />
                    <div class="absolute inset-0 rounded bg-gradient-to-r from-violet-500/0 to-cyan-500/0 group-hover/checkbox:from-violet-500/30 group-hover/checkbox:to-cyan-500/30 transition-all duration-300 pointer-events-none"></div>
                  </label>
                </div>

                <!-- Image -->
                <div class="relative w-full h-full overflow-hidden">
                  <img
                    :src="getImageUrl(image)"
                    :alt="image.alt_text || image.title"
                    class="w-full h-full object-cover cursor-pointer transition-all duration-700 group-hover:scale-110"
                    @click="openImage(image)"
                    loading="lazy"
                  />
                  <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-all duration-300"></div>
                </div>

                <!-- Title Overlay -->
                <div class="absolute bottom-0 left-0 right-0 p-3 transform translate-y-full group-hover:translate-y-0 transition-all duration-300">
                  <div class="backdrop-blur-md bg-black/40 rounded-lg p-2 border border-white/10">
                    <p class="text-xs font-medium text-white truncate">
                      {{ image.title || 'Untitled' }}
                    </p>
                  </div>
                </div>

                <!-- Hover Glow Effect -->
                <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-cyan-500/0 via-purple-500/0 to-violet-500/0 group-hover:from-cyan-500/10 group-hover:via-purple-500/10 group-hover:to-violet-500/10 transition-all duration-500 pointer-events-none"></div>
              </div>
            </div>
            
            <!-- No Images State -->
            <div v-else class="text-center py-16 animate-fade-in-up" style="animation-delay: 0.4s;">
              <div class="relative inline-block mb-6">
                <PhotoIcon class="mx-auto h-20 w-20 text-slate-500 animate-bounce" style="animation-delay: 0.5s;" />
                <div class="absolute inset-0 animate-ping opacity-25">
                  <PhotoIcon class="h-20 w-20 text-slate-500" />
                </div>
              </div>
              
              <h3 class="text-xl font-semibold text-slate-300 mb-2">No Images Yet</h3>
              <p class="text-slate-400 mb-8 max-w-md mx-auto">
                This album is waiting for its first masterpiece. Start building your collection!
              </p>
              
              <div v-if="can.manageImages">
                <Link
                  :href="route('albums.add-images-form', album.id)"
                  class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-violet-600 to-cyan-600 
                         hover:from-violet-500 hover:to-cyan-500 text-white font-semibold rounded-xl 
                         transition-all duration-300 transform hover:scale-105 hover:shadow-2xl hover:shadow-violet-500/25 
                         border border-violet-500/30 group"
                >
                  <PlusIcon class="h-5 w-5 mr-3 group-hover:rotate-12 transition-transform duration-300" />
                  Add Your First Images
                  <div class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/20 to-white/0 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-700 rounded-xl"></div>
                </Link>
              </div>
            </div>

            <!-- Pagination -->
            <div v-if="images.data && images.data.length > 0 && images.meta" 
                 class="mt-8 animate-fade-in-up" 
                 style="animation-delay: 0.5s;">
              <Pagination
                :links="images.links || []"
                :meta="images.meta || {}"
                class="flex justify-center"
              />
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import route from 'ziggy-js'
import { ChevronRightIcon, PhotoIcon, PlusIcon, PencilIcon } from '@heroicons/vue/24/outline'

import AppLayout from '@/Layouts/AppLayout.vue'
import Pagination from '@/Components/Pagination.vue'

const props = defineProps({
  album: { type: Object, default: () => ({}) },
  images: { type: Object, default: () => ({ data: [], links: [], meta: {} }) },
  filters: { type: Object, default: () => ({}) },
  can: { type: Object, default: () => ({}) },
})

// Image selection for management
const selectedImages = ref([])
const selectAllImages = ref(false)
const removeLoading = ref(false)

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  })
}

const getImageUrl = (image) => {
  if (image.storage_path) {
    return `http://localhost:9000/gallery-images/${image.storage_path}`
  }
  return '/images/placeholder.jpg'
}

const openImage = (image) => {
  router.visit(route('images.show', image.slug))
}

const toggleSelectAllImages = () => {
  if (selectAllImages.value) {
    selectedImages.value = props.images.data.map(img => img.id)
  } else {
    selectedImages.value = []
  }
}

const clearImageSelection = () => {
  selectedImages.value = []
  selectAllImages.value = false
}

const removeSelectedImages = () => {
  if (selectedImages.value.length === 0) return
  
  if (!confirm(`Remove ${selectedImages.value.length} image(s) from this album? Images will not be deleted.`)) {
    return
  }

  removeLoading.value = true

  router.delete(route('albums.remove-images', props.album.id), {
    data: { image_ids: selectedImages.value },
    onSuccess: () => {
      clearImageSelection()
      removeLoading.value = false
    },
    onError: () => {
      removeLoading.value = false
    }
  })
}
</script>

<style scoped>
@keyframes shimmer {
  0% { background-position: -400% 0; }
  100% { background-position: 400% 0; }
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

@keyframes shake {
  0%, 100% { transform: translateX(0); }
  25% { transform: translateX(-5px); }
  75% { transform: translateX(5px); }
}

.animate-shimmer {
  animation: shimmer 3s ease-in-out infinite;
}

.animate-fade-in-up {
  animation: fade-in-up 0.8s ease-out forwards;
  opacity: 0;
}

.animate-shake {
  animation: shake 0.5s ease-in-out;
}
</style>