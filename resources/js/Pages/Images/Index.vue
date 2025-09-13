<template>
  <AppLayout>
    <Head :title="pageTitle" />

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
            <h2 class="text-2xl font-bold bg-gradient-to-r from-violet-400 via-purple-400 to-cyan-400 bg-clip-text text-transparent">
              <span class="inline-block animate-shimmer bg-gradient-to-r from-violet-400 via-white via-purple-400 to-cyan-400 bg-[length:400%_100%] bg-clip-text text-transparent">
                {{ pageTitle }}
              </span>
            </h2>
            <p class="text-sm text-slate-300 mt-1">
              {{ pageDescription }}
            </p>
          </div>
          <div class="flex items-center space-x-4 animate-fade-in-up" style="animation-delay: 0.2s;">
            <Link
              v-if="canUpload"
              :href="route('upload')"
              class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-violet-600 to-cyan-600 
                     hover:from-violet-500 hover:to-cyan-500 text-white font-semibold rounded-xl 
                     transition-all duration-300 transform hover:scale-105 hover:shadow-2xl hover:shadow-violet-500/25 
                     border border-violet-500/30 group uppercase tracking-wider text-xs"
            >
              <PlusIcon class="h-4 w-4 mr-2 group-hover:rotate-12 transition-transform duration-300" />
              Upload Images
              <div class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/20 to-white/0 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-700 rounded-xl"></div>
            </Link>
          </div>
        </div>
      </div>
    </template>

    <div class="relative z-10 py-8 px-4 sm:px-6 lg:px-8">
      <div class="max-w-7xl mx-auto space-y-8">
        
        <!-- Filters -->
        <div class="backdrop-blur-xl bg-white/5 border border-white/10 shadow-2xl rounded-2xl overflow-hidden hover:shadow-violet-500/10 transition-all duration-500 hover:border-violet-500/30 animate-fade-in-up">
          <div class="bg-gradient-to-r from-violet-500/10 to-cyan-500/10 p-6 border-b border-white/10">
            <h3 class="text-lg font-semibold text-white flex items-center">
              <div class="w-2 h-2 bg-gradient-to-r from-violet-500 to-cyan-500 rounded-full mr-3 animate-pulse"></div>
              Search & Filters
            </h3>
          </div>
          <div class="p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-6">
              
              <!-- Search -->
              <div class="group animate-fade-in-up" style="animation-delay: 0.1s;">
                <label class="block text-sm font-medium text-slate-300 mb-2 transition-colors group-focus-within:text-violet-400">Search</label>
                <div class="relative">
                  <input
                    v-model="searchForm.search"
                    @keyup.enter="search"
                    type="text"
                    :placeholder="isMyImages ? 'Search your images...' : 'Search images...'"
                    class="w-full px-4 py-3 bg-slate-800/50 border border-slate-600/50 rounded-xl text-white placeholder-slate-400 
                           focus:border-violet-500 focus:ring-2 focus:ring-violet-500/20 focus:bg-slate-800/80 
                           transition-all duration-300 backdrop-blur-sm hover:border-slate-500"
                  />
                  <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-violet-500/0 to-cyan-500/0 group-focus-within:from-violet-500/10 group-focus-within:to-cyan-500/10 transition-all duration-300 pointer-events-none"></div>
                </div>
              </div>

              <!-- Album Filter -->
              <div class="group animate-fade-in-up" style="animation-delay: 0.2s;">
                <label class="block text-sm font-medium text-slate-300 mb-2 transition-colors group-focus-within:text-cyan-400">Album</label>
                <div class="relative">
                  <select
                    v-model="searchForm.album"
                    @change="search"
                    class="w-full px-4 py-3 bg-slate-800/50 border border-slate-600/50 rounded-xl text-white
                           focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20 focus:bg-slate-800/80 
                           transition-all duration-300 backdrop-blur-sm hover:border-slate-500 cursor-pointer"
                  >
                    <option value="" class="bg-slate-800">All Albums</option>
                    <option value="none" class="bg-slate-800">No Album</option>
                    <option v-for="album in albums" :key="album.id" :value="album.id" class="bg-slate-800">
                      {{ album.title }}
                    </option>
                  </select>
                  <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-cyan-500/0 to-purple-500/0 group-focus-within:from-cyan-500/10 group-focus-within:to-purple-500/10 transition-all duration-300 pointer-events-none"></div>
                </div>
              </div>

              <!-- Privacy Filter (My Images only) -->
              <div v-if="isMyImages" class="group animate-fade-in-up" style="animation-delay: 0.3s;">
                <label class="block text-sm font-medium text-slate-300 mb-2 transition-colors group-focus-within:text-purple-400">Privacy</label>
                <div class="relative">
                  <select
                    v-model="searchForm.privacy"
                    @change="search"
                    class="w-full px-4 py-3 bg-slate-800/50 border border-slate-600/50 rounded-xl text-white
                           focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 focus:bg-slate-800/80 
                           transition-all duration-300 backdrop-blur-sm hover:border-slate-500 cursor-pointer"
                  >
                    <option value="" class="bg-slate-800">All Privacy Levels</option>
                    <option value="public" class="bg-slate-800">🌍 Public</option>
                    <option value="unlisted" class="bg-slate-800">🔗 Unlisted</option>
                    <option value="private" class="bg-slate-800">🔒 Private</option>
                  </select>
                  <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-purple-500/0 to-violet-500/0 group-focus-within:from-purple-500/10 group-focus-within:to-violet-500/10 transition-all duration-300 pointer-events-none"></div>
                </div>
              </div>

              <!-- Published Filter (My Images only) -->
              <div v-if="isMyImages" class="group animate-fade-in-up" style="animation-delay: 0.4s;">
                <label class="block text-sm font-medium text-slate-300 mb-2 transition-colors group-focus-within:text-violet-400">Status</label>
                <div class="relative">
                  <select
                    v-model="searchForm.published"
                    @change="search"
                    class="w-full px-4 py-3 bg-slate-800/50 border border-slate-600/50 rounded-xl text-white
                           focus:border-violet-500 focus:ring-2 focus:ring-violet-500/20 focus:bg-slate-800/80 
                           transition-all duration-300 backdrop-blur-sm hover:border-slate-500 cursor-pointer"
                  >
                    <option value="" class="bg-slate-800">All Statuses</option>
                    <option value="published" class="bg-slate-800">✅ Published</option>
                    <option value="unpublished" class="bg-slate-800">📝 Unpublished</option>
                  </select>
                  <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-violet-500/0 to-cyan-500/0 group-focus-within:from-violet-500/10 group-focus-within:to-cyan-500/10 transition-all duration-300 pointer-events-none"></div>
                </div>
              </div>

              <!-- Clear Filters -->
              <div class="flex items-end animate-fade-in-up" style="animation-delay: 0.5s;">
                <button
                  @click="clearFilters"
                  class="w-full px-4 py-3 text-sm font-medium text-slate-300 bg-slate-700/50 hover:bg-slate-600/50 
                         border border-slate-600/50 hover:border-slate-500 rounded-xl transition-all duration-300 
                         backdrop-blur-sm hover:text-white transform hover:scale-105 group"
                >
                  <span class="flex items-center justify-center">
                    <svg class="w-4 h-4 mr-2 group-hover:rotate-12 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Clear Filters
                  </span>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Bulk Actions Bar (My Images only) -->
        <div v-if="isMyImages && selectedImages.length > 0" 
             class="backdrop-blur-xl bg-blue-500/10 border border-blue-500/30 rounded-2xl p-6 shadow-lg shadow-blue-500/10 animate-fade-in-up" 
             style="animation-delay: 0.1s;">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
              <div class="w-3 h-3 bg-gradient-to-r from-blue-400 to-cyan-400 rounded-full animate-pulse"></div>
              <span class="text-sm font-medium text-blue-200">
                {{ selectedImages.length }} image{{ selectedImages.length !== 1 ? 's' : '' }} selected
              </span>
            </div>
            <div class="flex items-center space-x-3">
              <select
                v-model="bulkAction"
                class="text-sm bg-slate-800/50 border border-blue-500/30 rounded-lg px-3 py-2 text-blue-200 
                       focus:ring-blue-500/20 focus:border-blue-400 backdrop-blur-sm transition-all duration-300"
              >
                <option value="" class="bg-slate-800">Select action...</option>
                <option value="publish" class="bg-slate-800">✅ Publish</option>
                <option value="unpublish" class="bg-slate-800">📝 Unpublish</option>
                <option value="privacy" class="bg-slate-800">🔒 Change Privacy</option>
                <option value="move_to_album" class="bg-slate-800">📁 Move to Album</option>
                <option value="delete" class="bg-slate-800 text-red-400">🗑️ Delete</option>
              </select>
              
              <select
                v-if="bulkAction === 'privacy'"
                v-model="privacyLevel"
                class="text-sm bg-slate-800/50 border border-blue-500/30 rounded-lg px-3 py-2 text-blue-200 
                       focus:ring-blue-500/20 focus:border-blue-400 backdrop-blur-sm transition-all duration-300"
              >
                <option value="public" class="bg-slate-800">🌍 Public</option>
                <option value="unlisted" class="bg-slate-800">🔗 Unlisted</option>
                <option value="private" class="bg-slate-800">🔒 Private</option>
              </select>

              <select
                v-if="bulkAction === 'move_to_album'"
                v-model="selectedAlbumId"
                class="text-sm bg-slate-800/50 border border-blue-500/30 rounded-lg px-3 py-2 text-blue-200 
                       focus:ring-blue-500/20 focus:border-blue-400 backdrop-blur-sm transition-all duration-300"
              >
                <option value="" class="bg-slate-800">Remove from Album</option>
                <option v-for="album in albums" :key="album.id" :value="album.id" class="bg-slate-800">
                  {{ album.title }}
                </option>
              </select>

              <button
                @click="executeBulkAction"
                :disabled="!bulkAction || bulkActionLoading"
                class="px-6 py-2 bg-gradient-to-r from-blue-600/80 to-cyan-600/80 hover:from-blue-500 hover:to-cyan-500 
                       disabled:from-slate-600 disabled:to-slate-700 text-white text-sm rounded-lg transition-all duration-300 
                       transform hover:scale-105 hover:shadow-lg hover:shadow-blue-500/25 disabled:opacity-50 disabled:cursor-not-allowed 
                       disabled:transform-none backdrop-blur-sm border border-blue-500/30 group"
              >
                <span class="flex items-center">
                  <div v-if="bulkActionLoading" class="w-4 h-4 mr-2 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                  {{ bulkActionLoading ? 'Processing...' : 'Apply' }}
                </span>
              </button>
              
              <button
                @click="clearSelection"
                class="px-6 py-2 bg-slate-700/50 hover:bg-slate-600/50 text-slate-300 hover:text-white text-sm rounded-lg 
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

        <!-- Images Grid -->
        <div class="backdrop-blur-xl bg-white/5 border border-white/10 shadow-2xl rounded-2xl overflow-hidden hover:shadow-cyan-500/10 transition-all duration-500 hover:border-cyan-500/30 animate-fade-in-up" style="animation-delay: 0.2s;">
          <div class="bg-gradient-to-r from-cyan-500/10 to-purple-500/10 p-6 border-b border-white/10">
            <!-- View Controls -->
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-6">
                <div class="flex items-center space-x-2">
                  <div class="w-2 h-2 bg-gradient-to-r from-cyan-500 to-purple-500 rounded-full animate-pulse"></div>
                  <span class="text-lg font-semibold text-white">
                    {{ images.meta?.total || 0 }} image{{ (images.meta?.total !== 1) ? 's' : '' }}
                  </span>
                </div>
                
                <div class="flex items-center space-x-3">
                  <select
                    v-model="searchForm.sort"
                    @change="search"
                    class="text-sm bg-slate-800/50 border border-slate-600/50 rounded-lg px-3 py-2 text-slate-300 
                           focus:ring-cyan-500/20 focus:border-cyan-400 backdrop-blur-sm transition-all duration-300"
                  >
                    <option value="created_at" class="bg-slate-800">Recently Added</option>
                    <option value="title" class="bg-slate-800">Title</option>
                    <option value="views" class="bg-slate-800">Most Viewed</option>
                    <option value="size" class="bg-slate-800">File Size</option>
                  </select>

                  <select
                    v-model="searchForm.direction"
                    @change="search"
                    class="text-sm bg-slate-800/50 border border-slate-600/50 rounded-lg px-3 py-2 text-slate-300 
                           focus:ring-purple-500/20 focus:border-purple-400 backdrop-blur-sm transition-all duration-300"
                  >
                    <option value="desc" class="bg-slate-800">Descending</option>
                    <option value="asc" class="bg-slate-800">Ascending</option>
                  </select>
                </div>
              </div>

              <!-- Select All (My Images only) -->
              <div v-if="isMyImages && images.data?.length > 0" class="flex items-center space-x-3">
                <label class="flex items-center space-x-2 cursor-pointer group">
                  <div class="relative">
                    <input
                      id="select-all"
                      v-model="selectAll"
                      @change="toggleSelectAll"
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
                :style="{ animationDelay: (0.02 * index) + 's' }"
              >
                <!-- Selection Checkbox (My Images only) -->
                <div v-if="isMyImages" class="absolute top-3 left-3 z-20">
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

                <!-- Privacy Badge (My Images only) -->
                <div v-if="isMyImages" class="absolute top-3 right-3 z-20">
                  <span
                    class="px-2 py-1 text-xs font-medium rounded-lg backdrop-blur-md border transition-all duration-300"
                    :class="{
                      'bg-emerald-500/20 text-emerald-300 border-emerald-500/40': image.privacy === 'public',
                      'bg-amber-500/20 text-amber-300 border-amber-500/40': image.privacy === 'unlisted',
                      'bg-red-500/20 text-red-300 border-red-500/40': image.privacy === 'private'
                    }"
                  >
                    {{ image.privacy }}
                  </span>
                </div>

                <!-- Published Status Badge (My Images only) -->
                <div v-if="isMyImages && !image.is_published" class="absolute bottom-3 left-3 z-20">
                  <span class="px-2 py-1 text-xs font-medium bg-slate-700/80 text-slate-300 rounded-lg backdrop-blur-md border border-slate-600/50">
                    Draft
                  </span>
                </div>

                <!-- Image -->
                <div class="relative w-full h-full overflow-hidden">
                  <img
                    :src="getImageUrl(image)"
                    :alt="image.alt_text || image.title"
                    class="w-full h-full object-cover cursor-pointer transition-all duration-700 group-hover:scale-110"
                    @click="openLightbox(image)"
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

            <!-- Empty State -->
            <div v-else class="text-center py-16 animate-fade-in-up" style="animation-delay: 0.3s;">
              <div class="relative inline-block mb-6">
                <PhotoIcon class="mx-auto h-20 w-20 text-slate-500 animate-bounce" style="animation-delay: 0.5s;" />
                <div class="absolute inset-0 animate-ping opacity-25">
                  <PhotoIcon class="h-20 w-20 text-slate-500" />
                </div>
              </div>
              
              <h3 class="text-xl font-semibold text-slate-300 mb-2">{{ getEmptyMessage() }}</h3>
              <div v-if="canUpload && isMyImages" class="mt-8">
                <Link
                  :href="route('upload')"
                  class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-violet-600 to-cyan-600 
                         hover:from-violet-500 hover:to-cyan-500 text-white font-semibold rounded-xl 
                         transition-all duration-300 transform hover:scale-105 hover:shadow-2xl hover:shadow-violet-500/25 
                         border border-violet-500/30 group"
                >
                  <PlusIcon class="h-5 w-5 mr-3 group-hover:rotate-12 transition-transform duration-300" />
                  Upload Images
                  <div class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/20 to-white/0 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-700 rounded-xl"></div>
                </Link>
              </div>
            </div>

            <!-- Pagination -->
            <div v-if="images.data && images.data.length > 0 && images.meta" 
                 class="mt-8 animate-fade-in-up" 
                 style="animation-delay: 0.4s;">
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

    <!-- Lightbox -->
    <Lightbox
      v-if="showLightbox"
      :images="images.data"
      :initial-index="currentImageIndex"
      @close="showLightbox = false"
    />
  </AppLayout>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import route from 'ziggy-js'
