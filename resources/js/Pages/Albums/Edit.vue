
<template>
  <AppLayout>
    <Head :title="album ? `Edit ${album.title}` : 'Edit Album'" />

    <!-- Animated Background -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
      <!-- Mesh Background -->
      <svg class="absolute inset-0 w-full h-full opacity-20" viewBox="0 0 100 100" preserveAspectRatio="none">
        <defs>
          <linearGradient id="meshGradient" x1="0%" y1="0%" x2="100%" y2="100%" gradientTransform="rotate(45)">
            <stop offset="0%" class="animate-pulse" style="stop-color:#8B5CF6;stop-opacity:0.3">
              <animate attributeName="stop-color" values="#8B5CF6;#06B6D4;#A855F7;#8B5CF6" dur="8s" repeatCount="indefinite"/>
            </stop>
            <stop offset="100%" style="stop-color:#06B6D4;stop-opacity:0.2">
              <animate attributeName="stop-color" values="#06B6D4;#A855F7;#8B5CF6;#06B6D4" dur="8s" repeatCount="indefinite"/>
            </stop>
          </linearGradient>
        </defs>
        <rect width="100" height="100" fill="url(#meshGradient)" opacity="0.1">
          <animateTransform attributeName="transform" type="translate" values="0,0;10,5;0,0" dur="15s" repeatCount="indefinite"/>
        </rect>
      </svg>

      <!-- Floating Particles -->
      <div class="absolute inset-0">
        <div v-for="i in 20" :key="i" 
             :class="`absolute w-1 h-1 bg-gradient-to-r from-violet-500 to-cyan-500 rounded-full opacity-30 animate-pulse`"
             :style="{
               left: Math.random() * 100 + '%',
               top: Math.random() * 100 + '%',
               animationDelay: Math.random() * 5 + 's',
               animationDuration: (3 + Math.random() * 4) + 's'
             }">
          <div class="absolute inset-0 animate-ping bg-gradient-to-r from-purple-500 to-cyan-500 rounded-full"></div>
        </div>
      </div>
    </div>

    <template #header>
      <div class="relative z-10 backdrop-blur-xl bg-slate-900/80 border-b border-white/10">
        <div class="flex items-center justify-between px-6 py-4">
          <div class="animate-fade-in-up">
            <nav class="flex mb-3" aria-label="Breadcrumb">
              <ol class="flex items-center space-x-4">
                <li>
                  <Link :href="route('albums.index')" 
                        class="text-slate-300 hover:text-white transition-colors duration-300 hover:bg-white/10 px-3 py-1 rounded-lg">
                    Albums
                  </Link>
                </li>
                <li v-if="album" class="flex items-center">
                  <ChevronRightIcon class="flex-shrink-0 h-4 w-4 text-slate-500 mx-2" />
                  <Link :href="route('albums.show', album.slug)" 
                        class="text-slate-300 hover:text-white transition-colors duration-300 hover:bg-white/10 px-3 py-1 rounded-lg max-w-32 truncate">
                    {{ album.title }}
                  </Link>
                </li>
                <li class="flex items-center">
                  <ChevronRightIcon class="flex-shrink-0 h-4 w-4 text-slate-500 mx-2" />
                  <span class="text-sm font-medium bg-gradient-to-r from-violet-400 to-cyan-400 bg-clip-text text-transparent">Edit</span>
                </li>
              </ol>
            </nav>
            <h2 class="text-2xl font-bold bg-gradient-to-r from-violet-400 via-purple-400 to-cyan-400 bg-clip-text text-transparent">
              <span class="inline-block animate-shimmer bg-gradient-to-r from-violet-400 via-white via-purple-400 to-cyan-400 bg-[length:400%_100%] bg-clip-text text-transparent">
                Edit Album
              </span>
            </h2>
          </div>
        </div>
      </div>
    </template>

    <div v-if="album" class="relative z-10 py-8 px-4 sm:px-6 lg:px-8">
      <div class="max-w-6xl mx-auto space-y-8 animate-fade-in-up" style="animation-delay: 0.2s;">
        
        <!-- Basic Info Form -->
        <div class="backdrop-blur-xl bg-white/5 border border-white/10 shadow-2xl rounded-2xl overflow-hidden hover:shadow-violet-500/10 transition-all duration-500 hover:border-violet-500/30">
          <div class="bg-gradient-to-r from-violet-500/10 to-cyan-500/10 p-6 border-b border-white/10">
            <h3 class="text-lg font-semibold text-white flex items-center">
              <div class="w-2 h-2 bg-gradient-to-r from-violet-500 to-cyan-500 rounded-full mr-3 animate-pulse"></div>
              Album Information
            </h3>
          </div>
          
          <form @submit.prevent="submit" class="p-8 space-y-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
              
              <!-- Left Column - Form Fields -->
              <div class="space-y-6 animate-fade-in-up" style="animation-delay: 0.3s;">
                
                <!-- Title Input -->
                <div class="group">
                  <label for="title" class="block text-sm font-medium text-slate-300 mb-2 transition-colors group-focus-within:text-violet-400">
                    Title *
                  </label>
                  <div class="relative">
                    <input
                      id="title"
                      v-model="form.title"
                      type="text"
                      required
                      class="w-full px-4 py-3 bg-slate-800/50 border border-slate-600/50 rounded-xl text-white placeholder-slate-400 
                             focus:border-violet-500 focus:ring-2 focus:ring-violet-500/20 focus:bg-slate-800/80 
                             transition-all duration-300 backdrop-blur-sm hover:border-slate-500"
                      placeholder="Enter album title..."
                    />
                    <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-violet-500/0 to-cyan-500/0 group-focus-within:from-violet-500/10 group-focus-within:to-cyan-500/10 transition-all duration-300 pointer-events-none"></div>
                  </div>
                  <div v-if="form.errors.title" class="mt-2 text-sm text-red-400 animate-shake">{{ form.errors.title }}</div>
                </div>

                <!-- Description Textarea -->
                <div class="group">
                  <label for="description" class="block text-sm font-medium text-slate-300 mb-2 transition-colors group-focus-within:text-cyan-400">
                    Description
                  </label>
                  <div class="relative">
                    <textarea
                      id="description"
                      v-model="form.description"
                      rows="4"
                      class="w-full px-4 py-3 bg-slate-800/50 border border-slate-600/50 rounded-xl text-white placeholder-slate-400 
                             focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20 focus:bg-slate-800/80 
                             transition-all duration-300 backdrop-blur-sm hover:border-slate-500 resize-none"
                      placeholder="Describe your album..."
                    ></textarea>
                    <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-cyan-500/0 to-purple-500/0 group-focus-within:from-cyan-500/10 group-focus-within:to-purple-500/10 transition-all duration-300 pointer-events-none"></div>
                  </div>
                  <div v-if="form.errors.description" class="mt-2 text-sm text-red-400 animate-shake">{{ form.errors.description }}</div>
                </div>

                <!-- Privacy Select -->
                <div class="group">
                  <label for="privacy" class="block text-sm font-medium text-slate-300 mb-2 transition-colors group-focus-within:text-purple-400">
                    Privacy *
                  </label>
                  <div class="relative">
                    <select
                      id="privacy"
                      v-model="form.privacy"
                      class="w-full px-4 py-3 bg-slate-800/50 border border-slate-600/50 rounded-xl text-white
                             focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 focus:bg-slate-800/80 
                             transition-all duration-300 backdrop-blur-sm hover:border-slate-500 cursor-pointer"
                    >
                      <option value="public" class="bg-slate-800">🌍 Public</option>
                      <option value="unlisted" class="bg-slate-800">🔗 Unlisted</option>
                      <option value="private" class="bg-slate-800">🔒 Private</option>
                    </select>
                    <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-purple-500/0 to-violet-500/0 group-focus-within:from-purple-500/10 group-focus-within:to-violet-500/10 transition-all duration-300 pointer-events-none"></div>
                  </div>
                  <div v-if="form.errors.privacy" class="mt-2 text-sm text-red-400 animate-shake">{{ form.errors.privacy }}</div>
                </div>
              </div>

              <!-- Right Column - Current Cover -->
              <div class="animate-fade-in-up" style="animation-delay: 0.4s;">
                <label class="block text-sm font-medium text-slate-300 mb-3">Current Cover Image</label>
                <div class="relative group">
                  <div class="aspect-video bg-gradient-to-br from-slate-800/50 to-slate-900/50 rounded-2xl overflow-hidden border border-slate-600/30 backdrop-blur-sm hover:border-violet-500/50 transition-all duration-500">
                    <img
                      v-if="currentCoverImage"
                      :src="getImageUrl(currentCoverImage)"
                      :alt="album.title"
                      class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                    />
                    <div v-else class="flex flex-col items-center justify-center h-full text-slate-400 group-hover:text-violet-400 transition-colors duration-300">
                      <div class="relative">
                        <FolderIcon class="h-16 w-16 mb-3 animate-bounce" style="animation-delay: 0.5s;" />
                        <div class="absolute inset-0 animate-ping opacity-25">
                          <FolderIcon class="h-16 w-16" />
                        </div>
                      </div>
                      <span class="text-sm font-medium">No cover image selected</span>
                      <span class="text-xs text-slate-500 mt-1">Choose from images below</span>
                    </div>
                  </div>
                  <div class="absolute inset-0 rounded-2xl bg-gradient-to-r from-violet-500/0 to-cyan-500/0 group-hover:from-violet-500/5 group-hover:to-cyan-500/5 transition-all duration-300 pointer-events-none"></div>
                </div>
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-between pt-8 border-t border-slate-600/30 animate-fade-in-up" style="animation-delay: 0.5s;">
              <Link :href="route('albums.show', album.slug)" 
                    class="inline-flex items-center px-6 py-3 text-sm font-medium text-slate-300 bg-slate-700/50 hover:bg-slate-600/50 
                           border border-slate-600/50 hover:border-slate-500 rounded-xl transition-all duration-300 
                           backdrop-blur-sm hover:text-white group">
                <ChevronRightIcon class="w-4 h-4 mr-2 rotate-180 group-hover:-translate-x-1 transition-transform duration-300" />
                Cancel
              </Link>
              
              <button
                type="submit"
                :disabled="form.processing"
                class="relative inline-flex items-center px-8 py-3 bg-gradient-to-r from-violet-600 to-cyan-600 
                       hover:from-violet-500 hover:to-cyan-500 disabled:from-slate-600 disabled:to-slate-700
                       border border-transparent rounded-xl font-semibold text-sm text-white uppercase tracking-wider
                       transition-all duration-300 transform hover:scale-105 hover:shadow-2xl hover:shadow-violet-500/25
                       disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none disabled:shadow-none
                       overflow-hidden group"
              >
                <div v-if="!form.processing" class="flex items-center">
                  <CheckIcon class="w-4 h-4 mr-2 group-hover:rotate-12 transition-transform duration-300" />
                  Save Changes
                </div>
                <div v-else class="flex items-center">
                  <div class="w-4 h-4 mr-2 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                  Saving...
                </div>
                <div class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/20 to-white/0 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-700"></div>
              </button>
            </div>
          </form>
        </div>

        <!-- Cover Image Selection -->
        <div v-if="images && images.length > 0" 
             class="backdrop-blur-xl bg-white/5 border border-white/10 shadow-2xl rounded-2xl overflow-hidden hover:shadow-cyan-500/10 transition-all duration-500 hover:border-cyan-500/30 animate-fade-in-up" 
             style="animation-delay: 0.6s;">
          <div class="bg-gradient-to-r from-cyan-500/10 to-purple-500/10 p-6 border-b border-white/10">
            <h3 class="text-lg font-semibold text-white flex items-center">
              <div class="w-2 h-2 bg-gradient-to-r from-cyan-500 to-purple-500 rounded-full mr-3 animate-pulse"></div>
              Choose Cover Image
              <span class="ml-auto text-sm text-slate-400 font-normal">{{ images.length }} images available</span>
            </h3>
          </div>
          
          <div class="p-8">
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8 gap-4">
              <div
                v-for="(image, index) in images"
                :key="image.id"
                @click="selectCoverImage(image.id)"
                class="relative aspect-square bg-gradient-to-br from-slate-800/50 to-slate-900/50 rounded-xl overflow-hidden 
                       cursor-pointer border-2 transition-all duration-300 transform hover:scale-105 hover:shadow-2xl group
                       animate-fade-in-up backdrop-blur-sm"
                :style="{ animationDelay: (0.1 * index) + 's' }"
                :class="{
                  'border-violet-500 ring-4 ring-violet-500/30 shadow-2xl shadow-violet-500/20': form.cover_image_id === image.id,
                  'border-slate-600/50 hover:border-cyan-500/50 hover:shadow-cyan-500/10': form.cover_image_id !== image.id
                }"
              >
                <img
                  :src="getImageUrl(image)"
                  :alt="image.title"
                  class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                />
                <div v-if="form.cover_image_id === image.id" 
                     class="absolute inset-0 bg-gradient-to-br from-violet-500/30 to-cyan-500/30 flex items-center justify-center backdrop-blur-[1px]">
                  <div class="relative">
                    <CheckIcon class="h-8 w-8 text-white drop-shadow-lg animate-bounce" />
                    <div class="absolute inset-0 animate-ping">
                      <CheckIcon class="h-8 w-8 text-white opacity-50" />
                    </div>
                  </div>
                </div>
                <div class="absolute inset-0 bg-gradient-to-t from-black/20 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="absolute bottom-2 left-2 right-2 opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-y-2 group-hover:translate-y-0">
                  <div class="text-xs text-white font-medium truncate bg-black/50 px-2 py-1 rounded backdrop-blur-sm">
                    {{ image.title || 'Untitled' }}
                  </div>
                </div>
              </div>
            </div>
            
            <div class="mt-8 flex justify-between items-center pt-6 border-t border-slate-600/30 animate-fade-in-up" style="animation-delay: 0.8s;">
              <button
                @click="form.cover_image_id = null"
                class="inline-flex items-center px-4 py-2 text-sm text-slate-400 hover:text-red-400 
                       border border-slate-600/50 hover:border-red-500/50 rounded-lg transition-all duration-300 
                       backdrop-blur-sm hover:bg-red-500/10 group"
              >
                <svg class="w-4 h-4 mr-2 group-hover:rotate-12 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                Remove cover image
              </button>
              
              <Link
                :href="route('albums.add-images-form', album.id)"
                class="inline-flex items-center px-6 py-2 bg-gradient-to-r from-cyan-600/80 to-purple-600/80 
                       hover:from-cyan-500 hover:to-purple-500 text-sm font-medium text-white rounded-lg 
                       transition-all duration-300 transform hover:scale-105 hover:shadow-lg hover:shadow-cyan-500/25 group"
              >
                <svg class="w-4 h-4 mr-2 group-hover:rotate-12 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add more images
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-else class="relative z-10 py-12">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="backdrop-blur-xl bg-white/5 border border-white/10 shadow-2xl rounded-2xl overflow-hidden">
          <div class="p-12 text-center">
            <div class="relative mx-auto w-20 h-20 mb-6">
              <div class="absolute inset-0 border-4 border-violet-500/30 rounded-full"></div>
              <div class="absolute inset-0 border-4 border-transparent border-t-violet-500 rounded-full animate-spin"></div>
              <div class="absolute inset-2 border-4 border-transparent border-t-cyan-500 rounded-full animate-spin" style="animation-direction: reverse; animation-duration: 1.5s;"></div>
            </div>
            <p class="text-slate-300 text-lg font-medium animate-pulse">Loading album...</p>
            <p class="text-slate-500 text-sm mt-2">Please wait while we fetch your album data</p>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import route from 'ziggy-js'
