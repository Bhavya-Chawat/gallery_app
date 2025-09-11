<template>
  <AdminLayout>
    <div class="min-h-screen bg-gray-50 dark:bg-slate-900">
      <!-- Header -->
      <div class="bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl border-b border-gray-200 dark:border-slate-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                Analytics Dashboard
              </h1>
              <p class="text-gray-500 dark:text-slate-400 mt-1">
                Track site performance and user engagement metrics
              </p>
            </div>
            
            <div class="flex items-center space-x-3">
              <!-- Date Range Selector -->
              <select 
                v-model="dateRange"
                @change="fetchAnalytics"
                class="px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100 text-sm"
              >
                <option value="7">Last 7 days</option>
                <option value="30">Last 30 days</option>
                <option value="90">Last 90 days</option>
                <option value="365">Last year</option>
              </select>
              
              <button
                @click="exportReport"
                :disabled="exportLoading"
                class="bg-gradient-to-r from-blue-600 via-cyan-500 to-blue-400 text-white px-4 py-2 rounded-lg hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200"
              >
                <Download class="w-4 h-4 inline mr-2" />
                {{ exportLoading ? 'Exporting...' : 'Export Report' }}
              </button>
            </div>
          </div>
        </div>
      </div>

      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Key Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <!-- Total Views -->
          <div class="bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl rounded-xl p-6 border border-gray-200 dark:border-slate-700">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-gray-600 dark:text-slate-400">Total Views</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-1">
                  {{ analytics.totalViews?.toLocaleString() || 0 }}
                </p>
                <div class="flex items-center mt-2">
                  <TrendingUp v-if="analytics.viewsGrowth >= 0" class="w-4 h-4 text-green-500 mr-1" />
                  <TrendingDown v-else class="w-4 h-4 text-red-500 mr-1" />
                  <span 
                    :class="{
                      'text-green-600 dark:text-green-400': analytics.viewsGrowth >= 0,
                      'text-red-600 dark:text-red-400': analytics.viewsGrowth < 0
                    }"
                    class="text-sm font-medium"
                  >
                    {{ Math.abs(analytics.viewsGrowth || 0) }}%
                  </span>
                  <span class="text-sm text-gray-500 dark:text-slate-400 ml-1">vs last period</span>
                </div>
              </div>
              <div class="p-3 rounded-lg bg-blue-100 dark:bg-blue-900/30">
                <Eye class="w-6 h-6 text-blue-600 dark:text-blue-400" />
              </div>
            </div>
          </div>

          <!-- Unique Visitors -->
          <div class="bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl rounded-xl p-6 border border-gray-200 dark:border-slate-700">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-gray-600 dark:text-slate-400">Unique Visitors</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-1">
                  {{ analytics.uniqueVisitors?.toLocaleString() || 0 }}
                </p>
                <div class="flex items-center mt-2">
                  <TrendingUp v-if="analytics.visitorsGrowth >= 0" class="w-4 h-4 text-green-500 mr-1" />
                  <TrendingDown v-else class="w-4 h-4 text-red-500 mr-1" />
                  <span 
                    :class="{
                      'text-green-600 dark:text-green-400': analytics.visitorsGrowth >= 0,
                      'text-red-600 dark:text-red-400': analytics.visitorsGrowth < 0
                    }"
                    class="text-sm font-medium"
                  >
                    {{ Math.abs(analytics.visitorsGrowth || 0) }}%
                  </span>
                </div>
              </div>
              <div class="p-3 rounded-lg bg-green-100 dark:bg-green-900/30">
                <Users class="w-6 h-6 text-green-600 dark:text-green-400" />
              </div>
            </div>
          </div>

          <!-- Avg Session Duration -->
          <div class="bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl rounded-xl p-6 border border-gray-200 dark:border-slate-700">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-gray-600 dark:text-slate-400">Avg Session</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-1">
                  {{ formatDuration(analytics.avgSessionDuration || 0) }}
                </p>
                <div class="flex items-center mt-2">
                  <Clock class="w-4 h-4 text-cyan-500 mr-1" />
                  <span class="text-sm text-gray-500 dark:text-slate-400">{{ analytics.bounceRate || 0 }}% bounce rate</span>
                </div>
              </div>
              <div class="p-3 rounded-lg bg-cyan-100 dark:bg-cyan-900/30">
                <Clock class="w-6 h-6 text-cyan-600 dark:text-cyan-400" />
              </div>
            </div>
          </div>

          <!-- New Registrations -->
          <div class="bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl rounded-xl p-6 border border-gray-200 dark:border-slate-700">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-gray-600 dark:text-slate-400">New Users</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-1">
                  {{ analytics.newRegistrations || 0 }}
                </p>
                <div class="flex items-center mt-2">
                  <UserPlus class="w-4 h-4 text-purple-500 mr-1" />
                  <span class="text-sm text-gray-500 dark:text-slate-400">this period</span>
                </div>
              </div>
              <div class="p-3 rounded-lg bg-purple-100 dark:bg-purple-900/30">
                <UserPlus class="w-6 h-6 text-purple-600 dark:text-purple-400" />
              </div>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
          <!-- Traffic Chart -->
          <div class="bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl rounded-xl border border-gray-200 dark:border-slate-700 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
              Traffic Overview
            </h3>
            
            <div class="h-80">
              <canvas ref="trafficChart"></canvas>
            </div>
          </div>

          <!-- Top Pages -->
          <div class="bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl rounded-xl border border-gray-200 dark:border-slate-700 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
              Popular Pages
            </h3>
            
            <div class="space-y-4">
              <div 
                v-for="page in analytics.topPages" 
                :key="page.path"
                class="flex items-center justify-between p-3 bg-gray-50 dark:bg-slate-700/50 rounded-lg"
              >
                <div class="flex-1 min-w-0">
                  <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                    {{ page.title || page.path }}
                  </p>
                  <p class="text-xs text-gray-500 dark:text-slate-400">
                    {{ page.path }}
                  </p>
                </div>
                <div class="text-right">
                  <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                    {{ page.views.toLocaleString() }}
                  </p>
                  <p class="text-xs text-gray-500 dark:text-slate-400">views</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Device Types -->
          <div class="bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl rounded-xl border border-gray-200 dark:border-slate-700 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
              Device Types
            </h3>
            
            <div class="space-y-3">
              <div 
                v-for="device in analytics.deviceTypes" 
                :key="device.type"
                class="flex items-center justify-between"
              >
                <div class="flex items-center">
                  <Monitor v-if="device.type === 'desktop'" class="w-4 h-4 text-gray-500 dark:text-slate-400 mr-2" />
                  <Smartphone v-else-if="device.type === 'mobile'" class="w-4 h-4 text-gray-500 dark:text-slate-400 mr-2" />
                  <Tablet v-else class="w-4 h-4 text-gray-500 dark:text-slate-400 mr-2" />
                  <span class="text-sm text-gray-900 dark:text-gray-100 capitalize">{{ device.type }}</span>
                </div>
                <div class="flex items-center space-x-2">
                  <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ device.percentage }}%</span>
                  <div class="w-16 bg-gray-200 dark:bg-slate-700 rounded-full h-2">
                    <div 
                      class="bg-blue-600 h-2 rounded-full"
                      :style="{ width: `${device.percentage}%` }"
                    ></div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Top Browsers -->
          <div class="bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl rounded-xl border border-gray-200 dark:border-slate-700 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
              Browsers
            </h3>
            
            <div class="space-y-3">
              <div 
                v-for="browser in analytics.browsers" 
                :key="browser.name"
                class="flex items-center justify-between"
              >
                <span class="text-sm text-gray-900 dark:text-gray-100">{{ browser.name }}</span>
                <div class="flex items-center space-x-2">
                  <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ browser.percentage }}%</span>
                  <div class="w-16 bg-gray-200 dark:bg-slate-700 rounded-full h-2">
                    <div 
                      class="bg-cyan-600 h-2 rounded-full"
                      :style="{ width: `${browser.percentage}%` }"
                    ></div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Geographic Data -->
          <div class="bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl rounded-xl border border-gray-200 dark:border-slate-700 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
              Top Countries
            </h3>
            
            <div class="space-y-3">
              <div 
                v-for="country in analytics.countries" 
                :key="country.code"
                class="flex items-center justify-between"
              >
                <div class="flex items-center">
                  <Globe class="w-4 h-4 text-gray-500 dark:text-slate-400 mr-2" />
                  <span class="text-sm text-gray-900 dark:text-gray-100">{{ country.name }}</span>
                </div>
                <div class="flex items-center space-x-2">
                  <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ country.visitors }}</span>
                  <div class="w-16 bg-gray-200 dark:bg-slate-700 rounded-full h-2">
                    <div 
                      class="bg-green-600 h-2 rounded-full"
                      :style="{ width: `${country.percentage}%` }"
                    ></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Image Performance -->
        <div class="mt-8 bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl rounded-xl border border-gray-200 dark:border-slate-700 p-6">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-6">
            Image Performance
          </h3>
          
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="text-center">
              <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ analytics.totalUploads || 0 }}</p>
              <p class="text-sm text-gray-600 dark:text-slate-400">Images Uploaded</p>
            </div>
            <div class="text-center">
              <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ analytics.totalDownloads || 0 }}</p>
              <p class="text-sm text-gray-600 dark:text-slate-400">Downloads</p>
            </div>
            <div class="text-center">
              <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ analytics.totalLikes || 0 }}</p>
              <p class="text-sm text-gray-600 dark:text-slate-400">Total Likes</p>
            </div>
          </div>

          <!-- Most Popular Images -->
          <h4 class="text-md font-semibold text-gray-900 dark:text-gray-100 mb-4">
            Most Popular Images
          </h4>
          
          <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
            <div 
              v-for="image in analytics.popularImages" 
              :key="image.id"
              class="group relative aspect-square rounded-lg overflow-hidden cursor-pointer"
              @click="viewImageDetails(image.id)"
            >
              <img 
                :src="image.thumbnail_url" 
                :alt="image.title"
                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
              />
              <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-all duration-200">
                <div class="absolute bottom-2 left-2 right-2">
                  <div class="bg-black/70 backdrop-blur-sm text-white px-2 py-1 rounded text-xs">
                    {{ image.views }} views
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { 
  Download, Eye, Users, Clock, UserPlus, TrendingUp, TrendingDown,
  Monitor, Smartphone, Tablet, Globe
} from 'lucide-vue-next'
import axios from 'axios'

