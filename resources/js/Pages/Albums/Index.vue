<template>
  <AppLayout>
    <Head title="Albums" />

    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Albums
          </h2>
          <p class="text-sm text-gray-600 mt-1">
            Browse and organize photo collections
          </p>
        </div>
        <div class="flex items-center space-x-4">
          <Link
            v-if="canCreate"
            :href="route('albums.create')"
            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700"
          >
            <PlusIcon class="h-4 w-4 mr-2" />
            Create Album
          </Link>
        </div>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Filters -->
        <div class="bg-white shadow-sm rounded-lg mb-6">
          <div class="p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
              <!-- Search -->
              <div class="flex-1 max-w-lg">
                <div class="relative">
                  <MagnifyingGlassIcon class="absolute left-3 top-3 h-4 w-4 text-gray-400" />
                  <input
                    v-model="searchForm.search"
                    @input="debounceSearch"
                    type="text"
                    placeholder="Search albums..."
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                  />
                </div>
              </div>

              <!-- Filters -->
              <div class="flex items-center space-x-4">
                <select
                  v-model="searchForm.owner"
                  @change="search"
                  class="border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                >
                  <option value="">All Albums</option>
                  <option value="mine" v-if="$page.props.auth.user">My Albums</option>
                </select>

                <select
                  v-model="searchForm.sort"
                  @change="search"
                  class="border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                >
                  <option value="updated_at">Recently Updated</option>
                  <option value="created_at">Recently Created</option>
                  <option value="title">Title</option>
                  <option value="image_count">Image Count</option>
                </select>
              </div>
            </div>
          </div>
        </div>

        <!-- Albums Grid -->
        <div v-if="albums.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <AlbumCard
            v-for="album in albums.data"
            :key="album.id"
            :album="album"
            :show-actions="true"
          />
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-12">
          <FolderIcon class="mx-auto h-12 w-12 text-gray-400" />
          <h3 class="mt-2 text-sm font-medium text-gray-900">
            {{ searchForm.search || searchForm.owner ? 'No albums found' : 'No albums yet' }}
          </h3>
          <p class="mt-1 text-sm text-gray-500">
            {{ searchForm.search || searchForm.owner 
                ? 'Try adjusting your search criteria.' 
                : 'Get started by creating your first album.' }}
          </p>
          <div class="mt-6" v-if="canCreate && !searchForm.search && !searchForm.owner">
            <Link
              :href="route('albums.create')"
              class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700"
            >
              <PlusIcon class="-ml-1 mr-2 h-5 w-5" />
              Create Album
            </Link>
          </div>
        </div>

        <!-- Pagination -->
        <Pagination
          v-if="albums.data.length > 0"
          :links="albums.links"
          :meta="albums.meta"
          class="mt-6"
        />
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import {
  PlusIcon,
  FolderIcon,
  MagnifyingGlassIcon,
} from '@heroicons/vue/24/outline'

import AppLayout from '@/Layouts/AppLayout.vue'
import AlbumCard from '@/Components/Gallery/AlbumCard.vue'
import Pagination from '@/Components/Pagination.vue'

const props = defineProps({
  albums: Object,
  filters: {
    type: Object,
    default: () => ({}),
  },
  canCreate: {
    type: Boolean,
    default: false,
  },
})

const searchForm = reactive({
  search: props.filters.search || '',
  owner: props.filters.owner || '',
  sort: props.filters.sort || 'updated_at',
})

let searchTimeout = null

const debounceSearch = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    search()
  }, 300)
}

const search = () => {
  router.get(route('albums.index'), searchForm, {
    preserveState: true,
    replace: true,
  })
}
</script>
