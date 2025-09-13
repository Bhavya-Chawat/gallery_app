<template>
  <AppLayout>
    <Head title="Create Collection" />

    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <nav class="flex" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-4">
              <li>
                <Link :href="route('collections.index')" class="text-gray-400 hover:text-gray-500">
                  Collections
                </Link>
              </li>
              <li class="flex">
                <ChevronRightIcon class="flex-shrink-0 h-5 w-5 text-gray-400" />
                <span class="ml-4 text-sm font-medium text-gray-900">Create</span>
              </li>
            </ol>
          </nav>
          <h2 class="mt-2 font-semibold text-xl text-gray-800 leading-tight">
            Create Collection
          </h2>
        </div>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <form @submit.prevent="submit" class="space-y-6">
          <!-- Basic Information -->
          <div class="bg-white shadow-sm rounded-lg p-6 space-y-6">
            <h3 class="text-lg font-medium text-gray-900">Collection Details</h3>
            
            <!-- Title -->
            <div>
              <InputLabel for="title" value="Title" />
              <TextInput
                id="title"
                v-model="form.title"
                type="text"
                class="mt-1 block w-full"
                placeholder="Enter collection title"
                required
              />
              <InputError class="mt-2" :message="form.errors.title" />
            </div>

            <!-- Description -->
            <div>
              <InputLabel for="description" value="Description" />
              <textarea
                id="description"
                v-model="form.description"
                rows="4"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                placeholder="Describe what this collection is about"
              ></textarea>
              <p class="mt-1 text-sm text-gray-500">
                Optional. Help people understand what makes this collection special.
              </p>
              <InputError class="mt-2" :message="form.errors.description" />
            </div>

            <!-- Privacy -->
            <div>
              <InputLabel for="privacy" value="Privacy" />
              <select
                id="privacy"
                v-model="form.privacy"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
              >
                <option value="public">Public - Anyone can view this collection</option>
                <option value="unlisted">Unlisted - Only accessible via direct link</option>
                <option value="private">Private - Only you can view this collection</option>
              </select>
              <InputError class="mt-2" :message="form.errors.privacy" />
            </div>
          </div>

          <!-- Collection Type -->
          <div class="bg-white shadow-sm rounded-lg p-6 space-y-4">
            <h3 class="text-lg font-medium text-gray-900">Collection Type</h3>
            
            <div class="grid grid-cols-1 gap-4">
              <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none">
                <input
                  v-model="form.type"
                  type="radio"
                  value="manual"
                  class="sr-only"
                />
                <span class="flex flex-1">
                  <span class="flex flex-col">
                    <span class="flex items-center">
                      <FolderIcon class="h-5 w-5 text-gray-400 mr-2" />
                      <span class="block text-sm font-medium text-gray-900">Manual Collection</span>
                    </span>
                    <span class="mt-1 flex items-center text-sm text-gray-500">
                      Manually curate and organize images
                    </span>
                  </span>
                </span>
                <CheckCircleIcon 
                  v-show="form.type === 'manual'" 
                  class="h-5 w-5 text-blue-600" 
                />
              </label>

              <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none opacity-50">
                <input
                  type="radio"
                  value="smart"
                  class="sr-only"
                  disabled
                />
                <span class="flex flex-1">
                  <span class="flex flex-col">
                    <span class="flex items-center">
                      <SparklesIcon class="h-5 w-5 text-gray-400 mr-2" />
                      <span class="block text-sm font-medium text-gray-900">Smart Collection</span>
                      <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                        Coming Soon
                      </span>
                    </span>
                    <span class="mt-1 flex items-center text-sm text-gray-500">
                      Automatically populate based on tags, dates, or other criteria
                    </span>
                  </span>
                </span>
              </label>
            </div>
          </div>

          <!-- Initial Images (Optional) -->
          <div class="bg-white shadow-sm rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Initial Images (Optional)</h3>
            <p class="text-sm text-gray-600 mb-4">
              You can add images to your collection after creating it, or select some to start with.
            </p>

            <!-- Search for images -->
            <div class="mb-4">
              <div class="relative">
                <input
                  v-model="imageSearch"
                  @input="searchImages"
                  type="text"
                  placeholder="Search your images to add to collection..."
                  class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                />
                <MagnifyingGlassIcon class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" />
              </div>
            </div>

            <!-- Available Images Grid -->
            <div v-if="availableImages.length > 0" class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-2">Your Images:</label>
              <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-3 max-h-60 overflow-y-auto border rounded-md p-3">
                <div
                  v-for="image in availableImages.slice(0, 20)"
                  :key="image.id"
                  @click="toggleImageSelection(image)"
                  class="relative aspect-square bg-gray-100 rounded-lg overflow-hidden cursor-pointer border-2 transition-colors"
                  :class="{
                    'border-blue-500 ring-2 ring-blue-200': selectedImages.includes(image.id),
                    'border-transparent hover:border-gray-300': !selectedImages.includes(image.id)
                  }"
                >
                  <img
                    :src="getImageUrl(image)"
                    :alt="image.title"
                    class="w-full h-full object-cover"
                  />
                  <div v-if="selectedImages.includes(image.id)" class="absolute inset-0 bg-blue-500/20 flex items-center justify-center">
                    <CheckIcon class="h-6 w-6 text-blue-600" />
                  </div>
                </div>
              </div>
              <p v-if="availableImages.length > 20" class="text-xs text-gray-500 mt-2">
                Showing first 20 images. Use search to find specific images.
              </p>
            </div>

            <!-- Selected Images Count -->
            <div v-if="selectedImages.length > 0" class="text-sm text-gray-600">
              {{ selectedImages.length }} image{{ selectedImages.length !== 1 ? 's' : '' }} selected
            </div>
          </div>

          <!-- Actions -->
          <div class="bg-white shadow-sm rounded-lg p-6">
            <div class="flex items-center justify-between">
              <Link
                :href="route('collections.index')"
                class="text-sm text-gray-600 hover:text-gray-900"
              >
                Cancel
              </Link>
              
              <PrimaryButton 
                :class="{ 'opacity-25': form.processing }" 
                :disabled="form.processing"
                type="submit"
              >
                <span v-if="form.processing">Creating...</span>
                <span v-else>Create Collection</span>
              </PrimaryButton>
            </div>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import route from 'ziggy-js'
