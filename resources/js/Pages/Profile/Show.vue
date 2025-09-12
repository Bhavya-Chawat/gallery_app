<template>
  <AppLayout>
    <Head :title="profileUser.name + ' - Profile'" />

    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        
        <!-- Profile Header -->
        <div class="bg-white shadow-sm rounded-lg mb-6">
          <div class="p-6">
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-6">
                <UserAvatar :user="profileUser" size="xl" />
                <div>
                  <h1 class="text-2xl font-bold text-gray-900">{{ profileUser.name }}</h1>
                  <p v-if="profileUser.email" class="text-sm text-gray-500">{{ profileUser.email }}</p>
                  <p class="text-xs text-gray-400 mt-1">
                    Member since {{ formatDate(profileUser.created_at) }}
                  </p>
                </div>
              </div>
              <div v-if="isOwnProfile" class="text-right">
                <Link :href="route('profile.edit')" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                  Edit Profile
                </Link>
              </div>
            </div>

            <!-- Bio -->
            <div v-if="profileUser.bio" class="mt-4">
              <p class="text-gray-700">{{ profileUser.bio }}</p>
            </div>

            <!-- Links -->
            <div v-if="profileUser.website || hasSocialLinks" class="mt-4 flex flex-wrap gap-4">
              <a v-if="profileUser.website" :href="profileUser.website" target="_blank" rel="noopener" class="flex items-center text-blue-600 hover:text-blue-500">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                </svg>
                Website
              </a>
              <a v-if="profileUser.social_links?.twitter" :href="getSocialUrl('twitter', profileUser.social_links.twitter)" target="_blank" rel="noopener" class="flex items-center text-blue-400 hover:text-blue-300">
                Twitter
              </a>
              <a v-if="profileUser.social_links?.instagram" :href="getSocialUrl('instagram', profileUser.social_links.instagram)" target="_blank" rel="noopener" class="flex items-center text-pink-600 hover:text-pink-500">
                Instagram
              </a>
              <a v-if="profileUser.social_links?.linkedin" :href="getSocialUrl('linkedin', profileUser.social_links.linkedin)" target="_blank" rel="noopener" class="flex items-center text-blue-700 hover:text-blue-600">
                LinkedIn
              </a>
            </div>
          </div>
        </div>

        <!-- Statistics -->
        <div v-if="stats && Object.keys(stats).length > 0" class="bg-white shadow-sm rounded-lg mb-6">
          <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Statistics</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
              <div class="text-center p-4 bg-gray-50 rounded-lg">
                <div class="text-xl font-semibold text-gray-900">{{ stats.images || 0 }}</div>
                <div class="text-sm text-gray-500">Images</div>
              </div>
              <div class="text-center p-4 bg-gray-50 rounded-lg">
                <div class="text-xl font-semibold text-gray-900">{{ stats.albums || 0 }}</div>
                <div class="text-sm text-gray-500">Albums</div>
              </div>
              <div class="text-center p-4 bg-gray-50 rounded-lg">
                <div class="text-xl font-semibold text-gray-900">{{ formatNumber(stats.views || 0) }}</div>
                <div class="text-sm text-gray-500">Total Views</div>
              </div>
              <div class="text-center p-4 bg-gray-50 rounded-lg">
                <div class="text-xl font-semibold text-gray-900">{{ formatNumber(stats.likes || 0) }}</div>
                <div class="text-sm text-gray-500">Total Likes</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Albums -->
        <div v-if="publicAlbums.length > 0" class="bg-white shadow-sm rounded-lg mb-6">
          <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Recent Albums</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
              <div v-for="album in publicAlbums" :key="album.id" class="relative bg-gray-100 rounded-lg overflow-hidden aspect-video hover:shadow-md transition-shadow">
                <Link :href="route('albums.show', album.slug)">
                  <img
                    v-if="album.cover_image"
                    :src="getImageUrl(album.cover_image)"
                    :alt="album.title"
                    class="w-full h-full object-cover"
                  />
                  <div v-else class="flex items-center justify-center h-full">
                    <FolderIcon class="h-8 w-8 text-gray-400" />
                  </div>
                  <div class="absolute inset-0 bg-black bg-opacity-0 hover:bg-opacity-10 transition-all"></div>
                  <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-50 text-white p-3">
                    <h3 class="font-medium text-sm truncate">{{ album.title }}</h3>
                    <p class="text-xs opacity-75">{{ album.images_count }} images</p>
                  </div>
                </Link>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Images -->
        <div v-if="publicImages.length > 0" class="bg-white shadow-sm rounded-lg">
          <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Recent Images</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
              <div v-for="image in publicImages" :key="image.id" class="relative bg-gray-100 rounded-lg overflow-hidden aspect-square hover:shadow-md transition-shadow">
                <Link :href="route('images.show', image.slug)">
                  <img
                    :src="getImageUrl(image)"
                    :alt="image.alt_text || image.title"
                    class="w-full h-full object-cover hover:scale-105 transition-transform"
                  />
                </Link>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="publicAlbums.length === 0 && publicImages.length === 0" class="bg-white shadow-sm rounded-lg">
          <div class="p-12 text-center">
            <PhotoIcon class="mx-auto h-12 w-12 text-gray-400 mb-4" />
            <h3 class="text-lg font-medium text-gray-900 mb-2">No Public Content</h3>
            <p class="text-gray-600">
              {{ isOwnProfile ? "You haven't published any albums or images yet." : "This user hasn't shared any public content yet." }}
            </p>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import route from 'ziggy-js'
import { FolderIcon, PhotoIcon } from '@heroicons/vue/24/outline'

import AppLayout from '@/Layouts/AppLayout.vue'
import UserAvatar from '@/Components/UserAvatar.vue'

const props = defineProps({
  profileUser: Object,
  publicAlbums: Array,
  publicImages: Array,
  stats: Object,
  isOwnProfile: Boolean,
})

const hasSocialLinks = computed(() => {
  const links = props.profileUser.social_links
  return links && (links.twitter || links.instagram || links.linkedin)
})

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  })
}

const formatNumber = (number) => {
  if (number >= 1000000) {
    return (number / 1000000).toFixed(1) + 'M'
  } else if (number >= 1000) {
    return (number / 1000).toFixed(1) + 'K'
  }
  return number.toString()
}

const getImageUrl = (image) => {
  if (image.storage_path) {
    return `http://localhost:9000/gallery-images/${image.storage_path}`
  }
  return '/images/placeholder.jpg'
}

const getSocialUrl = (platform, value) => {
  if (value.startsWith('http')) return value
  
  const baseUrls = {
    twitter: 'https://twitter.com/',
    instagram: 'https://instagram.com/',
    linkedin: 'https://linkedin.com/in/',
  }
  
  const cleanValue = value.replace('@', '')
  return baseUrls[platform] + cleanValue
}
</script>
