<script setup>
import { ref } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import InputError from '@/Components/UI/InputError.vue'
import PrimaryButton from '@/Components/UI/PrimaryButton.vue'

const form = useForm({
    site_name: '',
    site_description: '',
    enable_registration: true,
    moderate_uploads: false,
    max_upload_size: 10,
    allowed_file_types: ['jpg', 'jpeg', 'png', 'gif'],
})

const updateSettings = () => {
    form.post(route('settings.update'), {
        preserveScroll: true,
        onSuccess: () => {
            status.value = 'Settings updated successfully.'
        },
    })
}

const status = ref(null)
</script>

<template>
    <AppLayout>
        <Head title="Site Settings" />

        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">
                    Site Settings
                </h2>

                <form @submit.prevent="updateSettings" class="space-y-6">
                    <!-- Site Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Site Name
                        </label>
                        <input
                            v-model="form.site_name"
                            type="text"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600"
                        />
                        <InputError :message="form.errors.site_name" class="mt-2" />
                    </div>

                    <!-- Site Description -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Site Description
                        </label>
                        <textarea
                            v-model="form.site_description"
                            rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600"
                        ></textarea>
                        <InputError :message="form.errors.site_description" class="mt-2" />
                    </div>

                    <!-- Feature Toggles -->
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input
                                    v-model="form.enable_registration"
                                    type="checkbox"
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                />
                            </div>
                            <div class="ml-3 text-sm">
                                <label class="font-medium text-gray-700 dark:text-gray-300">
                                    Enable User Registration
                                </label>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input
                                    v-model="form.moderate_uploads"
                                    type="checkbox"
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                />
                            </div>
                            <div class="ml-3 text-sm">
                                <label class="font-medium text-gray-700 dark:text-gray-300">
                                    Moderate Image Uploads
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-center gap-4">
                        <PrimaryButton :disabled="form.processing">
                            Save Settings
                        </PrimaryButton>

                        <p v-if="status" class="text-sm text-green-600 dark:text-green-400">
                            {{ status }}
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>