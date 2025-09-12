<template>
  <!-- <GuestLayout> -->
    <div style="margin: 0; padding: 0;"></div>
    <!-- Enhanced Animated Background -->
    <div class="fixed inset-0 overflow-hidden">
      <!-- Base Background -->
      <div class="absolute inset-0 bg-slate-900">
        <!-- Animated Mesh Background -->
        <svg class="absolute inset-0 w-full h-full opacity-20" viewBox="0 0 100 100" preserveAspectRatio="none">
          <defs>
            <linearGradient id="mesh-gradient" x1="0%" y1="0%" x2="100%" y2="100%">
              <stop offset="0%" class="text-violet-500">
                <animate attributeName="stop-color" 
                  values="#8b5cf6;#06b6d4;#a855f7;#8b5cf6" 
                  dur="8s" 
                  repeatCount="indefinite" />
              </stop>
              <stop offset="100%" class="text-cyan-500">
                <animate attributeName="stop-color" 
                  values="#06b6d4;#a855f7;#8b5cf6;#06b6d4" 
                  dur="8s" 
                  repeatCount="indefinite" />
              </stop>
            </linearGradient>
          </defs>
          <path d="M0,0 L100,20 L100,100 L0,80 Z" fill="url(#mesh-gradient)" opacity="0.1">
            <animateTransform 
              attributeName="transform" 
              type="translate" 
              values="0,0;10,-5;0,0" 
              dur="10s" 
              repeatCount="indefinite" />
          </path>
        </svg>
        
        <!-- Floating Orbs -->
        <div class="absolute top-20 left-10 w-72 h-72 bg-gradient-to-r from-violet-500/20 to-purple-500/20 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute top-40 right-20 w-96 h-96 bg-gradient-to-l from-cyan-500/15 to-violet-500/15 rounded-full blur-3xl animate-pulse delay-700"></div>
        <div class="absolute bottom-20 left-1/3 w-80 h-80 bg-gradient-to-t from-purple-500/10 to-cyan-500/10 rounded-full blur-3xl animate-pulse delay-1000"></div>
        
        <!-- Particle System -->
        <div class="absolute inset-0">
          <div v-for="i in 20" :key="i" 
               :class="`absolute w-2 h-2 bg-gradient-to-r from-violet-400/30 to-cyan-400/30 rounded-full animate-float`"
               :style="{
                 left: Math.random() * 100 + '%',
                 top: Math.random() * 100 + '%',
                 animationDelay: Math.random() * 5 + 's',
                 animationDuration: (3 + Math.random() * 4) + 's'
               }">
          </div>
        </div>
      </div>
    </div>

    <!-- Premium Navigation Bar -->
    <nav class="relative z-50 bg-white/5 backdrop-blur-xl border-b border-white/10">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
          <!-- Logo -->
          <div class="flex items-center">
            <Link :href="route('gallery.index')" class="flex items-center space-x-2 group">
              <div class="w-10 h-10 bg-gradient-to-r from-violet-500 to-cyan-500 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                <PhotoIcon class="h-6 w-6 text-white" />
              </div>
              <span class="text-xl font-bold bg-gradient-to-r from-violet-400 to-cyan-400 bg-clip-text text-transparent">
                Visual Gallery
              </span>
            </Link>
          </div>

          <!-- Navigation Links -->
          <div class="hidden md:flex items-center space-x-8">
            <!-- Public Links -->
            <Link :href="route('gallery.index')" 
                  class="text-slate-300 hover:text-white transition-colors duration-300 hover:scale-105 transform">
              Gallery
            </Link>
            <Link :href="route('albums.index')" 
                  class="text-slate-300 hover:text-white transition-colors duration-300 hover:scale-105 transform">
              Albums
            </Link>
            
            <!-- Authentication Links -->
            <div class="flex items-center space-x-4">
              <!-- For Guests -->
              <template v-if="!auth.user">
                <Link v-if="canLogin" :href="route('login')" 
                      class="px-4 py-2 text-slate-300 hover:text-white transition-all duration-300">
                  Sign In
                </Link>
                <Link v-if="canRegister" :href="route('register')" 
                      class="px-6 py-2 bg-gradient-to-r from-violet-500 to-cyan-500 text-white font-semibold rounded-xl hover:shadow-lg hover:shadow-violet-500/25 transition-all duration-300 hover:scale-105">
                  Get Started
                </Link>
              </template>
              
              <!-- For Authenticated Users -->
              <template v-else>
                <Link :href="route('dashboard')" 
                      class="text-slate-300 hover:text-white transition-colors duration-300 hover:scale-105 transform">
                  Dashboard
                </Link>
                <Link :href="route('upload')" 
                      class="px-4 py-2 text-slate-300 hover:text-white transition-all duration-300">
                  Upload
                </Link>
                <Link :href="route('profile.edit')" 
                      class="px-6 py-2 bg-gradient-to-r from-violet-500 to-cyan-500 text-white font-semibold rounded-xl hover:shadow-lg hover:shadow-violet-500/25 transition-all duration-300 hover:scale-105">
                  Profile
                </Link>
              </template>
            </div>
          </div>

          <!-- Mobile Menu Button -->
          <div class="md:hidden">
            <button @click="mobileMenuOpen = !mobileMenuOpen" 
                    class="text-slate-300 hover:text-white p-2">
              <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Mobile Menu -->
      <div v-if="mobileMenuOpen" class="md:hidden bg-white/5 backdrop-blur-xl border-t border-white/10">
        <div class="px-4 py-4 space-y-4">
          <!-- Public Links -->
          <Link :href="route('gallery.index')" class="block text-slate-300 hover:text-white">Gallery</Link>
          <Link :href="route('albums.index')" class="block text-slate-300 hover:text-white">Albums</Link>
          
          <div class="border-t border-white/10 pt-4 space-y-2">
            <!-- For Guests -->
            <template v-if="!auth.user">
              <Link v-if="canLogin" :href="route('login')" class="block text-slate-300 hover:text-white">Sign In</Link>
              <Link v-if="canRegister" :href="route('register')" class="block px-4 py-2 bg-gradient-to-r from-violet-500 to-cyan-500 text-white font-semibold rounded-xl text-center">Get Started</Link>
            </template>
            
            <!-- For Authenticated Users -->
            <template v-else>
              <Link :href="route('dashboard')" class="block text-slate-300 hover:text-white">Dashboard</Link>
              <Link :href="route('upload')" class="block text-slate-300 hover:text-white">Upload</Link>
              <Link :href="route('profile.edit')" class="block px-4 py-2 bg-gradient-to-r from-violet-500 to-cyan-500 text-white font-semibold rounded-xl text-center">Profile</Link>
            </template>
          </div>
        </div>
      </div>
    </nav>

    <!-- Hero Section -->
    <div class="relative z-10 min-h-screen flex items-center pt-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
          
          <!-- Hero Content -->
          <div class="text-center lg:text-left animate-fade-in-up">
            <div class="mb-8">
              <h1 class="text-6xl lg:text-7xl font-extrabold leading-tight">
                <span class="block bg-gradient-to-r from-violet-400 via-white to-cyan-400 bg-clip-text text-transparent animate-shimmer bg-size-200 bg-pos-0">
                  Visual
                </span>
                <span class="block bg-gradient-to-r from-cyan-500 via-purple-500 to-violet-500 bg-clip-text text-transparent mt-2 animate-shimmer bg-size-200 bg-pos-0 animation-delay-1000">
                  Gallery
                </span>
              </h1>
              <div class="h-1 w-32 bg-gradient-to-r from-violet-500 to-cyan-500 mx-auto lg:mx-0 mt-4 rounded-full animate-pulse"></div>
            </div>

            <p class="text-xl text-slate-300 mb-8 max-w-xl mx-auto lg:mx-0 leading-relaxed animate-fade-in-up animation-delay-500">
              {{ auth.user 
                ? `Welcome back, ${auth.user.name}! Continue exploring and sharing your amazing photography.`
                : 'Discover, share, and organize your photos in a stunning, modern gallery. Experience seamless image management with powerful features and beautiful design.'
              }}
            </p>

            <!-- Enhanced CTA Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start animate-fade-in-up animation-delay-1000">
              <!-- For Authenticated Users -->
              <template v-if="auth.user">
                <Link
                  :href="route('dashboard')"
                  class="group relative px-8 py-4 bg-gradient-to-r from-violet-600 to-cyan-600 text-white font-semibold rounded-2xl shadow-lg hover:shadow-2xl hover:shadow-violet-500/25 transition-all duration-300 hover:scale-105 overflow-hidden"
                >
                  <div class="absolute inset-0 bg-gradient-to-r from-cyan-600 to-purple-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                  <span class="relative flex items-center justify-center">
                    My Dashboard
                    <ArrowRightIcon class="ml-2 h-5 w-5 group-hover:translate-x-1 transition-transform duration-300" />
                  </span>
                </Link>
                
                <Link
                  :href="route('upload')"
                  class="group px-8 py-4 bg-white/10 backdrop-blur-xl border border-white/20 text-slate-200 font-semibold rounded-2xl hover:bg-white/20 hover:border-violet-400/50 hover:scale-105 transition-all duration-300"
                >
                  <span class="flex items-center justify-center">
                    Upload Photos
                    <PhotoIcon class="ml-2 h-5 w-5 group-hover:rotate-12 transition-transform duration-300" />
                  </span>
                </Link>
              </template>
              
              <!-- For Guests -->
              <template v-else>
                <Link
                  :href="route('gallery.index')"
                  class="group relative px-8 py-4 bg-gradient-to-r from-violet-600 to-cyan-600 text-white font-semibold rounded-2xl shadow-lg hover:shadow-2xl hover:shadow-violet-500/25 transition-all duration-300 hover:scale-105 overflow-hidden"
                >
                  <div class="absolute inset-0 bg-gradient-to-r from-cyan-600 to-purple-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                  <span class="relative flex items-center justify-center">
                    Browse Gallery
                    <PhotoIcon class="ml-2 h-5 w-5 group-hover:rotate-12 transition-transform duration-300" />
                  </span>
                </Link>
                
                <Link
                  v-if="canRegister"
                  :href="route('register')"
                  class="group px-8 py-4 bg-white/10 backdrop-blur-xl border border-white/20 text-slate-200 font-semibold rounded-2xl hover:bg-white/20 hover:border-violet-400/50 hover:scale-105 transition-all duration-300"
                >
                  <span class="flex items-center justify-center">
                    Get Started
                    <ArrowRightIcon class="ml-2 h-5 w-5 group-hover:translate-x-1 transition-transform duration-300" />
                  </span>
                </Link>
              </template>
            </div>
          </div>

          <!-- Hero Visual -->
          <div class="relative animate-fade-in-up animation-delay-700">
            <div class="relative bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-8 shadow-2xl hover:bg-white/10 transition-all duration-500">
              <!-- Featured Image Preview Grid -->
              <div class="grid grid-cols-2 gap-4 mb-6">
                <div v-for="i in 4" :key="i" 
                     class="aspect-square bg-gradient-to-br from-slate-800 to-violet-900/50 rounded-2xl flex items-center justify-center hover:scale-105 transition-transform duration-300 border border-white/10"
                     :class="{
                       'animate-pulse delay-100': i === 1,
                       'animate-pulse delay-300': i === 2,
                       'animate-pulse delay-500': i === 3,
                       'animate-pulse delay-700': i === 4,
                     }">
                  <PhotoIcon class="h-12 w-12 text-violet-400/60" />
                </div>
              </div>
              
              <!-- Stats Preview -->
              <div class="grid grid-cols-3 gap-4 text-center">
                <div class="p-3 bg-white/5 rounded-xl border border-white/10">
                  <div class="text-2xl font-bold bg-gradient-to-r from-violet-400 to-cyan-400 bg-clip-text text-transparent">{{ formatNumber(stats.total_images) }}</div>
                  <div class="text-xs text-slate-400">Images</div>
                </div>
                <div class="p-3 bg-white/5 rounded-xl border border-white/10">
                  <div class="text-2xl font-bold bg-gradient-to-r from-cyan-400 to-purple-400 bg-clip-text text-transparent">{{ formatNumber(stats.total_albums) }}</div>
                  <div class="text-xs text-slate-400">Albums</div>
                </div>
                <div class="p-3 bg-white/5 rounded-xl border border-white/10">
                  <div class="text-2xl font-bold bg-gradient-to-r from-purple-400 to-violet-400 bg-clip-text text-transparent">{{ formatNumber(stats.total_users) }}</div>
                  <div class="text-xs text-slate-400">Users</div>
                </div>
              </div>
            </div>

            <!-- Floating Elements -->
            <div class="absolute -top-4 -right-4 w-24 h-24 bg-gradient-to-br from-violet-500/20 to-cyan-500/20 rounded-full animate-spin slow-spin border border-white/10"></div>
            <div class="absolute -bottom-6 -left-6 w-16 h-16 bg-gradient-to-br from-purple-500/30 to-cyan-500/30 rounded-full animate-bounce border border-white/10"></div>
            <div class="absolute top-1/2 -right-8 w-8 h-8 bg-gradient-to-br from-violet-400/40 to-purple-400/40 rounded-full animate-ping"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Featured Images Section -->
    <div class="relative z-10 py-24 animate-fade-in-up" v-if="featuredImages.length > 0">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
          <h2 class="text-4xl font-extrabold bg-gradient-to-r from-violet-400 via-white to-cyan-400 bg-clip-text text-transparent mb-4 animate-shimmer bg-size-200 bg-pos-0">
            Trending Captures
          </h2>
          <p class="text-lg text-slate-300 max-w-2xl mx-auto">
            Discover breathtaking photography from our vibrant community
          </p>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
          <div
            v-for="(image, index) in featuredImages"
            :key="image.id"
            class="group relative aspect-square bg-slate-800/50 backdrop-blur-xl border border-white/10 rounded-2xl overflow-hidden hover:border-violet-400/50 transition-all duration-500 hover:scale-105 hover:shadow-2xl hover:shadow-violet-500/25"
            :style="{ animationDelay: index * 100 + 'ms' }"
          >
            <img
              :src="getImageUrl(image, 'medium')"
              :alt="image.alt_text || image.title"
              class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
            >
            
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-all duration-300">
              <div class="absolute bottom-4 left-4 right-4 text-white transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300">
                <h3 class="font-semibold truncate text-white">{{ image.title || 'Untitled' }}</h3>
                <p class="text-sm text-slate-300">by {{ image.owner?.name }}</p>
              </div>
            </div>

            <Link
              :href="route('images.show', image.slug)"
              class="absolute inset-0 z-10"
            >
              <span class="sr-only">View {{ image.title }}</span>
            </Link>
          </div>
        </div>

        <div class="text-center mt-12">
          <Link
            :href="route('gallery.index')"
            class="group inline-flex items-center px-8 py-4 bg-white/5 backdrop-blur-xl border border-white/20 text-slate-200 font-semibold rounded-2xl hover:bg-white/10 hover:border-violet-400/50 hover:scale-105 transition-all duration-300"
          >
            Explore All Images
            <ArrowRightIcon class="ml-2 h-5 w-5 group-hover:translate-x-1 transition-transform duration-300" />
          </Link>
        </div>
      </div>
    </div>

    <!-- Featured Albums Section -->
    <div class="relative z-10 py-24 animate-fade-in-up" v-if="featuredAlbums.length > 0">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
          <h2 class="text-4xl font-extrabold bg-gradient-to-r from-cyan-400 via-purple-400 to-violet-400 bg-clip-text text-transparent mb-4 animate-shimmer bg-size-200 bg-pos-0">
            Featured Collections
          </h2>
          <p class="text-lg text-slate-300 max-w-2xl mx-auto">
            Curated albums showcasing exceptional visual stories
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <div
            v-for="(album, index) in featuredAlbums"
            :key="album.id"
            class="group bg-slate-800/30 backdrop-blur-xl border border-white/10 rounded-3xl overflow-hidden hover:border-violet-400/40 transition-all duration-500 hover:scale-105 hover:shadow-2xl hover:shadow-violet-500/20"
            :style="{ animationDelay: index * 150 + 'ms' }"
          >
            <div class="aspect-video bg-gradient-to-br from-violet-600 to-cyan-600 relative overflow-hidden">
              <img
                v-if="album.cover_image"
                :src="getImageUrl(album.cover_image, 'medium')"
                :alt="album.title"
                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
              >
              <div v-else class="flex items-center justify-center h-full">
                <FolderIcon class="h-16 w-16 text-white/60" />
              </div>
            </div>

            <div class="p-6 bg-white/5">
              <h3 class="text-xl font-semibold text-white mb-2 truncate">
                {{ album.title }}
              </h3>
              <p class="text-slate-300 text-sm mb-4 line-clamp-2">
                {{ album.description }}
              </p>
              
              <div class="flex items-center justify-between">
                <span class="text-sm text-slate-400 font-medium">
                  {{ album.images_count }} image{{ album.images_count !== 1 ? 's' : '' }}
                </span>
                <Link
                  :href="route('albums.show', album.slug)"
                  class="text-sm font-semibold bg-gradient-to-r from-violet-400 to-cyan-400 bg-clip-text text-transparent hover:from-white hover:to-white transition-all duration-300"
                >
                  View Album →
                </Link>
              </div>
            </div>
          </div>
        </div>

        <div class="text-center mt-12">
          <Link
            :href="route('albums.index')"
            class="group inline-flex items-center px-8 py-4 bg-white/5 backdrop-blur-xl border border-white/20 text-slate-200 font-semibold rounded-2xl hover:bg-white/10 hover:border-violet-400/50 hover:scale-105 transition-all duration-300"
          >
            Browse All Albums
            <ArrowRightIcon class="ml-2 h-5 w-5 group-hover:translate-x-1 transition-transform duration-300" />
          </Link>
        </div>
      </div>
    </div>

    <!-- Final CTA Section -->
    <div class="relative z-10 py-24 animate-fade-in-up" v-if="!auth.user">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="bg-white/5 backdrop-blur-xl border border-white/20 rounded-3xl p-12 shadow-2xl hover:bg-white/10 transition-all duration-500">
          <h2 class="text-4xl font-extrabold mb-6">
            <span class="block bg-gradient-to-r from-violet-400 via-white to-cyan-400 bg-clip-text text-transparent animate-shimmer bg-size-200 bg-pos-0">
              Ready to showcase
            </span>
            <span class="block bg-gradient-to-r from-cyan-400 to-purple-400 bg-clip-text text-transparent mt-2 animate-shimmer bg-size-200 bg-pos-0 animation-delay-1000">
              your visual story?
            </span>
          </h2>
          
          <p class="text-xl text-slate-300 mb-10 max-w-2xl mx-auto">
            Join our community and start sharing your amazing photography today
          </p>

          <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <Link
              v-if="canRegister"
              :href="route('register')"
              class="group px-10 py-5 bg-gradient-to-r from-violet-600 to-cyan-600 text-white font-bold rounded-2xl shadow-lg hover:shadow-2xl hover:shadow-violet-500/30 transition-all duration-300 hover:scale-105 text-lg overflow-hidden"
            >
              <div class="absolute inset-0 bg-gradient-to-r from-cyan-600 to-purple-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
              <span class="relative flex items-center justify-center">
                Create Account
                <ArrowRightIcon class="ml-2 h-5 w-5 group-hover:translate-x-1 transition-transform duration-300" />
              </span>
            </Link>
            
            <Link
              :href="canLogin ? route('login') : route('gallery.index')"
              class="group px-10 py-5 bg-white/10 backdrop-blur-xl border-2 border-white/20 text-slate-200 font-bold rounded-2xl hover:bg-white/20 hover:border-violet-400 hover:scale-105 transition-all duration-300 text-lg"
            >
              {{ canLogin ? 'Sign In' : 'Explore Gallery' }}
            </Link>
          </div>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <footer class="relative z-10 bg-white/5 backdrop-blur-xl border-t border-white/10 mt-24">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
          
          <!-- Brand Section -->
          <div class="col-span-1 md:col-span-2">
            <div class="flex items-center space-x-2 mb-4">
              <div class="w-10 h-10 bg-gradient-to-r from-violet-500 to-cyan-500 rounded-xl flex items-center justify-center">
                <PhotoIcon class="h-6 w-6 text-white" />
              </div>
              <span class="text-xl font-bold bg-gradient-to-r from-violet-400 to-cyan-400 bg-clip-text text-transparent">
                Visual Gallery
              </span>
            </div>
            <p class="text-slate-400 mb-6 max-w-md">
              A modern platform for discovering, sharing, and organizing beautiful photography. 
              Join our community of visual storytellers today.
            </p>
          </div>

          <!-- Quick Links -->
          <div>
            <h3 class="text-white font-semibold mb-4">Explore</h3>
            <ul class="space-y-2">
              <li>
                <Link :href="route('gallery.index')" class="text-slate-400 hover:text-white transition-colors duration-300">
                  Gallery
                </Link>
              </li>
              <li>
                <Link :href="route('albums.index')" class="text-slate-400 hover:text-white transition-colors duration-300">
                  Albums
                </Link>
              </li>
              <li v-if="auth.user">
                <Link :href="route('dashboard')" class="text-slate-400 hover:text-white transition-colors duration-300">
                  Dashboard
                </Link>
              </li>
              <li v-if="auth.user">
                <Link :href="route('upload')" class="text-slate-400 hover:text-white transition-colors duration-300">
                  Upload
                </Link>
              </li>
            </ul>
          </div>

          <!-- Account -->
          <div>
            <h3 class="text-white font-semibold mb-4">Account</h3>
            <ul class="space-y-2">
              <template v-if="!auth.user">
                <li v-if="canLogin">
                  <Link :href="route('login')" class="text-slate-400 hover:text-white transition-colors duration-300">
                    Sign In
                  </Link>
                </li>
                <li v-if="canRegister">
                  <Link :href="route('register')" class="text-slate-400 hover:text-white transition-colors duration-300">
                    Create Account
                  </Link>
                </li>
              </template>
              <template v-else>
                <li>
                  <Link :href="route('profile.edit')" class="text-slate-400 hover:text-white transition-colors duration-300">
                    Profile
                  </Link>
                </li>
