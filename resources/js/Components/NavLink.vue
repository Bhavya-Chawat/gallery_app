<template>
  <Link
    :href="href"
    :class="[
      'inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out focus:outline-none',
      active
        ? 'border-blue-400 text-gray-900 focus:border-blue-700'
        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:text-gray-700 focus:border-gray-300'
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
/* <template>
  <Link
    :href="href"
    :class="[
      'group relative inline-flex items-center px-3 pt-3 pb-2 text-sm font-medium leading-5 transition-all duration-300 ease-out focus:outline-none overflow-hidden',
      active
        ? 'text-transparent bg-clip-text bg-gradient-to-r from-violet-400 via-purple-400 to-cyan-400 animate-shimmer-active'
        : 'text-slate-300 hover:text-transparent hover:bg-clip-text hover:bg-gradient-to-r hover:from-violet-400 hover:to-cyan-400'
    ]"
  >
    <!-- Animated background elements for active state -->
    <div v-if="active" class="absolute inset-0 bg-gradient-to-r from-violet-500/10 via-purple-500/10 to-cyan-500/10 animate-gradient-pulse"></div>
    
    <!-- Floating particles system -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none opacity-0 group-hover:opacity-100 transition-opacity duration-300">
      <div v-for="i in 8" :key="i" 
           :class="`absolute rounded-full animate-float-nav-${i % 4}`"
           :style="`
             left: ${10 + (i * 10)}%; 
             top: ${20 + (i * 8)}%; 
             width: ${1 + (i % 2)}px; 
             height: ${1 + (i % 2)}px;
             background: ${i % 3 === 0 ? '#8b5cf6' : i % 3 === 1 ? '#06b6d4' : '#a855f7'};
             animation-delay: ${i * 0.2}s;
           `">
      </div>
    </div>

    <!-- Shimmer effect overlay -->
    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-700 ease-out"></div>

    <!-- Enhanced border system -->
    <div :class="[
      'absolute bottom-0 left-0 h-0.5 transition-all duration-300 ease-out',
      active 
        ? 'w-full bg-gradient-to-r from-violet-500 via-purple-500 to-cyan-500 animate-border-glow'
        : 'w-0 bg-gradient-to-r from-violet-500 to-cyan-500 group-hover:w-full'
    ]"></div>

    <!-- Glassmorphism overlay on hover -->
    <div class="absolute inset-0 bg-slate-800/20 backdrop-blur-sm rounded-md opacity-0 group-hover:opacity-100 transition-all duration-300 -mx-1 -my-1"></div>

    <!-- Active state indicator -->
    <div v-if="active" class="absolute -left-1 top-1/2 -translate-y-1/2 w-1 h-6 bg-gradient-to-b from-violet-500 to-cyan-500 rounded-r animate-pulse-glow"></div>

    <!-- Content with enhanced styling -->
    <span class="relative z-10 flex items-center space-x-2 transform group-hover:translate-y-[-1px] transition-transform duration-300">
      <!-- Active state dot indicator -->
      <div v-if="active" class="w-1.5 h-1.5 rounded-full bg-gradient-to-r from-violet-500 to-cyan-500 animate-pulse-dot"></div>
      
      <!-- Text content -->
      <span :class="[
        'transition-all duration-300',
        active 
          ? 'font-semibold drop-shadow-sm' 
          : 'group-hover:font-medium'
      ]">
        <slot />
      </span>
      
      <!-- Hover state accent -->
      <div class="w-1 h-1 rounded-full bg-cyan-400 opacity-0 group-hover:opacity-60 transition-all duration-300 group-hover:scale-125"></div>
    </span>

    <!-- Focus ring enhancement -->
    <div :class="[
      'absolute inset-0 rounded-md ring-0 transition-all duration-300 pointer-events-none',
      active 
        ? 'ring-2 ring-violet-500/30 shadow-sm shadow-violet-500/20'
        : 'group-focus:ring-2 group-focus:ring-violet-500/30'
    ]"></div>

    <!-- Corner accent elements -->
    <div class="absolute top-1 right-1 w-2 h-2 opacity-0 group-hover:opacity-40 transition-opacity duration-300">
      <div class="absolute top-0 right-0 w-0.5 h-0.5 bg-violet-400 rounded-full animate-pulse"></div>
      <div class="absolute top-1 right-1 w-0.5 h-0.5 bg-cyan-400 rounded-full animate-pulse" style="animation-delay: 0.5s;"></div>
    </div>

    <!-- Text glow effect for active state -->
    <div v-if="active" class="absolute inset-0 bg-gradient-to-r from-violet-400/20 via-purple-400/20 to-cyan-400/20 blur-lg opacity-50 animate-text-glow"></div>
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
/* Shimmer animation for active text */
@keyframes shimmer-active {
  0% { background-position: -200% center; }
  100% { background-position: 200% center; }
}