// Reactive data
const dateRange = ref('30')
const analytics = ref({
  totalViews: 0,
  uniqueVisitors: 0,
  avgSessionDuration: 0,
  newRegistrations: 0,
  viewsGrowth: 0,
  visitorsGrowth: 0,
  bounceRate: 0,
  topPages: [],
  deviceTypes: [],
  browsers: [],
  countries: [],
  popularImages: [],
  totalUploads: 0,
  totalDownloads: 0,
  totalLikes: 0
})

const exportLoading = ref(false)
const trafficChart = ref(null)

// Methods
const formatDuration = (seconds) => {
  const minutes = Math.floor(seconds / 60)
  const remainingSeconds = seconds % 60
  return `${minutes}m ${remainingSeconds}s`
}

const fetchAnalytics = async () => {
  try {
    const response = await axios.get(`/api/v1/admin/analytics?days=${dateRange.value}`)
    analytics.value = response.data.data
    
    await nextTick()
    renderTrafficChart()
  } catch (error) {
    console.error('Failed to fetch analytics:', error)
  }
}

const renderTrafficChart = () => {
  if (!trafficChart.value) return
  
  const ctx = trafficChart.value.getContext('2d')
  
  // Sample chart implementation using Chart.js
  new Chart(ctx, {
    type: 'line',
    data: {
      labels: analytics.value.chartLabels || [],
      datasets: [
        {
          label: 'Page Views',
          data: analytics.value.viewsData || [],
          borderColor: '#2563EB',
          backgroundColor: 'rgba(37, 99, 235, 0.1)',
          fill: true,
          tension: 0.4
        },
        {
          label: 'Unique Visitors',
          data: analytics.value.visitorsData || [],
          borderColor: '#06B6D4',
          backgroundColor: 'rgba(6, 182, 212, 0.1)',
          fill: true,
          tension: 0.4
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'top',
        },
      },
      scales: {
        y: {
          beginAtZero: true,
          grid: {
            color: 'rgba(148, 163, 184, 0.1)'
          }
        },
        x: {
          grid: {
            color: 'rgba(148, 163, 184, 0.1)'
          }
        }
      }
    }
  })
}

const exportReport = async () => {
  exportLoading.value = true
  
  try {
    const response = await axios.get(`/api/v1/admin/analytics/export?days=${dateRange.value}`, {
      responseType: 'blob'
    })
    
    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', `analytics-report-${dateRange.value}days.pdf`)
    document.body.appendChild(link)
    link.click()
    link.remove()
    window.URL.revokeObjectURL(url)
  } catch (error) {
    console.error('Failed to export report:', error)
  } finally {
    exportLoading.value = false
  }
}

const viewImageDetails = (imageId) => {
  // Navigate to image details or open modal
  window.open(`/gallery/image/${imageId}`, '_blank')
}

// Lifecycle
onMounted(() => {
  fetchAnalytics()
})
</script>