<template>
  <AdminLayout>
    <Head title="Admin Dashboard" />

    <template #header>
      <div class="animate-fade-in-up">
        <h2 class="font-semibold text-xl bg-gradient-to-r from-violet-400 via-purple-400 to-cyan-400 bg-clip-text text-transparent leading-tight animate-shimmer-text">
          System Dashboard
        </h2>
        <p class="text-sm text-slate-300 mt-1">
          Monitor system health and performance
        </p>
      </div>
    </template>

    <div class="space-y-6 relative">
      <!-- Floating Particles Background -->
      <div class="fixed inset-0 pointer-events-none overflow-hidden opacity-20 z-0">
        <div 
          v-for="i in 25" 
          :key="`particle-${i}`"
          :class="[
            'absolute rounded-full',
            i <= 10 ? 'w-1 h-1' : i <= 20 ? 'w-2 h-2' : 'w-0.5 h-0.5',
            'bg-gradient-to-r from-violet-400 via-purple-400 to-cyan-400',
            `animate-float-particle-${(i % 6) + 1}`
          ]"
          :style="{
            left: `${Math.random() * 100}%`,
            top: `${Math.random() * 100}%`,
            animationDelay: `${Math.random() * 12}s`,
            animationDuration: `${8 + Math.random() * 6}s`
          }"
        ></div>
      </div>

      <!-- System Health -->
      <div class="animate-fade-in-up" style="animation-delay: 0.1s;">
        <h3 class="text-lg font-medium bg-gradient-to-r from-violet-400 to-purple-400 bg-clip-text text-transparent mb-4 animate-shimmer-text">System Health</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          <div 
            v-for="(component, index) in ['database', 'storage', 'queue', 'cache']" 
            :key="component"
            class="relative group animate-slide-in-up"
            :style="{ animationDelay: `${0.1 + index * 0.1}s` }"
          >
            <div class="absolute inset-0 bg-gradient-to-br from-violet-500/10 to-cyan-500/10 rounded-2xl blur-lg opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
            <HealthCard
              :title="component.charAt(0).toUpperCase() + component.slice(1)"
              :status="health[component]?.status || 'unknown'"
              :message="health[component]?.message"
              :icon="getHealthIcon(component)"
              class="relative z-10 bg-slate-800/50 backdrop-blur-xl border border-white/10 rounded-2xl shadow-2xl hover:border-white/20 transition-all duration-500 hover:scale-105 hover:-translate-y-2"
            />
          </div>
        </div>
      </div>

      <!-- Stats Overview -->
      <div class="animate-fade-in-up" style="animation-delay: 0.2s;">
        <h3 class="text-lg font-medium bg-gradient-to-r from-cyan-400 to-violet-400 bg-clip-text text-transparent mb-4 animate-shimmer-text">Overview</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          <div class="relative group animate-slide-in-up" style="animation-delay: 0.1s;">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-500/10 to-cyan-500/10 rounded-2xl blur-lg opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
            <StatCard
              title="Total Users"
              :value="stats.users?.total || 0"
              :subtitle="`${stats.users?.active || 0} active`"
              icon="UsersIcon"
              color="blue"
              :href="route('admin.users.index')"
              class="relative z-10 bg-slate-800/50 backdrop-blur-xl border border-white/10 rounded-2xl shadow-2xl hover:border-white/20 transition-all duration-500 hover:scale-105 hover:-translate-y-2 hover:shadow-blue-500/10"
            />
          </div>
          
          <div class="relative group animate-slide-in-up" style="animation-delay: 0.2s;">
            <div class="absolute inset-0 bg-gradient-to-br from-green-500/10 to-emerald-500/10 rounded-2xl blur-lg opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
            <StatCard
              title="Total Images"
              :value="formatNumber(stats.content?.images || 0)"
              :subtitle="`${formatNumber(stats.content?.published_images || 0)} published`"
              icon="PhotoIcon"
              color="green"
              class="relative z-10 bg-slate-800/50 backdrop-blur-xl border border-white/10 rounded-2xl shadow-2xl hover:border-white/20 transition-all duration-500 hover:scale-105 hover:-translate-y-2 hover:shadow-green-500/10"
            />
          </div>
          
          <div class="relative group animate-slide-in-up" style="animation-delay: 0.3s;">
            <div class="absolute inset-0 bg-gradient-to-br from-purple-500/10 to-violet-500/10 rounded-2xl blur-lg opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
            <StatCard
              title="Storage Used"
              :value="formatBytes(stats.storage?.total_size || 0)"
              :subtitle="`${formatNumber(stats.storage?.total_files || 0)} files`"
              icon="CircleStackIcon"
              color="purple"
              class="relative z-10 bg-slate-800/50 backdrop-blur-xl border border-white/10 rounded-2xl shadow-2xl hover:border-white/20 transition-all duration-500 hover:scale-105 hover:-translate-y-2 hover:shadow-purple-500/10"
            />
          </div>
          
          <div class="relative group animate-slide-in-up" style="animation-delay: 0.4s;">
            <div class="absolute inset-0 bg-gradient-to-br from-yellow-500/10 to-orange-500/10 rounded-2xl blur-lg opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
            <StatCard
              title="Weekly Views"
              :value="formatNumber(stats.engagement?.this_week_views || 0)"
              :subtitle="`${stats.engagement?.pending_comments || 0} pending comments`"
              icon="EyeIcon"
              color="yellow"
              :href="route('admin.moderation.comments')"
              class="relative z-10 bg-slate-800/50 backdrop-blur-xl border border-white/10 rounded-2xl shadow-2xl hover:border-white/20 transition-all duration-500 hover:scale-105 hover:-translate-y-2 hover:shadow-yellow-500/10"
            />
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="animate-fade-in-up" style="animation-delay: 0.3s;">
        <h3 class="text-lg font-medium bg-gradient-to-r from-purple-400 to-cyan-400 bg-clip-text text-transparent mb-4 animate-shimmer-text">Quick Actions</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
          <div class="relative group animate-scale-in" style="animation-delay: 0.1s;">
            <div class="absolute inset-0 bg-gradient-to-br from-red-500/10 to-orange-500/10 rounded-2xl blur-lg opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
            <QuickAction
              title="Clear Cache"
              description="Clear application cache"
              icon="TrashIcon"
              @click="clearCache"
              :loading="loading.clearCache"
              class="relative z-10 bg-slate-800/50 backdrop-blur-xl border border-white/10 rounded-2xl shadow-2xl hover:border-white/20 transition-all duration-500 hover:scale-105 hover:-translate-y-2"
            />
          </div>
          
          <div class="relative group animate-scale-in" style="animation-delay: 0.2s;">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-500/10 to-cyan-500/10 rounded-2xl blur-lg opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
            <QuickAction
              title="View Users"
              description="Manage system users"
              icon="UsersIcon"
              :href="route('admin.users.index')"
              class="relative z-10 bg-slate-800/50 backdrop-blur-xl border border-white/10 rounded-2xl shadow-2xl hover:border-white/20 transition-all duration-500 hover:scale-105 hover:-translate-y-2"
            />
          </div>
          
          <div class="relative group animate-scale-in" style="animation-delay: 0.3s;">
            <div class="absolute inset-0 bg-gradient-to-br from-green-500/10 to-emerald-500/10 rounded-2xl blur-lg opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
            <QuickAction
              title="Moderate Content"
              description="Review pending content"
              icon="ShieldCheckIcon"
              :href="route('admin.moderation.index')"
              class="relative z-10 bg-slate-800/50 backdrop-blur-xl border border-white/10 rounded-2xl shadow-2xl hover:border-white/20 transition-all duration-500 hover:scale-105 hover:-translate-y-2"
            />
          </div>
          
          <div class="relative group animate-scale-in" style="animation-delay: 0.4s;">
            <div class="absolute inset-0 bg-gradient-to-br from-violet-500/10 to-purple-500/10 rounded-2xl blur-lg opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
            <QuickAction
              title="System Settings"
              description="Configure system"
              icon="CogIcon"
              :href="route('admin.system.settings')"
              class="relative z-10 bg-slate-800/50 backdrop-blur-xl border border-white/10 rounded-2xl shadow-2xl hover:border-white/20 transition-all duration-500 hover:scale-105 hover:-translate-y-2"
            />
          </div>
        </div>
      </div>

      <!-- Recent Activity & System Info -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 animate-fade-in-up" style="animation-delay: 0.4s;">
        <!-- Activity Feed -->
        <div class="relative group">
          <div class="absolute inset-0 bg-gradient-to-br from-cyan-500/10 via-purple-500/5 to-violet-500/10 rounded-2xl blur-xl opacity-0 group-hover:opacity-100 transition-opacity duration-700"></div>
          <div class="relative z-10 bg-slate-800/50 backdrop-blur-xl rounded-2xl shadow-2xl border border-white/10 hover:border-white/20 transition-all duration-500 hover:scale-[1.02] hover:shadow-cyan-500/10">
            <div class="p-6">
              <h3 class="text-lg font-medium bg-gradient-to-r from-cyan-400 to-blue-400 bg-clip-text text-transparent mb-4 animate-shimmer-text">Recent Activity</h3>
              <div v-if="recentLogs.length > 0" class="space-y-4">
                <div 
                  v-for="(log, index) in recentLogs.slice(0, 8)" 
                  :key="log.id"
                  class="flex items-center space-x-3 text-sm p-3 bg-slate-700/30 backdrop-blur-sm border border-white/10 rounded-xl hover:border-white/20 transition-all duration-300 hover:bg-slate-700/50 animate-slide-in-right"
                  :style="{ animationDelay: `${index * 0.1}s` }"
                >
                  <div class="flex-shrink-0 w-2 h-2 bg-gradient-to-r from-violet-500 to-cyan-500 rounded-full animate-pulse"></div>
                  <div class="flex-1 min-w-0">
                    <p class="text-white truncate">{{ formatActivity(log) }}</p>
                    <p class="text-slate-400 text-xs">{{ formatTimeAgo(log.created_at) }}</p>
                  </div>
                </div>
              </div>
              <div v-else class="text-center py-8">
                <div class="relative">
                  <ClockIcon class="mx-auto h-12 w-12 text-slate-500 animate-pulse" />
                  <div class="absolute inset-0 bg-gradient-to-r from-cyan-500/20 to-violet-500/20 rounded-full blur-xl animate-ping"></div>
                </div>
                <p class="mt-2 text-sm text-slate-400">No recent activity</p>
              </div>
            </div>
          </div>
        </div>

        <!-- System Information -->
        <div class="relative group">
          <div class="absolute inset-0 bg-gradient-to-br from-violet-500/10 via-purple-500/5 to-cyan-500/10 rounded-2xl blur-xl opacity-0 group-hover:opacity-100 transition-opacity duration-700"></div>
          <div class="relative z-10 bg-slate-800/50 backdrop-blur-xl rounded-2xl shadow-2xl border border-white/10 hover:border-white/20 transition-all duration-500 hover:scale-[1.02] hover:shadow-violet-500/10">
            <div class="p-6">
              <h3 class="text-lg font-medium bg-gradient-to-r from-violet-400 to-purple-400 bg-clip-text text-transparent mb-4 animate-shimmer-text">System Information</h3>
              <dl class="space-y-3">
                <div class="flex justify-between text-sm p-2 bg-slate-700/30 backdrop-blur-sm border border-white/10 rounded-lg hover:border-white/20 transition-all duration-300 animate-fade-in-up" style="animation-delay: 0.1s;">
                  <dt class="text-slate-400">Laravel</dt>
                  <dd class="text-white font-medium">{{ systemInfo.laravel_version || 'Unknown' }}</dd>
                </div>
                <div class="flex justify-between text-sm p-2 bg-slate-700/30 backdrop-blur-sm border border-white/10 rounded-lg hover:border-white/20 transition-all duration-300 animate-fade-in-up" style="animation-delay: 0.2s;">
                  <dt class="text-slate-400">PHP</dt>
                  <dd class="text-white font-medium">{{ systemInfo.php_version || 'Unknown' }}</dd>
                </div>
                <div class="flex justify-between text-sm p-2 bg-slate-700/30 backdrop-blur-sm border border-white/10 rounded-lg hover:border-white/20 transition-all duration-300 animate-fade-in-up" style="animation-delay: 0.3s;">
                  <dt class="text-slate-400">Environment</dt>
                  <dd class="text-white font-medium capitalize">{{ systemInfo.environment || 'Unknown' }}</dd>
                </div>
                <div class="flex justify-between text-sm p-2 bg-slate-700/30 backdrop-blur-sm border border-white/10 rounded-lg hover:border-white/20 transition-all duration-300 animate-fade-in-up" style="animation-delay: 0.4s;">
                  <dt class="text-slate-400">Queue Jobs</dt>
                  <dd class="text-white font-medium">
                    <span class="inline-flex items-center px-2 py-1 bg-gradient-to-r from-violet-500/20 to-cyan-500/20 rounded-full text-xs border border-violet-500/30">
                      {{ queue?.pending || 0 }} pending
                    </span>
                  </dd>
                </div>
              </dl>
            </div>
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