import { PlusIcon, PhotoIcon } from '@heroicons/vue/24/outline'

import AppLayout from '@/Layouts/AppLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import Lightbox from '@/Components/Gallery/Lightbox.vue'

const page = usePage()

const props = defineProps({
  images: { type: Object, default: () => ({ data: [], links: [], meta: {} }) },
  albums: { type: Array, default: () => [] },
  filters: { type: Object, default: () => ({}) },
  canUpload: { type: Boolean, default: false },
  isMyImages: { type: Boolean, default: false },
})

// FIXED: Reactive state without conflicting parameters
const searchForm = reactive({
  search: props.filters.search || '',
  album: props.filters.album || '',
  privacy: props.filters.privacy || '',
  published: props.filters.published || '',
  date_from: props.filters.date_from || '',
  date_to: props.filters.date_to || '',
  sort: props.filters.sort || 'created_at',
  direction: props.filters.direction || 'desc',
})

// Bulk operations (My Images only)
const selectedImages = ref([])
const selectAll = ref(false)
const bulkAction = ref('')
const privacyLevel = ref('public')
const selectedAlbumId = ref('')
const bulkActionLoading = ref(false)

// Lightbox
const showLightbox = ref(false)
const currentImageIndex = ref(0)

// Computed properties
const pageTitle = computed(() => {
  return props.isMyImages ? 'My Images' : 'Image Gallery'
})

