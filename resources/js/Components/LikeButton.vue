<template>
  <button
    @click="toggleLike"
    :disabled="loading"
    :class="[
      'inline-flex items-center space-x-1 px-3 py-1 rounded-md text-sm transition-colors',
      liked 
        ? 'bg-red-100 text-red-700 hover:bg-red-200' 
        : 'bg-gray-100 text-gray-700 hover:bg-gray-200',
      loading && 'opacity-50 cursor-not-allowed'
    ]"
  >
    <HeartIcon 
      :class="[
        'h-4 w-4',
        liked ? 'fill-current text-red-500' : ''
      ]" 
    />
    <span>{{ likesCount }}</span>
  </button>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { HeartIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  likeableType: String,
  likeableId: String,
  initialLiked: Boolean,
  initialLikesCount: Number,
})

const liked = ref(props.initialLiked)
const likesCount = ref(props.initialLikesCount)
const loading = ref(false)

const toggleLike = async () => {
  if (loading.value) return
  
  try {
    loading.value = true
    
    const response = await axios.post(route('likes.toggle'), {
      likeable_type: props.likeableType,
      likeable_id: props.likeableId,
    })
    
    liked.value = response.data.liked
    likesCount.value = response.data.likes_count
    
  } catch (error) {
    console.error('Failed to toggle like:', error)
  } finally {
    loading.value = false
  }
}
</script>
