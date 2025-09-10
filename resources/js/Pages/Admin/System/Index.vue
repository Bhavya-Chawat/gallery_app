<template>
  <AdminLayout>
    <Head title="System Dashboard" />

    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            System Dashboard
          </h2>
          <p class="text-sm text-gray-600 mt-1">
            Monitor system health and performance
          </p>
        </div>
        <div class="flex items-center space-x-3">
          <Link
            :href="route('admin.system.settings')"
            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
          >
            <CogIcon class="h-4 w-4 mr-2" />
            Settings
          </Link>
        </div>
      </div>
    </template>

    <div class="py-6 space-y-6">
      <!-- System Health -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <HealthCard
          title="Database"
          :status="health.database?.status || 'unknown'"
          :message="health.database?.message"
          icon="CircleStackIcon"
        />
        <HealthCard
          title="Storage"
          :status="health.storage?.status || 'unknown'"
          :message="health.storage?.message"
          icon="FolderIcon"
        />
        <HealthCard
          title="Queue"
          :status="health.queue?.status || 'unknown'"
          :message="health.queue?.message"
          icon="ClockIcon"
        />
        <HealthCard
          title="Cache"
          :status="health.cache?.status || 'unknown'"
          :message="health.cache?.message"
          icon="BoltIcon"
        />
      </div>

      <!-- Stats Overview -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <StatCard
          title="Total Users"
          :value="stats.users?.total || 0"
          :subtitle="`${stats.users?.active || 0} active`"
          icon="UsersIcon"
          color="blue"
          :href="route('admin.users.index')"
        />
        <StatCard
          title="Total Images"
          :value="formatNumber(stats.content?.images || 0)"
          :subtitle="`${formatNumber(stats.content?.published_images || 0)} published`"
          icon="PhotoIcon"
          color="green"
        />
        <StatCard
          title="Storage Used"
          :value="formatBytes(stats.storage?.total_size || 0)"
          :subtitle="`Avg: ${formatBytes(stats.storage?.average_size || 0)}`"
          icon="CircleStackIcon"
          color="purple"
        />
        <StatCard
          title="Weekly Views"
          :value="formatNumber(stats.engagement?.weekly_views || 0)"
          :subtitle="`${stats.engagement?.pending_comments || 0} pending comments`"
          icon="EyeIcon"
          color="yellow"
          :href="route('admin.moderation.comments')"
        />
      </div>

      <!-- Recent Activity -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Activity Feed -->
        <div class="bg-white shadow rounded-lg">
          <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Activity</h3>
            <div v-if="recentLogs.length > 0" class="flow-root">
              <ul role="list" class="-mb-8">
                <li
                  v-for="(log, index) in recentLogs"
                  :key="log.id"
                  class="relative pb-8"
                >
                  <span
                    v-if="index !== recentLogs.length - 1"
                    class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200"
                    aria-hidden="true"
                  ></span>
                  <div class="relative flex space-x-3">
                    <div class="flex-shrink-0">
                      <component
                        :is="getActivityIcon(log.action)"
                        class="h-6 w-6 text-gray-400"
                      />
                    </div>
                    <div class="min-w-0 flex-1">
                      <p class="text-sm text-gray-900">
                        {{ formatActivityMessage(log) }}
                      </p>
                      <p class="text-xs text-gray-500">
                        {{ formatTimeAgo(log.created_at) }}
                      </p>
                    </div>
                  </div>
                </li>
              </ul>
            </div>
            <div v-else class="text-center py-8">
              <ClockIcon class="mx-auto h-12 w-12 text-gray-400" />
              <p class="mt-2 text-sm text-gray-500">No recent activity</p>
            </div>
          </div>
        </div>

        <!-- System Information -->
        <div class="bg-white shadow rounded-lg">
          <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">System Information</h3>
            <dl class="space-y-4">
              <div class="flex justify-between">
                <dt class="text-sm font-medium text-gray-500">Laravel Version</dt>
                <dd class="text-sm text-gray-900">{{ systemInfo.laravel_version || 'Unknown' }}</dd>
              </div>
              <div class="flex justify-between">
                <dt class="text-sm font-medium text-gray-500">PHP Version</dt>
                <dd class="text-sm text-gray-900">{{ systemInfo.php_version || 'Unknown' }}</dd>
              </div>
              <div class="flex justify-between">
                <dt class="text-sm font-medium text-gray-500">Database</dt>
                <dd class="text-sm text-gray-900">PostgreSQL</dd>
              </div>
              <div class="flex justify-between">
                <dt class="text-sm font-medium text-gray-500">Queue Driver</dt>
                <dd class="text-sm text-gray-900">Redis</dd>
              </div>
              <div class="flex justify-between">
                <dt class="text-sm font-medium text-gray-500">Storage Driver</dt>
                <dd class="text-sm text-gray-900">S3/MinIO</dd>
              </div>
              <div class="flex justify-between">
                <dt class="text-sm font-medium text-gray-500">Cache Driver</dt>
                <dd class="text-sm text-gray-900">Redis</dd>
              </div>
            </dl>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="bg-white shadow rounded-lg">
        <div class="p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
          <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <QuickActionCard
              title="Clear Cache"
              description="Clear all application caches"
              icon="TrashIcon"
              @click="clearCache"
              :loading="loadingActions.clearCache"
            />
            <QuickActionCard
              title="View Logs"
              description="Browse system logs"
              icon="DocumentTextIcon"
              @click="viewLogs"
            />
            <QuickActionCard
              title="Backup Database"
              description="Create database backup"
              icon="CircleStackIcon"
              @click="backupDatabase"
              :loading="loadingActions.backup"
            />
            <QuickActionCard
              title="System Maintenance"
              description="Run maintenance tasks"
              icon="WrenchScrewdriverIcon"
              @click="showMaintenanceModal = true"
            />
          </div>
        </div>
      </div>
    </div>

    <!-- Maintenance Modal -->
    <MaintenanceModal v-model="showMaintenanceModal" />
  </AdminLayout>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import {
  CogIcon,
  CircleStackIcon,
  FolderIcon,
  ClockIcon,
  BoltIcon,
  UsersIcon,
  PhotoIcon,
  EyeIcon,
  TrashIcon,
  DocumentTextIcon,
  WrenchScrewdriverIcon,
} from '@heroicons/vue/24/outline'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import HealthCard from '@/Components/Admin/HealthCard.vue'
import StatCard from '@/Components/StatCard.vue'
import QuickActionCard from '@/Components/Admin/QuickActionCard.vue'
import MaintenanceModal from '@/Components/Admin/MaintenanceModal.vue'

