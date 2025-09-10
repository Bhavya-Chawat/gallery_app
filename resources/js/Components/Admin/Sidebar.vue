<template>
  <div class="bg-gray-800 overflow-y-auto">
    <div class="px-3 py-4">
      <!-- Admin menu items -->
      <nav class="space-y-1">
        <SidebarLink
          :href="route('admin.system.index')"
          :active="route().current('admin.system.index')"
          icon="ChartBarIcon"
        >
          Dashboard
        </SidebarLink>

        <!-- System -->
        <div class="py-2">
          <h3 class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">
            System
          </h3>
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

        <!-- Content Management -->
        <div class="py-2">
          <h3 class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">
            Content
          </h3>
          <SidebarLink
            :href="route('admin.users.index')"
            :active="route().current('admin.users.*')"
            icon="UsersIcon"
          >
            Users
          </SidebarLink>
          <SidebarLink
            :href="route('gallery.index')"
            :active="false"
            icon="PhotoIcon"
          >
            Images
          </SidebarLink>
          <SidebarLink
            :href="route('albums.index')"
            :active="false"
            icon="FolderIcon"
          >
            Albums
          </SidebarLink>
        </div>

        <!-- Moderation -->
        <div class="py-2">
          <h3 class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">
            Moderation
          </h3>
          <SidebarLink
            :href="route('admin.moderation.index')"
            :active="route().current('admin.moderation.index')"
            icon="ShieldCheckIcon"
          >
            Overview
          </SidebarLink>
          <SidebarLink
            :href="route('admin.moderation.comments')"
            :active="route().current('admin.moderation.comments')"
            icon="ChatBubbleLeftRightIcon"
            :badge="pendingCommentsCount"
          >
            Comments
          </SidebarLink>
          <SidebarLink
            :href="route('admin.moderation.reports')"
            :active="route().current('admin.moderation.reports')"
            icon="FlagIcon"
          >
            Reports
          </SidebarLink>
        </div>

        <!-- Tools -->
        <div class="py-2">
          <h3 class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">
            Tools
          </h3>
          <button
            @click="showMaintenanceModal = true"
            class="w-full text-left group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-300 hover:bg-gray-700 hover:text-white"
          >
            <WrenchScrewdriverIcon class="mr-3 h-5 w-5" />
            Maintenance
          </button>
          <SidebarLink
            href="#"
            :active="false"
            icon="DocumentArrowDownIcon"
            @click="exportData"
          >
            Export Data
          </SidebarLink>
        </div>

        <!-- Quick Actions -->
        <div class="py-2 border-t border-gray-700">
          <SidebarLink
            :href="route('dashboard')"
            :active="false"
            icon="HomeIcon"
          >
            Back to Gallery
          </SidebarLink>
        </div>
      </nav>
    </div>

    <!-- System Status -->
    <div class="px-3 py-4 border-t border-gray-700">
      <SystemStatus />
    </div>

    <!-- Maintenance Modal -->
    <MaintenanceModal v-model="showMaintenanceModal" />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import route from 'ziggy-js'

import SidebarLink from './SidebarLink.vue'
import SystemStatus from './SystemStatus.vue'
import MaintenanceModal from './MaintenanceModal.vue'

const page = usePage()
const showMaintenanceModal = ref(false)

const pendingCommentsCount = computed(() => {
  return page.props.pendingModerationCount || 0
})

const exportData = () => {
  // Implement export functionality
  console.log('Export data')
}
</script>
