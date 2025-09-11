<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
        Albums
        <span v-if="showCount && albums.length > 0" class="text-sm font-normal text-gray-500 dark:text-gray-400 ml-2">
          ({{ albums.length }})
        </span>
      </h2>
      
      <PrimaryButton v-if="canCreate" @click="$emit('create')" class="bg-gradient-to-r from-blue-600 via-cyan-500 to-blue-400 hover:shadow-lg transform hover:scale-105 transition-all duration-200">
        <PlusIcon class="h-5 w-5 mr-2" />
        Create Album
      </PrimaryButton>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
      <div v-for="i in 8" :key="i" class="animate-pulse">
        <div class="bg-gray-200 dark:bg-slate-700 rounded-xl aspect-video mb-4"></div>
        <div class="h-4 bg-gray-200 dark:bg-slate-700 rounded mb-2"></div>
        <div class="h-3 bg-gray-200 dark:bg-slate-700 rounded w-2/3"></div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else-if="albums.length === 0" class="text-center py-12">
      <PhotoIcon class="mx-auto h-12 w-12 text-gray-400 mb-4" />
      <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">No albums yet</h3>
      <p class="text-gray-500 dark:text-gray-400 mb-6 max-w-sm mx-auto">
        {{ emptyMessage || "Start organizing your photos by creating your first album." }}
      </p>
      <PrimaryButton v-if="canCreate" @click="$emit('create')" class="bg-gradient-to-r from-blue-600 via-cyan-500 to-blue-400">
        <PlusIcon class="h-5 w-5 mr-2" />
        Create Your First Album
      </PrimaryButton>
    </div>

    <!-- Albums Grid -->
    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
      <div
        v-for="album in albums"
        :key="album.id"
        class="group cursor-pointer transform hover:scale-105 transition-all duration-300"
        @click="$emit('open', album.id)"
      >
        <!-- Album Cover -->
        <div class="relative aspect-video bg-gradient-to-br from-gray-100 to-gray-200 dark:from-slate-700 dark:to-slate-800 rounded-xl overflow-hidden mb-4 shadow-md hover:shadow-xl transition-shadow duration-300">
          <!-- Cover Image -->
          <img
            v-if="album.cover_image"
            :src="album.cover_image.thumbnails.medium"
            :alt="album.title"
            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
            loading="lazy"
          />
          
          <!-- Default Cover -->
          <div v-else class="flex items-center justify-center w-full h-full">
            <PhotoIcon class="h-8 w-8 text-gray-400" />
          </div>

          <!-- Overlay -->
          <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
            <div class="absolute bottom-4 left-4 right-4">
              <div class="flex items-center justify-between text-white">
                <span class="text-sm font-medium">{{ album.images_count }} {{ album.images_count === 1 ? 'image' : 'images' }}</span>
                <EyeIcon v-if="album.visibility === 'public'" class="h-4 w-4" />
                <LockClosedIcon v-else class="h-4 w-4" />
              </div>
            </div>
          </div>

          <!-- Privacy Badge -->
          <div v-if="album.visibility === 'private'" class="absolute top-3 right-3">
            <div class="bg-black/50 backdrop-blur-sm rounded-full p-1.5">
              <LockClosedIcon class="h-4 w-4 text-white" />
            </div>
          </div>
        </div>

        <!-- Album Info -->
        <div class="space-y-2">
          <h3 class="font-semibold text-gray-900 dark:text-gray-100 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-200 line-clamp-1">
            {{ album.title }}
          </h3>
          
          <p v-if="album.description" class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2">
            {{ album.description }}
          </p>
          
          <!-- Stats -->
          <div class="flex items-center space-x-4 text-xs text-gray-500 dark:text-gray-400">
            <span class="flex items-center">
              <PhotoIcon class="h-4 w-4 mr-1" />
              {{ album.images_count }}
            </span>
            
            <span v-if="showOwner && album.owner" class="flex items-center">
              <UserIcon class="h-4 w-4 mr-1" />
              {{ album.owner.name }}
            </span>
            
            <span v-if="album.updated_at" class="flex items-center">
              <CalendarIcon class="h-4 w-4 mr-1" />
              {{ formatDate(album.updated_at) }}
            </span>
          </div>

          <!-- Tags -->
          <div v-if="album.tags && album.tags.length > 0" class="flex flex-wrap gap-1">
            <span
              v-for="tag in album.tags.slice(0, 3)"
              :key="tag.id"
              class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200"
            >
              {{ tag.name }}
            </span>
            <span v-if="album.tags.length > 3" class="text-xs text-gray-500 dark:text-gray-400 self-center">
              +{{ album.tags.length - 3 }}
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- Load More Button -->
    <div v-if="hasMore && !loading" class="text-center">
      <SecondaryButton @click="$emit('load-more')" :disabled="loadingMore">
        <ArrowPathIcon v-if="loadingMore" class="h-4 w-4 mr-2 animate-spin" />
        {{ loadingMore ? 'Loading...' : 'Load More Albums' }}
      </SecondaryButton>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { 
  PlusIcon, 
  PhotoIcon, 
  EyeIcon, 
  LockClosedIcon, 
  UserIcon, 
  CalendarIcon,
  ArrowPathIcon
} from '@heroicons/vue/24/outline'
import PrimaryButton from '@/Components/UI/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'

// Props
defineProps({
  albums: {
    type: Array,
    default: () => []
  },
  loading: {
    type: Boolean,
    default: false
  },
  loadingMore: {
    type: Boolean,
    default: false
  },
  hasMore: {
    type: Boolean,
    default: false
  },
  showCount: {
    type: Boolean,
    default: true
  },
  showOwner: {
    type: Boolean,
    default: false
  },
  canCreate: {
    type: Boolean,
    default: true
  },
  emptyMessage: {
    type: String,
    default: ''
  }
})

// Events
defineEmits(['open', 'create', 'load-more'])

// Helper function to format dates
const formatDate = (date) => {
  return new Intl.DateTimeFormat('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  }).format(new Date(date))
}
</script>

<style scoped>
.line-clamp-1 {
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>