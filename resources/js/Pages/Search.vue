<template>
  <AppLayout>
    <Head title="Search" />

    <!-- Enhanced Animated Background -->
    <div class="fixed inset-0 overflow-hidden bg-slate-900">
      <!-- Dynamic SVG Mesh Background -->
      <svg class="absolute inset-0 w-full h-full opacity-20" viewBox="0 0 100 100" preserveAspectRatio="none">
        <defs>
          <linearGradient id="mesh-gradient-search" x1="0%" y1="0%" x2="100%" y2="100%" gradientTransform="rotate(25)">
            <stop offset="0%" style="stop-color:#8b5cf6;stop-opacity:0.4">
              <animate attributeName="stop-color" 
                values="#8b5cf6;#06b6d4;#a855f7;#8b5cf6" 
                dur="8s" 
                repeatCount="indefinite" />
            </stop>
            <stop offset="50%" style="stop-color:#06b6d4;stop-opacity:0.3">
              <animate attributeName="stop-color" 
                values="#06b6d4;#a855f7;#8b5cf6;#06b6d4" 
                dur="8s" 
                repeatCount="indefinite" />
            </stop>
            <stop offset="100%" style="stop-color:#a855f7;stop-opacity:0.4">
              <animate attributeName="stop-color" 
                values="#a855f7;#8b5cf6;#06b6d4;#a855f7" 
                dur="8s" 
                repeatCount="indefinite" />
            </stop>
          </linearGradient>
        </defs>
        <path d="M0,15 Q40,5 80,15 L100,0 L0,0 Z" fill="url(#mesh-gradient-search)" opacity="0.7">
          <animateTransform
            attributeName="transform"
            type="translate"
            values="0,0;10,8;0,0"
            dur="15s"
            repeatCount="indefinite"/>
        </path>
        <path d="M0,85 Q60,75 100,85 L100,100 L0,100 Z" fill="url(#mesh-gradient-search)" opacity="0.5">
          <animateTransform
            attributeName="transform"
            type="translate"
            values="0,0;-8,-5;0,0"
            dur="12s"
            repeatCount="indefinite"/>
        </path>
      </svg>
      
      <!-- Enhanced Floating Orbs -->
      <div class="absolute top-24 right-20 w-96 h-96 bg-gradient-to-r from-violet-500/10 to-cyan-500/10 rounded-full blur-3xl animate-pulse-slow"></div>
      <div class="absolute top-60 left-20 w-80 h-80 bg-gradient-to-l from-purple-500/8 to-violet-500/8 rounded-full blur-3xl animate-pulse-slow delay-1000"></div>
      <div class="absolute bottom-40 right-1/3 w-72 h-72 bg-gradient-to-t from-cyan-500/12 to-purple-500/12 rounded-full blur-3xl animate-pulse-slow delay-700"></div>
      
      <!-- Floating Particle System -->
      <div class="absolute inset-0">
        <div v-for="i in 20" :key="i" 
             :class="`absolute rounded-full animate-float-search`"
             :style="{
               left: Math.random() * 100 + '%',
               top: Math.random() * 100 + '%',
               width: (1 + Math.random() * 3) + 'px',
               height: (1 + Math.random() * 3) + 'px',
               animationDelay: Math.random() * 8 + 's',
               animationDuration: (4 + Math.random() * 6) + 's'
             }">
          <div class="w-full h-full bg-gradient-to-r from-cyan-400/40 to-violet-400/40 rounded-full animate-pulse"></div>
        </div>
      </div>
    </div>

    <template #header>
      <div class="relative z-10 flex items-center justify-between">
        <div class="flex-1 max-w-2xl">
          <h2 class="font-semibold text-xl text-white leading-tight mb-6">
            <span class="bg-gradient-to-r from-cyan-400 via-white to-violet-400 bg-clip-text text-transparent animate-shimmer bg-size-200">
              Search Results
            </span>
          </h2>
          <div class="relative group">
            <MagnifyingGlassIcon class="absolute left-4 top-4 h-5 w-5 text-slate-400 group-focus-within:text-cyan-400 transition-colors duration-300" />
            <input
              v-model="searchQuery"
              @keydown.enter="performSearch"
              type="text"
              placeholder="Search images, albums, collections..."
              class="w-full pl-12 pr-4 py-4 bg-white/10 backdrop-blur-xl border border-white/20 rounded-xl text-white placeholder-slate-400 focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500/50 focus:bg-white/15 transition-all duration-300 hover:bg-white/12"
            />
            <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-violet-500/20 via-transparent to-cyan-500/20 opacity-0 group-focus-within:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
          </div>
        </div>
      </div>
    </template>

    <div class="relative z-10 py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Search Results -->
        <div v-if="query" class="space-y-12">
          <!-- Results Summary -->
          <div class="text-center animate-fade-in-up">
            <div class="bg-white/5 backdrop-blur-xl border border-white/20 rounded-2xl p-8 max-w-2xl mx-auto">
              <h3 class="text-3xl font-bold bg-gradient-to-r from-violet-400 to-cyan-400 bg-clip-text text-transparent">
                {{ getTotalResults() }} result{{ getTotalResults() !== 1 ? 's' : '' }} for "{{ query }}"
              </h3>
            </div>
          </div>

          <!-- Images -->
          <div v-if="results.images && results.images.length > 0" class="animate-fade-in-up delay-200">
            <div class="bg-white/5 backdrop-blur-xl border border-white/20 rounded-2xl p-8">
              <div class="flex items-center justify-between mb-8">
                <h4 class="text-xl font-semibold text-white flex items-center">
                  <div class="w-8 h-8 bg-gradient-to-r from-violet-500 to-purple-500 rounded-lg mr-3 flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                  </div>
                  Images ({{ results.images.length }})
                </h4>
                <Link
                  :href="route('gallery.index', { search: query })"
                  class="bg-gradient-to-r from-cyan-600 to-violet-600 hover:from-cyan-500 hover:to-violet-500 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 hover:scale-105 hover:shadow-lg hover:shadow-cyan-500/25"
                >
                  View all images →
                </Link>
              </div>
              
              <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                <div
                  v-for="(image, index) in results.images"
                  :key="image.id"
                  class="aspect-square bg-white/5 backdrop-blur-sm rounded-xl overflow-hidden hover:shadow-2xl hover:shadow-violet-500/20 transition-all duration-500 hover:scale-105 group"
                  :style="{animationDelay: index * 50 + 'ms'}"
                >
                  <Link :href="route('images.show', image.slug)">
                    <div class="relative w-full h-full">
                      <img
                        :src="getImageUrl(image, 'small')"
                        :alt="image.alt_text || image.title"
                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
                      >
                      <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </div>
                  </Link>
                </div>
              </div>
            </div>
          </div>

          <!-- Albums -->
          <div v-if="results.albums && results.albums.length > 0" class="animate-fade-in-up delay-400">
            <div class="bg-white/5 backdrop-blur-xl border border-white/20 rounded-2xl p-8">
              <div class="flex items-center justify-between mb-8">
                <h4 class="text-xl font-semibold text-white flex items-center">
                  <div class="w-8 h-8 bg-gradient-to-r from-cyan-500 to-purple-500 rounded-lg mr-3 flex items-center justify-center">
                    <FolderIcon class="w-4 h-4 text-white" />
                  </div>
                  Albums ({{ results.albums.length }})
                </h4>
                <Link
                  :href="route('albums.index', { search: query })"
                  class="bg-gradient-to-r from-purple-600 to-cyan-600 hover:from-purple-500 hover:to-cyan-500 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 hover:scale-105 hover:shadow-lg hover:shadow-purple-500/25"
                >
                  View all albums →
                </Link>
              </div>
              
              <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <div
                  v-for="(album, index) in results.albums"
                  :key="album.id"
                  class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-xl shadow-lg hover:shadow-2xl hover:shadow-cyan-500/20 transition-all duration-500 overflow-hidden group hover:scale-105"
                  :style="{animationDelay: index * 100 + 'ms'}"
                >
                  <div class="aspect-w-16 aspect-h-9 bg-white/5">
                    <img
                      v-if="album.cover_image"
                      :src="getImageUrl(album.cover_image, 'medium')"
                      :alt="album.title"
                      class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
                    >
                    <div v-else class="flex items-center justify-center">
                      <FolderIcon class="h-16 w-16 text-slate-400" />
                    </div>
                  </div>
                  <div class="p-6">
                    <h5 class="text-lg font-medium text-white truncate mb-2">
                      {{ album.title }}
                    </h5>
                    <p class="text-sm text-slate-300 line-clamp-2 mb-4">
                      {{ album.description }}
                    </p>
                    <div class="flex items-center justify-between">
                      <span class="text-sm text-slate-400">
                        {{ album.images_count }} image{{ album.images_count !== 1 ? 's' : '' }}
                      </span>
                      <Link
                        :href="route('albums.show', album.slug)"
                        class="text-sm font-medium bg-gradient-to-r from-cyan-400 to-violet-400 bg-clip-text text-transparent hover:from-white hover:to-white transition-all duration-300"
                      >
                        View Album
                      </Link>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Collections -->
          <div v-if="results.collections && results.collections.length > 0" class="animate-fade-in-up delay-600">
            <div class="bg-white/5 backdrop-blur-xl border border-white/20 rounded-2xl p-8">
              <div class="flex items-center justify-between mb-8">
                <h4 class="text-xl font-semibold text-white flex items-center">
                  <div class="w-8 h-8 bg-gradient-to-r from-purple-500 to-violet-500 rounded-lg mr-3 flex items-center justify-center">
                    <RectangleStackIcon class="w-4 h-4 text-white" />
                  </div>
                  Collections ({{ results.collections.length }})
                </h4>
                <Link
                  :href="route('collections.index', { search: query })"
                  class="bg-gradient-to-r from-violet-600 to-purple-600 hover:from-violet-500 hover:to-purple-500 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 hover:scale-105 hover:shadow-lg hover:shadow-violet-500/25"
                >
                  View all collections →
                </Link>
              </div>
              
              <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <div
                  v-for="(collection, index) in results.collections"
                  :key="collection.id"
                  class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-xl shadow-lg hover:shadow-2xl hover:shadow-purple-500/20 transition-all duration-500 overflow-hidden group hover:scale-105"
                  :style="{animationDelay: index * 100 + 'ms'}"
                >
                  <div class="aspect-w-16 aspect-h-9 bg-white/5">
                    <img
                      v-if="collection.cover_image"
                      :src="getImageUrl(collection.cover_image, 'medium')"
                      :alt="collection.title"
                      class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
                    >
                    <div v-else class="flex items-center justify-center">
                      <RectangleStackIcon class="h-16 w-16 text-slate-400" />
                    </div>
                  </div>
                  <div class="p-6">
                    <h5 class="text-lg font-medium text-white truncate mb-2">
                      {{ collection.title }}
                    </h5>
                    <p class="text-sm text-slate-300 line-clamp-2 mb-4">
                      {{ collection.description }}
                    </p>
                    <div class="flex items-center justify-between">
                      <span class="text-sm text-slate-400">
                        {{ collection.items_count }} item{{ collection.items_count !== 1 ? 's' : '' }}
                      </span>
                      <Link
                        :href="route('collections.show', collection.slug)"
                        class="text-sm font-medium bg-gradient-to-r from-violet-400 to-purple-400 bg-clip-text text-transparent hover:from-white hover:to-white transition-all duration-300"
                      >
                        View Collection
                      </Link>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- No Results -->
          <div v-if="getTotalResults() === 0" class="text-center py-16 animate-fade-in-up">
            <div class="bg-white/5 backdrop-blur-xl border border-white/20 rounded-2xl p-12 max-w-2xl mx-auto">
              <div class="w-20 h-20 bg-gradient-to-r from-violet-500/20 to-cyan-500/20 rounded-full flex items-center justify-center mx-auto mb-6">
                <MagnifyingGlassIcon class="h-10 w-10 text-slate-400" />
              </div>
              <h3 class="text-xl font-medium text-white mb-3">No results found</h3>
              <p class="text-slate-300 mb-8">
                Try different keywords or browse our gallery instead.
              </p>
              <Link
                :href="route('gallery.index')"
                class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-cyan-600 to-violet-600 hover:from-cyan-500 hover:to-violet-500 text-white font-medium rounded-xl transition-all duration-300 hover:scale-105 hover:shadow-lg hover:shadow-cyan-500/25"
              >
                Browse Gallery
              </Link>
            </div>
          </div>
        </div>

        <!-- Search Suggestions -->
        <div v-else class="text-center py-16 animate-fade-in-up">
          <div class="bg-white/5 backdrop-blur-xl border border-white/20 rounded-2xl p-12 max-w-2xl mx-auto">
            <div class="w-20 h-20 bg-gradient-to-r from-cyan-500/20 to-violet-500/20 rounded-full flex items-center justify-center mx-auto mb-6">
              <MagnifyingGlassIcon class="h-10 w-10 text-slate-400 animate-pulse" />
            </div>
            <h3 class="text-xl font-medium text-white mb-3">Start your search</h3>
            <p class="text-slate-300 mb-8">
              Enter keywords to search for images, albums, and collections.
            </p>
            
            <!-- Popular searches -->
            <div>
              <h4 class="text-sm font-medium text-slate-300 mb-4">Popular searches:</h4>
              <div class="flex flex-wrap justify-center gap-3">
                <button
                  v-for="(term, index) in popularSearches"
                  :key="term"
                  @click="searchFor(term)"
                  class="inline-flex items-center px-4 py-2 rounded-full text-sm bg-white/10 backdrop-blur-sm border border-white/20 text-slate-300 hover:bg-white/20 hover:text-white hover:scale-105 transition-all duration-300 hover:shadow-lg hover:shadow-violet-500/25"
                  :style="{animationDelay: index * 50 + 'ms'}"
                >
                  {{ term }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import route from 'ziggy-js'
import {
  MagnifyingGlassIcon,
  FolderIcon,
  RectangleStackIcon,
} from '@heroicons/vue/24/outline'

import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  query: {
    type: String,
    default: '',
  },
  results: {
    type: Object,
    default: () => ({
      images: [],
      albums: [],
      collections: [],
    }),
  },
})

