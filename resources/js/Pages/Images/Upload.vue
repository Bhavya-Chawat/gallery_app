<template>
  <AppLayout>
    <Head title="Upload Images" />
    
    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Upload Images
          </h2>
          <p class="text-sm text-gray-600 mt-1">
            Share your photos with the world
          </p>
        </div>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
        <!-- Error Panel -->
        <div v-if="lastError" class="mb-6 bg-red-50 border border-red-200 rounded-md p-4">
          <div class="flex">
            <ExclamationTriangleIcon class="h-5 w-5 text-red-400" />
            <div class="ml-3">
              <h3 class="text-sm font-medium text-red-800">
                Upload failed
                <span v-if="lastError.status" class="font-normal">(HTTP {{ lastError.status }})</span>
              </h3>
              <p class="mt-1 text-sm text-red-700" v-if="lastError.message">{{ lastError.message }}</p>
              <pre v-if="lastError.errors" class="mt-2 text-xs text-red-700 whitespace-pre-wrap">{{ JSON.stringify(lastError.errors, null, 2) }}</pre>
            </div>
          </div>
        </div>

        <!-- Storage Usage Warning -->
        <div v-if="storageUsage.percentage > 90" class="mb-6 bg-red-50 border border-red-200 rounded-md p-4">
          <div class="flex">
            <ExclamationTriangleIcon class="h-5 w-5 text-red-400" />
            <div class="ml-3">
              <h3 class="text-sm font-medium text-red-800">Storage quota almost full</h3>
              <p class="mt-1 text-sm text-red-700">
                You've used {{ storageUsage.percentage.toFixed(1) }}% of your storage quota. 
                Consider deleting some images or contact support to increase your quota.
              </p>
            </div>
          </div>
        </div>

        <!-- Upload Component -->
        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
          <div class="p-6">
            
            <!-- DRAG AND DROP FILE SELECTION AREA -->
            <div class="relative">
              <div
                @drop="handleDrop"
                @dragover.prevent="handleDragOver"
                @dragenter.prevent="handleDragEnter"
                @dragleave="handleDragLeave"
                :class="[
                  'border-2 border-dashed rounded-lg p-8 text-center mb-6 transition-all duration-200 relative',
                  isDragging 
                    ? 'border-blue-400 bg-blue-50 scale-105' 
                    : 'border-gray-300 hover:border-gray-400 hover:bg-gray-50'
                ]"
              >
                <input
                  ref="fileInput"
                  type="file"
                  multiple
                  accept=".jpg,.jpeg,.png,.webp,.avif"
                  class="hidden"
                  @change="onFileInputChange"
                />
                
                <CloudArrowUpIcon 
                  :class="[
                    'mx-auto h-12 w-12 transition-colors',
                    isDragging ? 'text-blue-500' : 'text-gray-400'
                  ]" 
                />
                
                <p class="mt-2 text-sm text-gray-600">
                  <button
                    @click="$refs.fileInput.click()"
                    :class="[
                      'font-medium transition-colors',
                      isDragging ? 'text-blue-700' : 'text-blue-600 hover:text-blue-500'
                    ]"
                  >
                    Click to upload
                  </button>
                  or drag and drop files here
                </p>
                
                <p class="text-xs text-gray-500 mt-1">
                  {{ allowedMimes.split(',').map(m => m.toUpperCase()).join(', ') }} up to {{ formatBytes(maxUploadSize) }} each
                </p>
                
                <!-- Drag Overlay -->
                <div 
                  v-if="isDragging" 
                  class="absolute inset-0 flex items-center justify-center bg-blue-50 bg-opacity-95 rounded-lg border-2 border-blue-400 border-dashed"
                >
                  <div class="text-center">
                    <CloudArrowUpIcon class="mx-auto h-16 w-16 text-blue-500 animate-bounce" />
                    <p class="mt-2 text-lg font-medium text-blue-700">Drop files to upload</p>
                    <p class="text-sm text-blue-600">{{ dragFileCount }} file(s) ready</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Album and Privacy Settings -->
            <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Album Selection -->
              <div>
                <label for="album" class="block text-sm font-medium text-gray-700 mb-2">
                  Add to Album (Optional)
                </label>
                <select
                  id="album"
                  v-model="selectedAlbum"
                  class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >
                  <option value="">No Album (Individual Images)</option>
                  <option v-for="album in albums" :key="album.id" :value="album.id">
                    {{ album.title }}
                  </option>
                </select>
                <p class="mt-1 text-sm text-gray-500">
                  Choose an album to organize your images
                </p>
              </div>

              <!-- Privacy Settings -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Privacy Settings
                </label>
                <div class="space-y-2">
                  <label class="flex items-center">
                    <input
                      type="radio"
                      v-model="selectedPrivacy"
                      value="public"
                      class="mr-2 text-blue-600"
                    />
                    <span class="text-sm">Public - Visible to everyone</span>
                  </label>
                  <label class="flex items-center">
                    <input
                      type="radio"
                      v-model="selectedPrivacy"
                      value="unlisted"
                      class="mr-2 text-blue-600"
                    />
                    <span class="text-sm">Unlisted - Only accessible via direct link</span>
                  </label>
                  <label class="flex items-center">
                    <input
                      type="radio"
                      v-model="selectedPrivacy"
                      value="private"
                      class="mr-2 text-blue-600"
                    />
                    <span class="text-sm">Private - Only visible to you</span>
                  </label>
                </div>
              </div>
            </div>

            <div class="mt-4 flex items-center gap-3">
              <!-- File Input Button -->
              <label class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 cursor-pointer transition-colors">
                <input
                  ref="fileInputButton"
                  type="file"
                  multiple
                  accept=".jpg,.jpeg,.png,.webp,.avif"
                  class="hidden"
                  @change="onFileInputChange"
                />
                Select Files
              </label>
              
              <button
                type="button"
                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50 transition-colors"
                :disabled="!selected.length || !hasValidMetadata || submitting"
                @click="submitToStore"
              >
                <span v-if="uploading">
                  Uploading... {{ uploadProgress }}%
                </span>
                <span v-else-if="submitting">
                  Processing...
                </span>
                <span v-else>
                  Upload {{ selected.length }} Image{{ selected.length !== 1 ? 's' : '' }}
                </span>
              </button>
              
              <span v-if="selected.length" class="text-sm text-gray-500">
                {{ selected.length }} file(s) selected
              </span>
            </div>
          </div>
        </div>

        <!-- FILE EDITOR SECTION -->
        <div v-if="showFileEditor && selected.length > 0" class="mt-6 bg-white shadow-sm rounded-lg overflow-hidden">
          <div class="p-6">
            <div class="flex items-center justify-between mb-6">
              <h3 class="text-lg font-medium text-gray-900">
                Edit Image Details ({{ selected.length }} files)
              </h3>
              <div class="flex space-x-3">
                <button
                  @click="copyFirstToAll"
                  class="text-sm text-blue-600 hover:text-blue-500 px-3 py-1 border border-blue-200 rounded transition-colors"
                  :disabled="selected.length < 2"
                >
                  Copy first caption/tags to all
                </button>
                <button
                  @click="clearAllFiles"
                  class="text-sm text-red-600 hover:text-red-500 px-3 py-1 border border-red-200 rounded transition-colors"
                >
                  Clear All Files
                </button>
              </div>
            </div>

            <div class="space-y-6 max-h-96 overflow-y-auto">
              <div
                v-for="(file, index) in selected"
                :key="index"
                class="border border-gray-200 rounded-lg p-4 bg-gray-50"
              >
                <div class="flex items-start space-x-4">
                  <!-- File Preview -->
                  <div class="flex-shrink-0 relative">
                    <div class="w-20 h-20 bg-gray-300 rounded-lg flex items-center justify-center overflow-hidden">
                      <img
                        v-if="filePreviewUrls[index]"
                        :src="filePreviewUrls[index]"
                        :alt="file.name"
                        class="w-full h-full object-cover"
                        @error="handlePreviewError(index)"
                      />
                      <span v-else class="text-xs text-gray-600 font-medium">IMG</span>
                    </div>
                    
                    <!-- File Size Badge -->
                    <div class="absolute -bottom-2 -right-2 bg-gray-900 text-white text-xs px-2 py-1 rounded">
                      {{ formatFileSize(file.size) }}
                    </div>
                    
                    <!-- File Type Badge -->
                    <div class="absolute -top-2 -left-2 bg-blue-600 text-white text-xs px-1.5 py-0.5 rounded">
                      {{ getFileExtension(file.name) }}
                    </div>
                  </div>

                  <!-- Edit Form -->
                  <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Title -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">
                        Title *
                      </label>
                      <input
                        v-model="fileMetadata[index].title"
                        type="text"
                        required
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        :placeholder="getCleanFileName(file.name)"
                      />
                    </div>

                    <!-- Alt Text -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">
                        Alt Text
                      </label>
                      <input
                        v-model="fileMetadata[index].alt_text"
                        type="text"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="Describe this image for accessibility"
                      />
                    </div>

                    <!-- Caption -->
                    <div class="md:col-span-2">
                      <label class="block text-sm font-medium text-gray-700 mb-1">
                        Caption
                      </label>
                      <textarea
                        v-model="fileMetadata[index].caption"
                        rows="2"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="Add a caption or description..."
                      ></textarea>
                    </div>

                    <!-- Tags -->
                    <div class="md:col-span-2">
                      <label class="block text-sm font-medium text-gray-700 mb-1">
                        Tags
                      </label>
                      <input
                        v-model="fileMetadata[index].tags"
                        type="text"
                        placeholder="nature, landscape, photography (comma separated)"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                      />
                    </div>
                  </div>

                  <!-- Remove Button -->
                  <button
                    @click="removeFile(index)"
                    class="flex-shrink-0 p-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-full transition-colors"
                  >
                    <XMarkIcon class="h-5 w-5" />
                  </button>
                </div>

                <!-- File Info -->
                <div class="mt-3 flex items-center justify-between">
                  <div class="flex items-center space-x-4 text-xs text-gray-500">
                    <span>{{ file.name }}</span>
                    <span>{{ formatFileSize(file.size) }}</span>
                    <span>{{ file.type }}</span>
                  </div>
                  
                  <div class="flex items-center space-x-2">
                    <!-- Validation Status -->
                    <span
                      v-if="!fileMetadata[index]?.title?.trim()"
                      class="inline-flex items-center px-2 py-1 text-xs bg-red-100 text-red-800 rounded"
                    >
                      <ExclamationTriangleIcon class="h-3 w-3 mr-1" />
                      Title required
                    </span>
                    <span
                      v-else
                      class="inline-flex items-center px-2 py-1 text-xs bg-green-100 text-green-800 rounded"
                    >
                      <CheckCircleIcon class="h-3 w-3 mr-1" />
                      Ready
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Progress Bar -->
        <div v-if="uploading" class="mt-6 bg-white rounded-lg shadow p-6">
          <div class="flex items-center justify-between mb-2">
            <h3 class="text-sm font-medium text-gray-900">Upload Progress</h3>
            <span class="text-sm text-gray-500">{{ uploadedCount }}/{{ selected.length }}</span>
          </div>
          
          <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
            <div 
              class="bg-blue-600 h-2 rounded-full transition-all duration-300" 
              :style="{ width: uploadProgress + '%' }"
            ></div>
          </div>
          
          <div class="text-sm text-gray-600">
            <div v-if="currentFileName">Uploading: {{ currentFileName }}</div>
            <div class="mt-1">{{ uploadProgress }}% complete</div>
          </div>
        </div>

        <!-- Upload Guidelines -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-md p-4">
          <div class="flex">
            <InformationCircleIcon class="h-5 w-5 text-blue-400" />
            <div class="ml-3">
              <h3 class="text-sm font-medium text-blue-800">Upload Guidelines</h3>
              <div class="mt-2 text-sm text-blue-700">
                <ul class="list-disc list-inside space-y-1">
                  <li>Maximum file size: {{ formatBytes(maxUploadSize) }}</li>
                  <li>Supported formats: {{ allowedMimes.split(',').map(m => m.toUpperCase()).join(', ') }}</li>
                  <li>You can upload up to 10 images at once</li>
                  <li>Images will be automatically resized and optimized</li>
                  <li>EXIF data will be preserved but GPS coordinates will be removed for privacy</li>
                  <li>All images must have a title before uploading</li>
                  <li><strong>Drag & Drop:</strong> Simply drag images from your desktop and drop them above</li>
                </ul>
              </div>
            </div>
          </div>
        </div>

        <!-- Storage Information -->
        <div class="mt-6 bg-white shadow-sm rounded-lg p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Storage Usage</h3>
          <StorageUsageCard
            :used="storageUsage.used"
            :quota="storageUsage.quota"
            :percentage="storageUsage.percentage"
            :show-details="true"
          />
        </div>

        <!-- Recent Uploads -->
        <div v-if="recentUploads.length > 0" class="mt-6 bg-white shadow-sm rounded-lg p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Uploads</h3>
          <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
            <Link
              v-for="image in recentUploads"
              :key="image.id"
              :href="route('images.show', image.slug)"
              class="aspect-square bg-gray-100 rounded-lg overflow-hidden hover:shadow-md transition-shadow"
            >
              <img
                :src="getImageUrl(image, 'small')"
                :alt="image.alt_text || image.title"
                class="w-full h-full object-cover hover:scale-105 transition-transform"
              >
            </Link>
          </div>
        </div>
      </div>
    </div>

    <!-- Success Modal with EXIF Data -->
    <div v-if="showSuccessModal" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="fixed inset-0 bg-black opacity-50"></div>
      <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative bg-white rounded-lg shadow-xl max-w-4xl w-full mx-4 p-6 max-h-[90vh] overflow-y-auto">
          <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
              <CheckCircleIcon class="h-6 w-6 text-green-600" />
            </div>
            
            <h3 class="text-lg font-medium text-gray-900 mb-2">Upload Successful!</h3>
            <p class="text-gray-600 mb-6">
              {{ uploadedImages.length }} image{{ uploadedImages.length !== 1 ? 's' : '' }} uploaded successfully
            </p>
            
            <!-- Uploaded Images with EXIF Data -->
            <div v-if="uploadedImages.length > 0" class="mb-6">
              <h4 class="text-sm font-medium text-gray-900 mb-4 text-left">Uploaded Images with Details</h4>
              
              <div class="space-y-4 max-h-96 overflow-y-auto">
                <div
                  v-for="image in uploadedImages"
                  :key="image.id"
                  class="flex items-start space-x-4 p-4 bg-gray-50 rounded-lg text-left"
                >
                  <!-- Image Preview -->
                  <div class="w-20 h-20 bg-gray-200 rounded-lg flex-shrink-0 overflow-hidden">
                    <img
                      v-if="image.storage_path"
                      :src="getImageUrl(image, 'small')"
                      :alt="image.title"
                      class="w-full h-full object-cover"
                    />
                    <div v-else class="w-full h-full flex items-center justify-center text-xs text-gray-500">
                      IMG
                    </div>
                  </div>
                  
                  <!-- Image Details -->
                  <div class="flex-1 min-w-0">
                    <h5 class="text-sm font-medium text-gray-900 truncate mb-2">{{ image.title }}</h5>
                    
                    <!-- EXIF and Metadata Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs text-gray-600">
                      <!-- Dimensions -->
                      <div v-if="image.width && image.height" class="flex items-center">
                        <span class="inline-block w-4 text-center mr-2">📐</span>
                        <span>{{ image.width }} × {{ image.height }} pixels</span>
                      </div>
                      
                      <!-- File Size -->
                      <div class="flex items-center">
                        <span class="inline-block w-4 text-center mr-2">💾</span>
                        <span>{{ formatFileSize(image.size_bytes) }}</span>
                      </div>
                      
                      <!-- Camera Info -->
                      <div v-if="image.camera_make || image.camera_model" class="flex items-center">
                        <span class="inline-block w-4 text-center mr-2">📷</span>
                        <span>{{ [image.camera_make, image.camera_model].filter(Boolean).join(' ') }}</span>
                      </div>
                      
                      <!-- Date Taken -->
                      <div v-if="image.taken_at" class="flex items-center">
                        <span class="inline-block w-4 text-center mr-2">📅</span>
                        <span>{{ formatDate(image.taken_at) }}</span>
                      </div>
                      
                      <!-- File Type -->
                      <div class="flex items-center">
                        <span class="inline-block w-4 text-center mr-2">🏷️</span>
                        <span>{{ image.mime_type }}</span>
                      </div>
                      
                      <!-- Privacy -->
                      <div class="flex items-center">
                        <span class="inline-block w-4 text-center mr-2">🔒</span>
                        <span class="capitalize">{{ image.privacy || selectedPrivacy }}</span>
                      </div>
                    </div>
                    
                    <!-- Link to View Image -->
                    <div class="mt-2">
                      <Link
                        :href="route('images.show', image.slug)"
                        class="text-xs text-blue-600 hover:text-blue-800 underline"
                      >
                        View Full Image →
                      </Link>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="flex justify-center space-x-4">
              <Link
                :href="route('my.images')"
                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
              >
                View My Images
              </Link>
              <button 
                @click="closeSuccessModal"
                class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400"
              >
                Upload More
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import axios from 'axios'
import { Head, Link } from '@inertiajs/vue3'
import route from 'ziggy-js'
import {
  ExclamationTriangleIcon,
  InformationCircleIcon,
  CloudArrowUpIcon,
  XMarkIcon,
  CheckCircleIcon,
} from '@heroicons/vue/24/outline'

