<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <!-- Logo and primary navigation -->
          <div class="flex">
            <!-- Logo -->
            <div class="flex-shrink-0 flex items-center">
              <Link :href="route('welcome')" class="text-xl font-bold text-gray-900">
                Gallery
              </Link>
            </div>

            <!-- Primary Navigation Links -->
            <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
              <NavLink :href="route('gallery.index')" :active="route().current('gallery.index')">
                Gallery
              </NavLink>
              <NavLink :href="route('albums.index')" :active="route().current('albums.*')">
                Albums
              </NavLink>
              <NavLink :href="route('collections.index')" :active="route().current('collections.*')">
                Collections
              </NavLink>
            </div>
          </div>

          <!-- Secondary navigation -->
          <div class="hidden sm:flex sm:items-center sm:space-x-4">
            <!-- Search -->
            <div class="relative">
              <input
                v-model="searchQuery"
                @keydown.enter="performSearch"
                type="text"
                placeholder="Search images, albums..."
                class="w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
              >
              <MagnifyingGlassIcon class="absolute left-3 top-2.5 h-4 w-4 text-gray-400" />
            </div>

            <!-- User menu -->
            <div v-if="user" class="relative">
              <Dropdown align="right" width="48">
                <template #trigger>
                  <button
                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150"
                  >
                    <div class="flex items-center space-x-2">
                      <UserAvatar
                        :user="user"
                        size="sm"
                      />
                      <span>{{ user.name || 'User' }}</span>
                    </div>
                    <ChevronDownIcon class="ml-2 h-4 w-4" />
                  </button>
                </template>

                <template #content>
                  <DropdownLink :href="route('dashboard')">
                    Dashboard
                  </DropdownLink>
                  <DropdownLink :href="route('my.images')">
                    My Images
                  </DropdownLink>
                  <DropdownLink :href="route('my.albums')">
                    My Albums
                  </DropdownLink>
                  <DropdownLink :href="route('upload')" v-if="canUpload">
                    Upload Images
                  </DropdownLink>
                  <div class="border-t border-gray-100"></div>
                  <DropdownLink :href="route('profile.edit')">
                    Profile
                  </DropdownLink>
                  <div class="border-t border-gray-100" v-if="isAdmin"></div>
                  <DropdownLink :href="route('admin.system.index')" v-if="isAdmin">
                    Admin Panel
                  </DropdownLink>
                  <div class="border-t border-gray-100"></div>
                  <DropdownLink :href="route('logout')" method="post" as="button">
                    Log Out
                  </DropdownLink>
                </template>
              </Dropdown>
            </div>

            <!-- Guest links -->
            <div v-else class="flex items-center space-x-4">
              <Link
                :href="route('login')"
                class="text-sm text-gray-700 underline"
              >
                Log in
              </Link>
              <Link
                v-if="canRegister"
                :href="route('register')"
                class="px-4 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700 transition"
              >
                Register
              </Link>
            </div>
          </div>

          <!-- Mobile menu button -->
          <div class="sm:hidden flex items-center">
            <button
              @click="showingNavigationDropdown = !showingNavigationDropdown"
              class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition"
            >
              <Bars3Icon v-if="!showingNavigationDropdown" class="h-6 w-6" />
              <XMarkIcon v-else class="h-6 w-6" />
            </button>
          </div>
        </div>
      </div>

      <!-- Mobile Navigation Menu -->
      <div
        :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }"
        class="sm:hidden"
      >
        <div class="pt-2 pb-3 space-y-1">
          <ResponsiveNavLink :href="route('gallery.index')" :active="route().current('gallery.index')">
            Gallery
          </ResponsiveNavLink>
          <ResponsiveNavLink :href="route('albums.index')" :active="route().current('albums.*')">
            Albums
          </ResponsiveNavLink>
          <ResponsiveNavLink :href="route('collections.index')" :active="route().current('collections.*')">
            Collections
          </ResponsiveNavLink>
        </div>

        <!-- Mobile user menu -->
        <div v-if="user" class="pt-4 pb-1 border-t border-gray-200">
          <div class="px-4">
            <div class="font-medium text-base text-gray-800">
              {{ user.name || 'User' }}
            </div>
            <div class="font-medium text-sm text-gray-500">
              {{ user.email || '' }}
            </div>
          </div>

          <div class="mt-3 space-y-1">
            <ResponsiveNavLink :href="route('dashboard')">
              Dashboard
            </ResponsiveNavLink>
            <ResponsiveNavLink :href="route('my.images')">
              My Images
            </ResponsiveNavLink>
            <ResponsiveNavLink :href="route('upload')" v-if="canUpload">
              Upload Images
            </ResponsiveNavLink>
            <ResponsiveNavLink :href="route('profile.edit')">
              Profile
            </ResponsiveNavLink>
            <ResponsiveNavLink :href="route('admin.system.index')" v-if="isAdmin">
              Admin Panel
            </ResponsiveNavLink>
            <ResponsiveNavLink :href="route('logout')" method="post" as="button">
              Log Out
            </ResponsiveNavLink>
          </div>
        </div>
      </div>
    </nav>

    <!-- Page Heading -->
    <header class="bg-white shadow" v-if="$slots.header">
      <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <slot name="header" />
      </div>
    </header>

    <!-- Page Content -->
    <main class="flex-1">
      <slot />
    </main>

    <!-- Footer -->
    <Footer v-if="showFooter" />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import route from 'ziggy-js'