const searchQuery = ref(props.query)

const popularSearches = [
  'landscape', 'portrait', 'nature', 'architecture', 'street',
  'black and white', 'vintage', 'travel', 'food', 'art'
]

const getTotalResults = () => {
  return (props.results.images?.length || 0) +
         (props.results.albums?.length || 0) +
         (props.results.collections?.length || 0)
}

const getImageUrl = (image, variant = 'medium') => {
  if (image.versions) {
    const version = image.versions.find(v => v.variant === variant)
    if (version) return version.url
  }
  return image.url || image.storage_path
}

const performSearch = () => {
  if (searchQuery.value.trim()) {
    router.visit(route('search', { q: searchQuery.value }))
  }
}

const searchFor = (term) => {
  searchQuery.value = term
  performSearch()
}
</script>

<style scoped>
/* Enhanced Animations */
@keyframes float-search {
  0%, 100% {
    transform: translateY(0px) translateX(0px) rotate(0deg);
    opacity: 0.3;
  }
  25% {
    transform: translateY(-15px) translateX(10px) rotate(90deg);
    opacity: 0.7;
  }
  50% {
    transform: translateY(-8px) translateX(-6px) rotate(180deg);
    opacity: 1;
  }
  75% {
    transform: translateY(-20px) translateX(-12px) rotate(270deg);
    opacity: 0.5;
  }
}

