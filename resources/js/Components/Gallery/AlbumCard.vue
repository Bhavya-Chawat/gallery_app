<template>
  <div class="gallery-card group cursor-pointer relative overflow-hidden bg-gradient-to-br from-slate-900/60 via-slate-800/60 to-slate-900/60 backdrop-blur-xl border border-white/10 rounded-2xl shadow-2xl shadow-violet-500/10 transition-all duration-500 hover:scale-105 hover:shadow-2xl hover:shadow-violet-500/20 hover:border-violet-500/30" @click="handleClick">
    <!-- Animated Background Mesh -->
    <div class="absolute inset-0 opacity-10">
      <svg class="w-full h-full animate-float-album" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
        <defs>
          <linearGradient id="album-mesh" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" class="animate-gradient-shift">
              <animate attributeName="stop-color" values="#8b5cf6;#06b6d4;#a855f7;#8b5cf6" dur="8s" repeatCount="indefinite"/>
            </stop>
            <stop offset="100%" class="animate-gradient-shift">
              <animate attributeName="stop-color" values="#06b6d4;#a855f7;#8b5cf6;#06b6d4" dur="8s" repeatCount="indefinite"/>
            </stop>
          </linearGradient>
        </defs>
        <rect x="10" y="10" width="80" height="50" fill="none" stroke="url(#album-mesh)" stroke-width="0.3" opacity="0.4" rx="6"/>
        <rect x="10" y="65" width="80" height="25" fill="none" stroke="url(#album-mesh)" stroke-width="0.3" opacity="0.4" rx="4"/>
        <circle cx="20" cy="20" r="1" fill="url(#album-mesh)" opacity="0.6"/>
        <circle cx="80" cy="20" r="1" fill="url(#album-mesh)" opacity="0.6"/>
        <circle cx="50" cy="75" r="1" fill="url(#album-mesh)" opacity="0.6"/>
        <line x1="10" y1="35" x2="90" y2="35" stroke="url(#album-mesh)" stroke-width="0.2" opacity="0.3"/>
      </svg>
    </div>

    <!-- Floating Particles -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
      <div class="particle absolute w-1 h-1 bg-gradient-to-r from-violet-400 to-cyan-400 rounded-full opacity-40 animate-float-album-1" style="left: 15%; top: 20%;"></div>
      <div class="particle absolute w-0.5 h-0.5 bg-gradient-to-r from-cyan-400 to-purple-400 rounded-full opacity-50 animate-float-album-2" style="left: 80%; top: 25%;"></div>
      <div class="particle absolute w-1 h-1 bg-gradient-to-r from-purple-400 to-violet-400 rounded-full opacity-30 animate-float-album-3" style="left: 25%; top: 75%;"></div>
      <div class="particle absolute w-0.5 h-0.5 bg-gradient-to-r from-violet-500 to-cyan-500 rounded-full opacity-60 animate-float-album-4" style="left: 85%; top: 80%;"></div>
    </div>

    <!-- Cover Image -->
    <div class="relative aspect-w-16 aspect-h-9 bg-gradient-to-br from-slate-800 to-slate-900 rounded-t-2xl overflow-hidden border-b border-white/10">
      <!-- Image Overlay Effect -->
      <div class="absolute inset-0 bg-gradient-to-t from-slate-900/20 via-transparent to-transparent z-10 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
      
      <img
        v-if="album.cover_image"
        :src="album.cover_image.url"
        :alt="album.title"
        class="w-full h-full object-cover group-hover:scale-110 transition-all duration-700 group-hover:brightness-110"
      >
      <div v-else class="flex items-center justify-center relative z-10">
        <!-- Enhanced Folder Icon -->
        <div class="relative p-4 rounded-full bg-gradient-to-br from-slate-700/50 to-slate-800/50 backdrop-blur-sm border border-white/10">
          <div class="absolute inset-0 rounded-full bg-gradient-to-r from-violet-500/20 to-cyan-500/20 blur-sm opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
          <FolderIcon class="relative z-10 h-12 w-12 text-slate-400 group-hover:text-slate-300 transition-colors duration-300" />
        </div>
      </div>

      <!-- Image Count Badge -->
      <div class="absolute top-3 right-3 z-20 bg-slate-900/80 backdrop-blur-sm border border-white/10 rounded-lg px-2 py-1 transition-all duration-300 group-hover:bg-slate-800/90 group-hover:border-violet-500/30">
        <span class="flex items-center text-xs font-medium text-slate-300 group-hover:text-white">
          <PhotoIcon class="h-3 w-3 mr-1" />
          {{ album.images_count || 0 }}
        </span>
      </div>
    </div>

    <!-- Content -->
    <div class="relative z-10 p-6">
      <div class="flex items-start justify-between">
        <div class="flex-1 min-w-0">
          <h3 class="text-lg font-semibold bg-gradient-to-r from-slate-100 to-slate-200 bg-clip-text text-transparent group-hover:from-white group-hover:to-slate-100 truncate transition-all duration-300 animate-shimmer-title">
            {{ album.title }}
          </h3>
          <p class="mt-2 text-sm text-slate-400 group-hover:text-slate-300 leading-relaxed transition-colors duration-300">
            {{ album.description }}
          </p>
        </div>
        <div class="ml-3 flex-shrink-0">
          <span :class="[
            'inline-flex items-center px-3 py-1 rounded-full text-xs font-medium backdrop-blur-sm border transition-all duration-300 group-hover:scale-105',
            privacyClasses[album.privacy]
          ]">
            {{ album.privacy }}
          </span>
        </div>
      </div>

      <!-- Stats -->
      <div class="mt-4 flex items-center justify-between text-sm">
        <span class="flex items-center text-slate-400 group-hover:text-slate-300 transition-colors duration-300">
          <div class="relative">
            <div class="absolute inset-0 rounded-full bg-gradient-to-r from-violet-500/20 to-cyan-500/20 blur-sm opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
            <PhotoIcon class="relative h-4 w-4 mr-2" />
          </div>
          {{ album.images_count || 0 }} image{{ (album.images_count || 0) !== 1 ? 's' : '' }}
        </span>
        <span class="text-slate-500 group-hover:text-slate-400 transition-colors duration-300 text-xs">
          {{ formatDate(album.updated_at) }}
        </span>
      </div>

      <!-- Owner -->
      <div class="mt-4 flex items-center">
        <div class="flex-shrink-0 relative">
          <!-- Enhanced Avatar -->
          <div class="h-7 w-7 rounded-full bg-gradient-to-br from-slate-600 to-slate-700 border border-white/10 transition-all duration-300 group-hover:scale-110 group-hover:border-violet-500/30"></div>
          <div class="absolute inset-0 rounded-full bg-gradient-to-r from-violet-500/20 to-cyan-500/20 blur-sm opacity-0 group-hover:opacity-75 transition-opacity duration-500"></div>
        </div>
        <span class="ml-3 text-sm text-slate-400 group-hover:text-slate-300 transition-colors duration-300">
          by <span class="font-medium">{{ album.owner?.name || 'Unknown' }}</span>
        </span>
      </div>
    </div>

    <!-- Enhanced Hover Border Effect -->
    <div class="absolute inset-0 rounded-2xl bg-gradient-to-r from-violet-500/0 via-cyan-500/0 to-purple-500/0 group-hover:from-violet-500/5 group-hover:via-cyan-500/5 group-hover:to-purple-500/5 transition-all duration-700 pointer-events-none"></div>
    
    <!-- Click Ripple Effect -->
    <div class="absolute inset-0 rounded-2xl bg-white/5 opacity-0 group-active:opacity-100 transition-opacity duration-150 pointer-events-none"></div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { router } from '@inertiajs/vue3'
