<template>
  <div class="space-y-6">
    <!-- Add error alert if present -->
    <div v-if="error" class="bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 p-4 rounded-lg mb-4">
      {{ error }}
    </div>

    <!-- Add loading state -->
    <div v-if="loading" class="text-center py-8">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mx-auto"></div>
    </div>

    <div v-if="comments.length === 0" class="text-center py-8">
      <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-slate-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
      </svg>
      <p class="text-gray-500 dark:text-slate-400">No comments yet</p>
      <p class="text-sm text-gray-400 dark:text-slate-500 mt-1">Be the first to leave a comment!</p>
    </div>

    <div
      v-for="comment in comments"
      :key="comment.id"
      class="flex space-x-4 border-b border-gray-100 dark:border-slate-700 last:border-b-0 pb-4 last:pb-0"
    >
      <div class="flex-shrink-0">
        <Link :href="route('profile.view', comment.user.username)">
          <img 
            :src="comment.user.avatar_url" 
            :alt="comment.user.name"
            class="h-10 w-10 rounded-full bg-gray-200"
          />
        </Link>
      </div>
      
      <div class="flex-grow">
        <div class="flex items-start justify-between">
          <div>
            <Link 
              :href="route('profile.view', comment.user.username)"
              class="font-medium text-gray-900 dark:text-white hover:underline"
            >
              {{ comment.user.name }}
            </Link>
            <p class="mt-1 text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">
              {{ comment.content }}
            </p>
          </div>
          
          <div class="flex items-center space-x-2 text-sm text-gray-500">
            <span>{{ formatDate(comment.created_at) }}</span>
            
            <button 
              v-if="canModerate"
              @click="$emit('delete', comment.id)"
              class="text-red-600 hover:text-red-800"
            >
              Delete
            </button>
          </div>
        </div>
      </div>
    </div>
    
    <div 
      v-if="!comments.length" 
      class="text-center py-12 text-gray-500 dark:text-gray-400"
    >
      No comments yet
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { formatDistanceToNow } from 'date-fns'
import { Link } from '@inertiajs/vue3'
import axios from 'axios'

const props = defineProps({
  comments: {
    type: Array,
    required: true,
    default: () => []
  },
  canModerate: {
    type: Boolean,
    default: false
  },
  imageId: {  // Add imageId prop for API calls
    type: Number,
    required: true
  }
})

const emit = defineEmits(['comment-updated', 'comment-deleted', 'comment-liked', 'delete'])

// Reactive data
const editingCommentId = ref(null)
const editingCommentContent = ref('')
const savingCommentEdit = ref(false)
const likingComments = ref([])
const deletingComments = ref([])

// Add error handling state
const error = ref(null)
const loading = ref(false)

// Add API base URL
const API_BASE = '/api/v1'

const page = usePage()

// Methods
const getUserInitials = (name) => {
  return name
    .split(' ')
    .map(word => word.charAt(0))
    .join('')
    .toUpperCase()
    .slice(0, 2)
}

const formatDate = (date) => {
  return formatDistanceToNow(new Date(date), { addSuffix: true })
}

const canEditComment = (comment) => {
  const user = page.props.auth.user
  return user && user.id === comment.user.id
}

const startEditingComment = (comment) => {
  editingCommentId.value = comment.id
  editingCommentContent.value = comment.content
}

const cancelCommentEdit = () => {
  editingCommentId.value = null
  editingCommentContent.value = ''
}

const saveCommentEdit = async (commentId) => {
  if (savingCommentEdit.value || !editingCommentContent.value.trim()) return
  
  error.value = null
  savingCommentEdit.value = true
  
  try {
    const response = await axios.patch(`${API_BASE}/comments/${commentId}`, {
      content: editingCommentContent.value.trim()
    })
    
    emit('comment-updated', commentId, response.data.data)
    editingCommentId.value = null
    editingCommentContent.value = ''
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to update comment'
    console.error('Error updating comment:', err)
  } finally {
    savingCommentEdit.value = false
  }
}

const deleteComment = async (commentId) => {
  if (deletingComments.value.includes(commentId)) return
  
  if (!confirm('Are you sure you want to delete this comment?')) return
  
  deletingComments.value.push(commentId)
  try {
    await axios.delete(`/api/v1/comments/${commentId}`)
    emit('comment-deleted', commentId)
  } catch (error) {
    console.error('Error deleting comment:', error)
  } finally {
    deletingComments.value = deletingComments.value.filter(id => id !== commentId)
  }
}

const toggleCommentLike = async (commentId) => {
  if (likingComments.value.includes(commentId) || !page.props.auth.user) return
  
  likingComments.value.push(commentId)
  try {
    const response = await axios.post(`/api/v1/comments/${commentId}/like`)
    emit('comment-liked', commentId, response.data.liked, response.data.likes_count)
  } catch (error) {
    console.error('Error toggling comment like:', error)
  } finally {
    likingComments.value = likingComments.value.filter(id => id !== commentId)
  }
}

// Add loading states to template
onMounted(() => {
  loading.value = false
})
</script>

<style scoped>
/* Add any component-specific styles here */
</style>