<li>
  <Link :href="route('logout')" method="post" as="button" class="text-slate-400 hover:text-white transition-colors duration-300">
    Sign Out
  </Link>
</li>
              </template>
              <li>
                <a href="#" class="text-slate-400 hover:text-white transition-colors duration-300">
                  Privacy Policy
                </a>
              </li>
              <li>
                <a href="#" class="text-slate-400 hover:text-white transition-colors duration-300">
                  Terms of Service
                </a>
              </li>
            </ul>
          </div>
        </div>

        <!-- Footer Bottom -->
        <div class="border-t border-white/10 mt-8 pt-8 flex flex-col md:flex-row justify-between items-center">
          <p class="text-slate-500 text-sm">
            © 2025 Visual Gallery. All rights reserved.
          </p>
          <p class="text-slate-500 text-sm mt-4 md:mt-0">
            Built with ❤️ for photography enthusiasts
          </p>
        </div>
      </div>
    </footer>
  <!-- </GuestLayout> -->
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
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

const page = usePage()
const auth = computed(() => page.props.auth || { user: null })

const mobileMenuOpen = ref(false)

const formatNumber = (number) => {
  if (number >= 1000000) {
    return (number / 1000000).toFixed(1) + 'M'
  } else if (number >= 1000) {
    return (number / 1000).toFixed(1) + 'K'
  }
  return number.toString()
}

