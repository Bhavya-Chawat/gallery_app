

<template>
  <AppLayout>
    <Head :title="pageTitle" />

    <!-- Animated Background Elements -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
      <!-- Animated Mesh Background -->
      <div class="absolute inset-0 opacity-20">
        <svg class="absolute inset-0 w-full h-full animate-pulse" viewBox="0 0 1000 1000">
          <defs>
            <linearGradient id="meshGradient" x1="0%" y1="0%" x2="100%" y2="100%">
              <stop offset="0%" class="animate-[colorShift_8s_ease-in-out_infinite] stop-color-violet-500" />
              <stop offset="50%" class="animate-[colorShift_8s_ease-in-out_infinite_2s] stop-color-cyan-500" />
              <stop offset="100%" class="animate-[colorShift_8s_ease-in-out_infinite_4s] stop-color-purple-500" />
            </linearGradient>
          </defs>
          <path d="M0,100 Q250,50 500,100 T1000,100 L1000,200 Q750,150 500,200 T0,200 Z" fill="url(#meshGradient)" opacity="0.1" class="animate-[wave_6s_ease-in-out_infinite]" />
          <path d="M0,300 Q250,250 500,300 T1000,300 L1000,400 Q750,350 500,400 T0,400 Z" fill="url(#meshGradient)" opacity="0.05" class="animate-[wave_8s_ease-in-out_infinite_reverse]" />
        </svg>
      </div>

      <!-- Floating Particles -->
      <div class="absolute inset-0">
        <div v-for="i in 20" :key="i" 
             class="absolute w-1 h-1 bg-gradient-to-r from-violet-400 to-cyan-400 rounded-full animate-float"
             :style="{
               left: Math.random() * 100 + '%',
               top: Math.random() * 100 + '%',
               animationDelay: Math.random() * 10 + 's',
               animationDuration: (3 + Math.random() * 4) + 's'
             }">
        </div>
      </div>
    </div>

    <template #header>
      <div class="relative bg-gradient-to-br from-slate-900 via-violet-900/20 to-slate-900 border-b border-violet-500/10">
        <div class="absolute inset-0 bg-gradient-to-r from-violet-500/5 via-transparent to-cyan-500/5"></div>
        <div class="relative flex items-center justify-between p-6">
          <div class="space-y-2">
            <h2 class="text-3xl font-bold bg-gradient-to-r from-violet-400 via-purple-400 to-cyan-400 bg-clip-text text-transparent animate-shimmer">
              {{ pageTitle }}
            </h2>
            <p class="text-slate-300 text-lg">
              {{ pageDescription }}
            </p>
          </div>
          <div class="flex items-center space-x-4">
            <Link
              v-if="canCreate"
              :href="route('albums.create')"
              class="group relative inline-flex items-center px-6 py-3 bg-gradient-to-r from-violet-500 to-purple-500 border border-violet-400/20 rounded-xl font-semibold text-sm text-white uppercase tracking-widest shadow-lg shadow-violet-500/25 hover:shadow-xl hover:shadow-violet-500/40 hover:scale-105 transform transition-all duration-300 backdrop-blur-sm"
            >
              <div class="absolute inset-0 bg-gradient-to-r from-violet-600 to-purple-600 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
              <PlusIcon class="h-5 w-5 mr-2 relative z-10" />
              <span class="relative z-10">Create Album</span>
            </Link>
          </div>
        </div>
      </div>
    </template>

    <div class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 py-8">
      <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <!-- Enhanced Filters Card -->
        <div class="mb-8 animate-fade-in-up">
          <div class="relative bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl shadow-xl shadow-black/20 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-violet-500/5 via-transparent to-cyan-500/5"></div>
            <div class="relative p-8">
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Search -->
                <div class="space-y-3">
                  <label class="block text-sm font-semibold text-slate-200">Search</label>
                  <div class="relative">
                    <input
                      v-model="searchForm.search"
                      @keyup.enter="search"
                      type="text"
                      :placeholder="isMyAlbums ? 'Search your albums...' : 'Search albums...'"
                      class="w-full px-4 py-3 bg-slate-800/50 border border-slate-600/30 rounded-xl text-slate-200 placeholder-slate-400 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-300 backdrop-blur-sm"
                    />
                    <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-violet-500/10 to-cyan-500/10 opacity-0 hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
                  </div>
                </div>

                <!-- Privacy Filter (My Albums only) -->
                <div v-if="isMyAlbums" class="space-y-3">
                  <label class="block text-sm font-semibold text-slate-200">Privacy</label>
                  <select
                    v-model="searchForm.privacy"
                    @change="search"
                    class="w-full px-4 py-3 bg-slate-800/50 border border-slate-600/30 rounded-xl text-slate-200 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-300 backdrop-blur-sm"
                  >
                    <option value="">All Privacy Levels</option>
                    <option value="public">Public</option>
                    <option value="unlisted">Unlisted</option>
                    <option value="private">Private</option>
                  </select>
                </div>

                <!-- Published Status (My Albums only) -->
                <div v-if="isMyAlbums" class="space-y-3">
                  <label class="block text-sm font-semibold text-slate-200">Status</label>
                  <select
                    v-model="searchForm.published"
                    @change="search"
                    class="w-full px-4 py-3 bg-slate-800/50 border border-slate-600/30 rounded-xl text-slate-200 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-300 backdrop-blur-sm"
                  >
                    <option value="">All Statuses</option>
                    <option value="published">Published</option>
                    <option value="unpublished">Unpublished</option>
                  </select>
                </div>

                <!-- Sort -->
                <div class="space-y-3">
                  <label class="block text-sm font-semibold text-slate-200">Sort by</label>
                  <select
                    v-model="searchForm.sort"
                    @change="search"
                    class="w-full px-4 py-3 bg-slate-800/50 border border-slate-600/30 rounded-xl text-slate-200 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-300 backdrop-blur-sm"
                  >
                    <option value="updated_at">Recently Updated</option>
                    <option value="created_at">Recently Created</option>
                    <option value="title">Title</option>
                    <option value="images_count">Image Count</option>
                  </select>
                </div>
              </div>

              <!-- Clear Filters Button -->
              <div class="mt-6 flex justify-center">
                <button
                  @click="clearFilters"
                  class="px-6 py-3 bg-slate-700/50 text-slate-300 border border-slate-600/30 rounded-xl hover:bg-slate-600/50 hover:text-white transition-all duration-300 backdrop-blur-sm"
                >
                  Clear Filters
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Enhanced Bulk Actions Bar (My Albums only) -->
        <div v-if="isMyAlbums && selectedAlbums.length > 0" 
             class="mb-8 animate-slide-in-down">
          <div class="relative bg-gradient-to-r from-violet-500/10 via-purple-500/10 to-cyan-500/10 backdrop-blur-xl border border-violet-400/20 rounded-2xl p-6 shadow-xl shadow-violet-500/10">
            <div class="absolute inset-0 bg-gradient-to-r from-violet-500/5 to-cyan-500/5 rounded-2xl"></div>
            <div class="relative flex items-center justify-between">
              <span class="text-lg font-semibold text-white">
                {{ selectedAlbums.length }} album(s) selected
              </span>
              <div class="flex items-center space-x-4">
                <select
                  v-model="bulkAction"
                  class="px-4 py-2 bg-slate-800/50 border border-violet-400/30 rounded-lg text-slate-200 focus:ring-2 focus:ring-violet-500 backdrop-blur-sm"
                >
                  <option value="">Select action...</option>
                  <option value="publish">Publish</option>
                  <option value="unpublish">Unpublish</option>
                  <option value="privacy">Change Privacy</option>
                  <option value="delete" class="text-red-400">Delete</option>
                </select>
                
                <select
                  v-if="bulkAction === 'privacy'"
                  v-model="privacyLevel"
                  class="px-4 py-2 bg-slate-800/50 border border-violet-400/30 rounded-lg text-slate-200 focus:ring-2 focus:ring-violet-500 backdrop-blur-sm"
                >
                  <option value="public">Public</option>
                  <option value="unlisted">Unlisted</option>
                  <option value="private">Private</option>
                </select>

                <button
                  @click="executeBulkAction"
                  :disabled="!bulkAction || bulkActionLoading"
                  class="px-6 py-2 bg-gradient-to-r from-violet-500 to-purple-500 text-white rounded-lg hover:from-violet-600 hover:to-purple-600 disabled:opacity-50 transition-all duration-300 shadow-lg"
                >
                  {{ bulkActionLoading ? 'Processing...' : 'Apply' }}
                </button>
                
                <button
                  @click="clearSelection"
                  class="px-6 py-2 bg-slate-600/50 text-slate-200 rounded-lg hover:bg-slate-500/50 transition-all duration-300"
                >
                  Clear
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Enhanced Albums Grid -->
        <div v-if="albums.data && albums.data.length > 0" 
             class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <div
            v-for="(album, index) in albums.data"
            :key="album.id"
            class="group relative bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl overflow-hidden hover:border-violet-400/30 transition-all duration-500 hover:scale-[1.02] hover:shadow-2xl hover:shadow-violet-500/20 animate-fade-in-up"
            :style="{ animationDelay: (index * 0.1) + 's' }"
          >
            <!-- Gradient Overlay on Hover -->
            <div class="absolute inset-0 bg-gradient-to-br from-violet-500/10 via-purple-500/5 to-cyan-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500 rounded-2xl"></div>

            <!-- Selection Checkbox (My Albums only) -->
            <div v-if="isMyAlbums" class="absolute top-4 left-4 z-20">
              <div class="relative">
                <input
                  v-model="selectedAlbums"
                  :value="album.id"
                  type="checkbox"
                  class="h-5 w-5 text-violet-500 focus:ring-violet-500 border-slate-500 rounded bg-slate-800/80 backdrop-blur-sm transition-all duration-300"
                />
                <div class="absolute inset-0 rounded bg-violet-500/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
              </div>
            </div>

            <!-- Privacy Badge (My Albums only) -->
            <div v-if="isMyAlbums" class="absolute top-4 right-4 z-20">
              <span
                class="px-3 py-1 text-xs font-semibold rounded-full backdrop-blur-md border transition-all duration-300"
                :class="{
                  'bg-emerald-500/20 text-emerald-300 border-emerald-400/30': album.privacy === 'public',
                  'bg-amber-500/20 text-amber-300 border-amber-400/30': album.privacy === 'unlisted',
                  'bg-red-500/20 text-red-300 border-red-400/30': album.privacy === 'private'
                }"
              >
                {{ album.privacy }}
              </span>
            </div>

            <!-- Enhanced Cover Image -->
            <div class="relative aspect-video bg-gradient-to-br from-slate-800 to-slate-900 overflow-hidden">
              <img
                v-if="album.cover_image"
                :src="getImageUrl(album.cover_image)"
                :alt="album.title"
                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
              />
              <div v-else class="flex items-center justify-center h-full">
                <FolderIcon class="h-16 w-16 text-slate-600 group-hover:text-violet-400 transition-colors duration-300" />
              </div>
              
              <!-- Enhanced Image Count Overlay -->
              <div class="absolute bottom-3 right-3 bg-black/60 backdrop-blur-md text-white px-3 py-1 rounded-lg text-sm font-medium border border-white/10">
                <span class="bg-gradient-to-r from-violet-400 to-cyan-400 bg-clip-text text-transparent">
                  {{ album.images_count || 0 }} images
                </span>
              </div>

              <!-- Overlay Gradient -->
              <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
            </div>

            <!-- Enhanced Album Info -->
            <div class="relative p-6 space-y-4">
              <div class="flex items-start justify-between">
                <div class="min-w-0 flex-1 space-y-2">
                  <Link
                    :href="route('albums.show', album.slug)"
                    class="block text-xl font-bold text-white hover:text-transparent hover:bg-gradient-to-r hover:from-violet-400 hover:to-cyan-400 hover:bg-clip-text transition-all duration-300 truncate"
                  >
                    {{ album.title }}
                  </Link>
                  
                  <p v-if="album.description" class="text-slate-300 text-sm leading-relaxed line-clamp-2">
                    {{ album.description }}
                  </p>
                  
                  <!-- Enhanced Published Status Badge (My Albums only) -->
                  <div v-if="isMyAlbums && !album.is_published" class="pt-2">
                    <span class="inline-flex items-center px-3 py-1 text-xs font-medium bg-slate-700/50 text-slate-300 rounded-full border border-slate-600/30 backdrop-blur-sm">
                      <div class="w-2 h-2 bg-slate-400 rounded-full mr-2 animate-pulse"></div>
                      Draft
                    </span>
                  </div>
                  
                  <!-- Enhanced Date and Owner Info -->
                  <div class="flex items-center pt-2 text-xs text-slate-400 space-x-2">
                    <span class="bg-gradient-to-r from-violet-400 to-cyan-400 bg-clip-text text-transparent">
                      {{ formatDate(album.updated_at) }}
                    </span>
                    <span class="text-slate-600">•</span>
                    <span>by {{ album.owner?.name }}</span>
                  </div>
                </div>

                <!-- Enhanced Actions Menu (My Albums only) -->
                <div v-if="isMyAlbums" class="ml-4">
                  <Dropdown align="right" width="56">
                    <template #trigger>
                      <button class="p-2 rounded-xl hover:bg-white/10 transition-all duration-300 group/btn backdrop-blur-sm border border-transparent hover:border-violet-400/30">
                        <EllipsisVerticalIcon class="h-5 w-5 text-slate-400 group-hover/btn:text-violet-400 transition-colors duration-300" />
                      </button>
                    </template>
                    <template #content>
                      <div class="bg-slate-800/90 backdrop-blur-xl border border-slate-700/50 rounded-xl shadow-2xl shadow-black/50 overflow-hidden">
                        <DropdownLink :href="route('albums.show', album.slug)" 
                                      class="flex items-center px-4 py-3 text-slate-200 hover:bg-violet-500/20 hover:text-white transition-all duration-200">
                          <EyeIcon class="h-4 w-4 mr-3 text-cyan-400" />
                          View Album
                        </DropdownLink>
                        <DropdownLink :href="route('albums.edit', album.id)"
                                      class="flex items-center px-4 py-3 text-slate-200 hover:bg-violet-500/20 hover:text-white transition-all duration-200">
                          <PencilIcon class="h-4 w-4 mr-3 text-violet-400" />
                          Edit Album
                        </DropdownLink>
                        <DropdownLink :href="route('albums.add-images-form', album.id)"
                                      class="flex items-center px-4 py-3 text-slate-200 hover:bg-violet-500/20 hover:text-white transition-all duration-200">
                          <PlusIcon class="h-4 w-4 mr-3 text-purple-400" />
                          Add Images
                        </DropdownLink>
                        <div class="border-t border-slate-700/50"></div>
                        <DropdownLink @click="deleteAlbum(album)" 
                                      class="flex items-center px-4 py-3 text-red-300 hover:bg-red-500/20 hover:text-red-200 transition-all duration-200">
                          <TrashIcon class="h-4 w-4 mr-3" />
                          Delete
                        </DropdownLink>
                      </div>
                    </template>
                  </Dropdown>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Enhanced Empty State -->
        <div v-else class="text-center py-20 animate-fade-in">
          <div class="relative">
            <!-- Floating Elements -->
            <div class="absolute -top-8 left-1/2 transform -translate-x-1/2">
              <div class="w-4 h-4 bg-violet-500 rounded-full animate-float opacity-60" style="animation-delay: 0s;"></div>
            </div>
            <div class="absolute -top-4 left-1/3 transform -translate-x-1/2">
              <div class="w-2 h-2 bg-cyan-500 rounded-full animate-float opacity-40" style="animation-delay: 2s;"></div>
            </div>
            <div class="absolute -top-6 right-1/3 transform translate-x-1/2">
              <div class="w-3 h-3 bg-purple-500 rounded-full animate-float opacity-50" style="animation-delay: 4s;"></div>
            </div>

            <div class="relative bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-12 max-w-md mx-auto">
              <FolderIcon class="mx-auto h-20 w-20 text-slate-600 mb-6 animate-pulse" />
              <h3 class="text-2xl font-bold text-white mb-3">{{ getEmptyMessage() }}</h3>
              
              <div v-if="canCreate && isMyAlbums && !hasFilters" class="mt-8">
                <Link
                  :href="route('albums.create')"
                  class="group relative inline-flex items-center px-8 py-4 bg-gradient-to-r from-violet-500 to-purple-500 border border-violet-400/20 rounded-xl font-semibold text-white shadow-lg shadow-violet-500/25 hover:shadow-xl hover:shadow-violet-500/40 hover:scale-105 transform transition-all duration-300"
                >
                  <div class="absolute inset-0 bg-gradient-to-r from-violet-600 to-purple-600 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                  <PlusIcon class="h-6 w-6 mr-3 relative z-10" />
                  <span class="relative z-10">Create Your First Album</span>
                </Link>
              </div>
            </div>
          </div>
        </div>

        <!-- Enhanced Pagination -->
        <div v-if="albums.data && albums.data.length > 0 && albums.meta" class="mt-12">
          <div class="flex justify-center">
            <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-2">
              <Pagination
                :links="albums.links || []"
                :meta="albums.meta || {}"
                class="pagination-enhanced"
              />
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import route from 'ziggy-js'
import { 
  PlusIcon, 
  FolderIcon, 
  EyeIcon, 
  PencilIcon, 
  TrashIcon,
  EllipsisVerticalIcon
} from '@heroicons/vue/24/outline'

