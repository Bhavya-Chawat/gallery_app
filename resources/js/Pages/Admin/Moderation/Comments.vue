<template>
  <AdminLayout>
    <Head title="Comment Moderation" />

    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Comment Moderation
          </h2>
          <p class="text-sm text-gray-600 mt-1">
            Review and moderate user comments
          </p>
        </div>
        <div class="flex items-center space-x-4">
          <span class="text-sm text-gray-500">
            {{ comments.meta?.total || 0 }} comments
          </span>
        </div>
      </div>
    </template>

    <div class="space-y-6">
      <!-- Status Stats -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <StatCard
          title="Pending"
          :value="statuses.pending || 0"
          icon="ClockIcon"
          color="yellow"
        />
        <StatCard
          title="Approved"
          :value="statuses.approved || 0"
          icon="CheckCircleIcon"
          color="green"
        />
        <StatCard
          title="Rejected"
          :value="statuses.rejected || 0"
          icon="XCircleIcon"
          color="red"
        />
        <StatCard
          title="Spam"
          :value="statuses.spam || 0"
          icon="ExclamationTriangleIcon"
          color="red"
        />
      </div>

      <!-- Filters -->
      <div class="bg-white rounded-lg shadow p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
            <input
              v-model="searchForm.search"
              @keyup.enter="search"
              type="text"
              placeholder="Search comments..."
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
            />
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select
              v-model="searchForm.status"
              @change="search"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
            >
              <option value="pending">Pending</option>
              <option value="approved">Approved</option>
              <option value="rejected">Rejected</option>
              <option value="spam">Spam</option>
              <option value="all">All</option>
            </select>
          </div>
          
          <div class="flex items-end">
            <button
              @click="clearFilters"
              class="w-full px-4 py-2 text-sm text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50"
            >
              Clear Filters
            </button>
          </div>
        </div>
      </div>

      <!-- Bulk Actions -->
      <div v-if="selectedComments.length > 0" class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-center justify-between">
          <span class="text-sm font-medium text-blue-900">
            {{ selectedComments.length }} comment(s) selected
          </span>
          <div class="flex items-center space-x-2">
            <select
              v-model="bulkAction"
              class="text-sm border border-blue-300 rounded-md px-3 py-1"
            >
              <option value="">Select action...</option>
              <option value="approve">Approve</option>
              <option value="reject">Reject</option>
              <option value="spam">Mark as Spam</option>
              <option value="delete">Delete</option>
            </select>
            <button
              @click="executeBulkAction"
              :disabled="!bulkAction || bulkActionLoading"
              class="px-4 py-1 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700 disabled:opacity-50"
            >
              {{ bulkActionLoading ? 'Processing...' : 'Apply' }}
            </button>
            <button
              @click="clearSelection"
              class="px-4 py-1 bg-gray-300 text-gray-700 text-sm rounded-md hover:bg-gray-400"
            >
              Clear
            </button>
          </div>
        </div>
      </div>

      <!-- Comments List -->
      <div class="bg-white rounded-lg shadow">
        <div class="divide-y divide-gray-200">
          <div v-if="comments.data && comments.data.length > 0">
            <div 
              v-for="comment in comments.data" 
              :key="comment.id"
              class="p-6 hover:bg-gray-50"
            >
              <div class="flex items-start space-x-4">
                <div class="flex-shrink-0">
                  <input
                    v-model="selectedComments"
                    :value="comment.id"
                    type="checkbox"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                  />
                </div>
                
                <UserAvatar :user="comment.user" size="sm" />
                
                <div class="flex-1 min-w-0">
                  <div class="flex items-center justify-between">
                    <div>
                      <p class="text-sm font-medium text-gray-900">{{ comment.user?.name }}</p>
                      <p class="text-sm text-gray-500">
                        on <span class="font-medium">{{ comment.image?.title }}</span>
                      </p>
                    </div>
                    <div class="flex items-center space-x-2">
                      <span 
                        :class="getStatusBadgeClass(comment.status)"
                        class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                      >
                        {{ comment.status }}
                      </span>
                      <span class="text-sm text-gray-500">
                        {{ formatDate(comment.created_at) }}
                      </span>
                    </div>
                  </div>
                  
                  <div class="mt-2">
                    <p class="text-sm text-gray-900">{{ comment.body }}</p>
                  </div>
                  
                  <div class="mt-4 flex items-center space-x-4">
                    <button
                      v-if="comment.status !== 'approved'"
                      @click="approveComment(comment)"
                      class="text-green-600 hover:text-green-500 text-sm font-medium"
                    >
                      Approve
                    </button>
                    <button
                      v-if="comment.status !== 'rejected'"
                      @click="rejectComment(comment)"
                      class="text-red-600 hover:text-red-500 text-sm font-medium"
                    >
                      Reject
                    </button>
                    <button
                      v-if="comment.status !== 'spam'"
                      @click="markAsSpam(comment)"
                      class="text-orange-600 hover:text-orange-500 text-sm font-medium"
                    >
                      Spam
                    </button>
                    <Link
                      :href="route('images.show', comment.image?.slug)"
                      class="text-blue-600 hover:text-blue-500 text-sm font-medium"
                      target="_blank"
                    >
                      View Image
                    </Link>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <div v-else class="text-center py-12">
            <ChatBubbleLeftIcon class="mx-auto h-12 w-12 text-gray-400" />
            <p class="mt-2 text-sm text-gray-500">No comments found</p>
          </div>
        </div>
        
        <!-- Pagination -->
        <div v-if="comments.links" class="bg-gray-50 px-4 py-3 border-t border-gray-200">
          <Pagination :links="comments.links" />
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import route from 'ziggy-js'
import { 
  ClockIcon, 
  CheckCircleIcon, 
  XCircleIcon, 
  ExclamationTriangleIcon,
  ChatBubbleLeftIcon 
} from '@heroicons/vue/24/outline'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import StatCard from '@/Components/Admin/StatCard.vue'
import UserAvatar from '@/Components/UserAvatar.vue'
import Pagination from '@/Components/Pagination.vue'

