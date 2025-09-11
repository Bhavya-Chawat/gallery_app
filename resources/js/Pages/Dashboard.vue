<template>
  <AppLayout>
    <div class="min-h-screen bg-gray-50 dark:bg-slate-900">
      <!-- Page Header -->
      <div class="bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl border-b border-gray-200 dark:border-slate-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                Welcome back, {{ user.name }}!
              </h1>
              <p class="text-gray-600 dark:text-slate-400 mt-1">
                Here's what's happening with your gallery
              </p>
            </div>
            <div class="flex items-center space-x-4">
              <!-- Quick Upload Button -->
              <Link
                href="/gallery/upload"
                class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-cyan-500 text-white rounded-lg hover:from-blue-700 hover:to-cyan-600 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 shadow-lg"
              >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Upload Images
              </Link>
            </div>
          </div>
        </div>
      </div>

      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <!-- Total Images -->
          <div class="bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl rounded-2xl shadow-xl p-6">
            <div class="flex items-center">
              <div class="p-3 rounded-full bg-gradient-to-r from-blue-400 to-cyan-400 text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-slate-400">Total Images</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ analytics?.total_images || 0 }}</p>
              </div>
            </div>
          </div>

          <!-- Total Albums -->
          <div class="bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl rounded-2xl shadow-xl p-6">
            <div class="flex items-center">
              <div class="p-3 rounded-full bg-gradient-to-r from-purple-400 to-pink-400 text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-slate-400">Albums</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ myAlbums.length }}</p>
              </div>
            </div>
          </div>

          <!-- Storage Used -->
          <div class="bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl rounded-2xl shadow-xl p-6">
            <div class="flex items-center">
              <div class="p-3 rounded-full bg-gradient-to-r from-green-400 to-emerald-400 text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4" />
                </svg>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-slate-400">Storage Used</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                  {{ formatFileSize(quota?.used || 0) }}
                </p>
                <div class="w-full bg-gray-200 dark:bg-slate-600 rounded-full h-2 mt-2">
                  <div
                    class="bg-gradient-to-r from-green-400 to-emerald-400 h-2 rounded-full transition-all duration-300"
                    :style="{ width: `${storagePercentage}%` }"
                  ></div>
                </div>
              </div>
            </div>
          </div>

          <!-- Total Views -->
          <div class="bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl rounded-2xl shadow-xl p-6">
            <div class="flex items-center">
              <div class="p-3 rounded-full bg-gradient-to-r from-yellow-400 to-orange-400 text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-slate-400">Total Views</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ analytics?.total_views || 0 }}</p>
              </div>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Main Content Area -->
          <div class="lg:col-span-2 space-y-8">
            <!-- Recent Uploads -->
            <div class="bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl rounded-2xl shadow-xl">
              <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                  <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Recent Uploads</h2>
                  <Link
                    href="/gallery?user=me"
                    class="text-blue-600 dark:text-cyan-400 hover:text-blue-800 dark:hover:text-cyan-300 text-sm font-medium"
                  >
                    View all →
                  </Link>
                </div>
                
                <div v-if="recentUploads.length > 0" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                  <Link
                    v-for="image in recentUploads"
                    :key="image.id"
                    :href="`/gallery/images/${image.id}`"
                    class="group relative aspect-square overflow-hidden rounded-xl bg-gray-100 dark:bg-slate-700"
                  >
                    <img
                      :src="image.thumb_url"
                      :alt="image.title || 'Untitled'"
                      class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200"
                    />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                      <div class="absolute bottom-2 left-2 right-2">
                        <p class="text-white text-sm font-medium truncate">
                          {{ image.title || 'Untitled' }}
                        </p>
                        <p class="text-gray-200 text-xs">
                          {{ formatTimeAgo(image.created_at) }}
                        </p>
                      </div>
                    </div>
                  </Link>
                </div>
                
                <div v-else class="text-center py-8">
                  <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-slate-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                  <p class="text-gray-500 dark:text-slate-400">No images uploaded yet</p>
                  <Link
                    href="/gallery/upload"
                    class="inline-block mt-3 px-4 py-2 bg-gradient-to-r from-blue-600 to-cyan-500 text-white rounded-lg hover:from-blue-700 hover:to-cyan-600 transition-all duration-200"
                  >
                    Upload your first image
                  </Link>
                </div>
              </div>
            </div>

            <!-- Activity Feed (if pendingModerationCount > 0 and user is admin) -->
            <div 
              v-if="pendingModerationCount > 0 && (user.roles && user.roles.some(role => role.name === 'admin'))"
              class="bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl rounded-2xl shadow-xl"
            >
              <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                  <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Pending Moderation</h2>
                  <span class="px-2 py-1 bg-gradient-to-r from-yellow-400 to-orange-400 text-white text-sm rounded-full">
                    {{ pendingModerationCount }}
                  </span>
                </div>
                <p class="text-gray-600 dark:text-slate-400 mb-4">
                  There are {{ pendingModerationCount }} items waiting for moderation review.
                </p>
                <Link
                  href="/admin/moderation"
                  class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-yellow-500 to-orange-500 text-white rounded-lg hover:from-yellow-600 hover:to-orange-600 transition-all duration-200"
                >
                  <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2-2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                  </svg>
                  Review Items
                </Link>
              </div>
            </div>
          </div>

          <!-- Sidebar -->
          <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl rounded-2xl shadow-xl p-6">
              <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Quick Actions</h3>
              <div class="space-y-3">
                <Link
                  href="/gallery/upload"
                  class="flex items-center p-3 rounded-lg bg-gradient-to-r from-blue-50 to-cyan-50 dark:from-blue-900/20 dark:to-cyan-900/20 hover:from-blue-100 hover:to-cyan-100 dark:hover:from-blue-900/30 dark:hover:to-cyan-900/30 transition-all duration-200"
                >
                  <svg class="w-5 h-5 text-blue-600 dark:text-cyan-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                  </svg>
                  <span class="text-gray-900 dark:text-gray-100 font-medium">Upload Images</span>
                </Link>
                
                <Link
                  href="/albums/create"
                  class="flex items-center p-3 rounded-lg bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 hover:from-purple-100 hover:to-pink-100 dark:hover:from-purple-900/30 dark:hover:to-pink-900/30 transition-all duration-200"
                >
                  <svg class="w-5 h-5 text-purple-600 dark:text-pink-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                  </svg>
                  <span class="text-gray-900 dark:text-gray-100 font-medium">Create Album</span>
                </Link>
                
                <Link
                  href="/settings"
                  class="flex items-center p-3 rounded-lg bg-gradient-to-r from-gray-50 to-slate-50 dark:from-slate-700/50 dark:to-slate-600/50 hover:from-gray-100 hover:to-slate-100 dark:hover:from-slate-600/50 dark:hover:to-slate-500/50 transition-all duration-200"
                >
                  <svg class="w-5 h-5 text-gray-600 dark:text-slate-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  </svg>
                  <span class="text-gray-900 dark:text-gray-100 font-medium">Settings</span>
                </Link>
              </div>
            </div>

            <!-- My Albums -->
            <div class="bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl rounded-2xl shadow-xl p-6">
              <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">My Albums</h3>
                <Link
                  href="/albums"
                  class="text-blue-600 dark:text-cyan-400 hover:text-blue-800 dark:hover:text-cyan-300 text-sm font-medium"
                >
                  View all →
                </Link>
              </div>
              
              <div v-if="myAlbums.length > 0" class="space-y-3">
                <Link
                  v-for="album in myAlbums.slice(0, 5)"
                  :key="album.id"
                  :href="`/gallery/albums/${album.id}`"
                  class="flex items-center p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors duration-200 group"
                >
                  <div class="w-10 h-10 rounded-lg overflow-hidden bg-gray-200 dark:bg-slate-600 mr-3">
                    <img
                      v-if="album.cover_image?.thumb_url"
                      :src="album.cover_image.thumb_url"
                      :alt="album.title"
                      class="w-full h-full object-cover"
                    />
                    <div v-else class="w-full h-full flex items-center justify-center">
                      <svg class="w-5 h-5 text-gray-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                      </svg>
                    </div>
                  </div>
                  <div class="flex-1">
                    <p class="font-medium text-gray-900 dark:text-gray-100 group-hover:text-blue-600 dark:group-hover:text-cyan-400">
                      {{ album.title }}
                    </p>
                    <p class="text-sm text-gray-500 dark:text-slate-400">
                      {{ album.images_count }} {{ album.images_count === 1 ? 'image' : 'images' }}
                    </p>
                  </div>
                </Link>
              </div>
              
              <div v-else class="text-center py-6">
                <svg class="mx-auto h-8 w-8 text-gray-400 dark:text-slate-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                <p class="text-gray-500 dark:text-slate-400 text-sm">No albums created yet</p>
              </div>
            </div>

            <!-- Storage Quota -->
            <div class="bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl rounded-2xl shadow-xl p-6">
              <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Storage Usage</h3>
              <div class="space-y-3">
                <div class="flex justify-between text-sm">
                  <span class="text-gray-600 dark:text-slate-400">Used</span>
                  <span class="text-gray-900 dark:text-gray-100 font-medium">
                    {{ formatFileSize(quota?.used || 0) }}
                  </span>
                </div>
                <div class="flex justify-between text-sm">
                  <span class="text-gray-600 dark:text-slate-400">Total</span>
                  <span class="text-gray-900 dark:text-gray-100 font-medium">
                    {{ formatFileSize(quota?.total || 0) }}
                  </span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-slate-600 rounded-full h-3">
                  <div
                    :class="[
                      'h-3 rounded-full transition-all duration-300',
                      storagePercentage > 90 ? 'bg-gradient-to-r from-red-400 to-red-500' :
                      storagePercentage > 70 ? 'bg-gradient-to-r from-yellow-400 to-orange-400' :
                      'bg-gradient-to-r from-green-400 to-emerald-400'
                    ]"
                    :style="{ width: `${storagePercentage}%` }"
                  ></div>
                </div>
                <div class="flex justify-between text-xs">
                  <span class="text-gray-500 dark:text-slate-500">{{ storagePercentage.toFixed(1) }}% used</span>
                  <span 
                    v-if="storagePercentage > 80"
                    class="text-orange-600 dark:text-orange-400 font-medium"
                  >
                    {{ storagePercentage > 95 ? 'Storage almost full' : 'Running low on storage' }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '../Layouts/AppLayout.vue'
import axios from 'axios'

// Props from backend
const props = defineProps({
  user: Object,
  recentUploads: Array,
  pendingModerationCount: Number,
  quota: Object
})

// Reactive data
const analytics = ref(null)
const myAlbums = ref([])
const loading = ref(true)

// Computed
const storagePercentage = computed(() => {
  if (!props.quota || !props.quota.total) return 0
  return Math.min((props.quota.used / props.quota.total) * 100, 100)
})

// Methods
const formatFileSize = (bytes) => {
  if (!bytes) return '0 B'
  const sizes = ['B', 'KB', 'MB', 'GB', 'TB']
  if (bytes === 0) return '0 B'
  const i = Math.floor(Math.log(bytes) / Math.log(1024))
  return Math.round(bytes / Math.pow(1024, i) * 100) / 100 + ' ' + sizes[i]
}

const formatTimeAgo = (dateString) => {
  try {
    const date = new Date(dateString)
    const now = new Date()
    const diffInSeconds = Math.floor((now - date) / 1000)
    
    if (diffInSeconds < 60) return 'just now'
    if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)}m ago`
    if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)}h ago`
    if (diffInSeconds < 2592000) return `${Math.floor(diffInSeconds / 86400)}d ago`
    
    return date.toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric'
    })
  } catch (error) {
    return ''
  }
}

const loadDashboardData = async () => {
  try {
    loading.value = true
    
    // Load analytics
    const analyticsResponse = await axios.get('/api/v1/my/analytics')
    analytics.value = analyticsResponse.data.data
    
    // Load albums
    const albumsResponse = await axios.get('/api/v1/my/albums')
    myAlbums.value = albumsResponse.data.data
    
  } catch (error) {
    console.error('Error loading dashboard data:', error)
  } finally {
    loading.value = false
  }
}

// Lifecycle
onMounted(() => {
  loadDashboardData()
})
</script>