import {
  Bars3Icon,
  XMarkIcon,
  ChevronDownIcon,
  MagnifyingGlassIcon,
} from '@heroicons/vue/24/outline'

import Dropdown from '@/Components/Dropdown.vue'
import DropdownLink from '@/Components/DropdownLink.vue'
import NavLink from '@/Components/NavLink.vue'
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue'
import UserAvatar from '@/Components/UserAvatar.vue'
import Footer from '@/Components/Footer.vue'

const page = usePage()

const showingNavigationDropdown = ref(false)
const searchQuery = ref('')

// Safe computed properties
const user = computed(() => {
  return page.props?.auth?.user || null
})

const canUpload = computed(() => {
  return user.value && (
    user.value.can_upload || 
    user.value.roles?.some(role => role === 'admin' || role === 'editor') ||
    user.value.permissions?.includes('upload_images')
  )
})

const canRegister = computed(() => {
  return page.props?.canRegister ?? true
})

const isAdmin = computed(() => {
  return user.value?.roles?.some(role => role === 'admin') || 
         user.value?.roles?.includes('admin') ||
         false
})

const showFooter = computed(() => {
  // Only show footer on certain pages
  const currentRoute = route().current()
  return !currentRoute?.includes('admin') && !currentRoute?.includes('upload')
})

