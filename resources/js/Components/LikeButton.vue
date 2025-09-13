<template>
  <button
    @click="toggleLike"
    :disabled="loading"
    :class="[
      'inline-flex items-center space-x-1 px-3 py-1 rounded-md text-sm transition-colors',
      liked 
        ? 'bg-red-100 text-red-700 hover:bg-red-200' 
        : 'bg-gray-100 text-gray-700 hover:bg-gray-200',
      loading && 'opacity-50 cursor-not-allowed'
    ]"
  >
    <HeartIcon 
      :class="[
        'h-4 w-4',
        liked ? 'fill-current text-red-500' : ''
      ]" 
    />
    <span>{{ likesCount }}</span>
  </button>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { HeartIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  likeableType: String,
  likeableId: String,
  initialLiked: Boolean,
  initialLikesCount: Number,
})

const liked = ref(props.initialLiked)
const likesCount = ref(props.initialLikesCount)
const loading = ref(false)

const toggleLike = async () => {
  if (loading.value) return
  
  try {
    loading.value = true
    
    const response = await axios.post(route('likes.toggle'), {
      likeable_type: props.likeableType,
      likeable_id: props.likeableId,
    })
    
    liked.value = response.data.liked
    likesCount.value = response.data.likes_count
    
  } catch (error) {
    console.error('Failed to toggle like:', error)
  } finally {
    loading.value = false
  }
}
</script>
/* <template>
  <button
    @click="toggleLike"
    :disabled="loading"
    :class="[
      'group relative inline-flex items-center space-x-2 px-4 py-2 rounded-lg text-sm font-medium overflow-hidden transition-all duration-300 transform hover:scale-105 active:scale-95',
      liked
        ? 'bg-gradient-to-r from-rose-500/20 to-pink-500/20 backdrop-blur-sm border border-rose-500/30 text-rose-300 hover:from-rose-500/30 hover:to-pink-500/30 shadow-lg shadow-rose-500/20'
        : 'bg-slate-800/50 backdrop-blur-sm border border-violet-500/20 text-slate-300 hover:bg-gradient-to-r hover:from-violet-500/20 hover:to-purple-500/20 hover:border-violet-500/40 shadow-lg shadow-slate-900/20',
      loading && 'opacity-50 cursor-not-allowed scale-100 hover:scale-100'
    ]"
  >
    <!-- Animated background mesh -->
    <div class="absolute inset-0 opacity-20 overflow-hidden">
      <div :class="[
        'absolute inset-0 transition-all duration-500',
        liked 
          ? 'bg-gradient-to-r from-rose-400/10 via-pink-400/10 to-rose-400/10 animate-gradient-shift-liked'
          : 'bg-gradient-to-r from-violet-400/10 via-purple-400/10 to-cyan-400/10 animate-gradient-shift-default'
      ]"></div>
    </div>

    <!-- Floating particles -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
      <div v-for="i in 6" :key="i" 
           :class="[
             'absolute rounded-full opacity-0 group-hover:opacity-60 transition-opacity duration-300',
             liked ? 'bg-rose-400' : 'bg-violet-400',
             i % 3 === 0 ? 'w-1 h-1' : 'w-0.5 h-0.5'
           ]"
           :style="`
             left: ${10 + (i * 15)}%; 
             top: ${20 + (i * 10)}%; 
             animation-delay: ${i * 0.2}s;
           `"
           class="animate-float-particles">
      </div>
    </div>

    <!-- Shimmer effect -->
    <div :class="[
      'absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-700 ease-out',
      liked ? 'via-rose-300/20' : 'via-violet-300/20'
    ]"></div>

    <!-- Pulse ring on liked state -->
    <div v-if="liked" class="absolute inset-0 rounded-lg animate-pulse-ring-liked"></div>

    <!-- Heart Icon with enhanced effects -->
    <div class="relative z-10">
      <HeartIcon
        :class="[
          'h-5 w-5 transition-all duration-300 transform',
          liked 
            ? 'fill-current text-rose-400 animate-heart-beat drop-shadow-sm' 
            : 'text-slate-400 group-hover:text-violet-400 group-hover:scale-110',
          loading && 'animate-spin'
        ]"
      />
      
      <!-- Heart glow effect when liked -->
      <div v-if="liked" class="absolute inset-0 rounded-full bg-rose-400/30 blur-md animate-pulse opacity-60"></div>
    </div>

    <!-- Enhanced count display -->
    <span class="relative z-10 font-semibold transition-all duration-300">
      <span :class="[
        'inline-block transition-all duration-300',
        liked 
          ? 'text-rose-300 drop-shadow-sm' 
          : 'text-slate-300 group-hover:text-violet-300',
        loading && 'animate-pulse'
      ]">
        {{ likesCount }}
      </span>
    </span>

    <!-- Loading spinner overlay -->
    <div v-if="loading" class="absolute inset-0 flex items-center justify-center bg-slate-900/20 backdrop-blur-sm rounded-lg">
      <div class="w-4 h-4 border-2 border-violet-400/30 border-t-violet-400 rounded-full animate-spin"></div>
    </div>

    <!-- Click ripple effect -->
    <div class="absolute inset-0 opacity-0 group-active:opacity-100 transition-opacity duration-150">
      <div :class="[
        'absolute inset-0 rounded-lg animate-ripple',
        liked ? 'bg-rose-400/20' : 'bg-violet-400/20'
      ]"></div>
    </div>

    <!-- Enhanced border glow -->
    <div :class="[
      'absolute inset-0 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300',
      liked 
        ? 'shadow-lg shadow-rose-500/25 ring-1 ring-rose-400/30' 
        : 'shadow-lg shadow-violet-500/25 ring-1 ring-violet-400/30'
    ]"></div>
  </button>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { HeartIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  likeableType: String,
  likeableId: String,
  initialLiked: Boolean,
  initialLikesCount: Number,
})

const liked = ref(props.initialLiked)
const likesCount = ref(props.initialLikesCount)
const loading = ref(false)

const toggleLike = async () => {
  if (loading.value) return
  
  try {
    loading.value = true
    
    const response = await axios.post(route('likes.toggle'), {
      likeable_type: props.likeableType,
      likeable_id: props.likeableId,
    })
    
    liked.value = response.data.liked
    likesCount.value = response.data.likes_count
    
  } catch (error) {
    console.error('Failed to toggle like:', error)
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
/* Gradient animation for background */
@keyframes gradient-shift-liked {
  0%, 100% { 
    background: linear-gradient(45deg, rgba(244, 63, 94, 0.1), rgba(236, 72, 153, 0.1), rgba(244, 63, 94, 0.1));
  }
  50% { 
    background: linear-gradient(45deg, rgba(236, 72, 153, 0.15), rgba(244, 63, 94, 0.15), rgba(236, 72, 153, 0.15));
  }
}

