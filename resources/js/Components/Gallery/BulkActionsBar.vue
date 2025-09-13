<template>
  <div class="relative overflow-hidden bg-gradient-to-br from-slate-900/70 via-slate-800/70 to-slate-900/70 backdrop-blur-xl border border-white/10 rounded-2xl shadow-2xl shadow-violet-500/10 p-6 transition-all duration-500">
    <!-- Animated Background Mesh -->
    <div class="absolute inset-0 opacity-10">
      <svg class="w-full h-full animate-float-bulk" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
        <defs>
          <linearGradient id="bulk-mesh" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" class="animate-gradient-shift">
              <animate attributeName="stop-color" values="#8b5cf6;#06b6d4;#a855f7;#8b5cf6" dur="8s" repeatCount="indefinite"/>
            </stop>
            <stop offset="100%" class="animate-gradient-shift">
              <animate attributeName="stop-color" values="#06b6d4;#a855f7;#8b5cf6;#06b6d4" dur="8s" repeatCount="indefinite"/>
            </stop>
          </linearGradient>
        </defs>
        <rect x="10" y="30" width="30" height="40" fill="none" stroke="url(#bulk-mesh)" stroke-width="0.3" opacity="0.4" rx="4"/>
        <rect x="60" y="30" width="30" height="40" fill="none" stroke="url(#bulk-mesh)" stroke-width="0.3" opacity="0.4" rx="4"/>
        <circle cx="25" cy="40" r="1" fill="url(#bulk-mesh)" opacity="0.6"/>
        <circle cx="75" cy="40" r="1" fill="url(#bulk-mesh)" opacity="0.6"/>
        <line x1="45" y1="30" x2="45" y2="70" stroke="url(#bulk-mesh)" stroke-width="0.2" opacity="0.3"/>
        <line x1="20" y1="50" x2="80" y2="50" stroke="url(#bulk-mesh)" stroke-width="0.2" opacity="0.3"/>
      </svg>
    </div>

    <!-- Floating Particles -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
      <div class="particle absolute w-1 h-1 bg-gradient-to-r from-violet-400 to-cyan-400 rounded-full opacity-40 animate-float-bulk-1" style="left: 10%; top: 30%;"></div>
      <div class="particle absolute w-0.5 h-0.5 bg-gradient-to-r from-cyan-400 to-purple-400 rounded-full opacity-50 animate-float-bulk-2" style="left: 85%; top: 25%;"></div>
      <div class="particle absolute w-1 h-1 bg-gradient-to-r from-purple-400 to-violet-400 rounded-full opacity-30 animate-float-bulk-3" style="left: 45%; top: 70%;"></div>
    </div>

    <!-- Main Content -->
    <div class="relative z-10 flex items-center justify-between">
      <div class="flex items-center space-x-6">
        <span class="text-sm font-medium bg-gradient-to-r from-slate-200 to-slate-300 bg-clip-text text-transparent animate-shimmer-count">
          {{ selectedCount }} image{{ selectedCount !== 1 ? 's' : '' }} selected
        </span>
        
        <button
          @click="$emit('clear-selection')"
          class="group text-sm font-medium text-slate-400 hover:text-white transition-all duration-300 hover:scale-105 relative"
        >
          <div class="absolute inset-0 rounded-lg bg-gradient-to-r from-slate-700/0 to-slate-600/0 group-hover:from-slate-700/30 group-hover:to-slate-600/30 transition-all duration-300 -m-2"></div>
          <span class="relative">Clear selection</span>
        </button>
      </div>

      <div class="flex items-center space-x-3">
        <!-- Move to album -->
        <Dropdown align="right" width="48" v-if="permissions.canMove">
          <template #trigger>
            <button class="group inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-slate-700/50 to-slate-600/50 backdrop-blur-sm border border-white/10 shadow-lg shadow-violet-500/5 text-sm font-medium rounded-xl text-slate-300 transition-all duration-300 hover:scale-105 hover:from-slate-600/60 hover:to-slate-500/60 hover:text-white hover:border-violet-500/30 hover:shadow-violet-500/20">
              <div class="relative">
                <div class="absolute inset-0 rounded-lg bg-gradient-to-r from-violet-500/0 to-cyan-500/0 group-hover:from-violet-500/20 group-hover:to-cyan-500/20 blur-sm transition-all duration-300"></div>
                <FolderIcon class="relative h-4 w-4 mr-2" />
              </div>
              Move to Album
              <ChevronDownIcon class="ml-2 h-4 w-4 transition-transform group-hover:rotate-180 duration-300" />
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
          class="group inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-slate-700/50 to-slate-600/50 backdrop-blur-sm border border-white/10 shadow-lg shadow-violet-500/5 text-sm font-medium rounded-xl text-slate-300 transition-all duration-300 hover:scale-105 hover:from-slate-600/60 hover:to-slate-500/60 hover:text-white hover:border-violet-500/30 hover:shadow-violet-500/20"
        >
          <div class="relative">
            <div class="absolute inset-0 rounded-lg bg-gradient-to-r from-violet-500/0 to-cyan-500/0 group-hover:from-violet-500/20 group-hover:to-cyan-500/20 blur-sm transition-all duration-300"></div>
            <PencilIcon class="relative h-4 w-4 mr-2" />
          </div>
          Edit
        </button>

        <!-- Download -->
        <button
          v-if="permissions.canDownload"
          @click="$emit('download')"
          class="group inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-emerald-600/20 to-emerald-500/20 backdrop-blur-sm border border-emerald-500/20 shadow-lg shadow-emerald-500/5 text-sm font-medium rounded-xl text-emerald-300 transition-all duration-300 hover:scale-105 hover:from-emerald-500/30 hover:to-emerald-400/30 hover:text-emerald-200 hover:border-emerald-400/40 hover:shadow-emerald-500/20"
        >
          <div class="relative">
            <div class="absolute inset-0 rounded-lg bg-emerald-500/20 blur-sm opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <ArrowDownTrayIcon class="relative h-4 w-4 mr-2" />
          </div>
          Download
        </button>

        <!-- Delete -->
        <button
          v-if="permissions.canDelete"
          @click="$emit('delete')"
          class="group inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-rose-600/20 to-rose-500/20 backdrop-blur-sm border border-rose-500/20 shadow-lg shadow-rose-500/5 text-sm font-medium rounded-xl text-rose-300 transition-all duration-300 hover:scale-105 hover:from-rose-500/30 hover:to-rose-400/30 hover:text-rose-200 hover:border-rose-400/40 hover:shadow-rose-500/20"
        >
          <div class="relative">
            <div class="absolute inset-0 rounded-lg bg-rose-500/20 blur-sm opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <TrashIcon class="relative h-4 w-4 mr-2" />
          </div>
          Delete
        </button>
      </div>
    </div>

    <!-- Edit Modal -->
    <Modal :show="showEditModal" @close="showEditModal = false">
      <template #default>
      <div class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 backdrop-blur-xl border border-white/10 rounded-2xl p-8">
        <!-- Modal Background Mesh -->
        <div class="absolute inset-0 opacity-5">
          <svg class="w-full h-full animate-float-modal" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
            <defs>
              <linearGradient id="modal-mesh" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" style="stop-color:#8b5cf6"/>
                <stop offset="100%" style="stop-color:#06b6d4"/>
              </linearGradient>
            </defs>
            <rect x="20" y="20" width="60" height="60" fill="none" stroke="url(#modal-mesh)" stroke-width="0.5" opacity="0.6" rx="8"/>
            <circle cx="30" cy="30" r="2" fill="url(#modal-mesh)" opacity="0.8"/>
            <circle cx="70" cy="70" r="2" fill="url(#modal-mesh)" opacity="0.8"/>
          </svg>
        </div>

        <div class="relative z-10">
          <div class="text-xl font-semibold mb-6 bg-gradient-to-r from-white to-slate-200 bg-clip-text text-transparent animate-shimmer-modal">
            Bulk Edit Images
          </div>
          <form @submit.prevent="handleBulkEdit">
            <div class="space-y-6">
              <div>
                <label for="privacy" class="block text-sm font-medium text-slate-300 mb-2">
                  Privacy
                </label>
                <select
                  v-model="editForm.privacy"
                  id="privacy"
                  class="w-full bg-slate-800/50 backdrop-blur-sm border border-white/10 rounded-xl shadow-lg px-4 py-3 text-slate-200 focus:border-violet-500/50 focus:ring-2 focus:ring-violet-500/20 transition-all duration-300"
                >
                  <option value="">Don't change</option>
                  <option value="public">Public</option>
                  <option value="unlisted">Unlisted</option>
                  <option value="private">Private</option>
                </select>
              </div>

              <div>
                <label for="license" class="block text-sm font-medium text-slate-300 mb-2">
                  License
                </label>
                <input
                  v-model="editForm.license"
                  id="license"
                  type="text"
                  placeholder="Don't change"
                  class="w-full bg-slate-800/50 backdrop-blur-sm border border-white/10 rounded-xl shadow-lg px-4 py-3 text-slate-200 placeholder-slate-400 focus:border-violet-500/50 focus:ring-2 focus:ring-violet-500/20 transition-all duration-300"
                >
              </div>

              <div class="space-y-4">
                <label class="flex items-center group cursor-pointer">
                  <input
                    v-model="editForm.allow_comments"
                    type="checkbox"
                    class="rounded border-white/20 bg-slate-800/50 text-violet-500 shadow-sm focus:border-violet-400 focus:ring focus:ring-violet-500/20 transition-all duration-300"
                  >
                  <span class="ml-3 text-sm text-slate-300 group-hover:text-white transition-colors duration-300">Allow comments</span>
                </label>

                <label class="flex items-center group cursor-pointer">
                  <input
                    v-model="editForm.allow_downloads"
                    type="checkbox"
                    class="rounded border-white/20 bg-slate-800/50 text-violet-500 shadow-sm focus:border-violet-400 focus:ring focus:ring-violet-500/20 transition-all duration-300"
                  >
                  <span class="ml-3 text-sm text-slate-300 group-hover:text-white transition-colors duration-300">Allow downloads</span>
                </label>
              </div>
            </div>

            <div class="mt-8 flex justify-end space-x-4">
              <button
                type="button"
                @click="showEditModal = false"
                class="group px-6 py-3 bg-slate-700/50 backdrop-blur-sm border border-white/10 rounded-xl text-sm font-medium text-slate-300 transition-all duration-300 hover:scale-105 hover:bg-slate-600/50 hover:text-white hover:border-white/20"
              >
                Cancel
              </button>
              <button
                type="submit"
                class="group px-6 py-3 bg-gradient-to-r from-violet-600 to-cyan-600 border border-transparent rounded-xl shadow-lg shadow-violet-500/20 text-sm font-medium text-white transition-all duration-300 hover:scale-105 hover:from-violet-500 hover:to-cyan-500 hover:shadow-violet-500/30"
              >
                <span class="relative">Update Images</span>
              </button>
            </div>
          </form>
        </div>
      </div>
      </template>
    </Modal>

    <!-- Enhanced Hover Border Effect -->
    <div class="absolute inset-0 rounded-2xl bg-gradient-to-r from-violet-500/0 via-cyan-500/0 to-purple-500/0 hover:from-violet-500/5 hover:via-cyan-500/5 hover:to-purple-500/5 transition-all duration-700 pointer-events-none"></div>
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