import { 
  ChevronRightIcon,
  FolderIcon,
  SparklesIcon,
  CheckCircleIcon,
  CheckIcon,
  MagnifyingGlassIcon
} from '@heroicons/vue/24/outline'

import AppLayout from '@/Layouts/AppLayout.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import TextInput from '@/Components/TextInput.vue'

// Reactive state
const imageSearch = ref('')
const availableImages = ref([])
const selectedImages = ref([])

// Form
const form = useForm({
  title: '',
  description: '',
  privacy: 'public',
  type: 'manual',
  initial_images: [],
})

// Methods
const getImageUrl = (image) => {
  if (image.storage_path) {
    return `http://localhost:9000/gallery-images/${image.storage_path}`
  }
  return image.url || '/images/placeholder.jpg'
}

const toggleImageSelection = (image) => {
  const index = selectedImages.value.indexOf(image.id)
  if (index > -1) {
    selectedImages.value.splice(index, 1)
  } else {
    selectedImages.value.push(image.id)
  }
}

const searchImages = async () => {
  if (imageSearch.value.length < 2) {
    loadInitialImages()
    return
  }

  try {
    const response = await fetch(`/api/search-images?q=${encodeURIComponent(imageSearch.value)}`)
    const data = await response.json()
    availableImages.value = data.images || []
  } catch (error) {
    console.error('Failed to search images:', error)
  }
}

const loadInitialImages = async () => {
  try {
    const response = await fetch('/api/my-images?limit=20')
    const data = await response.json()
    availableImages.value = data.images || []
  } catch (error) {
    console.error('Failed to load images:', error)
  }
}

const submit = () => {
  // Add selected images to form
  form.initial_images = selectedImages.value
  
  form.post(route('collections.store'), {
    onSuccess: () => {
      // Will redirect to the new collection
    },
  })
}

