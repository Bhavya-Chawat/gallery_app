<template>
  <Link
    :href="href"
    :class="[
      'block w-full pl-3 pr-4 py-2 border-l-4 text-left text-base font-medium transition duration-150 ease-in-out focus:outline-none',
      active
        ? 'border-blue-400 text-blue-700 bg-blue-50 focus:text-blue-800 focus:bg-blue-100 focus:border-blue-700'
        : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300'
    ]"
  >
    <slot />
  </Link>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
  href: String,
  active: Boolean,
})
</script>
/*<template>
  <Link
    :href="href"
    :class="[
      'group relative block w-full pl-4 pr-6 py-3 border-l-4 text-left text-base font-medium transition-all duration-300 ease-out focus:outline-none overflow-hidden',
      active
        ? 'border-violet-400 text-transparent bg-clip-text bg-gradient-to-r from-violet-400 via-purple-400 to-cyan-400 bg-gradient-to-r from-violet-500/20 via-purple-500/20 to-cyan-500/20 backdrop-blur-sm focus:from-violet-400 focus:via-purple-400 focus:to-cyan-400 focus:bg-gradient-to-r focus:from-violet-500/30 focus:via-purple-500/30 focus:to-cyan-500/30 focus:border-violet-300 shadow-lg shadow-violet-500/20 animate-active-glow'
        : 'border-transparent text-slate-300 hover:text-transparent hover:bg-clip-text hover:bg-gradient-to-r hover:from-violet-400 hover:to-cyan-400 hover:bg-gradient-to-r hover:from-slate-800/50 hover:via-slate-800/50 hover:to-slate-800/50 hover:backdrop-blur-sm hover:border-violet-500/30 focus:text-transparent focus:bg-clip-text focus:bg-gradient-to-r focus:from-violet-400 focus:to-cyan-400 focus:bg-gradient-to-r focus:from-slate-800/50 focus:via-slate-800/50 focus:to-slate-800/50 focus:backdrop-blur-sm focus:border-violet-500/30 hover:shadow-md hover:shadow-violet-500/10'
    ]"
  >
    <!-- Animated mesh background -->
    <div class="absolute inset-0 opacity-20 overflow-hidden">
      <div :class="[
        'absolute inset-0 transition-all duration-500',
        active 
          ? 'bg-gradient-to-r from-violet-400/15 via-purple-400/15 to-cyan-400/15 animate-gradient-shift-active'
          : 'bg-gradient-to-r from-slate-700/10 via-slate-600/10 to-slate-700/10 group-hover:from-violet-400/10 group-hover:via-purple-400/10 group-hover:to-cyan-400/10 group-hover:animate-gradient-shift-hover'
      ]"></div>
    </div>

    <!-- Floating particles system -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
      <div v-for="i in 6" :key="i" 
           :class="[
             'absolute rounded-full opacity-0 group-hover:opacity-50 transition-opacity duration-300',
             active ? 'opacity-40' : '',
             active ? 'bg-violet-400' : 'bg-cyan-400'
           ]"
           :style="`
             left: ${15 + (i * 15)}%; 
             top: ${25 + (i * 12)}%; 
             width: ${1 + (i % 2)}px; 
             height: ${1 + (i % 2)}px;
             animation: float-responsive-${i % 3} ${4 + i}s ease-in-out infinite;
             animation-delay: ${i * 0.3}s;
           `">
      </div>
    </div>

    <!-- Shimmer effect -->
    <div :class="[
      'absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-700 ease-out',
      active ? 'via-violet-300/20 translate-x-full animate-shimmer-active' : 'via-cyan-300/15'
    ]"></div>

    <!-- Active state indicator -->
    <div v-if="active" class="absolute left-0 top-0 w-1 h-full bg-gradient-to-b from-violet-500 via-purple-500 to-cyan-500 animate-pulse-indicator"></div>

    <!-- Glassmorphism overlay -->
    <div :class="[
      'absolute inset-0 backdrop-blur-sm transition-all duration-300',
      active 
        ? 'bg-slate-800/30 opacity-100' 
        : 'bg-slate-800/20 opacity-0 group-hover:opacity-100'
    ]"></div>

    <!-- Enhanced border gradient -->
    <div :class="[
      'absolute left-0 top-0 w-1 h-full transition-all duration-300',
      active 
        ? 'bg-gradient-to-b from-violet-400 via-purple-400 to-cyan-400 animate-border-glow' 
        : 'bg-transparent group-hover:bg-gradient-to-b group-hover:from-violet-500 group-hover:to-cyan-500'
    ]"></div>

    <!-- Content with enhanced styling -->
    <div class="relative z-10 flex items-center space-x-3">
      <!-- Active state dot -->
      <div :class="[
        'w-2 h-2 rounded-full transition-all duration-300',
        active 
          ? 'bg-gradient-to-r from-violet-400 to-cyan-400 animate-pulse-dot scale-100' 
          : 'bg-violet-500 scale-0 group-hover:scale-100 opacity-60'
      ]"></div>

      <!-- Text content -->
      <span :class="[
        'flex-1 transition-all duration-300 transform',
        active 
          ? 'font-semibold drop-shadow-sm' 
          : 'group-hover:font-medium group-hover:translate-x-1'
      ]">
        <slot />
      </span>

      <!-- Hover state accent -->
      <div :class="[
        'w-1 h-1 rounded-full transition-all duration-300',
        active 
          ? 'bg-cyan-400 opacity-60 animate-pulse' 
          : 'bg-violet-400 opacity-0 group-hover:opacity-60 group-hover:scale-125'
      ]"></div>
    </div>

    <!-- Focus ring enhancement -->
    <div :class="[
      'absolute inset-0 ring-0 transition-all duration-300 pointer-events-none',
      active 
        ? 'ring-2 ring-violet-500/40 shadow-sm shadow-violet-500/20' 
        : 'group-focus:ring-2 group-focus:ring-violet-500/30'
    ]"></div>

    <!-- Click ripple effect -->
    <div class="absolute inset-0 bg-gradient-to-r from-violet-400/20 to-cyan-400/20 scale-0 group-active:scale-100 group-active:opacity-30 opacity-0 transition-all duration-200 ease-out"></div>

    <!-- Corner decorative elements -->
    <div class="absolute top-2 right-2 w-3 h-3 opacity-0 group-hover:opacity-30 transition-opacity duration-300">
      <div class="absolute top-0 right-0 w-0.5 h-0.5 bg-violet-400 rounded-full animate-pulse"></div>
      <div class="absolute top-1 right-1 w-0.5 h-0.5 bg-cyan-400 rounded-full animate-pulse" style="animation-delay: 0.5s;"></div>
      <div class="absolute top-2 right-2 w-0.5 h-0.5 bg-purple-400 rounded-full animate-pulse" style="animation-delay: 1s;"></div>
    </div>

    <!-- Bottom gradient line -->
    <div :class="[
      'absolute bottom-0 left-4 right-4 h-px transition-all duration-300',
      active 
        ? 'bg-gradient-to-r from-violet-500/40 via-purple-500/40 to-cyan-500/40 animate-pulse-line' 
        : 'bg-gradient-to-r from-transparent via-violet-500/20 to-transparent opacity-0 group-hover:opacity-100'
    ]"></div>
  </Link>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
  href: String,
  active: Boolean,
})
</script>

<style scoped>
/* Active glow animation */
@keyframes active-glow {
  0%, 100% { 
    box-shadow: 0 0 10px rgba(139, 92, 246, 0.3);
    background: linear-gradient(90deg, rgba(139, 92, 246, 0.2), rgba(168, 85, 247, 0.2), rgba(6, 182, 212, 0.2));
  }
  50% { 
    box-shadow: 0 0 20px rgba(139, 92, 246, 0.5), 0 0 30px rgba(168, 85, 247, 0.3);
    background: linear-gradient(90deg, rgba(139, 92, 246, 0.25), rgba(168, 85, 247, 0.25), rgba(6, 182, 212, 0.25));
  }
}

