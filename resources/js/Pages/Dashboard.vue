<template>
  <AppLayout>
    <Head title="Dashboard" />

    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Dashboard
        </h2>
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
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <!-- Welcome Message -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900">
              Welcome back, {{ user.name }}!
            </h3>
            <p class="mt-1 text-sm text-gray-600">
              Here's what's happening with your gallery today.
            </p>
          </div>
        </div>

        <!-- Storage Usage -->
        <StorageUsageCard
          :used="storageUsed"
          :quota="storageQuota"
          :percentage="storageUsagePercentage"
        />

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
          <StatCard
            title="My Images"
            :value="stats.myImages || 0"
            icon="PhotoIcon"
            :href="route('my.images')"
          />
          <StatCard
            title="My Albums"
            :value="stats.myAlbums || 0"
            icon="FolderIcon"
            :href="route('my.albums')"
          />
          <StatCard
            title="Total Views"
            :value="formatNumber(stats.totalViews || 0)"
            icon="EyeIcon"
          />
          <StatCard
            title="Total Likes"
            :value="formatNumber(stats.totalLikes || 0)"
            icon="HeartIcon"
          />
        </div>

        <!-- Admin Stats (if admin) -->
        <div v-if="isAdmin" class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
          <StatCard
            title="Total Users"
            :value="stats.totalUsers || 0"
            icon="UsersIcon"
            color="green"
            :href="route('admin.users.index')"
          />
          <StatCard
            title="Total Images"
            :value="stats.totalImages || 0"
            icon="PhotoIcon"
            color="green"
          />
          <StatCard
            title="Pending Comments"
            :value="stats.pendingComments || 0"
            icon="ChatBubbleLeftRightIcon"
            color="yellow"
            :href="route('admin.moderation.comments')"
          />
          <StatCard
            title="System Health"
            :value="systemStatus.overall || 'Unknown'"
            icon="ShieldCheckIcon"
            :color="systemStatus.color || 'gray'"
            :href="route('admin.system.index')"
          />
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <!-- Recent Images -->
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
              <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Recent Images</h3>
                <Link
                  :href="route('my.images')"
                  class="text-sm text-blue-600 hover:text-blue-500"
                >
                  View all
                </Link>
              </div>
              
              <div v-if="recentImages.length > 0" class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                <div
                  v-for="image in recentImages.slice(0, 6)"
                  :key="image.id"
                  class="aspect-square bg-gray-100 rounded-lg overflow-hidden hover:shadow-md transition-shadow"
                >
                  <Link :href="route('images.show', image.slug)">
                    <img
                      :src="getImageUrl(image, 'small')"
                      :alt="image.alt_text || image.title"
                      class="w-full h-full object-cover hover:scale-105 transition-transform"
                    >
                  </Link>
                </div>
              </div>
              
              <div v-else class="text-center py-8">
                <PhotoIcon class="mx-auto h-12 w-12 text-gray-400" />
                <h3 class="mt-2 text-sm font-medium text-gray-900">No images yet</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by uploading your first image.</p>
                <div class="mt-6">
                  <Link
                    :href="route('upload')"
                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700"
                  >
                    <PlusIcon class="-ml-1 mr-2 h-5 w-5" />
                    Upload Images
                  </Link>
                </div>
              </div>
            </div>
          </div>

          <!-- Recent Albums -->
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
              <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Recent Albums</h3>
                <Link
                  :href="route('my.albums')"
                  class="text-sm text-blue-600 hover:text-blue-500"
                >
                  View all
                </Link>
              </div>
              
              <div v-if="recentAlbums.length > 0" class="space-y-4">
                <div
                  v-for="album in recentAlbums.slice(0, 4)"
                  :key="album.id"
                  class="flex items-center p-3 border border-gray-200 rounded-lg hover:shadow-sm transition-shadow"
                >
                  <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                      <FolderIcon class="h-6 w-6 text-gray-400" />
                    </div>
                  </div>
                  <div class="ml-4 flex-1 min-w-0">
                    <Link
                      :href="route('albums.show', album.slug)"
                      class="text-sm font-medium text-gray-900 hover:text-blue-600"
                    >
                      {{ album.title }}
                    </Link>
                    <p class="text-xs text-gray-500 mt-1">
                      {{ album.images_count }} image{{ album.images_count !== 1 ? 's' : '' }}
                    </p>
                  </div>
                  <div class="text-xs text-gray-400">
                    {{ formatDate(album.updated_at) }}
                  </div>
                </div>
              </div>
              
              <div v-else class="text-center py-8">
                <FolderIcon class="mx-auto h-12 w-12 text-gray-400" />
                <h3 class="mt-2 text-sm font-medium text-gray-900">No albums yet</h3>
                <p class="mt-1 text-sm text-gray-500">Create your first album to organize your images.</p>
                <div class="mt-6">
                  <Link
                    :href="route('albums.create')"
                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700"
                  >
                    <PlusIcon class="-ml-1 mr-2 h-5 w-5" />
                    Create Album
                  </Link>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Activity (for admins) -->
        <div v-if="isAdmin && recentActivities.length > 0" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Activity</h3>
            <div class="flow-root">
              <ul role="list" class="-mb-8">
                <li
                  v-for="(activity, index) in recentActivities.slice(0, 5)"
                  :key="activity.id"
                  class="relative pb-8"
                >
                  <span
                    v-if="index !== recentActivities.slice(0, 5).length - 1"
                    class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200"
                    aria-hidden="true"
                  ></span>
                  <div class="relative flex space-x-3">
                    <div class="flex-shrink-0">
                      <component
                        :is="getActivityIcon(activity.type)"
                        class="h-8 w-8 text-gray-400"
                      />
                    </div>
                    <div class="min-w-0 flex-1">
                      <p class="text-sm text-gray-500">
                        {{ activity.message }}
                        <span class="whitespace-nowrap">{{ formatTimeAgo(activity.timestamp) }}</span>
                      </p>
                    </div>
                  </div>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import route from 'ziggy-js'