const performSearch = () => {
  if (searchQuery.value.trim()) {
    window.location = route('search', { q: searchQuery.value })
  }
}
</script>
/*<template>
  <div class="min-h-screen bg-slate-900 relative overflow-hidden">
    <!-- Animated Mesh Background -->
    <div class="absolute inset-0 opacity-20">
      <svg class="w-full h-full animate-mesh-float" viewBox="0 0 400 400" xmlns="http://www.w3.org/2000/svg">
        <defs>
          <linearGradient id="meshGradient" x1="0%" y1="0%" x2="100%" y2="100%" gradientTransform="rotate(45)">
            <stop offset="0%" class="animate-gradient-shift-1">
              <animate attributeName="stop-color" values="#8b5cf6;#06b6d4;#a855f7;#8b5cf6" dur="8s" repeatCount="indefinite"/>
            </stop>
            <stop offset="100%" class="animate-gradient-shift-2">
              <animate attributeName="stop-color" values="#06b6d4;#a855f7;#8b5cf6;#06b6d4" dur="8s" repeatCount="indefinite"/>
            </stop>
          </linearGradient>
        </defs>
        <path d="M0,100 Q100,50 200,100 T400,100 L400,0 L0,0 Z" fill="url(#meshGradient)" opacity="0.3">
          <animateTransform attributeName="transform" type="translate" values="0,0;50,20;0,0" dur="12s" repeatCount="indefinite"/>
        </path>
        <path d="M0,200 Q150,150 300,200 T600,200 L600,100 L0,100 Z" fill="url(#meshGradient)" opacity="0.2">
          <animateTransform attributeName="transform" type="translate" values="0,0;-30,15;0,0" dur="10s" repeatCount="indefinite"/>
        </path>
      </svg>
    </div>

    <!-- Floating Particles System -->
    <div class="absolute inset-0 pointer-events-none overflow-hidden">
      <div 
        v-for="i in 20" 
        :key="`particle-${i}`"
        :class="[
          'absolute w-1 h-1 rounded-full opacity-40',
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

    <!-- Navigation -->
    <nav class="relative z-50 bg-slate-900/80 backdrop-blur-xl shadow-2xl border-b border-white/10">
      <div class="absolute inset-0 bg-gradient-to-r from-violet-500/10 via-purple-500/5 to-cyan-500/10"></div>
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="flex justify-between h-16">
          <!-- Logo and primary navigation -->
          <div class="flex">
            <!-- Logo -->
            <div class="flex-shrink-0 flex items-center group">
              <Link :href="route('welcome')" class="text-xl font-bold bg-gradient-to-r from-violet-400 via-purple-400 to-cyan-400 bg-clip-text text-transparent hover:scale-105 transition-transform duration-300 animate-shimmer-text">
                Gallery
              </Link>
            </div>

            <!-- Primary Navigation Links -->
            <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
              <NavLink :href="route('gallery.index')" :active="route().current('gallery.index')" class="nav-link-enhanced">
                Gallery
              </NavLink>
              <NavLink :href="route('albums.index')" :active="route().current('albums.*')" class="nav-link-enhanced">
                Albums
              </NavLink>
              <NavLink :href="route('collections.index')" :active="route().current('collections.*')" class="nav-link-enhanced">
                Collections
              </NavLink>
            </div>
          </div>

          <!-- Secondary navigation -->
          <div class="hidden sm:flex sm:items-center sm:space-x-4">
            <!-- Search -->
            <div class="relative group">
              <input
                v-model="searchQuery"
                @keydown.enter="performSearch"
                type="text"
                placeholder="Search images, albums..."
                class="w-64 pl-10 pr-4 py-2 bg-white/10 backdrop-blur-xl border border-white/20 rounded-xl text-slate-200 placeholder-slate-400 focus:ring-2 focus:ring-violet-500/50 focus:border-violet-500/50 transition-all duration-500 hover:bg-white/15 focus:bg-white/15 focus:scale-105"
              >
              <MagnifyingGlassIcon class="absolute left-3 top-2.5 h-4 w-4 text-violet-400 group-hover:text-cyan-400 transition-colors duration-300" />
              <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-violet-500/20 to-cyan-500/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300 -z-10"></div>
            </div>

            <!-- User menu -->
            <div v-if="user" class="relative">
              <Dropdown align="right" width="48">
                <template #trigger>
                  <button
                    class="inline-flex items-center px-3 py-2 border border-white/20 text-sm leading-4 font-medium rounded-xl text-slate-200 bg-white/10 backdrop-blur-xl hover:bg-white/15 hover:text-white hover:scale-105 focus:outline-none transition-all ease-in-out duration-300 hover:shadow-lg hover:shadow-violet-500/25"
                  >
                    <div class="flex items-center space-x-2">
                      <UserAvatar
                        :user="user"
                        size="sm"
                      />
                      <span>{{ user.name || 'User' }}</span>
                    </div>
                    <ChevronDownIcon class="ml-2 h-4 w-4 group-hover:rotate-180 transition-transform duration-300" />
                  </button>
                </template>

                <template #content>
                  <div class="py-1 bg-slate-900/95 backdrop-blur-xl border border-white/20 rounded-xl shadow-2xl">
                    <DropdownLink :href="route('dashboard')" class="dropdown-link-enhanced">
                      Dashboard
                    </DropdownLink>
                    <DropdownLink :href="route('my.images')" class="dropdown-link-enhanced">
                      My Images
                    </DropdownLink>
                    <DropdownLink :href="route('my.albums')" class="dropdown-link-enhanced">
                      My Albums
                    </DropdownLink>
                    <DropdownLink :href="route('upload')" v-if="canUpload" class="dropdown-link-enhanced">
                      Upload Images
                    </DropdownLink>
                    <div class="border-t border-white/10 my-1"></div>
                    <DropdownLink :href="route('profile.edit')" class="dropdown-link-enhanced">
                      Profile
                    </DropdownLink>
                    <div class="border-t border-white/10 my-1" v-if="isAdmin"></div>
                    <DropdownLink :href="route('admin.system.index')" v-if="isAdmin" class="dropdown-link-enhanced">
                      Admin Panel
                    </DropdownLink>
                    <div class="border-t border-white/10 my-1"></div>
                    <DropdownLink :href="route('logout')" method="post" as="button" class="dropdown-link-enhanced text-red-400 hover:text-red-300">
                      Log Out
                    </DropdownLink>
                  </div>
                </template>
              </Dropdown>
            </div>

            <!-- Guest links -->
            <div v-else class="flex items-center space-x-4">
              <Link
                :href="route('login')"
                class="text-sm text-slate-300 hover:text-white underline decoration-violet-500/50 hover:decoration-violet-400 underline-offset-4 transition-all duration-300"
              >
                Log in
              </Link>
              <Link
                v-if="canRegister"
                :href="route('register')"
                class="px-4 py-2 bg-gradient-to-r from-violet-600 via-purple-600 to-cyan-600 text-white text-sm rounded-xl hover:from-violet-500 hover:via-purple-500 hover:to-cyan-500 transition-all duration-300 hover:scale-105 hover:shadow-lg hover:shadow-violet-500/25 animate-gradient-shift"
              >
                Register
              </Link>
            </div>
          </div>

          <!-- Mobile menu button -->
          <div class="sm:hidden flex items-center">
            <button
              @click="showingNavigationDropdown = !showingNavigationDropdown"
              class="inline-flex items-center justify-center p-2 rounded-xl text-slate-400 hover:text-white hover:bg-white/10 focus:outline-none focus:bg-white/10 focus:text-white transition-all duration-300 hover:scale-110"
            >
              <Bars3Icon v-if="!showingNavigationDropdown" class="h-6 w-6" />
              <XMarkIcon v-else class="h-6 w-6" />
            </button>
          </div>
        </div>
      </div>

      <!-- Mobile Navigation Menu -->
      <div
        :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }"
        class="sm:hidden bg-slate-900/95 backdrop-blur-xl border-t border-white/10"
      >
        <div class="pt-2 pb-3 space-y-1 px-4">
          <ResponsiveNavLink :href="route('gallery.index')" :active="route().current('gallery.index')" class="responsive-nav-link-enhanced">
            Gallery
          </ResponsiveNavLink>
          <ResponsiveNavLink :href="route('albums.index')" :active="route().current('albums.*')" class="responsive-nav-link-enhanced">
            Albums
          </ResponsiveNavLink>
          <ResponsiveNavLink :href="route('collections.index')" :active="route().current('collections.*')" class="responsive-nav-link-enhanced">
            Collections
          </ResponsiveNavLink>
        </div>

        <!-- Mobile user menu -->
        <div v-if="user" class="pt-4 pb-1 border-t border-white/10">
          <div class="px-4">
            <div class="font-medium text-base text-white">
              {{ user.name || 'User' }}
            </div>
            <div class="font-medium text-sm text-slate-400">
              {{ user.email || '' }}
            </div>
          </div>

          <div class="mt-3 space-y-1 px-4">
            <ResponsiveNavLink :href="route('dashboard')" class="responsive-nav-link-enhanced">
              Dashboard
            </ResponsiveNavLink>
            <ResponsiveNavLink :href="route('my.images')" class="responsive-nav-link-enhanced">
              My Images
            </ResponsiveNavLink>
            <ResponsiveNavLink :href="route('upload')" v-if="canUpload" class="responsive-nav-link-enhanced">
              Upload Images
            </ResponsiveNavLink>
            <ResponsiveNavLink :href="route('profile.edit')" class="responsive-nav-link-enhanced">
              Profile
            </ResponsiveNavLink>
            <ResponsiveNavLink :href="route('admin.system.index')" v-if="isAdmin" class="responsive-nav-link-enhanced">
              Admin Panel
            </ResponsiveNavLink>
            <ResponsiveNavLink :href="route('logout')" method="post" as="button" class="responsive-nav-link-enhanced text-red-400">
              Log Out
            </ResponsiveNavLink>
          </div>
        </div>
      </div>
    </nav>

    <!-- Page Heading -->
    <header class="relative z-40 bg-slate-800/50 backdrop-blur-xl shadow-2xl border-b border-white/10 animate-fade-in-up" v-if="$slots.header">
      <div class="absolute inset-0 bg-gradient-to-r from-violet-600/10 via-purple-600/5 to-cyan-600/10"></div>
      <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 relative z-10">
        <slot name="header" />
      </div>
    </header>

    <!-- Page Content -->
    <main class="flex-1 relative z-30 animate-fade-in-up" style="animation-delay: 0.2s;">
      <slot />
    </main>

    <!-- Footer -->
    <Footer v-if="showFooter" class="relative z-30 animate-fade-in-up" style="animation-delay: 0.4s;" />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import route from 'ziggy-js'
import {
  Bars3Icon,
  XMarkIcon,
  ChevronDownIcon,
  MagnifyingGlassIcon,
} from '@heroicons/vue/24/outline'

import Dropdown from '@/Components/Dropdown.vue'
import DropdownLink from '@/Components/DropdownLink.vue'
import NavLink from '@/Components/NavLink.vue'
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue'
import UserAvatar from '@/Components/UserAvatar.vue'
import Footer from '@/Components/Footer.vue'

const page = usePage()

const showingNavigationDropdown = ref(false)
const searchQuery = ref('')

// Safe computed properties
const user = computed(() => {
  return page.props?.auth?.user || null
})

const canUpload = computed(() => {
  return user.value && (
    user.value.can_upload || 
    user.value.roles?.some(role => role === 'admin' || role === 'editor') ||
    user.value.permissions?.includes('upload_images')
  )
})

const canRegister = computed(() => {
  return page.props?.canRegister ?? true
})

const isAdmin = computed(() => {
  return user.value?.roles?.some(role => role === 'admin') || 
         user.value?.roles?.includes('admin') ||
         false
})

const showFooter = computed(() => {
  // Only show footer on certain pages
  const currentRoute = route().current()
  return !currentRoute?.includes('admin') && !currentRoute?.includes('upload')
})

const performSearch = () => {
  if (searchQuery.value.trim()) {
    window.location = route('search', { q: searchQuery.value })
  }
}
</script>

<style scoped>
@keyframes shimmer-text {
  0% { background-position: -200% center; }
  100% { background-position: 200% center; }
}

@keyframes mesh-float {
  0%, 100% { transform: translateY(0px) rotate(0deg); }
  33% { transform: translateY(-20px) rotate(1deg); }
  66% { transform: translateY(-10px) rotate(-1deg); }
}

@keyframes gradient-shift {
  0%, 100% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
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

.animate-mesh-float {
  animation: mesh-float 15s ease-in-out infinite;
}

.animate-gradient-shift {
  background-size: 200% 200%;
  animation: gradient-shift 4s ease infinite;
}

.animate-fade-in-up {
  animation: fade-in-up 0.8s ease-out forwards;
}

.animate-float-particle-1 { animation: float-particle-1 8s ease-in-out infinite; }
.animate-float-particle-2 { animation: float-particle-2 10s ease-in-out infinite; }
.animate-float-particle-3 { animation: float-particle-3 7s ease-in-out infinite; }
.animate-float-particle-4 { animation: float-particle-4 9s ease-in-out infinite; }
.animate-float-particle-5 { animation: float-particle-5 11s ease-in-out infinite; }

/* Enhanced Navigation Links */
.nav-link-enhanced {
  @apply text-slate-300 hover:text-white transition-all duration-300 relative;
}

.nav-link-enhanced:hover {
  transform: translateY(-2px);
}

.nav-link-enhanced.active {
  @apply text-violet-400;
}

/* Enhanced Dropdown Links */
.dropdown-link-enhanced {
  @apply text-slate-300 hover:text-white hover:bg-white/10 transition-all duration-300 px-4 py-2 rounded-lg mx-2 my-1;
}

.dropdown-link-enhanced:hover {
  transform: translateX(4px);
}

/* Enhanced Responsive Navigation Links */
.responsive-nav-link-enhanced {
  @apply text-slate-300 hover:text-white hover:bg-white/10 transition-all duration-300 rounded-lg;
}

.responsive-nav-link-enhanced:hover {
  transform: translateX(8px);
}

.responsive-nav-link-enhanced.active {
  @apply text-violet-400 bg-violet-500/20;
}
</style>