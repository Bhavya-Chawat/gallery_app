<template>
  <div class="min-h-screen bg-gray-50 dark:bg-slate-900 flex">
    <!-- Sidebar -->
    <div class="w-64 bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl border-r border-gray-200/50 dark:border-slate-700/50 flex flex-col">
      <!-- Logo -->
      <div class="px-6 py-4 border-b border-gray-200/50 dark:border-slate-700/50">
        <Link href="/" class="flex items-center space-x-2">
          <div class="w-8 h-8 bg-gradient-to-r from-blue-600 via-cyan-500 to-blue-400 rounded-lg flex items-center justify-center">
            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
            </svg>
          </div>
          <span class="text-xl font-bold bg-gradient-to-r from-blue-600 to-cyan-500 bg-clip-text text-transparent">
            Admin
          </span>
        </Link>
      </div>

      <!-- Navigation -->
      <nav class="flex-1 px-4 py-6 space-y-2">
        <Link 
          href="/admin/dashboard"
          class="flex items-center space-x-3 px-4 py-2 text-sm font-medium rounded-lg transition-colors"
          :class="isActive('/admin/dashboard') ? 'bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700'"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2V7zm16 0V5a2 2 0 00-2-2H7a2 2 0 00-2 2v2m16 0l-8 6-8-6" />
          </svg>
          <span>Dashboard</span>
        </Link>

        <Link 
          href="/admin/users"
          class="flex items-center space-x-3 px-4 py-2 text-sm font-medium rounded-lg transition-colors"
          :class="isActive('/admin/users') ? 'bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700'"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
          </svg>
          <span>Users</span>
        </Link>

        <Link 
          href="/admin/system"
          class="flex items-center space-x-3 px-4 py-2 text-sm font-medium rounded-lg transition-colors"
          :class="isActive('/admin/system') ? 'bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700'"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
          </svg>
          <span>System</span>
        </Link>

        <Link 
          href="/admin/moderation"
          class="flex items-center space-x-3 px-4 py-2 text-sm font-medium rounded-lg transition-colors"
          :class="isActive('/admin/moderation') ? 'bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700'"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
          </svg>
          <span>Moderation</span>
          <span v-if="pendingModerationCount > 0" class="ml-auto bg-red-500 text-white text-xs rounded-full px-2 py-0.5 min-w-[20px] text-center">
            {{ pendingModerationCount }}
          </span>
        </Link>

        <div class="border-t border-gray-200 dark:border-slate-700 mt-6 pt-6">
          <Link 
            href="/admin/analytics"
            class="flex items-center space-x-3 px-4 py-2 text-sm font-medium rounded-lg transition-colors"
            :class="isActive('/admin/analytics') ? 'bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700'"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            <span>Analytics</span>
          </Link>

          <Link 
            href="/admin/audit-logs"
            class="flex items-center space-x-3 px-4 py-2 text-sm font-medium rounded-lg transition-colors"
            :class="isActive('/admin/audit-logs') ? 'bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700'"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span>Audit Logs</span>
          </Link>
        </div>
      </nav>

      <!-- User Info & Back to Site -->
      <div class="px-4 py-4 border-t border-gray-200/50 dark:border-slate-700/50">
        <div class="flex items-center space-x-3 px-4 py-3 bg-gray-50 dark:bg-slate-700/50 rounded-lg">
          <div class="w-8 h-8 bg-gradient-to-r from-blue-600 to-cyan-500 rounded-full flex items-center justify-center text-white text-sm font-medium">
            {{ $page.props.auth?.user?.name?.charAt(0).toUpperCase() }}
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
              {{ $page.props.auth?.user?.name }}
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-400">
              Administrator
            </p>
          </div>
        </div>
        
        <Link 
          href="/"
          class="flex items-center justify-center space-x-2 mt-3 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-100 dark:hover:bg-slate-700 rounded-lg transition-colors"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
          <span>Back to Site</span>
        </Link>
      </div>
    </div>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col">
      <!-- Top Header -->
      <header class="bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl border-b border-gray-200/50 dark:border-slate-700/50 px-6 py-4">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
              {{ pageTitle }}
            </h1>
            <p v-if="pageDescription" class="text-sm text-gray-600 dark:text-gray-400 mt-1">
              {{ pageDescription }}
            </p>
          </div>
          
          <!-- Header Actions -->
          <div class="flex items-center space-x-4">
            <!-- Theme Toggle -->
            <button 
              @click="toggleTheme"
              class="p-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors"
              aria-label="Toggle theme"
            >
              <svg v-if="!isDark" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
              </svg>
              <svg v-else class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd" />
              </svg>
            </button>
            
            <!-- Quick Actions -->
            <slot name="header-actions" />
          </div>
        </div>
      </header>

      <!-- Page Content -->
      <main class="flex-1 p-6">
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'

const props = defineProps({
  pageTitle: {
    type: String,
    default: 'Admin Dashboard'
  },
  pageDescription: {
    type: String,
    default: null
  }
})

const isDark = ref(false)
const page = usePage()

const pendingModerationCount = computed(() => {
  return page.props.pendingModerationCount || 0
})

const isActive = (path) => {
  return page.url.startsWith(path)
}

const toggleTheme = () => {
  isDark.value = !isDark.value
  if (isDark.value) {
    document.documentElement.classList.add('dark')
    localStorage.setItem('theme', 'dark')
  } else {
    document.documentElement.classList.remove('dark')
    localStorage.setItem('theme', 'light')
  }
}

onMounted(() => {
  // Initialize theme from localStorage or system preference
  const savedTheme = localStorage.getItem('theme')
  const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches
  
  isDark.value = savedTheme ? savedTheme === 'dark' : systemPrefersDark
  
  if (isDark.value) {
    document.documentElement.classList.add('dark')
  }
})
</script>