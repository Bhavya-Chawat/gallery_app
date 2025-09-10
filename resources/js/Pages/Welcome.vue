<template>
    <div
        class="min-h-screen bg-gradient-to-br from-indigo-100 via-white to-cyan-100"
    >
        <!-- Navigation -->
        <nav class="flex justify-between items-center p-6 max-w-7xl mx-auto">
            <div class="flex items-center space-x-2">
                <div
                    class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center"
                >
                    <svg
                        class="w-5 h-5 text-white"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"
                            clip-rule="evenodd"
                        ></path>
                    </svg>
                </div>
                <h1 class="text-xl font-bold text-gray-900">Image Gallery</h1>
            </div>

            <div class="flex items-center space-x-4" v-if="canLogin">
                <Link
                    v-if="$page.props.auth.user"
                    :href="route('dashboard')"
                    class="text-gray-700 hover:text-indigo-600 font-medium"
                >
                    Dashboard
                </Link>
                <template v-else>
                    <Link
                        :href="route('login')"
                        class="text-gray-700 hover:text-indigo-600 font-medium"
                    >
                        Log in
                    </Link>
                    <Link
                        v-if="canRegister"
                        :href="route('register')"
                        class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 font-medium"
                    >
                        Register
                    </Link>
                </template>
            </div>
        </nav>

        <!-- Hero Section -->
        <div class="max-w-7xl mx-auto px-6 py-24">
            <div class="text-center">
                <h2 class="text-5xl font-bold text-gray-900 mb-6">
                    Manage Your Images
                    <span class="text-indigo-600">Beautifully</span>
                </h2>
                <p class="text-xl text-gray-600 mb-8 max-w-3xl mx-auto">
                    A modern image gallery application built with Laravel 11,
                    Vue 3, Inertia.js, and Tailwind CSS. Upload, organize, and
                    share your photos with ease.
                </p>

                <div class="flex justify-center space-x-4">
                    <Link
                        v-if="!$page.props.auth.user"
                        :href="route('register')"
                        class="bg-indigo-600 text-white px-8 py-3 rounded-lg hover:bg-indigo-700 font-medium text-lg"
                    >
                        Get Started
                    </Link>
                    <Link
                        v-else
                        :href="route('dashboard')"
                        class="bg-indigo-600 text-white px-8 py-3 rounded-lg hover:bg-indigo-700 font-medium text-lg"
                    >
                        Go to Dashboard
                    </Link>
                    <a
                        href="#features"
                        class="border border-gray-300 text-gray-700 px-8 py-3 rounded-lg hover:bg-gray-50 font-medium text-lg"
                    >
                        Learn More
                    </a>
                </div>
            </div>
        </div>

        <!-- Features Section -->
        <div id="features" class="max-w-7xl mx-auto px-6 py-16">
            <div class="text-center mb-16">
                <h3 class="text-3xl font-bold text-gray-900 mb-4">Features</h3>
                <p class="text-gray-600">
                    Everything you need for modern image management
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white p-6 rounded-xl shadow-sm border">
                    <div
                        class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center mb-4"
                    >
                        <svg
                            class="w-6 h-6 text-indigo-600"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"
                            ></path>
                        </svg>
                    </div>
                    <h4 class="text-xl font-semibold mb-2">Easy Upload</h4>
                    <p class="text-gray-600">
                        Drag and drop or click to upload multiple images at once
                        with progress tracking.
                    </p>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border">
                    <div
                        class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4"
                    >
                        <svg
                            class="w-6 h-6 text-green-600"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"
                            ></path>
                        </svg>
                    </div>
                    <h4 class="text-xl font-semibold mb-2">
                        Smart Organization
                    </h4>
                    <p class="text-gray-600">
                        Organize images in albums, add tags, and use powerful
                        search functionality.
                    </p>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border">
                    <div
                        class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4"
                    >
                        <svg
                            class="w-6 h-6 text-purple-600"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"
                            ></path>
                        </svg>
                    </div>
                    <h4 class="text-xl font-semibold mb-2">Easy Sharing</h4>
                    <p class="text-gray-600">
                        Share albums and individual photos with customizable
                        privacy settings.
                    </p>
                </div>
            </div>
        </div>

        <!-- Tech Stack Section -->
        <div class="bg-gray-50 py-16">
            <div class="max-w-7xl mx-auto px-6 text-center">
                <h3 class="text-3xl font-bold text-gray-900 mb-4">
                    Built with Modern Technology
                </h3>
                <p class="text-gray-600 mb-8">
                    Powered by industry-leading frameworks and tools
                </p>

                <div
                    class="flex flex-wrap justify-center items-center space-x-8 space-y-4"
                >
                    <div
                        class="flex items-center space-x-2 bg-white px-4 py-2 rounded-lg shadow-sm"
                    >
                        <span class="text-red-500 font-semibold"
                            >Laravel 11</span
                        >
                    </div>
                    <div
                        class="flex items-center space-x-2 bg-white px-4 py-2 rounded-lg shadow-sm"
                    >
                        <span class="text-green-500 font-semibold">Vue 3</span>
                    </div>
                    <div
                        class="flex items-center space-x-2 bg-white px-4 py-2 rounded-lg shadow-sm"
                    >
                        <span class="text-purple-500 font-semibold"
                            >Inertia.js</span
                        >
                    </div>
                    <div
                        class="flex items-center space-x-2 bg-white px-4 py-2 rounded-lg shadow-sm"
                    >
                        <span class="text-blue-500 font-semibold"
                            >Tailwind CSS</span
                        >
                    </div>
                    <div
                        class="flex items-center space-x-2 bg-white px-4 py-2 rounded-lg shadow-sm"
                    >
                        <span class="text-blue-600 font-semibold">Docker</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="bg-white py-8">
            <div class="max-w-7xl mx-auto px-6 text-center">
                <p class="text-gray-600">
                    © {{ new Date().getFullYear() }} Image Gallery. Built with
                    ❤️ using Laravel Breeze.
                </p>
            </div>
        </footer>
    </div>
</template>

<script setup>
import { Link } from "@inertiajs/vue3";

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});
</script>