import AppLayout from '@/Layouts/AppLayout.vue'
import StorageUsageCard from '@/Components/StorageUsageCard.vue'

const props = defineProps({
  albums: { type: Array, default: () => [] },
  maxUploadSize: { type: Number, default: 52428800 },
  allowedMimes: { type: String, default: 'jpg,jpeg,png,webp,avif' },
  defaultPrivacy: { type: String, default: 'unlisted' },
  storageUsage: {
    type: Object,
    default: () => ({
      used: 0,
      quota: 1073741824,
      percentage: 0,
      remaining: 1073741824
    })
  },
  recentUploads: { type: Array, default: () => [] },
})

// Form data for album and privacy
const selectedAlbum = ref('')
const selectedPrivacy = ref(props.defaultPrivacy)

// File handling
const selected = ref([])
const fileMetadata = ref([])
const filePreviewUrls = ref([])
const showFileEditor = ref(false)
const submitting = ref(false)
const uploading = ref(false)
const uploadProgress = ref(0)
const uploadedCount = ref(0)
const currentFileName = ref('')
const lastError = ref(null)
const showSuccessModal = ref(false)
const uploadedImages = ref([])

// Drag and drop
const isDragging = ref(false)
const dragCounter = ref(0)
const dragFileCount = ref(0)

