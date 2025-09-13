<template>
  <div class="min-h-screen bg-gray-100">
    <!-- Sidebar -->
    <div class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg">
      <div class="flex h-16 items-center justify-center border-b border-gray-200">
        <h1 class="text-xl font-bold text-gray-900">Admin Panel</h1>
      </div>
      
      <nav class="mt-8 px-4">
        <div class="space-y-2">
          <SidebarLink 
            :href="route('admin.system.index')" 
            :active="route().current('admin.system.index')"
            icon="ChartBarIcon"
          >
            Dashboard
          </SidebarLink>
          
          <SidebarLink 
            :href="route('admin.users.index')" 
            :active="route().current('admin.users.*')"
            icon="UsersIcon"
          >
            Users
          </SidebarLink>
          
          <SidebarLink 
            :href="route('admin.moderation.index')" 
            :active="route().current('admin.moderation.*')"
            icon="ShieldCheckIcon"
          >
            Moderation
          </SidebarLink>
          
          <SidebarLink 
            :href="route('admin.system.analytics')" 
            :active="route().current('admin.system.analytics')"
            icon="ChartPieIcon"
          >
            Analytics
          </SidebarLink>
          
          <SidebarLink 
            :href="route('admin.system.settings')" 
            :active="route().current('admin.system.settings')"
            icon="CogIcon"
          >
            Settings
          </SidebarLink>
        </div>
      </nav>
    </div>

    <!-- Main content -->
    <div class="pl-64">
      <!-- Top bar -->
      <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="px-6 py-4">
          <div class="flex items-center justify-between">
            <div v-if="$slots.header">
              <slot name="header" />
            </div>
            
            <div class="flex items-center space-x-4">
              <Link :href="route('dashboard')" class="text-sm text-gray-600 hover:text-gray-900">
                ← Back to Site
              </Link>
              
              <Dropdown align="right" width="48">
                <template #trigger>
                  <button class="flex items-center text-sm font-medium text-gray-700 hover:text-gray-900">
                    {{ $page.props.auth.user.name }}
                    <ChevronDownIcon class="ml-2 h-4 w-4" />
                  </button>
                </template>
                
                <template #content>
                  <DropdownLink :href="route('profile.edit')">Profile</DropdownLink>
                  <DropdownLink :href="route('logout')" method="post" as="button">
                    Log Out
                  </DropdownLink>
                </template>
              </Dropdown>
            </div>
          </div>
        </div>
      </header>

      <!-- Page content -->
      <main class="p-6">
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import route from 'ziggy-js'
import {
  ChartBarIcon,
  UsersIcon,
  ShieldCheckIcon,
  ChartPieIcon,
  CogIcon,
  ChevronDownIcon,
} from '@heroicons/vue/24/outline'

