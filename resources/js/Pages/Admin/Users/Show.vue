<template>
  <AdminLayout>
    <Head :title="`User: ${user.name}`" />

    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            User Details
          </h2>
          <p class="text-sm text-gray-600 mt-1">
            Viewing {{ user.name }}'s account
          </p>
        </div>
        <div class="flex space-x-3">
          <Link
            :href="route('admin.users.edit', user.id)"
            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
          >
            <PencilIcon class="h-4 w-4 mr-2" />
            Edit User
          </Link>
          <Link
            :href="route('admin.users.index')"
            class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700"
          >
            ← Back to Users
          </Link>
        </div>
      </div>
    </template>

    <div class="space-y-6">
      <!-- User Info Card -->
      <div class="bg-white rounded-lg shadow">
        <div class="p-6">
          <div class="flex items-center space-x-6">
            <UserAvatar :user="user" size="xl" />
            <div class="flex-1">
              <h3 class="text-lg font-medium text-gray-900">{{ user.name }}</h3>
              <p class="text-sm text-gray-500">{{ user.email }}</p>
              <div class="mt-2 flex items-center space-x-4">
                <span :class="user.is_active ? 'text-green-800 bg-green-100' : 'text-red-800 bg-red-100'" 
                      class="inline-flex px-2 py-1 text-xs font-semibold rounded-full">
                  {{ user.is_active ? 'Active' : 'Inactive' }}
                </span>
                <span v-if="user.email_verified_at" class="text-green-800 bg-green-100 inline-flex px-2 py-1 text-xs font-semibold rounded-full">
                  Verified
                </span>
                <span v-else class="text-yellow-800 bg-yellow-100 inline-flex px-2 py-1 text-xs font-semibold rounded-full">
                  Unverified
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Stats & Info Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Statistics -->
        <div class="bg-white rounded-lg shadow p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Statistics</h3>
          <div class="space-y-3">
            <div class="flex justify-between">
              <span class="text-sm text-gray-500">Images</span>
              <span class="text-sm font-medium text-gray-900">{{ stats.images_count }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-gray-500">Albums</span>
              <span class="text-sm font-medium text-gray-900">{{ stats.albums_count }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-gray-500">Comments</span>
              <span class="text-sm font-medium text-gray-900">{{ stats.comments_count }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-gray-500">Total Views</span>
              <span class="text-sm font-medium text-gray-900">{{ formatNumber(stats.total_views) }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-gray-500">Total Likes</span>
              <span class="text-sm font-medium text-gray-900">{{ formatNumber(stats.total_likes) }}</span>
            </div>
          </div>
        </div>

        <!-- Account Info -->
        <div class="bg-white rounded-lg shadow p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Account Information</h3>
          <div class="space-y-3">
            <div>
              <span class="text-sm text-gray-500">Roles</span>
              <div class="mt-1">
                <span 
                  v-for="role in user.roles" 
                  :key="role.id"
                  :class="getRoleBadgeClass(role.slug)"
                  class="inline-flex px-2 py-1 text-xs font-semibold rounded-full mr-1"
                >
                  {{ role.name }}
                </span>
              </div>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-gray-500">Joined</span>
              <span class="text-sm font-medium text-gray-900">{{ formatDate(user.created_at) }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-gray-500">Last Login</span>
              <span class="text-sm font-medium text-gray-900">
                {{ user.last_login_at ? formatDate(user.last_login_at) : 'Never' }}
              </span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-gray-500">Updated</span>
              <span class="text-sm font-medium text-gray-900">{{ formatDate(user.updated_at) }}</span>
            </div>
          </div>
        </div>

        <!-- Storage Usage -->
        <div class="bg-white rounded-lg shadow p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Storage Usage</h3>
          <div class="space-y-3">
            <div>
              <div class="flex justify-between text-sm mb-1">
                <span class="text-gray-500">Usage</span>
                <span class="font-medium text-gray-900">{{ user.storage_percentage }}%</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div 
                  class="bg-blue-600 h-2 rounded-full" 
                  :style="{ width: user.storage_percentage + '%' }"
                ></div>
              </div>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-gray-500">Used</span>
              <span class="text-sm font-medium text-gray-900">{{ formatBytes(user.storage_used) }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-gray-500">Quota</span>
              <span class="text-sm font-medium text-gray-900">{{ formatBytes(user.storage_quota) }}</span>
            </div>
            <div class="pt-2">
              <button
                @click="resetStorage"
                class="w-full text-sm text-blue-600 hover:text-blue-500"
              >
                Recalculate Storage
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Content -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Recent Images -->
        <div class="bg-white rounded-lg shadow">
          <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Images</h3>
            <div v-if="recentImages.length > 0" class="grid grid-cols-3 gap-4">
              <div 
                v-for="image in recentImages"
                :key="image.id"
                class="aspect-square bg-gray-100 rounded-lg overflow-hidden"
              >
                <img
                  :src="getImageUrl(image)"
                  :alt="image.title"
                  class="w-full h-full object-cover"
                />
              </div>
            </div>
            <div v-else class="text-center py-8">
              <PhotoIcon class="mx-auto h-12 w-12 text-gray-400" />
              <p class="mt-2 text-sm text-gray-500">No images uploaded</p>
            </div>
          </div>
        </div>

        <!-- Recent Comments -->
        <div class="bg-white rounded-lg shadow">
          <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Comments</h3>
            <div v-if="recentComments.length > 0" class="space-y-4">
              <div 
                v-for="comment in recentComments"
                :key="comment.id"
                class="border-l-4 border-gray-200 pl-4"
              >
                <p class="text-sm text-gray-900">{{ comment.body }}</p>
                <p class="text-xs text-gray-500 mt-1">
                  on {{ comment.image?.title || 'Image' }} • {{ formatDate(comment.created_at) }}
                </p>
              </div>
            </div>
            <div v-else class="text-center py-8">
              <ChatBubbleLeftIcon class="mx-auto h-12 w-12 text-gray-400" />
              <p class="mt-2 text-sm text-gray-500">No comments posted</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Actions -->
      <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">User Actions</h3>
        <div class="flex flex-wrap gap-3">
          <button
            @click="toggleStatus"
            :class="user.is_active ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700'"
            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest"
          >
            {{ user.is_active ? 'Deactivate User' : 'Activate User' }}
          </button>
          
          <button
            @click="showRoleModal = true"
            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
          >
            Change Role
          </button>
          
          <button
            @click="showStorageModal = true"
            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
          >
            Update Storage Quota
          </button>
        </div>
      </div>
    </div>

    <!-- Role Change Modal -->
    <Modal :show="showRoleModal" @close="showRoleModal = false">
      <div class="p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Change User Role</h3>
        <form @submit.prevent="updateRole">
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Select Role</label>
            <select
              v-model="roleForm.role"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
              required
            >
              <option value="">Select a role...</option>
              <option value="admin">Admin</option>
              <option value="editor">Editor</option>
              <option value="user">User</option>
            </select>
          </div>
          <div class="flex justify-end space-x-3">
            <button
              type="button"
              @click="showRoleModal = false"
              class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50"
            >
              Cancel
            </button>
            <button
              type="submit"
              class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700"
            >
              Update Role
            </button>
          </div>
        </form>
      </div>
    </Modal>

    <!-- Storage Quota Modal -->
    <Modal :show="showStorageModal" @close="showStorageModal = false">
      <div class="p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Update Storage Quota</h3>
        <form @submit.prevent="updateStorageQuota">
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Quota (GB)</label>
            <input
              v-model="storageForm.quota"
              type="number"
              step="0.1"
              min="0"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
              required
            />
          </div>
          <div class="flex justify-end space-x-3">
            <button
              type="button"
              @click="showStorageModal = false"
              class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50"
            >
              Cancel
            </button>
            <button
              type="submit"
              class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700"
            >
              Update Quota
            </button>
          </div>
        </form>
      </div>
    </Modal>
  </AdminLayout>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { Head, Link, router, useForm } from '@inertiajs/vue3'
import route from 'ziggy-js'
import { 
  PencilIcon, 
  PhotoIcon, 
  ChatBubbleLeftIcon 
} from '@heroicons/vue/24/outline'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import UserAvatar from '@/Components/UserAvatar.vue'
import Modal from '@/Components/Modal.vue'

const props = defineProps({
  user: Object,
  stats: Object,
  recentImages: Array,
  recentComments: Array,
})

const showRoleModal = ref(false)
const showStorageModal = ref(false)

const roleForm = reactive({
  role: props.user.roles[0]?.slug || ''
})

const storageForm = reactive({
  quota: Math.round(props.user.storage_quota / (1024 * 1024 * 1024))
})

const formatNumber = (num) => {
  if (num >= 1000000) return (num / 1000000).toFixed(1) + 'M'
  if (num >= 1000) return (num / 1000).toFixed(1) + 'K'
  return num.toString()
}

const formatBytes = (bytes) => {
  if (bytes === 0) return '0 B'
  const k = 1024
  const sizes = ['B', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i]
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString()
}

const getRoleBadgeClass = (roleSlug) => {
  const classes = {
    admin: 'text-red-800 bg-red-100',
    editor: 'text-blue-800 bg-blue-100',
    user: 'text-gray-800 bg-gray-100',
  }
  return classes[roleSlug] || classes.user
}

const getImageUrl = (image) => {
  return `http://localhost:9000/gallery-images/${image.storage_path}`
}

const toggleStatus = () => {
  const action = props.user.is_active ? 'deactivate' : 'activate'
  if (confirm(`Are you sure you want to ${action} this user?`)) {
    router.post(route('admin.users.toggle-active', props.user.id))
  }
}

const resetStorage = () => {
  if (confirm('Recalculate storage usage for this user?')) {
    router.post(route('admin.users.reset-storage', props.user.id))
  }
}

const updateRole = () => {
  router.post(route('admin.users.assign-role', props.user.id), {
    role: roleForm.role
  }, {
    onSuccess: () => {
      showRoleModal.value = false
    }
  })
}

const updateStorageQuota = () => {
  router.post(route('admin.users.reset-storage', props.user.id), {
    quota: storageForm.quota
  }, {
    onSuccess: () => {
      showStorageModal.value = false
    }
  })
}
</script>
