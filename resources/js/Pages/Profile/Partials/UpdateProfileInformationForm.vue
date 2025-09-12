<template>
  <section>
    <header>
      <h2 class="text-lg font-medium text-gray-900">
        Profile Information
      </h2>
      <p class="mt-1 text-sm text-gray-600">
        Update your account's profile information and email address.
      </p>
    </header>

    <form @submit.prevent="updateProfile" class="mt-6 space-y-6">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <InputLabel for="name" value="Name" />
          <TextInput
            id="name"
            type="text"
            class="mt-1 block w-full"
            v-model="form.name"
            required
            autofocus
            autocomplete="name"
          />
          <InputError class="mt-2" :message="form.errors.name" />
        </div>

        <div>
          <InputLabel for="email" value="Email" />
          <TextInput
            id="email"
            type="email"
            class="mt-1 block w-full"
            v-model="form.email"
            required
            autocomplete="username"
          />
          <InputError class="mt-2" :message="form.errors.email" />
        </div>
      </div>

      <div>
        <InputLabel for="bio" value="Bio" />
        <textarea
          id="bio"
          v-model="form.bio"
          rows="3"
          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
          placeholder="Tell people about yourself..."
        ></textarea>
        <InputError class="mt-2" :message="form.errors.bio" />
        <p class="mt-1 text-sm text-gray-500">{{ (form.bio || '').length }}/1000 characters</p>
      </div>

      <div>
        <InputLabel for="website" value="Website" />
        <TextInput
          id="website"
          type="url"
          class="mt-1 block w-full"
          v-model="form.website"
          placeholder="https://your-website.com"
          autocomplete="url"
        />
        <InputError class="mt-2" :message="form.errors.website" />
      </div>

      <div>
        <InputLabel value="Social Links" />
        <div class="mt-2 space-y-3">
          <div>
            <label for="twitter" class="block text-sm text-gray-700 mb-1">Twitter</label>
            <TextInput
              id="twitter"
              type="text"
              class="block w-full"
              v-model="form.social_links.twitter"
              placeholder="@username or https://twitter.com/username"
            />
          </div>
          <div>
            <label for="instagram" class="block text-sm text-gray-700 mb-1">Instagram</label>
            <TextInput
              id="instagram"
              type="text"
              class="block w-full"
              v-model="form.social_links.instagram"
              placeholder="@username or https://instagram.com/username"
            />
          </div>
          <div>
            <label for="linkedin" class="block text-sm text-gray-700 mb-1">LinkedIn</label>
            <TextInput
              id="linkedin"
              type="text"
              class="block w-full"
              v-model="form.social_links.linkedin"
              placeholder="https://linkedin.com/in/username"
            />
          </div>
        </div>
        <InputError class="mt-2" :message="form.errors.social_links" />
      </div>

      <div>
        <label class="flex items-center">
          <input
            v-model="form.email_notifications"
            type="checkbox"
            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
          />
          <span class="ml-2 text-sm text-gray-700">
            Email notifications
          </span>
        </label>
        <p class="mt-1 text-xs text-gray-500">
          Receive email notifications for comments, likes, and system updates.
        </p>
      </div>

      <div v-if="mustVerifyEmail && user.email_verified_at === null">
        <p class="mt-2 text-sm text-gray-800">
          Your email address is unverified.
          <Link
            :href="$route('verification.send')"
            method="post"
            as="button"
            class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
          >
            Click here to re-send the verification email.
          </Link>
        </p>

        <div
          v-show="status === 'verification-link-sent'"
          class="mt-2 text-sm font-medium text-green-600"
        >
          A new verification link has been sent to your email address.
        </div>
      </div>

      <div class="flex items-center gap-4">
        <PrimaryButton :disabled="form.processing">Save Changes</PrimaryButton>

        <Transition
          enter-active-class="transition ease-in-out"
          enter-from-class="opacity-0"
          leave-active-class="transition ease-in-out"
          leave-to-class="opacity-0"
        >
          <p v-if="form.recentlySuccessful" class="text-sm text-gray-600">
            Saved.
          </p>
        </Transition>
      </div>
    </form>
  </section>
</template>

<script setup>
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import TextInput from '@/Components/TextInput.vue'
import { Link, useForm, usePage } from '@inertiajs/vue3'

defineProps({
  mustVerifyEmail: Boolean,
  status: String,
  user: Object,
})

const user = usePage().props.auth.user

const form = useForm({
  name: user.name || '',
  email: user.email || '',
  bio: user.bio || '',
  website: user.website || '',
  social_links: {
    twitter: user.social_links?.twitter || '',
    instagram: user.social_links?.instagram || '',
    linkedin: user.social_links?.linkedin || '',
  },
  email_notifications: user.email_notifications || false,
})

const updateProfile = () => {
  form.patch('/profile', {
    preserveScroll: true,
  })
}
</script>