const getHealthIcon = (component) => {
  const icons = {
    database: 'CircleStackIcon',
    storage: 'FolderIcon',
    queue: 'ClockIcon',
    cache: 'BoltIcon'
  }
  return icons[component] || 'CircleStackIcon'
}

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

<style scoped>
@keyframes shimmer-text {
  0% { background-position: -200% center; }
  100% { background-position: 200% center; }
}

@keyframes fade-in-up {
  0% { 
    opacity: 0; 
    transform: translateY(30px); 
  }
  100% { 
    opacity: 1; 
    transform: translateY(0); 
  }
}

@keyframes slide-in-up {
  0% {
    opacity: 0;
    transform: translateY(20px);
  }
  100% {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes slide-in-right {
  0% {
    opacity: 0;
    transform: translateX(30px);
  }
  100% {
    opacity: 1;
    transform: translateX(0);
  }
}

@keyframes scale-in {
  0% {
    opacity: 0;
    transform: scale(0.9);
  }
  100% {
    opacity: 1;
    transform: scale(1);
  }
}

@keyframes float-particle-1 {
  0%, 100% { transform: translateY(0px) translateX(0px) rotate(0deg); opacity: 0.4; }
  25% { transform: translateY(-120px) translateX(60px) rotate(90deg); opacity: 1; }
  50% { transform: translateY(-80px) translateX(-40px) rotate(180deg); opacity: 0.8; }
  75% { transform: translateY(-160px) translateX(20px) rotate(270deg); opacity: 0.9; }
}

@keyframes float-particle-2 {
  0%, 100% { transform: translateY(0px) translateX(0px) rotate(0deg); opacity: 0.5; }
  33% { transform: translateY(-100px) translateX(-50px) rotate(120deg); opacity: 0.9; }
  66% { transform: translateY(-140px) translateX(30px) rotate(240deg); opacity: 0.7; }
}

@keyframes float-particle-3 {
  0%, 100% { transform: translateY(0px) translateX(0px) rotate(0deg); opacity: 0.7; }
  50% { transform: translateY(-90px) translateX(80px) rotate(180deg); opacity: 0.4; }
}

@keyframes float-particle-4 {
  0%, 100% { transform: translateY(0px) translateX(0px) rotate(0deg); opacity: 0.4; }
  25% { transform: translateY(-130px) translateX(-60px) rotate(90deg); opacity: 0.8; }
  75% { transform: translateY(-70px) translateX(40px) rotate(270deg); opacity: 0.6; }
}

@keyframes float-particle-5 {
  0%, 100% { transform: translateY(0px) translateX(0px) rotate(0deg); opacity: 0.6; }
  40% { transform: translateY(-120px) translateX(-20px) rotate(144deg); opacity: 0.5; }
  80% { transform: translateY(-150px) translateX(-10px) rotate(288deg); opacity: 0.7; }
}

@keyframes float-particle-6 {
  0%, 100% { transform: translateY(0px) translateX(0px) rotate(0deg); opacity: 0.3; }
  30% { transform: translateY(-110px) translateX(70px) rotate(108deg); opacity: 0.8; }
  70% { transform: translateY(-60px) translateX(-30px) rotate(252deg); opacity: 0.9; }
}

.animate-shimmer-text {
  background-size: 200% auto;
  animation: shimmer-text 3s linear infinite;
}

.animate-fade-in-up {
  animation: fade-in-up 0.8s ease-out forwards;
}

.animate-slide-in-up {
  animation: slide-in-up 0.6s ease-out forwards;
}

.animate-slide-in-right {
  animation: slide-in-right 0.6s ease-out forwards;
}

.animate-scale-in {
  animation: scale-in 0.6s ease-out forwards;
}

.animate-float-particle-1 { animation: float-particle-1 12s ease-in-out infinite; }
.animate-float-particle-2 { animation: float-particle-2 15s ease-in-out infinite; }
.animate-float-particle-3 { animation: float-particle-3 10s ease-in-out infinite; }
.animate-float-particle-4 { animation: float-particle-4 14s ease-in-out infinite; }
.animate-float-particle-5 { animation: float-particle-5 16s ease-in-out infinite; }
.animate-float-particle-6 { animation: float-particle-6 11s ease-in-out infinite; }
</style>
