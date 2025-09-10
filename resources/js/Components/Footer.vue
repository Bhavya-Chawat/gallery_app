<template>
  <footer class="bg-white border-t border-gray-200">
    <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
      <div class="xl:grid xl:grid-cols-3 xl:gap-8">
        <!-- Brand -->
        <div class="space-y-8 xl:col-span-1">
          <div class="text-xl font-bold text-gray-900">
            {{ $page.props.app?.name || 'Gallery' }}
          </div>
          <p class="text-gray-500 text-base">
            A modern, responsive image gallery built with Laravel and Vue.js.
          </p>
          <div class="flex space-x-6">
            <!-- Social links placeholder -->
          </div>
        </div>

        <!-- Links -->
        <div class="mt-12 grid grid-cols-2 gap-8 xl:mt-0 xl:col-span-2">
          <div class="md:grid md:grid-cols-2 md:gap-8">
            <div>
              <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">
                Gallery
              </h3>
              <ul class="mt-4 space-y-4">
                <li>
                  <Link :href="route('gallery.index')" class="text-base text-gray-500 hover:text-gray-900">
                    Browse Images
                  </Link>
                </li>
                <li>
                  <Link :href="route('albums.index')" class="text-base text-gray-500 hover:text-gray-900">
                    Albums
                  </Link>
                </li>
                <li>
                  <Link :href="route('collections.index')" class="text-base text-gray-500 hover:text-gray-900">
                    Collections
                  </Link>
                </li>
                <li>
                  <Link :href="route('tags.index')" class="text-base text-gray-500 hover:text-gray-900">
                    Tags
                  </Link>
                </li>
              </ul>
            </div>
            <div class="mt-12 md:mt-0">
              <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">
                Account
              </h3>
              <ul class="mt-4 space-y-4">
                <li v-if="!$page.props.auth.user">
                  <Link :href="route('login')" class="text-base text-gray-500 hover:text-gray-900">
                    Sign In
                  </Link>
                </li>
                <li v-if="!$page.props.auth.user && $page.props.canRegister">
                  <Link :href="route('register')" class="text-base text-gray-500 hover:text-gray-900">
                    Register
                  </Link>
                </li>
                <li v-if="$page.props.auth.user">
                  <Link :href="route('dashboard')" class="text-base text-gray-500 hover:text-gray-900">
                    Dashboard
                  </Link>
                </li>
                <li v-if="$page.props.auth.user">
                  <Link :href="route('profile.edit')" class="text-base text-gray-500 hover:text-gray-900">
                    Profile
                  </Link>
                </li>
              </ul>
            </div>
          </div>
          <div class="md:grid md:grid-cols-2 md:gap-8">
            <div>
              <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">
                Support
              </h3>
              <ul class="mt-4 space-y-4">
                <li>
                  <a href="#" class="text-base text-gray-500 hover:text-gray-900">
                    Help Center
                  </a>
                </li>
                <li>
                  <a href="#" class="text-base text-gray-500 hover:text-gray-900">
                    Contact Us
                  </a>
                </li>
                <li>
                  <a href="#" class="text-base text-gray-500 hover:text-gray-900">
                    API Documentation
                  </a>
                </li>
              </ul>
            </div>
            <div class="mt-12 md:mt-0">
              <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">
                Legal
              </h3>
              <ul class="mt-4 space-y-4">
                <li>
                  <Link :href="route('privacy')" class="text-base text-gray-500 hover:text-gray-900">
                    Privacy Policy
                  </Link>
                </li>
                <li>
                  <Link :href="route('terms')" class="text-base text-gray-500 hover:text-gray-900">
                    Terms of Service
                  </Link>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>

      <div class="mt-12 border-t border-gray-200 pt-8">
        <p class="text-base text-gray-400 xl:text-center">
          &copy; {{ new Date().getFullYear() }} {{ $page.props.app?.name || 'Gallery' }}. All rights reserved.
        </p>
      </div>
    </div>
  </footer>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import route from 'ziggy-js'
</script>