// Computed property to check if metadata is valid
const hasValidMetadata = computed(() => {
  return fileMetadata.value.length > 0 && 
         fileMetadata.value.every(meta => meta.title && meta.title.trim().length > 0)
})

// Utility functions
const formatBytes = (bytes) => {
  if (bytes === 0) return '0 B'
  const k = 1024
  const sizes = ['B', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

const formatFileSize = (bytes) => {
  return formatBytes(bytes)
}

const formatDate = (dateString) => {
  try {
    return new Date(dateString).toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    })
  } catch (e) {
    return dateString
  }
}

const getCleanFileName = (filename) => {
  return filename
    .replace(/\.[^/.]+$/, '')
    .replace(/[-_]/g, ' ')
    .replace(/\b\w/g, l => l.toUpperCase())
}

const getFileExtension = (filename) => {
  return filename.split('.').pop().toUpperCase()
}

const isValidImageFile = (file) => {
  const allowedTypes = props.allowedMimes.split(',')
  const fileExt = file.name.split('.').pop().toLowerCase()
  return allowedTypes.includes(fileExt) && file.type.startsWith('image/')
}

// Create file preview URLs
const createPreviewUrls = (files) => {
  filePreviewUrls.value = []
  files.forEach((file, index) => {
    if (file.type.startsWith('image/')) {
      try {
        const url = URL.createObjectURL(file)
        filePreviewUrls.value[index] = url
      } catch (error) {
        console.error('Error creating preview URL:', error)
        filePreviewUrls.value[index] = null
      }
    } else {
      filePreviewUrls.value[index] = null
    }
  })
}

