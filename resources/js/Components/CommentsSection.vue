<template>
  <div class="comments-section">
    <div class="flex items-center justify-between mb-6">
      <h3 class="text-lg font-medium text-gray-900">
        Comments ({{ comments.length }})
      </h3>
      <button
        v-if="canComment"
        @click="showCommentForm = !showCommentForm"
        class="text-sm text-blue-600 hover:text-blue-500"
      >
        {{ showCommentForm ? 'Cancel' : 'Add Comment' }}
      </button>
    </div>

    <!-- Comment Form -->
    <div v-if="showCommentForm && canComment" class="mb-6">
      <form @submit.prevent="submitComment" class="space-y-4">
        <div>
          <label for="comment" class="sr-only">Comment</label>
          <textarea
            id="comment"
            v-model="newComment"
            rows="3"
            class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
            placeholder="Write your comment..."
            required
          ></textarea>
        </div>
        <div class="flex justify-end space-x-3">
          <button
            type="button"
            @click="showCommentForm = false"
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200"
          >
            Cancel
          </button>
          <button
            type="submit"
            :disabled="submitting || !newComment.trim()"
            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 disabled:opacity-50"
          >
            {{ submitting ? 'Posting...' : 'Post Comment' }}
          </button>
        </div>
      </form>
    </div>

    <!-- Comments List -->
    <div class="space-y-4">
      <div
        v-for="comment in comments"
        :key="comment.id"
        class="flex space-x-3"
      >
        <!-- Avatar -->
        <div class="flex-shrink-0">
          <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
            <span class="text-sm font-medium text-gray-600">
              {{ comment.author?.name?.charAt(0).toUpperCase() || '?' }}
            </span>
          </div>
        </div>

        <!-- Comment Content -->
        <div class="flex-1 min-w-0">
          <div class="text-sm">
            <span class="font-medium text-gray-900">
              {{ comment.author?.name || 'Anonymous' }}
            </span>
            <span class="text-gray-500">
              • {{ formatDate(comment.created_at) }}
            </span>
          </div>
          <div class="mt-1 text-sm text-gray-700">
            {{ comment.content }}
          </div>

          <!-- Comment Actions -->
          <div class="mt-2 flex items-center space-x-4">
            <button
              @click="toggleLike(comment)"
              :class="[
                'text-xs font-medium',
                comment.is_liked ? 'text-red-600' : 'text-gray-500 hover:text-gray-700'
              ]"
            >
              {{ comment.is_liked ? 'Unlike' : 'Like' }}
              {{ comment.likes_count > 0 ? `(${comment.likes_count})` : '' }}
            </button>
            
            <button
              v-if="canReply"
              @click="toggleReply(comment.id)"
              class="text-xs font-medium text-gray-500 hover:text-gray-700"
            >
              Reply
            </button>

            <button
              v-if="canDelete(comment)"
              @click="deleteComment(comment)"
              class="text-xs font-medium text-red-600 hover:text-red-500"
            >
              Delete
            </button>
          </div>

          <!-- Reply Form -->
          <div v-if="replyingTo === comment.id" class="mt-3">
            <form @submit.prevent="submitReply(comment.id)" class="space-y-2">
              <textarea
                v-model="newReply"
                rows="2"
                class="block w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                placeholder="Write a reply..."
                required
              ></textarea>
              <div class="flex justify-end space-x-2">
                <button
                  type="button"
                  @click="cancelReply"
                  class="px-3 py-1 text-xs font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200"
                >
                  Cancel
                </button>
                <button
                  type="submit"
                  :disabled="submitting || !newReply.trim()"
                  class="px-3 py-1 text-xs font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 disabled:opacity-50"
                >
                  Reply
                </button>
              </div>
            </form>
          </div>

          <!-- Replies -->
          <div v-if="comment.replies && comment.replies.length" class="mt-4 space-y-3">
            <div
              v-for="reply in comment.replies"
              :key="reply.id"
              class="flex space-x-3"
            >
              <div class="flex-shrink-0">
                <div class="h-6 w-6 rounded-full bg-gray-300 flex items-center justify-center">
                  <span class="text-xs font-medium text-gray-600">
                    {{ reply.author?.name?.charAt(0).toUpperCase() || '?' }}
                  </span>
                </div>
              </div>
              <div class="flex-1 min-w-0">
                <div class="text-xs">
                  <span class="font-medium text-gray-900">
                    {{ reply.author?.name || 'Anonymous' }}
                  </span>
                  <span class="text-gray-500">
                    • {{ formatDate(reply.created_at) }}
                  </span>
                </div>
                <div class="mt-1 text-xs text-gray-700">
                  {{ reply.content }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-if="!comments.length" class="text-center py-6">
      <ChatBubbleLeftIcon class="mx-auto h-12 w-12 text-gray-400" />
      <h3 class="mt-2 text-sm font-medium text-gray-900">No comments yet</h3>
      <p class="mt-1 text-sm text-gray-500">
        {{ canComment ? 'Be the first to leave a comment.' : 'Comments are disabled.' }}
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { ChatBubbleLeftIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  comments: {
    type: Array,
    default: () => []
  },
  canComment: {
    type: Boolean,
    default: false
  },
  canReply: {
    type: Boolean,
    default: false
  },
  resourceType: {
    type: String,
    required: true // 'image', 'album', etc.
  },
  resourceId: {
    type: [String, Number],
    required: true
  }
})

const showCommentForm = ref(false)
const newComment = ref('')
const newReply = ref('')
const replyingTo = ref(null)
const submitting = ref(false)

const submitComment = async () => {
  if (!newComment.value.trim()) return
  
  submitting.value = true
  try {
    await router.post(route('comments.store'), {
      commentable_type: props.resourceType,
      commentable_id: props.resourceId,
      content: newComment.value
    })
    newComment.value = ''
    showCommentForm.value = false
  } finally {
    submitting.value = false
  }
}

const submitReply = async (parentId) => {
  if (!newReply.value.trim()) return
  
  submitting.value = true
  try {
    await router.post(route('comments.store'), {
      commentable_type: props.resourceType,
      commentable_id: props.resourceId,
      content: newReply.value,
      parent_id: parentId
    })
    newReply.value = ''
    replyingTo.value = null
  } finally {
    submitting.value = false
  }
}

const toggleReply = (commentId) => {
  replyingTo.value = replyingTo.value === commentId ? null : commentId
  newReply.value = ''
}

const cancelReply = () => {
  replyingTo.value = null
  newReply.value = ''
}

const toggleLike = async (comment) => {
  try {
    await router.post(route('comments.toggle-like', comment.id))
  } catch (error) {
    console.error('Failed to toggle like:', error)
  }
}

const deleteComment = async (comment) => {
  if (confirm('Are you sure you want to delete this comment?')) {
    try {
      await router.delete(route('comments.destroy', comment.id))
    } catch (error) {
      console.error('Failed to delete comment:', error)
    }
  }
}

const canDelete = (comment) => {
  // This would typically check if the current user owns the comment or is an admin
  return comment.can_delete || false
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString()
}
</script>
