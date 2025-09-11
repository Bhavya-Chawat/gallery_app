<template>
  <div 
    class="relative border-2 border-dashed rounded-xl transition-all duration-300"
    :class="{
      'border-blue-400 bg-blue-50/50 dark:bg-blue-900/20 dark:border-blue-500': isDragOver,
      'border-gray-300 dark:border-slate-600 bg-gray-50/50 dark:bg-slate-800/50': !isDragOver
    }"
    @dragover.prevent="handleDragOver"
    @dragleave.prevent="handleDragLeave"
    @drop.prevent="handleDrop"
    @click="triggerFileInput"
  >
    <input
      ref="fileInput"
      type="file"
      multiple
      :accept="acceptedFormats.join(',')"
      class="hidden"
      @change="handleFileSelect"
    />

    <div class="px-6 py-12 text-center">
      <div class="flex justify-center mb-4">
        <div 
          class="p-4 rounded-full transition-all duration-300"
          :class="{
            'bg-blue-100 dark:bg-blue-900/30': isDragOver,
            'bg-gray-100 dark:bg-slate-700': !isDragOver
          }"
        >
          <Upload 
            class="w-8 h-8 transition-colors duration-300"
            :class="{
              'text-blue-600 dark:text-blue-400': isDragOver,
              'text-gray-400 dark:text-slate-500': !isDragOver
            }"
          />
        </div>
      </div>

      <div class="space-y-2">
        <h3 
          class="text-lg font-medium transition-colors duration-300"
          :class="{
            'text-blue-900 dark:text-blue-100': isDragOver,
            'text-gray-900 dark:text-gray-100': !isDragOver
          }"
        >
          {{ isDragOver ? 'Drop your images here' : 'Upload your images' }}
        </h3>
        
        <p 
          class="text-sm transition-colors duration-300"
          :class="{
            'text-blue-600 dark:text-blue-300': isDragOver,
            'text-gray-500 dark:text-slate-400': !isDragOver
          }"
        >
          {{ isDragOver ? 'Release to upload' : 'Drag and drop images here, or click to browse' }}
        </p>

        <div class="flex flex-wrap justify-center gap-2 mt-4 text-xs text-gray-500 dark:text-slate-400">
          <span class="px-2 py-1 bg-gray-100 dark:bg-slate-700 rounded">
            Max {{ maxFiles }} files
          </span>
          <span class="px-2 py-1 bg-gray-100 dark:bg-slate-700 rounded">
            Up to {{ formatBytes(maxSize) }} each
          </span>
          <span class="px-2 py-1 bg-gray-100 dark:bg-slate-700 rounded">
            {{ supportedFormats }}
          </span>
        </div>
      </div>

      <!-- Quick Upload Options -->
      <div class="mt-6 flex flex-wrap justify-center gap-3">
        <button
          @click.stop="handleQuickAction('camera')"
          class="flex items-center px-4 py-2 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-600 transition-colors"
        >
          <Camera class="w-4 h-4 mr-2 text-gray-600 dark:text-slate-400" />
          <span class="text-sm text-gray-700 dark:text-slate-300">Camera</span>
        </button>
        
        <button
          @click.stop="handleQuickAction('paste')"
          class="flex items-center px-4 py-2 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-600 transition-colors"
        >
          <Clipboard class="w-4 h-4 mr-2 text-gray-600 dark:text-slate-400" />
          <span class="text-sm text-gray-700 dark:text-slate-300">Paste</span>
        </button>
        
        <button
          @click.stop="handleQuickAction('url')"
          class="flex items-center px-4 py-2 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-600 transition-colors"
        >
          <Link class="w-4 h-4 mr-2 text-gray-600 dark:text-slate-400" />
          <span class="text-sm text-gray-700 dark:text-slate-300">From URL</span>
        </button>
      </div>
    </div>

    <!-- Loading Overlay -->
    <div 
      v-if="processing"
      class="absolute inset-0 bg-white/80 dark:bg-slate-900/80 backdrop-blur-sm rounded-xl flex items-center justify-center"
    >
      <div class="text-center">
        <div class="w-8 h-8 border-4 border-blue-600 border-t-transparent rounded-full animate-spin mx-auto mb-2"></div>
        <p class="text-sm text-gray-600 dark:text-slate-400">Processing files...</p>
      </div>
    </div>
  </div>

  <!-- URL Upload Modal -->
  <div 
    v-if="showUrlModal"
    class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50"
    @click="showUrlModal = false"
  >
    <div 
      class="bg-white dark:bg-slate-800 rounded-xl p-6 max-w-md w-full mx-4"
      @click.stop
    >
      <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
        Upload from URL
      </h3>
      
      <div class="space-y-4">
        <input
          v-model="imageUrl"
          type="url"
          placeholder="https://example.com/image.jpg"
          class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          @keyup.enter="handleUrlUpload"
        />
        
        <div class="flex justify-end space-x-3">
          <button
            @click="showUrlModal = false"
            class="px-4 py-2 text-gray-600 dark:text-slate-400 hover:text-gray-800 dark:hover:text-slate-200"
          >
            Cancel
          </button>
          <button
            @click="handleUrlUpload"
            :disabled="!imageUrl.trim() || urlUploading"
            class="bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed text-white px-4 py-2 rounded-lg"
          >
            {{ urlUploading ? 'Fetching...' : 'Upload' }}
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Error Messages -->
  <div v-if="errors.length > 0" class="mt-4 space-y-2">
    <div 
      v-for="error in errors" 
      :key="error.id"
      class="flex items-center justify-between bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg px-4 py-3"
    >
      <div class="flex items-center">
        <AlertCircle class="w-4 h-4 text-red-600 dark:text-red-400 mr-2" />
        <span class="text-sm text-red-700 dark:text-red-300">{{ error.message }}</span>
      </div>
      <button
        @click="removeError(error.id)"
        class="text-red-500 dark:text-red-400 hover:text-red-700 dark:hover:text-red-200"
      >
        <X class="w-4 h-4" />
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { Upload, Camera, Clipboard, Link, AlertCircle, X } from 'lucide-vue-next'