.animate-active-glow {
  animation: active-glow 3s ease-in-out infinite;
}

/* Gradient shift animations */
@keyframes gradient-shift-active {
  0%, 100% { 
    background: linear-gradient(90deg, rgba(139, 92, 246, 0.15), rgba(168, 85, 247, 0.15), rgba(6, 182, 212, 0.15));
  }
  50% { 
    background: linear-gradient(90deg, rgba(168, 85, 247, 0.2), rgba(6, 182, 212, 0.2), rgba(139, 92, 246, 0.2));
  }
}

@keyframes gradient-shift-hover {
  0%, 100% { 
    background: linear-gradient(90deg, rgba(139, 92, 246, 0.1), rgba(168, 85, 247, 0.1), rgba(6, 182, 212, 0.1));
  }
  50% { 
    background: linear-gradient(90deg, rgba(168, 85, 247, 0.15), rgba(6, 182, 212, 0.15), rgba(139, 92, 246, 0.15));
  }
}

.animate-gradient-shift-active {
  animation: gradient-shift-active 4s ease-in-out infinite;
}

.animate-gradient-shift-hover {
  animation: gradient-shift-hover 3s ease-in-out infinite;
}

/* Floating responsive particles */
@keyframes float-responsive-0 {
  0%, 100% { transform: translateY(0px) translateX(0px) rotate(0deg); opacity: 0.3; }
  50% { transform: translateY(-10px) translateX(5px) rotate(180deg); opacity: 0.7; }
}

