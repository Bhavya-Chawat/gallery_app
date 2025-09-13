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
/*<template>
  <div class="comments-section relative overflow-hidden">
    <!-- Animated Mesh Background -->
    <div class="absolute inset-0 opacity-5 pointer-events-none">
      <svg class="w-full h-full animate-mesh-drift" viewBox="0 0 100 100" preserveAspectRatio="none">
        <defs>
          <linearGradient id="meshGradComments" x1="0%" y1="0%" x2="100%" y2="100%" gradientUnits="objectBoundingBox">
            <stop offset="0%" style="stop-color:#8b5cf6;stop-opacity:0.3">
              <animate attributeName="stop-color" values="#8b5cf6;#06b6d4;#a855f7;#8b5cf6" dur="8s" repeatCount="indefinite"/>
            </stop>
            <stop offset="50%" style="stop-color:#06b6d4;stop-opacity:0.2">
              <animate attributeName="stop-color" values="#06b6d4;#a855f7;#8b5cf6;#06b6d4" dur="8s" repeatCount="indefinite"/>
            </stop>
            <stop offset="100%" style="stop-color:#a855f7;stop-opacity:0.3">
              <animate attributeName="stop-color" values="#a855f7;#8b5cf6;#06b6d4;#a855f7" dur="8s" repeatCount="indefinite"/>
            </stop>
          </linearGradient>
        </defs>
        <rect width="100%" height="100%" fill="url(#meshGradComments)"/>
        <path d="M0,25 Q50,5 100,25 L100,75 Q50,95 0,75 Z" fill="url(#meshGradComments)" opacity="0.2">
          <animateTransform attributeName="transform" type="translate" values="0,0; 5,3; 0,0" dur="10s" repeatCount="indefinite"/>
        </path>
      </svg>
    </div>

    <!-- Floating Particles -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
      <div v-for="i in 8" :key="i" 
           class="absolute w-1 h-1 rounded-full animate-float-gentle"
           :style="{
             left: Math.random() * 100 + '%',
             top: Math.random() * 100 + '%',
             background: ['#8b5cf6', '#06b6d4', '#a855f7'][Math.floor(Math.random() * 3)],
             animationDelay: Math.random() * 8 + 's',
             animationDuration: (6 + Math.random() * 4) + 's'
           }">
      </div>
    </div>

    <div class="relative z-10">
      <!-- Enhanced Header -->
      <div class="flex items-center justify-between mb-8 animate-fade-in-up">
        <h3 class="text-2xl font-bold bg-gradient-to-r from-slate-200 via-white to-slate-200 bg-clip-text text-transparent animate-text-shimmer">
          Comments ({{ comments.length }})
        </h3>
        <button
          v-if="canComment"
          @click="showCommentForm = !showCommentForm"
          class="px-4 py-2 text-sm font-medium bg-gradient-to-r from-violet-500/20 via-purple-500/20 to-cyan-500/20 backdrop-blur-xl border border-violet-400/30 rounded-xl text-slate-200 hover:from-violet-500/30 hover:via-purple-500/30 hover:to-cyan-500/30 hover:border-violet-400/50 hover:text-white transition-all duration-300 hover:scale-105 shadow-lg"
        >
          {{ showCommentForm ? 'Cancel' : 'Add Comment' }}
        </button>
      </div>

      <!-- Enhanced Comment Form -->
      <div v-if="showCommentForm && canComment" class="mb-8 animate-slide-down">
        <div class="p-6 bg-white/5 backdrop-blur-2xl border border-white/10 rounded-2xl shadow-2xl">
          <form @submit.prevent="submitComment" class="space-y-5">
            <div>
              <label for="comment" class="sr-only">Comment</label>
              <textarea
                id="comment"
                v-model="newComment"
                rows="4"
                class="block w-full bg-white/5 backdrop-blur-xl border border-white/20 rounded-xl px-4 py-3 text-slate-200 placeholder-slate-400 focus:border-violet-400/50 focus:ring-4 focus:ring-violet-500/20 focus:outline-none transition-all duration-300 resize-none"
                placeholder="Share your thoughts..."
                required
              ></textarea>
            </div>
            <div class="flex justify-end space-x-3">
              <button
                type="button"
                @click="showCommentForm = false"
                class="px-5 py-3 text-sm font-medium text-slate-400 hover:text-slate-200 bg-white/5 hover:bg-white/10 border border-white/20 hover:border-white/30 rounded-xl transition-all duration-300 hover:scale-105"
              >
                Cancel
              </button>
              <button
                type="submit"
                :disabled="submitting || !newComment.trim()"
                class="px-6 py-3 text-sm font-medium bg-gradient-to-r from-violet-500 via-purple-500 to-cyan-500 text-white rounded-xl hover:from-violet-600 hover:via-purple-600 hover:to-cyan-600 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-300 hover:scale-105 shadow-lg hover:shadow-violet-500/25"
              >
                {{ submitting ? 'Posting...' : 'Post Comment' }}
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Enhanced Comments List -->
      <div class="space-y-6">
        <div
          v-for="(comment, index) in comments"
          :key="comment.id"
          class="flex space-x-4 animate-fade-in-up group"
          :style="{ animationDelay: (index * 0.1) + 's' }"
        >
          <!-- Enhanced Avatar -->
          <div class="flex-shrink-0 relative">
            <div class="h-10 w-10 bg-gradient-to-br from-violet-500 via-purple-500 to-cyan-500 rounded-full flex items-center justify-center shadow-lg border border-white/20 group-hover:scale-110 transition-transform duration-300">
              <span class="text-sm font-bold text-white">
                {{ comment.author?.name?.charAt(0).toUpperCase() || '?' }}
              </span>
            </div>
            <!-- Online indicator -->
            <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-400 rounded-full border-2 border-slate-800 animate-pulse"></div>
          </div>

          <!-- Enhanced Comment Content -->
          <div class="flex-1 min-w-0">
            <div class="p-5 bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl shadow-lg group-hover:bg-white/10 group-hover:border-violet-400/20 group-hover:shadow-xl transition-all duration-300">
              <!-- Comment Header -->
              <div class="text-sm mb-3">
                <span class="font-semibold bg-gradient-to-r from-violet-300 to-cyan-300 bg-clip-text text-transparent">
                  {{ comment.author?.name || 'Anonymous' }}
                </span>
                <span class="text-slate-400 ml-2">
                  • {{ formatDate(comment.created_at) }}
                </span>
              </div>
              
              <!-- Comment Text -->
              <div class="text-slate-300 leading-relaxed">
                {{ comment.content }}
              </div>

              <!-- Enhanced Comment Actions -->
              <div class="mt-4 flex items-center space-x-6">
                <button
                  @click="toggleLike(comment)"
                  :class="[
                    'flex items-center space-x-2 text-sm font-medium transition-all duration-300 hover:scale-105',
                    comment.is_liked 
                      ? 'text-red-400 hover:text-red-300' 
                      : 'text-slate-400 hover:text-red-400'
                  ]"
                >
                  <div class="p-1 rounded-lg bg-white/10 hover:bg-red-500/20 transition-colors">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                    </svg>
                  </div>
                  <span>{{ comment.is_liked ? 'Unlike' : 'Like' }}</span>
                  <span v-if="comment.likes_count > 0" class="px-2 py-1 bg-white/10 rounded-full text-xs">
                    {{ comment.likes_count }}
                  </span>
                </button>
                
                <button
                  v-if="canReply"
                  @click="toggleReply(comment.id)"
                  class="flex items-center space-x-2 text-sm font-medium text-slate-400 hover:text-cyan-400 transition-all duration-300 hover:scale-105"
                >
                  <div class="p-1 rounded-lg bg-white/10 hover:bg-cyan-500/20 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                    </svg>
                  </div>
                  <span>Reply</span>
                </button>

                <button
                  v-if="canDelete(comment)"
                  @click="deleteComment(comment)"
                  class="flex items-center space-x-2 text-sm font-medium text-slate-400 hover:text-red-400 transition-all duration-300 hover:scale-105"
                >
                  <div class="p-1 rounded-lg bg-white/10 hover:bg-red-500/20 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                  </div>
                  <span>Delete</span>
                </button>
              </div>
            </div>

            <!-- Enhanced Reply Form -->
            <div v-if="replyingTo === comment.id" class="mt-4 ml-6 animate-slide-down">
              <div class="p-4 bg-white/5 backdrop-blur-xl border border-white/10 rounded-xl shadow-lg">
                <form @submit.prevent="submitReply(comment.id)" class="space-y-3">
                  <textarea
                    v-model="newReply"
                    rows="3"
                    class="block w-full text-sm bg-white/5 backdrop-blur-xl border border-white/20 rounded-lg px-3 py-2 text-slate-200 placeholder-slate-400 focus:border-cyan-400/50 focus:ring-2 focus:ring-cyan-500/20 focus:outline-none transition-all duration-300 resize-none"
                    placeholder="Write a thoughtful reply..."
                    required
                  ></textarea>
                  <div class="flex justify-end space-x-2">
                    <button
                      type="button"
                      @click="cancelReply"
                      class="px-3 py-2 text-xs font-medium text-slate-400 hover:text-slate-200 bg-white/5 hover:bg-white/10 border border-white/20 rounded-lg transition-all duration-300"
                    >
                      Cancel
                    </button>
                    <button
                      type="submit"
                      :disabled="submitting || !newReply.trim()"
                      class="px-4 py-2 text-xs font-medium bg-gradient-to-r from-cyan-500 to-blue-500 text-white rounded-lg hover:from-cyan-600 hover:to-blue-600 disabled:opacity-50 transition-all duration-300 shadow-md"
                    >
                      Reply
                    </button>
                  </div>
                </form>
              </div>
            </div>

            <!-- Enhanced Replies -->
            <div v-if="comment.replies && comment.replies.length" class="mt-6 ml-6 space-y-4">
              <div
                v-for="reply in comment.replies"
                :key="reply.id"
                class="flex space-x-3 animate-fade-in-up"
              >
                <div class="flex-shrink-0">
                  <div class="h-8 w-8 bg-gradient-to-br from-cyan-500 via-blue-500 to-violet-500 rounded-full flex items-center justify-center shadow-md border border-white/20">
                    <span class="text-xs font-bold text-white">
                      {{ reply.author?.name?.charAt(0).toUpperCase() || '?' }}
                    </span>
                  </div>
                </div>
                <div class="flex-1 min-w-0">
                  <div class="p-4 bg-white/5 backdrop-blur-xl border border-white/10 rounded-xl shadow-md">
                    <div class="text-xs mb-2">
                      <span class="font-medium bg-gradient-to-r from-cyan-300 to-blue-300 bg-clip-text text-transparent">
                        {{ reply.author?.name || 'Anonymous' }}
                      </span>
                      <span class="text-slate-400 ml-2">
                        • {{ formatDate(reply.created_at) }}
                      </span>
                    </div>
                    <div class="text-sm text-slate-300 leading-relaxed">
                      {{ reply.content }}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Enhanced Empty State -->
      <div v-if="!comments.length" class="text-center py-16 animate-fade-in-up">
        <div class="mx-auto w-24 h-24 bg-gradient-to-br from-violet-500/10 via-purple-500/10 to-cyan-500/10 backdrop-blur-xl rounded-3xl border border-white/10 flex items-center justify-center mb-6 shadow-2xl">
          <ChatBubbleLeftIcon class="h-12 w-12 text-slate-400" />
        </div>
        <h3 class="text-xl font-semibold bg-gradient-to-r from-slate-300 to-slate-400 bg-clip-text text-transparent mb-2">
          No comments yet
        </h3>
        <p class="text-slate-400 max-w-md mx-auto leading-relaxed">
          {{ canComment ? 'Start the conversation and share your thoughts with the community.' : 'Comments are currently disabled for this content.' }}
        </p>
        <div v-if="canComment" class="mt-6">
          <button
            @click="showCommentForm = true"
            class="px-6 py-3 bg-gradient-to-r from-violet-500 via-purple-500 to-cyan-500 text-white rounded-xl hover:from-violet-600 hover:via-purple-600 hover:to-cyan-600 transition-all duration-300 hover:scale-105 shadow-lg font-medium"
          >
            Be the first to comment
          </button>
        </div>
      </div>
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

<style scoped>
@keyframes mesh-drift {
  0%, 100% { transform: translate(0, 0) rotate(0deg); }
  33% { transform: translate(15px, -15px) rotate(1deg); }
  66% { transform: translate(-10px, 10px) rotate(-1deg); }
}

@keyframes float-gentle {
  0%, 100% { 
    transform: translateY(0px) rotate(0deg);
    opacity: 0.3;
  }
  50% { 
    transform: translateY(-15px) rotate(180deg);
    opacity: 0.6;
  }
}

@keyframes text-shimmer {
  0% { background-position: -200% center; }
  100% { background-position: 200% center; }
}

@keyframes fade-in-up {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes slide-down {
  from {
    opacity: 0;
    transform: translateY(-10px) scale(0.95);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

.animate-mesh-drift {
  animation: mesh-drift 12s ease-in-out infinite;
}

.animate-float-gentle {
  animation: float-gentle 6s ease-in-out infinite;
}

.animate-text-shimmer {
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
  background-size: 200% 100%;
  animation: text-shimmer 3s ease-in-out infinite;
}

.animate-fade-in-up {
  animation: fade-in-up 0.6s ease-out forwards;
  opacity: 0;
}

.animate-slide-down {
  animation: slide-down 0.4s ease-out;
}
</style>