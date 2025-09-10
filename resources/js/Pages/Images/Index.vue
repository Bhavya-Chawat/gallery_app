<template>
  <AppLayout>
    <Head title="Gallery" />

    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Image Gallery
          </h2>
          <p class="text-sm text-gray-600 mt-1">
            Discover and explore amazing photography
          </p>
        </div>
        <div class="flex items-center space-x-4">
          <Link
            v-if="canUpload"
            :href="route('upload')"
            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700"
          >
            <PlusIcon class="h-4 w-4 mr-2" />
            Upload Images
          </Link>
        </div>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Filters -->
        <FiltersPanel
          v-model="searchForm"
          :albums="albums"
          :tags="tags"
          @search="search"
          class="mb-6"
        />

        <!-- Images Grid -->
        <div class="bg-white shadow-sm rounded-lg">
          <div class="p-6">
            <!-- View Controls -->
            <div class="flex items-center justify-between mb-6">
              <div class="flex items-center space-x-4">
                <span class="text-sm text-gray-500">
                  {{ images.meta?.total || 0 }} image{{ (images.meta?.total !== 1) ? 's' : '' }}
                </span>
                
                <select
                  v-model="searchForm.sort"
                  @change="search"
                  class="text-sm border border-gray-300 rounded-md px-3 py-1 focus:ring-blue-500 focus:border-blue-500"
                >
                  <option value="created_at">Recently Added</option>
                  <option value="title">Title</option>
                  <option value="views">Most Viewed</option>
                  <option value="likes">Most Liked</option>
                  <option value="size">File Size</option>
                </select>

                <select
                  v-model="searchForm.direction"
                  @change="search"
                  class="text-sm border border-gray-300 rounded-md px-3 py-1 focus:ring-blue-500 focus:border-blue-500"
                >
                  <option value="desc">Descending</option>
                  <option value="asc">Ascending</option>
                </select>
              </div>

              <!-- Grid Size Control -->
              <div class="flex items-center space-x-2">
                <label class="text-sm text-gray-500">Grid size:</label>
                <div class="flex border border-gray-300 rounded-md">
                  <button
                    @click="gridSize = 'small'"
                    :class="[
                      'px-3 py-1 text-sm',
                      gridSize === 'small'
                        ? 'bg-gray-100 text-gray-900'
                        : 'text-gray-500 hover:text-gray-700'
                    ]"
                  >
                    Small
                  </button>
                  <button
                    @click="gridSize = 'medium'"
                    :class="[
                      'px-3 py-1 text-sm border-l border-gray-300',
                      gridSize === 'medium'
                        ? 'bg-gray-100 text-gray-900'
                        : 'text-gray-500 hover:text-gray-700'
                    ]"
                  >
                    Medium
                  </button>
                  <button
                    @click="gridSize = 'large'"
                    :class="[
                      'px-3 py-1 text-sm border-l border-gray-300',
                      gridSize === 'large'
                        ? 'bg-gray-100 text-gray-900'
                        : 'text-gray-500 hover:text-gray-700'
                    ]"
                  >
                    Large
                  </button>
                </div>
              </div>
            </div>

            <!-- Images -->
            <ImageGrid
              :images="images.data"
              :card-size="gridSize"
              :show-bulk-actions="false"
              :empty-message="getEmptyMessage()"
              :show-upload-link="canUpload && !hasFilters"
            />

            <!-- Pagination -->
            <Pagination
              v-if="images.data.length > 0"
              :links="images.links"
              :meta="images.meta"
              class="mt-6"
            />
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import { PlusIcon } from '@heroicons/vue/24/outline'

import AppLayout from '@/Layouts/AppLayout.vue'
import ImageGrid from '@/Components/Gallery/ImageGrid.vue'
import FiltersPanel from '@/Components/Gallery/FiltersPanel.vue'
import Pagination from '@/Components/Pagination.vue'

const page = usePage()

const props = defineProps({
  images: Object,
  albums: {
    type: Array,
    default: () => [],
  },
  tags: {
    type: Array,
    default: () => [],
  },
  filters: {
    type: Object,
    default: () => ({}),
  },
  canUpload: {
    type: Boolean,
    default: false,
  },
})

const gridSize = ref('medium')

const searchForm = reactive({
  search: props.filters.search || '',
  album: props.filters.album || '',
  tag: props.filters.tag || '',
  owner: props.filters.owner || '',
  date_from: props.filters.date_from || '',
  date_to: props.filters.date_to || '',
  sort: props.filters.sort || 'created_at',
  direction: props.filters.direction || 'desc',
})

const hasFilters = computed(() => {
  return Object.values(searchForm).some(value => value && value !== 'created_at' && value !== 'desc')
})

const canUpload = computed(() => {
  return page.props.auth.user?.permissions?.includes('upload_images')
})

const search = () => {
  // Remove empty values
  const cleanForm = Object.fromEntries(
    Object.entries(searchForm).filter(([key, value]) => value !== '')
  )
  
  router.get(route('gallery.index'), cleanForm, {
    preserveState: true,
    replace: true,
  })
}

const getEmptyMessage = () => {
  if (hasFilters.value) {
    return 'No images found matching your criteria. Try adjusting your filters.'
  }
  return 'No images available yet.'
}
</script>