const cleanupPreviewUrls = () => {
  filePreviewUrls.value.forEach(url => {
    if (url) {
      URL.revokeObjectURL(url)
    }
  })
  filePreviewUrls.value = []
}

const handlePreviewError = (index) => {
  if (filePreviewUrls.value[index]) {
    URL.revokeObjectURL(filePreviewUrls.value[index])
    filePreviewUrls.value[index] = null
  }
}

// Drag and drop handlers
const handleDragOver = (e) => {
  e.preventDefault()
  e.stopPropagation()
}

const handleDragEnter = (e) => {
  e.preventDefault()
  e.stopPropagation()
  dragCounter.value++
  isDragging.value = true
  
  // Count potential files
  if (e.dataTransfer.items) {
    dragFileCount.value = Array.from(e.dataTransfer.items).filter(item => 
      item.kind === 'file' && item.type.startsWith('image/')
    ).length
  }
}

const handleDragLeave = (e) => {
  e.preventDefault()
  e.stopPropagation()
  dragCounter.value--
  
  if (dragCounter.value === 0) {
    isDragging.value = false
    dragFileCount.value = 0
  }
}

const handleDrop = (e) => {
  e.preventDefault()
  e.stopPropagation()
  isDragging.value = false
  dragCounter.value = 0
  dragFileCount.value = 0
  
  try {
    const files = Array.from(e.dataTransfer.files).filter(file => 
      isValidImageFile(file) && file.size <= props.maxUploadSize
    )
    
    if (files.length > 0) {
      addFiles(files)
      console.log(`${files.length} files dropped and added`)
    } else {
      lastError.value = {
        message: 'No valid image files found. Please drop JPG, PNG, WebP, or AVIF files under ' + formatBytes(props.maxUploadSize) + '.'
      }
    }
  } catch (error) {
    console.error('Drop error:', error)
    lastError.value = {
      message: 'Error processing dropped files: ' + error.message
    }
  }
}