.animate-shimmer-active {
  background-size: 200% 100%;
  animation: shimmer-active 3s ease-in-out infinite;
}

/* Gradient pulse for active background */
@keyframes gradient-pulse {
  0%, 100% { 
    background: linear-gradient(90deg, rgba(139, 92, 246, 0.1), rgba(168, 85, 247, 0.1), rgba(6, 182, 212, 0.1));
    opacity: 0.5;
  }
  50% { 
    background: linear-gradient(90deg, rgba(139, 92, 246, 0.2), rgba(168, 85, 247, 0.2), rgba(6, 182, 212, 0.2));
    opacity: 0.8;
  }
}

.animate-gradient-pulse {
  animation: gradient-pulse 2s ease-in-out infinite;
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
  animation: border-glow 2s ease-in-out infinite;
}

/* Floating navigation particles */
@keyframes float-nav-0 {
  0%, 100% { transform: translateY(0px) translateX(0px) rotate(0deg); opacity: 0.3; }
  50% { transform: translateY(-8px) translateX(4px) rotate(180deg); opacity: 0.7; }
}

@keyframes float-nav-1 {
  0%, 100% { transform: translateY(0px) translateX(0px) rotate(0deg); opacity: 0.4; }
  50% { transform: translateY(-6px) translateX(-3px) rotate(-180deg); opacity: 0.8; }
}

@keyframes float-nav-2 {
  0%, 100% { transform: translateY(0px) translateX(0px) rotate(0deg); opacity: 0.5; }
  50% { transform: translateY(-10px) translateX(2px) rotate(270deg); opacity: 0.6; }
}

@keyframes float-nav-3 {
  0%, 100% { transform: translateY(0px) translateX(0px) rotate(0deg); opacity: 0.3; }
  50% { transform: translateY(-7px) translateX(-5px) rotate(-270deg); opacity: 0.9; }
}

.animate-float-nav-0 { animation: float-nav-0 3s ease-in-out infinite; }
.animate-float-nav-1 { animation: float-nav-1 4s ease-in-out infinite; }
.animate-float-nav-2 { animation: float-nav-2 3.5s ease-in-out infinite; }
.animate-float-nav-3 { animation: float-nav-3 4.5s ease-in-out infinite; }

/* Pulse glow for active indicator */
@keyframes pulse-glow {
  0%, 100% { 
    opacity: 0.6;
    box-shadow: 0 0 5px rgba(139, 92, 246, 0.4);
  }
  50% { 
    opacity: 1;
    box-shadow: 0 0 15px rgba(139, 92, 246, 0.6), 0 0 25px rgba(6, 182, 212, 0.3);
  }
}

.animate-pulse-glow {
  animation: pulse-glow 2s ease-in-out infinite;
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
  animation: pulse-dot 1.5s ease-in-out infinite;
}

/* Text glow animation */
@keyframes text-glow {
  0%, 100% { opacity: 0.3; }
  50% { opacity: 0.6; }
}

.animate-text-glow {
  animation: text-glow 3s ease-in-out infinite;
}

/* Enhanced hover effects */
.group:hover .animate-float-nav-0,
.group:hover .animate-float-nav-1,
.group:hover .animate-float-nav-2,
.group:hover .animate-float-nav-3 {
  animation-duration: 2s;
}

/* Focus enhancements */
.group:focus-visible {
  outline: 2px solid rgba(139, 92, 246, 0.5);
  outline-offset: 2px;
  border-radius: 6px;
}

/* Responsive adjustments */
@media (max-width: 640px) {
  .group {
    padding-left: 0.5rem;
    padding-right: 0.5rem;
  }
  
  .group:hover span {
    transform: translateY(-0.5px);
  }
}

/* High contrast mode support */
@media (prefers-contrast: high) {
  .text-slate-300 {
    color: white !important;
  }
  
  .group:hover {
    background-color: rgba(255, 255, 255, 0.1) !important;
    color: white !important;
  }
  
  .animate-border-glow {
    background: white !important;
  }
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
  .group, .group *, 
  .animate-shimmer-active,
  .animate-gradient-pulse,
  .animate-border-glow,
  .animate-float-nav-0,
  .animate-float-nav-1,
  .animate-float-nav-2,
  .animate-float-nav-3,
  .animate-pulse-glow,
  .animate-pulse-dot,
  .animate-text-glow {
    animation: none !important;
    transition-duration: 0.01ms !important;
  }
}

/* Dark theme optimization */
@media (prefers-color-scheme: dark) {
  .text-slate-300 {
    color: rgb(203 213 225);
  }
  
  .bg-slate-800\/20 {
    background-color: rgba(30, 41, 59, 0.3);
  }
}
</style>