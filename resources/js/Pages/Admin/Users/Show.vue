
<template>
  <AdminLayout>
    <Head :title="`User: ${user.name}`" />

    <!-- Animated Background -->
    <div class="fixed inset-0 -z-10 overflow-hidden">
      <!-- Animated Mesh Pattern -->
      <div class="absolute inset-0 opacity-30">
        <svg class="absolute inset-0 h-full w-full animate-pulse" viewBox="0 0 400 400">
          <defs>
            <linearGradient id="meshGradient" x1="0%" y1="0%" x2="100%" y2="100%" gradientTransform="rotate(45)">
              <stop offset="0%" style="stop-color:#8b5cf6;stop-opacity:0.3">
                <animateTransform
                  attributeName="gradientTransform"
                  type="rotate"
                  values="0 50 50;360 50 50;0 50 50"
                  dur="8s"
                  repeatCount="indefinite"
                />
              </stop>
              <stop offset="50%" style="stop-color:#06b6d4;stop-opacity:0.3" />
              <stop offset="100%" style="stop-color:#a855f7;stop-opacity:0.3" />
            </linearGradient>
          </defs>
          <rect width="100%" height="100%" fill="url(#meshGradient)" />
          <g stroke="url(#meshGradient)" stroke-width="0.5" fill="none" opacity="0.4">
            <rect x="0" y="0" width="100" height="100" />
            <rect x="100" y="100" width="100" height="100" />
            <rect x="200" y="0" width="100" height="100" />
            <rect x="300" y="100" width="100" height="100" />
            <rect x="0" y="200" width="100" height="100" />
            <rect x="100" y="300" width="100" height="100" />
            <rect x="200" y="200" width="100" height="100" />
            <rect x="300" y="300" width="100" height="100" />
          </g>
        </svg>
      </div>

      <!-- Floating Particles -->
      <div class="absolute inset-0">
        <div 
          v-for="i in 20" 
          :key="i"
          class="absolute w-1 h-1 rounded-full bg-gradient-to-r from-violet-400 to-cyan-400 opacity-60"
          :style="{
            left: Math.random() * 100 + '%',
            top: Math.random() * 100 + '%',
            animationDelay: Math.random() * 8 + 's',
            animationDuration: (8 + Math.random() * 4) + 's'
          }"
          style="animation: float 12s ease-in-out infinite;"
        ></div>
      </div>

      <!-- Base gradient background -->
      <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-purple-900/50 to-slate-900"></div>
    </div>

    <template #header>
      <div class="flex items-center justify-between animate-fade-in-up" style="animation-delay: 0.1s;">
        <div>
          <h2 class="font-bold text-2xl text-transparent bg-clip-text bg-gradient-to-r from-violet-400 via-purple-400 to-cyan-400 leading-tight animate-shimmer">
            User Details
          </h2>
          <p class="text-sm text-slate-400 mt-1">
            Viewing {{ user.name }}'s account
          </p>
        </div>
        <div class="flex space-x-3">
          <Link
            :href="route('admin.users.edit', user.id)"
            class="group inline-flex items-center px-6 py-2.5 bg-white/10 backdrop-blur-xl border border-white/20 rounded-xl shadow-lg text-sm font-medium text-slate-300 hover:bg-white/20 hover:text-white hover:shadow-violet-500/25 hover:shadow-xl hover:border-violet-400/30 transition-all duration-300 hover:scale-105"
          >
            <PencilIcon class="h-4 w-4 mr-2 group-hover:text-violet-400 transition-colors" />
            Edit User
          </Link>
          <Link
            :href="route('admin.users.index')"
            class="group inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-violet-600 to-purple-600 hover:from-violet-500 hover:to-purple-500 border border-violet-500/20 rounded-xl font-semibold text-sm text-white shadow-lg hover:shadow-violet-500/25 hover:shadow-xl transition-all duration-300 hover:scale-105"
          >
            ← Back to Users
          </Link>
        </div>
      </div>
    </template>

    <div class="space-y-8">
      <!-- User Info Card -->
      <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl shadow-2xl hover:shadow-violet-500/10 transition-all duration-500 animate-fade-in-up" style="animation-delay: 0.2s;">
        <div class="p-8">
          <div class="flex items-center space-x-8">
            <div class="relative group">
              <div class="absolute -inset-1 bg-gradient-to-r from-violet-600 to-cyan-600 rounded-full blur opacity-75 group-hover:opacity-100 transition duration-300"></div>
              <div class="relative">
                <UserAvatar :user="user" size="xl" class="ring-4 ring-white/10" />
              </div>
            </div>
            <div class="flex-1">
              <h3 class="text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-white to-slate-300">
                {{ user.name }}
              </h3>
              <p class="text-slate-400 text-lg mt-1">{{ user.email }}</p>
              <div class="mt-4 flex items-center space-x-4">
                <span 
                  :class="user.is_active ? 'text-emerald-300 bg-emerald-500/20 border-emerald-500/30' : 'text-red-300 bg-red-500/20 border-red-500/30'" 
                  class="inline-flex px-3 py-1.5 text-sm font-semibold rounded-full border backdrop-blur-sm"
                >
                  {{ user.is_active ? 'Active' : 'Inactive' }}
                </span>
                <span 
                  v-if="user.email_verified_at" 
                  class="text-emerald-300 bg-emerald-500/20 border border-emerald-500/30 inline-flex px-3 py-1.5 text-sm font-semibold rounded-full backdrop-blur-sm"
                >
                  Verified
                </span>
                <span 
                  v-else 
                  class="text-amber-300 bg-amber-500/20 border border-amber-500/30 inline-flex px-3 py-1.5 text-sm font-semibold rounded-full backdrop-blur-sm"
                >
                  Unverified
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Stats & Info Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 animate-fade-in-up" style="animation-delay: 0.3s;">
        
        <!-- Statistics -->
        <div class="group bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl shadow-xl hover:shadow-cyan-500/10 hover:border-cyan-400/30 transition-all duration-500 hover:scale-105">
          <div class="p-6">
            <h3 class="text-xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-blue-400 mb-6">
              Statistics
            </h3>
            <div class="space-y-4">
              <div class="flex justify-between items-center p-3 rounded-xl bg-white/5 hover:bg-white/10 transition-all duration-300">
                <span class="text-slate-400">Images</span>
                <span class="text-lg font-bold text-cyan-300">{{ stats.images_count }}</span>
              </div>
              <div class="flex justify-between items-center p-3 rounded-xl bg-white/5 hover:bg-white/10 transition-all duration-300">
                <span class="text-slate-400">Albums</span>
                <span class="text-lg font-bold text-purple-300">{{ stats.albums_count }}</span>
              </div>
              <div class="flex justify-between items-center p-3 rounded-xl bg-white/5 hover:bg-white/10 transition-all duration-300">
                <span class="text-slate-400">Comments</span>
                <span class="text-lg font-bold text-violet-300">{{ stats.comments_count }}</span>
              </div>
              <div class="flex justify-between items-center p-3 rounded-xl bg-white/5 hover:bg-white/10 transition-all duration-300">
                <span class="text-slate-400">Total Views</span>
                <span class="text-lg font-bold text-emerald-300">{{ formatNumber(stats.total_views) }}</span>
              </div>
              <div class="flex justify-between items-center p-3 rounded-xl bg-white/5 hover:bg-white/10 transition-all duration-300">
                <span class="text-slate-400">Total Likes</span>
                <span class="text-lg font-bold text-pink-300">{{ formatNumber(stats.total_likes) }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Account Info -->
        <div class="group bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl shadow-xl hover:shadow-violet-500/10 hover:border-violet-400/30 transition-all duration-500 hover:scale-105">
          <div class="p-6">
            <h3 class="text-xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-violet-400 to-purple-400 mb-6">
              Account Information
            </h3>
            <div class="space-y-4">
              <div class="p-3 rounded-xl bg-white/5">
                <span class="text-slate-400 text-sm">Roles</span>
                <div class="mt-2">
                  <span 
                    v-for="role in user.roles" 
                    :key="role.id"
                    :class="getRoleBadgeClass(role.slug)"
                    class="inline-flex px-3 py-1.5 text-sm font-semibold rounded-full mr-2 border backdrop-blur-sm"
                  >
                    {{ role.name }}
                  </span>
                </div>
              </div>
              <div class="flex justify-between items-center p-3 rounded-xl bg-white/5 hover:bg-white/10 transition-all duration-300">
                <span class="text-slate-400">Joined</span>
                <span class="text-slate-200 font-medium">{{ formatDate(user.created_at) }}</span>
              </div>
              <div class="flex justify-between items-center p-3 rounded-xl bg-white/5 hover:bg-white/10 transition-all duration-300">
                <span class="text-slate-400">Last Login</span>
                <span class="text-slate-200 font-medium">
                  {{ user.last_login_at ? formatDate(user.last_login_at) : 'Never' }}
                </span>
              </div>
              <div class="flex justify-between items-center p-3 rounded-xl bg-white/5 hover:bg-white/10 transition-all duration-300">
                <span class="text-slate-400">Updated</span>
                <span class="text-slate-200 font-medium">{{ formatDate(user.updated_at) }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Storage Usage -->
        <div class="group bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl shadow-xl hover:shadow-purple-500/10 hover:border-purple-400/30 transition-all duration-500 hover:scale-105">
          <div class="p-6">
            <h3 class="text-xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-pink-400 mb-6">
              Storage Usage
            </h3>
            <div class="space-y-4">
              <div class="p-3 rounded-xl bg-white/5">
                <div class="flex justify-between text-sm mb-3">
                  <span class="text-slate-400">Usage</span>
                  <span class="font-bold text-purple-300">{{ user.storage_percentage }}%</span>
                </div>
                <div class="w-full bg-slate-700/50 rounded-full h-3 overflow-hidden">
                  <div 
                    class="bg-gradient-to-r from-purple-500 to-pink-500 h-3 rounded-full transition-all duration-1000 ease-out shadow-lg shadow-purple-500/30" 
                    :style="{ width: user.storage_percentage + '%' }"
                  ></div>
                </div>
              </div>
              <div class="flex justify-between items-center p-3 rounded-xl bg-white/5 hover:bg-white/10 transition-all duration-300">
                <span class="text-slate-400">Used</span>
                <span class="text-slate-200 font-bold">{{ formatBytes(user.storage_used) }}</span>
              </div>
              <div class="flex justify-between items-center p-3 rounded-xl bg-white/5 hover:bg-white/10 transition-all duration-300">
                <span class="text-slate-400">Quota</span>
                <span class="text-slate-200 font-bold">{{ formatBytes(user.storage_quota) }}</span>
              </div>
              <div class="pt-2">
                <button
                  @click="resetStorage"
                  class="w-full text-sm text-purple-400 hover:text-purple-300 py-2 px-4 rounded-lg bg-white/5 hover:bg-white/10 transition-all duration-300 font-medium"
                >
                  Recalculate Storage
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Content -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 animate-fade-in-up" style="animation-delay: 0.4s;">
        
        <!-- Recent Images -->
        <div class="group bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl shadow-xl hover:shadow-cyan-500/10 hover:border-cyan-400/30 transition-all duration-500">
          <div class="p-6">
            <h3 class="text-xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-blue-400 mb-6">
              Recent Images
            </h3>
            <div v-if="recentImages.length > 0" class="grid grid-cols-3 gap-4">
              <div 
                v-for="(image, index) in recentImages"
                :key="image.id"
                class="group aspect-square bg-slate-800/50 rounded-xl overflow-hidden border border-white/10 hover:border-cyan-400/50 transition-all duration-300 hover:scale-105 animate-fade-in-up"
                :style="{ animationDelay: (0.1 * index) + 's' }"
              >
                <img
                  :src="getImageUrl(image)"
                  :alt="image.title"
                  class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                />
                <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
              </div>
            </div>
            <div v-else class="text-center py-12">
              <div class="relative">
                <PhotoIcon class="mx-auto h-16 w-16 text-slate-600 mb-4" />
                <div class="absolute inset-0 bg-gradient-to-r from-cyan-400/20 to-blue-400/20 blur-xl rounded-full"></div>
              </div>
              <p class="text-slate-400">No images uploaded</p>
            </div>
          </div>
        </div>

        <!-- Recent Comments -->
        <div class="group bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl shadow-xl hover:shadow-violet-500/10 hover:border-violet-400/30 transition-all duration-500">
          <div class="p-6">
            <h3 class="text-xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-violet-400 to-purple-400 mb-6">
              Recent Comments
            </h3>
            <div v-if="recentComments.length > 0" class="space-y-4">
              <div 
                v-for="(comment, index) in recentComments"
                :key="comment.id"
                class="border-l-4 border-gradient-to-b from-violet-400 to-purple-400 pl-4 p-3 rounded-r-xl bg-white/5 hover:bg-white/10 transition-all duration-300 animate-fade-in-up"
                :style="{ animationDelay: (0.1 * index) + 's' }"
              >
                <p class="text-slate-200 text-sm leading-relaxed">{{ comment.body }}</p>
                <p class="text-xs text-slate-500 mt-2">
                  on <span class="text-violet-400">{{ comment.image?.title || 'Image' }}</span> • {{ formatDate(comment.created_at) }}
                </p>
              </div>
            </div>
            <div v-else class="text-center py-12">
              <div class="relative">
                <ChatBubbleLeftIcon class="mx-auto h-16 w-16 text-slate-600 mb-4" />
                <div class="absolute inset-0 bg-gradient-to-r from-violet-400/20 to-purple-400/20 blur-xl rounded-full"></div>
              </div>
              <p class="text-slate-400">No comments posted</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Actions -->
      <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl shadow-xl animate-fade-in-up" style="animation-delay: 0.5s;">
        <div class="p-6">
          <h3 class="text-xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-pink-400 to-rose-400 mb-6">
            User Actions
          </h3>
          <div class="flex flex-wrap gap-4">
            <button
              @click="toggleStatus"
              :class="user.is_active ? 'bg-gradient-to-r from-red-600 to-red-500 hover:from-red-500 hover:to-red-400 shadow-red-500/25' : 'bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-400 shadow-emerald-500/25'"
              class="inline-flex items-center px-6 py-3 border border-transparent rounded-xl font-semibold text-sm text-white shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105"
            >
              {{ user.is_active ? 'Deactivate User' : 'Activate User' }}
            </button>
            
            <button
              @click="showRoleModal = true"
              class="inline-flex items-center px-6 py-3 bg-white/10 backdrop-blur-xl border border-white/20 rounded-xl shadow-lg text-sm font-medium text-slate-300 hover:bg-white/20 hover:text-white hover:shadow-violet-500/25 hover:shadow-xl hover:border-violet-400/30 transition-all duration-300 hover:scale-105"
            >
              Change Role
            </button>
            
            <button
              @click="showStorageModal = true"
              class="inline-flex items-center px-6 py-3 bg-white/10 backdrop-blur-xl border border-white/20 rounded-xl shadow-lg text-sm font-medium text-slate-300 hover:bg-white/20 hover:text-white hover:shadow-purple-500/25 hover:shadow-xl hover:border-purple-400/30 transition-all duration-300 hover:scale-105"
            >
              Update Storage Quota
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Role Change Modal -->
    <Modal :show="showRoleModal" @close="showRoleModal = false">
      <div class="p-8 bg-slate-900 border border-white/20 rounded-2xl backdrop-blur-xl">
        <h3 class="text-xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-violet-400 to-purple-400 mb-6">
          Change User Role
        </h3>
        <form @submit.prevent="updateRole">
          <div class="mb-6">
            <label class="block text-sm font-medium text-slate-300 mb-3">Select Role</label>
            <select
              v-model="roleForm.role"
              class="w-full px-4 py-3 bg-white/10 backdrop-blur-xl border border-white/20 rounded-xl text-slate-200 focus:ring-2 focus:ring-violet-500 focus:border-violet-400 transition-all duration-300"
              required
            >
              <option value="">Select a role...</option>
              <option value="admin">Admin</option>
              <option value="editor">Editor</option>
              <option value="user">User</option>
            </select>
          </div>
          <div class="flex justify-end space-x-4">
            <button
              type="button"
              @click="showRoleModal = false"
              class="px-6 py-3 bg-white/10 backdrop-blur-xl border border-white/20 rounded-xl text-sm font-medium text-slate-300 hover:bg-white/20 hover:text-white transition-all duration-300"
            >
              Cancel
            </button>
            <button
              type="submit"
              class="px-6 py-3 bg-gradient-to-r from-violet-600 to-purple-600 hover:from-violet-500 hover:to-purple-500 text-white rounded-xl text-sm font-medium shadow-lg hover:shadow-violet-500/25 transition-all duration-300 hover:scale-105"
            >
              Update Role
            </button>
          </div>
        </form>
      </div>
    </Modal>

    <!-- Storage Quota Modal -->
    <Modal :show="showStorageModal" @close="showStorageModal = false">
      <div class="p-8 bg-slate-900 border border-white/20 rounded-2xl backdrop-blur-xl">
        <h3 class="text-xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-pink-400 mb-6">
          Update Storage Quota
        </h3>
        <form @submit.prevent="updateStorageQuota">
          <div class="mb-6">
            <label class="block text-sm font-medium text-slate-300 mb-3">Quota (GB)</label>
            <input
              v-model="storageForm.quota"
              type="number"
              step="0.1"
              min="0"
              class="w-full px-4 py-3 bg-white/10 backdrop-blur-xl border border-white/20 rounded-xl text-slate-200 focus:ring-2 focus:ring-purple-500 focus:border-purple-400 transition-all duration-300"
              required
            />
          </div>
          <div class="flex justify-end space-x-4">
            <button
              type="button"
              @click="showStorageModal = false"
              class="px-6 py-3 bg-white/10 backdrop-blur-xl border border-white/20 rounded-xl text-sm font-medium text-slate-300 hover:bg-white/20 hover:text-white transition-all duration-300"
            >
              Cancel
            </button>
            <button
              type="submit"
              class="px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-500 hover:to-pink-500 text-white rounded-xl text-sm font-medium shadow-lg hover:shadow-purple-500/25 transition-all duration-300 hover:scale-105"
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
    admin: 'text-red-300 bg-red-500/20 border-red-500/30',
    editor: 'text-blue-300 bg-blue-500/20 border-blue-500/30',
    user: 'text-slate-300 bg-slate-500/20 border-slate-500/30',
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

<style scoped>
@keyframes float {
  0%, 100% { transform: translateY(0px) rotate(0deg); }
  33% { transform: translateY(-20px) rotate(120deg); }
  66% { transform: translateY(-10px) rotate(240deg); }
}

@keyframes shimmer {
  0% { background-position: -200% 0; }
  100% { background-position: 200% 0; }
}

@keyframes fade-in-up {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-shimmer {
  background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
  background-size: 200% 100%;
  animation: shimmer 3s ease-in-out infinite;
  -webkit-background-clip: text;
  background-clip: text;
}

.animate-fade-in-up {
  animation: fade-in-up 0.6s ease-out forwards;
  opacity: 0;
}
</style>