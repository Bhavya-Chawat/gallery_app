
<template>
  <AdminLayout>
    <Head title="System Settings" />

    <template #header>
      <div class="animate-fade-in-up">
        <h2 class="font-semibold text-xl bg-gradient-to-r from-violet-400 via-purple-400 to-cyan-400 bg-clip-text text-transparent leading-tight animate-shimmer-text">
          System Settings
        </h2>
        <p class="text-sm text-slate-300 mt-1">
          Configure system preferences and limits
        </p>
      </div>
    </template>

    <div class="space-y-6 relative">
      <!-- Floating Particles Background -->
      <div class="fixed inset-0 pointer-events-none overflow-hidden opacity-15 z-0">
        <div 
          v-for="i in 12" 
          :key="`particle-${i}`"
          :class="[
            'absolute w-1 h-1 rounded-full',
            'bg-gradient-to-r from-violet-400 to-cyan-400',
            `animate-float-particle-${(i % 4) + 1}`
          ]"
          :style="{
            left: `${Math.random() * 100}%`,
            top: `${Math.random() * 100}%`,
            animationDelay: `${Math.random() * 8}s`,
            animationDuration: `${10 + Math.random() * 4}s`
          }"
        ></div>
      </div>

      <!-- Gallery Settings -->
      <div class="relative group animate-fade-in-up" style="animation-delay: 0.1s;">
        <div class="absolute inset-0 bg-gradient-to-br from-violet-500/10 via-purple-500/5 to-cyan-500/10 rounded-2xl blur-xl opacity-0 group-hover:opacity-100 transition-opacity duration-700"></div>
        <div class="relative z-10 bg-slate-800/50 backdrop-blur-xl rounded-2xl shadow-2xl border border-white/10 hover:border-white/20 transition-all duration-500 hover:scale-[1.01] hover:shadow-violet-500/10 p-6">
          <h3 class="text-lg font-medium bg-gradient-to-r from-violet-400 to-purple-400 bg-clip-text text-transparent mb-4 animate-shimmer-text">Gallery Settings</h3>
          <form @submit.prevent="updateSettings">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="group">
                <label class="block text-sm font-medium text-slate-300 mb-1 group-hover:text-white transition-colors duration-300">
                  Max Upload Size (MB)
                </label>
                <div class="relative">
                  <input
                    v-model="settingsForm.max_upload_size"
                    type="number"
                    min="1"
                    max="100"
                    class="w-full px-3 py-2 bg-slate-700/50 backdrop-blur-sm border border-white/20 rounded-xl text-white placeholder-slate-400 focus:ring-2 focus:ring-violet-500/50 focus:border-violet-500/50 transition-all duration-300 hover:bg-slate-700/70 focus:bg-slate-700/70 focus:scale-105"
                  />
                  <div class="absolute inset-0 bg-gradient-to-r from-violet-500/10 to-purple-500/10 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
                </div>
              </div>
              
              <div class="group">
                <label class="block text-sm font-medium text-slate-300 mb-1 group-hover:text-white transition-colors duration-300">
                  Allowed File Types
                </label>
                <div class="relative">
                  <input
                    v-model="settingsForm.allowed_mimes"
                    type="text"
                    placeholder="jpg,png,webp,avif"
                    class="w-full px-3 py-2 bg-slate-700/50 backdrop-blur-sm border border-white/20 rounded-xl text-white placeholder-slate-400 focus:ring-2 focus:ring-violet-500/50 focus:border-violet-500/50 transition-all duration-300 hover:bg-slate-700/70 focus:bg-slate-700/70 focus:scale-105"
                  />
                  <div class="absolute inset-0 bg-gradient-to-r from-cyan-500/10 to-violet-500/10 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
                </div>
              </div>
            </div>

            <div class="mt-6 space-y-4">
              <label class="flex items-center p-3 bg-slate-700/30 backdrop-blur-sm border border-white/10 rounded-xl hover:border-white/20 transition-all duration-300 hover:bg-slate-700/50 group cursor-pointer">
                <div class="relative">
                  <input
                    v-model="settingsForm.enable_registration"
                    type="checkbox"
                    class="h-4 w-4 text-violet-600 focus:ring-violet-500/50 border-white/30 rounded bg-slate-600/50 backdrop-blur-sm transition-all duration-300"
                  />
                  <div class="absolute -inset-1 bg-gradient-to-r from-violet-500/20 to-purple-500/20 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-300 blur-sm"></div>
                </div>
                <span class="ml-2 text-sm text-slate-300 group-hover:text-white transition-colors duration-300">
                  Enable user registration
                </span>
              </label>

              <label class="flex items-center p-3 bg-slate-700/30 backdrop-blur-sm border border-white/10 rounded-xl hover:border-white/20 transition-all duration-300 hover:bg-slate-700/50 group cursor-pointer">
                <div class="relative">
                  <input
                    v-model="settingsForm.enable_comments"
                    type="checkbox"
                    class="h-4 w-4 text-violet-600 focus:ring-violet-500/50 border-white/30 rounded bg-slate-600/50 backdrop-blur-sm transition-all duration-300"
                  />
                  <div class="absolute -inset-1 bg-gradient-to-r from-purple-500/20 to-cyan-500/20 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-300 blur-sm"></div>
                </div>
                <span class="ml-2 text-sm text-slate-300 group-hover:text-white transition-colors duration-300">
                  Enable comments
                </span>
              </label>

              <label class="flex items-center p-3 bg-slate-700/30 backdrop-blur-sm border border-white/10 rounded-xl hover:border-white/20 transition-all duration-300 hover:bg-slate-700/50 group cursor-pointer">
                <div class="relative">
                  <input
                    v-model="settingsForm.comment_moderation"
                    type="checkbox"
                    class="h-4 w-4 text-violet-600 focus:ring-violet-500/50 border-white/30 rounded bg-slate-600/50 backdrop-blur-sm transition-all duration-300"
                  />
                  <div class="absolute -inset-1 bg-gradient-to-r from-cyan-500/20 to-violet-500/20 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-300 blur-sm"></div>
                </div>
                <span class="ml-2 text-sm text-slate-300 group-hover:text-white transition-colors duration-300">
                  Enable comment moderation
                </span>
              </label>
            </div>

            <div class="mt-6">
              <button
                type="submit"
                :disabled="settingsForm.processing"
                class="relative inline-flex items-center px-4 py-2 bg-gradient-to-r from-violet-600 via-purple-600 to-cyan-600 border border-white/20 rounded-xl font-semibold text-xs text-white uppercase tracking-widest hover:from-violet-500 hover:via-purple-500 hover:to-cyan-500 disabled:opacity-50 transition-all duration-500 hover:scale-105 hover:shadow-2xl hover:shadow-violet-500/25 group overflow-hidden backdrop-blur-xl"
              >
                <div class="absolute inset-0 bg-gradient-to-r from-violet-400/20 via-purple-400/20 to-cyan-400/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <span class="relative z-10">{{ settingsForm.processing ? 'Saving...' : 'Save Settings' }}</span>
                <div class="absolute -inset-1 bg-gradient-to-r from-violet-600 via-purple-600 to-cyan-600 rounded-xl blur opacity-30 group-hover:opacity-70 transition-opacity duration-300 -z-10"></div>
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Cache Management -->
      <div class="relative group animate-fade-in-up" style="animation-delay: 0.2s;">
        <div class="absolute inset-0 bg-gradient-to-br from-cyan-500/10 via-purple-500/5 to-violet-500/10 rounded-2xl blur-xl opacity-0 group-hover:opacity-100 transition-opacity duration-700"></div>
        <div class="relative z-10 bg-slate-800/50 backdrop-blur-xl rounded-2xl shadow-2xl border border-white/10 hover:border-white/20 transition-all duration-500 hover:scale-[1.01] hover:shadow-cyan-500/10 p-6">
          <h3 class="text-lg font-medium bg-gradient-to-r from-cyan-400 to-violet-400 bg-clip-text text-transparent mb-4 animate-shimmer-text">Cache Management</h3>
          <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <button
              @click="clearCache('all')"
              :disabled="loading.clearCache"
              class="relative p-4 bg-slate-700/30 backdrop-blur-sm border border-white/10 rounded-xl hover:border-white/20 hover:bg-slate-700/50 disabled:opacity-50 transition-all duration-300 hover:scale-105 group overflow-hidden animate-slide-in-up"
              style="animation-delay: 0s;"
            >
              <div class="absolute inset-0 bg-gradient-to-br from-violet-500/10 to-purple-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
              <div class="relative z-10">
                <div class="text-sm font-medium text-white">Clear All Cache</div>
                <div class="text-xs text-slate-400 mt-1 group-hover:text-slate-300 transition-colors duration-300">
                  {{ loading.clearCache ? 'Clearing...' : 'Application cache' }}
                </div>
              </div>
              <div class="absolute -inset-1 bg-gradient-to-r from-violet-500/20 to-purple-500/20 rounded-xl blur opacity-0 group-hover:opacity-50 transition-opacity duration-300 -z-10"></div>
            </button>

            <button
              @click="clearCache('config')"
              class="relative p-4 bg-slate-700/30 backdrop-blur-sm border border-white/10 rounded-xl hover:border-white/20 hover:bg-slate-700/50 transition-all duration-300 hover:scale-105 group overflow-hidden animate-slide-in-up"
              style="animation-delay: 0.1s;"
            >
              <div class="absolute inset-0 bg-gradient-to-br from-purple-500/10 to-cyan-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
              <div class="relative z-10">
                <div class="text-sm font-medium text-white">Config Cache</div>
                <div class="text-xs text-slate-400 mt-1 group-hover:text-slate-300 transition-colors duration-300">Configuration</div>
              </div>
              <div class="absolute -inset-1 bg-gradient-to-r from-purple-500/20 to-cyan-500/20 rounded-xl blur opacity-0 group-hover:opacity-50 transition-opacity duration-300 -z-10"></div>
            </button>

            <button
              @click="clearCache('route')"
              class="relative p-4 bg-slate-700/30 backdrop-blur-sm border border-white/10 rounded-xl hover:border-white/20 hover:bg-slate-700/50 transition-all duration-300 hover:scale-105 group overflow-hidden animate-slide-in-up"
              style="animation-delay: 0.2s;"
            >
              <div class="absolute inset-0 bg-gradient-to-br from-cyan-500/10 to-violet-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
              <div class="relative z-10">
                <div class="text-sm font-medium text-white">Route Cache</div>
                <div class="text-xs text-slate-400 mt-1 group-hover:text-slate-300 transition-colors duration-300">Application routes</div>
              </div>
              <div class="absolute -inset-1 bg-gradient-to-r from-cyan-500/20 to-violet-500/20 rounded-xl blur opacity-0 group-hover:opacity-50 transition-opacity duration-300 -z-10"></div>
            </button>

            <button
              @click="clearCache('view')"
              class="relative p-4 bg-slate-700/30 backdrop-blur-sm border border-white/10 rounded-xl hover:border-white/20 hover:bg-slate-700/50 transition-all duration-300 hover:scale-105 group overflow-hidden animate-slide-in-up"
              style="animation-delay: 0.3s;"
            >
              <div class="absolute inset-0 bg-gradient-to-br from-violet-500/10 to-cyan-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
              <div class="relative z-10">
                <div class="text-sm font-medium text-white">View Cache</div>
                <div class="text-xs text-slate-400 mt-1 group-hover:text-slate-300 transition-colors duration-300">Compiled views</div>
              </div>
              <div class="absolute -inset-1 bg-gradient-to-r from-violet-500/20 to-cyan-500/20 rounded-xl blur opacity-0 group-hover:opacity-50 transition-opacity duration-300 -z-10"></div>
            </button>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { reactive } from 'vue'
