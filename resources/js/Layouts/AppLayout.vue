<template>
  <div class="min-h-screen bg-gray-50 dark:bg-slate-900">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm dark:bg-gray-800">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex">
            <!-- Always show home -->
            <Link
              :href="route('home')"
              class="inline-flex items-center px-1 pt-1 border-b-2"
              :class="[
                route().current('home')
                  ? 'border-indigo-400 text-gray-900'
                  : 'border-transparent text-gray-500'
              ]"
            >
              Home
            </Link>

            <!-- Show gallery link only if authenticated -->
            <Link
              v-if="auth.user && hasRoute('gallery.index')"
              :href="route('gallery.index')"
              class="inline-flex items-center px-1 pt-1 border-b-2 ml-8"
              :class="[
                route().current('gallery.*')
                  ? 'border-indigo-400 text-gray-900'
                  : 'border-transparent text-gray-500'
              ]"
            >
              Gallery
            </Link>
          </div>
        </div>
      </div>
    </nav>

    <!-- Page Content -->
    <main>
      <slot />
    </main>
  </div>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3'

const page = usePage()
const auth = computed(() => page.props.auth)

const hasRoute = (name) => {
    try {
        route(name)
        return true
    } catch {
        return false
    }
}
</script>