<template>
  <div class="group relative bg-slate-900/95 backdrop-blur-xl border border-violet-500/20 rounded-2xl p-6 shadow-2xl shadow-violet-500/10 hover:shadow-violet-500/20 transition-all duration-500 ease-out hover:scale-[1.02] overflow-hidden">
    <!-- Animated mesh background -->
    <div class="absolute inset-0 opacity-20 overflow-hidden rounded-2xl">
      <div class="absolute inset-0 bg-gradient-to-br from-violet-400/10 via-purple-400/10 to-cyan-400/10 animate-gradient-shift"></div>
      <div class="absolute top-0 left-0 w-full h-full">
        <div v-for="i in 12" :key="i" 
             :class="`absolute rounded-full bg-gradient-to-r from-violet-400 to-cyan-400 opacity-30 animate-float-storage-${i % 4}`"
             :style="`
               left: ${(i * 8) % 100}%; 
               top: ${(i * 9) % 100}%; 
               width: ${1 + (i % 2)}px; 
               height: ${1 + (i % 2)}px;
               animation-delay: ${i * 0.4}s;
             `">
        </div>
      </div>
    </div>

    <!-- Glassmorphism overlay -->
    <div class="absolute inset-0 bg-gradient-to-br from-slate-800/30 via-slate-900/30 to-slate-800/30 backdrop-blur-sm rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

    <!-- Shimmer effect -->
    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000 ease-out rounded-2xl"></div>

    <!-- Corner accent elements -->
    <div class="absolute top-3 left-3 w-3 h-3 opacity-60">
      <div class="absolute top-0 left-0 w-1 h-1 bg-violet-400 rounded-full animate-pulse"></div>
      <div class="absolute top-1 left-1 w-0.5 h-0.5 bg-cyan-400 rounded-full animate-pulse" style="animation-delay: 0.5s;"></div>
    </div>
    <div class="absolute top-3 right-3 w-3 h-3 opacity-60">
      <div class="absolute top-0 right-0 w-1 h-1 bg-purple-400 rounded-full animate-pulse" style="animation-delay: 0.25s;"></div>
      <div class="absolute top-1 right-1 w-0.5 h-0.5 bg-violet-400 rounded-full animate-pulse" style="animation-delay: 0.75s;"></div>
    </div>

    <div class="relative z-10">
      <!-- Header with enhanced styling -->
      <div class="flex items-center justify-between mb-4 animate-fade-in-up">
        <h4 class="text-sm font-semibold text-transparent bg-clip-text bg-gradient-to-r from-violet-400 via-purple-400 to-cyan-400 animate-shimmer-text">
          Storage Usage
        </h4>
        <span :class="[
          'text-sm font-bold transition-all duration-300',
          percentage >= 90 ? 'text-red-400 animate-pulse-warning' :
          percentage >= 75 ? 'text-amber-400' :
          percentage >= 50 ? 'text-cyan-400' :
          'text-emerald-400'
        ]">
          {{ percentage.toFixed(1) }}%
        </span>
      </div>

      <!-- Enhanced progress bar container -->
      <div class="relative w-full bg-slate-800/50 backdrop-blur-sm rounded-full h-3 mb-4 border border-slate-700/30 shadow-inner shadow-slate-900/50 animate-fade-in-up" style="animation-delay: 0.1s;">
        <!-- Progress bar glow background -->
        <div class="absolute inset-0 rounded-full bg-gradient-to-r from-violet-500/20 via-purple-500/20 to-cyan-500/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
        
        <!-- Main progress bar -->
        <div
          :class="[
            'relative h-3 rounded-full transition-all duration-500 ease-out overflow-hidden',
            percentage >= 90 ? 'bg-gradient-to-r from-red-500 via-red-400 to-rose-400 animate-pulse-critical' :
            percentage >= 75 ? 'bg-gradient-to-r from-amber-500 via-yellow-400 to-amber-400' :
            percentage >= 50 ? 'bg-gradient-to-r from-cyan-500 via-blue-400 to-cyan-400' :
            'bg-gradient-to-r from-emerald-500 via-green-400 to-emerald-400'
          ]"
          :style="{ width: Math.min(percentage, 100) + '%' }"
        >
          <!-- Progress bar shimmer -->
          <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/30 to-transparent -translate-x-full animate-progress-shimmer"></div>
          
          <!-- Progress bar glow -->
          <div :class="[
            'absolute inset-0 rounded-full blur-sm opacity-50',
            percentage >= 90 ? 'bg-red-400' :
            percentage >= 75 ? 'bg-amber-400' :
            percentage >= 50 ? 'bg-cyan-400' :
            'bg-emerald-400'
          ]"></div>
        </div>
        
        <!-- Progress indicator dot -->
        <div 
          :class="[
            'absolute top-1/2 -translate-y-1/2 w-2 h-2 rounded-full border-2 border-slate-900 transition-all duration-500 ease-out',
            percentage >= 90 ? 'bg-red-400 shadow-lg shadow-red-400/50' :
            percentage >= 75 ? 'bg-amber-400 shadow-lg shadow-amber-400/50' :
            percentage >= 50 ? 'bg-cyan-400 shadow-lg shadow-cyan-400/50' :
            'bg-emerald-400 shadow-lg shadow-emerald-400/50'
          ]"
          :style="{ left: Math.min(percentage, 100) + '%', transform: 'translateX(-50%) translateY(-50%)' }"
        ></div>
      </div>

      <!-- Usage statistics with enhanced styling -->
      <div class="flex justify-between text-xs font-medium animate-fade-in-up" style="animation-delay: 0.2s;">
        <span class="text-slate-400 group-hover:text-slate-300 transition-colors duration-300">
          <span class="text-violet-400 font-semibold">{{ formatBytes(used) }}</span> used
        </span>
        <span class="text-slate-400 group-hover:text-slate-300 transition-colors duration-300">
          <span class="text-cyan-400 font-semibold">{{ formatBytes(quota) }}</span> total
        </span>
      </div>

      <!-- Enhanced details section -->
      <div v-if="showDetails" class="mt-4 p-3 bg-slate-800/30 backdrop-blur-sm rounded-lg border border-violet-500/10 animate-fade-in-up" style="animation-delay: 0.3s;">
        <div class="flex items-center justify-between text-xs">
          <span class="text-slate-400">Remaining:</span>
          <span :class="[
            'font-semibold transition-colors duration-300',
            (quota - used) / quota < 0.1 ? 'text-red-400' :
            (quota - used) / quota < 0.25 ? 'text-amber-400' :
            'text-emerald-400'
          ]">
            {{ formatBytes(quota - used) }}
          </span>
        </div>
        
        <!-- Storage health indicator -->
        <div class="mt-2 flex items-center space-x-2">
          <div :class="[
            'w-2 h-2 rounded-full animate-pulse',
            percentage >= 90 ? 'bg-red-400' :
            percentage >= 75 ? 'bg-amber-400' :
            percentage >= 50 ? 'bg-cyan-400' :
            'bg-emerald-400'
          ]"></div>
          <span :class="[
            'text-xs font-medium',
            percentage >= 90 ? 'text-red-400' :
            percentage >= 75 ? 'text-amber-400' :
            percentage >= 50 ? 'text-cyan-400' :
            'text-emerald-400'
          ]">
            {{ 
              percentage >= 90 ? 'Critical Usage' :
              percentage >= 75 ? 'High Usage' :
              percentage >= 50 ? 'Moderate Usage' :
              'Good Storage'
            }}
          </span>
        </div>
      </div>
    </div>

    <!-- Bottom accent line -->
    <div class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-violet-500/30 via-purple-500/30 to-cyan-500/30 rounded-b-2xl animate-pulse-line"></div>
  </div>
</template>

<script setup>
const props = defineProps({
  used: { type: Number, default: 0 },
  quota: { type: Number, default: 1073741824 },
  percentage: { type: Number, default: 0 },
  showDetails: { type: Boolean, default: false },
})

const formatBytes = (bytes) => {
  if (bytes === 0) return '0 B'
  const k = 1024
  const sizes = ['B', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}
</script>

<style scoped>
/* Gradient shift animation */
@keyframes gradient-shift {
  0%, 100% { 
    background: linear-gradient(135deg, rgba(139, 92, 246, 0.1), rgba(168, 85, 247, 0.1), rgba(6, 182, 212, 0.1));
  }
  33% { 
    background: linear-gradient(135deg, rgba(168, 85, 247, 0.15), rgba(6, 182, 212, 0.15), rgba(139, 92, 246, 0.15));
  }
  66% { 
    background: linear-gradient(135deg, rgba(6, 182, 212, 0.12), rgba(139, 92, 246, 0.12), rgba(168, 85, 247, 0.12));
  }
}

.animate-gradient-shift {
  animation: gradient-shift 8s ease-in-out infinite;
}

/* Floating storage particles */
@keyframes float-storage-0 {
  0%, 100% { transform: translateY(0px) translateX(0px) rotate(0deg); opacity: 0.3; }
  50% { transform: translateY(-12px) translateX(6px) rotate(180deg); opacity: 0.7; }
}

@keyframes float-storage-1 {
  0%, 100% { transform: translateY(0px) translateX(0px) rotate(0deg); opacity: 0.4; }
  50% { transform: translateY(-10px) translateX(-5px) rotate(-180deg); opacity: 0.8; }
}

@keyframes float-storage-2 {
  0%, 100% { transform: translateY(0px) translateX(0px) rotate(0deg); opacity: 0.5; }
  50% { transform: translateY(-15px) translateX(3px) rotate(270deg); opacity: 0.6; }
}

@keyframes float-storage-3 {
  0%, 100% { transform: translateY(0px) translateX(0px) rotate(0deg); opacity: 0.3; }
  50% { transform: translateY(-8px) translateX(-8px) rotate(-270deg); opacity: 0.9; }
}

.animate-float-storage-0 { animation: float-storage-0 6s ease-in-out infinite; }
.animate-float-storage-1 { animation: float-storage-1 7s ease-in-out infinite; }
.animate-float-storage-2 { animation: float-storage-2 8s ease-in-out infinite; }
.animate-float-storage-3 { animation: float-storage-3 5s ease-in-out infinite; }

/* Text shimmer animation */
@keyframes shimmer-text {
  0% { background-position: -200% center; }
  100% { background-position: 200% center; }
}

.animate-shimmer-text {
  background-size: 200% 100%;
  animation: shimmer-text 3s ease-in-out infinite;
}

/* Progress bar shimmer */
@keyframes progress-shimmer {
  0% { transform: translateX(-100%); }
  100% { transform: translateX(100%); }
}

.animate-progress-shimmer {
  animation: progress-shimmer 2s ease-in-out infinite;
}

/* Warning pulse for high usage */
@keyframes pulse-warning {
  0%, 100% { opacity: 1; transform: scale(1); }
  50% { opacity: 0.7; transform: scale(1.05); }
}

.animate-pulse-warning {
  animation: pulse-warning 1.5s ease-in-out infinite;
}

/* Critical pulse for progress bar */
@keyframes pulse-critical {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.8; }
}

.animate-pulse-critical {
  animation: pulse-critical 1s ease-in-out infinite;
}

/* Fade in up animation */
@keyframes fade-in-up {
  0% { opacity: 0; transform: translateY(10px); }
  100% { opacity: 1; transform: translateY(0); }
}

.animate-fade-in-up {
  animation: fade-in-up 0.6s ease-out forwards;
  opacity: 0;
}

/* Pulse line animation */
@keyframes pulse-line {
  0%, 100% { opacity: 0.3; transform: scaleX(1); }
  50% { opacity: 0.7; transform: scaleX(1.02); }
}

.animate-pulse-line {
  animation: pulse-line 4s ease-in-out infinite;
}

/* Enhanced hover effects */
.group:hover .animate-float-storage-0,
.group:hover .animate-float-storage-1,
.group:hover .animate-float-storage-2,
.group:hover .animate-float-storage-3 {
  animation-duration: 4s;
}

.group:hover .animate-progress-shimmer {
  animation-duration: 1.5s;
}

/* Responsive adjustments */
@media (max-width: 640px) {
  .group {
    padding: 1rem;
  }
  
  .group:hover {
    transform: scale(1.01);
  }
}

/* High contrast mode support */
@media (prefers-contrast: high) {
  .bg-slate-900\/95 {
    background-color: rgb(0 0 0 / 0.98) !important;
  }
  
  .text-slate-400 {
    color: rgb(203 213 225) !important;
  }
  
  .border-violet-500\/20 {
    border-color: rgb(139 92 246 / 0.8) !important;
    border-width: 2px !important;
  }
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
  .group, .group *,
  .animate-gradient-shift,
  .animate-float-storage-0,
  .animate-float-storage-1,
  .animate-float-storage-2,
  .animate-float-storage-3,
  .animate-shimmer-text,
  .animate-progress-shimmer,
  .animate-pulse-warning,
  .animate-pulse-critical,
  .animate-fade-in-up,
  .animate-pulse-line {
    animation: none !important;
    transition-duration: 0.01ms !important;
  }
  
  .group:hover {
    transform: none !important;
  }
}

/* Dark theme optimization */
@media (prefers-color-scheme: dark) {
  .bg-slate-800\/50 {
    background-color: rgba(30, 41, 59, 0.6);
  }
}
</style>