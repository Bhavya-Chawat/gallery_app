<template>
  <div
    class="group relative bg-gradient-to-br from-slate-900/60 via-slate-800/60 to-slate-900/60 backdrop-blur-xl border border-white/10 rounded-2xl shadow-2xl shadow-violet-500/5 overflow-hidden transition-all duration-500 cursor-pointer hover:scale-105 hover:shadow-2xl hover:shadow-violet-500/20 hover:border-violet-500/30"
    :class="{ 'ring-2 ring-violet-500 shadow-violet-500/40': selected }"
    @click="handleImageClick"
  >
    <!-- Animated Background Mesh -->
    <div class="absolute inset-0 opacity-10">
      <svg class="w-full h-full animate-float-image" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
        <defs>
          <linearGradient id="image-mesh" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" class="animate-gradient-shift">
              <animate attributeName="stop-color" values="#8b5cf6;#06b6d4;#a855f7;#8b5cf6" dur="8s" repeatCount="indefinite"/>
            </stop>
            <stop offset="100%" class="animate-gradient-shift">
              <animate attributeName="stop-color" values="#06b6d4;#a855f7;#8b5cf6;#06b6d4" dur="8s" repeatCount="indefinite"/>
            </stop>
          </linearGradient>
        </defs>
        <rect x="15" y="15" width="70" height="50" fill="none" stroke="url(#image-mesh)" stroke-width="0.3" opacity="0.4" rx="6"/>
        <circle cx="25" cy="25" r="1" fill="url(#image-mesh)" opacity="0.6"/>
        <circle cx="75" cy="25" r="1" fill="url(#image-mesh)" opacity="0.6"/>
        <circle cx="50" cy="40" r="1" fill="url(#image-mesh)" opacity="0.6"/>
        <line x1="15" y1="40" x2="85" y2="40" stroke="url(#image-mesh)" stroke-width="0.2" opacity="0.3"/>
      </svg>
    </div>

    <!-- Floating Particles -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
      <div class="particle absolute w-1 h-1 bg-gradient-to-r from-violet-400 to-cyan-400 rounded-full opacity-40 animate-float-image-1" style="left: 15%; top: 20%;"></div>
      <div class="particle absolute w-0.5 h-0.5 bg-gradient-to-r from-cyan-400 to-purple-400 rounded-full opacity-50 animate-float-image-2" style="left: 80%; top: 25%;"></div>
      <div class="particle absolute w-1 h-1 bg-gradient-to-r from-purple-400 to-violet-400 rounded-full opacity-30 animate-float-image-3" style="left: 25%; top: 75%;"></div>
    </div>

    <!-- Selection checkbox -->
    <div v-if="showCheckbox" class="absolute top-3 left-3 z-20">
      <div class="relative">
        <div class="absolute inset-0 rounded-lg bg-slate-900/80 backdrop-blur-sm blur-sm"></div>
        <input
          type="checkbox"
          :checked="selected"
          @change="$emit('select', image.id)"
          @click.stop
          class="relative z-10 h-4 w-4 text-violet-500 focus:ring-violet-500/30 focus:ring-2 border-white/20 rounded bg-slate-800/50 backdrop-blur-sm transition-all duration-300"
        >
      </div>
    </div>

    <!-- Image -->
    <div class="aspect-square bg-gradient-to-br from-slate-800 to-slate-900 overflow-hidden rounded-t-2xl relative">
      <!-- Image Enhancement Overlay -->
      <div class="absolute inset-0 bg-gradient-to-t from-slate-900/10 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 z-10"></div>
      
      <img
        :src="getImageUrl('medium')"
        :alt="image.alt_text || image.title"
        class="w-full h-full object-cover group-hover:scale-110 transition-all duration-700 group-hover:brightness-110"
        loading="lazy"
        @error="onImageError"
      >
    </div>

    <!-- Privacy Badge -->
    <div v-if="image.privacy" class="absolute top-3 right-3 z-20">
      <span
        v-if="image.privacy === 'private'"
        class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-rose-500/20 text-rose-300 border border-rose-500/30 backdrop-blur-sm transition-all duration-300 group-hover:bg-rose-500/30 group-hover:border-rose-400/50"
      >Private</span>
      <span
        v-else-if="image.privacy === 'unlisted'"
        class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-amber-500/20 text-amber-300 border border-amber-500/30 backdrop-blur-sm transition-all duration-300 group-hover:bg-amber-500/30 group-hover:border-amber-400/50"
      >Unlisted</span>
      <span
        v-else
        class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 backdrop-blur-sm transition-all duration-300 group-hover:bg-emerald-500/30 group-hover:border-emerald-400/50"
      >Public</span>
    </div>

    <!-- Overlay -->
    <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-slate-900/20 to-transparent opacity-0 group-hover:opacity-100 transition-all duration-500 flex items-end pointer-events-none">
      <div class="w-full p-6 text-white transform translate-y-4 group-hover:translate-y-0 transition-all duration-500">
        <h3 class="text-sm font-semibold truncate mb-2 bg-gradient-to-r from-white to-slate-200 bg-clip-text text-transparent animate-shimmer-overlay">
          {{ image.title || 'Untitled' }}
        </h3>
        <div class="flex items-center justify-between text-xs">
          <span class="text-slate-300 font-medium">
            {{ formatDate(image.created_at) }}
          </span>
          <div class="flex items-center space-x-4">
            <span v-if="image.views_count" class="flex items-center text-slate-300 group-hover:text-white transition-colors duration-300">
              <div class="relative mr-1">
                <div class="absolute inset-0 rounded-full bg-cyan-500/20 blur-sm opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <EyeIcon class="relative h-3 w-3" />
              </div>
              {{ formatCount(image.views_count) }}
            </span>
            <span v-if="image.likes_count" class="flex items-center text-slate-300 group-hover:text-white transition-colors duration-300">
              <div class="relative mr-1">
                <div class="absolute inset-0 rounded-full bg-rose-500/20 blur-sm opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <HeartIcon class="relative h-3 w-3" />
              </div>
              {{ formatCount(image.likes_count) }}
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- Action menu -->
    <div class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-all duration-300 pointer-events-auto z-30" v-if="canEdit || canDelete || canDownload">
      <Dropdown align="right" width="48" @click.stop>
        <template #trigger>
          <button class="relative p-2 bg-slate-900/80 backdrop-blur-sm text-slate-300 rounded-full border border-white/10 transition-all duration-300 hover:bg-slate-800/90 hover:text-white hover:border-violet-500/30 hover:scale-110" @click.stop>
            <div class="absolute inset-0 rounded-full bg-gradient-to-r from-violet-500/0 to-cyan-500/0 hover:from-violet-500/20 hover:to-cyan-500/20 transition-all duration-300"></div>
            <EllipsisVerticalIcon class="relative h-4 w-4" />
          </button>
        </template>

        <template #content>
          <DropdownLink :href="route('images.show', image.slug)">
            View Details
          </DropdownLink>
          <DropdownLink :href="route('images.edit', image.slug)" v-if="canEdit">
            Edit
          </DropdownLink>
          <DropdownLink :href="route('images.download', image.slug)" v-if="canDownload">
            Download
          </DropdownLink>
          <div class="border-t border-gray-100" v-if="canEdit && canDelete"></div>
          <DropdownLink
            :href="route('images.destroy', image.slug)"
            method="delete"
            as="button"
            class="text-red-600"
            v-if="canDelete"
            @click="confirmDelete"
          >
            Delete
          </DropdownLink>
        </template>
      </Dropdown>
    </div>

    <!-- Image info -->
    <div class="relative z-10 p-4" v-if="showInfo">
      <h3 class="text-sm font-semibold bg-gradient-to-r from-slate-100 to-slate-200 bg-clip-text text-transparent truncate animate-shimmer-title">
        {{ image.title || 'Untitled' }}
      </h3>
      <p v-if="image.caption" class="mt-2 text-xs text-slate-400 group-hover:text-slate-300 line-clamp-2 leading-relaxed transition-colors duration-300">
        {{ image.caption }}
      </p>
      <div class="flex items-center justify-between mt-3">
        <span class="text-xs text-slate-500 group-hover:text-slate-400 font-medium transition-colors duration-300">
          {{ formatDate(image.created_at) }}
        </span>
        <div class="flex items-center space-x-3 text-xs text-slate-500 group-hover:text-slate-400">
          <span v-if="image.views_count" class="flex items-center transition-colors duration-300">
            <EyeIcon class="h-3 w-3 mr-1" />
            {{ formatCount(image.views_count) }} views
          </span>
          <span v-if="image.likes_count" class="flex items-center transition-colors duration-300">
            <HeartIcon class="h-3 w-3 mr-1" />
            {{ formatCount(image.likes_count) }} likes
          </span>
        </div>
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
import { Link, router } from '@inertiajs/vue3'
import route from 'ziggy-js'
import {
  EyeIcon,
  HeartIcon,
  LockClosedIcon,
  EllipsisVerticalIcon,
} from '@heroicons/vue/24/outline'

