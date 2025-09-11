<template>
  <div class="flex items-center justify-between">
    <div class="flex items-center space-x-4">
      <span class="text-sm text-gray-700">
        {{ selectedCount }} image{{ selectedCount !== 1 ? 's' : '' }} selected
      </span>
      
      <button
        @click="$emit('clear-selection')"
        class="text-sm text-gray-500 hover:text-gray-700"
      >
        Clear selection
      </button>
    </div>

    <div class="flex items-center space-x-2">
      <!-- Move to album -->
      <Dropdown align="right" width="48" v-if="permissions.canMove">
        <template #trigger>
          <button class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
            <FolderIcon class="h-4 w-4 mr-2" />
            Move to Album
            <ChevronDownIcon class="ml-2 h-4 w-4" />
          </button>
        </template>

        <template #content>
          <DropdownLink @click="$emit('move', null)">
            Remove from album
          </DropdownLink>
          <div class="border-t border-gray-100"></div>
          <DropdownLink
            v-for="album in availableAlbums"
            :key="album.id"
            @click="$emit('move', album.id)"
          >
            {{ album.title }}
          </DropdownLink>
        </template>
      </Dropdown>

      <!-- Edit properties -->
      <button
        v-if="permissions.canEdit"
        @click="showEditModal = true"
        class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
      >
        <PencilIcon class="h-4 w-4 mr-2" />
        Edit
      </button>

      <!-- Download -->
      <button
        v-if="permissions.canDownload"
        @click="$emit('download')"
        class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
      >
        <ArrowDownTrayIcon class="h-4 w-4 mr-2" />
        Download
      </button>

      <!-- Delete -->
      <button
        v-if="permissions.canDelete"
        @click="$emit('delete')"
        class="inline-flex items-center px-3 py-2 border border-red-300 shadow-sm text-sm leading-4 font-medium rounded-md text-red-700 bg-white hover:bg-red-50"
      >
        <TrashIcon class="h-4 w-4 mr-2" />
        Delete
      </button>
    </div>

    <!-- Edit Modal -->
    <Modal :show="showEditModal" @close="showEditModal = false">
      <template #default>
      <div class="text-lg font-medium mb-4">Bulk Edit Images</div>
      <form @submit.prevent="handleBulkEdit">
        <div class="space-y-4">
          <div>
            <label for="privacy" class="block text-sm font-medium text-gray-700">
              Privacy
            </label>
            <select
              v-model="editForm.privacy"
              id="privacy"
              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
            >
              <option value="">Don't change</option>
              <option value="public">Public</option>
              <option value="unlisted">Unlisted</option>
              <option value="private">Private</option>
            </select>
          </div>

          <div>
            <label for="license" class="block text-sm font-medium text-gray-700">
              License
            </label>
            <input
              v-model="editForm.license"
              id="license"
              type="text"
              placeholder="Don't change"
              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
            >
          </div>

          <div class="space-y-2">
            <label class="flex items-center">
              <input
                v-model="editForm.allow_comments"
                type="checkbox"
                class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200"
              >
              <span class="ml-2 text-sm text-gray-700">Allow comments</span>
            </label>

            <label class="flex items-center">
              <input
                v-model="editForm.allow_downloads"
                type="checkbox"
                class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200"
              >
              <span class="ml-2 text-sm text-gray-700">Allow downloads</span>
            </label>
          </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
          <button
            type="button"
            @click="showEditModal = false"
            class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50"
          >
            Cancel
          </button>
          <button
            type="submit"
            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700"
          >
            Update Images
          </button>
        </div>
      </form>
      </template>
    </Modal>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import {
  FolderIcon,
  PencilIcon,
  ArrowDownTrayIcon,
  TrashIcon,
  ChevronDownIcon,
} from '@heroicons/vue/24/outline'

import Dropdown from '@/Components/Dropdown.vue'
import DropdownLink from '@/Components/DropdownLink.vue'
import Modal from '@/Components/Modal.vue'

const props = defineProps({
  selectedCount: {
    type: Number,
    required: true,
  },
  permissions: {
    type: Object,
    default: () => ({}),
  },
  availableAlbums: {
    type: Array,
    default: () => [],
  },
})

const emit = defineEmits(['clear-selection', 'move', 'edit', 'download', 'delete'])

const showEditModal = ref(false)
const editForm = reactive({
  privacy: '',
  license: '',
  allow_comments: null,
  allow_downloads: null,
})

const handleBulkEdit = () => {
  const data = {}
  
  if (editForm.privacy) data.privacy = editForm.privacy
  if (editForm.license) data.license = editForm.license
  if (editForm.allow_comments !== null) data.allow_comments = editForm.allow_comments
  if (editForm.allow_downloads !== null) data.allow_downloads = editForm.allow_downloads
  
  emit('edit', data)
  showEditModal.value = false
  
  // Reset form
  Object.keys(editForm).forEach(key => {
    editForm[key] = key.includes('allow_') ? null : ''
  })
}
</script>
