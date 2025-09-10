<template>
  <AppLayout>
    <Head title="Search" />

    <template #header>
      <div class="flex items-center justify-between">
        <div class="flex-1 max-w-2xl">
          <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-4">
            Search Results
          </h2>
          <div class="relative">
            <MagnifyingGlassIcon class="absolute left-3 top-3 h-5 w-5 text-gray-400" />
            <input
              v-model="searchQuery"
              @keydown.enter="performSearch"
              type="text"
              placeholder="Search images, albums, collections..."
              class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
            />
          </div>
        </div>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Search Results -->
        <div v-if="query" class="space-y-8">
          <!-- Results Summary -->
          <div class="text-center">
            <h3 class="text-2xl font-bold text-gray-900">
              {{ getTotalResults() }} result{{ getTotalResults() !== 1 ? 's' : '' }} for "{{ query }}"
            </h3>
          </div>

          <!-- Images -->
          <div v-if="results.images && results.images.length > 0">
            <div class="flex items-center justify-between mb-6">
              <h4 class="text-lg font-semibold text-gray-900">
                Images ({{ results.images.length }})
              </h4>
              <Link
                :href="route('gallery.index', { search: query })"
                class="text-blue-600 hover:text-blue-500 text-sm font-medium"
              >
                View all images →
              </Link>
            </div>
            
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
              <div
                v-for="image in results.images"
                :key="image.id"
                class="aspect-square bg-gray-100 rounded-lg overflow-hidden hover:shadow-lg transition-shadow"
              >
                <Link :href="route('images.show', image.slug)">
                  <img
                    :src="getImageUrl(image, 'small')"
                    :alt="image.alt_text || image.title"
                    class="w-full h-full object-cover hover:scale-105 transition-transform"
                  >
                </Link>
              </div>
            </div>
          </div>

          <!-- Albums -->
          <div v-if="results.albums && results.albums.length > 0">
            <div class="flex items-center justify-between mb-6">
              <h4 class="text-lg font-semibold text-gray-900">
                Albums ({{ results.albums.length }})
              </h4>
              <Link
                :href="route('albums.index', { search: query })"
                class="text-blue-600 hover:text-blue-500 text-sm font-medium"
              >
                View all albums →
              </Link>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
              <div
                v-for="album in results.albums"
                :key="album.id"
                class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow overflow-hidden"
              >
                <div class="aspect-w-16 aspect-h-9 bg-gray-100">
                  <img
                    v-if="album.cover_image"
                    :src="getImageUrl(album.cover_image, 'medium')"
                    :alt="album.title"
                    class="w-full h-full object-cover"
                  >
                  <div v-else class="flex items-center justify-center">
                    <FolderIcon class="h-12 w-12 text-gray-400" />
                  </div>
                </div>
                <div class="p-4">
                  <h5 class="text-lg font-medium text-gray-900 truncate">
                    {{ album.title }}
                  </h5>
                  <p class="mt-1 text-sm text-gray-500 line-clamp-2">
                    {{ album.description }}
                  </p>
                  <div class="mt-3 flex items-center justify-between">
                    <span class="text-sm text-gray-500">
                      {{ album.images_count }} image{{ album.images_count !== 1 ? 's' : '' }}
                    </span>
                    <Link
                      :href="route('albums.show', album.slug)"
                      class="text-sm font-medium text-blue-600 hover:text-blue-500"
                    >
                      View Album
                    </Link>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Collections -->
          <div v-if="results.collections && results.collections.length > 0">
            <div class="flex items-center justify-between mb-6">
              <h4 class="text-lg font-semibold text-gray-900">
                Collections ({{ results.collections.length }})
              </h4>
              <Link
                :href="route('collections.index', { search: query })"
                class="text-blue-600 hover:text-blue-500 text-sm font-medium"
              >
                View all collections →
              </Link>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
              <div
                v-for="collection in results.collections"
                :key="collection.id"
                class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow overflow-hidden"
              >
                <div class="aspect-w-16 aspect-h-9 bg-gray-100">
                  <img
                    v-if="collection.cover_image"
                    :src="getImageUrl(collection.cover_image, 'medium')"
                    :alt="collection.title"
                    class="w-full h-full object-cover"
                  >
                  <div v-else class="flex items-center justify-center">
                    <RectangleStackIcon class="h-12 w-12 text-gray-400" />
                  </div>
                </div>
                <div class="p-4">
                  <h5 class="text-lg font-medium text-gray-900 truncate">
                    {{ collection.title }}
                  </h5>
                  <p class="mt-1 text-sm text-gray-500 line-clamp-2">
                    {{ collection.description }}
                  </p>
                  <div class="mt-3 flex items-center justify-between">
                    <span class="text-sm text-gray-500">
                      {{ collection.items_count }} item{{ collection.items_count !== 1 ? 's' : '' }}
                    </span>
                    <Link
                      :href="route('collections.show', collection.slug)"
                      class="text-sm font-medium text-blue-600 hover:text-blue-500"
                    >
                      View Collection
                    </Link>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- No Results -->
          <div v-if="getTotalResults() === 0" class="text-center py-12">
            <MagnifyingGlassIcon class="mx-auto h-12 w-12 text-gray-400" />
            <h3 class="mt-2 text-sm font-medium text-gray-900">No results found</h3>
            <p class="mt-1 text-sm text-gray-500">
              Try different keywords or browse our gallery instead.
            </p>
            <div class="mt-6">
              <Link
                :href="route('gallery.index')"
                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700"
              >
                Browse Gallery
              </Link>
            </div>
          </div>
        </div>

        <!-- Search Suggestions -->
        <div v-else class="text-center py-12">
          <MagnifyingGlassIcon class="mx-auto h-12 w-12 text-gray-400" />
          <h3 class="mt-2 text-sm font-medium text-gray-900">Start your search</h3>
          <p class="mt-1 text-sm text-gray-500">
            Enter keywords to search for images, albums, and collections.
          </p>
          
          <!-- Popular searches -->
          <div class="mt-8">
            <h4 class="text-sm font-medium text-gray-700 mb-3">Popular searches:</h4>
            <div class="flex flex-wrap justify-center gap-2">
              <button
                v-for="term in popularSearches"
                :key="term"
                @click="searchFor(term)"
                class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-gray-100 text-gray-700 hover:bg-gray-200"
              >
                {{ term }}
              </button>
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
import { route } from 'ziggy-js'
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
