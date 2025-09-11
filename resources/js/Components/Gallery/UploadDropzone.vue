<template>
  <div 
    :class="[
      'upload-dropzone relative border-2 border-dashed rounded-lg p-8 text-center transition-colors',
      isDragOver ? 'border-blue-400 bg-blue-50' : 'border-gray-300 hover:border-gray-400'
    ]"
    @dragover.prevent="handleDragOver"
    @dragleave.prevent="handleDragLeave"
    @drop.prevent="handleDrop"
  >
    <div class="space-y-4">
      <!-- Upload Icon -->
      <div class="mx-auto h-12 w-12 text-gray-400">
        <CloudArrowUpIcon class="h-12 w-12" />
      </div>

      <!-- Upload Text -->
      <div>
        <h3 class="text-lg font-medium text-gray-900">
          {{ isDragOver ? 'Drop files here' : 'Upload images' }}
        </h3>
        <p class="mt-1 text-sm text-gray-500">Drag and drop files here</p>
      </div>

      <!-- File Requirements -->
      <div class="text-xs text-gray-400 space-y-1">
        <p>Supported formats: {{ normalizedExtensions.map(e => e.toUpperCase()).join(', ') }}</p>
        <p>Maximum file size: {{ formatBytes(computedMaxSize) }}</p>
        <p v-if="maxFiles">Maximum {{ maxFiles }} files</p>
      </div>

      <!-- Storage Warning -->
      <div v-if="storageUsage && storageUsage.percentage > 80" class="p-3 bg-yellow-50 border border-yellow-200 rounded-md">
        <div class="flex items-center">
          <ExclamationTriangleIcon class="h-5 w-5 text-yellow-400 mr-2" />
          <p class="text-sm text-yellow-800">
            Storage {{ storageUsage.percentage.toFixed(1) }}% full
          </p>
        </div>
      </div>
    </div>
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