import AppLayout from '@/Layouts/AppLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import Dropdown from '@/Components/Dropdown.vue'
import DropdownLink from '@/Components/DropdownLink.vue'

const page = usePage()

const props = defineProps({
  albums: { type: Object, default: () => ({ data: [], links: [], meta: {} }) },
  filters: { type: Object, default: () => ({}) },
  canCreate: { type: Boolean, default: false },
  isMyAlbums: { type: Boolean, default: false },
})

// FIXED: Reactive state without conflicting parameters
const searchForm = reactive({
  search: props.filters.search || '',
  privacy: props.filters.privacy || '',
  published: props.filters.published || '',
  sort: props.filters.sort || 'updated_at',
  direction: props.filters.direction || 'desc',
})

// Bulk operations (My Albums only)
const selectedAlbums = ref([])
const selectAll = ref(false)
const bulkAction = ref('')
const privacyLevel = ref('public')
const bulkActionLoading = ref(false)

// Computed properties
const pageTitle = computed(() => {
  return props.isMyAlbums ? 'My Albums' : 'Albums'
})

const pageDescription = computed(() => {
  return props.isMyAlbums 
    ? 'Manage your photo collections and privacy settings'
    : 'Browse and explore photo collections'
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
  const routeName = props.isMyAlbums ? 'my.albums' : 'albums.index'
  
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
  if (image?.storage_path) {
    return `http://localhost:9000/gallery-images/${image.storage_path}`
  }
  return '/images/placeholder.jpg'
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  })
}

