<template>
  <AppLayout>
    <div class="min-h-screen bg-gray-50 dark:bg-slate-900">
      <!-- Header -->
      <div class="bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl border-b border-gray-200 dark:border-slate-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                Upload Images
              </h1>
              <p class="text-gray-500 dark:text-slate-400 mt-1">
                Share your photos with the world
              </p>
            </div>
            
            <!-- Storage Info -->
            <div class="text-right">
              <p class="text-sm text-gray-600 dark:text-slate-400">
                {{ formatBytes(quota?.used || 0) }} / {{ formatBytes(quota?.limit || 0) }} used
              </p>
              <div class="w-32 bg-gray-200 dark:bg-slate-700 rounded-full h-2 mt-1">
                <div 
                  class="bg-gradient-to-r from-blue-600 to-cyan-500 h-2 rounded-full"
                  :style="{ width: `${storagePercentage}%` }"
                ></div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Upload Dropzone -->
        <div class="mb-8">
          <UploadDropzone
            :accepted-formats="['image/jpeg', 'image/png', 'image/webp', 'image/gif']"
            :max-files="20"
            :max-size="10485760"
            :preserve-exif="preserveExif"
            @files-added="onFilesAdded"
            @progress="onProgress"
            @done="onUploadDone"
            @error="onUploadError"
          />
        </div>

        <!-- Upload Settings -->
        <div v-if="files.length === 0" class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
          <!-- Upload Options -->
          <div class="bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl rounded-xl p-6 border border-gray-200 dark:border-slate-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
              Upload Settings
            </h3>
            
            <div class="space-y-4">
              <!-- Default Album -->
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">
                  Default Album
                </label>
                <select 
                  v-model="defaultAlbum"
                  class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                  <option value="">No Album</option>
                  <option 
                    v-for="album in albums" 
                    :key="album.id" 
                    :value="album.id"
                  >
                    {{ album.title }}
                  </option>
                </select>
              </div>

              <!-- Privacy -->
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">
                  Default Privacy
                </label>
                <select 
                  v-model="defaultPrivacy"
                  class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                  <option value="public">Public</option>
                  <option value="unlisted">Unlisted</option>
                  <option value="private">Private</option>
                </select>
              </div>

              <!-- Preserve EXIF -->
              <div class="flex items-center">
                <input 
                  id="preserve-exif"
                  v-model="preserveExif"
                  type="checkbox"
                  class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                />
                <label for="preserve-exif" class="ml-2 text-sm text-gray-700 dark:text-slate-300">
                  Preserve EXIF data
                </label>
              </div>

              <!-- Auto-resize -->
              <div class="flex items-center">
                <input 
                  id="auto-resize"
                  v-model="autoResize"
                  type="checkbox"
                  class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                />
                <label for="auto-resize" class="ml-2 text-sm text-gray-700 dark:text-slate-300">
                  Auto-resize large images
                </label>
              </div>
            </div>
          </div>

          <!-- Quick Album Create -->
          <div class="bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl rounded-xl p-6 border border-gray-200 dark:border-slate-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
              Quick Album
            </h3>
            
            <div class="space-y-4">
              <input 
                v-model="newAlbumTitle"
                type="text"
                placeholder="Album title"
                class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
              
              <textarea 
                v-model="newAlbumDescription"
                placeholder="Album description (optional)"
                rows="3"
                class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              ></textarea>
              
              <button
                @click="createAlbum"
                :disabled="!newAlbumTitle.trim() || creatingAlbum"
                class="w-full bg-gradient-to-r from-blue-600 via-cyan-500 to-blue-400 text-white py-2 px-4 rounded-lg hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200"
              >
                <Plus class="w-4 h-4 inline mr-2" />
                {{ creatingAlbum ? 'Creating...' : 'Create Album' }}
              </button>
            </div>
          </div>
        </div>

        <!-- File List -->
        <div v-if="files.length > 0" class="bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl rounded-xl p-6 border border-gray-200 dark:border-slate-700">
          <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
              Upload Queue ({{ files.length }} files)
            </h3>
            
            <div class="flex space-x-2">
              <button
                @click="startUpload"
                :disabled="uploading || files.every(f => f.status === 'completed')"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
              >
                <Upload class="w-4 h-4 inline mr-2" />
                {{ uploading ? 'Uploading...' : 'Start Upload' }}
              </button>
              
              <button
                @click="clearFiles"
                :disabled="uploading"
                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
              >
                <X class="w-4 h-4 inline mr-2" />
                Clear All
              </button>
            </div>
          </div>

          <!-- Overall Progress -->
          <div v-if="uploading" class="mb-6">
            <div class="flex justify-between text-sm mb-2">
              <span class="text-gray-600 dark:text-slate-400">Overall Progress</span>
              <span class="font-medium text-gray-900 dark:text-gray-100">
                {{ completedFiles }}/{{ files.length }} files
              </span>
            </div>
            <div class="w-full bg-gray-200 dark:bg-slate-700 rounded-full h-2">
              <div 
                class="bg-gradient-to-r from-blue-600 to-cyan-500 h-2 rounded-full transition-all duration-300"
                :style="{ width: `${overallProgress}%` }"
              ></div>
            </div>
          </div>

          <!-- File Items -->
          <div class="space-y-4">
            <div 
              v-for="file in files" 
              :key="file.id"
              class="flex items-start space-x-4 p-4 bg-gray-50 dark:bg-slate-700/50 rounded-lg"
            >
              <!-- Preview -->
              <div class="flex-shrink-0 w-16 h-16 bg-gray-200 dark:bg-slate-600 rounded-lg overflow-hidden">
                <img 
                  v-if="file.preview" 
                  :src="file.preview" 
                  :alt="file.name"
                  class="w-full h-full object-cover"
                />
              </div>

              <!-- Details -->
              <div class="flex-grow min-w-0">
                <div class="flex items-center justify-between">
                  <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                    {{ file.name }}
                  </h4>
                  <span 
                    class="text-xs px-2 py-1 rounded-full"
                    :class="{
                      'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400': file.status === 'pending',
                      'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400': file.status === 'uploading',
                      'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400': file.status === 'completed',
                      'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400': file.status === 'error'
                    }"
                  >
                    {{ file.status }}
                  </span>
                </div>
                
                <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">
                  {{ formatBytes(file.size) }}
                </p>

                <!-- Progress Bar -->
                <div v-if="file.status === 'uploading'" class="mt-2">
                  <div class="w-full bg-gray-200 dark:bg-slate-600 rounded-full h-1">
                    <div 
                      class="bg-blue-600 h-1 rounded-full transition-all duration-300"
                      :style="{ width: `${file.progress || 0}%` }"
                    ></div>
                  </div>
                </div>

                <!-- Error Message -->
                <div v-if="file.status === 'error' && file.error" class="mt-2">
                  <p class="text-xs text-red-600 dark:text-red-400">
                    {{ file.error }}
                  </p>
                </div>

                <!-- Metadata Form -->
                <div v-if="file.status === 'pending'" class="mt-3 space-y-2">
                  <input 
                    v-model="file.metadata.title"
                    type="text"
                    :placeholder="'Title for ' + file.name"
                    class="w-full text-xs px-2 py-1 border border-gray-300 dark:border-slate-600 rounded bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100"
                  />
                  
                  <input 
                    v-model="file.metadata.tags"
                    type="text"
                    placeholder="Tags (comma separated)"
                    class="w-full text-xs px-2 py-1 border border-gray-300 dark:border-slate-600 rounded bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100"
                  />
                </div>
              </div>

              <!-- Actions -->
              <div class="flex-shrink-0">
                <button
                  v-if="file.status === 'error'"
                  @click="retryFile(file)"
                  class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300"
                >
                  <RotateCcw class="w-4 h-4" />
                </button>
                
                <button
                  v-if="file.status !== 'uploading'"
                  @click="removeFile(file.id)"
                  class="text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 ml-2"
                >
                  <X class="w-4 h-4" />
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Success Message -->
        <div v-if="uploadedImages.length > 0" class="mt-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-6">
          <div class="flex items-center mb-4">
            <CheckCircle class="w-6 h-6 text-green-600 dark:text-green-400 mr-2" />
            <h3 class="text-lg font-semibold text-green-900 dark:text-green-100">
              Upload Complete!
            </h3>
          </div>
          
          <p class="text-green-700 dark:text-green-300 mb-4">
            Successfully uploaded {{ uploadedImages.length }} images.
          </p>
          
          <div class="flex space-x-3">
            <Link 
              href="/dashboard"
              class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors"
            >
              View in Dashboard
            </Link>
            
            <Link 
              href="/gallery?my=true"
              class="bg-white dark:bg-slate-700 text-green-600 dark:text-green-400 border border-green-300 dark:border-green-600 px-4 py-2 rounded-lg hover:bg-green-50 dark:hover:bg-green-900/20 transition-colors"
            >
              View in Gallery
            </Link>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import UploadDropzone from '@/Components/Gallery/UploadDropzone.vue'
