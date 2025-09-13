<template>
  <div 
    :class="[
      'upload-dropzone relative border-2 border-dashed rounded-3xl p-12 text-center transition-all duration-500 overflow-hidden group',
      isDragOver 
        ? 'border-violet-400 bg-gradient-to-br from-violet-500/10 via-purple-500/10 to-cyan-500/10 backdrop-blur-xl shadow-2xl scale-105' 
        : 'border-slate-600/50 hover:border-violet-400/70 bg-gradient-to-br from-slate-800/30 via-slate-700/30 to-slate-800/30 backdrop-blur-xl hover:shadow-xl hover:scale-102'
    ]"
    @dragover.prevent="handleDragOver"
    @dragleave.prevent="handleDragLeave"
    @drop.prevent="handleDrop"
    style="box-shadow: 0 25px 50px -12px rgba(139, 92, 246, 0.15), 0 0 0 1px rgba(255, 255, 255, 0.05);"
  >
    <!-- Animated Mesh Background -->
    <div class="absolute inset-0 opacity-20 pointer-events-none">
      <svg class="w-full h-full animate-mesh-drift" viewBox="0 0 100 100" preserveAspectRatio="none">
        <defs>
          <linearGradient id="meshGradUpload" x1="0%" y1="0%" x2="100%" y2="100%" gradientUnits="objectBoundingBox">
            <stop offset="0%" style="stop-color:#8b5cf6;stop-opacity:0.4" class="animate-gradient-shift">
              <animate attributeName="stop-color" values="#8b5cf6;#06b6d4;#a855f7;#8b5cf6" dur="6s" repeatCount="indefinite"/>
            </stop>
            <stop offset="50%" style="stop-color:#06b6d4;stop-opacity:0.3">
              <animate attributeName="stop-color" values="#06b6d4;#a855f7;#8b5cf6;#06b6d4" dur="6s" repeatCount="indefinite"/>
            </stop>
            <stop offset="100%" style="stop-color:#a855f7;stop-opacity:0.4">
              <animate attributeName="stop-color" values="#a855f7;#8b5cf6;#06b6d4;#a855f7" dur="6s" repeatCount="indefinite"/>
            </stop>
          </linearGradient>
        </defs>
        <rect width="100%" height="100%" fill="url(#meshGradUpload)"/>
        <path d="M0,20 Q50,0 100,20 L100,80 Q50,100 0,80 Z" fill="url(#meshGradUpload)" opacity="0.3">
          <animateTransform attributeName="transform" type="translate" values="0,0; 3,2; 0,0" dur="8s" repeatCount="indefinite"/>
        </path>
      </svg>
    </div>

    <!-- Floating Particles -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
      <div v-for="i in 12" :key="i" 
           class="absolute w-1 h-1 rounded-full animate-float-gentle"
           :style="{
             left: Math.random() * 100 + '%',
             top: Math.random() * 100 + '%',
             background: ['#8b5cf6', '#06b6d4', '#a855f7'][Math.floor(Math.random() * 3)],
             animationDelay: Math.random() * 6 + 's',
             animationDuration: (6 + Math.random() * 3) + 's'
           }">
      </div>
    </div>

    <!-- Animated Border Glow -->
    <div class="absolute inset-0 rounded-3xl opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none">
      <div class="absolute inset-0 rounded-3xl bg-gradient-to-r from-violet-500/20 via-purple-500/20 to-cyan-500/20 blur-xl"></div>
    </div>

    <div class="space-y-8 relative z-10">
      <!-- Enhanced Upload Icon -->
      <div class="mx-auto relative group-hover:scale-110 transition-all duration-500">
        <div class="w-20 h-20 mx-auto bg-gradient-to-br from-violet-500/20 via-purple-500/20 to-cyan-500/20 backdrop-blur-xl rounded-2xl border border-white/10 flex items-center justify-center shadow-2xl group-hover:shadow-violet-500/25 transition-all duration-500">
          <CloudArrowUpIcon class="h-10 w-10 text-slate-300 group-hover:text-violet-300 transition-colors duration-300" />
        </div>
        
        <!-- Floating Ring Animation -->
        <div class="absolute inset-0 w-20 h-20 mx-auto rounded-2xl border-2 border-violet-400/30 animate-pulse-ring opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
        <div class="absolute inset-0 w-20 h-20 mx-auto rounded-2xl border-2 border-cyan-400/30 animate-pulse-ring-delayed opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
      </div>

      <!-- Enhanced Upload Text -->
      <div class="space-y-3">
        <h3 class="text-2xl font-bold bg-gradient-to-r from-slate-200 via-white to-slate-200 bg-clip-text text-transparent animate-text-shimmer group-hover:from-violet-300 group-hover:via-white group-hover:to-cyan-300 transition-all duration-700">
          {{ isDragOver ? 'Drop files here' : 'Upload images' }}
        </h3>
        <p class="text-slate-400 group-hover:text-slate-300 transition-colors duration-300 font-medium">
          {{ isDragOver ? 'Release to upload your files' : 'Drag and drop files here' }}
        </p>
      </div>

      <!-- Enhanced File Requirements -->
      <div class="space-y-3 animate-fade-in-up">
        <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-xl p-4 space-y-2">
          <div class="flex items-center justify-center space-x-2 text-slate-300">
            <div class="w-2 h-2 bg-violet-400 rounded-full animate-pulse"></div>
            <p class="text-sm font-medium">
              Supported formats: {{ normalizedExtensions.map(e => e.toUpperCase()).join(', ') }}
            </p>
          </div>
          
          <div class="flex items-center justify-center space-x-2 text-slate-300">
            <div class="w-2 h-2 bg-cyan-400 rounded-full animate-pulse animation-delay-300"></div>
            <p class="text-sm font-medium">
              Maximum file size: {{ formatBytes(computedMaxSize) }}
            </p>
          </div>
          
          <div v-if="maxFiles" class="flex items-center justify-center space-x-2 text-slate-300">
            <div class="w-2 h-2 bg-purple-400 rounded-full animate-pulse animation-delay-600"></div>
            <p class="text-sm font-medium">
              Maximum {{ maxFiles }} files
            </p>
          </div>
        </div>
      </div>

      <!-- Enhanced Storage Warning -->
      <div v-if="storageUsage && storageUsage.percentage > 80" class="animate-fade-in-up animation-delay-400">
        <div class="bg-gradient-to-r from-amber-500/10 via-orange-500/10 to-red-500/10 backdrop-blur-xl border border-amber-400/30 rounded-xl p-4 shadow-2xl">
          <div class="flex items-center justify-center space-x-3">
            <div class="relative">
              <ExclamationTriangleIcon class="h-6 w-6 text-amber-400 animate-pulse" />
              <div class="absolute inset-0 h-6 w-6 text-amber-400 animate-ping opacity-25">
                <ExclamationTriangleIcon class="h-6 w-6" />
              </div>
            </div>
            <div class="text-center">
              <p class="text-sm font-semibold text-amber-300">
                Storage Warning
              </p>
              <p class="text-xs text-amber-200/80">
                {{ storageUsage.percentage.toFixed(1) }}% storage used
              </p>
            </div>
          </div>
          
          <!-- Progress Bar -->
          <div class="mt-3 w-full bg-black/20 rounded-full h-2 overflow-hidden">
            <div 
              class="h-2 bg-gradient-to-r from-amber-400 via-orange-400 to-red-400 rounded-full transition-all duration-1000 shadow-lg"
              :style="{ width: storageUsage.percentage + '%' }"
            >
              <div class="h-full bg-white/20 animate-shimmer-bar"></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Upload Status Indicator (appears on drag) -->
      <div v-if="isDragOver" class="animate-bounce-gentle">
        <div class="bg-gradient-to-r from-violet-500/20 via-purple-500/20 to-cyan-500/20 backdrop-blur-xl border border-violet-400/50 rounded-xl p-3 shadow-2xl">
          <div class="flex items-center justify-center space-x-2">
            <div class="w-3 h-3 bg-violet-400 rounded-full animate-pulse"></div>
            <span class="text-violet-300 font-medium text-sm">Ready to receive files</span>
            <div class="w-3 h-3 bg-cyan-400 rounded-full animate-pulse animation-delay-300"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Corner Decorations -->
    <div class="absolute top-4 left-4 w-8 h-8 border-l-2 border-t-2 border-violet-400/30 rounded-tl-xl opacity-0 group-hover:opacity-100 transition-all duration-500"></div>
    <div class="absolute top-4 right-4 w-8 h-8 border-r-2 border-t-2 border-cyan-400/30 rounded-tr-xl opacity-0 group-hover:opacity-100 transition-all duration-500"></div>
    <div class="absolute bottom-4 left-4 w-8 h-8 border-l-2 border-b-2 border-purple-400/30 rounded-bl-xl opacity-0 group-hover:opacity-100 transition-all duration-500"></div>
    <div class="absolute bottom-4 right-4 w-8 h-8 border-r-2 border-b-2 border-violet-400/30 rounded-br-xl opacity-0 group-hover:opacity-100 transition-all duration-500"></div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import {
  CloudArrowUpIcon,
  ExclamationTriangleIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
  // Preferred formats as comma-separated list
  acceptedFormats: {
    type: [String, Array],
    default: 'jpg,jpeg,png,webp,avif'
  },
  // Back-compat prop (if provided, takes precedence)
  acceptedTypes: {
    type: String,
    default: ''
  },
  maxSize: {
    type: Number,
    default: null
  },
  maxFileSize: {
    type: Number,
    default: 52428800 // 50MB
  },
  maxFiles: {
    type: Number,
    default: null
  },
  storageUsage: {
    type: Object,
    default: null
  }
})

