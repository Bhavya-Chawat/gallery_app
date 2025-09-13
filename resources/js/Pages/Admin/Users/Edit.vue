
<template>
  <AdminLayout>
    <Head :title="`Edit User: ${user.name}`" />

    <template #header>
      <div class="flex items-center justify-between animate-fade-in-up">
        <div>
          <h2 class="font-semibold text-xl bg-gradient-to-r from-violet-400 via-purple-400 to-cyan-400 bg-clip-text text-transparent leading-tight animate-shimmer-text">
            Edit User
          </h2>
          <p class="text-sm text-slate-300 mt-1">
            Modify {{ user.name }}'s account details
          </p>
        </div>
        <Link
          :href="route('admin.users.show', user.id)"
          class="relative inline-flex items-center px-4 py-2 bg-gradient-to-r from-slate-600 via-slate-700 to-slate-600 border border-white/20 rounded-xl font-semibold text-xs text-white uppercase tracking-widest hover:from-slate-500 hover:via-slate-600 hover:to-slate-500 transition-all duration-500 hover:scale-105 hover:shadow-2xl hover:shadow-slate-500/25 group overflow-hidden backdrop-blur-xl"
        >
          <div class="absolute inset-0 bg-gradient-to-r from-slate-400/20 via-slate-500/20 to-slate-400/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
          <span class="relative z-10">← Back to User</span>
          <div class="absolute -inset-1 bg-gradient-to-r from-slate-600 via-slate-700 to-slate-600 rounded-xl blur opacity-30 group-hover:opacity-70 transition-opacity duration-300 -z-10"></div>
        </Link>
      </div>
    </template>

    <div class="max-w-2xl relative">
      <!-- Floating Particles Background -->
      <div class="fixed inset-0 pointer-events-none overflow-hidden opacity-15 z-0">
        <div 
          v-for="i in 10" 
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

      <div class="relative group animate-fade-in-up" style="animation-delay: 0.1s;">
        <div class="absolute inset-0 bg-gradient-to-br from-violet-500/10 via-purple-500/5 to-cyan-500/10 rounded-2xl blur-xl opacity-0 group-hover:opacity-100 transition-opacity duration-700"></div>
        <div class="relative z-10 bg-slate-800/50 backdrop-blur-xl rounded-2xl shadow-2xl border border-white/10 hover:border-white/20 transition-all duration-500 hover:scale-[1.01] hover:shadow-violet-500/10 p-6">
          <form @submit.prevent="updateUser">
            <div class="space-y-6">
              <!-- Name & Email -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="group animate-slide-in-right" style="animation-delay: 0.1s;">
                  <label class="block text-sm font-medium text-slate-300 mb-1 group-hover:text-white transition-colors duration-300">
                    Full Name *
                  </label>
                  <div class="relative">
                    <input
                      v-model="form.name"
                      type="text"
                      required
                      class="w-full px-3 py-2 bg-slate-700/50 backdrop-blur-sm border border-white/20 rounded-xl text-white placeholder-slate-400 focus:ring-2 focus:ring-violet-500/50 focus:border-violet-500/50 transition-all duration-300 hover:bg-slate-700/70 focus:bg-slate-700/70 focus:scale-105"
                      :class="{ 'border-red-500/50 focus:ring-red-500/50 focus:border-red-500/50': errors.name }"
                    />
                    <div class="absolute inset-0 bg-gradient-to-r from-violet-500/10 to-purple-500/10 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
                  </div>
                  <p v-if="errors.name" class="mt-1 text-sm text-red-400 animate-slide-in-down">{{ errors.name }}</p>
                </div>

                <div class="group animate-slide-in-left" style="animation-delay: 0.2s;">
                  <label class="block text-sm font-medium text-slate-300 mb-1 group-hover:text-white transition-colors duration-300">
                    Email Address *
                  </label>
                  <div class="relative">
                    <input
                      v-model="form.email"
                      type="email"
                      required
                      class="w-full px-3 py-2 bg-slate-700/50 backdrop-blur-sm border border-white/20 rounded-xl text-white placeholder-slate-400 focus:ring-2 focus:ring-violet-500/50 focus:border-violet-500/50 transition-all duration-300 hover:bg-slate-700/70 focus:bg-slate-700/70 focus:scale-105"
                      :class="{ 'border-red-500/50 focus:ring-red-500/50 focus:border-red-500/50': errors.email }"
                    />
                    <div class="absolute inset-0 bg-gradient-to-r from-cyan-500/10 to-violet-500/10 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
                  </div>
                  <p v-if="errors.email" class="mt-1 text-sm text-red-400 animate-slide-in-down">{{ errors.email }}</p>
                </div>
              </div>

              <!-- Password (Optional) -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="group animate-slide-in-right" style="animation-delay: 0.3s;">
                  <label class="block text-sm font-medium text-slate-300 mb-1 group-hover:text-white transition-colors duration-300">
                    New Password (leave blank to keep current)
                  </label>
                  <div class="relative">
                    <input
                      v-model="form.password"
                      type="password"
                      minlength="8"
                      class="w-full px-3 py-2 bg-slate-700/50 backdrop-blur-sm border border-white/20 rounded-xl text-white placeholder-slate-400 focus:ring-2 focus:ring-violet-500/50 focus:border-violet-500/50 transition-all duration-300 hover:bg-slate-700/70 focus:bg-slate-700/70 focus:scale-105"
                      :class="{ 'border-red-500/50 focus:ring-red-500/50 focus:border-red-500/50': errors.password }"
                    />
                    <div class="absolute inset-0 bg-gradient-to-r from-purple-500/10 to-cyan-500/10 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
                  </div>
                  <p v-if="errors.password" class="mt-1 text-sm text-red-400 animate-slide-in-down">{{ errors.password }}</p>
                </div>

                <div class="group animate-slide-in-left" style="animation-delay: 0.4s;">
                  <label class="block text-sm font-medium text-slate-300 mb-1 group-hover:text-white transition-colors duration-300">
                    Confirm New Password
                  </label>
                  <div class="relative">
                    <input
                      v-model="form.password_confirmation"
                      type="password"
                      minlength="8"
                      class="w-full px-3 py-2 bg-slate-700/50 backdrop-blur-sm border border-white/20 rounded-xl text-white placeholder-slate-400 focus:ring-2 focus:ring-violet-500/50 focus:border-violet-500/50 transition-all duration-300 hover:bg-slate-700/70 focus:bg-slate-700/70 focus:scale-105"
                    />
                    <div class="absolute inset-0 bg-gradient-to-r from-cyan-500/10 to-purple-500/10 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
                  </div>
                </div>
              </div>

              <!-- Storage & Settings -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="group animate-slide-in-right" style="animation-delay: 0.5s;">
                  <label class="block text-sm font-medium text-slate-300 mb-1 group-hover:text-white transition-colors duration-300">
                    Storage Quota (GB) *
                  </label>
                  <div class="relative">
                    <input
                      v-model="form.storage_quota_gb"
                      type="number"
                      min="0"
                      step="0.1"
                      required
                      class="w-full px-3 py-2 bg-slate-700/50 backdrop-blur-sm border border-white/20 rounded-xl text-white placeholder-slate-400 focus:ring-2 focus:ring-violet-500/50 focus:border-violet-500/50 transition-all duration-300 hover:bg-slate-700/70 focus:bg-slate-700/70 focus:scale-105"
                      :class="{ 'border-red-500/50 focus:ring-red-500/50 focus:border-red-500/50': errors.storage_quota_gb }"
                    />
                    <div class="absolute inset-0 bg-gradient-to-r from-violet-500/10 to-cyan-500/10 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
                  </div>
                  <p v-if="errors.storage_quota_gb" class="mt-1 text-sm text-red-400 animate-slide-in-down">{{ errors.storage_quota_gb }}</p>
                </div>
              </div>

              <!-- Status & Notifications -->
              <div class="space-y-4 animate-fade-in-up" style="animation-delay: 0.6s;">
                <label class="flex items-center p-3 bg-slate-700/30 backdrop-blur-sm border border-white/10 rounded-xl hover:border-white/20 transition-all duration-300 hover:bg-slate-700/50 group cursor-pointer">
                  <div class="relative">
                    <input
                      v-model="form.is_active"
                      type="checkbox"
                      class="h-4 w-4 text-violet-600 focus:ring-violet-500/50 border-white/30 rounded bg-slate-600/50 backdrop-blur-sm transition-all duration-300"
                    />
                    <div class="absolute -inset-1 bg-gradient-to-r from-violet-500/20 to-green-500/20 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-300 blur-sm"></div>
                  </div>
                  <span class="ml-2 text-sm text-slate-300 group-hover:text-white transition-colors duration-300">
                    Active (user can log in)
                  </span>
                </label>

                <label class="flex items-center p-3 bg-slate-700/30 backdrop-blur-sm border border-white/10 rounded-xl hover:border-white/20 transition-all duration-300 hover:bg-slate-700/50 group cursor-pointer">
                  <div class="relative">
                    <input
                      v-model="form.email_notifications"
                      type="checkbox"
                      class="h-4 w-4 text-violet-600 focus:ring-violet-500/50 border-white/30 rounded bg-slate-600/50 backdrop-blur-sm transition-all duration-300"
                    />
                    <div class="absolute -inset-1 bg-gradient-to-r from-cyan-500/20 to-violet-500/20 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-300 blur-sm"></div>
                  </div>
                  <span class="ml-2 text-sm text-slate-300 group-hover:text-white transition-colors duration-300">
                    Email notifications enabled
                  </span>
                </label>
              </div>

              <!-- Actions -->
              <div class="flex justify-end space-x-3 pt-6 border-t border-white/10 animate-fade-in-up" style="animation-delay: 0.7s;">
                <Link
                  :href="route('admin.users.show', user.id)"
                  class="relative inline-flex items-center px-4 py-2 bg-slate-700/50 backdrop-blur-sm border border-white/20 rounded-xl shadow-xl text-sm font-medium text-slate-300 hover:text-white hover:bg-slate-700/70 transition-all duration-300 hover:scale-105 group overflow-hidden"
                >
                  <div class="absolute inset-0 bg-gradient-to-r from-slate-500/10 to-slate-600/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                  <span class="relative z-10">Cancel</span>
                </Link>
                <button
                  type="submit"
                  :disabled="processing.value"
                  class="relative inline-flex items-center px-4 py-2 bg-gradient-to-r from-violet-600 via-purple-600 to-cyan-600 border border-white/20 rounded-xl font-semibold text-xs text-white uppercase tracking-widest hover:from-violet-500 hover:via-purple-500 hover:to-cyan-500 disabled:opacity-50 transition-all duration-500 hover:scale-105 hover:shadow-2xl hover:shadow-violet-500/25 group overflow-hidden backdrop-blur-xl"
                >
                  <div class="absolute inset-0 bg-gradient-to-r from-violet-400/20 via-purple-400/20 to-cyan-400/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                  <span class="relative z-10">{{ processing.value ? 'Updating...' : 'Update User' }}</span>
                  <div class="absolute -inset-1 bg-gradient-to-r from-violet-600 via-purple-600 to-cyan-600 rounded-xl blur opacity-30 group-hover:opacity-70 transition-opacity duration-300 -z-10"></div>
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { reactive } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import route from 'ziggy-js'

