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
