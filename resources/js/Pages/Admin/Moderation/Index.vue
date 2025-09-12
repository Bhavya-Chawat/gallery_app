<template>
  <AdminLayout>
    <Head title="Content Moderation" />

    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Content Moderation
          </h2>
          <p class="text-sm text-gray-600 mt-1">
            Review and moderate user-generated content
          </p>
        </div>
        <Link
          :href="route('admin.moderation.comments')"
          class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700"
        >
          <EyeIcon class="h-4 w-4 mr-2" />
          View All Comments
        </Link>
      </div>
    </template>

    <div class="space-y-6">
      <!-- Stats Overview -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <StatCard
          title="Pending Images"
          :value="stats.pending_images"
          icon="PhotoIcon"
          color="yellow"
        />
        <StatCard
          title="Pending Albums"
          :value="stats.pending_albums"
          icon="FolderIcon"
          color="yellow"
        />
        <StatCard
          title="Total Images"
          :value="stats.total_images"
          icon="PhotoIcon"
          color="blue"
        />
        <StatCard
          title="Total Albums"
          :value="stats.total_albums"
          icon="FolderIcon"
          color="blue"
        />
      </div>

      <!-- Pending Content -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Pending Images -->
        <div class="bg-white rounded-lg shadow">
          <div class="p-6">
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-lg font-medium text-gray-900">Pending Images</h3>
              <span class="text-sm text-gray-500">{{ pendingImages.length }} items</span>
            </div>
            
            <div v-if="pendingImages.length > 0" class="space-y-4">
              <div 
                v-for="image in pendingImages" 
                :key="image.id"
                class="flex items-center space-x-4 p-4 border border-gray-200 rounded-lg"
              >
                <img
                  :src="getImageUrl(image)"
                  :alt="image.title"
                  class="w-16 h-16 object-cover rounded-lg"
                />
                <div class="flex-1 min-w-0">
                  <h4 class="text-sm font-medium text-gray-900 truncate">{{ image.title }}</h4>
                  <p class="text-sm text-gray-500">by {{ image.owner?.name }}</p>
                  <p class="text-xs text-gray-400">{{ formatDate(image.created_at) }}</p>
                </div>
                <div class="flex space-x-2">
                  <button
                    @click="approveImage(image)"
                    class="text-green-600 hover:text-green-500 text-sm"
                  >
                    Approve
                  </button>
                  <button
                    @click="rejectImage(image)"
                    class="text-red-600 hover:text-red-500 text-sm"
                  >
                    Reject
                  </button>
                </div>
              </div>
            </div>
            
            <div v-else class="text-center py-8">
              <PhotoIcon class="mx-auto h-12 w-12 text-gray-400" />
              <p class="mt-2 text-sm text-gray-500">No pending images</p>
            </div>
          </div>
        </div>

        <!-- Pending Albums -->
        <div class="bg-white rounded-lg shadow">
          <div class="p-6">
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-lg font-medium text-gray-900">Pending Albums</h3>
              <span class="text-sm text-gray-500">{{ pendingAlbums.length }} items</span>
            </div>
            
            <div v-if="pendingAlbums.length > 0" class="space-y-4">
              <div 
                v-for="album in pendingAlbums" 
                :key="album.id"
                class="flex items-center space-x-4 p-4 border border-gray-200 rounded-lg"
              >
                <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center">
                  <FolderIcon class="h-8 w-8 text-gray-400" />
                </div>
                <div class="flex-1 min-w-0">
                  <h4 class="text-sm font-medium text-gray-900 truncate">{{ album.title }}</h4>
                  <p class="text-sm text-gray-500">by {{ album.owner?.name }}</p>
                  <p class="text-xs text-gray-400">{{ formatDate(album.created_at) }}</p>
                </div>
                <div class="flex space-x-2">
                  <button
                    @click="approveAlbum(album)"
                    class="text-green-600 hover:text-green-500 text-sm"
                  >
                    Approve
                  </button>
                  <button
                    @click="rejectAlbum(album)"
                    class="text-red-600 hover:text-red-500 text-sm"
                  >
                    Reject
                  </button>
                </div>
              </div>
            </div>
            
            <div v-else class="text-center py-8">
              <FolderIcon class="mx-auto h-12 w-12 text-gray-400" />
              <p class="mt-2 text-sm text-gray-500">No pending albums</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Content -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Recent Images -->
        <div class="bg-white rounded-lg shadow">
          <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Recently Approved Images</h3>
            
            <div v-if="recentImages.length > 0" class="grid grid-cols-3 gap-4">
              <div 
                v-for="image in recentImages.slice(0, 9)" 
                :key="image.id"
                class="aspect-square bg-gray-100 rounded-lg overflow-hidden"
              >
                <img
                  :src="getImageUrl(image)"
                  :alt="image.title"
                  class="w-full h-full object-cover"
                />
              </div>
            </div>
            
            <div v-else class="text-center py-8">
              <PhotoIcon class="mx-auto h-12 w-12 text-gray-400" />
              <p class="mt-2 text-sm text-gray-500">No recent images</p>
            </div>
          </div>
        </div>

        <!-- Recent Albums -->
        <div class="bg-white rounded-lg shadow">
          <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Recently Approved Albums</h3>
            
            <div v-if="recentAlbums.length > 0" class="space-y-3">
              <div 
                v-for="album in recentAlbums.slice(0, 6)" 
                :key="album.id"
                class="flex items-center space-x-3"
              >
                <FolderIcon class="h-8 w-8 text-gray-400" />
                <div class="flex-1 min-w-0">
                  <p class="text-sm font-medium text-gray-900 truncate">{{ album.title }}</p>
                  <p class="text-xs text-gray-500">by {{ album.owner?.name }}</p>
                </div>
                <Link
                  :href="route('albums.show', album.slug)"
                  class="text-blue-600 hover:text-blue-500 text-sm"
                >
                  View
                </Link>
              </div>
            </div>
            
            <div v-else class="text-center py-8">
              <FolderIcon class="mx-auto h-12 w-12 text-gray-400" />
              <p class="mt-2 text-sm text-gray-500">No recent albums</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import route from 'ziggy-js'
import { EyeIcon, PhotoIcon, FolderIcon } from '@heroicons/vue/24/outline'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import StatCard from '@/Components/Admin/StatCard.vue'

const props = defineProps({
  pendingImages: Array,
  pendingAlbums: Array,
  recentImages: Array,
  recentAlbums: Array,
  stats: Object,
})

const formatDate = (date) => {
  return new Date(date).toLocaleDateString()
}

const getImageUrl = (image) => {
  return `http://localhost:9000/gallery-images/${image.storage_path}`
}

const approveImage = (image) => {
  if (confirm('Approve this image?')) {
    router.post(route('admin.moderation.approve-image', image.id))
  }
}

const rejectImage = (image) => {
  if (confirm('Reject this image?')) {
    router.post(route('admin.moderation.reject-image', image.id))
  }
}

const approveAlbum = (album) => {
  if (confirm('Approve this album?')) {
    router.post(route('admin.moderation.approve-album', album.id))
  }
}

const rejectAlbum = (album) => {
  if (confirm('Reject this album?')) {
    router.post(route('admin.moderation.reject-album', album.id))
  }
}
</script>
