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
