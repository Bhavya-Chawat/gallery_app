<template>
  <AppLayout>
    <Head title="Create Album" />

    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <nav class="flex" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-4">
              <li>
                <Link :href="route('albums.index')" class="text-gray-400 hover:text-gray-500">
                  Albums
                </Link>
              </li>
              <li class="flex">
                <ChevronRightIcon class="flex-shrink-0 h-5 w-5 text-gray-400" />
                <span class="ml-4 text-sm font-medium text-gray-900">Create</span>
              </li>
            </ol>
          </nav>
          <h2 class="mt-2 font-semibold text-xl text-gray-800 leading-tight">
            Create New Album
          </h2>
        </div>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <form @submit.prevent="submit" class="p-6 space-y-6">
            <!-- Title -->
            <div>
              <label for="title" class="block text-sm font-medium text-gray-700">Album Title *</label>
              <input
                id="title"
                v-model="form.title"
                type="text"
                required
                autofocus
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                placeholder="Enter album title"
              />
              <div v-if="form.errors.title" class="mt-2 text-sm text-red-600">{{ form.errors.title }}</div>
            </div>

            <!-- Description -->
            <div>
              <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
              <textarea
                id="description"
                v-model="form.description"
                rows="3"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                placeholder="Describe your album (optional)"
              ></textarea>
              <div v-if="form.errors.description" class="mt-2 text-sm text-red-600">{{ form.errors.description }}</div>
            </div>

            <!-- Privacy -->
            <div>
              <label for="privacy" class="block text-sm font-medium text-gray-700">Privacy *</label>
              <select
                id="privacy"
                v-model="form.privacy"
                required
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
              >
                <option value="public">Public - Anyone can view</option>
                <option value="unlisted">Unlisted - Only accessible via direct link</option>
                <option value="private">Private - Only you can view</option>
              </select>
              <div v-if="form.errors.privacy" class="mt-2 text-sm text-red-600">{{ form.errors.privacy }}</div>
              
              <div class="mt-2 text-sm text-gray-500">
                <div v-if="form.privacy === 'public'" class="flex items-center">
                  <GlobeAltIcon class="h-4 w-4 mr-1 text-green-500" />
                  This album will be visible to everyone and appear in search results.
                </div>
                <div v-else-if="form.privacy === 'unlisted'" class="flex items-center">
                  <LinkIcon class="h-4 w-4 mr-1 text-yellow-500" />
                  This album is accessible to anyone with the direct link.
                </div>
                <div v-else class="flex items-center">
                  <LockClosedIcon class="h-4 w-4 mr-1 text-red-500" />
                  This album is private and only visible to you.
                </div>
              </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between pt-6 border-t">
              <Link
                :href="route('albums.index')"
                class="text-sm text-gray-600 hover:text-gray-900"
              >
                Cancel
              </Link>
              
              <button
                type="submit"
                :disabled="form.processing"
                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                :class="{ 'opacity-25': form.processing }"
              >
                <span v-if="form.processing">Creating...</span>
                <span v-else>Create Album</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import route from 'ziggy-js'
import {
  ChevronRightIcon,
  GlobeAltIcon,
  LinkIcon,
  LockClosedIcon,
} from '@heroicons/vue/24/outline'

import AppLayout from '@/Layouts/AppLayout.vue'

const form = useForm({
  title: '',
  description: '',
  privacy: 'unlisted',
})

