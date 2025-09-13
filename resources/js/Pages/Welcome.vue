<template>
  <div style="margin: 0; padding: 0;" class="min-h-screen">
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
    <nav class="relative z-50 bg-white/5 backdrop-blur-xl border-b border-white/10 mb-0">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
          <!-- Logo -->
          <div class="flex items-center">
            <Link :href="route('gallery.index')" class="flex items-center space-x-2 group">
              <div class="w-10 h-10 bg-gradient-to-r from-violet-500 to-cyan-500 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                <PhotoIcon class="h-6 w-6 text-white" />
              </div>
              <span class="text-xl font-bold bg-gradient-to-r from-violet-400 to-cyan-400 bg-clip-text text-transparent">
                Clone Fest Gallery
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
    <div class="relative z-10 min-h-screen flex items-center pt-0">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
          
          <!-- Hero Content -->
          <div class="text-center lg:text-left animate-fade-in-up">
            <div class="mb-8">
              <!-- Clone Fest Badge -->
              <div class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-violet-500/20 to-cyan-500/20 border border-violet-500/30 rounded-full mb-6 animate-pulse">
                <span class="text-sm font-semibold text-violet-300">🏆 Clone Fest Week 2 Project</span>
              </div>
              
              <h1 class="text-5xl lg:text-6xl font-extrabold leading-tight">
                <span class="block bg-gradient-to-r from-violet-400 via-white to-cyan-400 bg-clip-text text-transparent animate-shimmer bg-size-200 bg-pos-0">
                  Modern Visual
                </span>
                <span class="block bg-gradient-to-r from-cyan-500 via-purple-500 to-violet-500 bg-clip-text text-transparent mt-2 animate-shimmer bg-size-200 bg-pos-0 animation-delay-1000">
                  Gallery Platform
                </span>
              </h1>
              <div class="h-1 w-32 bg-gradient-to-r from-violet-500 to-cyan-500 mx-auto lg:mx-0 mt-4 rounded-full animate-pulse"></div>
            </div>

            <p class="text-xl text-slate-300 mb-6 max-w-xl mx-auto lg:mx-0 leading-relaxed animate-fade-in-up animation-delay-500">
              {{ auth.user 
                ? `Welcome back, ${auth.user.name}! Continue exploring our modernized gallery platform.`
                : 'Experience the evolution from classic PHP to modern Laravel 10 + Inertia.js + Vue 3. A complete media platform with extensible architecture.'
              }}
            </p>

            <!-- Tech Stack Display -->
            <div class="flex flex-wrap justify-center lg:justify-start gap-3 mb-8 animate-fade-in-up animation-delay-700">
              <span class="px-3 py-1 bg-red-500/20 text-red-300 rounded-full text-sm font-medium border border-red-500/30">Laravel 10</span>
              <span class="px-3 py-1 bg-green-500/20 text-green-300 rounded-full text-sm font-medium border border-green-500/30">Vue 3</span>
              <span class="px-3 py-1 bg-purple-500/20 text-purple-300 rounded-full text-sm font-medium border border-purple-500/30">Inertia.js</span>
              <span class="px-3 py-1 bg-cyan-500/20 text-cyan-300 rounded-full text-sm font-medium border border-cyan-500/30">Tailwind CSS</span>
              <span class="px-3 py-1 bg-blue-500/20 text-blue-300 rounded-full text-sm font-medium border border-blue-500/30">PostgreSQL</span>
              <span class="px-3 py-1 bg-yellow-500/20 text-yellow-300 rounded-full text-sm font-medium border border-yellow-500/30">Redis</span>
              <span class="px-3 py-1 bg-orange-500/20 text-orange-300 rounded-full text-sm font-medium border border-orange-500/30">S3/CDN</span>
            </div>

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
                    Upload Media
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
                    Explore Platform
                    <PhotoIcon class="ml-2 h-5 w-5 group-hover:rotate-12 transition-transform duration-300" />
                  </span>
                </Link>
                
                <Link
                  v-if="canRegister"
                  :href="route('register')"
                  class="group px-8 py-4 bg-white/10 backdrop-blur-xl border border-white/20 text-slate-200 font-semibold rounded-2xl hover:bg-white/20 hover:border-violet-400/50 hover:scale-105 transition-all duration-300"
                >
                  <span class="flex items-center justify-center">
                    Join Platform
                    <ArrowRightIcon class="ml-2 h-5 w-5 group-hover:translate-x-1 transition-transform duration-300" />
                  </span>
                </Link>
              </template>
            </div>
          </div>

          <!-- Hero Visual - SINGLE VERSION (Fixed) -->
          <div class="relative animate-fade-in-up animation-delay-700">
            <div class="relative bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-8 shadow-2xl hover:bg-white/10 transition-all duration-500">
              <!-- Enhanced Featured Image Preview Grid -->
              <div class="grid grid-cols-2 gap-4 mb-6">
                <div 
                  v-for="(image, index) in previewImages" 
                  :key="`preview-${image.id || index}`"
                  class="aspect-square bg-gradient-to-br from-slate-800 to-violet-900/50 rounded-2xl overflow-hidden flex items-center justify-center hover:scale-105 transition-transform duration-300 border border-white/10 relative group"
                  :class="{
                    'animate-pulse delay-100': index === 0,
                    'animate-pulse delay-300': index === 1,
                    'animate-pulse delay-500': index === 2,
                    'animate-pulse delay-700': index === 3,
                  }"
                >
                  <!-- Actual Image Display -->
                  <img 
                    v-if="getImageUrl(image, 'thumbnail')"
                    :src="getImageUrl(image, 'thumbnail')"
                    :alt="image.title || `Preview ${index + 1}`"
                    :data-index="index"
                    class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110"
                    @error="handlePreviewImageError"
                  />
                  
                  <!-- Fallback with Better Placeholder -->
                  <div 
                    v-else
                    class="w-full h-full bg-gradient-to-br from-violet-600/20 to-cyan-600/20 flex items-center justify-center"
                  >
                    <PhotoIcon class="h-12 w-12 text-violet-400/60" />
                  </div>
                  
                  <!-- Overlay on Hover -->
                  <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                    <div class="text-white text-xs font-medium text-center px-2">
                      <div v-if="image && image.title" class="truncate">{{ image.title }}</div>
                      <div v-else>Sample {{ index + 1 }}</div>
                    </div>
                  </div>
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

    <!-- Team Section -->
    <div class="relative z-10 py-0 animate-fade-in-up">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
          <h2 class="text-4xl font-extrabold bg-gradient-to-r from-violet-400 via-white to-cyan-400 bg-clip-text text-transparent mb-4 animate-shimmer bg-size-200 bg-pos-0">
            Clone Fest Week 2 Team
          </h2>
          <p class="text-lg text-slate-300 max-w-2xl mx-auto">
            Revolutionizing classic PHP galleries with modern technology stack
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-24">
          <div class="group bg-slate-800/30 backdrop-blur-xl border border-white/10 rounded-3xl p-8 hover:border-violet-400/40 transition-all duration-500 hover:scale-105 hover:shadow-2xl hover:shadow-violet-500/20">
            <div class="w-20 h-20 bg-gradient-to-r from-violet-500 to-purple-500 rounded-2xl flex items-center justify-center mx-auto mb-6">
              <svg class="h-10 w-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.618 5.984A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.031 9-11.622 0-1.042-.133-2.052-.382-3.016zM12 9v2m0 4h.01"/>
              </svg>
            </div>
            <h3 class="text-xl font-bold text-white mb-2 text-center">Bhavya Chawat</h3>
            <p class="text-violet-300 font-medium text-center mb-4">Database & Backend</p>
            <p class="text-slate-300 text-center">Laravel 10 architecture, PostgreSQL optimization, Redis caching, S3 integration</p>
          </div>

          <div class="group bg-slate-800/30 backdrop-blur-xl border border-white/10 rounded-3xl p-8 hover:border-cyan-400/40 transition-all duration-500 hover:scale-105 hover:shadow-2xl hover:shadow-cyan-500/20">
            <div class="w-20 h-20 bg-gradient-to-r from-cyan-500 to-blue-500 rounded-2xl flex items-center justify-center mx-auto mb-6">
              <svg class="h-10 w-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a4 4 0 004 4h4a2 2 0 002-2V5z"/>
              </svg>
            </div>
            <h3 class="text-xl font-bold text-white mb-2 text-center">Kruthi Krishna</h3>
            <p class="text-cyan-300 font-medium text-center mb-4">Frontend & Bonus Features</p>
            <p class="text-slate-300 text-center">Vue 3 components, Inertia.js integration, Tailwind UI, responsive design</p>
          </div>

          <div class="group bg-slate-800/30 backdrop-blur-xl border border-white/10 rounded-3xl p-8 hover:border-purple-400/40 transition-all duration-500 hover:scale-105 hover:shadow-2xl hover:shadow-purple-500/20">
            <div class="w-20 h-20 bg-gradient-to-r from-purple-500 to-pink-500 rounded-2xl flex items-center justify-center mx-auto mb-6">
              <svg class="h-10 w-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
              </svg>
            </div>
            <h3 class="text-xl font-bold text-white mb-2 text-center">Shreyas Sahoo</h3>
            <p class="text-purple-300 font-medium text-center mb-4">Documentation & Strategy</p>
            <p class="text-slate-300 text-center">Technical documentation, project strategy, API documentation, deployment guides</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Featured Images Section -->
    <div class="relative z-10 py-0 animate-fade-in-up" v-if="featuredImages.length > 0">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
          <h2 class="text-4xl font-extrabold bg-gradient-to-r from-violet-400 via-white to-cyan-400 bg-clip-text text-transparent mb-4 animate-shimmer bg-size-200 bg-pos-0">
            Platform Showcase
          </h2>
          <p class="text-lg text-slate-300 max-w-2xl mx-auto">
            Experience the power of our modern media management platform
          </p>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-24">
          <div
            v-for="(image, index) in featuredImages"
            :key="`featured-${image.id || index}`"
            class="group relative aspect-square bg-slate-800/50 backdrop-blur-xl border border-white/10 rounded-2xl overflow-hidden hover:border-violet-400/50 transition-all duration-500 hover:scale-105 hover:shadow-2xl hover:shadow-violet-500/25"
            :style="{ animationDelay: index * 100 + 'ms' }"
          >
            <img
              v-if="getImageUrl(image, 'medium')"
              :src="getImageUrl(image, 'medium')"
              :alt="image.alt_text || image.title || 'Gallery Image'"
              class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
              @error="handleFeaturedImageError"
            />
            
            <div v-else class="w-full h-full bg-gradient-to-br from-slate-700 to-violet-900/50 flex items-center justify-center">
              <PhotoIcon class="h-16 w-16 text-violet-400/60" />
            </div>
            
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-all duration-300">
              <div class="absolute bottom-4 left-4 right-4 text-white transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300">
                <h3 class="font-semibold truncate text-white">{{ image.title || 'Untitled' }}</h3>
                <p class="text-sm text-slate-300">by {{ image.owner?.name || 'Anonymous' }}</p>
              </div>
            </div>

            <Link
              :href="route('images.show', image.slug || image.id)"
              class="absolute inset-0 z-10"
            >
              <span class="sr-only">View {{ image.title || 'Image' }}</span>
            </Link>
          </div>
        </div>

        <div class="text-center">
          <Link
            :href="route('gallery.index')"
            class="group inline-flex items-center px-8 py-4 bg-white/5 backdrop-blur-xl border border-white/20 text-slate-200 font-semibold rounded-2xl hover:bg-white/10 hover:border-violet-400/50 hover:scale-105 transition-all duration-300"
          >
            Explore Full Platform
            <ArrowRightIcon class="ml-2 h-5 w-5 group-hover:translate-x-1 transition-transform duration-300" />
          </Link>
        </div>
      </div>
    </div>

    <!-- Featured Albums Section -->
    <div class="relative z-10 py-0 animate-fade-in-up" v-if="featuredAlbums.length > 0">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
          <h2 class="text-4xl font-extrabold bg-gradient-to-r from-cyan-400 via-purple-400 to-violet-400 bg-clip-text text-transparent mb-4 animate-shimmer bg-size-200 bg-pos-0">
            Media Collections
          </h2>
          <p class="text-lg text-slate-300 max-w-2xl mx-auto">
            Organized media galleries powered by modern architecture
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-24">
          <div
            v-for="(album, index) in featuredAlbums"
            :key="`album-${album.id || index}`"
            class="group bg-slate-800/30 backdrop-blur-xl border border-white/10 rounded-3xl overflow-hidden hover:border-violet-400/40 transition-all duration-500 hover:scale-105 hover:shadow-2xl hover:shadow-violet-500/20"
            :style="{ animationDelay: index * 150 + 'ms' }"
          >
            <div class="aspect-video bg-gradient-to-br from-violet-600 to-cyan-600 relative overflow-hidden">
              <img
                v-if="album.cover_image && getImageUrl(album.cover_image, 'medium')"
                :src="getImageUrl(album.cover_image, 'medium')"
                :alt="album.title || 'Album Cover'"
                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
                @error="handleAlbumImageError"
              />
              <div v-else class="flex items-center justify-center h-full">
                <FolderIcon class="h-16 w-16 text-white/60" />
              </div>
            </div>

            <div class="p-6 bg-white/5">
              <h3 class="text-xl font-semibold text-white mb-2 truncate">
                {{ album.title || 'Untitled Album' }}
              </h3>
              <p class="text-slate-300 text-sm mb-4">
                {{ album.description || 'No description available' }}
              </p>
              
              <div class="flex items-center justify-between">
                <span class="text-sm text-slate-400 font-medium">
                  {{ album.images_count || 0 }} image{{ (album.images_count || 0) !== 1 ? 's' : '' }}
                </span>
                <Link
                  :href="route('albums.show', album.slug || album.id)"
                  class="text-sm font-semibold bg-gradient-to-r from-violet-400 to-cyan-400 bg-clip-text text-transparent hover:from-white hover:to-white transition-all duration-300"
                >
                  View Album →
                </Link>
              </div>
            </div>
          </div>
        </div>

        <div class="text-center">
          <Link
            :href="route('albums.index')"
            class="group inline-flex items-center px-8 py-4 bg-white/5 backdrop-blur-xl border border-white/20 text-slate-200 font-semibold rounded-2xl hover:bg-white/10 hover:border-violet-400/50 hover:scale-105 transition-all duration-300"
          >
            Browse All Collections
            <ArrowRightIcon class="ml-2 h-5 w-5 group-hover:translate-x-1 transition-transform duration-300" />
          </Link>
        </div>
      </div>
    </div>

    <!-- Final CTA Section -->
    <div class="relative z-10 py-0 animate-fade-in-up" v-if="!auth.user">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="bg-white/5 backdrop-blur-xl border border-white/20 rounded-3xl p-12 shadow-2xl hover:bg-white/10 transition-all duration-500 mb-0">
          <h2 class="text-4xl font-extrabold mb-6">
            <span class="block bg-gradient-to-r from-violet-400 via-white to-cyan-400 bg-clip-text text-transparent animate-shimmer bg-size-200 bg-pos-0">
              Experience the Evolution
            </span>
            <span class="block bg-gradient-to-r from-cyan-400 to-purple-400 bg-clip-text text-transparent mt-2 animate-shimmer bg-size-200 bg-pos-0 animation-delay-1000">
              From Classic to Modern
            </span>
          </h2>
          
          <p class="text-xl text-slate-300 mb-10 max-w-2xl mx-auto">
            Join our Clone Fest Week 2 demonstration of transforming legacy PHP galleries into scalable, modern web applications
          </p>

          <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <Link
              v-if="canRegister"
              :href="route('register')"
              class="group px-10 py-5 bg-gradient-to-r from-violet-600 to-cyan-600 text-white font-bold rounded-2xl shadow-lg hover:shadow-2xl hover:shadow-violet-500/30 transition-all duration-300 hover:scale-105 text-lg overflow-hidden"
            >
              <div class="absolute inset-0 bg-gradient-to-r from-cyan-600 to-purple-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
              <span class="relative flex items-center justify-center">
                Join the Platform
                <ArrowRightIcon class="ml-2 h-5 w-5 group-hover:translate-x-1 transition-transform duration-300" />
              </span>
            </Link>
            
            <Link
              :href="canLogin ? route('login') : route('gallery.index')"
              class="group px-10 py-5 bg-white/10 backdrop-blur-xl border-2 border-white/20 text-slate-200 font-bold rounded-2xl hover:bg-white/20 hover:border-violet-400 hover:scale-105 transition-all duration-300 text-lg"
            >
              {{ canLogin ? 'Sign In' : 'Explore Platform' }}
            </Link>
          </div>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <footer class="relative z-10 bg-white/5 backdrop-blur-xl border-t border-white/10 mt-0">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
          
          <!-- Brand Section -->
          <div class="col-span-1 md:col-span-2">
            <div class="flex items-center space-x-2 mb-4">
              <div class="w-10 h-10 bg-gradient-to-r from-violet-500 to-cyan-500 rounded-xl flex items-center justify-center">
                <PhotoIcon class="h-6 w-6 text-white" />
              </div>
              <span class="text-xl font-bold bg-gradient-to-r from-violet-400 to-cyan-400 bg-clip-text text-transparent">
                Clone Fest Gallery
              </span>
            </div>
            <p class="text-slate-400 mb-4 max-w-md">
              Clone Fest Week 2 project demonstrating the modernization of classic PHP image galleries using Laravel 10, Vue 3, Inertia.js, and cutting-edge web technologies.
            </p>
            <div class="flex flex-wrap gap-2 mb-6">
              <span class="px-2 py-1 bg-red-500/10 text-red-400 rounded text-xs border border-red-500/20">Laravel 10</span>
              <span class="px-2 py-1 bg-green-500/10 text-green-400 rounded text-xs border border-green-500/20">Vue 3</span>
              <span class="px-2 py-1 bg-purple-500/10 text-purple-400 rounded text-xs border border-purple-500/20">Inertia.js</span>
              <span class="px-2 py-1 bg-cyan-500/10 text-cyan-400 rounded text-xs border border-cyan-500/20">PostgreSQL</span>
              <span class="px-2 py-1 bg-yellow-500/10 text-yellow-400 rounded text-xs border border-yellow-500/20">Redis</span>
            </div>
          </div>

          <!-- Quick Links -->
          <div>
            <h3 class="text-white font-semibold mb-4">Platform</h3>
            <ul class="space-y-2">
              <li>
                <Link :href="route('gallery.index')" class="text-slate-400 hover:text-white transition-colors duration-300">
                  Gallery
                </Link>
              </li>
              <li>
                <Link :href="route('albums.index')" class="text-slate-400 hover:text-white transition-colors duration-300">
                  Collections
                </Link>
              </li>
              <li v-if="auth.user">
                <Link :href="route('dashboard')" class="text-slate-400 hover:text-white transition-colors duration-300">
                  Dashboard
                </Link>
              </li>
              <li v-if="auth.user">
                <Link :href="route('upload')" class="text-slate-400 hover:text-white transition-colors duration-300">
                  Upload Media
                </Link>
              </li>
            </ul>
          </div>

          <!-- Team & Project -->
          <div>
            <h3 class="text-white font-semibold mb-4">Clone Fest Team</h3>
            <ul class="space-y-2 text-sm">
              <li class="text-slate-400">
                <span class="text-violet-400 font-medium">Bhavya Chawat</span><br>
                Database & Backend
              </li>
              <li class="text-slate-400">
                <span class="text-cyan-400 font-medium">Kruthi Krishna</span><br>
                Frontend & Features
              </li>
              <li class="text-slate-400">
                <span class="text-purple-400 font-medium">Shreyas Sahoo</span><br>
                Documentation & Strategy
              </li>
              <template v-if="!auth.user">
                <li class="pt-2">
                  <Link v-if="canLogin" :href="route('login')" class="text-slate-400 hover:text-white transition-colors duration-300 text-xs">
                    Sign In
                  </Link>
                </li>
                <li v-if="canRegister">
                  <Link :href="route('register')" class="text-slate-400 hover:text-white transition-colors duration-300 text-xs">
                    Create Account
                  </Link>
                </li>
              </template>
              <template v-else>
                <li class="pt-2">
                  <Link :href="route('profile.edit')" class="text-slate-400 hover:text-white transition-colors duration-300 text-xs">
                    Profile Settings
                  </Link>
                </li>
                <li>
                  <Link :href="route('logout')" method="post" as="button" class="text-slate-400 hover:text-white transition-colors duration-300 text-xs">
                    Sign Out
                  </Link>
                </li>
              </template>
            </ul>
          </div>
        </div>

        <!-- Footer Bottom -->
        <div class="border-t border-white/10 mt-8 pt-8 flex flex-col md:flex-row justify-between items-center">
          <p class="text-slate-500 text-sm">
            © 2025 Clone Fest Week 2 Project - Visual Gallery Platform. Modernizing Classic PHP Applications.
          </p>
          <p class="text-slate-500 text-sm mt-4 md:mt-0">
            Built with modern web technologies for extensible media management
          </p>
        </div>
      </div>
    </footer>
  </div>
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

