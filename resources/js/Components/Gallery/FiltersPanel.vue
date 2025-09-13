<template>
  <div class="relative overflow-hidden bg-gradient-to-br from-slate-900/60 via-slate-800/60 to-slate-900/60 backdrop-blur-xl border border-white/10 rounded-2xl shadow-2xl shadow-violet-500/10 p-6">
    <!-- Animated Background Mesh -->
    <div class="absolute inset-0 opacity-8">
      <svg class="w-full h-full animate-float-filter" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
        <defs>
          <linearGradient id="filter-mesh" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" class="animate-gradient-shift">
              <animate attributeName="stop-color" values="#8b5cf6;#06b6d4;#a855f7;#8b5cf6" dur="8s" repeatCount="indefinite"/>
            </stop>
            <stop offset="100%" class="animate-gradient-shift">
              <animate attributeName="stop-color" values="#06b6d4;#a855f7;#8b5cf6;#06b6d4" dur="8s" repeatCount="indefinite"/>
            </stop>
          </linearGradient>
        </defs>
        <rect x="10" y="10" width="80" height="15" fill="none" stroke="url(#filter-mesh)" stroke-width="0.3" opacity="0.4" rx="3"/>
        <rect x="10" y="30" width="35" height="15" fill="none" stroke="url(#filter-mesh)" stroke-width="0.3" opacity="0.4" rx="3"/>
        <rect x="55" y="30" width="35" height="15" fill="none" stroke="url(#filter-mesh)" stroke-width="0.3" opacity="0.4" rx="3"/>
        <rect x="10" y="50" width="35" height="15" fill="none" stroke="url(#filter-mesh)" stroke-width="0.3" opacity="0.4" rx="3"/>
        <rect x="55" y="50" width="35" height="15" fill="none" stroke="url(#filter-mesh)" stroke-width="0.3" opacity="0.4" rx="3"/>
        <rect x="10" y="75" width="80" height="15" fill="none" stroke="url(#filter-mesh)" stroke-width="0.3" opacity="0.4" rx="3"/>
        <circle cx="20" cy="20" r="1" fill="url(#filter-mesh)" opacity="0.6"/>
        <circle cx="80" cy="80" r="1" fill="url(#filter-mesh)" opacity="0.6"/>
      </svg>
    </div>

    <!-- Floating Particles -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
      <div class="particle absolute w-1 h-1 bg-gradient-to-r from-violet-400 to-cyan-400 rounded-full opacity-30 animate-float-filter-1" style="left: 15%; top: 20%;"></div>
      <div class="particle absolute w-0.5 h-0.5 bg-gradient-to-r from-cyan-400 to-purple-400 rounded-full opacity-40 animate-float-filter-2" style="left: 80%; top: 30%;"></div>
      <div class="particle absolute w-1 h-1 bg-gradient-to-r from-purple-400 to-violet-400 rounded-full opacity-25 animate-float-filter-3" style="left: 25%; top: 70%;"></div>
      <div class="particle absolute w-0.5 h-0.5 bg-gradient-to-r from-violet-500 to-cyan-500 rounded-full opacity-35 animate-float-filter-4" style="left: 85%; top: 85%;"></div>
    </div>

    <!-- Header -->
    <div class="relative z-10 flex items-center justify-between mb-6">
      <h3 class="text-xl font-semibold bg-gradient-to-r from-white to-slate-200 bg-clip-text text-transparent animate-shimmer-title">
        Filters
      </h3>
      <button
        v-if="hasActiveFilters"
        @click="clearFilters"
        class="group text-sm font-medium text-violet-400 hover:text-violet-300 transition-all duration-300 hover:scale-105 relative"
      >
        <div class="absolute inset-0 rounded-lg bg-violet-500/0 group-hover:bg-violet-500/10 transition-all duration-300 -m-2 blur-sm"></div>
        <span class="relative">Clear all</span>
      </button>
    </div>

    <!-- Filters -->
    <div class="relative z-10 space-y-5">
      <!-- Search -->
      <div class="group">
        <label class="block text-sm font-medium text-slate-300 mb-2 group-focus-within:text-white transition-colors duration-300">
          Search
        </label>
        <div class="relative">
          <!-- Enhanced Search Icon -->
          <div class="absolute left-3 top-3 transition-all duration-300 group-focus-within:scale-110">
            <div class="absolute inset-0 rounded-full bg-violet-500/0 group-focus-within:bg-violet-500/20 blur-sm transition-all duration-300"></div>
            <MagnifyingGlassIcon class="relative h-4 w-4 text-slate-400 group-focus-within:text-violet-400" />
          </div>
          <input
            v-model="localFilters.search"
            type="text"
            placeholder="Search..."
            class="w-full pl-10 pr-4 py-3 bg-slate-800/50 backdrop-blur-sm border border-white/10 rounded-xl text-slate-200 placeholder-slate-400 focus:border-violet-500/50 focus:ring-2 focus:ring-violet-500/20 transition-all duration-300 hover:border-white/20"
          />
        </div>
      </div>

      <!-- Category/Type Filter -->
      <div v-if="categories && categories.length" class="group">
        <label class="block text-sm font-medium text-slate-300 mb-2 group-focus-within:text-white transition-colors duration-300">
          Category
        </label>
        <select
          v-model="localFilters.category"
          class="w-full bg-slate-800/50 backdrop-blur-sm border border-white/10 rounded-xl px-4 py-3 text-slate-200 focus:border-violet-500/50 focus:ring-2 focus:ring-violet-500/20 transition-all duration-300 hover:border-white/20"
        >
          <option value="">All Categories</option>
          <option
            v-for="category in categories"
            :key="category.value"
            :value="category.value"
          >
            {{ category.label }}
          </option>
        </select>
      </div>

      <!-- Privacy Filter -->
      <div class="group">
        <label class="block text-sm font-medium text-slate-300 mb-2 group-focus-within:text-white transition-colors duration-300">
          Privacy
        </label>
        <select
          v-model="localFilters.privacy"
          class="w-full bg-slate-800/50 backdrop-blur-sm border border-white/10 rounded-xl px-4 py-3 text-slate-200 focus:border-violet-500/50 focus:ring-2 focus:ring-violet-500/20 transition-all duration-300 hover:border-white/20"
        >
          <option value="">All</option>
          <option value="public">Public</option>
          <option value="unlisted">Unlisted</option>
          <option value="private">Private</option>
        </select>
      </div>

      <!-- Date Range -->
      <div class="group">
        <label class="block text-sm font-medium text-slate-300 mb-2 group-focus-within:text-white transition-colors duration-300">
          Date Range
        </label>
        <select
          v-model="localFilters.dateRange"
          class="w-full bg-slate-800/50 backdrop-blur-sm border border-white/10 rounded-xl px-4 py-3 text-slate-200 focus:border-violet-500/50 focus:ring-2 focus:ring-violet-500/20 transition-all duration-300 hover:border-white/20"
        >
          <option value="">All Time</option>
          <option value="today">Today</option>
          <option value="week">This Week</option>
          <option value="month">This Month</option>
          <option value="year">This Year</option>
        </select>
      </div>

      <!-- Sort Options -->
      <div class="group">
        <label class="block text-sm font-medium text-slate-300 mb-2 group-focus-within:text-white transition-colors duration-300">
          Sort By
        </label>
        <select
          v-model="localFilters.sortBy"
          class="w-full bg-slate-800/50 backdrop-blur-sm border border-white/10 rounded-xl px-4 py-3 text-slate-200 focus:border-violet-500/50 focus:ring-2 focus:ring-violet-500/20 transition-all duration-300 hover:border-white/20"
        >
          <option value="created_at">Date Created</option>
          <option value="updated_at">Last Updated</option>
          <option value="title">Title</option>
          <option value="views_count">Views</option>
          <option value="likes_count">Likes</option>
        </select>
      </div>

      <!-- Sort Direction -->
      <div class="group">
        <label class="block text-sm font-medium text-slate-300 mb-2 group-focus-within:text-white transition-colors duration-300">
          Order
        </label>
        <select
          v-model="localFilters.sortOrder"
          class="w-full bg-slate-800/50 backdrop-blur-sm border border-white/10 rounded-xl px-4 py-3 text-slate-200 focus:border-violet-500/50 focus:ring-2 focus:ring-violet-500/20 transition-all duration-300 hover:border-white/20"
        >
          <option value="desc">Newest First</option>
          <option value="asc">Oldest First</option>
        </select>
      </div>
    </div>

    <!-- Apply Button -->
    <div class="relative z-10 mt-8">
      <button
        @click="applyFilters"
        class="group w-full bg-gradient-to-r from-violet-600 to-cyan-600 text-white py-3 px-6 rounded-xl font-medium shadow-lg shadow-violet-500/20 transition-all duration-300 hover:scale-105 hover:from-violet-500 hover:to-cyan-500 hover:shadow-violet-500/30 relative overflow-hidden"
      >
        <!-- Button Shimmer Effect -->
        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-700"></div>
        <span class="relative">Apply Filters</span>
      </button>
    </div>

    <!-- Enhanced Hover Border Effect -->
    <div class="absolute inset-0 rounded-2xl bg-gradient-to-r from-violet-500/0 via-cyan-500/0 to-purple-500/0 hover:from-violet-500/5 hover:via-cyan-500/5 hover:to-purple-500/5 transition-all duration-700 pointer-events-none"></div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { MagnifyingGlassIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  filters: {
    type: Object,
    default: () => ({})
  },
  categories: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['update:filters'])

