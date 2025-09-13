<template>
  <footer class="relative bg-slate-900 border-t border-violet-500/20 overflow-hidden">
    <!-- Animated Mesh Background -->
    <div class="absolute inset-0 opacity-20">
      <svg class="w-full h-full animate-mesh" viewBox="0 0 400 400" xmlns="http://www.w3.org/2000/svg">
        <defs>
          <linearGradient id="meshGrad1" x1="0%" y1="0%" x2="100%" y2="100%" gradientUnits="objectBoundingBox">
            <stop offset="0%" stop-color="#8b5cf6" class="animate-gradient-shift">
              <animate attributeName="stop-color" values="#8b5cf6;#06b6d4;#a855f7;#8b5cf6" dur="8s" repeatCount="indefinite"/>
            </stop>
            <stop offset="100%" stop-color="#06b6d4" class="animate-gradient-shift">
              <animate attributeName="stop-color" values="#06b6d4;#a855f7;#8b5cf6;#06b6d4" dur="8s" repeatCount="indefinite"/>
            </stop>
          </linearGradient>
        </defs>
        <path d="M0,100 Q100,50 200,100 T400,100 L400,400 L0,400 Z" fill="url(#meshGrad1)" opacity="0.3">
          <animateTransform attributeName="transform" type="translate" values="0,0;20,-10;0,0" dur="6s" repeatCount="indefinite"/>
        </path>
        <path d="M0,200 Q150,150 300,200 T600,200 L400,400 L0,400 Z" fill="url(#meshGrad1)" opacity="0.2">
          <animateTransform attributeName="transform" type="translate" values="0,0;-15,5;0,0" dur="8s" repeatCount="indefinite"/>
        </path>
      </svg>
    </div>

    <!-- Floating Particles -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
      <div v-for="i in 12" :key="i" 
           :class="`absolute w-1 h-1 rounded-full bg-gradient-to-r from-violet-400 to-cyan-400 animate-float-${i % 4}`"
           :style="`left: ${(i * 8.33) % 100}%; top: ${(i * 7) % 100}%; animation-delay: ${i * 0.5}s;`">
      </div>
    </div>

    <!-- Glass overlay -->
    <div class="absolute inset-0 bg-gradient-to-b from-transparent via-slate-900/50 to-slate-900/80 backdrop-blur-sm"></div>

    <div class="relative max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
      <div class="xl:grid xl:grid-cols-3 xl:gap-8">
        <!-- Brand -->
        <div class="space-y-8 xl:col-span-1 animate-fade-in-up">
          <div class="text-xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-violet-400 via-purple-400 to-cyan-400 animate-shimmer">
            Gallery
          </div>
          <p class="text-slate-300 text-base leading-relaxed">
            A modern, responsive image gallery built with Laravel and Vue.js.
          </p>
          <div class="flex space-x-6">
            <!-- Social links placeholder with enhanced styling -->
            <div class="flex space-x-4">
              <div class="w-8 h-8 rounded-full bg-gradient-to-r from-violet-500/20 to-cyan-500/20 backdrop-blur-sm border border-violet-500/30 flex items-center justify-center hover:scale-110 hover:shadow-lg hover:shadow-violet-500/25 transition-all duration-300 cursor-pointer group">
                <div class="w-3 h-3 rounded-full bg-violet-400 group-hover:bg-cyan-400 transition-colors duration-300"></div>
              </div>
              <div class="w-8 h-8 rounded-full bg-gradient-to-r from-purple-500/20 to-violet-500/20 backdrop-blur-sm border border-purple-500/30 flex items-center justify-center hover:scale-110 hover:shadow-lg hover:shadow-purple-500/25 transition-all duration-300 cursor-pointer group">
                <div class="w-3 h-3 rounded-full bg-purple-400 group-hover:bg-violet-400 transition-colors duration-300"></div>
              </div>
              <div class="w-8 h-8 rounded-full bg-gradient-to-r from-cyan-500/20 to-purple-500/20 backdrop-blur-sm border border-cyan-500/30 flex items-center justify-center hover:scale-110 hover:shadow-lg hover:shadow-cyan-500/25 transition-all duration-300 cursor-pointer group">
                <div class="w-3 h-3 rounded-full bg-cyan-400 group-hover:bg-purple-400 transition-colors duration-300"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Links -->
        <div class="mt-12 grid grid-cols-2 gap-8 xl:mt-0 xl:col-span-2">
          <div class="md:grid md:grid-cols-2 md:gap-8">
            <div class="animate-fade-in-up" style="animation-delay: 0.1s;">
              <h3 class="text-sm font-semibold text-violet-400 tracking-wider uppercase mb-2 relative">
                Gallery
                <div class="absolute bottom-0 left-0 w-0 h-px bg-gradient-to-r from-violet-500 to-cyan-500 group-hover:w-full transition-all duration-500"></div>
              </h3>
              <ul class="mt-4 space-y-4">
                <li>
                  <Link :href="route('gallery.index')" class="group relative text-base text-slate-300 hover:text-transparent hover:bg-clip-text hover:bg-gradient-to-r hover:from-violet-400 hover:to-cyan-400 transition-all duration-300 flex items-center space-x-2">
                    <div class="w-1 h-1 rounded-full bg-violet-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <span>Browse Images</span>
                  </Link>
                </li>
                <li>
                  <Link :href="route('albums.index')" class="group relative text-base text-slate-300 hover:text-transparent hover:bg-clip-text hover:bg-gradient-to-r hover:from-violet-400 hover:to-cyan-400 transition-all duration-300 flex items-center space-x-2">
                    <div class="w-1 h-1 rounded-full bg-purple-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <span>Albums</span>
                  </Link>
                </li>
                <li>
                  <Link :href="route('collections.index')" class="group relative text-base text-slate-300 hover:text-transparent hover:bg-clip-text hover:bg-gradient-to-r hover:from-violet-400 hover:to-cyan-400 transition-all duration-300 flex items-center space-x-2">
                    <div class="w-1 h-1 rounded-full bg-cyan-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <span>Collections</span>
                  </Link>
                </li>
              </ul>
            </div>
            <div class="mt-12 md:mt-0 animate-fade-in-up" style="animation-delay: 0.2s;">
              <h3 class="text-sm font-semibold text-purple-400 tracking-wider uppercase mb-2 relative">
                Account
                <div class="absolute bottom-0 left-0 w-0 h-px bg-gradient-to-r from-purple-500 to-violet-500 group-hover:w-full transition-all duration-500"></div>
              </h3>
              <ul class="mt-4 space-y-4">
                <li v-if="!user">
                  <Link :href="route('login')" class="group relative text-base text-slate-300 hover:text-transparent hover:bg-clip-text hover:bg-gradient-to-r hover:from-purple-400 hover:to-violet-400 transition-all duration-300 flex items-center space-x-2">
                    <div class="w-1 h-1 rounded-full bg-violet-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <span>Sign In</span>
                  </Link>
                </li>
                <li v-if="!user && canRegister">
                  <Link :href="route('register')" class="group relative text-base text-slate-300 hover:text-transparent hover:bg-clip-text hover:bg-gradient-to-r hover:from-purple-400 hover:to-violet-400 transition-all duration-300 flex items-center space-x-2">
                    <div class="w-1 h-1 rounded-full bg-purple-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <span>Register</span>
                  </Link>
                </li>
                <li v-if="user">
                  <Link :href="route('dashboard')" class="group relative text-base text-slate-300 hover:text-transparent hover:bg-clip-text hover:bg-gradient-to-r hover:from-purple-400 hover:to-violet-400 transition-all duration-300 flex items-center space-x-2">
                    <div class="w-1 h-1 rounded-full bg-violet-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <span>Dashboard</span>
                  </Link>
                </li>
                <li v-if="user">
                  <Link :href="route('profile.edit')" class="group relative text-base text-slate-300 hover:text-transparent hover:bg-clip-text hover:bg-gradient-to-r hover:from-purple-400 hover:to-violet-400 transition-all duration-300 flex items-center space-x-2">
                    <div class="w-1 h-1 rounded-full bg-purple-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <span>Profile</span>
                  </Link>
                </li>
              </ul>
            </div>
          </div>
          <div class="md:grid md:grid-cols-2 md:gap-8">
            <div class="animate-fade-in-up" style="animation-delay: 0.3s;">
              <h3 class="text-sm font-semibold text-cyan-400 tracking-wider uppercase mb-2 relative">
                Support
                <div class="absolute bottom-0 left-0 w-0 h-px bg-gradient-to-r from-cyan-500 to-purple-500 group-hover:w-full transition-all duration-500"></div>
              </h3>
              <ul class="mt-4 space-y-4">
                <li>
                  <a href="#" class="group relative text-base text-slate-300 hover:text-transparent hover:bg-clip-text hover:bg-gradient-to-r hover:from-cyan-400 hover:to-purple-400 transition-all duration-300 flex items-center space-x-2">
                    <div class="w-1 h-1 rounded-full bg-cyan-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <span>Help Center</span>
                  </a>
                </li>
                <li>
                  <a href="#" class="group relative text-base text-slate-300 hover:text-transparent hover:bg-clip-text hover:bg-gradient-to-r hover:from-cyan-400 hover:to-purple-400 transition-all duration-300 flex items-center space-x-2">
                    <div class="w-1 h-1 rounded-full bg-purple-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <span>Contact Us</span>
                  </a>
                </li>
                <li>
                  <a href="#" class="group relative text-base text-slate-300 hover:text-transparent hover:bg-clip-text hover:bg-gradient-to-r hover:from-cyan-400 hover:to-purple-400 transition-all duration-300 flex items-center space-x-2">
                    <div class="w-1 h-1 rounded-full bg-violet-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <span>API Documentation</span>
                  </a>
                </li>
              </ul>
            </div>
            <div class="mt-12 md:mt-0 animate-fade-in-up" style="animation-delay: 0.4s;">
              <h3 class="text-sm font-semibold text-violet-400 tracking-wider uppercase mb-2 relative">
                Legal
                <div class="absolute bottom-0 left-0 w-0 h-px bg-gradient-to-r from-violet-500 to-cyan-500 group-hover:w-full transition-all duration-500"></div>
              </h3>
              <ul class="mt-4 space-y-4">
                <li>
                  <Link :href="route('privacy')" class="group relative text-base text-slate-300 hover:text-transparent hover:bg-clip-text hover:bg-gradient-to-r hover:from-violet-400 hover:to-cyan-400 transition-all duration-300 flex items-center space-x-2">
                    <div class="w-1 h-1 rounded-full bg-violet-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <span>Privacy Policy</span>
                  </Link>
                </li>
                <li>
                  <Link :href="route('terms')" class="group relative text-base text-slate-300 hover:text-transparent hover:bg-clip-text hover:bg-gradient-to-r hover:from-violet-400 hover:to-cyan-400 transition-all duration-300 flex items-center space-x-2">
                    <div class="w-1 h-1 rounded-full bg-cyan-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <span>Terms of Service</span>
                  </Link>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>

      <div class="mt-12 border-t border-gradient-to-r from-violet-500/20 via-purple-500/20 to-cyan-500/20 pt-8 relative">
        <!-- Gradient border -->
        <div class="absolute top-0 left-0 w-full h-px bg-gradient-to-r from-transparent via-violet-500/40 to-transparent"></div>
        
        <div class="relative">
          <p class="text-base text-slate-400 xl:text-center group hover:text-transparent hover:bg-clip-text hover:bg-gradient-to-r hover:from-violet-400 hover:to-cyan-400 transition-all duration-500 cursor-default">
            &copy; {{ new Date().getFullYear() }} Gallery. All rights reserved.
            <span class="inline-block ml-2 w-2 h-2 rounded-full bg-gradient-to-r from-violet-500 to-cyan-500 animate-pulse"></span>
          </p>
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup>
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import route from 'ziggy-js'

