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
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
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
                <h3 class="text-sm font-medium text-red-800">
                  Storage quota almost full
                </h3>
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
              <UploadDropzone
                :max-files="10"
                :max-size="maxUploadSize"
                :accepted-formats="allowedExtensions"
                :albums="albums"
                :default-privacy="defaultPrivacy"
                :storage-remaining="storageUsage.remaining"
                @files-selected="onFilesSelected"
              />
<div class="mt-4 flex items-center gap-3">

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



  <!-- File Input Button -->
  <label class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 cursor-pointer">
    <input
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
  class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50"
  :disabled="!selected.length || submitting"
  @click="submitToStore"
>
  <span v-if="uploading">
    Uploading... {{ uploadProgress }}%
  </span>
  <span v-else-if="submitting">
    Processing...
  </span>
  <span v-else>
    Start Upload
  </span>
</button>

  <span v-if="selected.length" class="text-sm text-gray-500">
    {{ selected.length }} file(s) selected
  </span>
</div>

            </div>
          </div>
<!-- Progress Bar (show when uploading) -->
<div v-if="uploading" class="mt-4">
  <div class="flex items-center justify-between mb-2">
    <span class="text-sm text-gray-600">
      Uploading: {{ currentFileName }}
    </span>
    <span class="text-sm text-gray-600">{{ uploadProgress }}%</span>
  </div>
  <div class="w-full bg-gray-200 rounded-full h-2">
    <div 
      class="bg-blue-600 h-2 rounded-full transition-all duration-300" 
      :style="{ width: uploadProgress + '%' }"
    ></div>
  </div>
  <div class="mt-2 text-sm text-gray-500">
    {{ uploadedCount }} of {{ selected.length }} files uploaded
  </div>
</div>

          <!-- Upload Guidelines -->
          <div class="mt-6 bg-blue-50 border border-blue-200 rounded-md p-4">
            <div class="flex">
              <InformationCircleIcon class="h-5 w-5 text-blue-400" />
              <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">
                  Upload Guidelines
                </h3>
                <div class="mt-2 text-sm text-blue-700">
                  <ul class="list-disc list-inside space-y-1">
                    <li>Maximum file size: {{ formatBytes(maxUploadSize) }}</li>
                    <li>Supported formats: {{ allowedMimes.split(',').map(m => m.toUpperCase()).join(', ') }}</li>
                    <li>You can upload up to 10 images at once</li>
                    <li>Images will be automatically resized and optimized</li>
                    <li>EXIF data will be preserved but GPS coordinates will be removed for privacy</li>
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
      <!-- Success Modal -->
<div v-if="showSuccessModal" class="fixed inset-0 z-50 overflow-y-auto">
  <div class="fixed inset-0 bg-black opacity-50"></div>
  <div class="flex min-h-full items-center justify-center p-4">
    <div class="relative bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 p-6">
      <div class="text-center">
        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
          <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
        </div>
        
        <h3 class="text-lg font-medium text-gray-900 mb-2">
          Upload Successful!
        </h3>
        <p class="text-gray-600 mb-6">
          {{ uploadedImages.length }} image{{ uploadedImages.length !== 1 ? 's' : '' }} uploaded successfully
        </p>
        
        <!-- Uploaded Images Preview -->
        <div v-if="uploadedImages.length > 0" class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-6">
          <div
            v-for="image in uploadedImages.slice(0, 6)"
            :key="image.id"
            class="aspect-square bg-gray-100 rounded-lg overflow-hidden"
          >
            <img
              :src="getImageUrl(image, 'small')"
              :alt="image.title"
              class="w-full h-full object-cover"
            />
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
            @click="showSuccessModal = false"
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
  import { ref, computed } from 'vue'
  import axios from 'axios'
  import { Head, Link, router } from '@inertiajs/vue3'
  import route from 'ziggy-js'
  import {
    ExclamationTriangleIcon,
    InformationCircleIcon,
  } from '@heroicons/vue/24/outline'

  import AppLayout from '@/Layouts/AppLayout.vue'
  import UploadDropzone from '@/Components/Gallery/UploadDropzone.vue'
  import StorageUsageCard from '@/Components/StorageUsageCard.vue'

  const props = defineProps({
    albums: {
      type: Array,
      default: () => [],
    },
    maxUploadSize: {
      type: Number,
      default: 52428800, // 50MB
    },
    // Backend passes extensions (e.g. "jpg,jpeg,png,webp,avif")
    allowedMimes: {
      type: String,
      default: 'jpg,jpeg,png,webp,avif',
    },
    // Normalize name for child prop
    allowedExtensions: {
      type: String,
      default: undefined,
    },
    defaultPrivacy: {
      type: String,
      default: 'unlisted',
    },
    storageUsage: {
      type: Object,
      required: true,
    },
    recentUploads: {
      type: Array,
      default: () => [],
    },
  })

// Form data for album and privacy
const selectedAlbum = ref('')
const selectedPrivacy = ref('unlisted') // Default to unlisted


  const allowedExtensions = computed(() => {
    return props.allowedExtensions?.split(',') || props.allowedMimes.split(',')
  })

  const formatBytes = (bytes) => {
    if (bytes === 0) return '0 B'
    const k = 1024
    const sizes = ['B', 'KB', 'MB', 'GB']
    const i = Math.floor(Math.log(bytes) / Math.log(k))
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
  }

const onFileInputChange = (event) => {
  const files = Array.from(event.target.files)
  selected.value = files
  // Reset input to allow selecting same files again
  event.target.value = ''
}


const getImageUrl = (image, variant = 'small') => {
  // Use direct MinIO URL since thumbnails aren't processed yet
  if (image.storage_path) {
    return `http://localhost:9000/gallery-images/${image.storage_path}`
  }
  // Use direct MinIO URL since thumbnails aren't processed yet
  if (image.storage_path) {
    return `http://localhost:9000/gallery-images/${image.storage_path}`
  }
  return '/images/placeholder.jpg'
}


  const selected = ref([])
  const submitting = ref(false)
  const uploadedCount = ref(0)
  const lastError = ref(null)
  const progressText = computed(() => `${uploadedCount.value}/${selected.value.length}`)
  // Add these to your existing reactive vars
const uploading = ref(false)
const uploadProgress = ref(0)
const showSuccessModal = ref(false)
const uploadedImages = ref([])
const currentFileName = ref('')


  const onFilesSelected = (files) => {
    selected.value = files
  }

  // Fallback bulk upload via direct API → Image records + S3 putFileAs
const submitToStore = async () => {
  if (!selected.value.length) return
  
  try {
    submitting.value = true
    uploading.value = true
    uploadProgress.value = 0
    uploadedCount.value = 0
    lastError.value = null
    showSuccessModal.value = false
    
    await axios.get('/sanctum/csrf-cookie', { withCredentials: true })

    const formData = new FormData()
    selected.value.forEach(f => {
      formData.append('files[]', f)
      currentFileName.value = f.name
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

    // Show success modal instead of redirect
    if (resp.data?.images?.length) {
      uploadedImages.value = resp.data.images
      showSuccessModal.value = true
      selected.value = [] // Clear selection
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

  </script>
