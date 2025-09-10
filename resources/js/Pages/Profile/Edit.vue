<template>
  <AppLayout>
    <Head title="Profile" />

    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Profile Settings
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <!-- Profile Information -->
        <div class="bg-white shadow-sm rounded-lg">
          <div class="p-6">
            <div class="flex items-center space-x-6 mb-6">
              <UserAvatar :user="user" size="xl" />
              <div>
                <h3 class="text-lg font-medium text-gray-900">{{ user.name }}</h3>
                <p class="text-sm text-gray-500">{{ user.email }}</p>
                <p class="text-xs text-gray-400 mt-1">
                  Member since {{ formatDate(user.created_at) }}
                </p>
              </div>
            </div>

            <UpdateProfileInformationForm
              :must-verify-email="mustVerifyEmail"
              :status="status"
            />
          </div>
        </div>

        <!-- Storage Usage -->
        <div class="bg-white shadow-sm rounded-lg">
          <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Storage Usage</h3>
            <StorageUsageCard
              :used="user.storage_used_bytes"
              :quota="user.storage_quota_bytes"
              :percentage="user.storage_usage_percentage"
              :show-details="true"
            />
          </div>
        </div>

        <!-- Account Statistics -->
        <div class="bg-white shadow-sm rounded-lg">
          <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Your Statistics</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
              <div class="text-center">
                <div class="text-2xl font-semibold text-gray-900">{{ stats.images || 0 }}</div>
                <div class="text-sm text-gray-500">Images</div>
              </div>
              <div class="text-center">
                <div class="text-2xl font-semibold text-gray-900">{{ stats.albums || 0 }}</div>
                <div class="text-sm text-gray-500">Albums</div>
              </div>
              <div class="text-center">
                <div class="text-2xl font-semibold text-gray-900">{{ formatNumber(stats.views || 0) }}</div>
                <div class="text-sm text-gray-500">Total Views</div>
              </div>
              <div class="text-center">
                <div class="text-2xl font-semibold text-gray-900">{{ formatNumber(stats.likes || 0) }}</div>
                <div class="text-sm text-gray-500">Total Likes</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Password Update -->
        <div class="bg-white shadow-sm rounded-lg">
          <div class="p-6">
            <UpdatePasswordForm />
          </div>
        </div>

        <!-- Preferences -->
        <div class="bg-white shadow-sm rounded-lg">
          <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Preferences</h3>
            <form @submit.prevent="updatePreferences" class="space-y-4">
              <div>
                <label class="flex items-center">
                  <input
                    v-model="preferencesForm.email_notifications"
                    type="checkbox"
                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200"
                  >
                  <span class="ml-2 text-sm text-gray-700">Email notifications</span>
                </label>
                <p class="mt-1 text-xs text-gray-500">
                  Receive email notifications for comments, likes, and system updates.
                </p>
              </div>

              <div>
                <InputLabel for="timezone" value="Timezone" />
                <select
                  id="timezone"
                  v-model="preferencesForm.timezone"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >
                  <option value="UTC">UTC</option>
                  <option value="America/New_York">Eastern Time</option>
                  <option value="America/Chicago">Central Time</option>
                  <option value="America/Denver">Mountain Time</option>
                  <option value="America/Los_Angeles">Pacific Time</option>
                  <option value="Europe/London">London</option>
                  <option value="Europe/Paris">Paris</option>
                  <option value="Asia/Tokyo">Tokyo</option>
                </select>
              </div>

              <div class="pt-4">
                <button
                  type="submit"
                  :disabled="preferencesForm.processing"
                  class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 disabled:opacity-25"
                >
                  Save Preferences
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Danger Zone -->
        <div class="bg-white shadow-sm rounded-lg">
          <div class="p-6">
            <DeleteUserForm />
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import { Head } from '@inertiajs/vue3'

import AppLayout from '@/Layouts/AppLayout.vue'
import UserAvatar from '@/Components/UserAvatar.vue'
import StorageUsageCard from '@/Components/StorageUsageCard.vue'
import InputLabel from '@/Components/InputLabel.vue'
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue'
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue'
import DeleteUserForm from './Partials/DeleteUserForm.vue'

const props = defineProps({
  user: Object,
  mustVerifyEmail: Boolean,
  status: String,
  stats: {
    type: Object,
    default: () => ({}),
  },
})

const preferencesForm = useForm({
  email_notifications: props.user.email_notifications,
  timezone: props.user.timezone,
})

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  })
}

const formatNumber = (number) => {
  if (number >= 1000000) {
    return (number / 1000000).toFixed(1) + 'M'
  } else if (number >= 1000) {
    return (number / 1000).toFixed(1) + 'K'
  }
  return number.toString()
}

const updatePreferences = () => {
  preferencesForm.patch(route('profile.update'), {
    preserveScroll: true,
  })
}
</script>