const clearSelection = () => {
  selectedAlbums.value = []
  selectAll.value = false
  bulkAction.value = ''
}

const executeBulkAction = () => {
  if (!bulkAction.value || selectedAlbums.value.length === 0) return

  if (bulkAction.value === 'delete') {
    if (!confirm(`Are you sure you want to delete ${selectedAlbums.value.length} album(s)? Images will be preserved.`)) {
      return
    }
  }

  bulkActionLoading.value = true

  const data = {
    action: bulkAction.value,
    album_ids: selectedAlbums.value,
  }

  if (bulkAction.value === 'privacy') {
    data.privacy_level = privacyLevel.value
  }

  router.post(route('albums.bulk'), data, {
    onSuccess: () => {
      clearSelection()
      bulkActionLoading.value = false
    },
    onError: () => {
      bulkActionLoading.value = false
    }
  })
}

const deleteAlbum = (album) => {
  if (confirm(`Are you sure you want to delete "${album.title}"? Images will be preserved.`)) {
    router.delete(route('albums.destroy', album.id))
  }
}

const getEmptyMessage = () => {
  if (hasFilters.value) {
    return 'No albums found matching your criteria.'
  }
  
  if (props.isMyAlbums) {
    return 'You haven\'t created any albums yet.'
  }
  
  return 'No albums available yet.'
}
</script>

