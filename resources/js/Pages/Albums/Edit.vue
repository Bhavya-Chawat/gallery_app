<template>
  <AppLayout>
    <Head :title="`Edit ${album.title}`" />

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
                <Link :href="route('albums.show', album.id)" class="text-gray-400 hover:text-gray-500">
                  {{ album.title }}
                </Link>
              </li>
              <li class="flex">
                <ChevronRightIcon class="flex-shrink-0 h-5 w-5 text-gray-400" />
                <span class="ml-4 text-sm font-medium text-gray-900">Edit</span>
              </li>
            </ol>
          </nav>
          <h2 class="mt-2 font-semibold text-xl text-gray-800 leading-tight">
            Edit Album
          </h2>
        </div>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <!-- Album Details -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <form @submit.prevent="submit" class="p-6 space-y-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
              <!-- Left Column -->
              <div class="space-y-6">
                <!-- Title -->
                <div>
                  <InputLabel for="title" value="Album Title" required />
                  <TextInput
                    id="title"
                    v-model="form.title"
                    type="text"
                    class="mt-1 block w-full"
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
                    placeholder="Describe your album"
                  ></textarea>
                  <InputError class="mt-2" :message="form.errors.description" />
                </div>

                <!-- Privacy -->
                <div>
                  <InputLabel for="privacy" value="Privacy" required />
                  <select
                    id="privacy"
                    v-model="form.privacy"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    required
                  >
                    <option value="public">Public</option>
                    <option value="unlisted">Unlisted</option>
                    <option value="private">Private</option>
                  </select>
                  <InputError class="mt-2" :message="form.errors.privacy" />
                </div>
              </div>

              <!-- Right Column -->
              <div class="space-y-6">
                <!-- Cover Image -->
                <div>
                  <InputLabel value="Cover Image" />
                  <div class="mt-1">
                    <div v-if="form.cover_image_id" class="mb-4">
                      <img
                        :src="getCoverImageUrl()"
                        alt="Cover image"
                        class="w-full h-32 object-cover rounded-lg"
                      >
                    </div>
                    <select
                      v-model="form.cover_image_id"
                      class="block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                      <option value="">No cover image</option>
                      <option
                        v-for="image in images"
                        :key="image.id"
                        :value="image.id"
                      >
                        {{ image.title || image.original_filename }}
                      </option>
                    </select>
                  </div>
                  <InputError class="mt-2" :message="form.errors.cover_image_id" />
                </div>

                <!-- Album Stats -->
                <div class="bg-gray-50 rounded-lg p-4">
                  <h4 class="text-sm font-medium text-gray-900 mb-3">Album Statistics</h4>
                  <dl class="space-y-2 text-sm">
                    <div class="flex justify-between">
                      <dt class="text-gray-500">Images</dt>
                      <dd class="text-gray-900">{{ album.image_count }}</dd>
                    </div>
                    <div class="flex justify-between">
                      <dt class="text-gray-500">Total Size</dt>
                      <dd class="text-gray-900">{{ album.formatted_size }}</dd>
                    </div>
                    <div class="flex justify-between">
                      <dt class="text-gray-500">Created</dt>
                      <dd class="text-gray-900">{{ formatDate(album.created_at) }}</dd>
                    </div>
                    <div class="flex justify-between">
                      <dt class="text-gray-500">Last Updated</dt>
                      <dd class="text-gray-900">{{ formatDate(album.updated_at) }}</dd>
                    </div>
                  </dl>
                </div>
              </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between pt-6 border-t">
              <div class="flex items-center space-x-4">
                <Link
                  :href="route('albums.show', album.id)"
                  class="text-sm text-gray-600 hover:text-gray-900"
                >
                  Cancel
                </Link>
                
                <button
                  type="button"
                  @click="deleteAlbum"
                  class="text-sm text-red-600 hover:text-red-800"
                >
                  Delete Album
                </button>
              </div>
              
              <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                <span v-if="form.processing">Saving...</span>
                <span v-else>Save Changes</span>
              </PrimaryButton>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Head, Link, useForm, router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import { ChevronRightIcon } from '@heroicons/vue/24/outline'

import AppLayout from '@/Layouts/AppLayout.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import TextInput from '@/Components/TextInput.vue'

const props = defineProps({
  album: Object,
  images: {
    type: Array,
    default: () => [],
  },
})

const form = useForm({
  title: props.album.title,
  description: props.album.description,
  privacy: props.album.privacy,
  cover_image_id: props.album.cover_image_id,
})

const submit = () => {
  form.patch(route('albums.update', props.album.id))
}

const deleteAlbum = () => {
  if (confirm('Are you sure you want to delete this album? Images will be preserved.')) {
    router.delete(route('albums.destroy', props.album.id))
  }
}

const getCoverImageUrl = () => {
  if (!form.cover_image_id) return ''
  
  const coverImage = props.images.find(img => img.id === form.cover_image_id)
  return coverImage?.versions?.find(v => v.variant === 'medium')?.url || coverImage?.url || ''
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  })
}
</script>
