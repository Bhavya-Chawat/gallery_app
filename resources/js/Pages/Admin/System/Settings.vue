<template>
  <AdminLayout>
    <Head title="System Settings" />

    <template #header>
      <div>
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          System Settings
        </h2>
        <p class="text-sm text-gray-600 mt-1">
          Configure system preferences and limits
        </p>
      </div>
    </template>

    <div class="space-y-6">
      <!-- Gallery Settings -->
      <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Gallery Settings</h3>
        <form @submit.prevent="updateSettings">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Max Upload Size (MB)
              </label>
              <input
                v-model="settingsForm.max_upload_size"
                type="number"
                min="1"
                max="100"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
              />
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Allowed File Types
              </label>
              <input
                v-model="settingsForm.allowed_mimes"
                type="text"
                placeholder="jpg,png,webp,avif"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
              />
            </div>
          </div>

          <div class="mt-6 space-y-4">
            <label class="flex items-center">
              <input
                v-model="settingsForm.enable_registration"
                type="checkbox"
                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
              />
              <span class="ml-2 text-sm text-gray-700">
                Enable user registration
              </span>
            </label>

            <label class="flex items-center">
              <input
                v-model="settingsForm.enable_comments"
                type="checkbox"
                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
              />
              <span class="ml-2 text-sm text-gray-700">
                Enable comments
              </span>
            </label>

            <label class="flex items-center">
              <input
                v-model="settingsForm.comment_moderation"
                type="checkbox"
                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
              />
              <span class="ml-2 text-sm text-gray-700">
                Enable comment moderation
              </span>
            </label>
          </div>

          <div class="mt-6">
            <button
              type="submit"
              :disabled="settingsForm.processing"
              class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 disabled:opacity-50"
            >
              {{ settingsForm.processing ? 'Saving...' : 'Save Settings' }}
            </button>
          </div>
        </form>
      </div>

      <!-- Cache Management -->
      <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Cache Management</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
          <button
            @click="clearCache('all')"
            :disabled="loading.clearCache"
            class="p-4 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50"
          >
            <div class="text-sm font-medium">Clear All Cache</div>
            <div class="text-xs text-gray-500 mt-1">
              {{ loading.clearCache ? 'Clearing...' : 'Application cache' }}
            </div>
          </button>

          <button
            @click="clearCache('config')"
            class="p-4 border border-gray-300 rounded-lg hover:bg-gray-50"
          >
            <div class="text-sm font-medium">Config Cache</div>
            <div class="text-xs text-gray-500 mt-1">Configuration</div>
          </button>

          <button
            @click="clearCache('route')"
            class="p-4 border border-gray-300 rounded-lg hover:bg-gray-50"
          >
            <div class="text-sm font-medium">Route Cache</div>
            <div class="text-xs text-gray-500 mt-1">Application routes</div>
          </button>

          <button
            @click="clearCache('view')"
            class="p-4 border border-gray-300 rounded-lg hover:bg-gray-50"
          >
            <div class="text-sm font-medium">View Cache</div>
            <div class="text-xs text-gray-500 mt-1">Compiled views</div>
          </button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { reactive } from 'vue'
import { Head, router, useForm } from '@inertiajs/vue3'
import route from 'ziggy-js'

import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
})

const settingsForm = useForm({
  max_upload_size: props.settings.max_upload_size || 50,
  allowed_mimes: props.settings.allowed_mimes || 'jpg,jpeg,png,webp,avif',
  enable_registration: props.settings.enable_registration ?? true,
  enable_comments: props.settings.enable_comments ?? true,
  comment_moderation: props.settings.comment_moderation ?? true,
})

const loading = reactive({
  clearCache: false,
})

const updateSettings = () => {
  settingsForm.post(route('admin.system.update-settings'))
}

const clearCache = async (type) => {
  loading.clearCache = true
  try {
    await router.post(route('admin.system.clear-cache'), { type })
  } finally {
    loading.clearCache = false
  }
}
</script>
