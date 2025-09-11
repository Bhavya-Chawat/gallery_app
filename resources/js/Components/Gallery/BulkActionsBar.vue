<template>
  <div 
    v-if="selectedCount > 0"
    class="fixed bottom-6 left-1/2 transform -translate-x-1/2 bg-white/90 dark:bg-slate-800/90 backdrop-blur-xl border border-gray-200 dark:border-slate-700 rounded-xl shadow-lg px-6 py-4 z-40 transition-all duration-300"
  >
    <div class="flex items-center space-x-6">
      <!-- Selection Count -->
      <div class="flex items-center text-sm text-gray-600 dark:text-slate-400">
        <CheckSquare class="w-4 h-4 mr-2" />
        <span>{{ selectedCount }} selected</span>
      </div>

      <!-- Divider -->
      <div class="h-6 w-px bg-gray-300 dark:bg-slate-600"></div>

      <!-- Actions -->
      <div class="flex items-center space-x-2">
        <!-- Move to Album -->
        <div class="relative" v-if="permissions.canEditImages">
          <button
            @click="showAlbumSelector = !showAlbumSelector"
            class="flex items-center px-3 py-2 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors"
          >
            <FolderPlus class="w-4 h-4 mr-2" />
            Add to Album
            <ChevronDown class="w-4 h-4 ml-1" />
          </button>

          <!-- Album Dropdown -->
          <div 
            v-if="showAlbumSelector"
            class="absolute bottom-full mb-2 left-0 w-64 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg shadow-lg py-2 z-50"
          >
            <div class="px-3 py-2 text-xs font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wide">
              Select Album
            </div>
            
            <div class="max-h-48 overflow-y-auto">
              <button
                v-for="album in availableAlbums"
                :key="album.id"
                @click="moveToAlbum(album.id)"
                class="w-full text-left px-3 py-2 text-sm text-gray-700 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors"
              >
                <div class="flex items-center">
                  <div class="w-6 h-6 rounded bg-gradient-to-r from-blue-600 via-cyan-500 to-blue-400 flex items-center justify-center text-white text-xs font-medium mr-3">
                    {{ album.title.charAt(0).toUpperCase() }}
                  </div>
                  <div class="flex-1 min-w-0">
                    <p class="font-medium truncate">{{ album.title }}</p>
                    <p class="text-xs text-gray-500 dark:text-slate-400">
                      {{ album.images_count }} images
                    </p>
                  </div>
                </div>
              </button>
            </div>
            
            <div class="border-t border-gray-200 dark:border-slate-700 mt-2 pt-2">
              <button
                @click="createNewAlbum"
                class="w-full text-left px-3 py-2 text-sm text-blue-600 dark:text-blue-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors"
              >
                <Plus class="w-4 h-4 inline mr-2" />
                Create New Album
              </button>
            </div>
          </div>
        </div>

        <!-- Edit Metadata -->
        <button
          v-if="permissions.canEditImages"
          @click="editMetadata"
          class="flex items-center px-3 py-2 text-sm bg-cyan-600 hover:bg-cyan-700 text-white rounded-lg transition-colors"
        >
          <Edit class="w-4 h-4 mr-2" />
          Edit
        </button>

        <!-- Download -->
        <button
          v-if="permissions.canDownload"
          @click="downloadSelected"
          :disabled="downloadLoading"
          class="flex items-center px-3 py-2 text-sm bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed text-white rounded-lg transition-colors"
        >
          <Download class="w-4 h-4 mr-2" />
          {{ downloadLoading ? 'Preparing...' : 'Download' }}
        </button>

        <!-- Share -->
        <button
          v-if="permissions.canShare"
          @click="shareSelected"
          class="flex items-center px-3 py-2 text-sm bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors"
        >
          <Share2 class="w-4 h-4 mr-2" />
          Share
        </button>

        <!-- Delete -->
        <button
          v-if="permissions.canDelete"
          @click="confirmDelete"
          class="flex items-center px-3 py-2 text-sm bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors"
        >
          <Trash2 class="w-4 h-4 mr-2" />
          Delete
        </button>
      </div>

      <!-- Divider -->
      <div class="h-6 w-px bg-gray-300 dark:bg-slate-600"></div>

      <!-- Clear Selection -->
      <button
        @click="clearSelection"
        class="flex items-center text-sm text-gray-600 dark:text-slate-400 hover:text-gray-800 dark:hover:text-slate-200 transition-colors"
      >
        <X class="w-4 h-4 mr-2" />
        Clear
      </button>
    </div>
  </div>

  <!-- Delete Confirmation Modal -->
  <div 
    v-if="showDeleteConfirm"
    class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50"
    @click="showDeleteConfirm = false"
  >
    <div 
      class="bg-white dark:bg-slate-800 rounded-xl p-6 max-w-md w-full mx-4"
      @click.stop
    >
      <div class="flex items-center mb-4">
        <div class="p-2 bg-red-100 dark:bg-red-900/30 rounded-lg mr-3">
          <AlertTriangle class="w-6 h-6 text-red-600 dark:text-red-400" />
        </div>
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
          Delete Images
        </h3>
      </div>
      
      <p class="text-gray-600 dark:text-slate-400 mb-6">
        Are you sure you want to delete {{ selectedCount }} image{{ selectedCount > 1 ? 's' : '' }}? 
        This action cannot be undone.
      </p>
      
      <div class="flex justify-end space-x-3">
        <button
          @click="showDeleteConfirm = false"
          class="px-4 py-2 text-gray-600 dark:text-slate-400 hover:text-gray-800 dark:hover:text-slate-200 transition-colors"
        >
          Cancel
        </button>
        <button
          @click="deleteSelected"
          :disabled="deleteLoading"
          class="bg-red-600 hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed text-white px-4 py-2 rounded-lg transition-colors"
        >
          {{ deleteLoading ? 'Deleting...' : 'Delete' }}
        </button>
      </div>
    </div>
  </div>

  <!-- Bulk Edit Modal -->
  <div 
    v-if="showEditModal"
    class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50"
    @click="showEditModal = false"
  >
    <div 
      class="bg-white dark:bg-slate-800 rounded-xl p-6 max-w-lg w-full mx-4 max-h-[90vh] overflow-y-auto"
      @click.stop
    >
      <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
        Edit {{ selectedCount }} Images
      </h3>
      
      <form @submit.prevent="saveBulkEdit" class="space-y-4">
        <!-- Tags -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">
            Tags
          </label>
          <div class="space-y-2">
            <input
              v-model="bulkEditData.addTags"
              type="text"
              placeholder="Tags to add (comma separated)"
              class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
            <input
              v-model="bulkEditData.removeTags"
              type="text"
              placeholder="Tags to remove (comma separated)"
              class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
          </div>
        </div>

        <!-- Privacy -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">
            Privacy
          </label>
          <select 
            v-model="bulkEditData.privacy"
            class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          >
            <option value="">Keep current</option>
            <option value="public">Public</option>
            <option value="unlisted">Unlisted</option>
            <option value="private">Private</option>
          </select>
        </div>

        <!-- License -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">
            License
          </label>
          <select 
            v-model="bulkEditData.license"
            class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          >
            <option value="">Keep current</option>
            <option value="all_rights_reserved">All Rights Reserved</option>
            <option value="cc_by">CC BY</option>
            <option value="cc_by_sa">CC BY-SA</option>
            <option value="cc_by_nc">CC BY-NC</option>
            <option value="cc_by_nc_sa">CC BY-NC-SA</option>
            <option value="cc0">CC0 (Public Domain)</option>
          </select>
        </div>

        <div class="flex justify-end space-x-3 pt-4">
          <button
            type="button"
            @click="showEditModal = false"
            class="px-4 py-2 text-gray-600 dark:text-slate-400 hover:text-gray-800 dark:hover:text-slate-200 transition-colors"
          >
            Cancel
          </button>
          <button
            type="submit"
            :disabled="editLoading"
            class="bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed text-white px-4 py-2 rounded-lg transition-colors"
          >
            {{ editLoading ? 'Saving...' : 'Save Changes' }}
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Create Album Modal -->
  <div 
    v-if="showCreateAlbumModal"
    class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50"
    @click="showCreateAlbumModal = false"
  >
    <div 
      class="bg-white dark:bg-slate-800 rounded-xl p-6 max-w-md w-full mx-4"
      @click.stop
    >
      <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
        Create New Album
      </h3>
      
      <form @submit.prevent="saveNewAlbum" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">
            Album Title
          </label>
          <input
            v-model="newAlbumData.title"
            type="text"
            required
            class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">
            Description
          </label>
          <textarea
            v-model="newAlbumData.description"
            rows="3"
            class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          ></textarea>
        </div>

        <div class="flex justify-end space-x-3">
          <button
            type="button"
            @click="showCreateAlbumModal = false"
            class="px-4 py-2 text-gray-600 dark:text-slate-400 hover:text-gray-800 dark:hover:text-slate-200 transition-colors"
          >
            Cancel
          </button>
          <button
            type="submit"
            :disabled="createAlbumLoading || !newAlbumData.title.trim()"
            class="bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed text-white px-4 py-2 rounded-lg transition-colors"
          >
            {{ createAlbumLoading ? 'Creating...' : 'Create & Add Images' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import {
  CheckSquare, FolderPlus, ChevronDown, Edit, Download, Share2, Trash2, X,
  AlertTriangle, Plus
} from 'lucide-vue-next'
import axios from 'axios'

// Props
const props = defineProps({
  selectedCount: {
    type: Number,
    required: true
  },
  selectedIds: {
    type: Array,
    required: true
  },
  permissions: {
    type: Object,
    default: () => ({
      canEditImages: true,
      canDelete: true,
      canDownload: true,
      canShare: true
    })
  }
})

// Emits
const emit = defineEmits([
  'delete',
  'move-to-album',
  'edit-metadata',
  'download',
  'share',
  'clear-selection'
])

// Reactive data
const showAlbumSelector = ref(false)
const showDeleteConfirm = ref(false)
const showEditModal = ref(false)
const showCreateAlbumModal = ref(false)
const availableAlbums = ref([])
const deleteLoading = ref(false)
const downloadLoading = ref(false)
const editLoading = ref(false)
const createAlbumLoading = ref(false)

const bulkEditData = ref({
  addTags: '',
  removeTags: '',
  privacy: '',
  license: ''
})

const newAlbumData = ref({
  title: '',
  description: ''
})

// Methods
const fetchAlbums = async () => {
  try {
    const response = await axios.get('/api/v1/my/albums')
    availableAlbums.value = response.data.data
  } catch (error) {
    console.error('Failed to fetch albums:', error)
  }
}

const moveToAlbum = async (albumId) => {
  try {
    await axios.post(`/api/v1/albums/${albumId}/add-images`, {
      image_ids: props.selectedIds
    })
    
    emit('move-to-album', props.selectedIds, albumId)
    showAlbumSelector.value = false
  } catch (error) {
    console.error('Failed to move images to album:', error)
  }
}

const editMetadata = () => {
  showEditModal.value = true
}

const saveBulkEdit = async () => {
  editLoading.value = true
  
  try {
    const updateData = {}
    
    if (bulkEditData.value.addTags.trim()) {
      updateData.add_tags = bulkEditData.value.addTags.split(',').map(t => t.trim()).filter(Boolean)
    }
    
    if (bulkEditData.value.removeTags.trim()) {
      updateData.remove_tags = bulkEditData.value.removeTags.split(',').map(t => t.trim()).filter(Boolean)
    }
    
    if (bulkEditData.value.privacy) {
      updateData.privacy = bulkEditData.value.privacy
    }
    
    if (bulkEditData.value.license) {
      updateData.license = bulkEditData.value.license
    }
    
    await axios.post('/api/v1/images/bulk', {
      image_ids: props.selectedIds,
      action: 'update',
      data: updateData
    })
    
    emit('edit-metadata', props.selectedIds, updateData)
    showEditModal.value = false
    
    // Reset form
    bulkEditData.value = {
      addTags: '',
      removeTags: '',
      privacy: '',
      license: ''
    }
  } catch (error) {
    console.error('Failed to update images:', error)
  } finally {
    editLoading.value = false
  }
}

const downloadSelected = async () => {
  downloadLoading.value = true
  
  try {
    const response = await axios.post('/api/v1/images/bulk', {
      image_ids: props.selectedIds,
      action: 'download'
    }, {
      responseType: 'blob'
    })
    
    // Create download link
    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', `images-${Date.now()}.zip`)
    document.body.appendChild(link)
    link.click()
    link.remove()
    window.URL.revokeObjectURL(url)
    
    emit('download', props.selectedIds)
  } catch (error) {
    console.error('Failed to download images:', error)
  } finally {
    downloadLoading.value = false
  }
}

const shareSelected = () => {
  // Open share modal/functionality
  emit('share', props.selectedIds)
}

const confirmDelete = () => {
  showDeleteConfirm.value = true
}

const deleteSelected = async () => {
  deleteLoading.value = true
  
  try {
    await axios.post('/api/v1/images/bulk', {
      image_ids: props.selectedIds,
      action: 'delete'
    })
    
    emit('delete', props.selectedIds)
    showDeleteConfirm.value = false
  } catch (error) {
    console.error('Failed to delete images:', error)
  } finally {
    deleteLoading.value = false
  }
}

const createNewAlbum = () => {
  showAlbumSelector.value = false
  showCreateAlbumModal.value = true
}

const saveNewAlbum = async () => {
  createAlbumLoading.value = true
  
  try {
    const response = await axios.post('/api/v1/albums', {
      title: newAlbumData.value.title,
      description: newAlbumData.value.description || null
    })
    
    const newAlbum = response.data.data
    
    // Add images to new album
    await axios.post(`/api/v1/albums/${newAlbum.id}/add-images`, {
      image_ids: props.selectedIds
    })
    
    availableAlbums.value.push(newAlbum)
    emit('move-to-album', props.selectedIds, newAlbum.id)
    
    showCreateAlbumModal.value = false
    newAlbumData.value = { title: '', description: '' }
  } catch (error) {
    console.error('Failed to create album:', error)
  } finally {
    createAlbumLoading.value = false
  }
}

const clearSelection = () => {
  emit('clear-selection')
}

// Close dropdowns on outside click
const handleClickOutside = (event) => {
  if (!event.target.closest('.relative')) {
    showAlbumSelector.value = false
  }
}

// Lifecycle
onMounted(() => {
  fetchAlbums()
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>