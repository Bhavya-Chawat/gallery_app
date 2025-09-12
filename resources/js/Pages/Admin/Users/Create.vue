<template>
  <AdminLayout>
    <Head title="Create User" />

    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Create New User
          </h2>
          <p class="text-sm text-gray-600 mt-1">
            Add a new user to the system
          </p>
        </div>
        <Link
          :href="route('admin.users.index')"
          class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700"
        >
          ← Back to Users
        </Link>
      </div>
    </template>

    <div class="max-w-2xl">
      <div class="bg-white rounded-lg shadow p-6">
        <form @submit.prevent="createUser">
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

            <!-- Password -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  Password *
                </label>
                <input
                  v-model="form.password"
                  type="password"
                  required
                  minlength="8"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                  :class="{ 'border-red-300': errors.password }"
                />
                <p v-if="errors.password" class="mt-1 text-sm text-red-600">{{ errors.password }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  Confirm Password *
                </label>
                <input
                  v-model="form.password_confirmation"
                  type="password"
                  required
                  minlength="8"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                />
              </div>
            </div>

            <!-- Role & Storage -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  Role *
                </label>
                <select
                  v-model="form.role"
                  required
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                  :class="{ 'border-red-300': errors.role }"
                >
                  <option value="">Select a role...</option>
                  <option v-for="role in roles" :key="role.id" :value="role.slug">
                    {{ role.name }}
                  </option>
                </select>
                <p v-if="errors.role" class="mt-1 text-sm text-red-600">{{ errors.role }}</p>
              </div>

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

            <!-- Status -->
            <div>
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
            </div>

            <!-- Actions -->
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
              <Link
                :href="route('admin.users.index')"
                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
              >
                Cancel
              </Link>
              <button
                type="submit"
                :disabled="processing"
                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 disabled:opacity-50"
              >
                {{ processing ? 'Creating...' : 'Create User' }}
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
  roles: Array,
  errors: { type: Object, default: () => ({}) },
})

const form = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  role: '',
  storage_quota_gb: 5,
  is_active: true,
})

const processing = reactive({ value: false })

const createUser = () => {
  processing.value = true

  router.post(route('admin.users.store'), form, {
    onFinish: () => {
      processing.value = false
    }
  })
}
</script>
