<template>
  <AppLayout>
    <Head title="Collections" />

    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ isMyCollections ? 'My Collections' : 'Collections' }}
          </h2>
          <p class="text-sm text-gray-600 mt-1">
            {{ isMyCollections ? 'Manage your curated image collections' : 'Browse curated image collections' }}
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
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
              <!-- Search -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <input
                  v-model="searchForm.search"
                  @keyup.enter="search"
                  type="text"
                  :placeholder="isMyCollections ? 'Search your collections...' : 'Search collections...'"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                />
              </div>

              <!-- Privacy Filter (My Collections only) -->
              <div v-if="isMyCollections">
                <label class="block text-sm font-medium text-gray-700 mb-2">Privacy</label>
                <select
                  v-model="searchForm.privacy"
                  @change="search"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                >
                  <option value="">All Privacy Levels</option>
                  <option value="public">Public</option>
                  <option value="unlisted">Unlisted</option>
                  <option value="private">Private</option>
                </select>
              </div>

              <!-- Sort -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Sort by</label>
                <select
                  v-model="searchForm.sort"
                  @change="search"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                >
                  <option value="updated_at">Recently Updated</option>
                  <option value="created_at">Recently Created</option>
                  <option value="title">Title</option>
                  <option value="images_count">Image Count</option>
                </select>
              </div>

              <!-- Clear Filters -->
              <div class="flex items-end">
                <button
                  @click="clearFilters"
                  class="w-full px-4 py-2 text-sm text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50"
                >
                  Clear Filters
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Collections Grid -->
        <div class="bg-white shadow-sm rounded-lg">
          <div class="p-6">
            <!-- View Controls -->
            <div class="flex items-center justify-between mb-6">
              <div class="flex items-center space-x-4">
                <span class="text-sm text-gray-500">
                  {{ collections.data?.length || 0 }} collection{{ ((collections.data?.length || 0) !== 1) ? 's' : '' }}
                </span>
                
                <select
                  v-model="searchForm.direction"
                  @change="search"
                  class="text-sm border border-gray-300 rounded-md px-3 py-1 focus:ring-blue-500 focus:border-blue-500"
                >
                  <option value="desc">Descending</option>
                  <option value="asc">Ascending</option>
                </select>
              </div>
            </div>

            <!-- Collections Grid -->
            <div v-if="collections.data && collections.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              <div
                v-for="collection in collections.data"
                :key="collection.id"
                class="relative bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow border border-gray-200"
              >
                <!-- Cover Image -->
                <div class="aspect-video bg-gray-200 relative">
                  <img
                    v-if="collection.cover_image"
                    :src="getImageUrl(collection.cover_image)"
                    :alt="collection.title"
                    class="w-full h-full object-cover"
                  />
                  <div v-else class="flex items-center justify-center h-full">
                    <FolderIcon class="h-12 w-12 text-gray-400" />
                  </div>
                  
                  <!-- Image count overlay -->
                  <div class="absolute bottom-2 right-2 bg-black/50 text-white px-2 py-1 rounded text-xs">
                    {{ collection.images_count || 0 }} images
                  </div>

                  <!-- Privacy Badge (My Collections only) -->
                  <div v-if="isMyCollections" class="absolute top-2 right-2">
                    <span
                      class="px-2 py-1 text-xs font-medium rounded"
                      :class="{
                        'bg-green-100 text-green-800': collection.privacy === 'public',
                        'bg-yellow-100 text-yellow-800': collection.privacy === 'unlisted',
                        'bg-red-100 text-red-800': collection.privacy === 'private'
                      }"
                    >
                      {{ collection.privacy }}
                    </span>
                  </div>
                </div>

                <!-- Collection Info -->
                <div class="p-4">
                  <div class="flex items-start justify-between">
                    <div class="min-w-0 flex-1">
                      <Link
                        :href="route('collections.show', collection.id)"
                        class="text-lg font-medium text-gray-900 hover:text-blue-600 block truncate"
                      >
                        {{ collection.title }}
                      </Link>
                      <p v-if="collection.description" class="text-sm text-gray-500 mt-1 line-clamp-2">
                        {{ collection.description }}
                      </p>
                      
                      <div class="flex items-center mt-2 text-xs text-gray-400">
                        <span>{{ formatDate(collection.updated_at) }}</span>
                        <span class="mx-2">•</span>
                        <span>by {{ collection.curator?.name || 'Unknown' }}</span>
                      </div>
                    </div>

                    <!-- Actions Menu (My Collections only) -->
                    <div v-if="isMyCollections" class="ml-2">
                      <Dropdown align="right" width="48">
                        <template #trigger>
                          <button class="p-1 rounded-lg hover:bg-gray-100 transition-colors">
                            <EllipsisVerticalIcon class="h-5 w-5 text-gray-600" />
                          </button>
                        </template>
                        <template #content>
                          <DropdownLink :href="route('collections.show', collection.id)">
                            <EyeIcon class="h-4 w-4 mr-2" />
                            View Collection
                          </DropdownLink>
                          <DropdownLink :href="route('collections.edit', collection.id)">
                            <PencilIcon class="h-4 w-4 mr-2" />
                            Edit Collection
                          </DropdownLink>
                          <div class="border-t border-gray-100"></div>
                          <DropdownLink @click="deleteCollection(collection)" class="text-red-600">
                            <TrashIcon class="h-4 w-4 mr-2" />
                            Delete
                          </DropdownLink>
                        </template>
                      </Dropdown>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-12">
              <FolderIcon class="mx-auto h-12 w-12 text-gray-400" />
              <h3 class="mt-2 text-sm font-medium text-gray-900">{{ getEmptyMessage() }}</h3>
              <div v-if="canCreate && isMyCollections" class="mt-6">
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
              v-if="collections.data && collections.data.length > 0 && collections.meta"
              :links="collections.links || []"
              :meta="collections.meta || {}"
              class="mt-6"
            />
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { reactive, computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
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
  
  const routeName = props.isMyCollections ? 'my.collections' : 'collections.index'
  
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
  if (!date) return 'Unknown'
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  })
}

const deleteCollection = (collection) => {
  if (confirm(`Are you sure you want to delete "${collection.title}"?`)) {
    router.delete(route('collections.destroy', collection.id))
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
