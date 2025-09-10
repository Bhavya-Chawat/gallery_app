<template>
  <AppLayout>
    <Head title="Collections" />

    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Collections
          </h2>
          <p class="text-sm text-gray-600 mt-1">
            Curated galleries and themed collections
          </p>
        </div>
        <div class="flex items-center space-x-4">
          <Link
            v-if="canCreate"
            :href="route('collections.create')"
            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700"
          >
            <PlusIcon class="h-4 w-4 mr-2" />
            Create Collection
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
                    placeholder="Search collections..."
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                  />
                </div>
              </div>

              <!-- Filters -->
              <div class="flex items-center space-x-4">
                <select
                  v-model="searchForm.curator"
                  @change="search"
                  class="border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                >
                  <option value="">All Collections</option>
                  <option value="mine" v-if="$page.props.auth.user">My Collections</option>
                </select>
              </div>
            </div>
          </div>
        </div>

        <!-- Collections Grid -->
        <div v-if="collections.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="collection in collections.data"
            :key="collection.id"
            class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow overflow-hidden"
          >
            <!-- Cover Image -->
            <div class="aspect-w-16 aspect-h-9 bg-gray-100">
              <img
                v-if="collection.cover_image"
                :src="getImageUrl(collection.cover_image, 'medium')"
                :alt="collection.title"
                class="w-full h-full object-cover"
              >
              <div v-else class="flex items-center justify-center">
                <RectangleStackIcon class="h-16 w-16 text-gray-400" />
              </div>
            </div>

            <!-- Content -->
            <div class="p-6">
              <div class="flex items-start justify-between">
                <div class="flex-1 min-w-0">
                  <Link
                    :href="route('collections.show', collection.slug)"
                    class="text-lg font-semibold text-gray-900 hover:text-blue-600"
                  >
                    {{ collection.title }}
                  </Link>
                  <p class="mt-1 text-sm text-gray-500 line-clamp-2">
                    {{ collection.description }}
                  </p>
                </div>
              </div>

              <!-- Curator -->
              <div class="mt-4 flex items-center">
                <UserAvatar :user="collection.curator" size="xs" />
                <span class="ml-2 text-sm text-gray-600">
                  by {{ collection.curator.name }}
                </span>
              </div>

              <!-- Stats -->
              <div class="mt-4 flex items-center justify-between text-sm text-gray-500">
                <span>
                  {{ collection.items_count }} item{{ collection.items_count !== 1 ? 's' : '' }}
                </span>
                <span>
                  {{ formatDate(collection.updated_at) }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-12">
          <RectangleStackIcon class="mx-auto h-12 w-12 text-gray-400" />
          <h3 class="mt-2 text-sm font-medium text-gray-900">
            {{ searchForm.search || searchForm.curator ? 'No collections found' : 'No collections yet' }}
          </h3>
          <p class="mt-1 text-sm text-gray-500">
            {{ searchForm.search || searchForm.curator 
                ? 'Try adjusting your search criteria.' 
                : 'Get started by creating your first collection.' }}
          </p>
          <div class="mt-6" v-if="canCreate && !searchForm.search && !searchForm.curator">
            <Link
              :href="route('collections.create')"
              class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700"
            >
              <PlusIcon class="-ml-1 mr-2 h-5 w-5" />
              Create Collection
            </Link>
          </div>
        </div>

        <!-- Pagination -->
        <Pagination
          v-if="collections.data.length > 0"
          :links="collections.links"
          :meta="collections.meta"
          class="mt-6"
        />
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { reactive } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import route from 'ziggy-js'
import {
  PlusIcon,
  RectangleStackIcon,
  MagnifyingGlassIcon,
} from '@heroicons/vue/24/outline'

import AppLayout from '@/Layouts/AppLayout.vue'
import UserAvatar from '@/Components/UserAvatar.vue'
import Pagination from '@/Components/Pagination.vue'

const props = defineProps({
  collections: Object,
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
  curator: props.filters.curator || '',
})

let searchTimeout = null

const debounceSearch = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    search()
  }, 300)
}

const search = () => {
  router.get(route('collections.index'), searchForm, {
    preserveState: true,
    replace: true,
  })
}

const getImageUrl = (image, variant = 'medium') => {
  if (image.versions) {
    const version = image.versions.find(v => v.variant === variant)
    if (version) return version.url
  }
  return image.url || image.storage_path
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString()
}
</script>
