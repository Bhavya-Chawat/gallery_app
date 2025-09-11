<template>
  <transition
    enter-active-class="transition ease-out duration-200"
    enter-from-class="opacity-0 transform -translate-y-1"
    enter-to-class="opacity-100 transform translate-y-0"
    leave-active-class="transition ease-in duration-150"
    leave-from-class="opacity-100 transform translate-y-0"
    leave-to-class="opacity-0 transform -translate-y-1"
  >
    <div v-show="message" class="flex items-start space-x-2 mt-2">
      <ExclamationCircleIcon class="h-4 w-4 text-red-500 mt-0.5 flex-shrink-0" />
      <p class="text-sm text-red-600 dark:text-red-400 leading-5">
        {{ message }}
      </p>
    </div>
  </transition>
</template>

<script setup>
import { ExclamationCircleIcon } from '@heroicons/vue/24/outline'

defineProps({
  message: {
    type: String,
    default: ''
  }
})
</script>