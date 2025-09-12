<template>
  <AppLayout>
    <Head :title="`Edit ${image.title || 'Image'}`" />

    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <nav class="flex" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-4">
              <li>
                <Link :href="route('gallery.index')" class="text-gray-400 hover:text-gray-500">
                  Gallery
                </Link>
              </li>
              <li class="flex">
                <ChevronRightIcon class="flex-shrink-0 h-5 w-5 text-gray-400" />
                <Link :href="route('images.show', image.slug)" class="text-gray-400 hover:text-gray-500">
                  {{ image.title || 'Untitled' }}
                </Link>
              </li>
              <li class="flex">
                <ChevronRightIcon class="flex-shrink-0 h-5 w-5 text-gray-400" />
                <span class="ml-4 text-sm font-medium text-gray-900">Edit</span>
              </li>
            </ol>
          </nav>
          <h2 class="mt-2 font-semibold text-xl text-gray-800 leading-tight">
            Edit Image
          </h2>
        </div>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <form @submit.prevent="submit" class="space-y-6">
          <!-- Image Preview -->
          <div class="bg-white shadow-sm rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Preview</h3>
            <div class="max-w-md">
              <img
                :src="getImageUrl('medium')"
                :alt="form.alt_text || form.title"
                class="w-full rounded-lg shadow-sm"
              >
            </div>
          </div>

          <!-- Basic Information -->
          <div class="bg-white shadow-sm rounded-lg p-6 space-y-6">
            <h3 class="text-lg font-medium text-gray-900">Basic Information</h3>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
              <!-- Title -->
              <div>
                <InputLabel for="title" value="Title" />
                <TextInput
                  id="title"
                  v-model="form.title"
                  type="text"
                  class="mt-1 block w-full"
                  placeholder="Enter image title"
                />
                <InputError class="mt-2" :message="form.errors.title" />
              </div>

              <!-- Album -->
              <div>
                <InputLabel for="album_id" value="Album" />
                <select
                  id="album_id"
                  v-model="form.album_id"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >
                  <option value="">No album</option>
                  <option
                    v-for="album in albums"
                    :key="album.id"
                    :value="album.id"
                  >
                    {{ album.title }}
                  </option>
                </select>
                <InputError class="mt-2" :message="form.errors.album_id" />
              </div>
            </div>

            <!-- Caption -->
            <div>
              <InputLabel for="caption" value="Caption" />
              <textarea
                id="caption"
                v-model="form.caption"
                rows="3"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                placeholder="Add a caption to describe your image"
              ></textarea>
              <InputError class="mt-2" :message="form.errors.caption" />
            </div>

            <!-- Alt Text -->
            <div>
              <InputLabel for="alt_text" value="Alt Text" />
              <TextInput
                id="alt_text"
                v-model="form.alt_text"
                type="text"
                class="mt-1 block w-full"
                placeholder="Describe the image for accessibility"
              />
              <p class="mt-1 text-sm text-gray-500">
                Alternative text for screen readers and when the image fails to load.
              </p>
              <InputError class="mt-2" :message="form.errors.alt_text" />
            </div>
          </div>

          <!-- Tags -->
          <div class="bg-white shadow-sm rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Tags</h3>
            
            <!-- Tag Input -->
            <div class="space-y-4">
              <div>
                <div class="flex items-center space-x-2">
                  <input
                    v-model="newTag"
                    @keydown.enter.prevent="addTag"
                    @keydown.comma.prevent="addTag"
                    type="text"
                    placeholder="Type a tag and press Enter or comma"
                    class="flex-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                  >
                  <button
                    type="button"
                    @click="addTag"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                  >
                    Add Tag
                  </button>
                </div>
                <p class="mt-1 text-sm text-gray-500">
                  Press Enter or comma to add tags. Use lowercase, no spaces.
                </p>
              </div>

              <!-- Current Tags -->
              <div v-if="form.tags.length > 0">
                <label class="block text-sm font-medium text-gray-700 mb-2">Current Tags:</label>
                <div class="flex flex-wrap gap-2">
                  <span
                    v-for="(tag, index) in form.tags"
                    :key="index"
                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800"
                  >
                    {{ tag }}
                    <button
                      type="button"
                      @click="removeTag(index)"
                      class="ml-2 text-blue-600 hover:text-blue-800 focus:outline-none"
                    >
                      ×
                    </button>
                  </span>
                </div>
              </div>

              <!-- Popular Tags -->
              <div v-if="availableTags.length > 0">
                <label class="block text-sm font-medium text-gray-700 mb-2">Popular Tags:</label>
                <div class="flex flex-wrap gap-2">
                  <button
                    v-for="tag in availableTags.slice(0, 10)"
                    :key="tag.name"
                    type="button"
                    @click="addExistingTag(tag.name)"
                    :disabled="form.tags.includes(tag.name)"
                    class="px-3 py-1 text-sm border border-gray-300 rounded-full hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                    :class="{ 'bg-blue-50 border-blue-300': form.tags.includes(tag.name) }"
                  >
                    {{ tag.name }} ({{ tag.count }})
                  </button>
                </div>
              </div>
            </div>
            
            <InputError class="mt-2" :message="form.errors.tags" />
          </div>

          <!-- Privacy and License -->
          <div class="bg-white shadow-sm rounded-lg p-6 space-y-6">
            <h3 class="text-lg font-medium text-gray-900">Privacy & License</h3>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
              <!-- Privacy -->
              <div>
                <InputLabel for="privacy" value="Privacy" />
                <select
                  id="privacy"
                  v-model="form.privacy"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >
                  <option value="public">Public - Anyone can view</option>
                  <option value="unlisted">Unlisted - Only accessible via direct link</option>
                  <option value="private">Private - Only you can view</option>
                </select>
                <InputError class="mt-2" :message="form.errors.privacy" />
              </div>

              <!-- License -->
              <div>
                <InputLabel for="license" value="License" />
                <TextInput
                  id="license"
                  v-model="form.license"
                  type="text"
                  class="mt-1 block w-full"
                  placeholder="e.g., CC BY 4.0, All Rights Reserved"
                />
                <InputError class="mt-2" :message="form.errors.license" />
              </div>
            </div>
          </div>

          <!-- Settings -->
          <div class="bg-white shadow-sm rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Settings</h3>
            
            <div class="space-y-4">
              <label class="flex items-start">
                <input
                  v-model="form.allow_comments"
                  type="checkbox"
                  class="mt-1 rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200"
                >
                <span class="ml-3">
                  <span class="text-sm font-medium text-gray-700">Allow comments</span>
                  <p class="text-sm text-gray-500">Let people comment on this image.</p>
                </span>
              </label>

              <label class="flex items-start">
                <input
                  v-model="form.allow_downloads"
                  type="checkbox"
                  class="mt-1 rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200"
                >
                <span class="ml-3">
                  <span class="text-sm font-medium text-gray-700">Allow downloads</span>
                  <p class="text-sm text-gray-500">Let people download this image (respecting license terms).</p>
                </span>
              </label>
            </div>
          </div>

          <!-- Actions -->
          <div class="bg-white shadow-sm rounded-lg p-6">
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-4">
                <Link
                  :href="route('images.show', image.slug)"
                  class="text-sm text-gray-600 hover:text-gray-900"
                >
                  Cancel
                </Link>
                
                <button
                  type="button"
                  @click="deleteImage"
                  class="text-sm text-red-600 hover:text-red-800"
                >
                  Delete Image
                </button>
              </div>
              
              <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                <span v-if="form.processing">Saving...</span>
                <span v-else>Save Changes</span>
              </PrimaryButton>
            </div>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Head, Link, useForm, router } from "@inertiajs/vue3"
