<template>
  <AppLayout>
    <Head :title="profileUser.name + ' - Profile'" />

    <!-- Animated Background -->
    <div class="fixed inset-0 -z-10 overflow-hidden">
      <!-- Animated Mesh Background -->
      <svg class="absolute inset-0 w-full h-full opacity-30" viewBox="0 0 100 100" preserveAspectRatio="none">
        <defs>
          <linearGradient id="meshGradient" x1="0%" y1="0%" x2="100%" y2="100%" gradientTransform="rotate(0)">
            <stop offset="0%" stop-color="#8b5cf6" class="animate-gradient-shift">
              <animate attributeName="stop-color" values="#8b5cf6;#06b6d4;#8b5cf6" dur="8s" repeatCount="indefinite" />
            </stop>
            <stop offset="50%" stop-color="#06b6d4" class="animate-gradient-shift">
              <animate attributeName="stop-color" values="#06b6d4;#a855f7;#06b6d4" dur="8s" repeatCount="indefinite" />
            </stop>
            <stop offset="100%" stop-color="#a855f7" class="animate-gradient-shift">
              <animate attributeName="stop-color" values="#a855f7;#8b5cf6;#a855f7" dur="8s" repeatCount="indefinite" />
            </stop>
          </linearGradient>
        </defs>
        <path d="M0,0 Q25,25 50,0 T100,0 L100,25 Q75,50 50,25 T0,25 Z" fill="url(#meshGradient)" opacity="0.1">
          <animateTransform attributeName="transform" attributeType="XML" type="translate" values="0,0;10,-5;0,0" dur="6s" repeatCount="indefinite" />
        </path>
        <path d="M0,25 Q25,50 50,25 T100,25 L100,50 Q75,75 50,50 T0,50 Z" fill="url(#meshGradient)" opacity="0.15">
          <animateTransform attributeName="transform" attributeType="XML" type="translate" values="0,0;-8,3;0,0" dur="7s" repeatCount="indefinite" />
        </path>
        <path d="M0,50 Q25,75 50,50 T100,50 L100,75 Q75,100 50,75 T0,75 Z" fill="url(#meshGradient)" opacity="0.1">
          <animateTransform attributeName="transform" attributeType="XML" type="translate" values="0,0;5,8;0,0" dur="9s" repeatCount="indefinite" />
        </path>
      </svg>

      <!-- Floating Particles -->
      <div class="absolute inset-0">
        <div v-for="i in 20" :key="i" 
             class="absolute w-2 h-2 rounded-full opacity-40 animate-float"
             :class="getParticleClass(i)"
             :style="getParticleStyle(i)">
        </div>
      </div>

      <!-- Gradient Background -->
      <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900"></div>
    </div>

    <div class="min-h-screen py-12 relative">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-8 animate-fade-in-up">
        
        <!-- Profile Header -->
        <div class="glass-card rounded-2xl border border-white/20 shadow-2xl shadow-violet-500/10 hover:shadow-violet-500/20 transition-all duration-500 animate-fade-in-up" style="animation-delay: 0.1s">
          <div class="p-8">
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-8">
                <div class="relative group">
                  <div class="absolute inset-0 rounded-full bg-gradient-to-r from-violet-500 to-cyan-500 opacity-75 group-hover:opacity-100 blur-sm group-hover:blur-md transition-all duration-300"></div>
                  <UserAvatar :user="profileUser" size="xl" class="relative z-10 ring-4 ring-white/20 hover:ring-white/40 transition-all duration-300" />
                </div>
                <div class="space-y-2">
                  <h1 class="text-4xl font-bold gradient-text animate-shimmer">{{ profileUser.name }}</h1>
                  <p v-if="profileUser.email" class="text-slate-300 text-lg">{{ profileUser.email }}</p>
                  <p class="text-slate-400 text-sm flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 4v6a2 2 0 002 2h4a2 2 0 002-2v-6"></path>
                    </svg>
                    Member since {{ formatDate(profileUser.created_at) }}
                  </p>
                </div>
              </div>
              <div v-if="isOwnProfile" class="text-right">
                <Link :href="route('profile.edit')" class="premium-button group">
                  <svg class="w-4 h-4 mr-2 group-hover:rotate-12 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                  </svg>
                  Edit Profile
                </Link>
              </div>
            </div>

            <!-- Bio -->
            <div v-if="profileUser.bio" class="mt-6 p-4 glass-card-inner rounded-xl">
              <p class="text-slate-200 leading-relaxed">{{ profileUser.bio }}</p>
            </div>

            <!-- Links -->
            <div v-if="profileUser.website || hasSocialLinks" class="mt-6 flex flex-wrap gap-4">
              <a v-if="profileUser.website" :href="profileUser.website" target="_blank" rel="noopener" class="social-link group">
                <svg class="w-5 h-5 mr-2 group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                </svg>
                Website
              </a>
              <a v-if="profileUser.social_links?.twitter" :href="getSocialUrl('twitter', profileUser.social_links.twitter)" target="_blank" rel="noopener" class="social-link group hover:text-blue-400">
                <svg class="w-5 h-5 mr-2 group-hover:scale-110 transition-transform duration-300" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                </svg>
                Twitter
              </a>
              <a v-if="profileUser.social_links?.instagram" :href="getSocialUrl('instagram', profileUser.social_links.instagram)" target="_blank" rel="noopener" class="social-link group hover:text-pink-500">
                <svg class="w-5 h-5 mr-2 group-hover:scale-110 transition-transform duration-300" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                </svg>
                Instagram
              </a>
              <a v-if="profileUser.social_links?.linkedin" :href="getSocialUrl('linkedin', profileUser.social_links.linkedin)" target="_blank" rel="noopener" class="social-link group hover:text-blue-600">
                <svg class="w-5 h-5 mr-2 group-hover:scale-110 transition-transform duration-300" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                </svg>
                LinkedIn
              </a>
            </div>
          </div>
        </div>

        <!-- Statistics -->
        <div v-if="stats && Object.keys(stats).length > 0" class="glass-card rounded-2xl border border-white/20 shadow-2xl shadow-cyan-500/10 hover:shadow-cyan-500/20 transition-all duration-500 animate-fade-in-up" style="animation-delay: 0.2s">
          <div class="p-8">
            <h2 class="text-2xl font-bold gradient-text mb-6 flex items-center animate-shimmer">
              <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
              </svg>
              Statistics
            </h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
              <div class="stat-card group">
                <div class="text-3xl font-bold gradient-text group-hover:scale-110 transition-transform duration-300">{{ stats.images || 0 }}</div>
                <div class="text-slate-300 text-sm font-medium flex items-center mt-2">
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                  </svg>
                  Images
                </div>
              </div>
              <div class="stat-card group">
                <div class="text-3xl font-bold gradient-text group-hover:scale-110 transition-transform duration-300">{{ stats.albums || 0 }}</div>
                <div class="text-slate-300 text-sm font-medium flex items-center mt-2">
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                  </svg>
                  Albums
                </div>
              </div>
              <div class="stat-card group">
                <div class="text-3xl font-bold gradient-text group-hover:scale-110 transition-transform duration-300">{{ formatNumber(stats.views || 0) }}</div>
                <div class="text-slate-300 text-sm font-medium flex items-center mt-2">
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                  </svg>
                  Total Views
                </div>
              </div>
              <div class="stat-card group">
                <div class="text-3xl font-bold gradient-text group-hover:scale-110 transition-transform duration-300">{{ formatNumber(stats.likes || 0) }}</div>
                <div class="text-slate-300 text-sm font-medium flex items-center mt-2">
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                  </svg>
                  Total Likes
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Albums -->
        <div v-if="publicAlbums.length > 0" class="glass-card rounded-2xl border border-white/20 shadow-2xl shadow-purple-500/10 hover:shadow-purple-500/20 transition-all duration-500 animate-fade-in-up" style="animation-delay: 0.3s">
          <div class="p-8">
            <h2 class="text-2xl font-bold gradient-text mb-6 flex items-center animate-shimmer">
              <FolderIcon class="h-6 w-6 mr-3" />
              Recent Albums
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              <div v-for="(album, index) in publicAlbums" :key="album.id" 
                   class="album-card group animate-fade-in-up"
                   :style="`animation-delay: ${0.1 * index}s`">
                <Link :href="route('albums.show', album.slug)" class="block">
                  <div class="relative overflow-hidden rounded-xl aspect-video">
                    <img
                      v-if="album.cover_image"
                      :src="getImageUrl(album.cover_image)"
                      :alt="album.title"
                      class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                    />
                    <div v-else class="flex items-center justify-center h-full bg-gradient-to-br from-slate-700 to-slate-800">
                      <FolderIcon class="h-12 w-12 text-slate-400" />
                    </div>
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-all duration-300"></div>
                    <div class="absolute bottom-0 left-0 right-0 p-4 transform translate-y-2 group-hover:translate-y-0 transition-transform duration-300">
                      <h3 class="font-bold text-white text-lg mb-1 truncate">{{ album.title }}</h3>
                      <p class="text-slate-300 text-sm flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        {{ album.images_count }} images
                      </p>
                    </div>
                  </div>
                </Link>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Images -->
        <div v-if="publicImages.length > 0" class="glass-card rounded-2xl border border-white/20 shadow-2xl shadow-emerald-500/10 hover:shadow-emerald-500/20 transition-all duration-500 animate-fade-in-up" style="animation-delay: 0.4s">
          <div class="p-8">
            <h2 class="text-2xl font-bold gradient-text mb-6 flex items-center animate-shimmer">
              <PhotoIcon class="h-6 w-6 mr-3" />
              Recent Images
            </h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
              <div v-for="(image, index) in publicImages" :key="image.id" 
                   class="image-card"
                   :style="`animation-delay: ${0.05 * index}s`">
                <Link :href="route('images.show', image.slug)" class="block">
                  <div class="relative overflow-hidden rounded-lg aspect-square">
                    <img
                      :src="getImageUrl(image)"
                      :alt="image.alt_text || image.title"
                      class="w-full h-full object-cover hover:scale-110 transition-transform duration-500"
                    />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 hover:opacity-100 transition-all duration-300"></div>
                  </div>
                </Link>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="publicAlbums.length === 0 && publicImages.length === 0" class="glass-card rounded-2xl border border-white/20 shadow-2xl animate-fade-in-up" style="animation-delay: 0.5s">
          <div class="p-16 text-center">
            <div class="relative mb-6">
              <div class="absolute inset-0 bg-gradient-to-r from-violet-500 to-cyan-500 opacity-20 blur-2xl rounded-full"></div>
              <PhotoIcon class="relative mx-auto h-16 w-16 text-slate-400" />
            </div>
            <h3 class="text-2xl font-bold gradient-text mb-4 animate-shimmer">No Public Content</h3>
            <p class="text-slate-300 text-lg max-w-md mx-auto leading-relaxed">
              {{ isOwnProfile ? "You haven't published any albums or images yet. Start sharing your amazing content!" : "This user hasn't shared any public content yet. Check back later!" }}
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

