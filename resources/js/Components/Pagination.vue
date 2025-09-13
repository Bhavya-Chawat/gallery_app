
<template>
  <nav class="relative flex items-center justify-between border-t border-violet-500/20 bg-slate-900/50 backdrop-blur-sm px-4 py-4 sm:px-6 overflow-hidden" aria-label="Pagination">
    <!-- Animated mesh background -->
    <div class="absolute inset-0 opacity-10 overflow-hidden">
      <div class="absolute inset-0 bg-gradient-to-r from-violet-400/20 via-purple-400/20 to-cyan-400/20 animate-gradient-shift"></div>
      <div class="absolute top-0 left-0 w-full h-full">
        <div v-for="i in 12" :key="i" 
             :class="`absolute rounded-full bg-gradient-to-r from-violet-400 to-cyan-400 opacity-30 animate-float-pagination-${i % 4}`"
             :style="`
               left: ${(i * 8.5) % 100}%; 
               top: ${(i * 7) % 100}%; 
               width: ${1 + (i % 2)}px; 
               height: ${1 + (i % 2)}px;
               animation-delay: ${i * 0.4}s;
             `">
        </div>
      </div>
    </div>

    <!-- Results info with enhanced styling -->
    <div class="hidden sm:block relative z-10">
      <p class="text-sm text-slate-300 font-medium">
        Showing
        <span class="font-bold text-transparent bg-clip-text bg-gradient-to-r from-violet-400 to-cyan-400 animate-number-glow">{{ meta.from || 0 }}</span>
        to
        <span class="font-bold text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-violet-400 animate-number-glow" style="animation-delay: 0.2s;">{{ meta.to || 0 }}</span>
        of
        <span class="font-bold text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-purple-400 animate-number-glow" style="animation-delay: 0.4s;">{{ meta.total || 0 }}</span>
        results
      </p>
    </div>

    <!-- Navigation controls -->
    <div class="flex flex-1 justify-between sm:justify-end relative z-10">
      <!-- Previous Button -->
      <Link
        v-if="links.find(l => (l.label || '').includes('Previous') || (l.label || '').includes('&laquo;'))?.url"
        :href="links.find(l => (l.label || '').includes('Previous') || (l.label || '').includes('&laquo;')).url"
        class="group relative inline-flex items-center rounded-lg border border-violet-500/30 bg-slate-800/50 backdrop-blur-sm px-4 py-2 text-sm font-medium text-slate-300 hover:text-white hover:bg-gradient-to-r hover:from-violet-500/20 hover:to-purple-500/20 hover:border-violet-400/50 transition-all duration-300 hover:scale-105 hover:shadow-lg hover:shadow-violet-500/25 overflow-hidden"
      >
        <!-- Button shimmer effect -->
        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-700 ease-out"></div>
        
        <!-- Icon with glow -->
        <div class="relative">
          <ChevronLeftIcon class="h-5 w-5 mr-2 transform group-hover:-translate-x-1 transition-transform duration-300" />
          <div class="absolute inset-0 bg-violet-400/30 blur-md rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
        </div>
        
        <span class="relative">Previous</span>
      </Link>

      <!-- Page Numbers (for larger screens) -->
      <div class="hidden md:flex space-x-1 mx-4">
        <button
          v-for="(link, index) in pageLinks"
          :key="link.label"
          @click="navigate(link.url)"
          :disabled="!link.url"
          :class="[
            'group relative inline-flex items-center px-4 py-2 text-sm font-medium border rounded-lg transition-all duration-300 overflow-hidden',
            link.active
              ? 'bg-gradient-to-r from-violet-500/20 to-purple-500/20 border-violet-400/50 text-white z-10 shadow-lg shadow-violet-500/25 animate-active-page'
              : 'bg-slate-800/50 backdrop-blur-sm border-slate-600/30 text-slate-300 hover:text-white hover:bg-gradient-to-r hover:from-violet-500/10 hover:to-cyan-500/10 hover:border-violet-500/30 hover:scale-105 hover:shadow-md hover:shadow-violet-500/20',
            !link.url && 'cursor-not-allowed opacity-50 hover:scale-100'
          ]"
          :style="`animation-delay: ${index * 0.1}s;`"
          
        >
          <!-- Active page glow -->
          <div v-if="link.active" class="absolute inset-0 bg-gradient-to-r from-violet-400/20 via-purple-400/20 to-cyan-400/20 blur-sm animate-pulse"></div>
          
          <!-- Hover shimmer -->
          <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/5 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-500 ease-out"></div>
        </button>
      </div>

      <!-- Next Button -->
      <Link
        v-if="links.find(l => (l.label || '').includes('Next') || (l.label || '').includes('&raquo;'))?.url"
        :href="links.find(l => (l.label || '').includes('Next') || (l.label || '').includes('&raquo;')).url"
        class="group relative ml-3 inline-flex items-center rounded-lg border border-violet-500/30 bg-slate-800/50 backdrop-blur-sm px-4 py-2 text-sm font-medium text-slate-300 hover:text-white hover:bg-gradient-to-r hover:from-purple-500/20 hover:to-cyan-500/20 hover:border-violet-400/50 transition-all duration-300 hover:scale-105 hover:shadow-lg hover:shadow-violet-500/25 overflow-hidden"
      >
        <!-- Button shimmer effect -->
        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-700 ease-out"></div>
        
        <span class="relative">Next</span>
        
        <!-- Icon with glow -->
        <div class="relative">
          <ChevronRightIcon class="h-5 w-5 ml-2 transform group-hover:translate-x-1 transition-transform duration-300" />
          <div class="absolute inset-0 bg-cyan-400/30 blur-md rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
        </div>
      </Link>
    </div>

    <!-- Decorative accent elements -->
    <div class="absolute bottom-0 left-0 w-full h-px bg-gradient-to-r from-transparent via-violet-500/50 to-transparent animate-pulse-line"></div>
    
    <!-- Corner accents -->
    <div class="absolute top-2 left-2 w-2 h-2 opacity-40">
      <div class="absolute top-0 left-0 w-1 h-1 bg-violet-400 rounded-full animate-pulse"></div>
      <div class="absolute top-1 left-1 w-0.5 h-0.5 bg-cyan-400 rounded-full animate-pulse" style="animation-delay: 0.5s;"></div>
    </div>
    <div class="absolute top-2 right-2 w-2 h-2 opacity-40">
      <div class="absolute top-0 right-0 w-1 h-1 bg-purple-400 rounded-full animate-pulse" style="animation-delay: 0.25s;"></div>
      <div class="absolute top-1 right-1 w-0.5 h-0.5 bg-violet-400 rounded-full animate-pulse" style="animation-delay: 0.75s;"></div>
    </div>
  </nav>
</template>

<script setup>
import { computed } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import {
  ChevronLeftIcon,
  ChevronRightIcon,
} from '@heroicons/vue/20/solid'

const props = defineProps({
  links: {
    type: Object,
    required: true
  },
  meta: {
    type: Object,
    required: true
  }
})

const pageLinks = computed(() => {
  // Check if links exists and is an array
  if (!props.links || !Array.isArray(props.links)) {
    return []
  }
  
  // Filter out previous/next buttons to get just page numbers
  return props.links.filter(link => {
    const label = link.label || ''
    return !label.includes('Previous') && !label.includes('Next') && !label.includes('&laquo;') && !label.includes('&raquo;')
  })
})

const navigate = (url) => {
  if (url) {
    router.visit(url, {
      preserveState: true,
      preserveScroll: true,
    })
  }
}
</script>

<style scoped>
/* Gradient shift animation for background */
@keyframes gradient-shift {
  0%, 100% { 
    background: linear-gradient(90deg, rgba(139, 92, 246, 0.2), rgba(168, 85, 247, 0.2), rgba(6, 182, 212, 0.2));
  }
  33% { 
    background: linear-gradient(90deg, rgba(168, 85, 247, 0.25), rgba(6, 182, 212, 0.25), rgba(139, 92, 246, 0.25));
  }
  66% { 
    background: linear-gradient(90deg, rgba(6, 182, 212, 0.2), rgba(139, 92, 246, 0.2), rgba(168, 85, 247, 0.2));
  }
}

.animate-gradient-shift {
  animation: gradient-shift 8s ease-in-out infinite;
}

/* Floating pagination particles */
@keyframes float-pagination-0 {
  0%, 100% { transform: translateY(0px) translateX(0px) rotate(0deg); opacity: 0.3; }
  50% { transform: translateY(-15px) translateX(8px) rotate(180deg); opacity: 0.7; }
}

@keyframes float-pagination-1 {
  0%, 100% { transform: translateY(0px) translateX(0px) rotate(0deg); opacity: 0.4; }
  50% { transform: translateY(-12px) translateX(-6px) rotate(-180deg); opacity: 0.8; }
}

@keyframes float-pagination-2 {
  0%, 100% { transform: translateY(0px) translateX(0px) rotate(0deg); opacity: 0.5; }
  50% { transform: translateY(-18px) translateX(4px) rotate(270deg); opacity: 0.6; }
}

@keyframes float-pagination-3 {
  0%, 100% { transform: translateY(0px) translateX(0px) rotate(0deg); opacity: 0.3; }
  50% { transform: translateY(-10px) translateX(-10px) rotate(-270deg); opacity: 0.9; }
}

.animate-float-pagination-0 { animation: float-pagination-0 6s ease-in-out infinite; }
.animate-float-pagination-1 { animation: float-pagination-1 7s ease-in-out infinite; }
.animate-float-pagination-2 { animation: float-pagination-2 8s ease-in-out infinite; }
.animate-float-pagination-3 { animation: float-pagination-3 5s ease-in-out infinite; }

/* Number glow animation */
@keyframes number-glow {
  0%, 100% { 
    text-shadow: 0 0 5px rgba(139, 92, 246, 0.5);
    transform: scale(1);
  }
  50% { 
    text-shadow: 0 0 15px rgba(139, 92, 246, 0.8), 0 0 25px rgba(6, 182, 212, 0.4);
    transform: scale(1.05);
  }
}

.animate-number-glow {
  animation: number-glow 3s ease-in-out infinite;
}

/* Active page animation */
@keyframes active-page {
  0%, 100% { 
    transform: scale(1);
    box-shadow: 0 0 10px rgba(139, 92, 246, 0.3);
  }
  50% { 
    transform: scale(1.02);
    box-shadow: 0 0 20px rgba(139, 92, 246, 0.5), 0 0 30px rgba(168, 85, 247, 0.3);
  }
}

.animate-active-page {
  animation: active-page 2s ease-in-out infinite;
}

/* Pulse line animation */
@keyframes pulse-line {
  0%, 100% { opacity: 0.3; transform: scaleX(1); }
  50% { opacity: 0.8; transform: scaleX(1.02); }
}

.animate-pulse-line {
  animation: pulse-line 4s ease-in-out infinite;
}

/* Enhanced hover effects */
.group:hover .animate-float-pagination-0,
.group:hover .animate-float-pagination-1,
.group:hover .animate-float-pagination-2,
.group:hover .animate-float-pagination-3 {
  animation-duration: 3s;
}

/* Button click effects */
.group:active {
  transform: scale(0.98) !important;
}

/* Focus accessibility */
button:focus-visible,
a:focus-visible {
  outline: 2px solid rgba(139, 92, 246, 0.5);
  outline-offset: 2px;
  border-radius: 8px;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .animate-gradient-shift {
    animation-duration: 6s;
  }
  
  .animate-number-glow {
    animation-duration: 2s;
  }
  
  .group:hover {
    transform: scale(1.02);
  }
}

/* High contrast mode support */
@media (prefers-contrast: high) {
  .bg-slate-900\/50 {
    background-color: rgb(0 0 0 / 0.9) !important;
  }
  
  .text-slate-300 {
    color: white !important;
  }
  
  .border-violet-500\/20,
  .border-violet-500\/30 {
    border-color: rgb(139 92 246 / 0.8) !important;
    border-width: 2px !important;
  }
  
  .group:hover {
    background-color: rgba(139, 92, 246, 0.3) !important;
    color: white !important;
  }
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
  .animate-gradient-shift,
  .animate-float-pagination-0,
  .animate-float-pagination-1,
  .animate-float-pagination-2,
  .animate-float-pagination-3,
  .animate-number-glow,
  .animate-active-page,
  .animate-pulse-line {
    animation: none !important;
    transition-duration: 0.01ms !important;
  }
  
  .group:hover {
    transform: none !important;
  }
  
  .backdrop-blur-sm {
    backdrop-filter: none !important;
  }
}

/* Loading states */
.group:disabled {
  cursor: not-allowed;
  opacity: 0.5;
}

.group:disabled:hover {
  transform: none !important;
  background: inherit !important;
  border-color: inherit !important;
  box-shadow: none !important;
}
</style>