const submit = () => {
  form.post(route('albums.store'))
}
</script>
/*<template>
  <AppLayout>
    <Head title="Create Album" />

    <!-- Animated Background -->
    <div class="fixed inset-0 -z-10 overflow-hidden">
      <!-- Animated Mesh Pattern -->
      <div class="absolute inset-0 opacity-30">
        <svg class="absolute inset-0 h-full w-full animate-pulse" viewBox="0 0 400 400">
          <defs>
            <linearGradient id="meshGradient" x1="0%" y1="0%" x2="100%" y2="100%" gradientTransform="rotate(45)">
              <stop offset="0%" style="stop-color:#8b5cf6;stop-opacity:0.3">
                <animateTransform
                  attributeName="gradientTransform"
                  type="rotate"
                  values="0 50 50;360 50 50;0 50 50"
                  dur="8s"
                  repeatCount="indefinite"
                />
              </stop>
              <stop offset="50%" style="stop-color:#06b6d4;stop-opacity:0.3" />
              <stop offset="100%" style="stop-color:#a855f7;stop-opacity:0.3" />
            </linearGradient>
          </defs>
          <rect width="100%" height="100%" fill="url(#meshGradient)" />
          <g stroke="url(#meshGradient)" stroke-width="0.5" fill="none" opacity="0.4">
            <rect x="0" y="0" width="100" height="100" />
            <rect x="100" y="100" width="100" height="100" />
            <rect x="200" y="0" width="100" height="100" />
            <rect x="300" y="100" width="100" height="100" />
            <rect x="0" y="200" width="100" height="100" />
            <rect x="100" y="300" width="100" height="100" />
            <rect x="200" y="200" width="100" height="100" />
            <rect x="300" y="300" width="100" height="100" />
          </g>
        </svg>
      </div>

      <!-- Floating Particles -->
      <div class="absolute inset-0">
        <div 
          v-for="i in 20" 
          :key="i"
          class="absolute w-1 h-1 rounded-full bg-gradient-to-r from-violet-400 to-cyan-400 opacity-60"
          :style="{
            left: Math.random() * 100 + '%',
            top: Math.random() * 100 + '%',
            animationDelay: Math.random() * 8 + 's',
            animationDuration: (8 + Math.random() * 4) + 's'
          }"
          style="animation: float 12s ease-in-out infinite;"
        ></div>
      </div>

      <!-- Base gradient background -->
      <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-purple-900/50 to-slate-900"></div>
    </div>

    <template #header>
      <div class="flex items-center justify-between animate-fade-in-up" style="animation-delay: 0.1s;">
        <div>
          <nav class="flex mb-3" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-4">
              <li>
                <Link :href="route('albums.index')" class="text-slate-400 hover:text-violet-400 transition-colors duration-300">
                  Albums
                </Link>
              </li>
              <li class="flex">
                <ChevronRightIcon class="flex-shrink-0 h-5 w-5 text-slate-500" />
                <span class="ml-4 text-sm font-medium text-slate-300">Create</span>
              </li>
            </ol>
          </nav>
          <h2 class="font-bold text-2xl text-transparent bg-clip-text bg-gradient-to-r from-violet-400 via-purple-400 to-cyan-400 leading-tight animate-shimmer">
            Create New Album
          </h2>
        </div>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl shadow-2xl overflow-hidden animate-fade-in-up" style="animation-delay: 0.2s;">
          <form @submit.prevent="submit" class="p-8 space-y-8">
            <!-- Title -->
            <div class="group">
              <label for="title" class="block text-sm font-bold text-transparent bg-clip-text bg-gradient-to-r from-violet-400 to-purple-400 mb-3">
                Album Title *
              </label>
              <div class="relative">
                <input
                  id="title"
                  v-model="form.title"
                  type="text"
                  required
                  autofocus
                  class="block w-full px-4 py-4 bg-white/10 backdrop-blur-xl border border-white/20 rounded-xl text-slate-200 placeholder-slate-400 focus:border-violet-400 focus:ring-2 focus:ring-violet-500/20 transition-all duration-300 hover:bg-white/15"
                  placeholder="Enter album title"
                />
                <div class="absolute inset-0 bg-gradient-to-r from-violet-600/10 to-purple-600/10 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
              </div>
              <div v-if="form.errors.title" class="mt-3 text-sm text-red-400 bg-red-500/10 border border-red-500/20 rounded-lg px-3 py-2">
                {{ form.errors.title }}
              </div>
            </div>

            <!-- Description -->
            <div class="group">
              <label for="description" class="block text-sm font-bold text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-blue-400 mb-3">
                Description
              </label>
              <div class="relative">
                <textarea
                  id="description"
                  v-model="form.description"
                  rows="3"
                  class="block w-full px-4 py-4 bg-white/10 backdrop-blur-xl border border-white/20 rounded-xl text-slate-200 placeholder-slate-400 focus:border-cyan-400 focus:ring-2 focus:ring-cyan-500/20 transition-all duration-300 hover:bg-white/15 resize-none"
                  placeholder="Describe your album (optional)"
                ></textarea>
                <div class="absolute inset-0 bg-gradient-to-r from-cyan-600/10 to-blue-600/10 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
              </div>
              <div v-if="form.errors.description" class="mt-3 text-sm text-red-400 bg-red-500/10 border border-red-500/20 rounded-lg px-3 py-2">
                {{ form.errors.description }}
              </div>
            </div>

            <!-- Privacy -->
            <div class="group">
              <label for="privacy" class="block text-sm font-bold text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-pink-400 mb-3">
                Privacy *
              </label>
              <div class="relative">
                <select
                  id="privacy"
                  v-model="form.privacy"
                  required
                  class="block w-full px-4 py-4 bg-white/10 backdrop-blur-xl border border-white/20 rounded-xl text-slate-200 focus:border-purple-400 focus:ring-2 focus:ring-purple-500/20 transition-all duration-300 hover:bg-white/15"
                >
                  <option value="public">Public - Anyone can view</option>
                  <option value="unlisted">Unlisted - Only accessible via direct link</option>
                  <option value="private">Private - Only you can view</option>
                </select>
                <div class="absolute inset-0 bg-gradient-to-r from-purple-600/10 to-pink-600/10 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
              </div>
              <div v-if="form.errors.privacy" class="mt-3 text-sm text-red-400 bg-red-500/10 border border-red-500/20 rounded-lg px-3 py-2">
                {{ form.errors.privacy }}
              </div>
              
              <div class="mt-4 p-4 bg-white/5 backdrop-blur-xl rounded-xl border border-white/10">
                <div v-if="form.privacy === 'public'" class="flex items-center text-sm text-slate-300">
                  <div class="flex items-center justify-center w-8 h-8 bg-emerald-500/20 rounded-full mr-3">
                    <GlobeAltIcon class="h-4 w-4 text-emerald-400" />
                  </div>
                  <span>This album will be visible to everyone and appear in search results.</span>
                </div>
                <div v-else-if="form.privacy === 'unlisted'" class="flex items-center text-sm text-slate-300">
                  <div class="flex items-center justify-center w-8 h-8 bg-amber-500/20 rounded-full mr-3">
                    <LinkIcon class="h-4 w-4 text-amber-400" />
                  </div>
                  <span>This album is accessible to anyone with the direct link.</span>
                </div>
                <div v-else class="flex items-center text-sm text-slate-300">
                  <div class="flex items-center justify-center w-8 h-8 bg-red-500/20 rounded-full mr-3">
                    <LockClosedIcon class="h-4 w-4 text-red-400" />
                  </div>
                  <span>This album is private and only visible to you.</span>
                </div>
              </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between pt-8 border-t border-white/10">
              <Link
                :href="route('albums.index')"
                class="group text-sm text-slate-400 hover:text-slate-300 transition-all duration-300 font-medium"
              >
                <span class="relative">
                  Cancel
                  <span class="absolute inset-x-0 -bottom-1 h-px bg-gradient-to-r from-transparent via-slate-400 to-transparent transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300"></span>
                </span>
              </Link>
              
              <button
                type="submit"
                :disabled="form.processing"
                class="group relative inline-flex items-center px-6 py-3 bg-gradient-to-r from-violet-600 to-purple-600 hover:from-violet-500 hover:to-purple-500 border border-violet-500/20 rounded-xl font-bold text-sm text-white shadow-lg hover:shadow-violet-500/25 hover:shadow-xl transition-all duration-300 hover:scale-105 disabled:opacity-25 disabled:cursor-not-allowed disabled:hover:scale-100"
                :class="{ 'opacity-25': form.processing }"
              >
                <span v-if="form.processing" class="flex items-center">
                  <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  Creating...
                </span>
                <span v-else class="relative z-10">Create Album</span>
                <div class="absolute inset-0 bg-gradient-to-r from-violet-400 to-purple-400 rounded-xl blur opacity-0 group-hover:opacity-30 transition-opacity duration-300"></div>
              </button>
            </div>
          </form>

          <!-- Decorative Elements -->
          <div class="absolute top-4 right-4 w-20 h-20 bg-gradient-to-br from-violet-500/10 to-purple-500/10 rounded-full blur-xl"></div>
          <div class="absolute bottom-4 left-4 w-16 h-16 bg-gradient-to-br from-cyan-500/10 to-blue-500/10 rounded-full blur-xl"></div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import route from 'ziggy-js'
import {
  ChevronRightIcon,
  GlobeAltIcon,
  LinkIcon,
  LockClosedIcon,
} from '@heroicons/vue/24/outline'

import AppLayout from '@/Layouts/AppLayout.vue'

const form = useForm({
  title: '',
  description: '',
  privacy: 'unlisted',
})

const submit = () => {
  form.post(route('albums.store'))
}
</script>

<style scoped>
@keyframes float {
  0%, 100% { transform: translateY(0px) rotate(0deg); }
  33% { transform: translateY(-20px) rotate(120deg); }
  66% { transform: translateY(-10px) rotate(240deg); }
}

@keyframes shimmer {
  0% { background-position: -200% 0; }
  100% { background-position: 200% 0; }
}

@keyframes fade-in-up {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-shimmer {
  background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
  background-size: 200% 100%;
  animation: shimmer 3s ease-in-out infinite;
  -webkit-background-clip: text;
  background-clip: text;
}

.animate-fade-in-up {
  animation: fade-in-up 0.6s ease-out forwards;
  opacity: 0;
}

/* Custom select styling */
select {
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
  background-position: right 12px center;
  background-repeat: no-repeat;
  background-size: 16px 12px;
  padding-right: 40px;
}

select option {
  background-color: #1e293b;
  color: #e2e8f0;
}

/* Custom scrollbar for textarea */
textarea::-webkit-scrollbar {
  width: 6px;
}

textarea::-webkit-scrollbar-track {
  background: rgba(255, 255, 255, 0.1);
  border-radius: 3px;
}

textarea::-webkit-scrollbar-thumb {
  background: rgba(139, 92, 246, 0.5);
  border-radius: 3px;
}

textarea::-webkit-scrollbar-thumb:hover {
  background: rgba(139, 92, 246, 0.7);
}
</style>