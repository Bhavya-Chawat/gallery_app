<template>
  <AdminLayout>
    <Head title="Create User" />

    <template #header>
      <div class="flex items-center justify-between animate-fadeInUp">
        <div class="relative">
          <!-- Floating particles around header -->
          <div class="absolute -top-2 -left-2 w-1 h-1 bg-violet-400 rounded-full animate-pulse opacity-60"></div>
          <div class="absolute -bottom-1 -right-1 w-0.5 h-0.5 bg-cyan-400 rounded-full animate-ping opacity-40"></div>
          
          <h2 class="font-bold text-2xl bg-gradient-to-r from-violet-400 via-purple-400 to-cyan-400 bg-clip-text text-transparent animate-shimmer">
            Create New User
          </h2>
          <p class="text-sm text-slate-300 mt-1">
            Add a new user to the system
          </p>
        </div>
        
        <Link
          :href="route('admin.users.index')"
          class="group relative inline-flex items-center px-6 py-3 bg-slate-800/60 backdrop-blur-sm border border-slate-600/30 rounded-xl font-semibold text-sm text-slate-300 uppercase tracking-wider 
                 hover:bg-slate-700/60 hover:text-white hover:border-violet-500/40 hover:scale-105 hover:shadow-lg hover:shadow-violet-500/20 
                 transition-all duration-300"
        >
          <!-- Button glow effect -->
          <div class="absolute inset-0 bg-gradient-to-r from-violet-500/0 to-cyan-500/0 group-hover:from-violet-500/10 group-hover:to-cyan-500/10 rounded-xl transition-all duration-300"></div>
          <span class="relative">← Back to Users</span>
        </Link>
      </div>
    </template>

    <div class="max-w-2xl animate-fadeInUp" style="animation-delay: 0.1s;">
      <div class="relative bg-slate-900/60 backdrop-blur-xl rounded-2xl shadow-2xl shadow-slate-900/20 p-8 border border-slate-700/50">
        <!-- Form container glow effect -->
        <div class="absolute inset-0 bg-gradient-to-r from-violet-500/5 via-transparent to-cyan-500/5 rounded-2xl pointer-events-none"></div>
        
        <!-- Floating particles -->
        <div class="absolute top-4 left-6 w-0.5 h-0.5 bg-purple-400 rounded-full animate-pulse opacity-40"></div>
        <div class="absolute bottom-6 right-8 w-1 h-1 bg-violet-400 rounded-full animate-bounce opacity-30"></div>
        
        <form @submit.prevent="createUser" class="relative">
          <div class="space-y-8">
            <!-- Enhanced Name & Email -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 animate-slideInUp" style="animation-delay: 0.1s;">
              <div class="group">
                <label class="block text-sm font-medium text-slate-300 mb-2">
                  Full Name *
                </label>
                <div class="relative">
                  <!-- Input glow effect -->
                  <div class="absolute -inset-0.5 bg-gradient-to-r from-violet-500/0 via-cyan-500/0 to-purple-500/0 rounded-xl opacity-0 group-hover:opacity-30 group-focus-within:opacity-50 transition-opacity duration-500 blur-sm"></div>
                  
                  <input
                    v-model="form.name"
                    type="text"
                    required
                    class="relative w-full px-4 py-3 bg-slate-800/60 backdrop-blur-sm border rounded-xl 
                           text-slate-100 placeholder-slate-400 
                           focus:bg-slate-700/60 focus:border-violet-500/50 focus:ring-4 focus:ring-violet-500/20 
                           hover:border-slate-500/50 transition-all duration-300 outline-none shadow-lg"
                    :class="{ 
                      'border-slate-600/50': !errors.name, 
                      'border-red-500/50 focus:border-red-500/70 focus:ring-red-500/20': errors.name 
                    }"
                  />
                  
                  <!-- Input shimmer effect -->
                  <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-transparent via-white/5 to-transparent -skew-x-12 opacity-0 group-focus-within:opacity-100 group-focus-within:animate-shimmer transition-opacity duration-700 pointer-events-none"></div>
                </div>
                <p v-if="errors.name" class="mt-2 text-sm text-red-400 animate-fadeIn">{{ errors.name }}</p>
              </div>

              <div class="group">
                <label class="block text-sm font-medium text-slate-300 mb-2">
                  Email Address *
                </label>
                <div class="relative">
                  <div class="absolute -inset-0.5 bg-gradient-to-r from-violet-500/0 via-cyan-500/0 to-purple-500/0 rounded-xl opacity-0 group-hover:opacity-30 group-focus-within:opacity-50 transition-opacity duration-500 blur-sm"></div>
                  
                  <input
                    v-model="form.email"
                    type="email"
                    required
                    class="relative w-full px-4 py-3 bg-slate-800/60 backdrop-blur-sm border rounded-xl 
                           text-slate-100 placeholder-slate-400 
                           focus:bg-slate-700/60 focus:border-violet-500/50 focus:ring-4 focus:ring-violet-500/20 
                           hover:border-slate-500/50 transition-all duration-300 outline-none shadow-lg"
                    :class="{ 
                      'border-slate-600/50': !errors.email, 
                      'border-red-500/50 focus:border-red-500/70 focus:ring-red-500/20': errors.email 
                    }"
                  />
                  
                  <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-transparent via-white/5 to-transparent -skew-x-12 opacity-0 group-focus-within:opacity-100 group-focus-within:animate-shimmer transition-opacity duration-700 pointer-events-none"></div>
                </div>
                <p v-if="errors.email" class="mt-2 text-sm text-red-400 animate-fadeIn">{{ errors.email }}</p>
              </div>
            </div>

            <!-- Enhanced Password -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 animate-slideInUp" style="animation-delay: 0.2s;">
              <div class="group">
                <label class="block text-sm font-medium text-slate-300 mb-2">
                  Password *
                </label>
                <div class="relative">
                  <div class="absolute -inset-0.5 bg-gradient-to-r from-violet-500/0 via-cyan-500/0 to-purple-500/0 rounded-xl opacity-0 group-hover:opacity-30 group-focus-within:opacity-50 transition-opacity duration-500 blur-sm"></div>
                  
                  <input
                    v-model="form.password"
                    type="password"
                    required
                    minlength="8"
                    class="relative w-full px-4 py-3 bg-slate-800/60 backdrop-blur-sm border rounded-xl 
                           text-slate-100 placeholder-slate-400 
                           focus:bg-slate-700/60 focus:border-violet-500/50 focus:ring-4 focus:ring-violet-500/20 
                           hover:border-slate-500/50 transition-all duration-300 outline-none shadow-lg"
                    :class="{ 
                      'border-slate-600/50': !errors.password, 
                      'border-red-500/50 focus:border-red-500/70 focus:ring-red-500/20': errors.password 
                    }"
                  />
                  
                  <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-transparent via-white/5 to-transparent -skew-x-12 opacity-0 group-focus-within:opacity-100 group-focus-within:animate-shimmer transition-opacity duration-700 pointer-events-none"></div>
                </div>
                <p v-if="errors.password" class="mt-2 text-sm text-red-400 animate-fadeIn">{{ errors.password }}</p>
              </div>

              <div class="group">
                <label class="block text-sm font-medium text-slate-300 mb-2">
                  Confirm Password *
                </label>
                <div class="relative">
                  <div class="absolute -inset-0.5 bg-gradient-to-r from-violet-500/0 via-cyan-500/0 to-purple-500/0 rounded-xl opacity-0 group-hover:opacity-30 group-focus-within:opacity-50 transition-opacity duration-500 blur-sm"></div>
                  
                  <input
                    v-model="form.password_confirmation"
                    type="password"
                    required
                    minlength="8"
                    class="relative w-full px-4 py-3 bg-slate-800/60 backdrop-blur-sm border border-slate-600/50 rounded-xl 
                           text-slate-100 placeholder-slate-400 
                           focus:bg-slate-700/60 focus:border-violet-500/50 focus:ring-4 focus:ring-violet-500/20 
                           hover:border-slate-500/50 transition-all duration-300 outline-none shadow-lg"
                  />
                  
                  <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-transparent via-white/5 to-transparent -skew-x-12 opacity-0 group-focus-within:opacity-100 group-focus-within:animate-shimmer transition-opacity duration-700 pointer-events-none"></div>
                </div>
              </div>
            </div>

            <!-- Enhanced Role & Storage -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 animate-slideInUp" style="animation-delay: 0.3s;">
              <div class="group">
                <label class="block text-sm font-medium text-slate-300 mb-2">
                  Role *
                </label>
                <div class="relative">
                  <div class="absolute -inset-0.5 bg-gradient-to-r from-violet-500/0 via-cyan-500/0 to-purple-500/0 rounded-xl opacity-0 group-hover:opacity-30 group-focus-within:opacity-50 transition-opacity duration-500 blur-sm"></div>
                  
                  <select
                    v-model="form.role"
                    required
                    class="relative w-full px-4 py-3 bg-slate-800/60 backdrop-blur-sm border rounded-xl 
                           text-slate-100 focus:bg-slate-700/60 focus:border-violet-500/50 focus:ring-4 focus:ring-violet-500/20 
                           hover:border-slate-500/50 transition-all duration-300 outline-none shadow-lg"
                    :class="{ 
                      'border-slate-600/50': !errors.role, 
                      'border-red-500/50 focus:border-red-500/70 focus:ring-red-500/20': errors.role 
                    }"
                  >
                    <option value="" class="bg-slate-800">Select a role...</option>
                    <option v-for="role in roles" :key="role.id" :value="role.slug" class="bg-slate-800">
                      {{ role.name }}
                    </option>
                  </select>
                </div>
                <p v-if="errors.role" class="mt-2 text-sm text-red-400 animate-fadeIn">{{ errors.role }}</p>
              </div>

              <div class="group">
                <label class="block text-sm font-medium text-slate-300 mb-2">
                  Storage Quota (GB) *
                </label>
                <div class="relative">
                  <div class="absolute -inset-0.5 bg-gradient-to-r from-violet-500/0 via-cyan-500/0 to-purple-500/0 rounded-xl opacity-0 group-hover:opacity-30 group-focus-within:opacity-50 transition-opacity duration-500 blur-sm"></div>
                  
                  <input
                    v-model="form.storage_quota_gb"
                    type="number"
                    min="0"
                    step="0.1"
                    required
                    class="relative w-full px-4 py-3 bg-slate-800/60 backdrop-blur-sm border rounded-xl 
                           text-slate-100 placeholder-slate-400 
                           focus:bg-slate-700/60 focus:border-violet-500/50 focus:ring-4 focus:ring-violet-500/20 
                           hover:border-slate-500/50 transition-all duration-300 outline-none shadow-lg"
                    :class="{ 
                      'border-slate-600/50': !errors.storage_quota_gb, 
                      'border-red-500/50 focus:border-red-500/70 focus:ring-red-500/20': errors.storage_quota_gb 
                    }"
                  />
                  
                  <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-transparent via-white/5 to-transparent -skew-x-12 opacity-0 group-focus-within:opacity-100 group-focus-within:animate-shimmer transition-opacity duration-700 pointer-events-none"></div>
                </div>
                <p v-if="errors.storage_quota_gb" class="mt-2 text-sm text-red-400 animate-fadeIn">{{ errors.storage_quota_gb }}</p>
              </div>
            </div>

            <!-- Enhanced Status -->
            <div class="animate-slideInUp" style="animation-delay: 0.4s;">
              <label class="group relative flex items-center cursor-pointer">
                <div class="relative">
                  <input
                    v-model="form.is_active"
                    type="checkbox"
                    class="h-5 w-5 text-violet-600 bg-slate-800/60 border-slate-500/50 rounded focus:ring-violet-500/30 focus:ring-2 transition-all duration-200"
                  />
                  <!-- Checkbox glow -->
                  <div class="absolute -inset-1 bg-violet-500/20 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-300 -z-10"></div>
                </div>
                <span class="ml-3 text-sm text-slate-300 group-hover:text-slate-200 transition-colors duration-300">
                  Active (user can log in)
                </span>
              </label>
            </div>

            <!-- Enhanced Actions -->
            <div class="flex justify-end space-x-4 pt-8 border-t border-slate-700/50 animate-slideInUp" style="animation-delay: 0.5s;">
              <Link
                :href="route('admin.users.index')"
                class="group relative inline-flex items-center px-6 py-3 border border-slate-600/30 rounded-xl shadow-lg text-sm font-medium text-slate-300 bg-slate-800/50 backdrop-blur-sm 
                       hover:bg-slate-700/50 hover:text-white hover:border-slate-500/40 hover:scale-105 
                       transition-all duration-300"
              >
                <!-- Button glow effect -->
                <div class="absolute inset-0 bg-gradient-to-r from-slate-500/0 to-slate-400/0 group-hover:from-slate-500/10 group-hover:to-slate-400/10 rounded-xl transition-all duration-300"></div>
                <span class="relative">Cancel</span>
              </Link>
              
              <button
                type="submit"
                :disabled="processing"
                class="group relative inline-flex items-center px-8 py-3 bg-gradient-to-r from-violet-600/80 to-cyan-600/80 backdrop-blur-sm border border-transparent rounded-xl font-semibold text-sm text-white uppercase tracking-wider 
                       hover:from-violet-500/90 hover:to-cyan-500/90 hover:scale-105 hover:shadow-xl hover:shadow-violet-500/30 
                       disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100 
                       transition-all duration-300"
              >
                <!-- Button glow effect -->
                <div class="absolute inset-0 bg-gradient-to-r from-violet-400/0 to-cyan-400/0 group-hover:from-violet-400/20 group-hover:to-cyan-400/20 rounded-xl transition-all duration-300"></div>
                
                <!-- Loading state -->
                <svg v-if="processing" class="relative -ml-1 mr-3 h-4 w-4 animate-spin text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                
                <span class="relative">{{ processing ? 'Creating...' : 'Create User' }}</span>
              </button>
            </div>
          </div>
        </form>
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
  roles: Array,
  errors: { type: Object, default: () => ({}) },
})

const form = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  role: '',
  storage_quota_gb: 5,
  is_active: true,
})

const processing = reactive({ value: false })

const createUser = () => {
  processing.value = true

  router.post(route('admin.users.store'), form, {
    onFinish: () => {
      processing.value = false
    }
  })
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

@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
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

.animate-fadeIn {
  animation: fadeIn 0.3s ease-out;
}

/* Enhanced select dropdown styling */
select option {
  background: rgb(30 41 59);
  color: rgb(226 232 240);
}

/* Custom spinner for loading state */
@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.animate-spin {
  animation: spin 1s linear infinite;
}
</style>