const props = defineProps({
  stats: {
    type: Object,
    default: () => ({}),
  },
  health: {
    type: Object,
    default: () => ({}),
  },
  storage: {
    type: Object,
    default: () => ({}),
  },
  queue: {
    type: Object,
    default: () => ({}),
  },
  recentLogs: {
    type: Array,
    default: () => [],
  },
  systemInfo: {
    type: Object,
    default: () => ({}),
  },
})

const showMaintenanceModal = ref(false)
const loadingActions = reactive({
  clearCache: false,
  backup: false,
})

const formatNumber = (number) => {
  if (number >= 1000000) {
    return (number / 1000000).toFixed(1) + 'M'
  } else if (number >= 1000) {
    return (number / 1000).toFixed(1) + 'K'
  }
  return number.toString()
}

const formatBytes = (bytes) => {
  if (bytes === 0) return '0 B'
  const k = 1024
  const sizes = ['B', 'KB', 'MB', 'GB', 'TB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

const formatTimeAgo = (date) => {
  const seconds = Math.floor((new Date() - new Date(date)) / 1000)
  
  if (seconds < 60) return `${seconds}s ago`
  if (seconds < 3600) return `${Math.floor(seconds / 60)}m ago`
  if (seconds < 86400) return `${Math.floor(seconds / 3600)}h ago`
  return `${Math.floor(seconds / 86400)}d ago`
}

const getActivityIcon = (action) => {
  const icons = {
    created: PhotoIcon,
    updated: CogIcon,
    deleted: TrashIcon,
    login: UsersIcon,
  }
  return icons[action] || ClockIcon
}

const formatActivityMessage = (log) => {
  return `${log.user?.name || 'System'} ${log.action} ${log.auditable_type?.split('\\').pop()?.toLowerCase()}`
}

const clearCache = async () => {
  loadingActions.clearCache = true
  try {
    await router.post(route('admin.system.clear-cache'), { type: 'all' }, {
      preserveState: true,
    })
  } finally {
    loadingActions.clearCache = false
  }
}

const viewLogs = () => {
  // Navigate to logs page (to be implemented)
  console.log('View logs')
}

const backupDatabase = async () => {
  loadingActions.backup = true
  try {
    await router.post(route('admin.system.maintenance'), { task: 'backup_database' }, {
      preserveState: true,
    })
  } finally {
    loadingActions.backup = false
  }
}
</script>
