<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Admin Navigation -->
    <nav class="bg-white shadow-sm border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex">
            <!-- Logo -->
            <div class="flex-shrink-0 flex items-center">
              <Link :href="route('dashboard')" class="text-xl font-bold text-gray-900">
                {{ $page.props.app?.name || 'Gallery' }} Admin
              </Link>
            </div>

            <!-- Admin Navigation Links -->
            <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
              <NavLink :href="route('admin.system.index')" :active="route().current('admin.system.*')">
                System
              </NavLink>
              <NavLink :href="route('admin.users.index')" :active="route().current('admin.users.*')">
                Users
              </NavLink>
              <NavLink :href="route('admin.moderation.index')" :active="route().current('admin.moderation.*')">
                Moderation
                <span
                  v-if="pendingCount > 0"
                  class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800"
                >
                  {{ pendingCount }}
                </span>
              </NavLink>
            </div>
          </div>

          <div class="hidden sm:flex sm:items-center sm:space-x-4">
            <!-- Back to Gallery -->
            <Link
              :href="route('gallery.index')"
              class="text-sm text-gray-600 hover:text-gray-900"
            >
              Back to Gallery
            </Link>

            <!-- User menu -->
            <Dropdown align="right" width="48">
              <template #trigger>
                <button
                  class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition"
                >
                  <UserAvatar :user="$page.props.auth.user" size="sm" />
                  <span class="ml-2">{{ $page.props.auth.user.name }}</span>
                  <ChevronDownIcon class="ml-2 h-4 w-4" />
                </button>
              </template>

              <template #content>
                <DropdownLink :href="route('dashboard')">
                  Dashboard
                </DropdownLink>
                <DropdownLink :href="route('profile.edit')">
                  Profile
                </DropdownLink>
                <div class="border-t border-gray-100"></div>
                <DropdownLink :href="route('logout')" method="post" as="button">
                  Log Out
                </DropdownLink>
              </template>
            </Dropdown>
          </div>

          <!-- Mobile menu button -->
          <div class="sm:hidden flex items-center">
            <button
              @click="showingNavigationDropdown = !showingNavigationDropdown"
              class="p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100"
            >
              <Bars3Icon v-if="!showingNavigationDropdown" class="h-6 w-6" />
              <XMarkIcon v-else class="h-6 w-6" />
            </button>
          </div>
        </div>
      </div>

      <!-- Mobile Navigation -->
      <div
        :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }"
        class="sm:hidden"
      >
        <div class="pt-2 pb-3 space-y-1">
          <ResponsiveNavLink :href="route('admin.system.index')" :active="route().current('admin.system.*')">
            System
          </ResponsiveNavLink>
          <ResponsiveNavLink :href="route('admin.users.index')" :active="route().current('admin.users.*')">
            Users
          </ResponsiveNavLink>
          <ResponsiveNavLink :href="route('admin.moderation.index')" :active="route().current('admin.moderation.*')">
            Moderation
          </ResponsiveNavLink>
        </div>
      </div>
    </nav>

    <!-- Sidebar -->
    <div class="flex">
      <AdminSidebar class="hidden lg:flex lg:w-64 lg:flex-col lg:fixed lg:inset-y-0 lg:pt-16" />
      
      <!-- Main content -->
      <div class="flex flex-col flex-1 lg:pl-64">
        <!-- Page header -->
        <header class="bg-white shadow-sm" v-if="$slots.header">
          <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <slot name="header" />
          </div>
        </header>

        <!-- Page content -->
        <main class="flex-1 p-4 sm:p-6 lg:p-8">
          <slot />
        </main>
      </div>
    </div>

    <!-- Notifications -->
    <NotificationList />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import route from 'ziggy-js'
import {
  Bars3Icon,
  XMarkIcon,
  ChevronDownIcon,
} from '@heroicons/vue/24/outline'

import Dropdown from '@/Components/Dropdown.vue'
import DropdownLink from '@/Components/DropdownLink.vue'
import NavLink from '@/Components/NavLink.vue'
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue'
import UserAvatar from '@/Components/UserAvatar.vue'
import AdminSidebar from '@/Components/Admin/Sidebar.vue'
import NotificationList from '@/Components/NotificationList.vue'

const page = usePage()
const showingNavigationDropdown = ref(false)

const pendingCount = computed(() => {
  return page.props.pendingModerationCount || 0
})
</script>
