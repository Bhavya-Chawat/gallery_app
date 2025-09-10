<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <!-- Logo and primary navigation -->
          <div class="flex">
            <!-- Logo -->
            <div class="flex-shrink-0 flex items-center">
              <Link :href="route('welcome')" class="text-xl font-bold text-gray-900">
                {{ $page.props.app?.name || 'Gallery' }}
              </Link>
            </div>

            <!-- Primary Navigation Links -->
            <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
              <NavLink :href="route('gallery.index')" :active="route().current('gallery.index')">
                Gallery
              </NavLink>
              <NavLink :href="route('albums.index')" :active="route().current('albums.*')">
                Albums
              </NavLink>
              <NavLink :href="route('collections.index')" :active="route().current('collections.*')">
                Collections
              </NavLink>
              <NavLink :href="route('tags.index')" :active="route().current('tags.*')">
                Tags
              </NavLink>
            </div>
          </div>

          <!-- Secondary navigation -->
          <div class="hidden sm:flex sm:items-center sm:space-x-4">
            <!-- Search -->
            <div class="relative">
              <input
                v-model="searchQuery"
                @keydown.enter="performSearch"
                type="text"
                placeholder="Search images, albums..."
                class="w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
              >
              <MagnifyingGlassIcon class="absolute left-3 top-2.5 h-4 w-4 text-gray-400" />
            </div>

            <!-- User menu -->
            <div v-if="$page.props.auth.user" class="relative">
              <Dropdown align="right" width="48">
                <template #trigger>
                  <button
                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150"
                  >
                    <div class="flex items-center space-x-2">
                      <UserAvatar
                        :user="$page.props.auth.user"
                        size="sm"
                      />
                      <span>{{ $page.props.auth.user.name }}</span>
                    </div>
                    <ChevronDownIcon class="ml-2 h-4 w-4" />
                  </button>
                </template>

                <template #content>
                  <DropdownLink :href="route('dashboard')">
                    Dashboard
                  </DropdownLink>
                  <DropdownLink :href="route('my.images')">
                    My Images
                  </DropdownLink>
                  <DropdownLink :href="route('my.albums')">
                    My Albums
                  </DropdownLink>
                  <DropdownLink :href="route('upload')" v-if="canUpload">
                    Upload Images
                  </DropdownLink>
                  <div class="border-t border-gray-100"></div>
                  <DropdownLink :href="route('profile.edit')">
                    Profile
                  </DropdownLink>
                  <div class="border-t border-gray-100" v-if="isAdmin"></div>
                  <DropdownLink :href="route('admin.system.index')" v-if="isAdmin">
                    Admin Panel
                  </DropdownLink>
                  <div class="border-t border-gray-100"></div>
                  <DropdownLink :href="route('logout')" method="post" as="button">
                    Log Out
                  </DropdownLink>
                </template>
              </Dropdown>
            </div>

            <!-- Guest links -->
            <div v-else class="flex items-center space-x-4">
              <Link
                :href="route('login')"
                class="text-sm text-gray-700 underline"
              >
                Log in
              </Link>
              <Link
                v-if="canRegister"
                :href="route('register')"
                class="px-4 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700 transition"
              >
                Register
              </Link>
            </div>
          </div>

          <!-- Mobile menu button -->
          <div class="sm:hidden flex items-center">
            <button
              @click="showingNavigationDropdown = !showingNavigationDropdown"
              class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition"
            >
              <Bars3Icon v-if="!showingNavigationDropdown" class="h-6 w-6" />
              <XMarkIcon v-else class="h-6 w-6" />
            </button>
          </div>
        </div>
      </div>

      <!-- Mobile Navigation Menu -->
      <div
        :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }"
        class="sm:hidden"
      >
        <div class="pt-2 pb-3 space-y-1">
          <ResponsiveNavLink :href="route('gallery.index')" :active="route().current('gallery.index')">
            Gallery
          </ResponsiveNavLink>
          <ResponsiveNavLink :href="route('albums.index')" :active="route().current('albums.*')">
            Albums
          </ResponsiveNavLink>
          <ResponsiveNavLink :href="route('collections.index')" :active="route().current('collections.*')">
            Collections
          </ResponsiveNavLink>
          <ResponsiveNavLink :href="route('tags.index')" :active="route().current('tags.*')">
            Tags
          </ResponsiveNavLink>
        </div>

        <!-- Mobile user menu -->
        <div v-if="$page.props.auth.user" class="pt-4 pb-1 border-t border-gray-200">
          <div class="px-4">
            <div class="font-medium text-base text-gray-800">
              {{ $page.props.auth.user.name }}
            </div>
            <div class="font-medium text-sm text-gray-500">
              {{ $page.props.auth.user.email }}
            </div>
          </div>

          <div class="mt-3 space-y-1">
            <ResponsiveNavLink :href="route('dashboard')">
              Dashboard
            </ResponsiveNavLink>
            <ResponsiveNavLink :href="route('my.images')">
              My Images
            </ResponsiveNavLink>
            <ResponsiveNavLink :href="route('upload')" v-if="canUpload">
              Upload Images
            </ResponsiveNavLink>
            <ResponsiveNavLink :href="route('profile.edit')">
              Profile
            </ResponsiveNavLink>
            <ResponsiveNavLink :href="route('admin.system.index')" v-if="isAdmin">
              Admin Panel
            </ResponsiveNavLink>
            <ResponsiveNavLink :href="route('logout')" method="post" as="button">
              Log Out
            </ResponsiveNavLink>
          </div>
        </div>
      </div>
    </nav>

    <!-- Page Heading -->
    <header class="bg-white shadow" v-if="$slots.header">
      <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <slot name="header" />
      </div>
    </header>

    <!-- Page Content -->
    <main class="flex-1">
      <slot />
    </main>

    <!-- Footer -->
    <Footer />

    <!-- Notifications -->
    <NotificationList />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import {
  Bars3Icon,
  XMarkIcon,
  ChevronDownIcon,
  MagnifyingGlassIcon,
} from '@heroicons/vue/24/outline'

import Dropdown from '@/Components/Dropdown.vue'
import DropdownLink from '@/Components/DropdownLink.vue'
import NavLink from '@/Components/NavLink.vue'
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue'
import UserAvatar from '@/Components/UserAvatar.vue'
import Footer from '@/Components/Footer.vue'
import NotificationList from '@/Components/NotificationList.vue'

const page = usePage()

const showingNavigationDropdown = ref(false)
const searchQuery = ref('')

const canUpload = computed(() => {
  return page.props.auth.user && page.props.auth.user.permissions?.includes('upload_images')
})

const canRegister = computed(() => {
  return page.props.canRegister
})

const isAdmin = computed(() => {
  return page.props.auth.user?.roles?.includes('admin')
})

const performSearch = () => {
  if (searchQuery.value.trim()) {
    window.location = route('search', { q: searchQuery.value })
  }
}
</script>
