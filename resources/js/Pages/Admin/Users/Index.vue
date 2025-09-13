<template>
  <AdminLayout>
    <Head title="User Management" />

    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            User Management
          </h2>
          <p class="text-sm text-gray-600 mt-1">
            Manage users, roles, and permissions
          </p>
        </div>
        <Link
          :href="route('admin.users.create')"
          class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700"
        >
          <PlusIcon class="h-4 w-4 mr-2" />
          Create User
        </Link>
      </div>
    </template>

    <div class="space-y-6">
      <!-- Filters -->
      <div class="bg-white rounded-lg shadow p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
            <input
              v-model="searchForm.search"
              @keyup.enter="search"
              type="text"
              placeholder="Search users..."
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
            />
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
            <select
              v-model="searchForm.role"
              @change="search"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
            >
              <option value="">All Roles</option>
              <option v-for="role in roles" :key="role.id" :value="role.slug">
                {{ role.name }}
              </option>
            </select>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select
              v-model="searchForm.status"
              @change="search"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
            >
              <option value="">All Statuses</option>
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>
          
          <div class="flex items-end">
            <button
              @click="clearFilters"
              class="w-full px-4 py-2 text-sm text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50"
            >
              Clear Filters
            </button>
          </div>
        </div>
      </div>

      <!-- Users Table -->
      <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  User
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Role
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Status
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Storage
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Joined
                </th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Actions
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="user in users.data" :key="user.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <UserAvatar :user="user" size="sm" />
                    <div class="ml-4">
                      <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
                      <div class="text-sm text-gray-500">{{ user.email }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span 
                    v-for="role in user.roles" 
                    :key="role.id"
                    :class="getRoleBadgeClass(role.slug)"
                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full mr-1"
                  >
                    {{ role.name }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="user.is_active ? 'text-green-800 bg-green-100' : 'text-red-800 bg-red-100'" 
                        class="inline-flex px-2 py-1 text-xs font-semibold rounded-full">
                    {{ user.is_active ? 'Active' : 'Inactive' }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  <div class="w-full bg-gray-200 rounded-full h-2">
                    <div 
                      class="bg-blue-600 h-2 rounded-full" 
                      :style="{ width: user.storage_percentage + '%' }"
                    ></div>
                  </div>
                  <div class="text-xs text-gray-500 mt-1">
                    {{ formatBytes(user.storage_used) }} / {{ formatBytes(user.storage_quota) }}
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ formatDate(user.created_at) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <div class="flex justify-end space-x-2">
                    <Link
                      :href="route('admin.users.show', user.id)"
                      class="text-blue-600 hover:text-blue-500"
                    >
                      View
                    </Link>
                    <Link
                      :href="route('admin.users.edit', user.id)"
                      class="text-indigo-600 hover:text-indigo-500"
                    >
                      Edit
                    </Link>
                    <button
                      @click="toggleUserStatus(user)"
                      :class="user.is_active ? 'text-red-600 hover:text-red-500' : 'text-green-600 hover:text-green-500'"
                    >
                      {{ user.is_active ? 'Disable' : 'Enable' }}
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        
        <!-- Pagination -->
        <div v-if="users.links" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
          <Pagination :links="users.links" />
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { reactive } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import route from 'ziggy-js'
import { PlusIcon } from '@heroicons/vue/24/outline'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import UserAvatar from '@/Components/UserAvatar.vue'
import Pagination from '@/Components/Pagination.vue'

const props = defineProps({
  users: Object,
  roles: Array,
  filters: Object,
})

const searchForm = reactive({
  search: props.filters.search || '',
  role: props.filters.role || '',
  status: props.filters.status || '',
})

const search = () => {
  const params = Object.fromEntries(
    Object.entries(searchForm).filter(([_, value]) => value !== '')
  )
  
  router.get(route('admin.users.index'), params, {
    preserveState: true,
    replace: true,
  })
}

const clearFilters = () => {
  Object.keys(searchForm).forEach(key => {
    searchForm[key] = ''
  })
  search()
}

const getRoleBadgeClass = (roleSlug) => {
  const classes = {
    admin: 'text-red-800 bg-red-100',
    editor: 'text-blue-800 bg-blue-100',
    user: 'text-gray-800 bg-gray-100',
  }
  return classes[roleSlug] || classes.user
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

const toggleUserStatus = (user) => {
  const action = user.is_active ? 'disable' : 'enable'
  if (confirm(`Are you sure you want to ${action} this user?`)) {
    router.post(route('admin.users.toggle-active', user.id))
  }
}
</script>
/*<template>
  <AdminLayout>
    <Head title="User Management" />

    <template #header>
      <div class="flex items-center justify-between animate-fade-in-up">
        <div>
          <h2 class="font-semibold text-xl bg-gradient-to-r from-violet-400 via-purple-400 to-cyan-400 bg-clip-text text-transparent leading-tight animate-shimmer-text">
            User Management
          </h2>
          <p class="text-sm text-slate-300 mt-1">
            Manage users, roles, and permissions
          </p>
        </div>
        <Link
          :href="route('admin.users.create')"
          class="relative inline-flex items-center px-4 py-2 bg-gradient-to-r from-violet-600 via-purple-600 to-cyan-600 border border-white/20 rounded-xl font-semibold text-xs text-white uppercase tracking-widest hover:from-violet-500 hover:via-purple-500 hover:to-cyan-500 transition-all duration-500 hover:scale-105 hover:shadow-2xl hover:shadow-violet-500/25 group overflow-hidden backdrop-blur-xl"
        >
          <div class="absolute inset-0 bg-gradient-to-r from-violet-400/20 via-purple-400/20 to-cyan-400/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
          <PlusIcon class="h-4 w-4 mr-2 relative z-10" />
          <span class="relative z-10">Create User</span>
          <div class="absolute -inset-1 bg-gradient-to-r from-violet-600 via-purple-600 to-cyan-600 rounded-xl blur opacity-30 group-hover:opacity-70 transition-opacity duration-300 -z-10"></div>
        </Link>
      </div>
    </template>

    <div class="space-y-6 relative">
      <!-- Floating Particles Background -->
      <div class="fixed inset-0 pointer-events-none overflow-hidden opacity-15 z-0">
        <div 
          v-for="i in 20" 
          :key="`particle-${i}`"
          :class="[
            'absolute w-1 h-1 rounded-full',
            'bg-gradient-to-r from-violet-400 to-cyan-400',
            `animate-float-particle-${(i % 5) + 1}`
          ]"
          :style="{
            left: `${Math.random() * 100}%`,
            top: `${Math.random() * 100}%`,
            animationDelay: `${Math.random() * 10}s`,
            animationDuration: `${8 + Math.random() * 4}s`
          }"
        ></div>
      </div>

      <!-- Filters -->
      <div class="relative group animate-fade-in-up" style="animation-delay: 0.1s;">
        <div class="absolute inset-0 bg-gradient-to-br from-violet-500/10 via-purple-500/5 to-cyan-500/10 rounded-2xl blur-xl opacity-0 group-hover:opacity-100 transition-opacity duration-700"></div>
        <div class="relative z-10 bg-slate-800/50 backdrop-blur-xl rounded-2xl shadow-2xl border border-white/10 hover:border-white/20 transition-all duration-500 hover:scale-[1.01] hover:shadow-violet-500/10 p-6">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="group animate-slide-in-right" style="animation-delay: 0.1s;">
              <label class="block text-sm font-medium text-slate-300 mb-1 group-hover:text-white transition-colors duration-300">Search</label>
              <div class="relative">
                <input
                  v-model="searchForm.search"
                  @keyup.enter="search"
                  type="text"
                  placeholder="Search users..."
                  class="w-full px-3 py-2 bg-slate-700/50 backdrop-blur-sm border border-white/20 rounded-xl text-white placeholder-slate-400 focus:ring-2 focus:ring-violet-500/50 focus:border-violet-500/50 transition-all duration-300 hover:bg-slate-700/70 focus:bg-slate-700/70 focus:scale-105"
                />
                <div class="absolute inset-0 bg-gradient-to-r from-violet-500/10 to-purple-500/10 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
              </div>
            </div>
            
            <div class="group animate-slide-in-right" style="animation-delay: 0.2s;">
              <label class="block text-sm font-medium text-slate-300 mb-1 group-hover:text-white transition-colors duration-300">Role</label>
              <div class="relative">
                <select
                  v-model="searchForm.role"
                  @change="search"
                  class="w-full px-3 py-2 bg-slate-700/50 backdrop-blur-sm border border-white/20 rounded-xl text-white focus:ring-2 focus:ring-violet-500/50 focus:border-violet-500/50 transition-all duration-300 hover:bg-slate-700/70 focus:bg-slate-700/70 focus:scale-105"
                >
                  <option value="">All Roles</option>
                  <option v-for="role in roles" :key="role.id" :value="role.slug">
                    {{ role.name }}
                  </option>
                </select>
                <div class="absolute inset-0 bg-gradient-to-r from-purple-500/10 to-cyan-500/10 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
              </div>
            </div>
            
            <div class="group animate-slide-in-right" style="animation-delay: 0.3s;">
              <label class="block text-sm font-medium text-slate-300 mb-1 group-hover:text-white transition-colors duration-300">Status</label>
              <div class="relative">
                <select
                  v-model="searchForm.status"
                  @change="search"
                  class="w-full px-3 py-2 bg-slate-700/50 backdrop-blur-sm border border-white/20 rounded-xl text-white focus:ring-2 focus:ring-violet-500/50 focus:border-violet-500/50 transition-all duration-300 hover:bg-slate-700/70 focus:bg-slate-700/70 focus:scale-105"
                >
                  <option value="">All Statuses</option>
                  <option value="active">Active</option>
                  <option value="inactive">Inactive</option>
                </select>
                <div class="absolute inset-0 bg-gradient-to-r from-cyan-500/10 to-violet-500/10 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
              </div>
            </div>
            
            <div class="flex items-end animate-slide-in-right" style="animation-delay: 0.4s;">
              <button
                @click="clearFilters"
                class="w-full px-4 py-2 text-sm bg-slate-700/50 backdrop-blur-sm text-slate-300 border border-white/20 rounded-xl hover:bg-slate-700/70 hover:text-white hover:border-white/30 transition-all duration-300 hover:scale-105"
              >
                Clear Filters
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Users Table -->
      <div class="relative group animate-fade-in-up" style="animation-delay: 0.2s;">
        <div class="absolute inset-0 bg-gradient-to-br from-cyan-500/10 via-purple-500/5 to-violet-500/10 rounded-2xl blur-xl opacity-0 group-hover:opacity-100 transition-opacity duration-700"></div>
        <div class="relative z-10 bg-slate-800/50 backdrop-blur-xl rounded-2xl shadow-2xl border border-white/10 hover:border-white/20 transition-all duration-500 hover:scale-[1.005] hover:shadow-cyan-500/10 overflow-hidden">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-white/10">
              <thead class="bg-slate-800/70 backdrop-blur-sm">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider animate-fade-in-up" style="animation-delay: 0.1s;">
                    User
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider animate-fade-in-up" style="animation-delay: 0.2s;">
                    Role
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider animate-fade-in-up" style="animation-delay: 0.3s;">
                    Status
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider animate-fade-in-up" style="animation-delay: 0.4s;">
                    Storage
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider animate-fade-in-up" style="animation-delay: 0.5s;">
                    Joined
                  </th>
                  <th class="px-6 py-3 text-right text-xs font-medium text-slate-400 uppercase tracking-wider animate-fade-in-up" style="animation-delay: 0.6s;">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody class="bg-transparent divide-y divide-white/10">
                <tr 
                  v-for="(user, index) in users.data" 
                  :key="user.id" 
                  class="hover:bg-slate-700/30 transition-all duration-300 group animate-slide-in-up"
                  :style="{ animationDelay: `${0.1 + index * 0.05}s` }"
                >
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center group-hover:scale-105 transition-transform duration-300">
                      <UserAvatar :user="user" size="sm" />
                      <div class="ml-4">
                        <div class="text-sm font-medium text-white">{{ user.name }}</div>
                        <div class="text-sm text-slate-400">{{ user.email }}</div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span 
                      v-for="role in user.roles" 
                      :key="role.id"
                      :class="getRoleBadgeClass(role.slug)"
                      class="inline-flex px-2 py-1 text-xs font-semibold rounded-full mr-1 backdrop-blur-sm border border-white/20 transition-all duration-300 hover:scale-110"
                    >
                      {{ role.name }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span 
                      :class="user.is_active ? 'text-emerald-300 bg-emerald-500/20 border-emerald-500/30' : 'text-red-300 bg-red-500/20 border-red-500/30'" 
                      class="inline-flex px-2 py-1 text-xs font-semibold rounded-full backdrop-blur-sm border transition-all duration-300 hover:scale-110"
                    >
                      {{ user.is_active ? 'Active' : 'Inactive' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                    <div class="w-full bg-slate-700/50 rounded-full h-2 backdrop-blur-sm border border-white/10 overflow-hidden">
                      <div 
                        class="bg-gradient-to-r from-violet-500 to-cyan-500 h-2 rounded-full transition-all duration-1000 ease-out animate-pulse" 
                        :style="{ width: user.storage_percentage + '%' }"
                      ></div>
                    </div>
                    <div class="text-xs text-slate-400 mt-1">
                      {{ formatBytes(user.storage_used) }} / {{ formatBytes(user.storage_quota) }}
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-300">
                    {{ formatDate(user.created_at) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <div class="flex justify-end space-x-2">
                      <Link
                        :href="route('admin.users.show', user.id)"
                        class="px-3 py-1.5 bg-violet-600/80 hover:bg-violet-500 text-violet-100 rounded-lg transition-all duration-300 hover:scale-105 backdrop-blur-sm border border-violet-500/20 hover:shadow-lg hover:shadow-violet-500/25"
                      >
                        View
                      </Link>
                      <Link
                        :href="route('admin.users.edit', user.id)"
                        class="px-3 py-1.5 bg-cyan-600/80 hover:bg-cyan-500 text-cyan-100 rounded-lg transition-all duration-300 hover:scale-105 backdrop-blur-sm border border-cyan-500/20 hover:shadow-lg hover:shadow-cyan-500/25"
                      >
                        Edit
                      </Link>
                      <button
                        @click="toggleUserStatus(user)"
                        :class="user.is_active ? 'bg-red-600/80 hover:bg-red-500 text-red-100 border-red-500/20 hover:shadow-red-500/25' : 'bg-green-600/80 hover:bg-green-500 text-green-100 border-green-500/20 hover:shadow-green-500/25'"
                        class="px-3 py-1.5 rounded-lg transition-all duration-300 hover:scale-105 backdrop-blur-sm border hover:shadow-lg"
                      >
                        {{ user.is_active ? 'Disable' : 'Enable' }}
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          
          <!-- Pagination -->
          <div v-if="users.links" class="bg-slate-800/70 backdrop-blur-sm px-4 py-3 border-t border-white/10 sm:px-6">
            <Pagination :links="users.links" />
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { reactive } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import route from 'ziggy-js'
import { PlusIcon } from '@heroicons/vue/24/outline'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import UserAvatar from '@/Components/UserAvatar.vue'
import Pagination from '@/Components/Pagination.vue'

const props = defineProps({
  users: Object,
  roles: Array,
  filters: Object,
})

const searchForm = reactive({
  search: props.filters.search || '',
  role: props.filters.role || '',
  status: props.filters.status || '',
})

const search = () => {
  const params = Object.fromEntries(
    Object.entries(searchForm).filter(([_, value]) => value !== '')
  )
  
  router.get(route('admin.users.index'), params, {
    preserveState: true,
    replace: true,
  })
}

const clearFilters = () => {
  Object.keys(searchForm).forEach(key => {
    searchForm[key] = ''
  })
  search()
}

const getRoleBadgeClass = (roleSlug) => {
  const classes = {
    admin: 'text-red-300 bg-red-500/20 border-red-500/30',
    editor: 'text-blue-300 bg-blue-500/20 border-blue-500/30',
    user: 'text-slate-300 bg-slate-500/20 border-slate-500/30',
  }
  return classes[roleSlug] || classes.user
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

const toggleUserStatus = (user) => {
  const action = user.is_active ? 'disable' : 'enable'
  if (confirm(`Are you sure you want to ${action} this user?`)) {
    router.post(route('admin.users.toggle-active', user.id))
  }
}
</script>

<style scoped>
@keyframes shimmer-text {
  0% { background-position: -200% center; }
  100% { background-position: 200% center; }
}

@keyframes fade-in-up {
  0% { 
    opacity: 0; 
    transform: translateY(30px); 
  }
  100% { 
    opacity: 1; 
    transform: translateY(0); 
  }
}

@keyframes slide-in-right {
  0% {
    opacity: 0;
    transform: translateX(30px);
  }
  100% {
    opacity: 1;
    transform: translateX(0);
  }
}

@keyframes slide-in-up {
  0% {
    opacity: 0;
    transform: translateY(20px);
  }
  100% {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes float-particle-1 {
  0%, 100% { 
    transform: translateY(0px) translateX(0px) rotate(0deg); 
    opacity: 0.4; 
  }
  25% { 
    transform: translateY(-120px) translateX(60px) rotate(90deg); 
    opacity: 1; 
  }
  50% { 
    transform: translateY(-80px) translateX(-40px) rotate(180deg); 
    opacity: 0.8; 
  }
  75% { 
    transform: translateY(-160px) translateX(20px) rotate(270deg); 
    opacity: 0.9; 
  }
}

@keyframes float-particle-2 {
  0%, 100% { 
    transform: translateY(0px) translateX(0px) rotate(0deg); 
    opacity: 0.5; 
  }
  25% { 
    transform: translateY(-100px) translateX(-50px) rotate(120deg); 
    opacity: 0.9; 
  }
  50% { 
    transform: translateY(-140px) translateX(30px) rotate(240deg); 
    opacity: 0.7; 
  }
  75% { 
    transform: translateY(-70px) translateX(-20px) rotate(360deg); 
    opacity: 0.8; 
  }
}

@keyframes float-particle-3 {
  0%, 100% { 
    transform: translateY(0px) translateX(0px) rotate(0deg); 
    opacity: 0.7; 
  }
  33% { 
    transform: translateY(-90px) translateX(80px) rotate(150deg); 
    opacity: 0.4; 
  }
  66% { 
    transform: translateY(-180px) translateX(-30px) rotate(300deg); 
    opacity: 1; 
  }
}

@keyframes float-particle-4 {
  0%, 100% { 
    transform: translateY(0px) translateX(0px) rotate(0deg); 
    opacity: 0.4; 
  }
  50% { 
    transform: translateY(-130px) translateX(-60px) rotate(180deg); 
    opacity: 0.8; 
  }
}

@keyframes float-particle-5 {
  0%, 100% { 
    transform: translateY(0px) translateX(0px) rotate(0deg); 
    opacity: 0.6; 
  }
  20% { 
    transform: translateY(-60px) translateX(40px) rotate(72deg); 
    opacity: 0.9; 
  }
  40% { 
    transform: translateY(-120px) translateX(-20px) rotate(144deg); 
    opacity: 0.5; 
  }
  60% { 
    transform: translateY(-90px) translateX(70px) rotate(216deg); 
    opacity: 0.8; 
  }
  80% { 
    transform: translateY(-150px) translateX(-10px) rotate(288deg); 
    opacity: 0.7; 
  }
}

.animate-shimmer-text {
  background-size: 200% auto;
  animation: shimmer-text 3s linear infinite;
}

.animate-fade-in-up {
  animation: fade-in-up 0.8s ease-out forwards;
}

.animate-slide-in-right {
  animation: slide-in-right 0.6s ease-out forwards;
}

.animate-slide-in-up {
  animation: slide-in-up 0.6s ease-out forwards;
}

.animate-float-particle-1 { animation: float-particle-1 12s ease-in-out infinite; }
.animate-float-particle-2 { animation: float-particle-2 15s ease-in-out infinite; }
.animate-float-particle-3 { animation: float-particle-3 10s ease-in-out infinite; }
.animate-float-particle-4 { animation: float-particle-4 14s ease-in-out infinite; }
.animate-float-particle-5 { animation: float-particle-5 16s ease-in-out infinite; }

/* Enhanced form styling */
select option {
  background: rgb(30, 41, 59);
  color: white;
}

input:focus,
select:focus {
  box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
}

/* Custom scrollbar for better aesthetics */
::-webkit-scrollbar {
  width: 6px;
  height: 6px;
}

::-webkit-scrollbar-track {
  background: rgba(30, 41, 59, 0.5);
  border-radius: 3px;
}

::-webkit-scrollbar-thumb {
  background: linear-gradient(135deg, #8b5cf6, #06b6d4);
  border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
  background: linear-gradient(135deg, #7c3aed, #0891b2);
}
</style>