import route from 'ziggy-js'
import { ChevronRightIcon } from '@heroicons/vue/24/outline'

import AppLayout from '@/Layouts/AppLayout.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import TextInput from '@/Components/TextInput.vue'

const props = defineProps({
  image: Object,
  albums: {
    type: Array,
    default: () => [],
  },
  availableTags: {
    type: Array,
    default: () => [],
  },
})

const newTag = ref('')

const form = useForm({
  title: props.image.title,
  caption: props.image.caption,
  alt_text: props.image.alt_text,
  privacy: props.image.privacy,
  license: props.image.license,
  album_id: props.image.album_id,
  tags: props.image.tags?.map(tag => tag.name) || [],
  allow_comments: props.image.allow_comments ?? true,
  allow_downloads: props.image.allow_downloads ?? true,
})

const getImageUrl = (variant = 'medium') => {
  if (props.image.storage_path) {
    return `http://localhost:9000/gallery-images/${props.image.storage_path}`
  }
  return props.image.url || '/images/placeholder.jpg'
}

const addTag = () => {
  const tag = newTag.value.trim().toLowerCase().replace(/\s+/g, '-')
  
  if (tag && !form.tags.includes(tag)) {
    form.tags.push(tag)
    newTag.value = ''
  }
}

const addExistingTag = (tagName) => {
  if (!form.tags.includes(tagName)) {
    form.tags.push(tagName)
  }
}

const removeTag = (index) => {
  form.tags.splice(index, 1)
}

const submit = () => {
  form.patch(route('images.update', props.image.slug))
}

const deleteImage = () => {
  if (confirm('Are you sure you want to delete this image? This action cannot be undone.')) {
    router.delete(route('images.destroy', props.image.slug))
  }
}
</script>