// Add files function (handles both click and drag)
const addFiles = (files) => {
  try {
    console.log('Adding files:', files.length)
    
    // Filter out files that are too large or invalid
    const validFiles = files.filter(file => 
      isValidImageFile(file) && file.size <= props.maxUploadSize
    )
    
    if (validFiles.length !== files.length) {
      const rejected = files.length - validFiles.length
      lastError.value = {
        message: `${rejected} file(s) were rejected (too large or invalid format)`
      }
    }
    
    // Filter out duplicates
    const uniqueFiles = validFiles.filter(file => 
      !selected.value.find(existing => 
        existing.name === file.name && existing.size === file.size
      )
    )
    
    if (uniqueFiles.length > 0) {
      // Cleanup old preview URLs
      cleanupPreviewUrls()
      
      // Add to selected files
      selected.value.push(...uniqueFiles)
      
      // Create metadata for new files only
      const newMetadata = uniqueFiles.map(file => ({
        title: getCleanFileName(file.name),
        caption: '',
        alt_text: '',
        tags: ''
      }))
      
      fileMetadata.value.push(...newMetadata)
      
      // Create preview URLs for all files
      createPreviewUrls(selected.value)
      
      // Show editor
      showFileEditor.value = true
      
      console.log(`${uniqueFiles.length} unique files added successfully`)
      
      // Clear any previous errors
      if (uniqueFiles.length === validFiles.length) {
        lastError.value = null
      }
    } else if (validFiles.length > 0) {
      lastError.value = {
        message: 'All files were already selected'
      }
    }
    
  } catch (error) {
    console.error('Error adding files:', error)
    lastError.value = {
      message: 'Error processing files: ' + error.message
    }
  }
}

