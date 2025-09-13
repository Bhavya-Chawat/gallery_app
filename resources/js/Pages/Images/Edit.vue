<template>
  <AppLayout>
    <Head :title="`Edit ${image.title || 'Image'}`" />

    <!-- Animated Background -->
    <div class="fixed inset-0 -z-10 overflow-hidden">
      <!-- Animated Mesh Background -->
      <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900">
        <svg class="absolute inset-0 w-full h-full opacity-20" viewBox="0 0 100 100" preserveAspectRatio="none">
          <defs>
            <pattern id="mesh" x="0" y="0" width="10" height="10" patternUnits="userSpaceOnUse">
              <circle cx="5" cy="5" r="1" fill="currentColor" class="text-violet-500/30">
                <animate attributeName="r" values="1;2;1" dur="4s" repeatCount="indefinite"/>
              </circle>
            </pattern>
            <linearGradient id="meshGradient" x1="0%" y1="0%" x2="100%" y2="100%">
              <stop offset="0%" style="stop-color:#8b5cf6">
                <animate attributeName="stop-color" values="#8b5cf6;#06b6d4;#8b5cf6" dur="8s" repeatCount="indefinite"/>
              </stop>
              <stop offset="100%" style="stop-color:#06b6d4">
                <animate attributeName="stop-color" values="#06b6d4;#a855f7;#06b6d4" dur="8s" repeatCount="indefinite"/>
              </stop>
            </linearGradient>
          </defs>
          <rect width="100" height="100" fill="url(#mesh)"/>
          <rect width="100" height="100" fill="url(#meshGradient)" opacity="0.1"/>
          <animateTransform
            attributeName="transform"
            type="translate"
            values="0 0;10 5;0 0"
            dur="12s"
            repeatCount="indefinite"
          />
        </svg>
      </div>

      <!-- Floating Particles -->
      <div class="absolute inset-0">
        <div v-for="i in 20" :key="`particle-${i}`" 
          class="absolute w-2 h-2 rounded-full opacity-60"
          :class="[
            i % 3 === 0 ? 'bg-violet-500' : i % 3 === 1 ? 'bg-cyan-500' : 'bg-purple-500'
          ]"
          :style="{
            left: Math.random() * 100 + '%',
            top: Math.random() * 100 + '%',
            animationDelay: Math.random() * 10 + 's',
            animationDuration: (8 + Math.random() * 4) + 's'
          }"
        >
          <div class="w-full h-full rounded-full animate-ping"></div>
        </div>
      </div>

      <!-- Gradient Overlay -->
      <div class="absolute inset-0 bg-gradient-to-br from-violet-900/20 via-transparent to-cyan-900/20"></div>
    </div>

    <template #header>
      <div class="flex items-center justify-between animate-fade-in-up">
        <div>
          <nav class="flex" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-4">
              <li>
                <Link :href="route('gallery.index')" 
                  class="text-slate-300 hover:text-white transition-all duration-300 hover:scale-105 inline-flex items-center gap-1">
                  <span class="bg-gradient-to-r from-violet-400 to-cyan-400 bg-clip-text text-transparent font-medium">Gallery</span>
                </Link>
              </li>
              <li class="flex">
                <ChevronRightIcon class="flex-shrink-0 h-5 w-5 text-slate-400 animate-pulse" />
                <Link :href="route('images.show', image.slug)" 
                  class="text-slate-300 hover:text-white transition-all duration-300 hover:scale-105">
                  {{ image.title || 'Untitled' }}
                </Link>
              </li>
              <li class="flex">
                <ChevronRightIcon class="flex-shrink-0 h-5 w-5 text-slate-400 animate-pulse" />
                <span class="ml-4 text-sm font-medium text-white bg-gradient-to-r from-violet-400 to-cyan-400 bg-clip-text text-transparent">Edit</span>
              </li>
            </ol>
          </nav>
          <h2 class="mt-2 font-bold text-2xl text-white leading-tight">
            <span class="bg-gradient-to-r from-violet-400 via-purple-400 to-cyan-400 bg-clip-text text-transparent animate-shimmer bg-300% bg-pos-0">
              Edit Image
            </span>
          </h2>
        </div>
      </div>
    </template>

    <div class="py-12 min-h-screen">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <form @submit.prevent="submit" class="space-y-8">
          <!-- Image Preview Card -->
          <div class="group animate-fade-in-up" style="animation-delay: 0.1s">
            <div class="bg-white/5 backdrop-blur-xl border border-white/10 shadow-2xl rounded-3xl p-8 
                        hover:bg-white/10 hover:border-white/20 transition-all duration-500 
                        hover:shadow-violet-500/10 hover:shadow-2xl transform hover:scale-[1.02]">
              <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-r from-violet-500 to-cyan-500 flex items-center justify-center">
                  <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                  </svg>
                </div>
                <h3 class="text-xl font-bold text-white">Preview</h3>
              </div>
              <div class="max-w-md">
                <div class="relative group overflow-hidden rounded-2xl shadow-2xl">
                  <img
                    :src="getImageUrl('medium')"
                    :alt="form.alt_text || form.title"
                    class="w-full transition-all duration-700 group-hover:scale-110 group-hover:brightness-110"
                  >
                  <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent 
                              opacity-0 group-hover:opacity-100 transition-all duration-500"></div>
                  <div class="absolute inset-0 ring-1 ring-white/20 rounded-2xl group-hover:ring-violet-400/50 
                              transition-all duration-500"></div>
                </div>
              </div>
            </div>
          </div>

          <!-- Basic Information Card -->
          <div class="animate-fade-in-up" style="animation-delay: 0.2s">
            <div class="bg-white/5 backdrop-blur-xl border border-white/10 shadow-2xl rounded-3xl p-8 
                        hover:bg-white/10 hover:border-white/20 transition-all duration-500 
                        hover:shadow-cyan-500/10 hover:shadow-2xl space-y-8">
              <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-r from-cyan-500 to-purple-500 flex items-center justify-center">
                  <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                </div>
                <h3 class="text-xl font-bold text-white">Basic Information</h3>
              </div>
              
              <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Title -->
                <div class="group">
                  <InputLabel for="title" value="Title" class="text-slate-300 font-medium mb-3 group-focus-within:text-violet-400 transition-colors duration-300" />
                  <TextInput
                    id="title"
                    v-model="form.title"
                    type="text"
                    class="mt-1 block w-full bg-white/5 border-white/10 text-white placeholder-slate-400 
                           rounded-xl focus:border-violet-500 focus:ring-violet-500/20 focus:bg-white/10 
                           transition-all duration-300 hover:bg-white/10"
                    placeholder="Enter image title"
                  />
                  <InputError class="mt-2 text-red-400" :message="form.errors.title" />
                </div>

                <!-- Album -->
                <div class="group">
                  <InputLabel for="album_id" value="Album" class="text-slate-300 font-medium mb-3 group-focus-within:text-cyan-400 transition-colors duration-300" />
                  <select
                    id="album_id"
                    v-model="form.album_id"
                    class="mt-1 block w-full bg-white/5 border-white/10 text-white rounded-xl 
                           focus:border-cyan-500 focus:ring-cyan-500/20 focus:bg-white/10 
                           transition-all duration-300 hover:bg-white/10"
                  >
                    <option value="" class="bg-slate-800">No album</option>
                    <option
                      v-for="album in albums"
                      :key="album.id"
                      :value="album.id"
                      class="bg-slate-800"
                    >
                      {{ album.title }}
                    </option>
                  </select>
                  <InputError class="mt-2 text-red-400" :message="form.errors.album_id" />
                </div>
              </div>

              <!-- Caption -->
              <div class="group">
                <InputLabel for="caption" value="Caption" class="text-slate-300 font-medium mb-3 group-focus-within:text-purple-400 transition-colors duration-300" />
                <textarea
                  id="caption"
                  v-model="form.caption"
                  rows="4"
                  class="mt-1 block w-full bg-white/5 border-white/10 text-white placeholder-slate-400 
                         rounded-xl focus:border-purple-500 focus:ring-purple-500/20 focus:bg-white/10 
                         transition-all duration-300 hover:bg-white/10 resize-none"
                  placeholder="Add a caption to describe your image"
                ></textarea>
                <InputError class="mt-2 text-red-400" :message="form.errors.caption" />
              </div>

              <!-- Alt Text -->
              <div class="group">
                <InputLabel for="alt_text" value="Alt Text" class="text-slate-300 font-medium mb-3 group-focus-within:text-violet-400 transition-colors duration-300" />
                <TextInput
                  id="alt_text"
                  v-model="form.alt_text"
                  type="text"
                  class="mt-1 block w-full bg-white/5 border-white/10 text-white placeholder-slate-400 
                         rounded-xl focus:border-violet-500 focus:ring-violet-500/20 focus:bg-white/10 
                         transition-all duration-300 hover:bg-white/10"
                  placeholder="Describe the image for accessibility"
                />
                <p class="mt-2 text-sm text-slate-400">
                  Alternative text for screen readers and when the image fails to load.
                </p>
                <InputError class="mt-2 text-red-400" :message="form.errors.alt_text" />
              </div>
            </div>
          </div>

          <!-- Tags Card -->
          <div class="animate-fade-in-up" style="animation-delay: 0.3s">
            <div class="bg-white/5 backdrop-blur-xl border border-white/10 shadow-2xl rounded-3xl p-8 
                        hover:bg-white/10 hover:border-white/20 transition-all duration-500 
                        hover:shadow-purple-500/10 hover:shadow-2xl">
              <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-r from-purple-500 to-violet-500 flex items-center justify-center">
                  <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                  </svg>
                </div>
                <h3 class="text-xl font-bold text-white">Tags</h3>
              </div>
              
              <!-- Tag Input -->
              <div class="space-y-6">
                <div class="group">
                  <div class="flex items-center space-x-3">
                    <input
                      v-model="newTag"
                      @keydown.enter.prevent="addTag"
                      @keydown.comma.prevent="addTag"
                      type="text"
                      placeholder="Type a tag and press Enter or comma"
                      class="flex-1 bg-white/5 border-white/10 text-white placeholder-slate-400 
                             rounded-xl focus:border-purple-500 focus:ring-purple-500/20 focus:bg-white/10 
                             transition-all duration-300 hover:bg-white/10"
                    >
                    <button
                      type="button"
                      @click="addTag"
                      class="px-6 py-3 bg-gradient-to-r from-purple-600 to-violet-600 text-white font-medium 
                             rounded-xl hover:from-purple-700 hover:to-violet-700 focus:outline-none 
                             focus:ring-4 focus:ring-purple-500/20 transition-all duration-300 
                             transform hover:scale-105 shadow-lg hover:shadow-purple-500/25"
                    >
                      Add Tag
                    </button>
                  </div>
                  <p class="mt-2 text-sm text-slate-400">
                    Press Enter or comma to add tags. Use lowercase, no spaces.
                  </p>
                </div>

                <!-- Current Tags -->
                <div v-if="form.tags.length > 0" class="animate-fade-in-up">
                  <label class="block text-sm font-medium text-slate-300 mb-3">Current Tags:</label>
                  <div class="flex flex-wrap gap-3">
                    <span
                      v-for="(tag, index) in form.tags"
                      :key="index"
                      class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium 
                             bg-gradient-to-r from-violet-500/20 to-purple-500/20 text-violet-300 
                             border border-violet-400/30 backdrop-blur-sm hover:from-violet-500/30 
                             hover:to-purple-500/30 transition-all duration-300 transform hover:scale-105"
                    >
                      {{ tag }}
                      <button
                        type="button"
                        @click="removeTag(index)"
                        class="ml-2 text-violet-300 hover:text-white focus:outline-none 
                               transition-colors duration-200 hover:bg-red-500/20 rounded-full 
                               w-5 h-5 flex items-center justify-center"
                      >
                        ×
                      </button>
                    </span>
                  </div>
                </div>

                <!-- Popular Tags -->
                <div v-if="availableTags.length > 0" class="animate-fade-in-up" style="animation-delay: 0.1s">
                  <label class="block text-sm font-medium text-slate-300 mb-3">Popular Tags:</label>
                  <div class="flex flex-wrap gap-3">
                    <button
                      v-for="tag in availableTags.slice(0, 10)"
                      :key="tag.name"
                      type="button"
                      @click="addExistingTag(tag.name)"
                      :disabled="form.tags.includes(tag.name)"
                      class="px-4 py-2 text-sm font-medium border border-white/20 rounded-full 
                             hover:bg-white/10 disabled:opacity-50 disabled:cursor-not-allowed 
                             transition-all duration-300 transform hover:scale-105 text-slate-300 
                             hover:text-white hover:border-cyan-400/50"
                      :class="{ 
                        'bg-cyan-500/20 border-cyan-400/50 text-cyan-300': form.tags.includes(tag.name),
                        'hover:shadow-lg hover:shadow-cyan-500/20': !form.tags.includes(tag.name)
                      }"
                    >
                      {{ tag.name }} ({{ tag.count }})
                    </button>
                  </div>
                </div>
              </div>
              
              <InputError class="mt-2 text-red-400" :message="form.errors.tags" />
            </div>
          </div>

          <!-- Privacy and License Card -->
          <div class="animate-fade-in-up" style="animation-delay: 0.4s">
            <div class="bg-white/5 backdrop-blur-xl border border-white/10 shadow-2xl rounded-3xl p-8 
                        hover:bg-white/10 hover:border-white/20 transition-all duration-500 
                        hover:shadow-cyan-500/10 hover:shadow-2xl space-y-8">
              <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-500 flex items-center justify-center">
                  <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                  </svg>
                </div>
                <h3 class="text-xl font-bold text-white">Privacy &amp; License</h3>
              </div>
              
              <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Privacy -->
                <div class="group">
                  <InputLabel for="privacy" value="Privacy" class="text-slate-300 font-medium mb-3 group-focus-within:text-cyan-400 transition-colors duration-300" />
                  <select
                    id="privacy"
                    v-model="form.privacy"
                    class="mt-1 block w-full bg-white/5 border-white/10 text-white rounded-xl 
                           focus:border-cyan-500 focus:ring-cyan-500/20 focus:bg-white/10 
                           transition-all duration-300 hover:bg-white/10"
                  >
                    <option value="public" class="bg-slate-800">Public - Anyone can view</option>
                    <option value="unlisted" class="bg-slate-800">Unlisted - Only accessible via direct link</option>
                    <option value="private" class="bg-slate-800">Private - Only you can view</option>
                  </select>
                  <InputError class="mt-2 text-red-400" :message="form.errors.privacy" />
                </div>

                <!-- License -->
                <div class="group">
                  <InputLabel for="license" value="License" class="text-slate-300 font-medium mb-3 group-focus-within:text-blue-400 transition-colors duration-300" />
                  <TextInput
                    id="license"
                    v-model="form.license"
                    type="text"
                    class="mt-1 block w-full bg-white/5 border-white/10 text-white placeholder-slate-400 
                           rounded-xl focus:border-blue-500 focus:ring-blue-500/20 focus:bg-white/10 
                           transition-all duration-300 hover:bg-white/10"
                    placeholder="e.g., CC BY 4.0, All Rights Reserved"
                  />
                  <InputError class="mt-2 text-red-400" :message="form.errors.license" />
                </div>
              </div>
            </div>
          </div>

          <!-- Settings Card -->
          <div class="animate-fade-in-up" style="animation-delay: 0.5s">
            <div class="bg-white/5 backdrop-blur-xl border border-white/10 shadow-2xl rounded-3xl p-8 
                        hover:bg-white/10 hover:border-white/20 transition-all duration-500 
                        hover:shadow-violet-500/10 hover:shadow-2xl">
              <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-r from-violet-500 to-purple-500 flex items-center justify-center">
                  <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                  </svg>
                </div>
                <h3 class="text-xl font-bold text-white">Settings</h3>
              </div>
              
              <div class="space-y-6">
                <label class="flex items-start group cursor-pointer">
                  <div class="flex items-center h-5">
                    <input
                      v-model="form.allow_comments"
                      type="checkbox"
                      class="w-5 h-5 rounded border-white/20 bg-white/5 text-violet-600 
                             shadow-sm focus:border-violet-300 focus:ring focus:ring-violet-200 
                             focus:ring-opacity-50 transition-all duration-300"
                    >
                  </div>
                  <div class="ml-4 text-sm">
                    <span class="font-medium text-slate-300 group-hover:text-white transition-colors duration-300">Allow comments</span>
                    <p class="text-slate-400 group-hover:text-slate-300 transition-colors duration-300">Let people comment on this image.</p>
                  </div>
                </label>

                <label class="flex items-start group cursor-pointer">
                  <div class="flex items-center h-5">
                    <input
                      v-model="form.allow_downloads"
                      type="checkbox"
                      class="w-5 h-5 rounded border-white/20 bg-white/5 text-cyan-600 
                             shadow-sm focus:border-cyan-300 focus:ring focus:ring-cyan-200 
                             focus:ring-opacity-50 transition-all duration-300"
                    >
                  </div>
                  <div class="ml-4 text-sm">
                    <span class="font-medium text-slate-300 group-hover:text-white transition-colors duration-300">Allow downloads</span>
                    <p class="text-slate-400 group-hover:text-slate-300 transition-colors duration-300">Let people download this image (respecting license terms).</p>
                  </div>
                </label>
              </div>
            </div>
          </div>

          <!-- Actions Card -->
          <div class="animate-fade-in-up" style="animation-delay: 0.6s">
            <div class="bg-white/5 backdrop-blur-xl border border-white/10 shadow-2xl rounded-3xl p-8 
                        hover:bg-white/10 hover:border-white/20 transition-all duration-500 
                        hover:shadow-emerald-500/10 hover:shadow-2xl">
              <div class="flex items-center justify-between">
                <div class="flex items-center space-x-6">
                  <Link
                    :href="route('images.show', image.slug)"
                    class="text-sm text-slate-400 hover:text-white transition-all duration-300 
                           hover:scale-105 font-medium"
                  >
                    Cancel
                  </Link>
                  
                  <button
                    type="button"
                    @click="deleteImage"
                    class="text-sm text-red-400 hover:text-red-300 transition-all duration-300 
                           hover:scale-105 font-medium hover:bg-red-500/10 px-3 py-1 rounded-lg"
                  >
                    Delete Image
                  </button>
                </div>
                
                <PrimaryButton 
                  :class="{ 'opacity-50': form.processing }" 
                  :disabled="form.processing"
                  class="bg-gradient-to-r from-emerald-600 to-cyan-600 hover:from-emerald-700 
                         hover:to-cyan-700 text-white font-bold px-8 py-3 rounded-xl 
                         focus:outline-none focus:ring-4 focus:ring-emerald-500/20 
                         transition-all duration-300 transform hover:scale-105 
                         shadow-lg hover:shadow-emerald-500/25 disabled:transform-none 
                         disabled:hover:shadow-lg"
                >
                  <span v-if="form.processing" class="flex items-center gap-2">
                    <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Saving...
                  </span>
                  <span v-else class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Save Changes
                  </span>
                </PrimaryButton>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Head, Link, useForm, router } from "@inertiajs/vue3"