import Dropdown from '@/Components/Dropdown.vue'
import DropdownLink from '@/Components/DropdownLink.vue'

const props = defineProps({
  image: {
    type: Object,
    required: true,
  },
  size: {
    type: String,
    default: 'medium',
    validator: (value) => ['small', 'medium', 'large'].includes(value),
  },
  showCheckbox: {
    type: Boolean,
    default: false,
  },
  showInfo: {
    type: Boolean,
    default: true,
  },
  selected: {
    type: Boolean,
    default: false,
  },
  canEdit: {
    type: Boolean,
    default: false,
  },
  canDelete: {
    type: Boolean,
    default: false,
  },
  canDownload: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['select', 'open'])

const getImageUrl = (variant = 'medium') => {
  // Use direct MinIO URL since thumbnails aren't processed yet  
  if (props.image.storage_path) {
    return `http://localhost:9000/gallery-images/${props.image.storage_path}`
  }
  return props.image.url || '/images/placeholder.jpg'
}

const handleImageClick = () => {
  console.log('ImageCard clicked!', props.image.id)
  emit('open', props.image.id)
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString()
}

const formatCount = (count) => {
  if (count >= 1000000) {
    return Math.floor(count / 1000000) + 'M'
  } else if (count >= 1000) {
    return Math.floor(count / 1000) + 'K'
  }
  return count.toString()
}

const onImageError = (event) => {
  // Fallback to a placeholder or retry with original
  event.target.src = '/images/placeholder.jpg'
}

const confirmDelete = (event) => {
  if (!confirm('Are you sure you want to delete this image?')) {
    event.preventDefault()
  }
}
</script>

<style scoped>
@keyframes float-image {
  0%, 100% { transform: translateY(0px) rotate(0deg); }
  25% { transform: translateY(-3px) rotate(0.5deg); }
  50% { transform: translateY(-6px) rotate(0deg); }
  75% { transform: translateY(-3px) rotate(-0.5deg); }
}

@keyframes float-image-1 {
  0%, 100% { transform: translate(0px, 0px); opacity: 0.4; }
  25% { transform: translate(6px, -6px); opacity: 0.7; }
  50% { transform: translate(12px, 3px); opacity: 0.2; }
  75% { transform: translate(3px, 9px); opacity: 0.5; }
}

@keyframes float-image-2 {
  0%, 100% { transform: translate(0px, 0px); opacity: 0.5; }
  33% { transform: translate(-8px, 6px); opacity: 0.8; }
  66% { transform: translate(6px, -3px); opacity: 0.3; }
}

@keyframes float-image-3 {
  0%, 100% { transform: translate(0px, 0px); opacity: 0.3; }
  40% { transform: translate(10px, -10px); opacity: 0.6; }
  80% { transform: translate(-6px, 6px); opacity: 0.1; }
}

@keyframes shimmer-title {
  0% { background-position: -200% 0; }
  100% { background-position: 200% 0; }
}

@keyframes shimmer-overlay {
  0% { background-position: -200% 0; }
  100% { background-position: 200% 0; }
}

@keyframes gradient-shift {
  0%, 100% { stop-color: #8b5cf6; }
  25% { stop-color: #06b6d4; }
  50% { stop-color: #a855f7; }
  75% { stop-color: #8b5cf6; }
}

.animate-float-image {
  animation: float-image 8s ease-in-out infinite;
}

.animate-float-image-1 {
  animation: float-image-1 7s ease-in-out infinite;
}

.animate-float-image-2 {
  animation: float-image-2 9s ease-in-out infinite 1s;
}

.animate-float-image-3 {
  animation: float-image-3 8s ease-in-out infinite 2s;
}

.animate-shimmer-title {
  background-image: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
  background-size: 200% 100%;
  animation: shimmer-title 4s ease-in-out infinite;
}

.animate-shimmer-overlay {
  background-image: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
  background-size: 200% 100%;
  animation: shimmer-overlay 3s ease-in-out infinite;
}

.animate-gradient-shift {
  animation: gradient-shift 8s ease-in-out infinite;
}

/* Line clamp utility for caption */

</style>