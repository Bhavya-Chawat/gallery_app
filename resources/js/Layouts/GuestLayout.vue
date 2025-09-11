<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Simple navigation -->
    <nav class="bg-white shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex items-center">
            <Link :href="route('welcome')" class="text-xl font-bold text-gray-900">
              {{ $page.props.app?.name || 'Gallery' }}
            </Link>
          </div>

          <div class="flex items-center space-x-4">
            <Link :href="route('gallery.index')" class="text-gray-600 hover:text-gray-900">
              Browse Gallery
            </Link>
            <template v-if="$page.props.auth?.user">
              <Link :href="route('dashboard')" class="text-gray-600 hover:text-gray-900">
                Dashboard
              </Link>
              <Link v-if="$page.props.auth.user.roles?.some(r => r.slug === 'admin')" :href="route('admin.system.index')" class="text-gray-600 hover:text-gray-900">
                Admin
              </Link>
            </template>
            <template v-else>
              <Link
                v-if="$page.props.can?.login"
                :href="route('login')"
                class="text-sm text-gray-700 underline"
              >
                Log in
              </Link>
              <Link
                v-if="$page.props.can?.register"
                :href="route('register')"
                class="px-4 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700"
              >
                Register
              </Link>
            </template>
          </div>
        </div>
      </div>
    </nav>

    <!-- Content -->
    <main>
      <slot />
    </main>

    <!-- Footer -->
    <Footer />
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import route from 'ziggy-js'
import Footer from '@/Components/Footer.vue'
</script>
