<script setup>
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { CheckCircle } from 'lucide-vue-next'
import axios from 'axios'

const props = defineProps({
  album: {
    type: Object,
    required: true
  }
})

const form = ref({
  title: props.album.title,
  description: props.album.description,
  privacy: props.album.privacy
})

const updateAlbum = async () => {
  try {
    await axios.put(`/api/albums/${props.album.id}`, form.value)
    router.visit(`/albums/${props.album.id}`)
  } catch (error) {
    console.error('Failed to update album:', error)
  }
}
</script>

<template>
  <AppLayout>
    <div class="min-h-screen bg-gray-50 dark:bg-slate-900">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white/70 dark:bg-slate-800/70 backdrop-blur-xl rounded-xl border border-gray-200 dark:border-slate-700 p-6">
          <form @submit.prevent="updateAlbum" class="space-y-6">
            <!-- Form fields similar to Create.vue but with edit functionality -->
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>