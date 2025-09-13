<template>
  <Link
    :href="href"
    :method="method"
    :as="as"
    class="block w-full px-4 py-2 text-left text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"
  >
    <slot />
  </Link>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
  href: String,
  method: {
    type: String,
    default: 'get',
  },
  as: {
    type: String,
    default: 'a',
  },
})
</script>
/*<template>
  <Link
    :href="href"
    :method="method"
    :as="as"
    class="group relative block w-full overflow-hidden rounded-lg transition-all duration-300 ease-in-out transform hover:scale-[1.02] hover:shadow-lg hover:shadow-violet-500/20"
  >
    <!-- Animated background gradient -->
    <div class="absolute inset-0 bg-gradient-to-r from-violet-500/5 via-purple-500/5 to-cyan-500/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
    
    <!-- Shimmer effect on hover -->
    <div class="absolute inset-0 -translate-x-full group-hover:translate-x-full bg-gradient-to-r from-transparent via-white/10 to-transparent transition-transform duration-700 ease-out"></div>
    
    <!-- Border gradient -->
    <div class="absolute inset-0 rounded-lg bg-gradient-to-r from-violet-500/20 via-purple-500/20 to-cyan-500/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300 p-[1px]">
      <div class="w-full h-full rounded-lg bg-slate-900/50 backdrop-blur-sm"></div>
    </div>
    
    <!-- Content wrapper -->
    <div class="relative px-4 py-2 text-left">
      <!-- Text content with enhanced styling -->
      <div class="flex items-center space-x-3">
        <!-- Icon placeholder (you can add icons here) -->
        <div class="w-2 h-2 rounded-full bg-gradient-to-r from-violet-500 to-cyan-500 opacity-60 group-hover:opacity-100 transition-opacity duration-300"></div>
        
        <!-- Text with gradient effect on hover -->
        <span class="text-sm leading-5 text-slate-300 group-hover:text-transparent group-hover:bg-clip-text group-hover:bg-gradient-to-r group-hover:from-violet-400 group-hover:via-purple-400 group-hover:to-cyan-400 transition-all duration-300 font-medium">
          <slot />
        </span>
      </div>
      
      <!-- Hover indicator -->
      <div class="absolute right-3 top-1/2 -translate-y-1/2 w-1 h-1 rounded-full bg-cyan-400 opacity-0 group-hover:opacity-100 transition-all duration-300 group-hover:scale-150"></div>
    </div>
    
    <!-- Bottom border animation -->
    <div class="absolute bottom-0 left-0 w-0 h-[2px] bg-gradient-to-r from-violet-500 via-purple-500 to-cyan-500 group-hover:w-full transition-all duration-500 ease-out"></div>
  </Link>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
  href: String,
  method: {
    type: String,
    default: 'get',
  },
  as: {
    type: String,
    default: 'a',
  },
})
</script>

<style scoped>
/* Additional animations for extra polish */
@keyframes float {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-2px); }
}

.group:hover .float-animation {
  animation: float 2s ease-in-out infinite;
}

/* Pulse effect for the indicator dot */
@keyframes pulse-glow {
  0%, 100% { 
    box-shadow: 0 0 5px rgba(139, 92, 246, 0.5);
  }
  50% { 
    box-shadow: 0 0 15px rgba(139, 92, 246, 0.8), 0 0 25px rgba(139, 92, 246, 0.4);
  }
}

.group:hover .pulse-glow {
  animation: pulse-glow 2s ease-in-out infinite;
}
</style>