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
/*<template>
  <AdminLayout>
    <Head title="Content Moderation" />

    <template #header>
      <div class="flex items-center justify-between animate-fade-in-up">
        <div>
          <h2 class="font-semibold text-xl bg-gradient-to-r from-violet-400 via-purple-400 to-cyan-400 bg-clip-text text-transparent leading-tight animate-shimmer-text">
            Content Moderation
          </h2>
          <p class="text-sm text-slate-300 mt-1">
            Review and moderate user-generated content
          </p>
        </div>
        <Link
          :href="route('admin.moderation.comments')"
          class="relative inline-flex items-center px-4 py-2 bg-gradient-to-r from-violet-600 via-purple-600 to-cyan-600 border border-white/20 rounded-xl font-semibold text-xs text-white uppercase tracking-widest hover:from-violet-500 hover:via-purple-500 hover:to-cyan-500 transition-all duration-500 hover:scale-105 hover:shadow-2xl hover:shadow-violet-500/25 group overflow-hidden backdrop-blur-xl"
        >
          <div class="absolute inset-0 bg-gradient-to-r from-violet-400/20 via-purple-400/20 to-cyan-400/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
          <EyeIcon class="h-4 w-4 mr-2 relative z-10" />
          <span class="relative z-10">View All Comments</span>
          <div class="absolute -inset-1 bg-gradient-to-r from-violet-600 via-purple-600 to-cyan-600 rounded-xl blur opacity-30 group-hover:opacity-70 transition-opacity duration-300 -z-10"></div>
        </Link>
      </div>
    </template>

    <div class="space-y-6 relative">
      <!-- Floating Particles Background -->
      <div class="fixed inset-0 pointer-events-none overflow-hidden opacity-20 z-0">
        <div 
          v-for="i in 15" 
          :key="`particle-${i}`"
          :class="[
            'absolute w-1 h-1 rounded-full',
            'bg-gradient-to-r from-violet-400 to-cyan-400',
            `animate-float-particle-${(i % 5) + 1}`
          ]"
          :style="{
            left: `${Math.random() * 100}%`,
            top: `${Math.random() * 100}%`,
            animationDelay: `${Math.random() * 10}s`,
            animationDuration: `${8 + Math.random() * 4}s`
          }"
        ></div>
      </div>

      <!-- Stats Overview -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4 animate-fade-in-up" style="animation-delay: 0.1s;">
        <div class="relative group">
          <div class="absolute inset-0 bg-gradient-to-r from-yellow-500/20 to-amber-500/20 rounded-2xl blur-lg opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
          <StatCard
            title="Pending Images"
            :value="stats.pending_images"
            icon="PhotoIcon"
            color="yellow"
            class="relative z-10 bg-slate-800/50 backdrop-blur-xl border border-white/10 rounded-2xl shadow-2xl hover:shadow-yellow-500/10 transition-all duration-500 hover:scale-105 hover:-translate-y-2"
          />
        </div>
        <div class="relative group">
          <div class="absolute inset-0 bg-gradient-to-r from-yellow-500/20 to-amber-500/20 rounded-2xl blur-lg opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
          <StatCard
            title="Pending Albums"
            :value="stats.pending_albums"
            icon="FolderIcon"
            color="yellow"
            class="relative z-10 bg-slate-800/50 backdrop-blur-xl border border-white/10 rounded-2xl shadow-2xl hover:shadow-yellow-500/10 transition-all duration-500 hover:scale-105 hover:-translate-y-2"
          />
        </div>
        <div class="relative group">
          <div class="absolute inset-0 bg-gradient-to-r from-cyan-500/20 to-blue-500/20 rounded-2xl blur-lg opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
          <StatCard
            title="Total Images"
            :value="stats.total_images"
            icon="PhotoIcon"
            color="blue"
            class="relative z-10 bg-slate-800/50 backdrop-blur-xl border border-white/10 rounded-2xl shadow-2xl hover:shadow-cyan-500/10 transition-all duration-500 hover:scale-105 hover:-translate-y-2"
          />
        </div>
        <div class="relative group">
          <div class="absolute inset-0 bg-gradient-to-r from-cyan-500/20 to-blue-500/20 rounded-2xl blur-lg opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
          <StatCard
            title="Total Albums"
            :value="stats.total_albums"
            icon="FolderIcon"
            color="blue"
            class="relative z-10 bg-slate-800/50 backdrop-blur-xl border border-white/10 rounded-2xl shadow-2xl hover:shadow-cyan-500/10 transition-all duration-500 hover:scale-105 hover:-translate-y-2"
          />
        </div>
      </div>

      <!-- Pending Content -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 animate-fade-in-up" style="animation-delay: 0.2s;">
        
        <!-- Pending Images -->
        <div class="relative group">
          <div class="absolute inset-0 bg-gradient-to-br from-violet-500/10 via-purple-500/5 to-cyan-500/10 rounded-2xl blur-xl opacity-0 group-hover:opacity-100 transition-opacity duration-700"></div>
          <div class="relative z-10 bg-slate-800/50 backdrop-blur-xl rounded-2xl shadow-2xl border border-white/10 hover:border-white/20 transition-all duration-500 hover:scale-[1.02] hover:shadow-violet-500/10">
            <div class="p-6">
              <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium bg-gradient-to-r from-violet-400 to-purple-400 bg-clip-text text-transparent animate-shimmer-text">Pending Images</h3>
                <span class="text-sm text-slate-400 bg-slate-700/50 px-3 py-1 rounded-full backdrop-blur-sm">{{ pendingImages.length }} items</span>
              </div>
              
              <div v-if="pendingImages.length > 0" class="space-y-4">
                <div 
                  v-for="(image, index) in pendingImages" 
                  :key="image.id"
                  class="flex items-center space-x-4 p-4 bg-slate-700/30 backdrop-blur-sm border border-white/10 rounded-xl hover:border-white/20 transition-all duration-300 hover:bg-slate-700/50 group animate-slide-in-right"
                  :style="{ animationDelay: `${index * 0.1}s` }"
                >
                  <div class="relative">
                    <img
                      :src="getImageUrl(image)"
                      :alt="image.title"
                      class="w-16 h-16 object-cover rounded-xl transition-transform duration-300 group-hover:scale-110"
                    />
                    <div class="absolute inset-0 bg-gradient-to-t from-violet-600/20 to-transparent rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                  </div>
                  <div class="flex-1 min-w-0">
                    <h4 class="text-sm font-medium text-white truncate">{{ image.title }}</h4>
                    <p class="text-sm text-slate-300">by {{ image.owner?.name }}</p>
                    <p class="text-xs text-slate-400">{{ formatDate(image.created_at) }}</p>
                  </div>
                  <div class="flex space-x-2">
                    <button
                      @click="approveImage(image)"
                      class="px-3 py-1.5 bg-green-600/80 hover:bg-green-500 text-green-100 text-sm rounded-lg transition-all duration-300 hover:scale-105 backdrop-blur-sm border border-green-500/20 hover:shadow-lg hover:shadow-green-500/25"
                    >
                      Approve
                    </button>
                    <button
                      @click="rejectImage(image)"
                      class="px-3 py-1.5 bg-red-600/80 hover:bg-red-500 text-red-100 text-sm rounded-lg transition-all duration-300 hover:scale-105 backdrop-blur-sm border border-red-500/20 hover:shadow-lg hover:shadow-red-500/25"
                    >
                      Reject
                    </button>
                  </div>
                </div>
              </div>
              
              <div v-else class="text-center py-8">
                <div class="relative">
                  <PhotoIcon class="mx-auto h-12 w-12 text-slate-500 animate-pulse" />
                  <div class="absolute inset-0 bg-gradient-to-r from-violet-500/20 to-cyan-500/20 rounded-full blur-xl animate-ping"></div>
                </div>
                <p class="mt-2 text-sm text-slate-400">No pending images</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Pending Albums -->
        <div class="relative group">
          <div class="absolute inset-0 bg-gradient-to-br from-cyan-500/10 via-purple-500/5 to-violet-500/10 rounded-2xl blur-xl opacity-0 group-hover:opacity-100 transition-opacity duration-700"></div>
          <div class="relative z-10 bg-slate-800/50 backdrop-blur-xl rounded-2xl shadow-2xl border border-white/10 hover:border-white/20 transition-all duration-500 hover:scale-[1.02] hover:shadow-cyan-500/10">
            <div class="p-6">
              <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium bg-gradient-to-r from-cyan-400 to-violet-400 bg-clip-text text-transparent animate-shimmer-text">Pending Albums</h3>
                <span class="text-sm text-slate-400 bg-slate-700/50 px-3 py-1 rounded-full backdrop-blur-sm">{{ pendingAlbums.length }} items</span>
              </div>
              
              <div v-if="pendingAlbums.length > 0" class="space-y-4">
                <div 
                  v-for="(album, index) in pendingAlbums" 
                  :key="album.id"
                  class="flex items-center space-x-4 p-4 bg-slate-700/30 backdrop-blur-sm border border-white/10 rounded-xl hover:border-white/20 transition-all duration-300 hover:bg-slate-700/50 group animate-slide-in-left"
                  :style="{ animationDelay: `${index * 0.1}s` }"
                >
                  <div class="w-16 h-16 bg-gradient-to-br from-slate-700 to-slate-800 rounded-xl flex items-center justify-center border border-white/10 group-hover:border-white/20 transition-all duration-300">
                    <FolderIcon class="h-8 w-8 text-slate-400 group-hover:text-cyan-400 transition-colors duration-300" />
                  </div>
                  <div class="flex-1 min-w-0">
                    <h4 class="text-sm font-medium text-white truncate">{{ album.title }}</h4>
                    <p class="text-sm text-slate-300">by {{ album.owner?.name }}</p>
                    <p class="text-xs text-slate-400">{{ formatDate(album.created_at) }}</p>
                  </div>
                  <div class="flex space-x-2">
                    <button
                      @click="approveAlbum(album)"
                      class="px-3 py-1.5 bg-green-600/80 hover:bg-green-500 text-green-100 text-sm rounded-lg transition-all duration-300 hover:scale-105 backdrop-blur-sm border border-green-500/20 hover:shadow-lg hover:shadow-green-500/25"
                    >
                      Approve
                    </button>
                    <button
                      @click="rejectAlbum(album)"
                      class="px-3 py-1.5 bg-red-600/80 hover:bg-red-500 text-red-100 text-sm rounded-lg transition-all duration-300 hover:scale-105 backdrop-blur-sm border border-red-500/20 hover:shadow-lg hover:shadow-red-500/25"
                    >
                      Reject
                    </button>
                  </div>
                </div>
              </div>
              
              <div v-else class="text-center py-8">
                <div class="relative">
                  <FolderIcon class="mx-auto h-12 w-12 text-slate-500 animate-pulse" />
                  <div class="absolute inset-0 bg-gradient-to-r from-cyan-500/20 to-violet-500/20 rounded-full blur-xl animate-ping"></div>
                </div>
                <p class="mt-2 text-sm text-slate-400">No pending albums</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Content -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 animate-fade-in-up" style="animation-delay: 0.3s;">
        
        <!-- Recent Images -->
        <div class="relative group">
          <div class="absolute inset-0 bg-gradient-to-br from-purple-500/10 via-violet-500/5 to-cyan-500/10 rounded-2xl blur-xl opacity-0 group-hover:opacity-100 transition-opacity duration-700"></div>
          <div class="relative z-10 bg-slate-800/50 backdrop-blur-xl rounded-2xl shadow-2xl border border-white/10 hover:border-white/20 transition-all duration-500 hover:scale-[1.02] hover:shadow-purple-500/10">
            <div class="p-6">
              <h3 class="text-lg font-medium bg-gradient-to-r from-purple-400 to-cyan-400 bg-clip-text text-transparent mb-4 animate-shimmer-text">Recently Approved Images</h3>
              
              <div v-if="recentImages.length > 0" class="grid grid-cols-3 gap-4">
                <div 
                  v-for="(image, index) in recentImages.slice(0, 9)" 
                  :key="image.id"
                  class="aspect-square bg-gradient-to-br from-slate-700 to-slate-800 rounded-xl overflow-hidden border border-white/10 hover:border-white/20 transition-all duration-300 group animate-scale-in"
                  :style="{ animationDelay: `${index * 0.1}s` }"
                >
                  <img
                    :src="getImageUrl(image)"
                    :alt="image.title"
                    class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                  />
                  <div class="absolute inset-0 bg-gradient-to-t from-purple-600/30 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                </div>
              </div>
              
              <div v-else class="text-center py-8">
                <div class="relative">
                  <PhotoIcon class="mx-auto h-12 w-12 text-slate-500 animate-pulse" />
                  <div class="absolute inset-0 bg-gradient-to-r from-purple-500/20 to-cyan-500/20 rounded-full blur-xl animate-ping"></div>
                </div>
                <p class="mt-2 text-sm text-slate-400">No recent images</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Albums -->
        <div class="relative group">
          <div class="absolute inset-0 bg-gradient-to-br from-cyan-500/10 via-purple-500/5 to-violet-500/10 rounded-2xl blur-xl opacity-0 group-hover:opacity-100 transition-opacity duration-700"></div>
          <div class="relative z-10 bg-slate-800/50 backdrop-blur-xl rounded-2xl shadow-2xl border border-white/10 hover:border-white/20 transition-all duration-500 hover:scale-[1.02] hover:shadow-cyan-500/10">
            <div class="p-6">
              <h3 class="text-lg font-medium bg-gradient-to-r from-cyan-400 to-purple-400 bg-clip-text text-transparent mb-4 animate-shimmer-text">Recently Approved Albums</h3>
              
              <div v-if="recentAlbums.length > 0" class="space-y-3">
                <div 
                  v-for="(album, index) in recentAlbums.slice(0, 6)" 
                  :key="album.id"
                  class="flex items-center space-x-3 p-3 bg-slate-700/30 backdrop-blur-sm border border-white/10 rounded-xl hover:border-white/20 transition-all duration-300 hover:bg-slate-700/50 group animate-slide-in-right"
                  :style="{ animationDelay: `${index * 0.1}s` }"
                >
                  <div class="bg-gradient-to-br from-slate-600 to-slate-700 rounded-lg p-2 border border-white/10 group-hover:border-white/20 transition-all duration-300">
                    <FolderIcon class="h-8 w-8 text-slate-400 group-hover:text-cyan-400 transition-colors duration-300" />
                  </div>
                  <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-white truncate">{{ album.title }}</p>
                    <p class="text-xs text-slate-400">by {{ album.owner?.name }}</p>
                  </div>
                  <Link
                    :href="route('albums.show', album.slug)"
                    class="px-3 py-1.5 bg-gradient-to-r from-cyan-600/80 to-violet-600/80 hover:from-cyan-500 hover:to-violet-500 text-white text-sm rounded-lg transition-all duration-300 hover:scale-105 backdrop-blur-sm border border-cyan-500/20 hover:shadow-lg hover:shadow-cyan-500/25"
                  >
                    View
                  </Link>
                </div>
              </div>
              
              <div v-else class="text-center py-8">
                <div class="relative">
                  <FolderIcon class="mx-auto h-12 w-12 text-slate-500 animate-pulse" />
                  <div class="absolute inset-0 bg-gradient-to-r from-cyan-500/20 to-purple-500/20 rounded-full blur-xl animate-ping"></div>
                </div>
                <p class="mt-2 text-sm text-slate-400">No recent albums</p>
              </div>
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

<style scoped>
@keyframes shimmer-text {
  0% { background-position: -200% center; }
  100% { background-position: 200% center; }
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

@keyframes slide-in-right {
  0% {
    opacity: 0;
    transform: translateX(30px);
  }
  100% {
    opacity: 1;
    transform: translateX(0);
  }
}

@keyframes slide-in-left {
  0% {
    opacity: 0;
    transform: translateX(-30px);
  }
  100% {
    opacity: 1;
    transform: translateX(0);
  }
}

@keyframes scale-in {
  0% {
    opacity: 0;
    transform: scale(0.8);
  }
  100% {
    opacity: 1;
    transform: scale(1);
  }
}

@keyframes float-particle-1 {
  0%, 100% { transform: translateY(0px) translateX(0px); opacity: 0.4; }
  50% { transform: translateY(-100px) translateX(50px); opacity: 0.8; }
}

@keyframes float-particle-2 {
  0%, 100% { transform: translateY(0px) translateX(0px); opacity: 0.3; }
  50% { transform: translateY(-80px) translateX(-30px); opacity: 0.7; }
}

@keyframes float-particle-3 {
  0%, 100% { transform: translateY(0px) translateX(0px); opacity: 0.5; }
  50% { transform: translateY(-120px) translateX(20px); opacity: 0.9; }
}

@keyframes float-particle-4 {
  0%, 100% { transform: translateY(0px) translateX(0px); opacity: 0.4; }
  50% { transform: translateY(-90px) translateX(-40px); opacity: 0.6; }
}

@keyframes float-particle-5 {
  0%, 100% { transform: translateY(0px) translateX(0px); opacity: 0.6; }
  50% { transform: translateY(-110px) translateX(60px); opacity: 0.8; }
}

.animate-shimmer-text {
  background-size: 200% auto;
  animation: shimmer-text 3s linear infinite;
}

.animate-fade-in-up {
  animation: fade-in-up 0.8s ease-out forwards;
}

.animate-slide-in-right {
  animation: slide-in-right 0.6s ease-out forwards;
}

.animate-slide-in-left {
  animation: slide-in-left 0.6s ease-out forwards;
}

.animate-scale-in {
  animation: scale-in 0.6s ease-out forwards;
}

.animate-float-particle-1 { animation: float-particle-1 8s ease-in-out infinite; }
.animate-float-particle-2 { animation: float-particle-2 10s ease-in-out infinite; }
.animate-float-particle-3 { animation: float-particle-3 7s ease-in-out infinite; }
.animate-float-particle-4 { animation: float-particle-4 9s ease-in-out infinite; }
.animate-float-particle-5 { animation: float-particle-5 11s ease-in-out infinite; }
</style>