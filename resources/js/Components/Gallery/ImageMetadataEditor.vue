<template>
  <div class="bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl rounded-2xl shadow-xl">
    <div class="p-6">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
          Image Details
        </h3>
        <button
          v-if="editable && !isEditing"
          @click="startEditing"
          class="px-3 py-2 text-sm bg-gradient-to-r from-blue-600 to-cyan-500 text-white rounded-lg hover:from-blue-700 hover:to-cyan-600 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
        >
          Edit
        </button>
      </div>

      <!-- View Mode -->
      <div v-if="!isEditing" class="space-y-4">
        <!-- Title -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            Title
          </label>
          <p class="text-gray-900 dark:text-gray-100">
            {{ localImage.title || 'Untitled' }}
          </p>
        </div>

        <!-- Caption -->
        <div v-if="localImage.caption">
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            Caption
          </label>
          <p class="text-gray-900 dark:text-gray-100">
            {{ localImage.caption }}
          </p>
        </div>

        <!-- Alt Text -->
        <div v-if="localImage.alt_text">
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            Alt Text
          </label>
          <p class="text-gray-900 dark:text-gray-100">
            {{ localImage.alt_text }}
          </p>
        </div>

        <!-- Tags -->
        <div v-if="localImage.tags && localImage.tags.length > 0">
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Tags
          </label>
          <div class="flex flex-wrap gap-2">
            <span
              v-for="tag in localImage.tags"
              :key="tag.id"
              class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gradient-to-r from-blue-100 to-cyan-100 text-blue-800 dark:from-blue-900/50 dark:to-cyan-900/50 dark:text-blue-200"
            >
              {{ tag.name }}
            </span>
          </div>
        </div>

        <!-- Privacy -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            Privacy
          </label>
          <div class="flex items-center space-x-2">
            <div 
              :class="[
                'w-2 h-2 rounded-full',
                localImage.privacy === 'public' ? 'bg-green-500' : 
                localImage.privacy === 'unlisted' ? 'bg-yellow-500' : 'bg-red-500'
              ]"
            ></div>
            <span class="text-gray-900 dark:text-gray-100 capitalize">
              {{ localImage.privacy || 'private' }}
            </span>
          </div>
        </div>

        <!-- License -->
        <div v-if="localImage.license">
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            License
          </label>
          <p class="text-gray-900 dark:text-gray-100">
            {{ localImage.license }}
          </p>
        </div>

        <!-- Date Taken -->
        <div v-if="localImage.taken_at">
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            Date Taken
          </label>
          <p class="text-gray-900 dark:text-gray-100">
            {{ formatDate(localImage.taken_at) }}
          </p>
        </div>

        <!-- Camera Info -->
        <div v-if="localImage.camera_make || localImage.camera_model">
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            Camera
          </label>
          <p class="text-gray-900 dark:text-gray-100">
            {{ [localImage.camera_make, localImage.camera_model].filter(Boolean).join(' ') }}
          </p>
        </div>

        <!-- EXIF Data -->
        <div v-if="localImage.exif_data && Object.keys(localImage.exif_data).length > 0">
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            EXIF Data
          </label>
          <div class="bg-gray-50 dark:bg-slate-700/50 rounded-lg p-3 space-y-1">
            <div v-if="localImage.exif_data.aperture" class="flex justify-between text-sm">
              <span class="text-gray-600 dark:text-gray-400">Aperture:</span>
              <span class="text-gray-900 dark:text-gray-100">f/{{ localImage.exif_data.aperture }}</span>
            </div>
            <div v-if="localImage.exif_data.shutter_speed" class="flex justify-between text-sm">
              <span class="text-gray-600 dark:text-gray-400">Shutter Speed:</span>
              <span class="text-gray-900 dark:text-gray-100">{{ localImage.exif_data.shutter_speed }}</span>
            </div>
            <div v-if="localImage.exif_data.iso" class="flex justify-between text-sm">
              <span class="text-gray-600 dark:text-gray-400">ISO:</span>
              <span class="text-gray-900 dark:text-gray-100">{{ localImage.exif_data.iso }}</span>
            </div>
            <div v-if="localImage.exif_data.focal_length" class="flex justify-between text-sm">
              <span class="text-gray-600 dark:text-gray-400">Focal Length:</span>
              <span class="text-gray-900 dark:text-gray-100">{{ localImage.exif_data.focal_length }}mm</span>
            </div>
          </div>
        </div>

        <!-- File Info -->
        <div class="border-t border-gray-200 dark:border-slate-700 pt-4 mt-4">
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            File Information
          </label>
          <div class="space-y-1 text-sm">
            <div v-if="localImage.filename" class="flex justify-between">
              <span class="text-gray-600 dark:text-gray-400">Filename:</span>
              <span class="text-gray-900 dark:text-gray-100">{{ localImage.filename }}</span>
            </div>
            <div v-if="localImage.file_size" class="flex justify-between">
              <span class="text-gray-600 dark:text-gray-400">Size:</span>
              <span class="text-gray-900 dark:text-gray-100">{{ formatFileSize(localImage.file_size) }}</span>
            </div>
            <div v-if="localImage.dimensions" class="flex justify-between">
              <span class="text-gray-600 dark:text-gray-400">Dimensions:</span>
              <span class="text-gray-900 dark:text-gray-100">{{ localImage.dimensions.width }} × {{ localImage.dimensions.height }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600 dark:text-gray-400">Uploaded:</span>
              <span class="text-gray-900 dark:text-gray-100">{{ formatDate(localImage.created_at) }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Edit Mode -->
      <form v-else @submit.prevent="saveChanges" class="space-y-4">
        <!-- Title -->
        <div>
          <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            Title
          </label>
          <input
            id="title"
            v-model="editForm.title"
            type="text"
            class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100"
            placeholder="Enter image title"
          />
        </div>

        <!-- Caption -->
        <div>
          <label for="caption" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            Caption
          </label>
          <textarea
            id="caption"
            v-model="editForm.caption"
            rows="3"
            class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100"
            placeholder="Enter image caption"
          ></textarea>
        </div>

        <!-- Alt Text -->
        <div>
          <label for="alt_text" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            Alt Text
          </label>
          <input
            id="alt_text"
            v-model="editForm.alt_text"
            type="text"
            class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100"
            placeholder="Describe the image for accessibility"
          />
        </div>

        <!-- Tags -->
        <div>
          <label for="tags" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            Tags
          </label>
          <div class="flex flex-wrap gap-2 mb-2">
            <span
              v-for="(tag, index) in editForm.tags"
              :key="index"
              class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gradient-to-r from-blue-100 to-cyan-100 text-blue-800 dark:from-blue-900/50 dark:to-cyan-900/50 dark:text-blue-200"
            >
              {{ tag }}
              <button
                type="button"
                @click="removeTag(index)"
                class="ml-2 text-blue-600 dark:text-blue-300 hover:text-blue-800 dark:hover:text-blue-100"
              >
                ×
              </button>
            </span>
          </div>
          <div class="flex">
            <input
              v-model="newTag"
              type="text"
              @keydown.enter.prevent="addTag"
              @keydown.comma.prevent="addTag"
              class="flex-1 px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-l-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100"
              placeholder="Add tags (press Enter or comma)"
            />
            <button
              type="button"
              @click="addTag"
              class="px-4 py-2 bg-gradient-to-r from-blue-600 to-cyan-500 text-white rounded-r-lg hover:from-blue-700 hover:to-cyan-600 transition-all duration-200"
            >
              Add
            </button>
          </div>
        </div>

        <!-- Privacy -->
        <div>
          <label for="privacy" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            Privacy
          </label>
          <select
            id="privacy"
            v-model="editForm.privacy"
            class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100"
          >
            <option value="public">Public - Visible to everyone</option>
            <option value="unlisted">Unlisted - Only accessible with direct link</option>
            <option value="private">Private - Only visible to you</option>
          </select>
        </div>

        <!-- License -->
        <div>
          <label for="license" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            License
          </label>
          <select
            id="license"
            v-model="editForm.license"
            class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100"
          >
            <option value="">No License</option>
            <option value="CC BY">CC BY - Attribution</option>
            <option value="CC BY-SA">CC BY-SA - Attribution-ShareAlike</option>
            <option value="CC BY-NC">CC BY-NC - Attribution-NonCommercial</option>
            <option value="CC BY-NC-SA">CC BY-NC-SA - Attribution-NonCommercial-ShareAlike</option>
            <option value="CC BY-ND">CC BY-ND - Attribution-NoDerivs</option>
            <option value="CC BY-NC-ND">CC BY-NC-ND - Attribution-NonCommercial-NoDerivs</option>
            <option value="All Rights Reserved">All Rights Reserved</option>
          </select>
        </div>

        <!-- Date Taken -->
        <div>
          <label for="taken_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            Date Taken
          </label>
          <input
            id="taken_at"
            v-model="editForm.taken_at"
            type="datetime-local"
            class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100"
          />
        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-slate-700">
          <button
            type="button"
            @click="cancelEditing"
            class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-slate-700 rounded-lg hover:bg-gray-200 dark:hover:bg-slate-600 transition-colors duration-200"
            :disabled="saving"
          >
            Cancel
          </button>
          <button
            type="submit"
            class="px-4 py-2 bg-gradient-to-r from-blue-600 to-cyan-500 text-white rounded-lg hover:from-blue-700 hover:to-cyan-600 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50"
            :disabled="saving"
          >
            {{ saving ? 'Saving...' : 'Save Changes' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, watch } from 'vue'

const props = defineProps({
  image: {
    type: Object,
    required: true
  },
  editable: {
    type: Boolean,
    default: false
  },
  metadata: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['save', 'update:metadata'])

// Reactive data
const isEditing = ref(false)
const saving = ref(false)
const newTag = ref('')
const localImage = ref({ ...props.image })
const localMetadata = ref({ ...props.metadata })

const editForm = reactive({
  title: '',
  caption: '',
  alt_text: '',
  tags: [],
  privacy: 'public',
  license: '',
  taken_at: ''
})

// Methods
const startEditing = () => {
  // Populate form with current image data
  editForm.title = localImage.value.title || ''
  editForm.caption = localImage.value.caption || ''
  editForm.alt_text = localImage.value.alt_text || ''
  editForm.tags = localImage.value.tags ? localImage.value.tags.map(tag => tag.name || tag) : []
  editForm.privacy = localImage.value.privacy || 'public'
  editForm.license = localImage.value.license || ''
  editForm.taken_at = localImage.value.taken_at ? formatDateForInput(localImage.value.taken_at) : ''
  
  isEditing.value = true
}

const cancelEditing = () => {
  isEditing.value = false
}

const addTag = () => {
  const tag = newTag.value.trim()
  if (tag && !editForm.tags.includes(tag)) {
    editForm.tags.push(tag)
    newTag.value = ''
  }
}

const removeTag = (index) => {
  editForm.tags.splice(index, 1)
}

const saveChanges = async () => {
  if (saving.value) return
  
  saving.value = true
  try {
    const updatedData = {
      title: editForm.title,
      caption: editForm.caption,
      alt_text: editForm.alt_text,
      tags: editForm.tags,
      privacy: editForm.privacy,
      license: editForm.license,
      taken_at: editForm.taken_at || null
    }
    
    emit('save', updatedData)
    
    // Update local image data
    Object.assign(localImage.value, updatedData)
    isEditing.value = false
  } catch (error) {
    console.error('Error saving changes:', error)
  } finally {
    saving.value = false
  }
}

const formatDate = (dateString) => {
  if (!dateString) return ''
  try {
    return new Date(dateString).toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'long',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    })
  } catch (error) {
    return ''
  }
}

const formatDateForInput = (dateString) => {
  if (!dateString) return ''
  try {
    const date = new Date(dateString)
    return date.toISOString().slice(0, 16) // Format for datetime-local input
  } catch (error) {
    return ''
  }
}

const formatFileSize = (bytes) => {
  if (!bytes) return ''
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  if (bytes === 0) return '0 Bytes'
  const i = Math.floor(Math.log(bytes) / Math.log(1024))
  return Math.round(bytes / Math.pow(1024, i) * 100) / 100 + ' ' + sizes[i]
}

// Watch for prop changes
watch(() => props.image, (newImage) => {
  localImage.value = { ...newImage }
}, { deep: true })

watch(() => props.metadata, (newVal) => {
  localMetadata.value = { ...newVal }
})
</script>