<style scoped>
@keyframes float-bulk {
  0%, 100% { transform: translateY(0px) rotate(0deg); }
  25% { transform: translateY(-2px) rotate(0.3deg); }
  50% { transform: translateY(-4px) rotate(0deg); }
  75% { transform: translateY(-2px) rotate(-0.3deg); }
}

@keyframes float-bulk-1 {
  0%, 100% { transform: translate(0px, 0px); opacity: 0.4; }
  25% { transform: translate(5px, -5px); opacity: 0.7; }
  50% { transform: translate(10px, 2px); opacity: 0.2; }
  75% { transform: translate(2px, 7px); opacity: 0.5; }
}

@keyframes float-bulk-2 {
  0%, 100% { transform: translate(0px, 0px); opacity: 0.5; }
  33% { transform: translate(-6px, 5px); opacity: 0.8; }
  66% { transform: translate(5px, -2px); opacity: 0.3; }
}

@keyframes float-bulk-3 {
  0%, 100% { transform: translate(0px, 0px); opacity: 0.3; }
  40% { transform: translate(8px, -8px); opacity: 0.6; }
  80% { transform: translate(-5px, 5px); opacity: 0.1; }
}

@keyframes float-modal {
  0%, 100% { transform: translateY(0px) rotate(0deg); }
  50% { transform: translateY(-3px) rotate(1deg); }
}

