<template>
  <Link
    :href="href"
    :class="[
      'block w-full pl-3 pr-4 py-2 border-l-4 text-left text-base font-medium transition duration-150 ease-in-out focus:outline-none',
      active
        ? 'border-blue-400 text-blue-700 bg-blue-50 focus:text-blue-800 focus:bg-blue-100 focus:border-blue-700'
        : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300'
    ]"
  >
    <slot />
  </Link>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
  href: String,
  active: Boolean,
})
</script>