const getParticleClass = (index) => {
  const colors = ['bg-violet-500', 'bg-cyan-500', 'bg-purple-500', 'bg-emerald-500', 'bg-pink-500']
  return colors[index % colors.length]
}

const getParticleStyle = (index) => {
  const positions = [
    'top: 10%; left: 10%;',
    'top: 20%; left: 80%;',
    'top: 30%; left: 15%;',
    'top: 40%; left: 75%;',
    'top: 50%; left: 20%;',
    'top: 60%; left: 85%;',
    'top: 70%; left: 10%;',
    'top: 80%; left: 70%;',
    'top: 15%; left: 45%;',
    'top: 25%; left: 60%;',
    'top: 35%; left: 30%;',
    'top: 45%; left: 90%;',
    'top: 55%; left: 5%;',
    'top: 65%; left: 55%;',
    'top: 75%; left: 25%;',
    'top: 85%; left: 40%;',
    'top: 5%; left: 65%;',
    'top: 95%; left: 35%;',
    'top: 12%; left: 95%;',
    'top: 88%; left: 50%;'
  ]
  return positions[index - 1] + `animation-duration: ${3 + (index % 4)}s; animation-delay: ${index * 0.2}s;`
}
</script>

<style scoped>
/* Glass morphism effects */
.glass-card {
  @apply bg-white/5 backdrop-blur-xl border border-white/20;
}

