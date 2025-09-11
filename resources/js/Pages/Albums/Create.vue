<script setup>
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { CheckCircle } from 'lucide-vue-next'
import axios from 'axios'

const form = ref({
  title: '',
  description: '',
  privacy: 'public',
  cover_image_id: null
})

const createAlbum = async () => {
  try {
    await axios.post('/api/albums', form.value)
    router.visit('/albums')
  } catch (error) {
    console.error('Failed to create album:', error)
  }
}
</script>

<template>
  <AppLayout>
    <div class="min-h-screen bg-gray-50 dark:bg-slate-900">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl rounded-xl border border-gray-200 dark:border-slate-700 p-6">
          <form @submit.prevent="createAlbum" class="space-y-6">
            <!-- Title -->
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">
                Album Title *
              </label>
              <input
                v-model="form.title"
                type="text"
                required
                class="w-full px-4 py-3 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700"
              />
            </div>

            <!-- Description -->
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">
                Description
              </label>
              <textarea
                v-model="form.description"
                rows="3"
                class="w-full px-4 py-3 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700"
              ></textarea>
            </div>

            <!-- Submit -->
            <div class="flex justify-end">
              <button
                type="submit"
                class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700"
              >
                Create Album
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>