import { ChevronRightIcon, FolderIcon, CheckIcon } from '@heroicons/vue/24/outline'

import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  album: {
    type: Object,
    required: true
  },
  images: {
    type: Array,
    default: () => []
  },
})

// Initialize form with safe defaults
const form = useForm({
  title: props.album?.title || '',
  description: props.album?.description || '',
  privacy: props.album?.privacy || 'public',
  cover_image_id: props.album?.cover_image_id || null,
})

const currentCoverImage = computed(() => {
  if (form.cover_image_id && props.images) {
    return props.images.find(img => img.id === form.cover_image_id)
  }
  return null
})

const getImageUrl = (image) => {
  if (image?.storage_path) {
    return `http://localhost:9000/gallery-images/${image.storage_path}`
  }
  return '/images/placeholder.jpg'
}

const selectCoverImage = (imageId) => {
  form.cover_image_id = form.cover_image_id === imageId ? null : imageId
}

const submit = () => {
  if (!props.album?.id) return
  
  form.patch(route('albums.update', props.album.id), {
    onSuccess: () => {
      // Redirect to album show page after successful update
      window.location.href = route('albums.show', props.album.slug)
    }
  })
}
</script>

<style scoped>
@keyframes shimmer {
  0% { background-position: -400% 0; }
  100% { background-position: 400% 0; }
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

@keyframes shake {
  0%, 100% { transform: translateX(0); }
  25% { transform: translateX(-5px); }
  75% { transform: translateX(5px); }
}

.animate-shimmer {
  animation: shimmer 3s ease-in-out infinite;
}

.animate-fade-in-up {
  animation: fade-in-up 0.8s ease-out forwards;
  opacity: 0;
}

.animate-shake {
  animation: shake 0.5s ease-in-out;
}
</style>