.glass-card-inner {
  @apply bg-white/5 backdrop-blur-sm border border-white/10;
}

/* Gradient text effects */
.gradient-text {
  @apply bg-clip-text text-transparent bg-gradient-to-r from-violet-400 via-cyan-400 to-purple-400;
}

/* Shimmer animation */
.animate-shimmer {
  background-size: 200% 200%;
  animation: shimmer 3s ease-in-out infinite;
}

@keyframes shimmer {
  0%, 100% {
    background-position: 0% 50%;
  }
  50% {
    background-position: 100% 50%;
  }
}

/* Premium button styles */
.premium-button {
  @apply inline-flex items-center px-6 py-3 bg-gradient-to-r from-violet-600 to-purple-600 
         border border-violet-500/50 rounded-xl font-semibold text-sm text-white 
         uppercase tracking-wide hover:from-violet-500 hover:to-purple-500 
         hover:scale-105 hover:shadow-lg hover:shadow-violet-500/25 
         transition-all duration-300 backdrop-blur-sm;
}

/* Social links */
.social-link {
  @apply inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-sm 
         border border-white/20 rounded-lg text-slate-300 font-medium text-sm
         hover:bg-white/20 hover:border-white/30 hover:text-white hover:scale-105
         transition-all duration-300;
}

/* Stat cards */
.stat-card {
  @apply text-center p-6 bg-white/5 backdrop-blur-sm border border-white/10 
         rounded-xl hover:bg-white/10 hover:border-white/20 hover:scale-105 
         transition-all duration-300 cursor-pointer;
}

