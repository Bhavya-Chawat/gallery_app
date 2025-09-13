<template>
  <AppLayout>
    <Head title="Collections" />

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
            {{ isMyCollections ? 'My Collections' : 'Collections' }}
          </h2>
          <p class="text-sm text-slate-400 mt-1">
            {{ isMyCollections ? 'Manage your curated image collections' : 'Browse curated image collections' }}
          </p>
        </div>
        <div class="flex items-center space-x-4">
          <Link
            v-if="canCreate"
            href="/collections/create"
            class="group inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-violet-600 to-purple-600 hover:from-violet-500 hover:to-purple-500 border border-violet-500/20 rounded-xl font-semibold text-sm text-white shadow-lg hover:shadow-violet-500/25 hover:shadow-xl transition-all duration-300 hover:scale-105"
          >
            <PlusIcon class="h-4 w-4 mr-2 group-hover:rotate-90 transition-transform duration-300" />
            Create Collection
          </Link>
        </div>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Filters -->
        <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl shadow-xl mb-8 animate-fade-in-up" style="animation-delay: 0.2s;">
          <div class="p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
              <!-- Search -->
              <div class="group">
                <label class="block text-sm font-bold text-transparent bg-clip-text bg-gradient-to-r from-violet-400 to-purple-400 mb-3">Search</label>
                <div class="relative">
                  <input
                    v-model="searchForm.search"
                    @keyup.enter="search"
                    type="text"
                    :placeholder="isMyCollections ? 'Search your collections...' : 'Search collections...'"
                    class="w-full px-4 py-3 bg-white/10 backdrop-blur-xl border border-white/20 rounded-xl text-slate-200 placeholder-slate-400 focus:border-violet-400 focus:ring-2 focus:ring-violet-500/20 transition-all duration-300 hover:bg-white/15"
                  />
                  <div class="absolute inset-0 bg-gradient-to-r from-violet-600/10 to-purple-600/10 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
                </div>
              </div>

              <!-- Privacy Filter (My Collections only) -->
              <div v-if="isMyCollections" class="group">
                <label class="block text-sm font-bold text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-blue-400 mb-3">Privacy</label>
                <div class="relative">
                  <select
                    v-model="searchForm.privacy"
                    @change="search"
                    class="w-full px-4 py-3 bg-white/10 backdrop-blur-xl border border-white/20 rounded-xl text-slate-200 focus:border-cyan-400 focus:ring-2 focus:ring-cyan-500/20 transition-all duration-300 hover:bg-white/15"
                  >
                    <option value="">All Privacy Levels</option>
                    <option value="public">Public</option>
                    <option value="unlisted">Unlisted</option>
                    <option value="private">Private</option>
                  </select>
                  <div class="absolute inset-0 bg-gradient-to-r from-cyan-600/10 to-blue-600/10 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
                </div>
              </div>

              <!-- Sort -->
              <div class="group">
                <label class="block text-sm font-bold text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-pink-400 mb-3">Sort by</label>
                <div class="relative">
                  <select
                    v-model="searchForm.sort"
                    @change="search"
                    class="w-full px-4 py-3 bg-white/10 backdrop-blur-xl border border-white/20 rounded-xl text-slate-200 focus:border-purple-400 focus:ring-2 focus:ring-purple-500/20 transition-all duration-300 hover:bg-white/15"
                  >
                    <option value="updated_at">Recently Updated</option>
                    <option value="created_at">Recently Created</option>
                    <option value="title">Title</option>
                    <option value="images_count">Image Count</option>
                  </select>
                  <div class="absolute inset-0 bg-gradient-to-r from-purple-600/10 to-pink-600/10 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
                </div>
              </div>

              <!-- Clear Filters -->
              <div class="flex items-end">
                <button
                  @click="clearFilters"
                  class="group w-full px-4 py-3 text-sm text-slate-300 bg-white/10 backdrop-blur-xl border border-white/20 rounded-xl hover:bg-white/20 hover:text-white hover:border-slate-400/30 transition-all duration-300 font-medium"
                >
                  Clear Filters
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Collections Grid -->
        <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl shadow-xl animate-fade-in-up" style="animation-delay: 0.3s;">
          <div class="p-8">
            <!-- View Controls -->
            <div class="flex items-center justify-between mb-8 p-4 bg-white/5 backdrop-blur-xl rounded-xl border border-white/10">
              <div class="flex items-center space-x-4">
                <span class="text-sm text-slate-400">
                  <span class="text-violet-400 font-bold">{{ collections.data?.length || 0 }}</span> collection{{ ((collections.data?.length || 0) !== 1) ? 's' : '' }}
                </span>
                
                <div class="relative group">
                  <select
                    v-model="searchForm.direction"
                    @change="search"
                    class="text-sm bg-white/10 backdrop-blur-xl border border-white/20 rounded-lg px-3 py-2 text-slate-200 focus:border-violet-400 focus:ring-1 focus:ring-violet-500/20 transition-all duration-300"
                  >
                    <option value="desc">Descending</option>
                    <option value="asc">Ascending</option>
                  </select>
                </div>
              </div>
            </div>

            <!-- Collections Grid -->
            <div v-if="collections.data && collections.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
              <div
                v-for="(collection, index) in collections.data"
                :key="collection.id"
                class="group relative bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl shadow-xl overflow-hidden hover:shadow-2xl hover:shadow-violet-500/20 hover:border-violet-400/30 transition-all duration-500 hover:scale-105 animate-fade-in-up"
                :style="{ animationDelay: (0.1 * (index % 9)) + 's' }"
              >
                <!-- Cover Image -->
                <div class="aspect-video bg-slate-800/50 relative overflow-hidden">
                  <img
                    v-if="collection.cover_image"
                    :src="getImageUrl(collection.cover_image)"
                    :alt="collection.title"
                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
                    loading="lazy"
                  />
                  <div v-else class="flex items-center justify-center h-full">
                    <div class="relative">
                      <FolderIcon class="h-16 w-16 text-slate-600" />
                      <div class="absolute inset-0 bg-gradient-to-r from-violet-400/20 to-cyan-400/20 blur-xl rounded-full"></div>
                    </div>
                  </div>
                  
                  <!-- Gradient overlay -->
                  <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                  
                  <!-- Image count overlay -->
                  <div class="absolute bottom-3 right-3 bg-black/60 backdrop-blur-sm text-white px-3 py-1.5 rounded-lg text-xs font-medium border border-white/20">
                    {{ collection.images_count || 0 }} images
                  </div>

                  <!-- Privacy Badge (My Collections only) -->
                  <div v-if="isMyCollections" class="absolute top-3 right-3">
                    <span
                      class="px-3 py-1.5 text-xs font-bold rounded-full border backdrop-blur-sm"
                      :class="{
                        'bg-emerald-500/20 text-emerald-300 border-emerald-500/30': collection.privacy === 'public',
                        'bg-amber-500/20 text-amber-300 border-amber-500/30': collection.privacy === 'unlisted',
                        'bg-red-500/20 text-red-300 border-red-500/30': collection.privacy === 'private'
                      }"
                    >
                      {{ collection.privacy }}
                    </span>
                  </div>

                  <!-- Published status indicator -->
                  <div v-if="isMyCollections && !collection.is_published" class="absolute top-3 left-3">
                    <span class="px-3 py-1.5 text-xs font-bold rounded-full bg-slate-500/20 text-slate-300 border border-slate-500/30 backdrop-blur-sm">
                      Draft
                    </span>
                  </div>

                  <!-- Hover glow effect -->
                  <div class="absolute inset-0 bg-gradient-to-r from-violet-600/20 to-cyan-600/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
                </div>

                <!-- Collection Info -->
                <div class="p-6">
                  <div class="flex items-start justify-between">
                    <div class="min-w-0 flex-1">
                      <Link
                        :href="`/collections/${collection.slug}`"
                        class="text-lg font-bold text-transparent bg-clip-text bg-gradient-to-r from-white to-slate-300 hover:from-violet-400 hover:to-cyan-400 block truncate transition-all duration-300"
                      >
                        {{ collection.title }}
                      </Link>
                      <p v-if="collection.description" class="text-sm text-slate-400 mt-2 line-clamp-2 leading-relaxed">
                        {{ collection.description }}
                      </p>
                      
                      <div class="flex items-center mt-3 text-xs text-slate-500">
                        <span>{{ formatDate(collection.updated_at) }}</span>
                        <span class="mx-2">•</span>
                        <span class="text-violet-400">{{ collection.curator?.name || 'Anonymous' }}</span>
                      </div>
                    </div>

                    <!-- Actions Menu (My Collections only) -->
                    <div v-if="isMyCollections" class="ml-3">
                      <Dropdown align="right" width="48">
                        <template #trigger>
                          <button class="p-2 rounded-xl hover:bg-white/10 transition-all duration-300 group">
                            <EllipsisVerticalIcon class="h-5 w-5 text-slate-400 group-hover:text-white" />
                          </button>
                        </template>
                        <template #content>
                          <div class="py-2 bg-slate-900/95 backdrop-blur-xl border border-white/20 rounded-xl shadow-2xl">
                            <DropdownLink :href="`/collections/${collection.slug}`" class="flex items-center px-4 py-2 text-sm text-slate-300 hover:bg-white/10 hover:text-white transition-colors">
                              <EyeIcon class="h-4 w-4 mr-3 text-violet-400" />
                              View Collection
                            </DropdownLink>
                            <DropdownLink :href="`/collections/${collection.slug}/edit`" class="flex items-center px-4 py-2 text-sm text-slate-300 hover:bg-white/10 hover:text-white transition-colors">
                              <PencilIcon class="h-4 w-4 mr-3 text-cyan-400" />
                              Edit Collection
                            </DropdownLink>
                            <div class="border-t border-white/10 my-2"></div>
                            <button
                              @click="togglePublishStatus(collection)"
                              class="flex w-full items-center px-4 py-2 text-sm text-left text-slate-300 hover:bg-white/10 hover:text-white transition-colors"
                            >
                              <component :is="collection.is_published ? EyeSlashIcon : EyeIcon" class="h-4 w-4 mr-3 text-purple-400" />
                              {{ collection.is_published ? 'Unpublish' : 'Publish' }}
                            </button>
                            <div class="border-t border-white/10 my-2"></div>
                            <button
                              @click="deleteCollection(collection)"
                              class="flex w-full items-center px-4 py-2 text-sm text-left text-red-400 hover:bg-red-500/10 hover:text-red-300 transition-colors"
                            >
                              <TrashIcon class="h-4 w-4 mr-3" />
                              Delete
                            </button>
                          </div>
                        </template>
                      </Dropdown>
                    </div>
                  </div>
                </div>

                <!-- Decorative border gradient -->
                <div class="absolute inset-0 bg-gradient-to-r from-violet-500/0 via-violet-500/20 to-cyan-500/0 opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none rounded-2xl"></div>
              </div>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-20 animate-fade-in-up" style="animation-delay: 0.4s;">
              <div class="relative inline-block mb-6">
                <FolderIcon class="mx-auto h-20 w-20 text-slate-600" />
                <div class="absolute inset-0 bg-gradient-to-r from-violet-400/30 to-cyan-400/30 blur-2xl rounded-full"></div>
              </div>
              <h3 class="text-xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-violet-400 to-cyan-400 mb-2">
                {{ getEmptyMessage() }}
              </h3>
              <p class="text-slate-400 mb-8 max-w-md mx-auto leading-relaxed">
                <span v-if="hasFilters">Try adjusting your search criteria.</span>
                <span v-else-if="isMyCollections">Create your first collection to organize your images.</span>
                <span v-else>Check back later for new collections.</span>
              </p>
              <div v-if="canCreate && isMyCollections" class="mt-8">
                <Link
                  href="/collections/create"
                  class="group inline-flex items-center px-8 py-4 bg-gradient-to-r from-violet-600 to-cyan-600 hover:from-violet-500 hover:to-cyan-500 border border-violet-500/20 shadow-lg text-sm font-bold rounded-2xl text-white hover:shadow-violet-500/25 hover:shadow-2xl transition-all duration-300 hover:scale-105"
                >
                  <PlusIcon class="-ml-1 mr-3 h-6 w-6 group-hover:rotate-90 transition-transform duration-300" />
                  Create Collection
                  <div class="absolute inset-0 bg-gradient-to-r from-violet-400/20 to-cyan-400/20 rounded-2xl blur opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                </Link>
              </div>
            </div>

            <!-- Pagination -->
            <div v-if="collections.links && collections.links.length > 3" class="mt-8">
              <nav class="flex justify-center">
                <div class="flex space-x-2">
                  <Link
                    v-for="link in collections.links"
                    :key="link.label"
                    :href="link.url || '#'"
                    :class="[
                      'px-4 py-2 text-sm rounded-xl font-medium transition-all duration-300',
                      link.active 
                        ? 'bg-gradient-to-r from-violet-600 to-purple-600 text-white shadow-lg shadow-violet-500/25' 
                        : 'bg-white/10 backdrop-blur-xl text-slate-300 hover:bg-white/20 hover:text-white border border-white/20 hover:border-violet-400/30',
                      !link.url ? 'opacity-50 cursor-not-allowed' : 'hover:scale-105'
                    ]"
                    v-html="link.label"
                  />
                </div>
              </nav>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { reactive, computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { 
  PlusIcon, 
  FolderIcon, 
  EyeIcon, 
  EyeSlashIcon,
  PencilIcon, 
  TrashIcon,
  EllipsisVerticalIcon
} from '@heroicons/vue/24/outline'

import AppLayout from '@/Layouts/AppLayout.vue'
import Dropdown from '@/Components/Dropdown.vue'
import DropdownLink from '@/Components/DropdownLink.vue'

const props = defineProps({
  collections: { type: Object, default: () => ({ data: [], links: [], meta: {} }) },
  filters: { type: Object, default: () => ({}) },
  canCreate: { type: Boolean, default: false },
  isMyCollections: { type: Boolean, default: false },
})

// Reactive state
const searchForm = reactive({
  search: props.filters.search || '',
  privacy: props.filters.privacy || '',
  sort: props.filters.sort || 'updated_at',
  direction: props.filters.direction || 'desc',
})

// Computed properties
const hasFilters = computed(() => {
  return Object.entries(searchForm).some(([key, value]) => 
    value && value !== '' && !['sort', 'direction'].includes(key)
  )
})

// Methods
const search = () => {
  const cleanForm = Object.fromEntries(
    Object.entries(searchForm).filter(([key, value]) => value !== '')
  )
  
  const routeName = props.isMyCollections ? '/my/collections' : '/collections'
  
  router.get(routeName, cleanForm, {
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
  if (!date) return 'Unknown'
  try {
    return new Date(date).toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
    })
  } catch (e) {
    return 'Invalid date'
  }
}

const deleteCollection = (collection) => {
  if (confirm(`Are you sure you want to delete "${collection.title}"? This action cannot be undone.`)) {
    router.delete(`/collections/${collection.slug}`, {
      onSuccess: () => {
        // Success handled by redirect
      },
      onError: (errors) => {
        console.error('Delete failed:', errors)
        alert('Failed to delete collection. Please try again.')
      }
    })
  }
}

const togglePublishStatus = (collection) => {
  const action = collection.is_published ? 'unpublish' : 'publish'
  const message = `Are you sure you want to ${action} "${collection.title}"?`
  
  if (confirm(message)) {
    router.post(`/collections/${collection.slug}/toggle-publish`, {}, {
      onSuccess: () => {
        // Success handled by page refresh
      },
      onError: (errors) => {
        console.error('Toggle publish failed:', errors)
        alert(`Failed to ${action} collection. Please try again.`)
      }
    })
  }
}

const getEmptyMessage = () => {
  if (hasFilters.value) {
    return 'No collections found matching your criteria.'
  }
  
  if (props.isMyCollections) {
    return 'You haven\'t created any collections yet.'
  }
  
  return 'No collections available yet.'
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

/* Custom select styling */
select {
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
  background-position: right 12px center;
  background-repeat: no-repeat;
  background-size: 16px 12px;
  padding-right: 40px;
}

select option {
  background-color: #1e293b;
  color: #e2e8f0;
}

/* Line clamp utility */
</style>