// Props
const props = defineProps({
  acceptedFormats: {
    type: Array,
    default: () => ['image/jpeg', 'image/png', 'image/webp', 'image/gif']
  },
  maxFiles: {
    type: Number,
    default: 20
  },
  maxSize: {
    type: Number,
    default: 10485760 // 10MB
  },
  preserveExif: {
    type: Boolean,
    default: true
  }
})

// Emits
const emit = defineEmits([
  'files-added',
  'progress',
  'done', 
  'error'
])

// Reactive data
const isDragOver = ref(false)
const processing = ref(false)
const showUrlModal = ref(false)
const imageUrl = ref('')
const urlUploading = ref(false)
const errors = ref([])
const fileInput = ref(null)
const dragCounter = ref(0)

// Computed
const supportedFormats = computed(() => {
  return props.acceptedFormats
    .map(format => format.split('/')[1].toUpperCase())
    .join(', ')
})

// Methods
const formatBytes = (bytes) => {
  if (bytes === 0) return '0 Bytes'
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

const addError = (message, file = null) => {
  const error = {
    id: Math.random().toString(36).substr(2, 9),
    message,
    file: file?.name || null,
    timestamp: Date.now()
  }
  errors.value.push(error)
  
  // Auto-remove after 5 seconds
  setTimeout(() => {
    removeError(error.id)
  }, 5000)
  
  emit('error', error)
}

const removeError = (errorId) => {
  const index = errors.value.findIndex(e => e.id === errorId)
  if (index !== -1) {
    errors.value.splice(index, 1)
  }
}

const validateFile = (file) => {
  // Check file type
  if (!props.acceptedFormats.includes(file.type)) {
    addError(`File type ${file.type} is not supported`, file)
    return false
  }
  
  // Check file size
  if (file.size > props.maxSize) {
    addError(`File ${file.name} is too large (${formatBytes(file.size)}). Maximum size is ${formatBytes(props.maxSize)}`, file)
    return false
  }
  
  return true
}

const processFiles = async (files) => {
  processing.value = true
  
  try {
    const validFiles = []
    
    for (const file of files) {
      if (validFiles.length >= props.maxFiles) {
        addError(`Maximum ${props.maxFiles} files allowed`)
        break
      }
      
      if (validateFile(file)) {
        validFiles.push(file)
      }
    }
    
    if (validFiles.length > 0) {
      emit('files-added', validFiles)
    }
  } finally {
    processing.value = false
  }
}

const handleDragOver = (e) => {
  e.preventDefault()
  dragCounter.value++
  isDragOver.value = true
}

const handleDragLeave = (e) => {
  e.preventDefault()
  dragCounter.value--
  if (dragCounter.value === 0) {
    isDragOver.value = false
  }
}

const handleDrop = (e) => {
  e.preventDefault()
  isDragOver.value = false
  dragCounter.value = 0
  
  const files = Array.from(e.dataTransfer.files)
  processFiles(files)
}

const handleFileSelect = (e) => {
  const files = Array.from(e.target.files)
  processFiles(files)
  
  // Reset input
  e.target.value = ''
}

const triggerFileInput = () => {
  if (!processing.value) {
    fileInput.value?.click()
  }
}

const handleQuickAction = (action) => {
  switch (action) {
    case 'camera':
      // Trigger file input with camera capture
      const input = document.createElement('input')
      input.type = 'file'
      input.accept = 'image/*'
      input.capture = 'environment'
      input.onchange = (e) => {
        const files = Array.from(e.target.files)
        processFiles(files)
      }
      input.click()
      break
      
    case 'paste':
      handlePaste()
      break
      
    case 'url':
      showUrlModal.value = true
      break
  }
}

const handlePaste = async () => {
  try {
    const clipboardItems = await navigator.clipboard.read()
    const imageFiles = []
    
    for (const clipboardItem of clipboardItems) {
      for (const type of clipboardItem.types) {
        if (type.startsWith('image/')) {
          const blob = await clipboardItem.getType(type)
          const file = new File([blob], `pasted-image-${Date.now()}.${type.split('/')[1]}`, { type })
          imageFiles.push(file)
        }
      }
    }
    
    if (imageFiles.length > 0) {
      processFiles(imageFiles)
    } else {
      addError('No images found in clipboard')
    }
  } catch (error) {
    addError('Failed to read clipboard. Please paste manually or use browser file selection.')
  }
}

const handleUrlUpload = async () => {
  if (!imageUrl.value.trim()) return
  
  urlUploading.value = true
  
  try {
    // Create a temporary image to validate URL
    const img = new Image()
    img.crossOrigin = 'anonymous'
    
    await new Promise((resolve, reject) => {
      img.onload = resolve
      img.onerror = () => reject(new Error('Failed to load image from URL'))
      img.src = imageUrl.value
    })
    
    // Convert to canvas and then to blob
    const canvas = document.createElement('canvas')
    const ctx = canvas.getContext('2d')
    canvas.width = img.naturalWidth
    canvas.height = img.naturalHeight
    ctx.drawImage(img, 0, 0)
    
    canvas.toBlob(async (blob) => {
      const filename = imageUrl.value.split('/').pop() || `image-${Date.now()}.jpg`
      const file = new File([blob], filename, { type: blob.type })
      
      await processFiles([file])
      
      imageUrl.value = ''
      showUrlModal.value = false
    }, 'image/jpeg', 0.9)
    
  } catch (error) {
    addError('Failed to fetch image from URL: ' + error.message)
  } finally {
    urlUploading.value = false
  }
}

// Keyboard shortcuts
const handleKeydown = (e) => {
  if (e.ctrlKey || e.metaKey) {
    if (e.key === 'v') {
      e.preventDefault()
      handlePaste()
    } else if (e.key === 'u') {
      e.preventDefault()
      triggerFileInput()
    }
  }
}

// Lifecycle
onMounted(() => {
  document.addEventListener('keydown', handleKeydown)
  
  // Prevent default drag behaviors
  document.addEventListener('dragover', (e) => e.preventDefault())
  document.addEventListener('drop', (e) => e.preventDefault())
})

onUnmounted(() => {
  document.removeEventListener('keydown', handleKeydown)
})
</script>