import { Head, router, useForm } from '@inertiajs/vue3'
import route from 'ziggy-js'

import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
})

const settingsForm = useForm({
  max_upload_size: props.settings.max_upload_size || 50,
  allowed_mimes: props.settings.allowed_mimes || 'jpg,jpeg,png,webp,avif',
  enable_registration: props.settings.enable_registration ?? true,
  enable_comments: props.settings.enable_comments ?? true,
  comment_moderation: props.settings.comment_moderation ?? true,
})

const loading = reactive({
  clearCache: false,
})

const updateSettings = () => {
  settingsForm.post(route('admin.system.update-settings'))
}

const clearCache = async (type) => {
  loading.clearCache = true
  try {
    await router.post(route('admin.system.clear-cache'), { type })
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

@keyframes float-particle-1 {
  0%, 100% { 
    transform: translateY(0px) translateX(0px) rotate(0deg); 
    opacity: 0.4; 
  }
  25% { 
    transform: translateY(-80px) translateX(40px) rotate(90deg); 
    opacity: 0.8; 
  }
  50% { 
    transform: translateY(-40px) translateX(-20px) rotate(180deg); 
    opacity: 0.6; 
  }
  75% { 
    transform: translateY(-120px) translateX(10px) rotate(270deg); 
    opacity: 0.9; 
  }
}

@keyframes float-particle-2 {
  0%, 100% { 
    transform: translateY(0px) translateX(0px) rotate(0deg); 
    opacity: 0.3; 
  }
  33% { 
    transform: translateY(-60px) translateX(-30px) rotate(120deg); 
    opacity: 0.7; 
  }
  66% { 
    transform: translateY(-100px) translateX(20px) rotate(240deg); 
    opacity: 0.5; 
  }
}

@keyframes float-particle-3 {
  0%, 100% { 
    transform: translateY(0px) translateX(0px) rotate(0deg); 
    opacity: 0.5; 
  }
  50% { 
    transform: translateY(-90px) translateX(50px) rotate(180deg); 
    opacity: 0.8; 
  }
}

@keyframes float-particle-4 {
  0%, 100% { 
    transform: translateY(0px) translateX(0px) rotate(0deg); 
    opacity: 0.4; 
  }
  25% { 
    transform: translateY(-40px) translateX(30px) rotate(90deg); 
    opacity: 0.9; 
  }
  50% { 
    transform: translateY(-110px) translateX(-10px) rotate(180deg); 
    opacity: 0.6; 
  }
  75% { 
    transform: translateY(-70px) translateX(-40px) rotate(270deg); 
    opacity: 0.7; 
  }
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

.animate-float-particle-1 { animation: float-particle-1 12s ease-in-out infinite; }
.animate-float-particle-2 { animation: float-particle-2 14s ease-in-out infinite; }
.animate-float-particle-3 { animation: float-particle-3 10s ease-in-out infinite; }
.animate-float-particle-4 { animation: float-particle-4 13s ease-in-out infinite; }

/* Enhanced form styling */
input[type="checkbox"]:checked {
  background: linear-gradient(135deg, #8b5cf6, #06b6d4);
  border-color: rgba(139, 92, 246, 0.5);
}

input[type="number"]:focus,
input[type="text"]:focus {
  box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
}

/* Custom scrollbar for better aesthetics */
::-webkit-scrollbar {
  width: 6px;
}

::-webkit-scrollbar-track {
  background: rgba(30, 41, 59, 0.5);
  border-radius: 3px;
}

::-webkit-scrollbar-thumb {
  background: linear-gradient(135deg, #8b5cf6, #06b6d4);
  border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
  background: linear-gradient(135deg, #7c3aed, #0891b2);
}
</style>