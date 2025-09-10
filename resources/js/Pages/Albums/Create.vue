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
              <InputLabel for="title" value="Album Title" required />
              <TextInput
                id="title"
                v-model="form.title"
                type="text"
                class="mt-1 block w-full"
                placeholder="Enter album title"
                required
                autofocus
              />
              <InputError class="mt-2" :message="form.errors.title" />
            </div>

            <!-- Description -->
            <div>
              <InputLabel for="description" value="Description" />
              <textarea
                id="description"
                v-model="form.description"
                rows="3"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                placeholder="Describe your album (optional)"
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
                <option value="public">Public - Anyone can view</option>
                <option value="unlisted">Unlisted - Only accessible via direct link</option>
                <option value="private">Private - Only you can view</option>
              </select>
              <InputError class="mt-2" :message="form.errors.privacy" />
              
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
              
              <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                <span v-if="form.processing">Creating...</span>
                <span v-else>Create Album</span>
              </PrimaryButton>
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
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import TextInput from '@/Components/TextInput.vue'

const form = useForm({
  title: '',
  description: '',
  privacy: 'unlisted',
})

const submit = () => {
  form.post(route('albums.store'))
}
</script>