const props = defineProps({
  comments: Object,
  filters: Object,
  statuses: Object,
})

const searchForm = reactive({
  search: props.filters.search || '',
  status: props.filters.status || 'pending',
})

const selectedComments = ref([])
const bulkAction = ref('')
const bulkActionLoading = ref(false)

const search = () => {
  const params = Object.fromEntries(
    Object.entries(searchForm).filter(([_, value]) => value !== '')
  )
  
  router.get(route('admin.moderation.comments'), params, {
    preserveState: true,
    replace: true,
  })
}

const clearFilters = () => {
  searchForm.search = ''
  searchForm.status = 'pending'
  search()
}

const clearSelection = () => {
  selectedComments.value = []
  bulkAction.value = ''
}

const getStatusBadgeClass = (status) => {
  const classes = {
    pending: 'text-yellow-800 bg-yellow-100',
    approved: 'text-green-800 bg-green-100',
    rejected: 'text-red-800 bg-red-100',
    spam: 'text-red-800 bg-red-100',
  }
  return classes[status] || classes.pending
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString()
}

const approveComment = (comment) => {
  router.post(route('admin.moderation.approve-comment', comment.id))
}

const rejectComment = (comment) => {
  router.post(route('admin.moderation.reject-comment', comment.id))
}

const markAsSpam = (comment) => {
  router.post(route('admin.moderation.mark-spam', comment.id))
}