const pageDescription = computed(() => {
  return props.isMyImages 
    ? 'Manage your uploaded images and privacy settings'
    : 'Discover and explore amazing photography'
})

const hasFilters = computed(() => {
  return Object.entries(searchForm).some(([key, value]) => 
    value && value !== '' && !['sort', 'direction'].includes(key)
  )
})

// FIXED: Methods with proper route handling
const search = () => {
  const cleanForm = Object.fromEntries(
    Object.entries(searchForm).filter(([key, value]) => value !== '')
  )
  
  // FIXED: Use correct route based on context
  const routeName = props.isMyImages ? 'my.images' : 'gallery.index'
  
  router.get(route(routeName), cleanForm, {
    preserveState: true,
    replace: true,
  })
}

const clearFilters = () => {
  Object.keys(searchForm).forEach(key => {
    if (!['sort', 'direction'].includes(key)) {
      searchForm[key] = ''
    }
  })
  search()
}

const getImageUrl = (image) => {
  if (image.storage_path) {
    return `http://localhost:9000/gallery-images/${image.storage_path}`
  }
  return '/images/placeholder.jpg'
}

const openLightbox = (image) => {
  currentImageIndex.value = props.images.data.findIndex(img => img.id === image.id)
  showLightbox.value = true
}

const toggleSelectAll = () => {
  if (selectAll.value) {
    selectedImages.value = props.images.data.map(img => img.id)
  } else {
    selectedImages.value = []
  }
}

const clearSelection = () => {
  selectedImages.value = []
  selectAll.value = false
  bulkAction.value = ''
}

const executeBulkAction = () => {
  if (!bulkAction.value || selectedImages.value.length === 0) return

  if (bulkAction.value === 'delete') {
    if (!confirm(`Are you sure you want to delete ${selectedImages.value.length} image(s)? This action cannot be undone.`)) {
      return
    }
  }

  bulkActionLoading.value = true

  const data = {
    action: bulkAction.value,
    image_ids: selectedImages.value,
  }

  if (bulkAction.value === 'privacy') {
    data.privacy_level = privacyLevel.value
  }

  if (bulkAction.value === 'move_to_album') {
    data.album_id = selectedAlbumId.value
  }

  router.post(route('images.bulk'), data, {
    onSuccess: () => {
      clearSelection()
      bulkActionLoading.value = false
    },
    onError: () => {
      bulkActionLoading.value = false
    }
  })
}

const getEmptyMessage = () => {
  if (hasFilters.value) {
    return 'No images found matching your criteria.'
  }
  
  if (props.isMyImages) {
    return 'You haven\'t uploaded any images yet.'
  }
  
  return 'No images available yet.'
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