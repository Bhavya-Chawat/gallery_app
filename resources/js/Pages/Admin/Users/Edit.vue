<template>
  <AdminLayout>
    <Head :title="`Edit User: ${user.name}`" />

    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit User
          </h2>
          <p class="text-sm text-gray-600 mt-1">
            Modify {{ user.name }}'s account details
          </p>
        </div>
        <Link
          :href="route('admin.users.show', user.id)"
          class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700"
        >
          ← Back to User
        </Link>
      </div>
    </template>

    <div class="max-w-2xl">
      <div class="bg-white rounded-lg shadow p-6">
        <form @submit.prevent="updateUser">
          <div class="space-y-6">
            <!-- Name & Email -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  Full Name *
                </label>
                <input
                  v-model="form.name"
                  type="text"
                  required
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                  :class="{ 'border-red-300': errors.name }"
                />
                <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  Email Address *
                </label>
                <input
                  v-model="form.email"
                  type="email"
                  required
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                  :class="{ 'border-red-300': errors.email }"
                />
                <p v-if="errors.email" class="mt-1 text-sm text-red-600">{{ errors.email }}</p>
              </div>
            </div>

            <!-- Password (Optional) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  New Password (leave blank to keep current)
                </label>
                <input
                  v-model="form.password"
                  type="password"
                  minlength="8"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                  :class="{ 'border-red-300': errors.password }"
                />
                <p v-if="errors.password" class="mt-1 text-sm text-red-600">{{ errors.password }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  Confirm New Password
                </label>
                <input
                  v-model="form.password_confirmation"
                  type="password"
                  minlength="8"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                />
              </div>
            </div>

            <!-- Storage & Settings -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  Storage Quota (GB) *
                </label>
                <input
                  v-model="form.storage_quota_gb"
                  type="number"
                  min="0"
                  step="0.1"
                  required
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                  :class="{ 'border-red-300': errors.storage_quota_gb }"
                />
                <p v-if="errors.storage_quota_gb" class="mt-1 text-sm text-red-600">{{ errors.storage_quota_gb }}</p>
              </div>
            </div>

            <!-- Status & Notifications -->
            <div class="space-y-4">
              <label class="flex items-center">
                <input
                  v-model="form.is_active"
                  type="checkbox"
                  class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                />
                <span class="ml-2 text-sm text-gray-700">
                  Active (user can log in)
                </span>
              </label>

              <label class="flex items-center">
                <input
                  v-model="form.email_notifications"
                  type="checkbox"
                  class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                />
                <span class="ml-2 text-sm text-gray-700">
                  Email notifications enabled
                </span>
              </label>
            </div>

            <!-- Actions -->
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
              <Link
                :href="route('admin.users.show', user.id)"
                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
              >
                Cancel
              </Link>
              <button
                type="submit"
                :disabled="processing"
                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 disabled:opacity-50"
              >
                {{ processing ? 'Updating...' : 'Update User' }}
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { reactive } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import route from 'ziggy-js'

import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  user: Object,
  roles: Array,
  errors: { type: Object, default: () => ({}) },
})

const form = reactive({
  name: props.user.name,
  email: props.user.email,
  password: '',
  password_confirmation: '',
  storage_quota_gb: props.user.storage_quota_gb,
  is_active: props.user.is_active,
  email_notifications: props.user.email_notifications,
})

const processing = reactive({ value: false })

const updateUser = () => {
  processing.value = true

  router.put(route('admin.users.update', props.user.id), form, {
    onFinish: () => {
      processing.value = false
    }
  })
}
</script>