// Props definition MUST come first
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

// Page and auth setup
const page = usePage()
const auth = computed(() => page.props.auth || { user: null })
const mobileMenuOpen = ref(false)

// Create preview images from featured images or use sample data
const previewImages = computed(() => {
  if (props.featuredImages && props.featuredImages.length >= 4) {
    // Use actual featured images if available
    return props.featuredImages.slice(0, 4)
  } else if (props.featuredImages && props.featuredImages.length > 0) {
    // Repeat available images to fill 4 slots
    const images = []
    for (let i = 0; i < 4; i++) {
      images.push(props.featuredImages[i % props.featuredImages.length])
    }
    return images
  } else {
    // Fallback sample data with placeholder URLs
    return [
      { 
        id: 'sample-1', 
        title: 'Modern Architecture',
        url: 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=400&h=400&fit=crop&crop=center',
        storage_path: null
      },
      { 
        id: 'sample-2', 
        title: 'Urban Photography',
        url: 'https://images.unsplash.com/photo-1449824913935-59a10b8d2000?w=400&h=400&fit=crop&crop=center',
        storage_path: null
      },
      { 
        id: 'sample-3', 
        title: 'Nature Vista',
        url: 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=400&h=400&fit=crop&crop=center',
        storage_path: null
      },
      { 
        id: 'sample-4', 
        title: 'Creative Design',
        url: 'https://images.unsplash.com/photo-1460472178825-e5240623afd5?w=400&h=400&fit=crop&crop=center',
        storage_path: null
      }
    ]
  }
})

