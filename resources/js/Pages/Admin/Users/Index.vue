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
            Manage user accounts and permissions
          </p>
        </div>
        <div class="flex items-center space-x-3">
          <Link
            :href="route('admin.users.create')"
            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700"
          >
            <PlusIcon class="h-4 w-4 mr-2" />
            Create User
          </Link>
        </div>
      </div>
    </template>

    <div class="py-6">
      <div class="space-y-6">
        <!-- Filters -->
        <div class="bg-white shadow rounded-lg">
          <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700">Search</label>
                <input
                  v-model="searchForm.search"
                  @input="debounceSearch"
                  type="text"
                  placeholder="Search users..."
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Role</label>
                <select
                  v-model="searchForm.role"
                  @change="search"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                >
                  <option value="">All Roles</option>
                  <option
                    v-for="role in roles"
                    :key="role.id"
                    :value="role.slug"
                  >
                    {{ role.name }}
                  </option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Status</label>
                <select
                  v-model="searchForm.status"
                  @change="search"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                >
                  <option value="">All Users</option>
                  <option value="active">Active</option>
                  <option value="inactive">Inactive</option>
                </select>
              </div>
              <div class="flex items-end">
                <button
                  @click="resetFilters"
                  class="w-full px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                >
                  Reset
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Users Table -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">
              Users ({{ users.meta?.total || 0 }})
            </h3>
          </div>
          
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
                    Last Login
                  </th>
                  <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr
                  v-for="user in users.data"
                  :key="user.id"
                  class="hover:bg-gray-50"
                >
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <UserAvatar :user="user" size="sm" />
                      <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900">
                          {{ user.name }}
                        </div>
                        <div class="text-sm text-gray-500">
                          {{ user.email }}
                        </div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span
                      v-for="role in user.roles"
                      :key="role.id"
                      class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mr-1"
                    >
                      {{ role.name }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span
                      :class="[
                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                        user.is_active
                          ? 'bg-green-100 text-green-800'
                          : 'bg-red-100 text-red-800'
                      ]"
                    >
                      {{ user.is_active ? 'Active' : 'Inactive' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    <div class="flex items-center">
                      <div class="flex-1">
                        <div class="w-full bg-gray-200 rounded-full h-2">
                          <div
                            class="bg-blue-600 h-2 rounded-full"
                            :style="{ width: `${user.storage_percentage}%` }"
                          ></div>
                        </div>
                      </div>
                      <div class="ml-2 text-xs text-gray-500">
                        {{ formatBytes(user.storage_used) }}
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ user.last_login_at ? formatDate(user.last_login_at) : 'Never' }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <div class="flex items-center justify-end space-x-2">
                      <Link
                        :href="route('admin.users.show', user.id)"
                        class="text-blue-600 hover:text-blue-900"
                      >
                        View
                      </Link>
                      <Link
                        :href="route('admin.users.edit', user.id)"
                        class="text-blue-600 hover:text-blue-900"
                      >
                        Edit
                      </Link>
                      <button
                        @click="toggleUserStatus(user)"
                        :class="[
                          'text-sm',
                          user.is_active
                            ? 'text-red-600 hover:text-red-900'
                            : 'text-green-600 hover:text-green-900'
                        ]"
                      >
                        {{ user.is_active ? 'Deactivate' : 'Activate' }}
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <Pagination
            v-if="users.data.length > 0"
            :links="users.links"
            :meta="users.meta"
            class="px-6 py-4 border-t border-gray-200"
          />
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
  filters: {
    type: Object,
    default: () => ({}),
  },
})

const searchForm = reactive({
  search: props.filters.search || '',
  role: props.filters.role || '',
  status: props.filters.status || '',
})

let searchTimeout = null

const debounceSearch = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    search()
  }, 300)
}

const search = () => {
  router.get(route('admin.users.index'), searchForm, {
    preserveState: true,
    replace: true,
  })
}

const resetFilters = () => {
  Object.keys(searchForm).forEach(key => {
    searchForm[key] = ''
  })
  search()
}

const toggleUserStatus = (user) => {
  const action = user.is_active ? 'deactivate' : 'activate'
  if (confirm(`Are you sure you want to ${action} ${user.name}?`)) {
    router.post(route('admin.users.toggle-active', user.id))
  }
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
</script>
