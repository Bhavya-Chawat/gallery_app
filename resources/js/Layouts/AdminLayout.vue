<template>
  <div class="min-h-screen bg-gray-100">
    <!-- Sidebar -->
    <div class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg">
      <div class="flex h-16 items-center justify-center border-b border-gray-200">
        <h1 class="text-xl font-bold text-gray-900">Admin Panel</h1>
      </div>
      
      <nav class="mt-8 px-4">
        <div class="space-y-2">
          <SidebarLink 
            :href="route('admin.system.index')" 
            :active="route().current('admin.system.index')"
            icon="ChartBarIcon"
          >
            Dashboard
          </SidebarLink>
          
          <SidebarLink 
            :href="route('admin.users.index')" 
            :active="route().current('admin.users.*')"
            icon="UsersIcon"
          >
            Users
          </SidebarLink>
          
          <SidebarLink 
            :href="route('admin.moderation.index')" 
            :active="route().current('admin.moderation.*')"
            icon="ShieldCheckIcon"
          >
            Moderation
          </SidebarLink>
          
          <SidebarLink 
            :href="route('admin.system.analytics')" 
            :active="route().current('admin.system.analytics')"
            icon="ChartPieIcon"
          >
            Analytics
          </SidebarLink>
          
          <SidebarLink 
            :href="route('admin.system.settings')" 
            :active="route().current('admin.system.settings')"
            icon="CogIcon"
          >
            Settings
          </SidebarLink>
        </div>
      </nav>
    </div>

    <!-- Main content -->
    <div class="pl-64">
      <!-- Top bar -->
      <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="px-6 py-4">
          <div class="flex items-center justify-between">
            <div v-if="$slots.header">
              <slot name="header" />
            </div>
            
            <div class="flex items-center space-x-4">
              <Link :href="route('dashboard')" class="text-sm text-gray-600 hover:text-gray-900">
                ← Back to Site
              </Link>
              
              <Dropdown align="right" width="48">
                <template #trigger>
                  <button class="flex items-center text-sm font-medium text-gray-700 hover:text-gray-900">
                    {{ $page.props.auth.user.name }}
                    <ChevronDownIcon class="ml-2 h-4 w-4" />
                  </button>
                </template>
                
                <template #content>
                  <DropdownLink :href="route('profile.edit')">Profile</DropdownLink>
                  <DropdownLink :href="route('logout')" method="post" as="button">
                    Log Out
                  </DropdownLink>
                </template>
              </Dropdown>
            </div>
          </div>
        </div>
      </header>

      <!-- Page content -->
      <main class="p-6">
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import route from 'ziggy-js'
import {
  ChartBarIcon,
  UsersIcon,
  ShieldCheckIcon,
  ChartPieIcon,
  CogIcon,
  ChevronDownIcon,
} from '@heroicons/vue/24/outline'

import SidebarLink from '@/Components/Admin/SidebarLink.vue'
import Dropdown from '@/Components/Dropdown.vue'
import DropdownLink from '@/Components/DropdownLink.vue'
</script>