@keyframes gradient-shift-default {
  0%, 100% { 
    background: linear-gradient(45deg, rgba(139, 92, 246, 0.1), rgba(168, 85, 247, 0.1), rgba(6, 182, 212, 0.1));
  }
  50% { 
    background: linear-gradient(45deg, rgba(168, 85, 247, 0.15), rgba(6, 182, 212, 0.15), rgba(139, 92, 246, 0.15));
  }
}

.animate-gradient-shift-liked {
  animation: gradient-shift-liked 3s ease-in-out infinite;
}

.animate-gradient-shift-default {
  animation: gradient-shift-default 3s ease-in-out infinite;
}

/* Floating particles animation */
@keyframes float-particles {
  0%, 100% { 
    transform: translateY(0px) translateX(0px); 
    opacity: 0.3; 
  }
  50% { 
    transform: translateY(-8px) translateX(-4px); 
    opacity: 0.8; 
  }
}

.animate-float-particles {
  animation: float-particles 2s ease-in-out infinite;
}

/* Heart beat animation */
@keyframes heart-beat {
  0%, 100% { 
    transform: scale(1); 
  }
  25% { 
    transform: scale(1.1); 
  }
  50% { 
    transform: scale(1.05); 
  }
  75% { 
    transform: scale(1.15); 
  }
}

.animate-heart-beat {
  animation: heart-beat 1.5s ease-in-out infinite;
}

/* Pulse ring for liked state */
@keyframes pulse-ring-liked {
  0% {
    box-shadow: 0 0 0 0 rgba(244, 63, 94, 0.4);
  }
  70% {
    box-shadow: 0 0 0 8px rgba(244, 63, 94, 0);
  }
  100% {
    box-shadow: 0 0 0 0 rgba(244, 63, 94, 0);
  }
}

.animate-pulse-ring-liked {
  animation: pulse-ring-liked 2s ease-out infinite;
}

/* Ripple effect */
@keyframes ripple {
  0% {
    transform: scale(0);
    opacity: 0.6;
  }
  100% {
    transform: scale(2);
    opacity: 0;
  }
}

.animate-ripple {
  animation: ripple 0.6s ease-out;
}

/* Enhanced hover states */
.group:hover .animate-float-particles {
  animation-duration: 1.5s;
}

.group:active .animate-heart-beat {
  animation-duration: 0.3s;
}

/* Loading state enhancements */
.group:disabled .animate-gradient-shift-liked,
.group:disabled .animate-gradient-shift-default {
  animation-play-state: paused;
}

/* Focus accessibility */
.group:focus-visible {
  outline: 2px solid rgba(139, 92, 246, 0.5);
  outline-offset: 2px;
}

/* Responsive adjustments */
@media (max-width: 640px) {
  .group {
    padding: 0.375rem 0.75rem;
  }
  
  .group .h-5 {
    height: 1rem;
    width: 1rem;
  }
  
  .group:hover {
    transform: scale(1.02);
  }
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
  .group, .group *, .animate-heart-beat, .animate-float-particles, .animate-gradient-shift-liked, .animate-gradient-shift-default {
    animation: none !important;
    transition-duration: 0.01ms !important;
  }
  
  .group:hover {
    transform: none !important;
  }
}

/* High contrast mode */
@media (prefers-contrast: high) {
  .group {
    border-width: 2px;
  }
  
  .group:hover {
    background: white !important;
    color: black !important;
  }
}
</style>