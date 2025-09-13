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
/* <template>
  <AdminLayout>
    <Head title="System Analytics" />

    <template #header>
      <div class="flex items-center justify-between animate-fadeInUp">
        <div class="relative">
          <!-- Floating particles around header -->
          <div class="absolute -top-2 -left-2 w-1 h-1 bg-violet-400 rounded-full animate-pulse opacity-60"></div>
          <div class="absolute -bottom-1 -right-1 w-0.5 h-0.5 bg-cyan-400 rounded-full animate-ping opacity-40"></div>
          
          <h2 class="font-bold text-2xl bg-gradient-to-r from-violet-400 via-purple-400 to-cyan-400 bg-clip-text text-transparent animate-shimmer">
            System Analytics
          </h2>
          <p class="text-sm text-slate-300 mt-1">
            View system usage and performance metrics
          </p>
        </div>
        
        <div class="relative group">
          <!-- Selector glow effect -->
          <div class="absolute -inset-0.5 bg-gradient-to-r from-violet-500/0 via-cyan-500/0 to-purple-500/0 rounded-xl opacity-0 group-hover:opacity-30 group-focus-within:opacity-50 transition-opacity duration-500 blur-sm"></div>
          
          <select
            v-model="dateRange"
            @change="updateDateRange"
            class="relative px-4 py-2 bg-slate-800/60 backdrop-blur-xl border border-slate-600/50 rounded-xl 
                   text-slate-100 focus:bg-slate-700/60 focus:border-violet-500/50 focus:ring-4 focus:ring-violet-500/20 
                   hover:border-slate-500/50 transition-all duration-300 outline-none shadow-lg"
          >
            <option value="7">Last 7 days</option>
            <option value="30">Last 30 days</option>
            <option value="90">Last 90 days</option>
          </select>
        </div>
      </div>
    </template>

    <div class="space-y-6">
      <!-- Enhanced Overview Cards -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 animate-fadeInUp" style="animation-delay: 0.1s;">
        <StatCard
          title="Total Uploads"
          :value="analytics.uploads?.total || 0"
          :subtitle="`+${analytics.uploads?.this_period || 0} this period`"
          icon="PhotoIcon"
          color="blue"
          class="hover:scale-105 transition-transform duration-300"
        />
        <StatCard
          title="Total Users"
          :value="analytics.users?.total || 0"
          :subtitle="`${analytics.users?.active_users || 0} active`"
          icon="UsersIcon"
          color="green"
          class="hover:scale-105 transition-transform duration-300"
        />
        <StatCard
          title="Storage Used"
          :value="formatBytes(analytics.storage?.total_size || 0)"
          :subtitle="`${analytics.storage?.total_files || 0} files`"
          icon="CircleStackIcon"
          color="purple"
          class="hover:scale-105 transition-transform duration-300"
        />
        <StatCard
          title="Total Views"
          :value="formatNumber(analytics.engagement?.total_views || 0)"
          :subtitle="`${analytics.engagement?.total_comments || 0} comments`"
          icon="EyeIcon"
          color="yellow"
          class="hover:scale-105 transition-transform duration-300"
        />
      </div>

      <!-- Enhanced Charts Section -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 animate-fadeInUp" style="animation-delay: 0.2s;">
        <!-- Enhanced Upload Trend -->
        <div class="relative bg-slate-900/60 backdrop-blur-xl rounded-2xl shadow-2xl shadow-slate-900/20 p-6 border border-slate-700/50 group">
          <!-- Card glow effect -->
          <div class="absolute inset-0 bg-gradient-to-r from-violet-500/5 via-transparent to-cyan-500/5 rounded-2xl pointer-events-none"></div>
          
          <!-- Floating particles -->
          <div class="absolute top-3 left-4 w-0.5 h-0.5 bg-violet-400 rounded-full animate-pulse opacity-40"></div>
          <div class="absolute bottom-4 right-6 w-1 h-1 bg-cyan-400 rounded-full animate-bounce opacity-30"></div>
          
          <div class="relative">
            <h3 class="text-lg font-semibold text-slate-200 mb-4 group-hover:text-white transition-colors duration-300">Upload Trend</h3>
            <div v-if="analytics.uploads?.by_day?.length > 0" class="space-y-3">
              <div 
                v-for="(day, index) in analytics.uploads.by_day.slice(-10)" 
                :key="day.date"
                class="group/bar flex items-center justify-between text-sm animate-slideInUp"
                :style="`animation-delay: ${index * 0.05}s`"
              >
                <span class="text-slate-400 group-hover/bar:text-slate-300 transition-colors duration-300">{{ formatDate(day.date) }}</span>
                <div class="flex items-center space-x-3">
                  <div class="relative w-32 bg-slate-700/50 rounded-full h-2.5 overflow-hidden">
                    <!-- Enhanced progress bar -->
                    <div 
                      class="bg-gradient-to-r from-violet-500 to-cyan-500 h-2.5 rounded-full transition-all duration-700 ease-out group-hover/bar:from-violet-400 group-hover/bar:to-cyan-400" 
                      :style="{ width: getBarWidth(day.count, analytics.uploads.by_day) + '%' }"
                    ></div>
                    <!-- Progress bar glow -->
                    <div class="absolute inset-0 bg-gradient-to-r from-violet-500/30 to-cyan-500/30 rounded-full opacity-0 group-hover/bar:opacity-50 transition-opacity duration-300"></div>
                  </div>
                  <span class="font-medium text-slate-200 w-8 text-right group-hover/bar:text-white transition-colors duration-300">{{ day.count }}</span>
                </div>
              </div>
            </div>
            <div v-else class="text-center py-12 animate-fadeInUp">
              <div class="relative inline-block">
                <!-- Floating particles around empty state -->
                <div class="absolute -top-1 -left-1 w-0.5 h-0.5 bg-violet-400 rounded-full animate-pulse opacity-60"></div>
                <div class="absolute -bottom-1 -right-1 w-1 h-1 bg-cyan-400 rounded-full animate-ping opacity-40"></div>
                <p class="text-slate-400">No upload data available</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Enhanced Quick Stats -->
        <div class="relative bg-slate-900/60 backdrop-blur-xl rounded-2xl shadow-2xl shadow-slate-900/20 p-6 border border-slate-700/50 group">
          <!-- Card glow effect -->
          <div class="absolute inset-0 bg-gradient-to-r from-cyan-500/5 via-transparent to-purple-500/5 rounded-2xl pointer-events-none"></div>
          
          <!-- Floating particles -->
          <div class="absolute top-2 right-4 w-1 h-1 bg-purple-400 rounded-full animate-pulse opacity-40"></div>
          <div class="absolute bottom-3 left-6 w-0.5 h-0.5 bg-cyan-400 rounded-full animate-bounce opacity-30"></div>
          
          <div class="relative">
            <h3 class="text-lg font-semibold text-slate-200 mb-4 group-hover:text-white transition-colors duration-300">Quick Stats</h3>
            <div class="space-y-4">
              <div class="group/stat flex justify-between p-3 rounded-xl hover:bg-slate-800/30 transition-all duration-300">
                <span class="text-slate-400 group-hover/stat:text-slate-300 transition-colors duration-300">New Users ({{ dateRange }} days)</span>
                <span class="font-semibold text-slate-200 group-hover/stat:text-white transition-colors duration-300">{{ analytics.users?.new_users || 0 }}</span>
              </div>
              <div class="group/stat flex justify-between p-3 rounded-xl hover:bg-slate-800/30 transition-all duration-300">
                <span class="text-slate-400 group-hover/stat:text-slate-300 transition-colors duration-300">Average File Size</span>
                <span class="font-semibold text-slate-200 group-hover/stat:text-white transition-colors duration-300">{{ formatBytes(analytics.storage?.average_size || 0) }}</span>
              </div>
              <div class="group/stat flex justify-between p-3 rounded-xl hover:bg-slate-800/30 transition-all duration-300">
                <span class="text-slate-400 group-hover/stat:text-slate-300 transition-colors duration-300">Pending Comments</span>
                <span class="font-semibold text-slate-200 group-hover/stat:text-white transition-colors duration-300">{{ analytics.engagement?.pending_comments || 0 }}</span>
              </div>
              <div class="group/stat flex justify-between p-3 rounded-xl hover:bg-slate-800/30 transition-all duration-300">
                <span class="text-slate-400 group-hover/stat:text-slate-300 transition-colors duration-300">Storage per User</span>
                <span class="font-semibold text-slate-200 group-hover/stat:text-white transition-colors duration-300">
                  {{ analytics.users?.total > 0 ? formatBytes((analytics.storage?.total_size || 0) / analytics.users.total) : '0 B' }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Enhanced Detailed Table -->
      <div class="relative bg-slate-900/60 backdrop-blur-xl rounded-2xl shadow-2xl shadow-slate-900/20 border border-slate-700/50 overflow-hidden animate-fadeInUp" style="animation-delay: 0.3s;">
        <!-- Table container glow -->
        <div class="absolute inset-0 bg-gradient-to-r from-violet-500/5 via-transparent to-cyan-500/5 pointer-events-none"></div>
        
        <div class="relative p-6">
          <!-- Floating particles around title -->
          <div class="absolute top-4 left-8 w-0.5 h-0.5 bg-violet-400 rounded-full animate-pulse opacity-60"></div>
          <div class="absolute top-6 right-12 w-1 h-1 bg-cyan-400 rounded-full animate-bounce opacity-40"></div>
          
          <h3 class="text-lg font-semibold text-slate-200 mb-6">Summary</h3>
          <div class="overflow-x-auto">
            <table class="min-w-full">
              <thead>
                <tr class="border-b border-slate-700/50">
                  <th class="px-6 py-4 text-left text-xs font-semibold text-slate-300 uppercase tracking-wider">Metric</th>
                  <th class="px-6 py-4 text-left text-xs font-semibold text-slate-300 uppercase tracking-wider">Value</th>
                  <th class="px-6 py-4 text-left text-xs font-semibold text-slate-300 uppercase tracking-wider">Period</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-700/30">
                <tr class="group hover:bg-slate-800/30 transition-all duration-300 animate-slideInUp" style="animation-delay: 0.1s;">
                  <td class="px-6 py-4 text-sm text-slate-300 group-hover:text-slate-200 transition-colors duration-300">Total Images</td>
                  <td class="px-6 py-4 text-sm font-semibold text-slate-200 group-hover:text-white transition-colors duration-300">{{ analytics.uploads?.total || 0 }}</td>
                  <td class="px-6 py-4 text-sm text-slate-400 group-hover:text-slate-300 transition-colors duration-300">All time</td>
                </tr>
                <tr class="group hover:bg-slate-800/30 transition-all duration-300 animate-slideInUp" style="animation-delay: 0.15s;">
                  <td class="px-6 py-4 text-sm text-slate-300 group-hover:text-slate-200 transition-colors duration-300">New Images</td>
                  <td class="px-6 py-4 text-sm font-semibold text-slate-200 group-hover:text-white transition-colors duration-300">{{ analytics.uploads?.this_period || 0 }}</td>
                  <td class="px-6 py-4 text-sm text-slate-400 group-hover:text-slate-300 transition-colors duration-300">Last {{ dateRange }} days</td>
                </tr>
                <tr class="group hover:bg-slate-800/30 transition-all duration-300 animate-slideInUp" style="animation-delay: 0.2s;">
                  <td class="px-6 py-4 text-sm text-slate-300 group-hover:text-slate-200 transition-colors duration-300">Total Users</td>
                  <td class="px-6 py-4 text-sm font-semibold text-slate-200 group-hover:text-white transition-colors duration-300">{{ analytics.users?.total || 0 }}</td>
                  <td class="px-6 py-4 text-sm text-slate-400 group-hover:text-slate-300 transition-colors duration-300">All time</td>
                </tr>
                <tr class="group hover:bg-slate-800/30 transition-all duration-300 animate-slideInUp" style="animation-delay: 0.25s;">
                  <td class="px-6 py-4 text-sm text-slate-300 group-hover:text-slate-200 transition-colors duration-300">Storage Used</td>
                  <td class="px-6 py-4 text-sm font-semibold text-slate-200 group-hover:text-white transition-colors duration-300">{{ formatBytes(analytics.storage?.total_size || 0) }}</td>
                  <td class="px-6 py-4 text-sm text-slate-400 group-hover:text-slate-300 transition-colors duration-300">All time</td>
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

<style scoped>
@keyframes shimmer {
  0% {
    transform: translateX(-100%) skewX(-12deg);
  }
  100% {
    transform: translateX(200%) skewX(-12deg);
  }
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes slideInUp {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-shimmer {
  animation: shimmer 2s ease-in-out;
}

.animate-fadeInUp {
  animation: fadeInUp 0.6s ease-out;
}

.animate-slideInUp {
  animation: slideInUp 0.4s ease-out;
}

/* Custom scrollbar for table */
.overflow-x-auto::-webkit-scrollbar {
  height: 4px;
}

.overflow-x-auto::-webkit-scrollbar-track {
  background: rgba(15, 23, 42, 0.1);
}

.overflow-x-auto::-webkit-scrollbar-thumb {
  background: rgba(139, 92, 246, 0.3);
  border-radius: 2px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
  background: rgba(139, 92, 246, 0.5);
}
</style>