/* Album cards */
.album-card {
  @apply bg-white/5 backdrop-blur-sm border border-white/10 rounded-xl 
         hover:bg-white/10 hover:border-white/20 hover:scale-105 hover:shadow-xl
         hover:shadow-violet-500/10 transition-all duration-300 overflow-hidden;
}

/* Image cards */
.image-card {
  @apply bg-white/5 backdrop-blur-sm border border-white/10 rounded-lg 
         hover:bg-white/10 hover:border-white/20 hover:scale-105 hover:shadow-lg
         hover:shadow-cyan-500/10 transition-all duration-300 overflow-hidden;
}

/* Floating animation */
.animate-float {
  animation: float 6s ease-in-out infinite;
}

@keyframes float {
  0%, 100% {
    transform: translateY(0px) rotate(0deg);
  }
  25% {
    transform: translateY(-10px) rotate(90deg);
  }
  50% {
    transform: translateY(-20px) rotate(180deg);
  }
  75% {
    transform: translateY(-10px) rotate(270deg);
  }
}

/* Fade in up animation */
.animate-fade-in-up {
  opacity: 0;
  transform: translateY(30px);
  animation: fadeInUp 0.8s ease-out forwards;
}

@keyframes fadeInUp {
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Gradient shift for mesh */
.animate-gradient-shift {
  animation: gradientShift 8s ease-in-out infinite;
}

@keyframes gradientShift {
  0%, 100% {
    stop-color: #8b5cf6;
  }
  33% {
    stop-color: #06b6d4;
  }
  66% {
    stop-color: #a855f7;
  }
}

/* Custom scrollbar */
::-webkit-scrollbar {
  width: 8px;
}

::-webkit-scrollbar-track {
  @apply bg-slate-800/50;
}

::-webkit-scrollbar-thumb {
  @apply bg-gradient-to-b from-violet-500 to-purple-500 rounded-full;
}

::-webkit-scrollbar-thumb:hover {
  @apply from-violet-400 to-purple-400;
}
</style>