.animate-float-search {
  animation: float-search 10s ease-in-out infinite;
}

@keyframes shimmer {
  0% { background-position: -200% 50%; }
  100% { background-position: 200% 50%; }
}
.animate-shimmer {
  animation: shimmer 3s ease-in-out infinite;
}

@keyframes fade-in-up {
  0% {
    opacity: 0;
    transform: translateY(30px);
  }
  100% {
    opacity: 1;
    transform: translateY(0);
  }
}
.animate-fade-in-up {
  animation: fade-in-up 0.6s ease-out both;
}

.delay-200 { animation-delay: 0.2s; }
.delay-400 { animation-delay: 0.4s; }
.delay-600 { animation-delay: 0.6s; }

@keyframes pulse-slow {
  0%, 100% { 
    opacity: 0.3; 
    transform: scale(1);
  }
  50% { 
    opacity: 0.8; 
    transform: scale(1.05);
  }
}
.animate-pulse-slow {
  animation: pulse-slow 4s ease-in-out infinite;
}

.bg-size-200 {
  background-size: 200% 200%;
}

/* Custom Grid Animation */
.grid > * {
  animation: fade-in-up 0.6s ease-out both;
}

/* Aspect Ratio Fix */
.aspect-w-16 {
  position: relative;
  padding-bottom: 56.25%; /* 16:9 aspect ratio */
}
.aspect-w-16 > * {
  position: absolute;
  height: 100%;
  width: 100%;
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
}

/* Line clamp utility */
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Enhanced focus states */
.group:focus-within .group-focus-within\:text-cyan-400 {
  color: rgb(6 182 212);
}

/* Backdrop blur enhancement */
.backdrop-blur-xl {
  backdrop-filter: blur(24px);
  -webkit-backdrop-filter: blur(24px);
}
</style>