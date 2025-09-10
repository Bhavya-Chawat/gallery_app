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
    <input
      ref="fileInput"
      type="file"
      multiple
      :accept="acceptedTypes"
      @change="handleFileSelect"
      class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
    />

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
        <p class="mt-1 text-sm text-gray-500">
          Drag and drop files here, or click to select files
        </p>
      </div>

      <!-- File Requirements -->
      <div class="text-xs text-gray-400 space-y-1">
        <p>Supported formats: {{ allowedExtensions.join(', ').toUpperCase() }}</p>
        <p>Maximum file size: {{ formatBytes(maxFileSize) }}</p>
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

    <!-- Upload Progress -->
    <div v-if="uploading" class="mt-4">
      <div class="bg-gray-200 rounded-full h-2">
        <div 
          class="bg-blue-600 h-2 rounded-full transition-all duration-300"
          :style="{ width: `${uploadProgress}%` }"
        ></div>
      </div>
      <p class="mt-2 text-sm text-gray-600">
        Uploading {{ uploadedFiles }}/{{ totalFiles }} files...
      </p>
    </div>

    <!-- File Preview -->
    <div v-if="selectedFiles.length" class="mt-4 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2">
      <div 
        v-for="(file, index) in selectedFiles.slice(0, 8)"
        :key="index"
        class="relative aspect-square bg-gray-100 rounded-lg overflow-hidden"
      >
        <img
          :src="file.preview"
          :alt="file.name"
          class="w-full h-full object-cover"
        />
        <button
          @click="removeFile(index)"
          class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 hover:bg-red-600"
        >
          <XMarkIcon class="h-3 w-3" />
        </button>
      </div>
      <div 
        v-if="selectedFiles.length > 8"
        class="aspect-square bg-gray-200 rounded-lg flex items-center justify-center"
      >
        <span class="text-sm text-gray-500">
          +{{ selectedFiles.length - 8 }} more
        </span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import {
  CloudArrowUpIcon,
  ExclamationTriangleIcon,
  XMarkIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
  acceptedTypes: {
    type: String,
    default: 'image/*'
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

const emit = defineEmits(['files-selected', 'upload-progress', 'upload-complete'])

const fileInput = ref(null)
const isDragOver = ref(false)
const selectedFiles = ref([])
const uploading = ref(false)
const uploadProgress = ref(0)
const uploadedFiles = ref(0)
const totalFiles = ref(0)

const allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'avif']

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

const handleFileSelect = (e) => {
  const files = Array.from(e.target.files)
  processFiles(files)
}

const processFiles = (files) => {
  const validFiles = files.filter(file => {
    // Check file type
    if (!file.type.startsWith('image/')) {
      console.warn(`File ${file.name} is not a valid image`)
      return false
    }
    
    // Check file size
    if (file.size > props.maxFileSize) {
      console.warn(`File ${file.name} is too large`)
      return false
    }
    
    return true
  })

  // Create preview URLs
  const filesWithPreviews = validFiles.map(file => ({
    file,
    name: file.name,
    size: file.size,
    preview: URL.createObjectURL(file)
  }))

  selectedFiles.value = filesWithPreviews
  emit('files-selected', validFiles)
}

const removeFile = (index) => {
  const removed = selectedFiles.value.splice(index, 1)
  // Clean up preview URL
  if (removed[0]?.preview) {
    URL.revokeObjectURL(removed[0].preview)
  }
}

const formatBytes = (bytes) => {
  if (bytes === 0) return '0 B'
  const k = 1024
  const sizes = ['B', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}
</script>
