<template>
  <AppLayout>
    <Head :title="`Edit ${album.title}`" />

    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <nav class="flex" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-4">
              <li>
                <Link :href="route('albums.index')" class="text-gray-400 hover:text-gray-500">Albums</Link>
              </li>
              <li class="flex">
                <ChevronRightIcon class="flex-shrink-0 h-5 w-5 text-gray-400" />
                <Link :href="route('albums.show', album.slug)" class="text-gray-400 hover:text-gray-500">
                  {{ album.title }}
                </Link>
              </li>
              <li class="flex">
                <ChevronRightIcon class="flex-shrink-0 h-5 w-5 text-gray-400" />
                <span class="ml-4 text-sm font-medium text-gray-900">Edit</span>
              </li>
            </ol>
          </nav>
          <h2 class="mt-2 font-semibold text-xl text-gray-800 leading-tight">Edit Album</h2>
        </div>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <!-- Basic Info Form -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <form @submit.prevent="submit" class="p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Left Column - Form -->
              <div class="space-y-6">
                <div>
                  <label for="title" class="block text-sm font-medium text-gray-700">Title *</label>
                  <input
                    id="title"
                    v-model="form.title"
                    type="text"
                    required
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                  />
                  <div v-if="form.errors.title" class="mt-2 text-sm text-red-600">{{ form.errors.title }}</div>
                </div>

                <div>
                  <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                  <textarea
                    id="description"
                    v-model="form.description"
                    rows="3"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                  ></textarea>
                  <div v-if="form.errors.description" class="mt-2 text-sm text-red-600">{{ form.errors.description }}</div>
                </div>

                <div>
                  <label for="privacy" class="block text-sm font-medium text-gray-700">Privacy *</label>
                  <select
                    id="privacy"
                    v-model="form.privacy"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                  >
                    <option value="public">Public</option>
                    <option value="unlisted">Unlisted</option>
                    <option value="private">Private</option>
                  </select>
                  <div v-if="form.errors.privacy" class="mt-2 text-sm text-red-600">{{ form.errors.privacy }}</div>
                </div>
              </div>

              <!-- Right Column - Current Cover -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Current Cover Image</label>
                <div class="aspect-video bg-gray-100 rounded-lg overflow-hidden">
                  <img
                    v-if="currentCoverImage"
                    :src="getImageUrl(currentCoverImage)"
                    :alt="album.title"
                    class="w-full h-full object-cover"
                  />
                  <div v-else class="flex items-center justify-center h-full">
                    <FolderIcon class="h-12 w-12 text-gray-400" />
                    <span class="ml-2 text-gray-500">No cover image</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between pt-6 border-t">
              <Link :href="route('albums.show', album.slug)" class="text-sm text-gray-600 hover:text-gray-900">
                Cancel
              </Link>
              
              <button
                type="submit"
                :disabled="form.processing"
                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 disabled:opacity-25"
              >
                {{ form.processing ? 'Saving...' : 'Save Changes' }}
              </button>
            </div>
          </form>
        </div>

        <!-- Cover Image Selection -->
        <div v-if="images.length > 0" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Choose Cover Image</h3>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
              <div
                v-for="image in images"
                :key="image.id"
                @click="selectCoverImage(image.id)"
                class="aspect-square bg-gray-100 rounded-lg overflow-hidden cursor-pointer border-2 transition-colors"
                :class="{
                  'border-blue-500 ring-2 ring-blue-200': form.cover_image_id === image.id,
                  'border-transparent hover:border-gray-300': form.cover_image_id !== image.id
                }"
              >
                <img
                  :src="getImageUrl(image)"
                  :alt="image.title"
                  class="w-full h-full object-cover"
                />
                <div v-if="form.cover_image_id === image.id" class="absolute inset-0 bg-blue-500/20 flex items-center justify-center">
                  <CheckIcon class="h-8 w-8 text-blue-600" />
                </div>
              </div>
            </div>
            
            <div class="mt-4 flex justify-between items-center">
              <button
                @click="form.cover_image_id = null"
                class="text-sm text-gray-600 hover:text-gray-900"
              >
                Remove cover image
              </button>
              
              <Link
                :href="route('albums.add-images-form', album.id)"
                class="text-sm text-blue-600 hover:text-blue-500"
              >
                Add more images
              </Link>
            </div>
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
  album: Object,
  images: Array,
})

const form = useForm({
  title: props.album.title,
  description: props.album.description,
  privacy: props.album.privacy,
  cover_image_id: props.album.cover_image_id,
})

const currentCoverImage = computed(() => {
  if (form.cover_image_id) {
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
  form.patch(route('albums.update', props.album.id))
}
</script>
