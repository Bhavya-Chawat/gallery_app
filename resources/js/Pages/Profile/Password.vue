<script setup>
import { ref } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import InputError from '@/Components/UI/InputError.vue'
import PrimaryButton from '@/Components/UI/PrimaryButton.vue'

const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: ''
})

const updatePassword = () => {
    passwordForm.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => {
            passwordForm.reset()
            if (status) {
                status.value = 'Password updated successfully.'
            }
        }
    })
}

const status = ref(null)
</script>

<template>
    <AppLayout>
        <Head title="Update Password" />

        <div class="max-w-3xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Update Password
                </h2>

                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Ensure your account is using a long, random password to stay secure.
                </p>

                <form @submit.prevent="updatePassword" class="mt-6 space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Current Password
                        </label>
                        <input
                            v-model="passwordForm.current_password"
                            type="password"
                            autocomplete="current-password"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600"
                        />
                        <InputError :message="passwordForm.errors.current_password" class="mt-2" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            New Password
                        </label>
                        <input
                            v-model="passwordForm.password"
                            type="password"
                            autocomplete="new-password"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600"
                        />
                        <InputError :message="passwordForm.errors.password" class="mt-2" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Confirm Password
                        </label>
                        <input
                            v-model="passwordForm.password_confirmation"
                            type="password"
                            autocomplete="new-password"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600"
                        />
                        <InputError :message="passwordForm.errors.password_confirmation" class="mt-2" />
                    </div>

                    <div class="flex items-center gap-4">
                        <PrimaryButton :disabled="passwordForm.processing">
                            Save
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