<style>
@keyframes shimmer {
  0%, 100% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
}

@keyframes colorShift {
  0%, 100% { stop-color: rgb(139 92 246); }
  33% { stop-color: rgb(168 85 247); }
  66% { stop-color: rgb(34 211 238); }
}

@keyframes wave {
  0%, 100% { transform: translateX(0px) translateY(0px); }
  50% { transform: translateX(10px) translateY(-5px); }
}

@keyframes float {
  0%, 100% { 
    transform: translateY(0px) rotate(0deg);
    opacity: 0.4;
  }
  50% { 
    transform: translateY(-20px) rotate(180deg);
    opacity: 0.8;
  }
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

@keyframes slide-in-down {
  from {
    opacity: 0;
    transform: translateY(-30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes fade-in {
  from { opacity: 0; }
  to { opacity: 1; }
}

.animate-shimmer {
  background-size: 200% 200%;
  animation: shimmer 3s ease-in-out infinite;
}

.animate-fade-in-up {
  animation: fade-in-up 0.6s ease-out forwards;
}

.animate-slide-in-down {
  animation: slide-in-down 0.5s ease-out forwards;
}

.animate-fade-in {
  animation: fade-in 0.8s ease-out forwards;
}

.animate-float {
  animation: float 6s ease-in-out infinite;
}
</style>