// File selection with metadata creation
const onFileInputChange = (event) => {
  try {
    const files = Array.from(event.target.files)
    addFiles(files)
    event.target.value = ''
  } catch (error) {
    console.error('File selection error:', error)
    lastError.value = {
      message: 'Error selecting files: ' + error.message
    }
  }
}

// File management functions
const removeFile = (index) => {
  try {
    // Cleanup preview URL for this file
    if (filePreviewUrls.value[index]) {
      URL.revokeObjectURL(filePreviewUrls.value[index])
    }
    
    selected.value.splice(index, 1)
    fileMetadata.value.splice(index, 1)
    filePreviewUrls.value.splice(index, 1)
    
    // Hide editor if no files
    if (selected.value.length === 0) {
      showFileEditor.value = false
    }
    
    console.log(`File removed, ${selected.value.length} files remaining`)
  } catch (error) {
    console.error('Error removing file:', error)
  }
}

const clearAllFiles = () => {
  cleanupPreviewUrls()
  selected.value = []
  fileMetadata.value = []
  showFileEditor.value = false
  lastError.value = null
  console.log('All files cleared')
}

const copyFirstToAll = () => {
  if (fileMetadata.value.length > 1) {
    const first = fileMetadata.value[0]
    fileMetadata.value.forEach((meta, index) => {
      if (index > 0) {
        meta.caption = first.caption
        meta.alt_text = first.alt_text
        meta.tags = first.tags
        // Don't copy title - keep individual titles
      }
    })
    console.log('Metadata copied to all files')
  }
}