const executeBulkAction = () => {
  if (!bulkAction.value || selectedComments.value.length === 0) return

  if (bulkAction.value === 'delete') {
    if (!confirm(`Are you sure you want to delete ${selectedComments.value.length} comment(s)?`)) {
      return
    }
  }

  bulkActionLoading.value = true

  router.post(route('admin.moderation.bulk-moderate'), {
    action: bulkAction.value,
    comment_ids: selectedComments.value,
  }, {
    onSuccess: () => {
      clearSelection()
      bulkActionLoading.value = false
    },
    onError: () => {
      bulkActionLoading.value = false
    }
  })
}
</script>
/*<template>
  <AdminLayout>
    <Head title="Comment Moderation" />

    <template #header>
      <div class="flex items-center justify-between animate-fadeInUp">
        <div class="relative">
          <!-- Floating particles around header -->
          <div class="absolute -top-2 -left-2 w-1 h-1 bg-violet-400 rounded-full animate-pulse opacity-60"></div>
          <div class="absolute -bottom-1 -right-1 w-0.5 h-0.5 bg-cyan-400 rounded-full animate-ping opacity-40"></div>
          
          <h2 class="font-bold text-2xl bg-gradient-to-r from-violet-400 via-purple-400 to-cyan-400 bg-clip-text text-transparent animate-shimmer">
            Comment Moderation
          </h2>
          <p class="text-sm text-slate-300 mt-1">
            Review and moderate user comments
          </p>
        </div>
        <div class="flex items-center space-x-4">
          <div class="px-3 py-2 bg-slate-800/50 backdrop-blur-sm rounded-xl border border-slate-600/30 shadow-lg">
            <span class="text-sm text-slate-300 font-medium">
              {{ comments.meta?.total || 0 }} comments
            </span>
          </div>
        </div>
      </div>
    </template>

    <div class="space-y-6">
      <!-- Enhanced Status Stats -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 animate-fadeInUp" style="animation-delay: 0.1s;">
        <StatCard
          title="Pending"
          :value="statuses.pending || 0"
          icon="ClockIcon"
          color="yellow"
          class="hover:scale-105 transition-transform duration-300"
        />
        <StatCard
          title="Approved"
          :value="statuses.approved || 0"
          icon="CheckCircleIcon"
          color="green"
          class="hover:scale-105 transition-transform duration-300"
        />
        <StatCard
          title="Rejected"
          :value="statuses.rejected || 0"
          icon="XCircleIcon"
          color="red"
          class="hover:scale-105 transition-transform duration-300"
        />
        <StatCard
          title="Spam"
          :value="statuses.spam || 0"
          icon="ExclamationTriangleIcon"
          color="red"
          class="hover:scale-105 transition-transform duration-300"
        />
      </div>

      <!-- Enhanced Filters -->
      <div class="relative bg-slate-900/60 backdrop-blur-xl rounded-2xl shadow-2xl shadow-slate-900/20 p-6 border border-slate-700/50 animate-fadeInUp" style="animation-delay: 0.2s;">
        <!-- Filter glow effect -->
        <div class="absolute inset-0 bg-gradient-to-r from-violet-500/5 via-transparent to-cyan-500/5 rounded-2xl pointer-events-none"></div>
        
        <!-- Floating particles -->
        <div class="absolute top-2 left-4 w-0.5 h-0.5 bg-purple-400 rounded-full animate-pulse opacity-40"></div>
        <div class="absolute bottom-3 right-6 w-1 h-1 bg-violet-400 rounded-full animate-bounce opacity-30"></div>
        
        <div class="relative grid grid-cols-1 md:grid-cols-3 gap-6">
          <div class="group">
            <label class="block text-sm font-medium text-slate-300 mb-2">Search</label>
            <div class="relative">
              <input
                v-model="searchForm.search"
                @keyup.enter="search"
                type="text"
                placeholder="Search comments..."
                class="w-full px-4 py-3 bg-slate-800/60 backdrop-blur-sm border border-slate-600/50 rounded-xl 
                       text-slate-100 placeholder-slate-400 
                       focus:bg-slate-700/60 focus:border-violet-500/50 focus:ring-4 focus:ring-violet-500/20 
                       hover:border-slate-500/50 transition-all duration-300 outline-none shadow-lg"
              />
              <!-- Input shimmer effect -->
              <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-transparent via-white/5 to-transparent -skew-x-12 opacity-0 group-focus-within:opacity-100 group-focus-within:animate-shimmer transition-opacity duration-700 pointer-events-none"></div>
            </div>
          </div>
          
          <div class="group">
            <label class="block text-sm font-medium text-slate-300 mb-2">Status</label>
            <div class="relative">
              <select
                v-model="searchForm.status"
                @change="search"
                class="w-full px-4 py-3 bg-slate-800/60 backdrop-blur-sm border border-slate-600/50 rounded-xl 
                       text-slate-100 focus:bg-slate-700/60 focus:border-violet-500/50 focus:ring-4 focus:ring-violet-500/20 
                       hover:border-slate-500/50 transition-all duration-300 outline-none shadow-lg"
              >
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
                <option value="spam">Spam</option>
                <option value="all">All</option>
              </select>
            </div>
          </div>
          
          <div class="flex items-end">
            <button
              @click="clearFilters"
              class="group relative w-full px-4 py-3 text-sm font-medium text-slate-300 bg-slate-800/50 backdrop-blur-sm border border-slate-600/30 rounded-xl 
                     hover:bg-slate-700/50 hover:text-white hover:border-violet-500/40 hover:scale-105 
                     transition-all duration-300 shadow-lg hover:shadow-xl hover:shadow-violet-500/20"
            >
              <!-- Button glow effect -->
              <div class="absolute inset-0 bg-gradient-to-r from-violet-500/0 to-cyan-500/0 group-hover:from-violet-500/10 group-hover:to-cyan-500/10 rounded-xl transition-all duration-300"></div>
              <span class="relative">Clear Filters</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Enhanced Bulk Actions -->
      <div v-if="selectedComments.length > 0" class="relative bg-gradient-to-r from-violet-900/40 via-purple-900/40 to-cyan-900/40 backdrop-blur-xl border border-violet-500/30 rounded-2xl p-4 shadow-2xl shadow-violet-500/20 animate-slideInUp">
        <!-- Bulk actions glow -->
        <div class="absolute inset-0 bg-gradient-to-r from-violet-500/10 via-purple-500/10 to-cyan-500/10 rounded-2xl pointer-events-none"></div>
        
        <div class="relative flex items-center justify-between">
          <span class="text-sm font-medium text-violet-300">
            {{ selectedComments.length }} comment(s) selected
          </span>
          <div class="flex items-center space-x-3">
            <select
              v-model="bulkAction"
              class="text-sm bg-slate-800/60 backdrop-blur-sm border border-violet-500/30 rounded-xl px-3 py-2 text-slate-200 focus:border-violet-400/50 focus:ring-2 focus:ring-violet-500/20 transition-all duration-300"
            >
              <option value="">Select action...</option>
              <option value="approve">Approve</option>
              <option value="reject">Reject</option>
              <option value="spam">Mark as Spam</option>
              <option value="delete">Delete</option>
            </select>
            <button
              @click="executeBulkAction"
              :disabled="!bulkAction || bulkActionLoading"
              class="group relative px-4 py-2 bg-violet-600/80 backdrop-blur-sm text-white text-sm rounded-xl 
                     hover:bg-violet-500/80 hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed
                     transition-all duration-300 shadow-lg hover:shadow-xl hover:shadow-violet-500/30"
            >
              <!-- Button glow -->
              <div class="absolute inset-0 bg-gradient-to-r from-violet-500/20 to-purple-500/20 group-hover:from-violet-400/30 group-hover:to-purple-400/30 rounded-xl transition-all duration-300"></div>
              <span class="relative">{{ bulkActionLoading ? 'Processing...' : 'Apply' }}</span>
            </button>
            <button
              @click="clearSelection"
              class="group relative px-4 py-2 bg-slate-600/60 backdrop-blur-sm text-slate-200 text-sm rounded-xl 
                     hover:bg-slate-500/60 hover:scale-105 transition-all duration-300 shadow-lg"
            >
              <span class="relative">Clear</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Enhanced Comments List -->
      <div class="relative bg-slate-900/60 backdrop-blur-xl rounded-2xl shadow-2xl shadow-slate-900/20 border border-slate-700/50 overflow-hidden animate-fadeInUp" style="animation-delay: 0.3s;">
        <!-- Container glow effect -->
        <div class="absolute inset-0 bg-gradient-to-r from-violet-500/5 via-transparent to-cyan-500/5 pointer-events-none"></div>
        
        <div class="relative divide-y divide-slate-700/50">
          <div v-if="comments.data && comments.data.length > 0">
            <div 
              v-for="(comment, index) in comments.data" 
              :key="comment.id"
              class="group relative p-6 hover:bg-slate-800/40 transition-all duration-300 animate-slideInUp"
              :style="`animation-delay: ${index * 0.05}s`"
            >
              <!-- Comment hover glow -->
              <div class="absolute inset-0 bg-gradient-to-r from-violet-500/0 via-cyan-500/0 to-purple-500/0 group-hover:from-violet-500/5 group-hover:via-cyan-500/5 group-hover:to-purple-500/5 transition-all duration-500 pointer-events-none"></div>
              
              <div class="relative flex items-start space-x-4">
                <div class="flex-shrink-0 mt-1">
                  <div class="relative">
                    <input
                      v-model="selectedComments"
                      :value="comment.id"
                      type="checkbox"
                      class="h-4 w-4 text-violet-600 bg-slate-800/60 border-slate-500/50 rounded focus:ring-violet-500/30 focus:ring-2 transition-all duration-200"
                    />
                    <!-- Checkbox glow -->
                    <div class="absolute -inset-1 bg-violet-500/20 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-300 -z-10"></div>
                  </div>
                </div>
                
                <div class="relative">
                  <UserAvatar :user="comment.user" size="sm" class="ring-2 ring-slate-600/30 group-hover:ring-violet-500/30 transition-all duration-300" />
                </div>
                
                <div class="flex-1 min-w-0">
                  <div class="flex items-center justify-between">
                    <div>
                      <p class="text-sm font-medium text-slate-200 group-hover:text-white transition-colors duration-300">{{ comment.user?.name }}</p>
                      <p class="text-sm text-slate-400 group-hover:text-slate-300 transition-colors duration-300">
                        on <span class="font-medium text-violet-400">{{ comment.image?.title }}</span>
                      </p>
                    </div>
                    <div class="flex items-center space-x-3">
                      <span 
                        :class="getEnhancedStatusBadgeClass(comment.status)"
                        class="inline-flex px-3 py-1 text-xs font-semibold rounded-full backdrop-blur-sm transition-all duration-300 group-hover:scale-105"
                      >
                        {{ comment.status }}
                      </span>
                      <span class="text-sm text-slate-400 group-hover:text-slate-300 transition-colors duration-300">
                        {{ formatDate(comment.created_at) }}
                      </span>
                    </div>
                  </div>
                  
                  <div class="mt-3">
                    <p class="text-sm text-slate-300 group-hover:text-slate-100 transition-colors duration-300 leading-relaxed">{{ comment.body }}</p>
                  </div>
                  
                  <div class="mt-4 flex items-center space-x-4">
                    <button
                      v-if="comment.status !== 'approved'"
                      @click="approveComment(comment)"
                      class="group/btn relative text-emerald-400 hover:text-emerald-300 text-sm font-medium px-2 py-1 rounded-lg hover:bg-emerald-500/10 transition-all duration-300 hover:scale-105"
                    >
                      <span class="relative">Approve</span>
                      <div class="absolute inset-0 bg-emerald-500/0 group-hover/btn:bg-emerald-500/20 rounded-lg transition-all duration-300 -z-10"></div>
                    </button>
                    <button
                      v-if="comment.status !== 'rejected'"
                      @click="rejectComment(comment)"
                      class="group/btn relative text-red-400 hover:text-red-300 text-sm font-medium px-2 py-1 rounded-lg hover:bg-red-500/10 transition-all duration-300 hover:scale-105"
                    >
                      <span class="relative">Reject</span>
                      <div class="absolute inset-0 bg-red-500/0 group-hover/btn:bg-red-500/20 rounded-lg transition-all duration-300 -z-10"></div>
                    </button>
                    <button
                      v-if="comment.status !== 'spam'"
                      @click="markAsSpam(comment)"
                      class="group/btn relative text-orange-400 hover:text-orange-300 text-sm font-medium px-2 py-1 rounded-lg hover:bg-orange-500/10 transition-all duration-300 hover:scale-105"
                    >
                      <span class="relative">Spam</span>
                      <div class="absolute inset-0 bg-orange-500/0 group-hover/btn:bg-orange-500/20 rounded-lg transition-all duration-300 -z-10"></div>
                    </button>
                    <Link
                      :href="route('images.show', comment.image?.slug)"
                      class="group/btn relative text-cyan-400 hover:text-cyan-300 text-sm font-medium px-2 py-1 rounded-lg hover:bg-cyan-500/10 transition-all duration-300 hover:scale-105"
                      target="_blank"
                    >
                      <span class="relative">View Image</span>
                      <div class="absolute inset-0 bg-cyan-500/0 group-hover/btn:bg-cyan-500/20 rounded-lg transition-all duration-300 -z-10"></div>
                    </Link>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Enhanced Empty State -->
          <div v-else class="text-center py-16 animate-fadeInUp">
            <div class="relative inline-block">
              <!-- Floating particles around icon -->
              <div class="absolute -top-2 -left-2 w-1 h-1 bg-violet-400 rounded-full animate-pulse opacity-60"></div>
              <div class="absolute -bottom-1 -right-1 w-0.5 h-0.5 bg-cyan-400 rounded-full animate-ping opacity-40"></div>
              
              <ChatBubbleLeftIcon class="mx-auto h-16 w-16 text-slate-500 group-hover:text-slate-400 transition-colors duration-300" />
            </div>
            <p class="mt-4 text-sm text-slate-400 font-medium">No comments found</p>
          </div>
        </div>
        
        <!-- Enhanced Pagination -->
        <div v-if="comments.links" class="relative bg-slate-800/30 backdrop-blur-sm px-4 py-3 border-t border-slate-700/50">
          <div class="absolute inset-0 bg-gradient-to-r from-violet-500/5 via-transparent to-cyan-500/5 pointer-events-none"></div>
          <div class="relative">
            <Pagination :links="comments.links" />
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import route from 'ziggy-js'
import { 
  ClockIcon, 
  CheckCircleIcon, 
  XCircleIcon, 
  ExclamationTriangleIcon,
  ChatBubbleLeftIcon 
} from '@heroicons/vue/24/outline'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import StatCard from '@/Components/Admin/StatCard.vue'
import UserAvatar from '@/Components/UserAvatar.vue'
import Pagination from '@/Components/Pagination.vue'

const props = defineProps({
  comments: Object,
  filters: Object,
  statuses: Object,
})

const searchForm = reactive({
  search: props.filters.search || '',
  status: props.filters.status || 'pending',
})

const selectedComments = ref([])
const bulkAction = ref('')
const bulkActionLoading = ref(false)

const search = () => {
  const params = Object.fromEntries(
    Object.entries(searchForm).filter(([_, value]) => value !== '')
  )
  
  router.get(route('admin.moderation.comments'), params, {
    preserveState: true,
    replace: true,
  })
}

const clearFilters = () => {
  searchForm.search = ''
  searchForm.status = 'pending'
  search()
}

const clearSelection = () => {
  selectedComments.value = []
  bulkAction.value = ''
}

const getEnhancedStatusBadgeClass = (status) => {
  const classes = {
    pending: 'text-amber-300 bg-amber-500/20 border border-amber-500/30 shadow-lg shadow-amber-500/20',
    approved: 'text-emerald-300 bg-emerald-500/20 border border-emerald-500/30 shadow-lg shadow-emerald-500/20',
    rejected: 'text-red-300 bg-red-500/20 border border-red-500/30 shadow-lg shadow-red-500/20',
    spam: 'text-orange-300 bg-orange-500/20 border border-orange-500/30 shadow-lg shadow-orange-500/20',
  }
  return classes[status] || classes.pending
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString()
}

const approveComment = (comment) => {
  router.post(route('admin.moderation.approve-comment', comment.id))
}

const rejectComment = (comment) => {
  router.post(route('admin.moderation.reject-comment', comment.id))
}

const markAsSpam = (comment) => {
  router.post(route('admin.moderation.mark-spam', comment.id))
}

const executeBulkAction = () => {
  if (!bulkAction.value || selectedComments.value.length === 0) return

  if (bulkAction.value === 'delete') {
    if (!confirm(`Are you sure you want to delete ${selectedComments.value.length} comment(s)?`)) {
      return
    }
  }

  bulkActionLoading.value = true

  router.post(route('admin.moderation.bulk-moderate'), {
    action: bulkAction.value,
    comment_ids: selectedComments.value,
  }, {
    onSuccess: () => {
      clearSelection()
      bulkActionLoading.value = false
    },
    onError: () => {
      bulkActionLoading.value = false
    }
  })
}
</script>

<style scoped>
@keyframes shimmer {
  0% {
    transform: translateX(-100%) skewX(-12deg);
  }
  100% {
    transform: translateX(200%) skewX(-12deg);
  }
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes slideInUp {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-shimmer {
  animation: shimmer 2s ease-in-out;
}

.animate-fadeInUp {
  animation: fadeInUp 0.6s ease-out;
}

.animate-slideInUp {
  animation: slideInUp 0.4s ease-out;
}
</style>