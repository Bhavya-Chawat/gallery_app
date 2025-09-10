<template>
  <GuestLayout>
    <!-- Hero Section -->
    <div class="relative bg-white overflow-hidden">
      <div class="max-w-7xl mx-auto">
        <div class="relative z-10 pb-8 bg-white sm:pb-16 md:pb-20 lg:max-w-2xl lg:w-full lg:pb-28 xl:pb-32">
          <svg
            class="hidden lg:block absolute right-0 inset-y-0 h-full w-48 text-white transform translate-x-1/2"
            fill="currentColor"
            viewBox="0 0 100 100"
            preserveAspectRatio="none"
            aria-hidden="true"
          >
            <polygon points="50,0 100,0 50,100 0,100" />
          </svg>

          <main class="mt-10 mx-auto max-w-7xl px-4 sm:mt-12 sm:px-6 md:mt-16 lg:mt-20 lg:px-8 xl:mt-28">
            <div class="sm:text-center lg:text-left">
              <h1 class="text-4xl tracking-tight font-extrabold text-gray-900 sm:text-5xl md:text-6xl">
                <span class="block xl:inline">Modern Image</span>
                <span class="block text-blue-600 xl:inline">Gallery</span>
              </h1>
              <p class="mt-3 text-base text-gray-500 sm:mt-5 sm:text-lg sm:max-w-xl sm:mx-auto md:mt-5 md:text-xl lg:mx-0">
                Discover, share, and organize your photos in a beautiful, responsive gallery. 
                Upload, manage, and showcase your images with powerful features and seamless organization.
              </p>
              <div class="mt-5 sm:mt-8 sm:flex sm:justify-center lg:justify-start">
                <div class="rounded-md shadow">
                  <Link
                    :href="route('gallery.index')"
                    class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 md:py-4 md:text-lg md:px-10"
                  >
                    Browse Gallery
                  </Link>
                </div>
                <div class="mt-3 sm:mt-0 sm:ml-3">
                  <Link
                    v-if="canRegister"
                    :href="route('register')"
                    class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 md:py-4 md:text-lg md:px-10"
                  >
                    Get Started
                  </Link>
                </div>
              </div>
            </div>
          </main>
        </div>
      </div>
      
      <!-- Hero Image -->
      <div class="lg:absolute lg:inset-y-0 lg:right-0 lg:w-1/2">
        <div class="h-56 w-full sm:h-72 md:h-96 lg:w-full lg:h-full bg-gradient-to-br from-blue-400 to-purple-600 flex items-center justify-center">
          <div class="text-white text-center">
            <PhotoIcon class="mx-auto h-24 w-24 mb-4 opacity-80" />
            <p class="text-lg">Beautiful Gallery Preview</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Stats Section -->
    <div class="bg-blue-50">
      <div class="max-w-7xl mx-auto py-12 px-4 sm:py-16 sm:px-6 lg:px-8 lg:py-20">
        <div class="max-w-4xl mx-auto text-center">
          <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
            Trusted by photographers worldwide
          </h2>
          <p class="mt-3 text-xl text-gray-500 sm:mt-4">
            Join thousands of users sharing their visual stories
          </p>
        </div>
        <dl class="mt-10 text-center sm:max-w-3xl sm:mx-auto sm:grid sm:grid-cols-3 sm:gap-8">
          <div class="flex flex-col">
            <dt class="order-2 mt-2 text-lg leading-6 font-medium text-gray-500">
              Images Shared
            </dt>
            <dd class="order-1 text-5xl font-extrabold text-blue-600">
              {{ formatNumber(stats.total_images) }}
            </dd>
          </div>
          <div class="flex flex-col mt-10 sm:mt-0">
            <dt class="order-2 mt-2 text-lg leading-6 font-medium text-gray-500">
              Albums Created
            </dt>
            <dd class="order-1 text-5xl font-extrabold text-blue-600">
              {{ formatNumber(stats.total_albums) }}
            </dd>
          </div>
          <div class="flex flex-col mt-10 sm:mt-0">
            <dt class="order-2 mt-2 text-lg leading-6 font-medium text-gray-500">
              Active Users
            </dt>
            <dd class="order-1 text-5xl font-extrabold text-blue-600">
              {{ formatNumber(stats.total_users) }}
            </dd>
          </div>
        </dl>
      </div>
    </div>

    <!-- Featured Images Section -->
    <div class="bg-white" v-if="featuredImages.length > 0">
      <div class="max-w-7xl mx-auto py-12 px-4 sm:py-16 sm:px-6 lg:px-8">
        <div class="text-center">
          <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
            Featured Images
          </h2>
          <p class="mt-3 max-w-2xl mx-auto text-xl text-gray-500 sm:mt-4">
            Discover amazing photography from our community
          </p>
        </div>
        
        <div class="mt-12 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
          <div
            v-for="image in featuredImages"
            :key="image.id"
            class="group relative aspect-square bg-gray-100 rounded-lg overflow-hidden hover:shadow-lg transition-shadow"
          >
            <img
              :src="getImageUrl(image, 'medium')"
              :alt="image.alt_text || image.title"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
            >
            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-300">
              <div class="absolute bottom-0 left-0 right-0 p-4 text-white transform translate-y-full group-hover:translate-y-0 transition-transform duration-300">
                <h3 class="font-medium truncate">{{ image.title || 'Untitled' }}</h3>
                <p class="text-sm opacity-75">by {{ image.owner?.name }}</p>
              </div>
            </div>
            <Link
              :href="route('images.show', image.slug)"
              class="absolute inset-0"
            >
              <span class="sr-only">View {{ image.title }}</span>
            </Link>
          </div>
        </div>

        <div class="mt-12 text-center">
          <Link
            :href="route('gallery.index')"
            class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200"
          >
            View All Images
            <ArrowRightIcon class="ml-2 h-5 w-5" />
          </Link>
        </div>
      </div>
    </div>

    <!-- Featured Albums Section -->
    <div class="bg-gray-50" v-if="featuredAlbums.length > 0">
      <div class="max-w-7xl mx-auto py-12 px-4 sm:py-16 sm:px-6 lg:px-8">
        <div class="text-center">
          <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
            Featured Albums
          </h2>
          <p class="mt-3 max-w-2xl mx-auto text-xl text-gray-500 sm:mt-4">
            Curated collections from talented photographers
          </p>
        </div>
        
        <div class="mt-12 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
          <div
            v-for="album in featuredAlbums"
            :key="album.id"
            class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow overflow-hidden"
          >
            <div class="aspect-w-16 aspect-h-9 bg-gray-100">
              <img
                v-if="album.cover_image"
                :src="getImageUrl(album.cover_image, 'medium')"
                :alt="album.title"
                class="w-full h-full object-cover"
              >
              <div v-else class="flex items-center justify-center">
                <FolderIcon class="h-12 w-12 text-gray-400" />
              </div>
            </div>
            <div class="p-6">
              <h3 class="text-lg font-medium text-gray-900 truncate">
                {{ album.title }}
              </h3>
              <p class="mt-1 text-sm text-gray-500 line-clamp-2">
                {{ album.description }}
              </p>
              <div class="mt-4 flex items-center justify-between">
                <span class="text-sm text-gray-500">
                  {{ album.images_count }} image{{ album.images_count !== 1 ? 's' : '' }}
                </span>
                <Link
                  :href="route('albums.show', album.slug)"
                  class="text-sm font-medium text-blue-600 hover:text-blue-500"
                >
                  View Album
                </Link>
              </div>
            </div>
          </div>
        </div>

        <div class="mt-12 text-center">
          <Link
            :href="route('albums.index')"
            class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200"
          >
            Browse Albums
            <ArrowRightIcon class="ml-2 h-5 w-5" />
          </Link>
        </div>
      </div>
    </div>

    <!-- CTA Section -->
    <div class="bg-blue-600">
      <div class="max-w-7xl mx-auto py-12 px-4 sm:py-16 sm:px-6 lg:px-8 lg:flex lg:items-center lg:justify-between">
        <h2 class="text-3xl font-extrabold tracking-tight text-white sm:text-4xl">
          <span class="block">Ready to share your photos?</span>
          <span class="block text-blue-200">Start your gallery today.</span>
        </h2>
        <div class="mt-8 flex lg:mt-0 lg:flex-shrink-0">
          <div class="inline-flex rounded-md shadow">
            <Link
              v-if="canRegister"
              :href="route('register')"
              class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-blue-600 bg-white hover:bg-blue-50"
            >
              Sign up free
            </Link>
          </div>
          <div class="ml-3 inline-flex rounded-md shadow">
            <Link
              :href="canLogin ? route('login') : route('gallery.index')"
              class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-500 hover:bg-blue-400"
            >
              {{ canLogin ? 'Sign in' : 'Browse Gallery' }}
            </Link>
          </div>
        </div>
      </div>
    </div>
  </GuestLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import route from 'ziggy-js'
import {
  PhotoIcon,
  FolderIcon,
  ArrowRightIcon,
} from '@heroicons/vue/24/outline'

import GuestLayout from '@/Layouts/GuestLayout.vue'

const props = defineProps({
  canLogin: Boolean,
  canRegister: Boolean,
  featuredImages: {
    type: Array,
    default: () => [],
  },
  featuredAlbums: {
    type: Array,
    default: () => [],
  },
  stats: {
    type: Object,
    default: () => ({
      total_images: 0,
      total_albums: 0,
      total_users: 0,
    }),
  },
})

const formatNumber = (number) => {
  if (number >= 1000000) {
    return (number / 1000000).toFixed(1) + 'M'
  } else if (number >= 1000) {
    return (number / 1000).toFixed(1) + 'K'
  }
  return number.toString()
}

const getImageUrl = (image, variant = 'medium') => {
  if (image.versions) {
    const version = image.versions.find(v => v.variant === variant)
    if (version) return version.url
  }
  return image.url || image.storage_path
}
</script>