const getImageUrl = (image, variant = 'small') => {
  if (image.storage_path) {
    return `http://localhost:9000/gallery-images/${image.storage_path}`
  }
  return '/images/placeholder.jpg'
}

// Upload with metadata
const submitToStore = async () => {
  if (!selected.value.length || !hasValidMetadata.value) return
  
  try {
    submitting.value = true
    uploading.value = true
    uploadProgress.value = 0
    uploadedCount.value = 0
    lastError.value = null
    showSuccessModal.value = false
    
    await axios.get('/sanctum/csrf-cookie', { withCredentials: true })
    
    const formData = new FormData()
    
    // ADD FILES AND METADATA
    selected.value.forEach((file, index) => {
      formData.append(`files[${index}]`, file)
      currentFileName.value = file.name
      
      // ADD METADATA FOR EACH FILE
      const metadata = fileMetadata.value[index]
      formData.append(`metadata[${index}][title]`, metadata.title)
      formData.append(`metadata[${index}][caption]`, metadata.caption || '')
      formData.append(`metadata[${index}][alt_text]`, metadata.alt_text || '')
      formData.append(`metadata[${index}][tags]`, metadata.tags || '')
    })
    
    if (selectedAlbum.value) {
      formData.append('album_id', selectedAlbum.value)
    }
    formData.append('privacy', selectedPrivacy.value)
    
    const resp = await axios.post(route('upload.store'), formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
      withCredentials: true,
      onUploadProgress: (progressEvent) => {
        if (progressEvent.lengthComputable) {
          uploadProgress.value = Math.round((progressEvent.loaded / progressEvent.total) * 100)
          uploadedCount.value = Math.round((progressEvent.loaded / progressEvent.total) * selected.value.length)
        }
      }
    })
    
    if (resp.data?.images?.length) {
      uploadedImages.value = resp.data.images
      
      // Update storage usage if returned
      if (resp.data.storage_usage) {
        Object.assign(props.storageUsage, resp.data.storage_usage)
      }
      
      showSuccessModal.value = true
      
      // CLEANUP AND RESET EVERYTHING
      cleanupPreviewUrls()
      selected.value = []
      fileMetadata.value = []
      showFileEditor.value = false
    }
    
  } catch (e) {
    console.error('Upload failed', e)
    const status = e?.response?.status
    const data = e?.response?.data
    lastError.value = {
      status,
      message: data?.message || e.message,
      errors: data?.errors || data?.error || data,
    }
  } finally {
    submitting.value = false
    uploading.value = false
    uploadProgress.value = 0
  }
}

const closeSuccessModal = () => {
  showSuccessModal.value = false
  uploadedImages.value = []
  selectedAlbum.value = ''
}

// Cleanup on component unmount
onUnmounted(() => {
  cleanupPreviewUrls()
})
</script>