// Utility functions
const formatNumber = (number) => {
  if (!number) return '0'
  if (number >= 1000000) {
    return (number / 1000000).toFixed(1) + 'M'
  } else if (number >= 1000) {
    return (number / 1000).toFixed(1) + 'K'
  }
  return number.toString()
}

// Enhanced image URL function with multiple fallbacks
const getImageUrl = (image, variant = 'medium') => {
  if (!image) return null
  
  try {
    // For preview/thumbnail variant, try smaller sizes first
    if (variant === 'thumbnail') {
      if (image.storage_path) {
        return `http://localhost:9000/gallery-images/${image.storage_path}`
      }
      if (image.thumbnails && image.thumbnails.small) {
        return image.thumbnails.small
      }
    }
    
    // Try multiple URL sources
    if (image.storage_path) {
      return `http://localhost:9000/gallery-images/${image.storage_path}`
    }
    if (image.url) {
      return image.url
    }
    if (image.thumbnails && image.thumbnails[variant]) {
      return image.thumbnails[variant]
    }
  } catch (error) {
    console.warn('Error getting image URL:', error)
  }
  
  return null
}

// Error handling functions
const handlePreviewImageError = (event) => {
  const fallbackImages = [
    'https://via.placeholder.com/400x400/8b5cf6/ffffff?text=Gallery+1',
    'https://via.placeholder.com/400x400/06b6d4/ffffff?text=Gallery+2',
    'https://via.placeholder.com/400x400/a855f7/ffffff?text=Gallery+3',
    'https://via.placeholder.com/400x400/3b82f6/ffffff?text=Gallery+4'
  ]
  
  const index = parseInt(event.target.dataset.index) || 0
  event.target.src = fallbackImages[index] || fallbackImages
  event.target.classList.add('opacity-75')
  console.warn('Preview image failed to load, using fallback')
}

const handleFeaturedImageError = (event) => {
  event.target.src = 'https://via.placeholder.com/400x400/374151/ffffff?text=No+Image'
  event.target.classList.add('opacity-75')
  console.warn('Featured image failed to load, using fallback')
}

const handleAlbumImageError = (event) => {
  event.target.src = 'https://via.placeholder.com/400x300/374151/ffffff?text=No+Cover'
  event.target.classList.add('opacity-75')
  console.warn('Album cover failed to load, using fallback')
}
</script>


<style scoped>
/* Global resets */
:global(body) {
  margin: 0;
  padding: 0;
}

:global(html) {
  margin: 0;
  padding: 0;
}

:global(.min-h-screen) {
  min-height: 100vh;
}

/* Animation keyframes */
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

/* Ensure no gaps between sections */
.py-0 {
  padding-top: 0;
  padding-bottom: 0;
}

/* Custom spacing for seamless sections */
section + section {
  margin-top: 0;
}

/* Remove any default margins */
* {
  box-sizing: border-box;
}
</style>