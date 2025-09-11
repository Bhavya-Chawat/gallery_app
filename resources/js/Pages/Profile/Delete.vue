<script setup>
import { ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import DangerButton from '@/Components/UI/DangerButton.vue'
import Modal from '@/Components/UI/Modal.vue'

const confirmingUserDeletion = ref(false)
const password = ref('')
const passwordError = ref('')

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true
}

const deleteUser = () => {
    router.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: (errors) => {
            if (errors.password) {
                passwordError.value = errors.password
                password.value = ''
            }
        },
    })
}

const closeModal = () => {
    confirmingUserDeletion.value = false
    password.value = ''
    passwordError.value = ''
}
</script>

<template>
    <AppLayout>
        <Head title="Delete Account" />

        <div class="max-w-3xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Delete Account
                </h2>

                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Once your account is deleted, all of its resources and data will be permanently deleted.
                </p>

                <div class="mt-6">
                    <DangerButton @click="confirmUserDeletion">
                        Delete Account
                    </DangerButton>
                </div>

                <Modal :show="confirmingUserDeletion" @close="closeModal">
                    <div class="p-6">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            Are you sure you want to delete your account?
                        </h2>

                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                            Please enter your password to confirm you would like to permanently delete your account.
                        </p>

                        <div class="mt-6">
                            <input
                                type="password"
                                v-model="password"
                                placeholder="Password"
                                class="w-full px-3 py-2 border rounded-md"
                                @keyup.enter="deleteUser"
                            />
                            <p v-if="passwordError" class="mt-2 text-sm text-red-600">
                                {{ passwordError }}
                            </p>
                        </div>

                        <div class="mt-6 flex justify-end space-x-3">
                            <button
                                type="button"
                                class="px-4 py-2 text-sm text-gray-700 hover:text-gray-900"
                                @click="closeModal"
                            >
                                Cancel
                            </button>
                            <DangerButton
                                @click="deleteUser"
                                :class="{ 'opacity-25': password === '' }"
                                :disabled="password === ''"
                            >
                                Delete Account
                            </DangerButton>
                        </div>
                    </div>
                </Modal>
            </div>
        </div>
    </AppLayout>
</template>