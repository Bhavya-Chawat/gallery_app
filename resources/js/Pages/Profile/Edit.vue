<template>
  <AppLayout>
    <Head title="Profile Settings" />

    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Profile Settings
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
        
        <!-- Profile Overview -->
        <div class="bg-white shadow-sm rounded-lg">
          <div class="p-6">
            <div class="flex items-center justify-between mb-6">
              <div class="flex items-center space-x-6">
                <UserAvatar :user="user" size="xl" />
                <div>
                  <h3 class="text-lg font-medium text-gray-900">{{ user.name }}</h3>
                  <p class="text-sm text-gray-500">{{ user.email }}</p>
                  <p class="text-xs text-gray-400 mt-1">
                    Member since {{ formatDate(user.created_at) }}
                  </p>
                </div>
              </div>
              <div class="text-right">
                <Link :href="route('profile.show', user.id)" class="text-blue-600 hover:text-blue-500 text-sm">
                  View Public Profile
                </Link>
              </div>
            </div>

            <!-- Profile Completion -->
            <div class="mb-6">
              <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-700">Profile Completion</span>
                <span class="text-sm text-gray-500">{{ profileCompletion }}%</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div 
                  class="bg-blue-600 h-2 rounded-full transition-all" 
                  :style="{ width: profileCompletion + '%' }"
                ></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Avatar Upload -->
        <div class="bg-white shadow-sm rounded-lg">
          <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Profile Photo</h3>
            <form @submit.prevent="uploadAvatar" enctype="multipart/form-data">
              <div class="flex items-center space-x-6">
                <UserAvatar :user="user" size="lg" />
                <div class="flex-1">
                  <input
                    ref="avatarInput"
                    type="file"
                    accept="image/*"
                    @change="handleAvatarChange"
                    class="hidden"
                  />
                  <button
                    type="button"
                    @click="$refs.avatarInput.click()"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                  >
                    Change Photo
                  </button>
                  <p class="mt-2 text-sm text-gray-500">
                    JPG, PNG up to 2MB. Recommended: 400x400px
                  </p>
                </div>
              </div>
              <div v-if="avatarForm.avatar" class="mt-4">
                <button
                  type="submit"
                  :disabled="avatarForm.processing"
                  class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 disabled:opacity-50"
                >
                  {{ avatarForm.processing ? 'Uploading...' : 'Upload Photo' }}
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Profile Information -->
        <div class="bg-white shadow-sm rounded-lg">
          <div class="p-6">
            <UpdateProfileInformationForm
              :must-verify-email="mustVerifyEmail"
              :status="status"
              :user="user"
            />
          </div>
        </div>

        <!-- Privacy Settings -->
        <div class="bg-white shadow-sm rounded-lg">
          <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Privacy Settings</h3>
            <form @submit.prevent="updatePrivacy" class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Profile Visibility
                </label>
                <div class="space-y-2">
                  <label class="flex items-center">
                    <input
                      v-model="privacyForm.profile_visibility"
                      type="radio"
                      value="public"
                      class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                    />
                    <span class="ml-2 text-sm text-gray-700">
                      Public - Anyone can view your profile
                    </span>
                  </label>
                  <label class="flex items-center">
                    <input
                      v-model="privacyForm.profile_visibility"
                      type="radio"
                      value="private"
                      class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                    />
                    <span class="ml-2 text-sm text-gray-700">
                      Private - Only you can view your profile
                    </span>
                  </label>
                </div>
              </div>

              <div>
                <label class="flex items-center">
                  <input
                    v-model="privacyForm.show_email_publicly"
                    type="checkbox"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                  />
                  <span class="ml-2 text-sm text-gray-700">
                    Show email address on public profile
                  </span>
                </label>
              </div>

              <div>
                <label class="flex items-center">
                  <input
                    v-model="privacyForm.show_stats_publicly"
                    type="checkbox"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                  />
                  <span class="ml-2 text-sm text-gray-700">
                    Show statistics on public profile
                  </span>
                </label>
              </div>

              <div>
                <label class="flex items-center">
                  <input
                    v-model="privacyForm.email_notifications"
                    type="checkbox"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                  />
                  <span class="ml-2 text-sm text-gray-700">
                    Email notifications
                  </span>
                </label>
                <p class="mt-1 text-xs text-gray-500">
                  Receive email notifications for comments, likes, and system updates.
                </p>
              </div>

              <div class="pt-4">
                <button
                  type="submit"
                  :disabled="privacyForm.processing"
                  class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 disabled:opacity-25"
                >
                  Save Privacy Settings
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Statistics -->
        <div class="bg-white shadow-sm rounded-lg">
          <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Your Statistics</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
              <div class="text-center p-4 bg-gray-50 rounded-lg">
                <div class="text-2xl font-semibold text-gray-900">{{ stats.images || 0 }}</div>
                <div class="text-sm text-gray-500">Images</div>
              </div>
              <div class="text-center p-4 bg-gray-50 rounded-lg">
                <div class="text-2xl font-semibold text-gray-900">{{ stats.albums || 0 }}</div>
                <div class="text-sm text-gray-500">Albums</div>
              </div>
              <div class="text-center p-4 bg-gray-50 rounded-lg">
                <div class="text-2xl font-semibold text-gray-900">{{ formatNumber(stats.views || 0) }}</div>
                <div class="text-sm text-gray-500">Total Views</div>
              </div>
              <div class="text-center p-4 bg-gray-50 rounded-lg">
                <div class="text-2xl font-semibold text-gray-900">{{ formatNumber(stats.likes || 0) }}</div>
                <div class="text-sm text-gray-500">Total Likes</div>
              </div>
            </div>
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

        <!-- Password Update -->
        <div class="bg-white shadow-sm rounded-lg">
          <div class="p-6">
            <UpdatePasswordForm />
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
import { computed, ref } from 'vue'
import { useForm, Head, Link } from '@inertiajs/vue3'
import route from 'ziggy-js'

import AppLayout from '@/Layouts/AppLayout.vue'
import UserAvatar from '@/Components/UserAvatar.vue'
import StorageUsageCard from '@/Components/StorageUsageCard.vue'
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

// Avatar upload form
const avatarForm = useForm({
  avatar: null,
})

const avatarInput = ref(null)

// Privacy settings form  
const privacyForm = useForm({
  profile_visibility: props.user.profile_visibility,
  show_email_publicly: props.user.show_email_publicly,
  show_stats_publicly: props.user.show_stats_publicly,
  email_notifications: props.user.email_notifications,
})

// Profile completion calculation
const profileCompletion = computed(() => {
  let completion = 0
  const fields = ['name', 'email', 'bio', 'avatar_path', 'website']
  
  fields.forEach(field => {
    if (props.user[field]) completion += 20
  })
  
  return Math.round(completion)
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

const handleAvatarChange = (event) => {
  const file = event.target.files[0]
  if (file) {
    avatarForm.avatar = file
  }
}

const uploadAvatar = () => {
  avatarForm.post(route('profile.avatar'), {
    onSuccess: () => {
      avatarForm.reset()
      avatarInput.value.value = ''
    }
  })
}

const updatePrivacy = () => {
  privacyForm.patch(route('profile.privacy'))
}
</script>
