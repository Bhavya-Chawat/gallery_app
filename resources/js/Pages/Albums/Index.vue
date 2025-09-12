<template>
  <AppLayout>
    <Head :title="pageTitle" />

    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ pageTitle }}
          </h2>
          <p class="text-sm text-gray-600 mt-1">
            {{ pageDescription }}
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
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
              <!-- Search -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <input
                  v-model="searchForm.search"
                  @keyup.enter="search"
                  type="text"
                  :placeholder="isMyAlbums ? 'Search your albums...' : 'Search albums...'"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                />
              </div>

              <!-- Privacy Filter (My Albums only) -->
              <div v-if="isMyAlbums">
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

              <!-- Published Status (My Albums only) -->
              <div v-if="isMyAlbums">
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select
                  v-model="searchForm.published"
                  @change="search"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                >
                  <option value="">All Statuses</option>
                  <option value="published">Published</option>
                  <option value="unpublished">Unpublished</option>
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

        <!-- Bulk Actions Bar (My Albums only) -->
        <div v-if="isMyAlbums && selectedAlbums.length > 0" class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
          <div class="flex items-center justify-between">
            <span class="text-sm font-medium text-blue-900">
              {{ selectedAlbums.length }} album(s) selected
            </span>
            <div class="flex items-center space-x-2">
              <select
                v-model="bulkAction"
                class="text-sm border border-blue-300 rounded-md px-3 py-1 focus:ring-blue-500 focus:border-blue-500"
              >
                <option value="">Select action...</option>
                <option value="publish">Publish</option>
                <option value="unpublish">Unpublish</option>
                <option value="privacy">Change Privacy</option>
                <option value="delete" class="text-red-600">Delete</option>
              </select>
              
              <select
                v-if="bulkAction === 'privacy'"
                v-model="privacyLevel"
                class="text-sm border border-blue-300 rounded-md px-3 py-1 focus:ring-blue-500 focus:border-blue-500"
              >
                <option value="public">Public</option>
                <option value="unlisted">Unlisted</option>
                <option value="private">Private</option>
              </select>

              <button
                @click="executeBulkAction"
                :disabled="!bulkAction || bulkActionLoading"
                class="px-4 py-1 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700 disabled:opacity-50"
              >
                {{ bulkActionLoading ? 'Processing...' : 'Apply' }}
              </button>
              
              <button
                @click="clearSelection"
                class="px-4 py-1 bg-gray-300 text-gray-700 text-sm rounded-md hover:bg-gray-400"
              >
                Clear
              </button>
            </div>
          </div>
        </div>

        <!-- Albums Grid -->
        <div v-if="albums.data && albums.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="album in albums.data"
            :key="album.id"
            class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow"
          >
            <!-- Selection Checkbox (My Albums only) -->
            <div v-if="isMyAlbums" class="absolute top-4 left-4 z-10">
              <input
                v-model="selectedAlbums"
                :value="album.id"
                type="checkbox"
                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded bg-white/80"
              />
            </div>

            <!-- Privacy Badge (My Albums only) -->
            <div v-if="isMyAlbums" class="absolute top-4 right-4 z-10">
              <span
                class="px-2 py-1 text-xs font-medium rounded"
                :class="{
                  'bg-green-100 text-green-800': album.privacy === 'public',
                  'bg-yellow-100 text-yellow-800': album.privacy === 'unlisted',
                  'bg-red-100 text-red-800': album.privacy === 'private'
                }"
              >
                {{ album.privacy }}
              </span>
            </div>

            <!-- Published Status Badge (My Albums only) -->
            <div v-if="isMyAlbums && !album.is_published" class="absolute bottom-16 left-4 z-10">
              <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded">
                Draft
              </span>
            </div>

            <!-- Cover Image -->
            <div class="aspect-video bg-gray-200 relative">
              <img
                v-if="album.cover_image"
                :src="getImageUrl(album.cover_image)"
                :alt="album.title"
                class="w-full h-full object-cover"
              />
              <div v-else class="flex items-center justify-center h-full">
                <FolderIcon class="h-12 w-12 text-gray-400" />
              </div>
              
              <!-- Image count overlay -->
              <div class="absolute bottom-2 right-2 bg-black/50 text-white px-2 py-1 rounded text-xs">
                {{ album.images_count || 0 }} images
              </div>
            </div>

            <!-- Album Info -->
            <div class="p-4">
              <div class="flex items-start justify-between">
                <div class="min-w-0 flex-1">
                  <Link
                    :href="route('albums.show', album.slug)"
                    class="text-lg font-medium text-gray-900 hover:text-blue-600 block truncate"
                  >
                    {{ album.title }}
                  </Link>
                  <p v-if="album.description" class="text-sm text-gray-500 mt-1 line-clamp-2">
                    {{ album.description }}
                  </p>
                  <div class="flex items-center mt-2 text-xs text-gray-400">
                    <span>{{ formatDate(album.updated_at) }}</span>
                    <span class="mx-2">•</span>
                    <span>by {{ album.owner?.name }}</span>
                  </div>
                </div>

                <!-- Actions Menu (My Albums only) -->
                <div v-if="isMyAlbums" class="ml-2">
                  <Dropdown align="right" width="48">
                    <template #trigger>
                      <button class="p-1 rounded-lg hover:bg-gray-100 transition-colors">
                        <EllipsisVerticalIcon class="h-5 w-5 text-gray-600" />
                      </button>
                    </template>
                    <template #content>
                      <DropdownLink :href="route('albums.show', album.slug)">
                        <EyeIcon class="h-4 w-4 mr-2" />
                        View Album
                      </DropdownLink>
                      <DropdownLink :href="route('albums.edit', album.id)">
                        <PencilIcon class="h-4 w-4 mr-2" />
                        Edit Album
                      </DropdownLink>
                      <DropdownLink :href="route('albums.add-images-form', album.id)">
                        <PlusIcon class="h-4 w-4 mr-2" />
                        Add Images
                      </DropdownLink>
                      <div class="border-t border-gray-100"></div>
                      <DropdownLink @click="deleteAlbum(album)" class="text-red-600">
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
          <div v-if="canCreate && isMyAlbums && !hasFilters" class="mt-6">
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
          v-if="albums.data && albums.data.length > 0 && albums.meta"
          :links="albums.links || []"
          :meta="albums.meta || {}"
          class="mt-6"
        />
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

// Reactive state
const searchForm = reactive({
  search: props.filters.search || '',
  privacy: props.filters.privacy || '',
  published: props.filters.published || '',
  sort: props.filters.sort || 'updated_at',
  direction: props.filters.direction || 'desc',
  owner: props.isMyAlbums ? 'mine' : '',
  show_all: props.isMyAlbums || false,
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
    value && value !== '' && !['sort', 'direction', 'owner', 'show_all'].includes(key)
  )
})

// Methods
const search = () => {
  const cleanForm = Object.fromEntries(
    Object.entries(searchForm).filter(([key, value]) => value !== '')
  )
  
  // Stay on current route - don't redirect
  router.get(window.location.pathname, cleanForm, {
    preserveState: true,
    replace: true,
  })
}

const clearFilters = () => {
  Object.keys(searchForm).forEach(key => {
    if (!['sort', 'direction', 'owner', 'show_all'].includes(key)) {
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
