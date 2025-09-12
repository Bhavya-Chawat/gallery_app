<template>
  <AdminLayout>
    <Head title="Admin Dashboard" />

    <template #header>
      <div>
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          System Dashboard
        </h2>
        <p class="text-sm text-gray-600 mt-1">
          Monitor system health and performance
        </p>
      </div>
    </template>

    <div class="space-y-6">
      <!-- System Health -->
      <div>
        <h3 class="text-lg font-medium text-gray-900 mb-4">System Health</h3>
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
      </div>

      <!-- Stats Overview -->
      <div>
        <h3 class="text-lg font-medium text-gray-900 mb-4">Overview</h3>
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
            :subtitle="`${formatNumber(stats.storage?.total_files || 0)} files`"
            icon="CircleStackIcon"
            color="purple"
          />
          <StatCard
            title="Weekly Views"
            :value="formatNumber(stats.engagement?.this_week_views || 0)"
            :subtitle="`${stats.engagement?.pending_comments || 0} pending comments`"
            icon="EyeIcon"
            color="yellow"
            :href="route('admin.moderation.comments')"
          />
        </div>
      </div>

      <!-- Quick Actions -->
      <div>
        <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
          <QuickAction
            title="Clear Cache"
            description="Clear application cache"
            icon="TrashIcon"
            @click="clearCache"
            :loading="loading.clearCache"
          />
          <QuickAction
            title="View Users"
            description="Manage system users"
            icon="UsersIcon"
            :href="route('admin.users.index')"
          />
          <QuickAction
            title="Moderate Content"
            description="Review pending content"
            icon="ShieldCheckIcon"
            :href="route('admin.moderation.index')"
          />
          <QuickAction
            title="System Settings"
            description="Configure system"
            icon="CogIcon"
            :href="route('admin.system.settings')"
          />
        </div>
      </div>

      <!-- Recent Activity & System Info -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Activity Feed -->
        <div class="bg-white rounded-lg shadow">
          <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Activity</h3>
            <div v-if="recentLogs.length > 0" class="space-y-4">
              <div 
                v-for="log in recentLogs.slice(0, 8)" 
                :key="log.id"
                class="flex items-center space-x-3 text-sm"
              >
                <div class="flex-shrink-0 w-2 h-2 bg-blue-600 rounded-full"></div>
                <div class="flex-1 min-w-0">
                  <p class="text-gray-900 truncate">{{ formatActivity(log) }}</p>
                  <p class="text-gray-500 text-xs">{{ formatTimeAgo(log.created_at) }}</p>
                </div>
              </div>
            </div>
            <div v-else class="text-center py-8">
              <ClockIcon class="mx-auto h-12 w-12 text-gray-400" />
              <p class="mt-2 text-sm text-gray-500">No recent activity</p>
            </div>
          </div>
        </div>

        <!-- System Information -->
        <div class="bg-white rounded-lg shadow">
          <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">System Information</h3>
            <dl class="space-y-3">
              <div class="flex justify-between text-sm">
                <dt class="text-gray-500">Laravel</dt>
                <dd class="text-gray-900">{{ systemInfo.laravel_version || 'Unknown' }}</dd>
              </div>
              <div class="flex justify-between text-sm">
                <dt class="text-gray-500">PHP</dt>
                <dd class="text-gray-900">{{ systemInfo.php_version || 'Unknown' }}</dd>
              </div>
              <div class="flex justify-between text-sm">
                <dt class="text-gray-500">Environment</dt>
                <dd class="text-gray-900 capitalize">{{ systemInfo.environment || 'Unknown' }}</dd>
              </div>
              <div class="flex justify-between text-sm">
                <dt class="text-gray-500">Queue Jobs</dt>
                <dd class="text-gray-900">{{ queue?.pending || 0 }} pending</dd>
              </div>
            </dl>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import route from 'ziggy-js'
import { ClockIcon } from '@heroicons/vue/24/outline'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import HealthCard from '@/Components/Admin/HealthCard.vue'
import StatCard from '@/Components/Admin/StatCard.vue'
import QuickAction from '@/Components/Admin/QuickAction.vue'

const props = defineProps({
  stats: { type: Object, default: () => ({}) },
  health: { type: Object, default: () => ({}) },
  storage: { type: Object, default: () => ({}) },
  queue: { type: Object, default: () => ({}) },
  recentLogs: { type: Array, default: () => [] },
  systemInfo: { type: Object, default: () => ({}) },
})

const loading = reactive({
  clearCache: false,
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

const formatTimeAgo = (date) => {
  const seconds = Math.floor((new Date() - new Date(date)) / 1000)
  if (seconds < 60) return `${seconds}s ago`
  if (seconds < 3600) return `${Math.floor(seconds / 60)}m ago`
  if (seconds < 86400) return `${Math.floor(seconds / 3600)}h ago`
  return `${Math.floor(seconds / 86400)}d ago`
}

const formatActivity = (log) => {
  const user = log.user?.name || 'System'
  const type = log.auditable_type?.split('\\').pop()?.toLowerCase() || 'item'
  return `${user} ${log.action} ${type}`
}

const clearCache = async () => {
  loading.clearCache = true
  try {
    await router.post(route('admin.system.clear-cache'))
  } finally {
    loading.clearCache = false
  }
}
</script>