import route from 'ziggy-js'
import {
  FolderIcon,
  PhotoIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
  album: {
    type: Object,
    required: true
  }
})

const privacyClasses = {
  public: 'bg-emerald-500/10 text-emerald-300 border-emerald-500/20 group-hover:bg-emerald-500/20 group-hover:border-emerald-500/40',
  unlisted: 'bg-amber-500/10 text-amber-300 border-amber-500/20 group-hover:bg-amber-500/20 group-hover:border-amber-500/40',
  private: 'bg-rose-500/10 text-rose-300 border-rose-500/20 group-hover:bg-rose-500/20 group-hover:border-rose-500/40'
}

const handleClick = () => {
  router.visit(route('albums.show', props.album.slug))
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString()
}
</script>

<style scoped>
@keyframes float-album {
  0%, 100% { transform: translateY(0px) rotate(0deg); }
  25% { transform: translateY(-3px) rotate(0.5deg); }
  50% { transform: translateY(-6px) rotate(0deg); }
  75% { transform: translateY(-3px) rotate(-0.5deg); }
}

@keyframes float-album-1 {
  0%, 100% { transform: translate(0px, 0px); opacity: 0.4; }
  25% { transform: translate(6px, -6px); opacity: 0.7; }
  50% { transform: translate(12px, 3px); opacity: 0.2; }
  75% { transform: translate(3px, 9px); opacity: 0.5; }
}

@keyframes float-album-2 {
  0%, 100% { transform: translate(0px, 0px); opacity: 0.5; }
  33% { transform: translate(-8px, 6px); opacity: 0.8; }
  66% { transform: translate(6px, -3px); opacity: 0.3; }
}

@keyframes float-album-3 {
  0%, 100% { transform: translate(0px, 0px); opacity: 0.3; }
  40% { transform: translate(10px, -10px); opacity: 0.6; }
  80% { transform: translate(-6px, 6px); opacity: 0.1; }
}

@keyframes float-album-4 {
  0%, 100% { transform: translate(0px, 0px); opacity: 0.6; }
  30% { transform: translate(-6px, -4px); opacity: 0.8; }
  70% { transform: translate(4px, 6px); opacity: 0.2; }
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

.animate-float-album {
  animation: float-album 8s ease-in-out infinite;
}

.animate-float-album-1 {
  animation: float-album-1 7s ease-in-out infinite;
}

.animate-float-album-2 {
  animation: float-album-2 9s ease-in-out infinite 1s;
}

.animate-float-album-3 {
  animation: float-album-3 8s ease-in-out infinite 2s;
}

.animate-float-album-4 {
  animation: float-album-4 6s ease-in-out infinite 3s;
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