<template>
  <div class="bg-gray-50 dark:bg-slate-700/50 rounded-lg p-4">
    <form @submit.prevent="submitComment" class="space-y-4">
      <TextArea
        v-model="form.content"
        placeholder="Write a comment..."
        :error="form.errors.content"
        required
      />

      <div class="flex justify-end">
        <PrimaryButton 
          type="submit" 
          :disabled="form.processing"
        >
          Post Comment
        </PrimaryButton>
      </div>
    </form>

    <!-- Comment Guidelines (expandable) -->
    <div class="mt-3 border-t border-gray-200 dark:border-slate-600 pt-3">
      <button
        @click="showGuidelines = !showGuidelines"
        class="flex items-center text-xs text-gray-500 dark:text-slate-400 hover:text-gray-700 dark:hover:text-slate-300 transition-colors duration-200"
      >
        <svg 
          class="w-4 h-4 mr-1 transform transition-transform duration-200"
          :class="{ 'rotate-90': showGuidelines }"
          fill="none" 
          stroke="currentColor" 
          viewBox="0 0 24 24"
        >
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        Comment Guidelines
      </button>
      
      <div 
        v-if="showGuidelines" 
        class="mt-2 text-xs text-gray-600 dark:text-slate-400 space-y-1"
      >
        <p>• Be respectful and constructive in your comments</p>
        <p>• Avoid spam, harassment, or inappropriate content</p>
        <p>• Stay on topic and add value to the discussion</p>
        <p>• Comments are subject to moderation</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import PrimaryButton from '@/Components/UI/PrimaryButton.vue'
import TextArea from '@/Components/UI/TextArea.vue'

const props = defineProps({
  imageId: {
    type: Number,
    required: true
  }
})

const form = useForm({
  content: ''
})

const emit = defineEmits(['commented'])

const submitComment = () => {
  form.post(route('images.comments.store', props.imageId), {
    preserveScroll: true,
    onSuccess: () => {
      form.reset()
      emit('commented')
    }
  })
}
</script>

<style scoped>
kbd {
  font-family: ui-monospace, SFMono-Regular, "SF Mono", Consolas, "Liberation Mono", Menlo, monospace;
}
</style>