import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  user: Object,
  roles: Array,
  errors: { type: Object, default: () => ({}) },
})

const form = reactive({
  name: props.user.name,
  email: props.user.email,
  password: '',
  password_confirmation: '',
  storage_quota_gb: props.user.storage_quota_gb,
  is_active: props.user.is_active,
  email_notifications: props.user.email_notifications,
})

const processing = reactive({ value: false })

const updateUser = () => {
  processing.value = true

  router.put(route('admin.users.update', props.user.id), form, {
    onFinish: () => {
      processing.value = false
    }
  })
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

@keyframes slide-in-left {
  0% {
    opacity: 0;
    transform: translateX(-30px);
  }
  100% {
    opacity: 1;
    transform: translateX(0);
  }
}

@keyframes slide-in-down {
  0% {
    opacity: 0;
    transform: translateY(-10px);
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

.animate-slide-in-right {
  animation: slide-in-right 0.6s ease-out forwards;
}

.animate-slide-in-left {
  animation: slide-in-left 0.6s ease-out forwards;
}

.animate-slide-in-down {
  animation: slide-in-down 0.4s ease-out forwards;
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

input[type="text"]:focus,
input[type="email"]:focus,
input[type="password"]:focus,
input[type="number"]:focus {
  box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
}

/* Error state styling */
input.border-red-500\/50:focus {
  box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
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