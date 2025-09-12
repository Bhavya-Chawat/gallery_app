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