const localFilters = ref({
  search: '',
  category: '',
  privacy: '',
  dateRange: '',
  sortBy: 'created_at',
  sortOrder: 'desc',
  ...props.filters
})

const hasActiveFilters = computed(() => {
  return Object.values(localFilters.value).some(value => 
    value !== '' && value !== 'created_at' && value !== 'desc'
  )
})

const clearFilters = () => {
  localFilters.value = {
    search: '',
    category: '',
    privacy: '',
    dateRange: '',
    sortBy: 'created_at',
    sortOrder: 'desc'
  }
  applyFilters()
}

const applyFilters = () => {
  emit('update:filters', { ...localFilters.value })
}

// Auto-apply search filter with debounce
let searchTimeout = null
watch(() => localFilters.value.search, () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    applyFilters()
  }, 300)
})
</script>

<style scoped>
@keyframes float-filter {
  0%, 100% { transform: translateY(0px) rotate(0deg); }
  25% { transform: translateY(-2px) rotate(0.2deg); }
  50% { transform: translateY(-4px) rotate(0deg); }
  75% { transform: translateY(-2px) rotate(-0.2deg); }
}

@keyframes float-filter-1 {
  0%, 100% { transform: translate(0px, 0px); opacity: 0.3; }
  25% { transform: translate(4px, -4px); opacity: 0.6; }
  50% { transform: translate(8px, 2px); opacity: 0.2; }
  75% { transform: translate(2px, 6px); opacity: 0.4; }
}