const emit = defineEmits(['files-selected'])

const isDragOver = ref(false)
const selectedFiles = ref([])

const defaultExtensions = ['jpg', 'jpeg', 'png', 'webp', 'avif']

const normalizedExtensions = computed(() => {
  if (Array.isArray(props.acceptedFormats)) {
    return props.acceptedFormats.map(s => String(s).trim().toLowerCase()).filter(Boolean)
  }
  return (props.acceptedFormats || '')
    .split(',')
    .map(s => s.trim().toLowerCase())
    .filter(Boolean)
    .concat([])
    .filter((v, i, a) => a.indexOf(v) === i)
    || defaultExtensions
})

const computedMaxSize = computed(() => props.maxSize || props.maxFileSize)

// Note: input click removed; drag-and-drop only

const handleDragOver = () => {
  isDragOver.value = true
}

const handleDragLeave = () => {
  isDragOver.value = false
}

const handleDrop = (e) => {
  isDragOver.value = false
  const files = Array.from(e.dataTransfer.files)
  processFiles(files)
}

const processFiles = (files) => {
  let incoming = files
  if (props.maxFiles && files.length > props.maxFiles) {
    incoming = files.slice(0, props.maxFiles)
    console.warn(`Only the first ${props.maxFiles} files will be used.`)
  }

  const validFiles = incoming.filter(file => {
    // Check file type
    if (!(file.type && file.type.startsWith('image/'))) {
      const nameExt = (file.name.split('.').pop() || '').toLowerCase()
      if (!normalizedExtensions.value.includes(nameExt)) {
        console.warn(`File ${file.name} is not a valid image`)
        return false
      }
    }
    
    // Check file size
    if (file.size > computedMaxSize.value) {
      console.warn(`File ${file.name} is too large`)
      return false
    }
    
    return true
  })

  selectedFiles.value = validFiles.map(file => ({ name: file.name, size: file.size }))
  emit('files-selected', validFiles)
}