import {
  PlusIcon,
  PhotoIcon,
  FolderIcon,
  EyeIcon,
  HeartIcon,
  UsersIcon,
  ChatBubbleLeftRightIcon,
  ShieldCheckIcon,
} from '@heroicons/vue/24/outline'

import AppLayout from '@/Layouts/AppLayout.vue'
import StorageUsageCard from '@/Components/StorageUsageCard.vue'
import StatCard from '@/Components/StatCard.vue'

const page = usePage()

const props = defineProps({
  user: { type: Object, default: () => ({}) },
  storageUsed: { type: Number, default: 0 },
  storageQuota: { type: Number, default: 0 },
  storageUsagePercentage: { type: Number, default: 0 },
  stats: { type: Object, default: () => ({}) },
  recentImages: { type: Array, default: () => [] },
  recentAlbums: { type: Array, default: () => [] },
  recentActivities: { type: Array, default: () => [] },
  systemStatus: { type: Object, default: () => ({}) },
  auth: { type: Object, default: () => ({ user: null, roles: [] }) },
  errors: { type: Object, default: () => ({}) },
})

const canUpload = computed(() => {
  return page.props.auth.user?.permissions?.includes('upload_images')
})

const isAdmin = computed(() => {
  return page.props.auth.user?.roles?.includes('admin')
})

const formatNumber = (number) => {
  if (number >= 1000000) {
    return (number / 1000000).toFixed(1) + 'M'
  } else if (number >= 1000) {
    return (number / 1000).toFixed(1) + 'K'
  }
  return number.toString()
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString()
}

const formatTimeAgo = (date) => {
  const seconds = Math.floor((new Date() - new Date(date)) / 1000)
  
  if (seconds < 60) return `${seconds}s ago`
  if (seconds < 3600) return `${Math.floor(seconds / 60)}m ago`
  if (seconds < 86400) return `${Math.floor(seconds / 3600)}h ago`
  return `${Math.floor(seconds / 86400)}d ago`
}

const getImageUrl = (image, variant = 'medium') => {
  if (image.versions) {
    const version = image.versions.find(v => v.variant === variant)
    if (version) return version.url
  }
  return image.url || image.storage_path
}

const getActivityIcon = (type) => {
  const icons = {
    image_uploaded: PhotoIcon,
    comment_added: ChatBubbleLeftRightIcon,
    user_registered: UsersIcon,
  }
  return icons[type] || PhotoIcon
}
</script>
