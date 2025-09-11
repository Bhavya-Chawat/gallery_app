<template>
  <Head title="Welcome" />
  <AppLayout>
    <!-- Hero Section -->
    <section class="relative overflow-hidden bg-gradient-to-br from-blue-50 via-white to-cyan-50 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900">
      <!-- Background Pattern -->
      <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHZpZXdCb3g9IjAgMCAyMCAyMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KICA8ZGVmcz4KICAgIDxwYXR0ZXJuIGlkPSJncmlkIiB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHBhdHRlcm5Vbml0cz0idXNlclNwYWNlT25Vc2UiPgogICAgICA8cGF0aCBkPSJNIDIwIDAgTCAwIDAgMCAyMCIgZmlsbD0ibm9uZSIgc3Ryb2tlPSIjZTVlN2ViIiBzdHJva2Utd2lkdGg9IjEiLz4KICAgIDwvcGF0dGVybj4KICA8L2RlZnM+CiAgPHJlY3Qgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgZmlsbD0idXJsKCNncmlkKSIvPgo8L3N2Zz4=')] opacity-30 dark:opacity-10" />
      
      <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 lg:py-32">
        <div class="text-center">
          <!-- Hero Content -->
          <h1 class="text-4xl sm:text-6xl lg:text-7xl font-bold">
            <span class="bg-gradient-to-r from-blue-600 via-cyan-500 to-blue-400 bg-clip-text text-transparent">
              Discover
            </span>
            <span class="block text-gray-900 dark:text-white mt-2">
              Amazing Photography
            </span>
          </h1>
          
          <p class="mt-6 max-w-2xl mx-auto text-xl text-gray-600 dark:text-gray-300 leading-relaxed">
            Explore a curated collection of stunning images from talented photographers around the world. 
            Share your own masterpieces and connect with a creative community.
          </p>

          <!-- CTA Buttons -->
          <div class="mt-10 flex flex-col sm:flex-row gap-4 justify-center">
            <Button
              v-if="!$page.props.auth?.user"
              href="/register"
              variant="primary"
              size="lg"
              class="px-8 py-4"
            >
              <template #icon-left>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
              </template>
              Get Started Free
            </Button>
            
            <Button
              href="/gallery"
              variant="outline"
              size="lg"
              class="px-8 py-4"
            >
              <template #icon-left>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
              </template>
              Explore Gallery
            </Button>
          </div>

          <!-- Stats -->
          <div v-if="stats" class="mt-16 grid grid-cols-2 md:grid-cols-4 gap-8 max-w-2xl mx-auto">
            <div class="text-center">
              <div class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-cyan-500 bg-clip-text text-transparent">
                {{ formatNumber(stats.images_count) }}
              </div>
              <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">Images</div>
            </div>
            <div class="text-center">
              <div class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-cyan-500 bg-clip-text text-transparent">
                {{ formatNumber(stats.albums_count) }}
              </div>
              <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">Albums</div>
            </div>
            <div class="text-center">
              <div class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-cyan-500 bg-clip-text text-transparent">
                {{ formatNumber(stats.users_count) }}
              </div>
              <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">Artists</div>
            </div>
            <div class="text-center">
              <div class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-cyan-500 bg-clip-text text-transparent">
                {{ formatNumber(stats.views_count) }}
              </div>
              <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">Views</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Featured Collections -->
    <section v-if="publicCollections && publicCollections.length > 0" class="py-16 bg-white dark:bg-slate-800">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
          <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">
            Featured Collections
          </h2>
          <p class="text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
            Discover curated collections of exceptional photography from our community of talented artists.
          </p>
        </div>

        <!-- Collections Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <div
            v-for="collection in publicCollections"
            :key="collection.id"
            class="group cursor-pointer"
            @click="$inertia.visit(`/collections/${collection.slug || collection.id}`)"
          >
            <div class="relative bg-white dark:bg-slate-700 rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 transform group-hover:scale-105">
              <!-- Collection Cover -->
              <div class="aspect-video overflow-hidden bg-gray-100 dark:bg-slate-600">
                <img
                  v-if="collection.cover_image_url"
                  :src="collection.cover_image_url"
                  :alt="collection.title"
                  class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                />
                <div v-else class="w-full h-full flex items-center justify-center">
                  <svg class="w-12 h-12 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                  </svg>
                </div>
              </div>

              <!-- Collection Info -->
              <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                  {{ collection.title }}
                </h3>
                <p v-if="collection.description" class="text-gray-600 dark:text-gray-400 text-sm mb-3 line-clamp-2">
                  {{ collection.description }}
                </p>
                <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
                  <span>{{ collection.images_count || 0 }} images</span>
                  <span v-if="collection.curator?.name">by {{ collection.curator.name }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Featured Albums -->
    <section v-if="featuredAlbums && featuredAlbums.length > 0" class="py-16 bg-gray-50 dark:bg-slate-900">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
          <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">
            Popular Albums
          </h2>
          <p class="text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
            Browse through the most loved photo albums from our creative community.
          </p>
        </div>

        <!-- Albums Carousel/Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          <div
            v-for="album in featuredAlbums"
            :key="album.id"
            class="group cursor-pointer"
            @click="$inertia.visit(`/albums/${album.slug || album.id}`)"
          >
            <div class="bg-white dark:bg-slate-800 rounded-lg overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300">
              <!-- Album Cover -->
              <div class="aspect-square overflow-hidden bg-gray-100 dark:bg-slate-700">
                <img
                  v-if="album.cover_image_url"
                  :src="album.cover_image_url"
                  :alt="album.title"
                  class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                />
                <div v-else class="w-full h-full flex items-center justify-center">
                  <svg class="w-8 h-8 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                  </svg>
                </div>
              </div>

              <!-- Album Info -->
              <div class="p-4">
                <h3 class="font-medium text-gray-900 dark:text-white truncate group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                  {{ album.title }}
                </h3>
                <div class="flex items-center justify-between mt-2 text-sm text-gray-500 dark:text-gray-400">
                  <span>{{ album.images_count || 0 }} photos</span>
                  <span v-if="album.owner?.name" class="truncate ml-2">{{ album.owner.name }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- View All Albums Button -->
        <div class="text-center mt-12">
          <Button href="/albums" variant="outline" size="lg">
            View All Albums
            <template #icon-right>
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
              </svg>
            </template>
          </Button>
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section class="py-16 bg-white dark:bg-slate-800">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
          <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">
            Why Choose Our Gallery?
          </h2>
          <p class="text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
            Experience the best way to share, discover, and organize your photography with powerful features designed for creators.
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <!-- Feature 1 -->
          <div class="text-center group">
            <div class="w-16 h-16 bg-gradient-to-r from-blue-600 via-cyan-500 to-blue-400 rounded-xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-200">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
              </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">High-Quality Uploads</h3>
            <p class="text-gray-600 dark:text-gray-400">Upload your photos in full resolution with automatic optimization and multiple format support.</p>
          </div>

          <!-- Feature 2 -->
          <div class="text-center group">
            <div class="w-16 h-16 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 rounded-xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-200">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
              </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Smart Organization</h3>
            <p class="text-gray-600 dark:text-gray-400">Organize photos into albums and collections with AI-powered tagging and metadata management.</p>
          </div>

          <!-- Feature 3 -->
          <div class="text-center group">
            <div class="w-16 h-16 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-200">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Community & Social</h3>
            <p class="text-gray-600 dark:text-gray-400">Connect with fellow photographers, share feedback, and discover inspiring work from around the world.</p>
          </div>

          <!-- Feature 4 -->
          <div class="text-center group">
            <div class="w-16 h-16 bg-gradient-to-r from-orange-500 to-red-500 rounded-xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-200">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
              </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Privacy & Security</h3>
            <p class="text-gray-600 dark:text-gray-400">Control who sees your work with granular privacy settings and secure cloud storage.</p>
          </div>

          <!-- Feature 5 -->
          <div class="text-center group">
            <div class="w-16 h-16 bg-gradient-to-r from-violet-500 to-purple-500 rounded-xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-200">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
              </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Analytics & Insights</h3>
            <p class="text-gray-600 dark:text-gray-400">Track views, likes, and engagement to understand what resonates with your audience.</p>
          </div>

          <!-- Feature 6 -->
          <div class="text-center group">
            <div class="w-16 h-16 bg-gradient-to-r from-cyan-500 to-blue-500 rounded-xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-200">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
              </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Mobile Optimized</h3>
            <p class="text-gray-600 dark:text-gray-400">Access your gallery anywhere with our responsive design and mobile-first approach.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 bg-gradient-to-br from-blue-600 via-cyan-500 to-blue-400">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl font-bold text-white mb-6">
          Ready to Share Your Vision?
        </h2>
        <p class="text-xl text-blue-100 mb-8 max-w-2xl mx-auto">
          Join thousands of photographers already showcasing their work and building their audience.
        </p>
        
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
          <Button
            v-if="!$page.props.auth?.user"
            href="/register"
            variant="secondary"
            size="lg"
            class="bg-white text-blue-600 hover:bg-gray-50"
          >
            Start Your Journey
          </Button>
          <Button
            v-else
            href="/upload"
            variant="secondary"
            size="lg"
            class="bg-white text-blue-600 hover:bg-gray-50"
          >
            Upload Your First Photo
          </Button>
          
          <Button
            href="/gallery"
            variant="outline"
            size="lg"
            class="border-white text-white hover:bg-white/10"
          >
            Explore Gallery
          </Button>
        </div>
      </div>
    </section>
  </AppLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Head } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import Button from '@/Components/UI/Button.vue'  // Fix case sensitivity

// Add proper prop definitions with types
const props = defineProps({
  featuredAlbums: {
    type: Array,
    required: false,
    default: () => []
  },
  publicCollections: {
    type: Array,
    required: false,
    default: () => []
  },
  stats: {
    type: Object,
    required: false,
    default: () => ({
      images_count: 0,
      albums_count: 0,
      users_count: 0,
      views_count: 0
    })
  }
})

// Add proper type safety to formatNumber
const formatNumber = (num) => {
  if (!num || isNaN(num)) return '0'
  
  const n = Number(num)
  if (n >= 1000000) {
    return (n / 1000000).toFixed(1) + 'M'
  }
  if (n >= 1000) {
    return (n / 1000).toFixed(1) + 'K'
  }
  return n.toString()
}
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>