import SidebarLink from '@/Components/Admin/SidebarLink.vue'
import Dropdown from '@/Components/Dropdown.vue'
import DropdownLink from '@/Components/DropdownLink.vue'
</script>
/* <template>
  <div class="min-h-screen bg-slate-950 relative overflow-hidden">
    <!-- Animated mesh background -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
      <svg class="absolute inset-0 w-full h-full opacity-20" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
        <defs>
          <linearGradient id="meshGradient" x1="0%" y1="0%" x2="100%" y2="100%" gradientTransform="rotate(45)">
            <stop offset="0%" class="animate-colorShift1">
              <animate attributeName="stop-color" values="#8b5cf6;#06b6d4;#a855f7;#8b5cf6" dur="8s" repeatCount="indefinite" />
            </stop>
            <stop offset="50%" class="animate-colorShift2">
              <animate attributeName="stop-color" values="#06b6d4;#a855f7;#8b5cf6;#06b6d4" dur="8s" repeatCount="indefinite" />
            </stop>
            <stop offset="100%" class="animate-colorShift3">
              <animate attributeName="stop-color" values="#a855f7;#8b5cf6;#06b6d4;#a855f7" dur="8s" repeatCount="indefinite" />
            </stop>
          </linearGradient>
        </defs>
        <rect width="100" height="100" fill="url(#meshGradient)" opacity="0.1">
          <animateTransform attributeName="transform" attributeType="XML" type="translate" values="0,0; -10,5; 0,0" dur="10s" repeatCount="indefinite" />
        </rect>
        <circle cx="20" cy="20" r="2" fill="url(#meshGradient)" opacity="0.3">
          <animateTransform attributeName="transform" attributeType="XML" type="translate" values="0,0; 5,-3; 0,0" dur="6s" repeatCount="indefinite" />
        </circle>
        <circle cx="80" cy="30" r="1.5" fill="url(#meshGradient)" opacity="0.4">
          <animateTransform attributeName="transform" attributeType="XML" type="translate" values="0,0; -3,7; 0,0" dur="8s" repeatCount="indefinite" />
        </circle>
        <circle cx="50" cy="70" r="2.5" fill="url(#meshGradient)" opacity="0.2">
          <animateTransform attributeName="transform" attributeType="XML" type="translate" values="0,0; 8,-5; 0,0" dur="12s" repeatCount="indefinite" />
        </circle>
      </svg>
    </div>

    <!-- Floating particle system -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
      <div v-for="i in 20" :key="i" 
           class="absolute w-1 h-1 rounded-full animate-float opacity-30"
           :class="getParticleClass(i)"
           :style="getParticleStyle(i)">
      </div>
    </div>

    <!-- Enhanced Sidebar -->
    <div class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-900/80 backdrop-blur-xl border-r border-slate-700/50 shadow-2xl shadow-violet-500/10">
      <!-- Sidebar glow effect -->
      <div class="absolute inset-0 bg-gradient-to-r from-violet-500/5 via-transparent to-cyan-500/5 pointer-events-none"></div>
      
      <!-- Logo header with enhanced styling -->
      <div class="relative flex h-16 items-center justify-center border-b border-slate-700/50 bg-slate-800/50 backdrop-blur-sm">
        <div class="absolute inset-0 bg-gradient-to-r from-violet-600/10 via-purple-600/10 to-cyan-600/10"></div>
        <h1 class="relative text-xl font-bold bg-gradient-to-r from-violet-400 via-purple-400 to-cyan-400 bg-clip-text text-transparent animate-shimmer">
          Admin Panel
        </h1>
        <!-- Floating particles around logo -->
        <div class="absolute top-2 left-4 w-1 h-1 bg-violet-400 rounded-full animate-pulse opacity-60"></div>
        <div class="absolute bottom-3 right-6 w-0.5 h-0.5 bg-cyan-400 rounded-full animate-ping opacity-40"></div>
      </div>
      
      <!-- Enhanced Navigation -->
      <nav class="mt-8 px-4 relative">
        <div class="space-y-2">
          <SidebarLink 
            :href="route('admin.system.index')"
            :active="route().current('admin.system.index')"
            icon="ChartBarIcon"
            class="group relative overflow-hidden rounded-xl transition-all duration-300 hover:scale-105 hover:shadow-lg hover:shadow-violet-500/20"
          >
            Dashboard
          </SidebarLink>
          
          <SidebarLink 
            :href="route('admin.users.index')"
            :active="route().current('admin.users.*')"
            icon="UsersIcon"
            class="group relative overflow-hidden rounded-xl transition-all duration-300 hover:scale-105 hover:shadow-lg hover:shadow-cyan-500/20"
          >
            Users
          </SidebarLink>
          
          <SidebarLink 
            :href="route('admin.moderation.index')"
            :active="route().current('admin.moderation.*')"
            icon="ShieldCheckIcon"
            class="group relative overflow-hidden rounded-xl transition-all duration-300 hover:scale-105 hover:shadow-lg hover:shadow-purple-500/20"
          >
            Moderation
          </SidebarLink>
          
          <SidebarLink 
            :href="route('admin.system.analytics')"
            :active="route().current('admin.system.analytics')"
            icon="ChartPieIcon"
            class="group relative overflow-hidden rounded-xl transition-all duration-300 hover:scale-105 hover:shadow-lg hover:shadow-violet-500/20"
          >
            Analytics
          </SidebarLink>
          
          <SidebarLink 
            :href="route('admin.system.settings')"
            :active="route().current('admin.system.settings')"
            icon="CogIcon"
            class="group relative overflow-hidden rounded-xl transition-all duration-300 hover:scale-105 hover:shadow-lg hover:shadow-cyan-500/20"
          >
            Settings
          </SidebarLink>
        </div>
      </nav>
    </div>

    <!-- Main content area -->
    <div class="pl-64">
      <!-- Enhanced Top bar -->
      <header class="bg-slate-900/60 backdrop-blur-xl shadow-xl shadow-slate-900/20 border-b border-slate-700/50 relative">
        <!-- Header glow effect -->
        <div class="absolute inset-0 bg-gradient-to-r from-violet-500/5 via-transparent to-cyan-500/5 pointer-events-none"></div>
        
        <div class="relative px-6 py-4">
          <div class="flex items-center justify-between">
            <div v-if="$slots.header" class="animate-fadeInUp">
              <slot name="header" />
            </div>
            
            <!-- Enhanced user controls -->
            <div class="flex items-center space-x-6">
              <!-- Back to site link with enhanced styling -->
              <Link 
                :href="route('dashboard')" 
                class="group relative inline-flex items-center text-sm font-medium text-slate-300 hover:text-white transition-all duration-300 hover:scale-105"
              >
                <div class="absolute inset-0 bg-gradient-to-r from-violet-500/0 to-cyan-500/0 group-hover:from-violet-500/20 group-hover:to-cyan-500/20 rounded-lg transition-all duration-300 -z-10"></div>
                <span class="relative px-3 py-2">
                  ← Back to Site
                </span>
              </Link>
              
              <!-- Enhanced Dropdown -->
              <Dropdown align="right" width="48" class="relative">
                <template #trigger>
                  <button class="group relative flex items-center px-4 py-2 text-sm font-medium text-slate-300 hover:text-white bg-slate-800/50 hover:bg-slate-700/50 backdrop-blur-sm rounded-xl border border-slate-600/30 hover:border-violet-500/40 transition-all duration-300 hover:scale-105 hover:shadow-lg hover:shadow-violet-500/20">
                    <!-- Button glow effect -->
                    <div class="absolute inset-0 bg-gradient-to-r from-violet-500/0 to-cyan-500/0 group-hover:from-violet-500/10 group-hover:to-cyan-500/10 rounded-xl transition-all duration-300"></div>
                    
                    <span class="relative">{{ $page.props.auth.user.name }}</span>
                    <ChevronDownIcon class="relative ml-2 h-4 w-4 transition-transform duration-300 group-hover:rotate-180" />
                  </button>
                </template>
                
                <template #content>
                  <DropdownLink :href="route('profile.edit')">Profile</DropdownLink>
                  <DropdownLink :href="route('logout')" method="post" as="button">
                    Log Out
                  </DropdownLink>
                </template>
              </Dropdown>
            </div>
          </div>
        </div>
        
        <!-- Header floating particles -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
          <div class="absolute top-2 left-8 w-1 h-1 bg-violet-400 rounded-full animate-pulse opacity-40"></div>
          <div class="absolute top-4 right-12 w-0.5 h-0.5 bg-cyan-400 rounded-full animate-bounce opacity-30"></div>
        </div>
      </header>

      <!-- Enhanced Page content -->
      <main class="p-6 relative">
        <!-- Content glow overlay -->
        <div class="absolute inset-0 bg-gradient-to-br from-violet-500/5 via-transparent to-cyan-500/5 pointer-events-none rounded-2xl"></div>
        
        <!-- Content wrapper with enhanced styling -->
        <div class="relative min-h-[calc(100vh-12rem)] bg-slate-900/30 backdrop-blur-sm rounded-2xl border border-slate-700/30 shadow-2xl shadow-slate-900/20 p-6 animate-fadeInUp">
          <slot />
        </div>
        
        <!-- Main content floating particles -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
          <div class="absolute top-4 left-8 w-1.5 h-1.5 bg-purple-400 rounded-full animate-float opacity-20"></div>
          <div class="absolute bottom-8 right-12 w-1 h-1 bg-violet-400 rounded-full animate-float opacity-30" style="animation-delay: 1s;"></div>
          <div class="absolute top-1/3 left-12 w-0.5 h-0.5 bg-cyan-400 rounded-full animate-float opacity-25" style="animation-delay: 2s;"></div>
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import route from 'ziggy-js'
import {
  ChartBarIcon,
  UsersIcon,
  ShieldCheckIcon,
  ChartPieIcon,
  CogIcon,
  ChevronDownIcon,
} from '@heroicons/vue/24/outline'

import SidebarLink from '@/Components/Admin/SidebarLink.vue'
import Dropdown from '@/Components/Dropdown.vue'
import DropdownLink from '@/Components/DropdownLink.vue'

// Particle system helper functions
const getParticleClass = (index) => {
  const colors = ['bg-violet-400', 'bg-cyan-400', 'bg-purple-400', 'bg-indigo-400']
  return colors[index % colors.length]
}

const getParticleStyle = (index) => {
  return {
    left: `${Math.random() * 100}%`,
    top: `${Math.random() * 100}%`,
    animationDelay: `${Math.random() * 3}s`,
    animationDuration: `${3 + Math.random() * 4}s`
  }
}
</script>

<style scoped>
@keyframes shimmer {
  0% {
    background-position: -200% center;
  }
  100% {
    background-position: 200% center;
  }
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes float {
  0%, 100% {
    transform: translateY(0px) rotate(0deg);
  }
  33% {
    transform: translateY(-10px) rotate(120deg);
  }
  66% {
    transform: translateY(5px) rotate(240deg);
  }
}

@keyframes colorShift1 {
  0%, 100% { stop-color: #8b5cf6; }
  33% { stop-color: #06b6d4; }
  66% { stop-color: #a855f7; }
}

@keyframes colorShift2 {
  0%, 100% { stop-color: #06b6d4; }
  33% { stop-color: #a855f7; }
  66% { stop-color: #8b5cf6; }
}

@keyframes colorShift3 {
  0%, 100% { stop-color: #a855f7; }
  33% { stop-color: #8b5cf6; }
  66% { stop-color: #06b6d4; }
}

.animate-shimmer {
  background-size: 200% auto;
  animation: shimmer 3s ease-in-out infinite;
}

.animate-fadeInUp {
  animation: fadeInUp 0.6s ease-out;
}

.animate-float {
  animation: float 4s ease-in-out infinite;
}

/* Custom scrollbar for sidebar */
nav::-webkit-scrollbar {
  width: 4px;
}

nav::-webkit-scrollbar-track {
  background: rgba(15, 23, 42, 0.1);
}

nav::-webkit-scrollbar-thumb {
  background: rgba(139, 92, 246, 0.3);
  border-radius: 2px;
}

nav::-webkit-scrollbar-thumb:hover {
  background: rgba(139, 92, 246, 0.5);
}
</style>