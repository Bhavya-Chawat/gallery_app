<template>
  <AuthenticatedLayout>
    <Head title="Collections" />

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
          <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Collections</h2>
            <p class="text-gray-600 mt-1">Curated image collections</p>
          </div>
          
          <Link
            v-if="canCreate"
            :href="route('collections.create')"
            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700"
          >
            <PlusIcon class="h-4 w-4 mr-2" />
            New Collection
          </Link>
        </div>

        <!-- Search & Filters -->
        <div class="bg-white rounded-lg shadow p-4 mb-6">
          <div class="flex space-x-4">
            <div class="flex-1">
              <input
                v-model="searchForm.search"
                @keyup.enter="search"
                type="text"
                placeholder="Search collections..."
                class="w-full border-gray-300 rounded-md focus:border-blue-500 focus:ring-blue-500"
              />
            </div>
            <select
              v-model="searchForm.curator"
              @change="search"
              class="border-gray-300 rounded-md focus:border-blue-500 focus:ring-blue-500"
            >
              <option value="">All Collections</option>
              <option value="mine">My Collections</option>
            </select>
          </div>
        </div>

        <!-- Collections Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="collection in collections.data"
            :key="collection.id"
            class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow"
          >
            <!-- Collection Cover -->
            <Link :href="route('collections.show', collection.id)">
              <div class="aspect-video bg-gray-200 relative">
                <img
                  v-if="collection.cover_image"
                  :src="collection.cover_image.url"
                  :alt="collection.title"
                  class="w-full h-full object-cover"
                />
                <div v-else class="flex items-center justify-center h-full">
                  <FolderIcon class="h-16 w-16 text-gray-400" />
                </div>
                
                <!-- Item Count Badge -->
                <div class="absolute bottom-2 right-2 bg-black bg-opacity-75 text-white text-xs px-2 py-1 rounded">
                  {{ collection.item_count }} items
                </div>
              </div>
            </Link>

            <!-- Collection Info -->
            <div class="p-4">
              <h3 class="font-semibold text-lg text-gray-900 mb-1">
                <Link :href="route('collections.show', collection.id)">
                  {{ collection.title }}
                </Link>
              </h3>
              
              <p v-if="collection.description" class="text-gray-600 text-sm mb-2 line-clamp-2">
                {{ collection.description }}
              </p>
              
              <div class="flex items-center justify-between text-sm text-gray-500">
                <span>by {{ collection.curator.name }}</span>
                <span>{{ formatDate(collection.created_at) }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="collections.links" class="mt-8">
          <Pagination :links="collections.links" />
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { reactive } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { PlusIcon, FolderIcon } from '@heroicons/vue/24/outline'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Pagination from '@/Components/Pagination.vue'

const props = defineProps({
  collections: Object,
  filters: Object,
  canCreate: Boolean,
})

const searchForm = reactive({
  search: props.filters.search || '',
  curator: props.filters.curator || '',
})

const search = () => {
  router.get(route('collections.index'), searchForm, {
    preserveState: true,
    replace: true,
  })
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString()
}
</script>