@keyframes shimmer-count {
  0% { background-position: -200% 0; }
  100% { background-position: 200% 0; }
}

@keyframes shimmer-modal {
  0% { background-position: -200% 0; }
  100% { background-position: 200% 0; }
}

@keyframes gradient-shift {
  0%, 100% { stop-color: #8b5cf6; }
  25% { stop-color: #06b6d4; }
  50% { stop-color: #a855f7; }
  75% { stop-color: #8b5cf6; }
}

.animate-float-bulk {
  animation: float-bulk 8s ease-in-out infinite;
}

.animate-float-bulk-1 {
  animation: float-bulk-1 6s ease-in-out infinite;
}

.animate-float-bulk-2 {
  animation: float-bulk-2 8s ease-in-out infinite 1s;
}

.animate-float-bulk-3 {
  animation: float-bulk-3 7s ease-in-out infinite 2s;
}

.animate-float-modal {
  animation: float-modal 6s ease-in-out infinite;
}

.animate-shimmer-count {
  background-image: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
  background-size: 200% 100%;
  animation: shimmer-count 3s ease-in-out infinite;
}

.animate-shimmer-modal {
  background-image: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
  background-size: 200% 100%;
  animation: shimmer-modal 4s ease-in-out infinite;
}

.animate-gradient-shift {
  animation: gradient-shift 8s ease-in-out infinite;
}
</style>