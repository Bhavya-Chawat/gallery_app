<template>
  <component
    :is="component"
    :href="href"
    :method="method"
    :as="as"
    class="block w-full px-4 py-2 text-left text-sm leading-5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700/50 focus:outline-none focus:bg-gray-100 dark:focus:bg-slate-700/50 transition duration-150 ease-in-out"
    v-bind="$attrs"
  >
    <slot />
  </component>
</template>

<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
  href: {
    type: String,
    default: null
  },
  method: {
    type: String,
    default: 'get'
  },
  as: {
    type: String,
    default: null
  }
})

const component = computed(() => {
  if (props.as) {
    return props.as
  }
  
  if (props.href) {
    return Link
  }
  
  return 'button'
})
</script>
