<template>
  <AdminLayout>
    <Head title="System Analytics" />

    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            System Analytics
          </h2>
          <p class="text-sm text-gray-600 mt-1">
            View system usage and performance metrics
          </p>
        </div>
        <select
          v-model="dateRange"
          @change="updateDateRange"
          class="px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
        >
          <option value="7">Last 7 days</option>
          <option value="30">Last 30 days</option>
          <option value="90">Last 90 days</option>
        </select>
      </div>
    </template>

    <div class="space-y-6">
      <!-- Overview Cards -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <StatCard
          title="Total Uploads"
          :value="analytics.uploads?.total || 0"
          :subtitle="`+${analytics.uploads?.this_period || 0} this period`"
          icon="PhotoIcon"
          color="blue"
        />
        <StatCard
          title="Total Users"
          :value="analytics.users?.total || 0"
          :subtitle="`${analytics.users?.active_users || 0} active`"
          icon="UsersIcon"
          color="green"
        />
        <StatCard
          title="Storage Used"
          :value="formatBytes(analytics.storage?.total_size || 0)"
          :subtitle="`${analytics.storage?.total_files || 0} files`"
          icon="CircleStackIcon"
          color="purple"
        />
        <StatCard
          title="Total Views"
          :value="formatNumber(analytics.engagement?.total_views || 0)"
          :subtitle="`${analytics.engagement?.total_comments || 0} comments`"
          icon="EyeIcon"
          color="yellow"
        />
      </div>

      <!-- Charts Section -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Upload Trend -->
        <div class="bg-white rounded-lg shadow p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Upload Trend</h3>
          <div v-if="analytics.uploads?.by_day?.length > 0" class="space-y-2">
            <div 
              v-for="day in analytics.uploads.by_day.slice(-10)" 
              :key="day.date"
              class="flex items-center justify-between text-sm"
            >
              <span class="text-gray-600">{{ formatDate(day.date) }}</span>
              <div class="flex items-center space-x-2">
                <div class="w-32 bg-gray-200 rounded-full h-2">
                  <div 
                    class="bg-blue-600 h-2 rounded-full" 
                    :style="{ width: getBarWidth(day.count, analytics.uploads.by_day) + '%' }"
                  ></div>
                </div>
                <span class="font-medium text-gray-900 w-8 text-right">{{ day.count }}</span>
              </div>
            </div>
          </div>
          <div v-else class="text-center py-8">
            <p class="text-gray-500">No upload data available</p>
          </div>
        </div>

        <!-- Quick Stats -->
        <div class="bg-white rounded-lg shadow p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Stats</h3>
          <div class="space-y-4">
            <div class="flex justify-between">
              <span class="text-gray-600">New Users ({{ dateRange }} days)</span>
              <span class="font-medium">{{ analytics.users?.new_users || 0 }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Average File Size</span>
              <span class="font-medium">{{ formatBytes(analytics.storage?.average_size || 0) }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Pending Comments</span>
              <span class="font-medium">{{ analytics.engagement?.pending_comments || 0 }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Storage per User</span>
              <span class="font-medium">
                {{ analytics.users?.total > 0 ? formatBytes((analytics.storage?.total_size || 0) / analytics.users.total) : '0 B' }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Detailed Tables -->
      <div class="bg-white rounded-lg shadow">
        <div class="p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Summary</h3>
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Metric</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Value</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Period</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200">
                <tr>
                  <td class="px-6 py-4 text-sm text-gray-900">Total Images</td>
                  <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ analytics.uploads?.total || 0 }}</td>
                  <td class="px-6 py-4 text-sm text-gray-500">All time</td>
                </tr>
                <tr>
                  <td class="px-6 py-4 text-sm text-gray-900">New Images</td>
                  <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ analytics.uploads?.this_period || 0 }}</td>
                  <td class="px-6 py-4 text-sm text-gray-500">Last {{ dateRange }} days</td>
                </tr>
                <tr>
                  <td class="px-6 py-4 text-sm text-gray-900">Total Users</td>
                  <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ analytics.users?.total || 0 }}</td>
                  <td class="px-6 py-4 text-sm text-gray-500">All time</td>
                </tr>
                <tr>
                  <td class="px-6 py-4 text-sm text-gray-900">Storage Used</td>
                  <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ formatBytes(analytics.storage?.total_size || 0) }}</td>
                  <td class="px-6 py-4 text-sm text-gray-500">All time</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import route from 'ziggy-js'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import StatCard from '@/Components/Admin/StatCard.vue'

const props = defineProps({
  analytics: { type: Object, default: () => ({}) },
})

const dateRange = ref(props.analytics.dateRange || 30)

const updateDateRange = () => {
  router.get(route('admin.system.analytics'), { range: dateRange.value }, {
    preserveState: true,
  })
}

const formatBytes = (bytes) => {
  if (bytes === 0) return '0 B'
  const k = 1024
  const sizes = ['B', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i]
}

const formatNumber = (num) => {
  if (num >= 1000000) return (num / 1000000).toFixed(1) + 'M'
  if (num >= 1000) return (num / 1000).toFixed(1) + 'K'
  return num.toString()
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString()
}

const getBarWidth = (value, data) => {
  if (!data || data.length === 0) return 0
  const max = Math.max(...data.map(d => d.count))
  return max > 0 ? (value / max) * 100 : 0
}
</script>