import route from 'ziggy-js'
import { ChevronRightIcon } from '@heroicons/vue/24/outline'

import AppLayout from '@/Layouts/AppLayout.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import TextInput from '@/Components/TextInput.vue'

const props = defineProps({
  image: {
    type: Object,
    required: true
  },
  albums: {
    type: Array,
    default: () => [],
  },
  availableTags: {
    type: Array,
    default: () => [],
  },
})

const newTag = ref('')

const form = useForm({
  title: props.image.title,
  caption: props.image.caption,
  alt_text: props.image.alt_text,
  privacy: props.image.privacy,
  license: props.image.license,
  album_id: props.image.album_id,
  tags: props.image.tags?.map(tag => tag.name) || [],
  allow_comments: props.image.allow_comments ?? true,
  allow_downloads: props.image.allow_downloads ?? true,
})

const getImageUrl = (variant = 'medium') => {
  if (props.image.storage_path) {
    return `http://localhost:9000/gallery-images/${props.image.storage_path}`
  }
  return props.image.url || '/images/placeholder.jpg'
}

const addTag = () => {
  const tag = newTag.value.trim().toLowerCase().replace(/\s+/g, '-')

  if (tag && !form.tags.includes(tag)) {
    form.tags.push(tag)
    newTag.value = ''
  }
}