@keyframes float-responsive-1 {
  0%, 100% { transform: translateY(0px) translateX(0px) rotate(0deg); opacity: 0.4; }
  50% { transform: translateY(-8px) translateX(-4px) rotate(-180deg); opacity: 0.8; }
}

@keyframes float-responsive-2 {
  0%, 100% { transform: translateY(0px) translateX(0px) rotate(0deg); opacity: 0.5; }
  50% { transform: translateY(-12px) translateX(3px) rotate(270deg); opacity: 0.6; }
}

/* Active shimmer animation */
@keyframes shimmer-active {
  0% { transform: translateX(-100%); opacity: 0.3; }
  50% { opacity: 0.7; }
  100% { transform: translateX(200%); opacity: 0.3; }
}

.animate-shimmer-active {
  animation: shimmer-active 2s ease-in-out infinite;
}

/* Pulse indicator animation */
@keyframes pulse-indicator {
  0%, 100% { 
    opacity: 0.8; 
    transform: scaleY(1);
  }
  50% { 
    opacity: 1; 
    transform: scaleY(1.05);
  }
}

.animate-pulse-indicator {
  animation: pulse-indicator 2s ease-in-out infinite;
}

/* Border glow animation */
@keyframes border-glow {
  0%, 100% { 
    box-shadow: 0 0 5px rgba(139, 92, 246, 0.5);
    opacity: 0.8;
  }
  50% { 
    box-shadow: 0 0 15px rgba(139, 92, 246, 0.8), 0 0 25px rgba(6, 182, 212, 0.4);
    opacity: 1;
  }
}

.animate-border-glow {
  animation: border-glow 2.5s ease-in-out infinite;
}

/* Pulse dot animation */
@keyframes pulse-dot {
  0%, 100% { 
    transform: scale(1);
    opacity: 0.8;
  }
  50% { 
    transform: scale(1.2);
    opacity: 1;
  }
}

.animate-pulse-dot {
  animation: pulse-dot 1.8s ease-in-out infinite;
}

/* Pulse line animation */
@keyframes pulse-line {
  0%, 100% { opacity: 0.4; transform: scaleX(1); }
  50% { opacity: 0.8; transform: scaleX(1.02); }
}

.animate-pulse-line {
  animation: pulse-line 3s ease-in-out infinite;
}

/* Enhanced hover effects */
.group:hover .animate-gradient-shift-hover {
  animation-duration: 2s;
}

/* Focus enhancements */
.group:focus-visible {
  outline: 2px solid rgba(139, 92, 246, 0.5);
  outline-offset: -2px;
}

/* Responsive adjustments */
@media (max-width: 640px) {
  .group {
    padding-left: 1rem;
    padding-right: 1.25rem;
    padding-top: 0.625rem;
    padding-bottom: 0.625rem;
  }
  
  .group:hover span {
    transform: translateX(0.25rem);
  }
}

/* High contrast mode support */
@media (prefers-contrast: high) {
  .text-slate-300 {
    color: white !important;
  }
  
  .group:hover,
  .group:focus {
    background-color: rgba(139, 92, 246, 0.3) !important;
    color: white !important;
    border-left-color: white !important;
  }
  
  .border-violet-400 {
    border-left-color: white !important;
    border-left-width: 6px !important;
  }
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
  .group, .group *,
  .animate-active-glow,
  .animate-gradient-shift-active,
  .animate-gradient-shift-hover,
  .animate-shimmer-active,
  .animate-pulse-indicator,
  .animate-border-glow,
  .animate-pulse-dot,
  .animate-pulse-line {
    animation: none !important;
    transition-duration: 0.01ms !important;
  }
  
  .group:hover span {
    transform: none !important;
  }
}

/* Dark theme optimization */
@media (prefers-color-scheme: dark) {
  .backdrop-blur-sm {
    background-color: rgba(30, 41, 59, 0.4);
  }
  
  .text-slate-300 {
    color: rgb(203 213 225);
  }
}

/* Loading state */
.group.loading {
  opacity: 0.7;
  pointer-events: none;
}

.group.loading .animate-pulse-indicator {
  animation-duration: 1s;
}
</style>