const page = usePage()

// Safe computed properties with null checks
const user = computed(() => {
  return page.props?.auth?.user || null
})

const canRegister = computed(() => {
  return page.props?.canRegister ?? true
})
</script>

<style scoped>
/* Mesh animation */
@keyframes mesh {
  0%, 100% { transform: translate(0, 0) scale(1); }
  33% { transform: translate(10px, -5px) scale(1.02); }
  66% { transform: translate(-5px, 10px) scale(0.98); }
}

.animate-mesh {
  animation: mesh 12s ease-in-out infinite;
}

/* Floating particles */
@keyframes float-0 {
  0%, 100% { transform: translateY(0px) translateX(0px); opacity: 0.4; }
  50% { transform: translateY(-20px) translateX(10px); opacity: 0.8; }
}

@keyframes float-1 {
  0%, 100% { transform: translateY(0px) translateX(0px); opacity: 0.3; }
  50% { transform: translateY(-15px) translateX(-8px); opacity: 0.7; }
}

@keyframes float-2 {
  0%, 100% { transform: translateY(0px) translateX(0px); opacity: 0.5; }
  50% { transform: translateY(-25px) translateX(5px); opacity: 0.9; }
}

@keyframes float-3 {
  0%, 100% { transform: translateY(0px) translateX(0px); opacity: 0.4; }
  50% { transform: translateY(-18px) translateX(-12px); opacity: 0.6; }
}

.animate-float-0 { animation: float-0 4s ease-in-out infinite; }
.animate-float-1 { animation: float-1 5s ease-in-out infinite; }
.animate-float-2 { animation: float-2 6s ease-in-out infinite; }
.animate-float-3 { animation: float-3 7s ease-in-out infinite; }

/* Shimmer effect */
@keyframes shimmer {
  0% { background-position: -200% center; }
  100% { background-position: 200% center; }
}

.animate-shimmer {
  background: linear-gradient(90deg, #8b5cf6 0%, #06b6d4 50%, #a855f7 100%);
  background-size: 200% 100%;
  -webkit-background-clip: text;
  background-clip: text;
  animation: shimmer 3s ease-in-out infinite;
}

/* Fade in up animation */
@keyframes fade-in-up {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in-up {
  animation: fade-in-up 0.6s ease-out forwards;
  opacity: 0;
}

/* Enhanced hover effects */
.group:hover .w-0 {
  width: 100%;
}

/* Gradient border animation */
.border-gradient-to-r {
  border-image: linear-gradient(90deg, rgba(139, 92, 246, 0.2), rgba(168, 85, 247, 0.2), rgba(6, 182, 212, 0.2)) 1;
}
</style>