@keyframes float-filter-2 {
  0%, 100% { transform: translate(0px, 0px); opacity: 0.4; }
  33% { transform: translate(-6px, 4px); opacity: 0.7; }
  66% { transform: translate(4px, -2px); opacity: 0.2; }
}

@keyframes float-filter-3 {
  0%, 100% { transform: translate(0px, 0px); opacity: 0.25; }
  40% { transform: translate(8px, -8px); opacity: 0.5; }
  80% { transform: translate(-4px, 4px); opacity: 0.1; }
}

@keyframes float-filter-4 {
  0%, 100% { transform: translate(0px, 0px); opacity: 0.35; }
  30% { transform: translate(-4px, -3px); opacity: 0.6; }
  70% { transform: translate(3px, 4px); opacity: 0.15; }
}

@keyframes shimmer-title {
  0% { background-position: -200% 0; }
  100% { background-position: 200% 0; }
}

@keyframes gradient-shift {
  0%, 100% { stop-color: #8b5cf6; }
  25% { stop-color: #06b6d4; }
  50% { stop-color: #a855f7; }
  75% { stop-color: #8b5cf6; }
}

.animate-float-filter {
  animation: float-filter 6s ease-in-out infinite;
}

.animate-float-filter-1 {
  animation: float-filter-1 7s ease-in-out infinite;
}

.animate-float-filter-2 {
  animation: float-filter-2 9s ease-in-out infinite 1s;
}

.animate-float-filter-3 {
  animation: float-filter-3 8s ease-in-out infinite 2s;
}

.animate-float-filter-4 {
  animation: float-filter-4 6s ease-in-out infinite 3s;
}

.animate-shimmer-title {
  background-image: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
  background-size: 200% 100%;
  animation: shimmer-title 4s ease-in-out infinite;
}

.animate-gradient-shift {
  animation: gradient-shift 8s ease-in-out infinite;
}
</style>