// Load initial images on mount
onMounted(() => {
  loadInitialImages()
})
</script>
/*<template>
  <AppLayout>
    <Head title="Create Collection" />

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
                  <Link :href="route('collections.index')" 
                        class="text-slate-300 hover:text-white transition-colors duration-300 hover:bg-white/10 px-3 py-1 rounded-lg">
                    Collections
                  </Link>
                </li>
                <li class="flex items-center">
                  <ChevronRightIcon class="flex-shrink-0 h-4 w-4 text-slate-500 mx-2" />
                  <span class="text-sm font-medium bg-gradient-to-r from-violet-400 to-cyan-400 bg-clip-text text-transparent">Create</span>
                </li>
              </ol>
            </nav>
            <h2 class="text-2xl font-bold bg-gradient-to-r from-violet-400 via-purple-400 to-cyan-400 bg-clip-text text-transparent">
              <span class="inline-block animate-shimmer bg-gradient-to-r from-violet-400 via-white via-purple-400 to-cyan-400 bg-[length:400%_100%] bg-clip-text text-transparent">
                Create Collection
              </span>
            </h2>
          </div>
        </div>
      </div>
    </template>

    <div class="relative z-10 py-8 px-4 sm:px-6 lg:px-8">
      <div class="max-w-3xl mx-auto">
        <form @submit.prevent="submit" class="space-y-8">
          
          <!-- Basic Information -->
          <div class="backdrop-blur-xl bg-white/5 border border-white/10 shadow-2xl rounded-2xl overflow-hidden hover:shadow-violet-500/10 transition-all duration-500 hover:border-violet-500/30 animate-fade-in-up">
            <div class="bg-gradient-to-r from-violet-500/10 to-cyan-500/10 p-6 border-b border-white/10">
              <h3 class="text-xl font-semibold text-white flex items-center">
                <div class="w-2 h-2 bg-gradient-to-r from-violet-500 to-cyan-500 rounded-full mr-3 animate-pulse"></div>
                Collection Details
              </h3>
            </div>
            
            <div class="p-8 space-y-8">
              <!-- Title -->
              <div class="group animate-fade-in-up" style="animation-delay: 0.1s;">
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
                    placeholder="Enter collection title..."
                  />
                  <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-violet-500/0 to-cyan-500/0 group-focus-within:from-violet-500/10 group-focus-within:to-cyan-500/10 transition-all duration-300 pointer-events-none"></div>
                </div>
                <div v-if="form.errors.title" class="mt-2 text-sm text-red-400 animate-shake">{{ form.errors.title }}</div>
              </div>

              <!-- Description -->
              <div class="group animate-fade-in-up" style="animation-delay: 0.2s;">
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
                    placeholder="Describe what this collection is about..."
                  ></textarea>
                  <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-cyan-500/0 to-purple-500/0 group-focus-within:from-cyan-500/10 group-focus-within:to-purple-500/10 transition-all duration-300 pointer-events-none"></div>
                </div>
                <p class="mt-2 text-sm text-slate-400">
                  Optional. Help people understand what makes this collection special.
                </p>
                <div v-if="form.errors.description" class="mt-2 text-sm text-red-400 animate-shake">{{ form.errors.description }}</div>
              </div>

              <!-- Privacy -->
              <div class="group animate-fade-in-up" style="animation-delay: 0.3s;">
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
                    <option value="public" class="bg-slate-800">🌍 Public - Anyone can view this collection</option>
                    <option value="unlisted" class="bg-slate-800">🔗 Unlisted - Only accessible via direct link</option>
                    <option value="private" class="bg-slate-800">🔒 Private - Only you can view this collection</option>
                  </select>
                  <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-purple-500/0 to-violet-500/0 group-focus-within:from-purple-500/10 group-focus-within:to-violet-500/10 transition-all duration-300 pointer-events-none"></div>
                </div>
                <div v-if="form.errors.privacy" class="mt-2 text-sm text-red-400 animate-shake">{{ form.errors.privacy }}</div>
              </div>
            </div>
          </div>

          <!-- Collection Type -->
          <div class="backdrop-blur-xl bg-white/5 border border-white/10 shadow-2xl rounded-2xl overflow-hidden hover:shadow-cyan-500/10 transition-all duration-500 hover:border-cyan-500/30 animate-fade-in-up" style="animation-delay: 0.1s;">
            <div class="bg-gradient-to-r from-cyan-500/10 to-purple-500/10 p-6 border-b border-white/10">
              <h3 class="text-xl font-semibold text-white flex items-center">
                <div class="w-2 h-2 bg-gradient-to-r from-cyan-500 to-purple-500 rounded-full mr-3 animate-pulse"></div>
                Collection Type
              </h3>
            </div>
            
            <div class="p-8 space-y-4">
              <!-- Manual Collection -->
              <label class="relative flex cursor-pointer rounded-xl backdrop-blur-sm bg-slate-800/30 border border-slate-600/50 p-6 hover:border-violet-500/50 hover:bg-slate-800/50 transition-all duration-300 group animate-fade-in-up" style="animation-delay: 0.2s;">
                <input
                  v-model="form.type"
                  type="radio"
                  value="manual"
                  class="sr-only"
                />
                <span class="flex flex-1">
                  <span class="flex flex-col">
                    <span class="flex items-center">
                      <FolderIcon class="h-6 w-6 text-violet-400 mr-3 group-hover:scale-110 transition-transform duration-300" />
                      <span class="block text-lg font-semibold text-white">Manual Collection</span>
                    </span>
                    <span class="mt-2 text-slate-300 group-hover:text-slate-200 transition-colors">
                      Manually curate and organize images with full control
                    </span>
                  </span>
                </span>
                <div class="flex items-center">
                  <CheckCircleIcon 
                    v-show="form.type === 'manual'" 
                    class="h-6 w-6 text-violet-400 animate-bounce" 
                  />
                  <div v-show="form.type !== 'manual'" class="w-6 h-6 border-2 border-slate-600 rounded-full group-hover:border-violet-400 transition-colors duration-300"></div>
                </div>
                <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-violet-500/0 to-cyan-500/0 group-hover:from-violet-500/5 group-hover:to-cyan-500/5 transition-all duration-300 pointer-events-none" :class="{ 'from-violet-500/10 to-cyan-500/10': form.type === 'manual' }"></div>
              </label>

              <!-- Smart Collection -->
              <label class="relative flex cursor-not-allowed rounded-xl backdrop-blur-sm bg-slate-800/20 border border-slate-700/50 p-6 opacity-60 animate-fade-in-up" style="animation-delay: 0.3s;">
                <input
                  type="radio"
                  value="smart"
                  class="sr-only"
                  disabled
                />
                <span class="flex flex-1">
                  <span class="flex flex-col">
                    <span class="flex items-center">
                      <SparklesIcon class="h-6 w-6 text-slate-500 mr-3" />
                      <span class="block text-lg font-semibold text-slate-400">Smart Collection</span>
                      <span class="ml-3 inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gradient-to-r from-amber-500/20 to-orange-500/20 text-amber-300 border border-amber-500/30">
                        <svg class="w-3 h-3 mr-1 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                        </svg>
                        Coming Soon
                      </span>
                    </span>
                    <span class="mt-2 text-slate-500">
                      Automatically populate based on tags, dates, or other criteria
                    </span>
                  </span>
                </span>
              </label>
            </div>
          </div>

          <!-- Initial Images -->
          <div class="backdrop-blur-xl bg-white/5 border border-white/10 shadow-2xl rounded-2xl overflow-hidden hover:shadow-purple-500/10 transition-all duration-500 hover:border-purple-500/30 animate-fade-in-up" style="animation-delay: 0.2s;">
            <div class="bg-gradient-to-r from-purple-500/10 to-violet-500/10 p-6 border-b border-white/10">
              <h3 class="text-xl font-semibold text-white flex items-center">
                <div class="w-2 h-2 bg-gradient-to-r from-purple-500 to-violet-500 rounded-full mr-3 animate-pulse"></div>
                Initial Images
                <span class="ml-3 text-sm text-slate-400 font-normal">(Optional)</span>
              </h3>
            </div>

            <div class="p-8 space-y-6">
              <p class="text-slate-300 animate-fade-in-up" style="animation-delay: 0.3s;">
                You can add images to your collection after creating it, or select some to start with.
              </p>

              <!-- Search Input -->
              <div class="relative group animate-fade-in-up" style="animation-delay: 0.4s;">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <MagnifyingGlassIcon class="h-5 w-5 text-slate-400 group-focus-within:text-purple-400 transition-colors duration-300" />
                </div>
                <input
                  v-model="imageSearch"
                  @input="searchImages"
                  type="text"
                  placeholder="Search your images to add to collection..."
                  class="w-full pl-10 pr-4 py-3 bg-slate-800/50 border border-slate-600/50 rounded-xl text-white placeholder-slate-400 
                         focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 focus:bg-slate-800/80 
                         transition-all duration-300 backdrop-blur-sm hover:border-slate-500"
                />
                <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-purple-500/0 to-cyan-500/0 group-focus-within:from-purple-500/10 group-focus-within:to-cyan-500/10 transition-all duration-300 pointer-events-none"></div>
              </div>

              <!-- Available Images Grid -->
              <div v-if="availableImages.length > 0" class="animate-fade-in-up" style="animation-delay: 0.5s;">
                <label class="block text-sm font-medium text-slate-300 mb-4">Your Images:</label>
                <div class="backdrop-blur-sm bg-slate-800/30 border border-slate-600/50 rounded-xl p-4 max-h-72 overflow-y-auto custom-scrollbar">
                  <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-3">
                    <div
                      v-for="(image, index) in availableImages.slice(0, 20)"
                      :key="image.id"
                      @click="toggleImageSelection(image)"
                      class="relative aspect-square bg-gradient-to-br from-slate-800/50 to-slate-900/50 rounded-xl overflow-hidden 
                             cursor-pointer border-2 transition-all duration-300 transform hover:scale-105 hover:shadow-lg 
                             backdrop-blur-sm group animate-fade-in-up"
                      :style="{ animationDelay: (0.02 * index) + 's' }"
                      :class="{
                        'border-violet-500 ring-4 ring-violet-500/30 shadow-2xl shadow-violet-500/20': selectedImages.includes(image.id),
                        'border-slate-600/50 hover:border-purple-500/50 hover:shadow-purple-500/10': !selectedImages.includes(image.id)
                      }"
                    >
                      <img
                        :src="getImageUrl(image)"
                        :alt="image.title"
                        class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                        loading="lazy"
                      />
                      <div v-if="selectedImages.includes(image.id)" 
                           class="absolute inset-0 bg-gradient-to-br from-violet-500/30 to-cyan-500/30 flex items-center justify-center backdrop-blur-[1px]">
                        <div class="relative">
                          <CheckIcon class="h-6 w-6 text-white drop-shadow-lg animate-bounce" />
                          <div class="absolute inset-0 animate-ping">
                            <CheckIcon class="h-6 w-6 text-white opacity-50" />
                          </div>
                        </div>
                      </div>
                      <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </div>
                  </div>
                </div>
                <p v-if="availableImages.length > 20" class="text-xs text-slate-500 mt-3 animate-fade-in-up" style="animation-delay: 0.6s;">
                  Showing first 20 images. Use search to find specific images.
                </p>
              </div>

              <!-- Selected Images Count -->
              <div v-if="selectedImages.length > 0" class="flex items-center justify-center p-4 rounded-xl bg-gradient-to-r from-violet-500/20 to-cyan-500/20 border border-violet-500/30 animate-fade-in-up" style="animation-delay: 0.7s;">
                <div class="flex items-center space-x-3">
                  <div class="w-3 h-3 bg-gradient-to-r from-violet-400 to-cyan-400 rounded-full animate-pulse"></div>
                  <span class="text-sm font-medium text-violet-200">
                    {{ selectedImages.length }} image{{ selectedImages.length !== 1 ? 's' : '' }} selected
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Actions -->
          <div class="backdrop-blur-xl bg-white/5 border border-white/10 shadow-2xl rounded-2xl overflow-hidden animate-fade-in-up" style="animation-delay: 0.3s;">
            <div class="p-8">
              <div class="flex items-center justify-between">
                <Link
                  :href="route('collections.index')"
                  class="inline-flex items-center px-6 py-3 text-sm font-medium text-slate-300 bg-slate-700/50 hover:bg-slate-600/50 
                         border border-slate-600/50 hover:border-slate-500 rounded-xl transition-all duration-300 
                         backdrop-blur-sm hover:text-white group"
                >
                  <ChevronRightIcon class="w-4 h-4 mr-2 rotate-180 group-hover:-translate-x-1 transition-transform duration-300" />
                  Cancel
                </Link>
                
                <button
                  :disabled="form.processing"
                  type="submit"
                  class="relative inline-flex items-center px-8 py-3 bg-gradient-to-r from-violet-600 to-cyan-600 
                         hover:from-violet-500 hover:to-cyan-500 disabled:from-slate-600 disabled:to-slate-700
                         border border-transparent rounded-xl font-semibold text-sm text-white uppercase tracking-wider
                         transition-all duration-300 transform hover:scale-105 hover:shadow-2xl hover:shadow-violet-500/25
                         disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none disabled:shadow-none
                         overflow-hidden group"
                >
                  <div v-if="!form.processing" class="flex items-center">
                    <svg class="w-4 h-4 mr-2 group-hover:rotate-12 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Create Collection
                  </div>
                  <div v-else class="flex items-center">
                    <div class="w-4 h-4 mr-2 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                    Creating...
                  </div>
                  <div class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/20 to-white/0 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-700"></div>
                </button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import route from 'ziggy-js'
import { 
  ChevronRightIcon,
  FolderIcon,
  SparklesIcon,
  CheckCircleIcon,
  CheckIcon,
  MagnifyingGlassIcon
} from '@heroicons/vue/24/outline'

import AppLayout from '@/Layouts/AppLayout.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import TextInput from '@/Components/TextInput.vue'

// Reactive state
const imageSearch = ref('')
const availableImages = ref([])
const selectedImages = ref([])

// Form
const form = useForm({
  title: '',
  description: '',
  privacy: 'public',
  type: 'manual',
  initial_images: [],
})

// Methods
const getImageUrl = (image) => {
  if (image.storage_path) {
    return `http://localhost:9000/gallery-images/${image.storage_path}`
  }
  return image.url || '/images/placeholder.jpg'
}

const toggleImageSelection = (image) => {
  const index = selectedImages.value.indexOf(image.id)
  if (index > -1) {
    selectedImages.value.splice(index, 1)
  } else {
    selectedImages.value.push(image.id)
  }
}

const searchImages = async () => {
  if (imageSearch.value.length < 2) {
    loadInitialImages()
    return
  }

  try {
    const response = await fetch(`/api/search-images?q=${encodeURIComponent(imageSearch.value)}`)
    const data = await response.json()
    availableImages.value = data.images || []
  } catch (error) {
    console.error('Failed to search images:', error)
  }
}

const loadInitialImages = async () => {
  try {
    const response = await fetch('/api/my-images?limit=20')
    const data = await response.json()
    availableImages.value = data.images || []
  } catch (error) {
    console.error('Failed to load images:', error)
  }
}

const submit = () => {
  // Add selected images to form
  form.initial_images = selectedImages.value
  
  form.post(route('collections.store'), {
    onSuccess: () => {
      // Will redirect to the new collection
    },
  })
}

// Load initial images on mount
onMounted(() => {
  loadInitialImages()
})
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

/* Custom Scrollbar */
.custom-scrollbar::-webkit-scrollbar {
  width: 8px;
}

.custom-scrollbar::-webkit-scrollbar-track {
  background: rgba(71, 85, 105, 0.2);
  border-radius: 4px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background: linear-gradient(to bottom, #8B5CF6, #06B6D4);
  border-radius: 4px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: linear-gradient(to bottom, #7C3AED, #0891B2);
}
</style>