const getImageUrl = (image, variant = 'medium') => {
  // Use direct MinIO URL since thumbnails aren't processed yet
  if (image.storage_path) {
    return `http://localhost:9000/gallery-images/${image.storage_path}`
  }
  return image.url || '/images/placeholder.jpg'
}
</script>

<style scoped>

/* Add this at the top of your existing styles */
:global(body) {
  margin: 0;
  padding: 0;
}

:global(html) {
  margin: 0;
  padding: 0;
}

/* Also ensure the layout fills the screen */
:global(.min-h-screen) {
  min-height: 100vh;
}

/* Rest of your existing styles... */
@keyframes slow-spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

/* All existing styles remain the same */
@keyframes slow-spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

@keyframes float {
  0%, 100% {
    transform: translateY(0px);
    opacity: 0.5;
  }
  25% {
    transform: translateY(-12px) translateX(8px);
    opacity: 0.8;
  }
  50% {
    transform: translateY(-6px) translateX(-4px);
    opacity: 1;
  }
  75% {
    transform: translateY(-18px) translateX(-10px);
    opacity: 0.6;
  }
}

.animate-float {
  animation: float 6s ease-in-out infinite;
}

@keyframes shimmer {
  0% { background-position: -200% 0; }
  100% { background-position: 200% 0; }
}
.animate-shimmer {
  animation: shimmer 3s ease-in-out infinite;
}

@keyframes fade-in-up {
  0% {
    opacity: 0;
    transform: translateY(24px);
  }
  100% {
    opacity: 1;
    transform: translateY(0);
  }
}
.animate-fade-in-up {
  animation: fade-in-up 0.8s ease 0s 1 both;
}

.slow-spin {
  animation: slow-spin 25s linear infinite;
}

.bg-size-200 {
  background-size: 200% 200%;
}
.bg-pos-0 {
  background-position: 0% 50%;
}

.animation-delay-500 {
  animation-delay: 0.5s;
}
.animation-delay-700 {
  animation-delay: 0.7s;
}
.animation-delay-1000 {
  animation-delay: 1s;
}

.line-clamp-2 {
  overflow: hidden;
  display: -webkit-box;
  -webkit-box-orient: vertical;
  -webkit-line-clamp: 2;
  line-clamp: 2;
}
</style>