const addExistingTag = (tagName) => {
  if (!form.tags.includes(tagName)) {
    form.tags.push(tagName)
  }
}

const removeTag = (index) => {
  form.tags.splice(index, 1)
}

const submit = () => {
  form.patch(route('images.update', props.image.slug))
}

const deleteImage = () => {
  if (confirm('Are you sure you want to delete this image? This action cannot be undone.')) {
    router.delete(route('images.destroy', props.image.slug))
  }
}
</script>

<style scoped>
/* Custom Animations */
@keyframes fade-in-up {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes shimmer {
  0% {
    background-position: 0% 50%;
  }
  50% {
    background-position: 100% 50%;
  }
  100% {
    background-position: 0% 50%;
  }
}

@keyframes float {
  0%, 100% {
    transform: translateY(0px) translateX(0px);
  }
  25% {
    transform: translateY(-20px) translateX(10px);
  }
  50% {
    transform: translateY(-10px) translateX(-10px);
  }
  75% {
    transform: translateY(-15px) translateX(5px);
  }
}

.animate-fade-in-up {
  animation: fade-in-up 0.8s ease-out forwards;
  opacity: 0;
}

.animate-shimmer {
  animation: shimmer 3s ease-in-out infinite;
}

.bg-300\% {
  background-size: 300%;
}

.bg-pos-0 {
  background-position: 0% 50%;
}

/* Floating Particles */
.absolute:nth-child(1) { animation: float 8s ease-in-out infinite; }
.absolute:nth-child(2) { animation: float 9s ease-in-out infinite 0.5s; }
.absolute:nth-child(3) { animation: float 7s ease-in-out infinite 1s; }
.absolute:nth-child(4) { animation: float 10s ease-in-out infinite 1.5s; }
.absolute:nth-child(5) { animation: float 8.5s ease-in-out infinite 2s; }
.absolute:nth-child(6) { animation: float 9.5s ease-in-out infinite 2.5s; }
.absolute:nth-child(7) { animation: float 7.5s ease-in-out infinite 3s; }
.absolute:nth-child(8) { animation: float 11s ease-in-out infinite 3.5s; }
.absolute:nth-child(9) { animation: float 8.2s ease-in-out infinite 4s; }
.absolute:nth-child(10) { animation: float 9.8s ease-in-out infinite 4.5s; }
.absolute:nth-child(11) { animation: float 7.8s ease-in-out infinite 5s; }
.absolute:nth-child(12) { animation: float 10.5s ease-in-out infinite 5.5s; }
.absolute:nth-child(13) { animation: float 8.7s ease-in-out infinite 6s; }
.absolute:nth-child(14) { animation: float 9.2s ease-in-out infinite 6.5s; }
.absolute:nth-child(15) { animation: float 7.3s ease-in-out infinite 7s; }
.absolute:nth-child(16) { animation: float 10.8s ease-in-out infinite 7.5s; }
.absolute:nth-child(17) { animation: float 8.9s ease-in-out infinite 8s; }
.absolute:nth-child(18) { animation: float 9.7s ease-in-out infinite 8.5s; }
.absolute:nth-child(19) { animation: float 7.6s ease-in-out infinite 9s; }
.absolute:nth-child(20) { animation: float 11.2s ease-in-out infinite 9.5s; }

/* Custom scrollbar */
::-webkit-scrollbar {
  width: 8px;
}

::-webkit-scrollbar-track {
  background: rgba(30, 41, 59, 0.1);
  border-radius: 10px;
}

::-webkit-scrollbar-thumb {
  background: linear-gradient(45deg, #8b5cf6, #06b6d4);
  border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
  background: linear-gradient(45deg, #7c3aed, #0891b2);
}

/* Glass morphism effects */
.backdrop-blur-xl {
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
}

/* Enhanced focus states */
.focus\:ring-violet-500\/20:focus {
  box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.2);
}

.focus\:ring-cyan-500\/20:focus {
  box-shadow: 0 0 0 3px rgba(6, 182, 212, 0.2);
}

.focus\:ring-purple-500\/20:focus {
  box-shadow: 0 0 0 3px rgba(168, 85, 247, 0.2);
}

.focus\:ring-blue-500\/20:focus {
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
}

.focus\:ring-emerald-500\/20:focus {
  box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2);
}

/* Enhanced hover shadows */
.hover\:shadow-violet-500\/10:hover {
  box-shadow: 0 25px 50px -12px rgba(139, 92, 246, 0.1);
}

.hover\:shadow-cyan-500\/10:hover {
  box-shadow: 0 25px 50px -12px rgba(6, 182, 212, 0.1);
}

.hover\:shadow-purple-500\/10:hover {
  box-shadow: 0 25px 50px -12px rgba(168, 85, 247, 0.1);
}

.hover\:shadow-emerald-500\/10:hover {
  box-shadow: 0 25px 50px -12px rgba(16, 185, 129, 0.1);
}

.hover\:shadow-violet-500\/25:hover {
  box-shadow: 0 25px 50px -12px rgba(139, 92, 246, 0.25);
}

.hover\:shadow-cyan-500\/20:hover {
  box-shadow: 0 25px 50px -12px rgba(6, 182, 212, 0.2);
}

.hover\:shadow-emerald-500\/25:hover {
  box-shadow: 0 25px 50px -12px rgba(16, 185, 129, 0.25);
}

/* Gradient text animations */
.bg-gradient-to-r.from-violet-400.via-purple-400.to-cyan-400 {
  background: linear-gradient(90deg, #a78bfa, #c084fc, #67e8f9);
  background-size: 300% 100%;
  animation: shimmer 3s ease-in-out infinite;
}

/* Form field enhancements */
input[type="text"]:focus,
input[type="email"]:focus,
textarea:focus,
select:focus {
  transform: translateY(-1px);
  transition: all 0.3s ease;
}

/* Button enhancements */
button:not(:disabled):active {
  transform: scale(0.98);
}

/* Card hover effects */
.group:hover .group-hover\:scale-110 {
  transform: scale(1.1);
}

.group:hover .group-hover\:brightness-110 {
  filter: brightness(1.1);
}
</style>