const formatBytes = (bytes) => {
  if (bytes === 0) return '0 B'
  const k = 1024
  const sizes = ['B', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

// Drag-and-drop only; parent page handles submission
</script>

<style scoped>
@keyframes mesh-drift {
  0%, 100% { transform: translate(0, 0) rotate(0deg); }
  33% { transform: translate(10px, -10px) rotate(1deg); }
  66% { transform: translate(-8px, 8px) rotate(-1deg); }
}

@keyframes float-gentle {
  0%, 100% { 
    transform: translateY(0px) rotate(0deg);
    opacity: 0.3;
  }
  50% { 
    transform: translateY(-15px) rotate(180deg);
    opacity: 0.7;
  }
}

@keyframes text-shimmer {
  0% { background-position: -200% center; }
  100% { background-position: 200% center; }
}

@keyframes pulse-ring {
  0% {
    transform: scale(1);
    opacity: 1;
  }
  100% {
    transform: scale(1.5);
    opacity: 0;
  }
}

@keyframes pulse-ring-delayed {
  0% {
    transform: scale(1);
    opacity: 1;
  }
  100% {
    transform: scale(1.8);
    opacity: 0;
  }
}

@keyframes fade-in-up {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes bounce-gentle {
  0%, 100% { 
    transform: translateY(0);
  }
  50% { 
    transform: translateY(-10px);
  }
}

@keyframes shimmer-bar {
  0% { transform: translateX(-100%); }
  100% { transform: translateX(100%); }
}

.animate-mesh-drift {
  animation: mesh-drift 12s ease-in-out infinite;
}

.animate-float-gentle {
  animation: float-gentle 6s ease-in-out infinite;
}

.animate-text-shimmer {
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
  background-size: 200% 100%;
  animation: text-shimmer 3s ease-in-out infinite;
}

.animate-pulse-ring {
  animation: pulse-ring 2s ease-out infinite;
}

.animate-pulse-ring-delayed {
  animation: pulse-ring-delayed 2s ease-out infinite;
  animation-delay: 0.5s;
}

.animate-fade-in-up {
  animation: fade-in-up 0.8s ease-out forwards;
  opacity: 0;
}

.animate-bounce-gentle {
  animation: bounce-gentle 1.5s ease-in-out infinite;
}

.animate-shimmer-bar {
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
  animation: shimmer-bar 2s ease-in-out infinite;
}

.animation-delay-300 {
  animation-delay: 0.3s;
}

.animation-delay-400 {
  animation-delay: 0.4s;
}

.animation-delay-600 {
  animation-delay: 0.6s;
}

.hover\:scale-102:hover {
  transform: scale(1.02);
}
</style>