import { 
  Upload, Plus, X, CheckCircle, RotateCcw 
} from 'lucide-vue-next'
import axios from 'axios'

const props = defineProps({
  uploadPresets: {
    type: Object,
    required: true
  },
  albums: {
    type: Array,
    default: () => []
  },
  quota: {
    type: Object,
    required: true
  }
})

// Reactive data
const files = ref([])
const uploadedImages = ref([])
const uploading = ref(false)
const defaultAlbum = ref('')
const defaultPrivacy = ref('public')
const preserveExif = ref(true)
const autoResize = ref(true)
const newAlbumTitle = ref('')
const newAlbumDescription = ref('')
const creatingAlbum = ref(false)
const albums = ref([])

// Computed
const storagePercentage = computed(() => {
  const quota = props.quota
  if (!quota?.limit) return 0
  return Math.round((quota.used / quota.limit) * 100)
})

const completedFiles = computed(() => {
  return files.value.filter(f => f.status === 'completed').length
})

const overallProgress = computed(() => {
  if (files.value.length === 0) return 0
  return Math.round((completedFiles.value / files.value.length) * 100)
})

// Methods
const formatBytes = (bytes) => {
  if (bytes === 0) return '0 Bytes'
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

const onFilesAdded = (newFiles) => {
  const fileObjects = newFiles.map(file => ({
    id: Math.random().toString(36).substr(2, 9),
    file,
    name: file.name,
    size: file.size,
    preview: URL.createObjectURL(file),
    status: 'pending',
    progress: 0,
    error: null,
    metadata: {
      title: file.name.replace(/\.[^/.]+$/, ''),
      tags: '',
      album_id: defaultAlbum.value,
      privacy: defaultPrivacy.value
    }
  }))
  
  files.value.push(...fileObjects)
}

const onProgress = ({ fileId, percent }) => {
  const file = files.value.find(f => f.id === fileId)
  if (file) {
    file.progress = percent
  }
}

const onUploadDone = ({ uploadedIds }) => {
  uploadedIds.forEach(id => {
    const file = files.value.find(f => f.id === id)
    if (file) {
      file.status = 'completed'
      file.progress = 100
    }
  })
  
  uploading.value = false
}

const onUploadError = ({ fileId, error }) => {
  const file = files.value.find(f => f.id === fileId)
  if (file) {
    file.status = 'error'
    file.error = error
  }
}

const startUpload = async () => {
  uploading.value = true
  
  for (const file of files.value.filter(f => f.status === 'pending')) {
    try {
      file.status = 'uploading'
      
      // Get presigned URL
      const presignResponse = await axios.post('/api/v1/upload/presign', {
        filename: file.file.name,
        content_type: file.file.type,
        size: file.file.size
      })
      
      const { upload_url, fields, key } = presignResponse.data.data
      
      // Upload to storage
      const formData = new FormData()
      Object.entries(fields).forEach(([k, v]) => formData.append(k, v))
      formData.append('file', file.file)
      
      await axios.post(upload_url, formData, {
        onUploadProgress: (progressEvent) => {
          const percent = Math.round((progressEvent.loaded / progressEvent.total) * 100)
          onProgress({ fileId: file.id, percent })
        }
      })
      
      // Complete upload with metadata
      const completeResponse = await axios.post('/api/v1/upload/complete', {
        key,
        metadata: {
          ...file.metadata,
          preserve_exif: preserveExif.value,
          auto_resize: autoResize.value
        }
      })
      
      file.status = 'completed'
      file.imageId = completeResponse.data.data.id
      uploadedImages.value.push(completeResponse.data.data)
      
    } catch (error) {
      file.status = 'error'
      file.error = error.response?.data?.message || 'Upload failed'
    }
  }
  
  uploading.value = false
}

const retryFile = (file) => {
  file.status = 'pending'
  file.error = null
  file.progress = 0
}

const removeFile = (fileId) => {
  const index = files.value.findIndex(f => f.id === fileId)
  if (index !== -1) {
    const file = files.value[index]
    if (file.preview) {
      URL.revokeObjectURL(file.preview)
    }
    files.value.splice(index, 1)
  }
}

const clearFiles = () => {
  files.value.forEach(file => {
    if (file.preview) {
      URL.revokeObjectURL(file.preview)
    }
  })
  files.value = []
  uploadedImages.value = []
}

const createAlbum = async () => {
  if (!newAlbumTitle.value.trim()) return
  
  creatingAlbum.value = true
  
  try {
    const response = await axios.post('/api/v1/albums', {
      title: newAlbumTitle.value,
      description: newAlbumDescription.value || null,
      privacy: defaultPrivacy.value
    })
    
    const newAlbum = response.data.data
    albums.value.push(newAlbum)
    defaultAlbum.value = newAlbum.id
    
    newAlbumTitle.value = ''
    newAlbumDescription.value = ''
  } catch (error) {
    console.error('Failed to create album:', error)
  } finally {
    creatingAlbum.value = false
  }
}

const fetchAlbums = async () => {
  try {
    const response = await axios.get('/api/v1/my/albums')
    albums.value = response.data.data
  } catch (error) {
    console.error('Failed to fetch albums:', error)
  }
}

// Lifecycle
onMounted(() => {
  fetchAlbums()
})
</script>