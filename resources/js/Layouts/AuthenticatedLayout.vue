<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-100">
            <nav
                class="border-b border-gray-100 bg-white"
            >
                <!-- Primary Navigation Menu -->
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 justify-between">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="flex shrink-0 items-center">
                                <Link :href="route('dashboard')">
                                    <ApplicationLogo
                                        class="block h-9 w-auto fill-current text-gray-800"
                                    />
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div
                                class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex"
                            >
                                <NavLink
                                    :href="route('dashboard')"
                                    :active="route().current('dashboard')"
                                >
                                    Dashboard
                                </NavLink>
                            </div>
                        </div>

                        <div class="hidden sm:ms-6 sm:flex sm:items-center">
                            <!-- Settings Dropdown -->
                            <div class="relative ms-3">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none"
                                            >
                                                {{ $page.props.auth.user.name }}

                                                <svg
                                                    class="-me-0.5 ms-2 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink
                                            :href="route('profile.edit')"
                                        >
                                            Profile
                                        </DropdownLink>
                                        <DropdownLink
                                            :href="route('logout')"
                                            method="post"
                                            as="button"
                                        >
                                            Log Out
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button
                                @click="
                                    showingNavigationDropdown =
                                        !showingNavigationDropdown
                                "
                                class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500 focus:outline-none"
                            >
                                <svg
                                    class="h-6 w-6"
                                    stroke="currentColor"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex':
                                                !showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex':
                                                showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div
                    :class="{
                        block: showingNavigationDropdown,
                        hidden: !showingNavigationDropdown,
                    }"
                    class="sm:hidden"
                >
                    <div class="space-y-1 pb-3 pt-2">
                        <ResponsiveNavLink
                            :href="route('dashboard')"
                            :active="route().current('dashboard')"
                        >
                            Dashboard
                        </ResponsiveNavLink>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div
                        class="border-t border-gray-200 pb-1 pt-4"
                    >
                        <div class="px-4">
                            <div
                                class="text-base font-medium text-gray-800"
                            >
                                {{ $page.props.auth.user.name }}
                            </div>
                            <div class="text-sm font-medium text-gray-500">
                                {{ $page.props.auth.user.email }}
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.edit')">
                                Profile
                            </ResponsiveNavLink>
                            <ResponsiveNavLink
                                :href="route('logout')"
                                method="post"
                                as="button"
                            >
                                Log Out
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header
                class="bg-white shadow"
                v-if="$slots.header"
            >
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>
/*<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
</script>

<template>
    <div>
        <div class="min-h-screen bg-slate-950 relative overflow-hidden">
            <!-- Animated mesh background -->
            <div class="fixed inset-0 overflow-hidden pointer-events-none">
                <svg class="absolute inset-0 w-full h-full opacity-20" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <linearGradient id="meshGradient" x1="0%" y1="0%" x2="100%" y2="100%" gradientTransform="rotate(45)">
                            <stop offset="0%" class="animate-colorShift1">
                                <animate attributeName="stop-color" values="#8b5cf6;#06b6d4;#a855f7;#8b5cf6" dur="8s" repeatCount="indefinite" />
                            </stop>
                            <stop offset="50%" class="animate-colorShift2">
                                <animate attributeName="stop-color" values="#06b6d4;#a855f7;#8b5cf6;#06b6d4" dur="8s" repeatCount="indefinite" />
                            </stop>
                            <stop offset="100%" class="animate-colorShift3">
                                <animate attributeName="stop-color" values="#a855f7;#8b5cf6;#06b6d4;#a855f7" dur="8s" repeatCount="indefinite" />
                            </stop>
                        </linearGradient>
                    </defs>
                    <rect width="100" height="100" fill="url(#meshGradient)" opacity="0.1">
                        <animateTransform attributeName="transform" attributeType="XML" type="translate" values="0,0; -10,5; 0,0" dur="10s" repeatCount="indefinite" />
                    </rect>
                    <circle cx="20" cy="20" r="2" fill="url(#meshGradient)" opacity="0.3">
                        <animateTransform attributeName="transform" attributeType="XML" type="translate" values="0,0; 5,-3; 0,0" dur="6s" repeatCount="indefinite" />
                    </circle>
                    <circle cx="80" cy="30" r="1.5" fill="url(#meshGradient)" opacity="0.4">
                        <animateTransform attributeName="transform" attributeType="XML" type="translate" values="0,0; -3,7; 0,0" dur="8s" repeatCount="indefinite" />
                    </circle>
                    <circle cx="50" cy="70" r="2.5" fill="url(#meshGradient)" opacity="0.2">
                        <animateTransform attributeName="transform" attributeType="XML" type="translate" values="0,0; 8,-5; 0,0" dur="12s" repeatCount="indefinite" />
                    </circle>
                </svg>
            </div>

            <!-- Floating particle system -->
            <div class="fixed inset-0 overflow-hidden pointer-events-none">
                <div v-for="i in 20" :key="i" 
                     class="absolute w-1 h-1 rounded-full animate-float opacity-30"
                     :class="getParticleClass(i)"
                     :style="getParticleStyle(i)">
                </div>
            </div>

            <!-- Enhanced Navigation -->
            <nav class="relative border-b border-slate-700/50 bg-slate-900/60 backdrop-blur-xl shadow-xl shadow-slate-900/20">
                <!-- Navigation glow effect -->
                <div class="absolute inset-0 bg-gradient-to-r from-violet-500/5 via-transparent to-cyan-500/5 pointer-events-none"></div>
                
                <!-- Primary Navigation Menu -->
                <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 justify-between">
                        <div class="flex">
                            <!-- Enhanced Logo -->
                            <div class="flex shrink-0 items-center group">
                                <Link :href="route('dashboard')" class="relative">
                                    <!-- Logo glow effect -->
                                    <div class="absolute inset-0 bg-gradient-to-r from-violet-500/20 to-cyan-500/20 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 blur-sm -z-10"></div>
                                    <ApplicationLogo
                                        class="relative block h-9 w-auto fill-current text-slate-300 group-hover:text-white transition-colors duration-300 filter group-hover:drop-shadow-[0_0_8px_rgba(139,92,246,0.5)]"
                                    />
                                    <!-- Floating particles around logo -->
                                    <div class="absolute -top-1 -right-1 w-1 h-1 bg-violet-400 rounded-full opacity-0 group-hover:opacity-60 group-hover:animate-ping transition-opacity duration-300"></div>
                                    <div class="absolute -bottom-1 -left-1 w-0.5 h-0.5 bg-cyan-400 rounded-full opacity-0 group-hover:opacity-40 group-hover:animate-pulse transition-opacity duration-300"></div>
                                </Link>
                            </div>

                            <!-- Enhanced Navigation Links -->
                            <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                                <NavLink
                                    :href="route('dashboard')"
                                    :active="route().current('dashboard')"
                                    class="group relative px-4 py-2 rounded-xl transition-all duration-300 hover:scale-105"
                                >
                                    <!-- Link background glow -->
                                    <div class="absolute inset-0 bg-gradient-to-r from-violet-500/0 to-cyan-500/0 group-hover:from-violet-500/10 group-hover:to-cyan-500/10 rounded-xl transition-all duration-300"></div>
                                    <span class="relative text-slate-300 group-hover:text-white">Dashboard</span>
                                </NavLink>
                            </div>
                        </div>

                        <div class="hidden sm:ms-6 sm:flex sm:items-center">
                            <!-- Enhanced Settings Dropdown -->
                            <div class="relative ms-3">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                class="group relative inline-flex items-center rounded-xl border border-slate-600/30 bg-slate-800/50 backdrop-blur-sm px-4 py-2 text-sm font-medium leading-4 text-slate-300 transition-all duration-300 ease-in-out hover:text-white hover:bg-slate-700/50 hover:border-violet-500/40 hover:scale-105 hover:shadow-lg hover:shadow-violet-500/20 focus:outline-none"
                                            >
                                                <!-- Button glow effect -->
                                                <div class="absolute inset-0 bg-gradient-to-r from-violet-500/0 to-cyan-500/0 group-hover:from-violet-500/10 group-hover:to-cyan-500/10 rounded-xl transition-all duration-300"></div>
                                                
                                                <span class="relative">{{ $page.props.auth.user.name }}</span>

                                                <svg
                                                    class="relative -me-0.5 ms-2 h-4 w-4 transition-transform duration-300 group-hover:rotate-180"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink :href="route('profile.edit')">
                                            Profile
                                        </DropdownLink>
                                        <DropdownLink
                                            :href="route('logout')"
                                            method="post"
                                            as="button"
                                        >
                                            Log Out
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Enhanced Hamburger -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button
                                @click="showingNavigationDropdown = !showingNavigationDropdown"
                                class="group relative inline-flex items-center justify-center rounded-xl p-2 text-slate-400 bg-slate-800/50 backdrop-blur-sm border border-slate-600/30 transition-all duration-300 ease-in-out hover:bg-slate-700/50 hover:text-slate-300 hover:border-violet-500/40 hover:scale-105 focus:bg-slate-700/50 focus:text-slate-300 focus:outline-none"
                            >
                                <!-- Button glow effect -->
                                <div class="absolute inset-0 bg-gradient-to-r from-violet-500/0 to-cyan-500/0 group-hover:from-violet-500/10 group-hover:to-cyan-500/10 rounded-xl transition-all duration-300"></div>
                                
                                <svg class="relative h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path
                                        :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex': !showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex': showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Enhanced Responsive Navigation Menu -->
                <div
                    :class="{
                        block: showingNavigationDropdown,
                        hidden: !showingNavigationDropdown,
                    }"
                    class="sm:hidden relative bg-slate-800/60 backdrop-blur-xl border-t border-slate-700/50"
                >
                    <!-- Mobile menu glow effect -->
                    <div class="absolute inset-0 bg-gradient-to-r from-violet-500/5 via-transparent to-cyan-500/5 pointer-events-none"></div>
                    
                    <div class="relative space-y-1 pb-3 pt-2">
                        <ResponsiveNavLink
                            :href="route('dashboard')"
                            :active="route().current('dashboard')"
                            class="group relative mx-2 px-4 py-2 rounded-xl transition-all duration-300"
                        >
                            <!-- Link background glow -->
                            <div class="absolute inset-0 bg-gradient-to-r from-violet-500/0 to-cyan-500/0 group-hover:from-violet-500/10 group-hover:to-cyan-500/10 rounded-xl transition-all duration-300"></div>
                            <span class="relative">Dashboard</span>
                        </ResponsiveNavLink>
                    </div>

                    <!-- Enhanced Responsive Settings Options -->
                    <div class="relative border-t border-slate-700/50 pb-1 pt-4">
                        <div class="px-4">
                            <div class="text-base font-medium text-slate-200 bg-gradient-to-r from-violet-400 to-cyan-400 bg-clip-text text-transparent">
                                {{ $page.props.auth.user.name }}
                            </div>
                            <div class="text-sm font-medium text-slate-400">
                                {{ $page.props.auth.user.email }}
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink 
                                :href="route('profile.edit')"
                                class="group relative mx-2 px-4 py-2 rounded-xl transition-all duration-300"
                            >
                                <!-- Link background glow -->
                                <div class="absolute inset-0 bg-gradient-to-r from-violet-500/0 to-cyan-500/0 group-hover:from-violet-500/10 group-hover:to-cyan-500/10 rounded-xl transition-all duration-300"></div>
                                <span class="relative">Profile</span>
                            </ResponsiveNavLink>
                            <ResponsiveNavLink
                                :href="route('logout')"
                                method="post"
                                as="button"
                                class="group relative mx-2 px-4 py-2 rounded-xl transition-all duration-300"
                            >
                                <!-- Link background glow -->
                                <div class="absolute inset-0 bg-gradient-to-r from-red-500/0 to-pink-500/0 group-hover:from-red-500/10 group-hover:to-pink-500/10 rounded-xl transition-all duration-300"></div>
                                <span class="relative">Log Out</span>
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Enhanced Page Heading -->
            <header
                class="relative bg-slate-900/40 backdrop-blur-xl shadow-xl shadow-slate-900/20 border-b border-slate-700/30"
                v-if="$slots.header"
            >
                <!-- Header glow effect -->
                <div class="absolute inset-0 bg-gradient-to-r from-violet-500/5 via-transparent to-cyan-500/5 pointer-events-none"></div>
                
                <div class="relative mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <!-- Header floating particles -->
                    <div class="absolute inset-0 overflow-hidden pointer-events-none">
                        <div class="absolute top-2 left-8 w-1 h-1 bg-violet-400 rounded-full animate-pulse opacity-40"></div>
                        <div class="absolute bottom-3 right-12 w-0.5 h-0.5 bg-cyan-400 rounded-full animate-bounce opacity-30"></div>
                    </div>
                    
                    <div class="animate-fadeInUp">
                        <slot name="header" />
                    </div>
                </div>
            </header>

            <!-- Enhanced Page Content -->
            <main class="relative">
                <!-- Main content glow overlay -->
                <div class="absolute inset-0 bg-gradient-to-br from-violet-500/5 via-transparent to-cyan-500/5 pointer-events-none"></div>
                
                <!-- Content wrapper with glassmorphism -->
                <div class="relative min-h-[calc(100vh-8rem)] bg-slate-900/20 backdrop-blur-sm">
                    <!-- Main content floating particles -->
                    <div class="absolute inset-0 overflow-hidden pointer-events-none">
                        <div class="absolute top-8 left-12 w-1.5 h-1.5 bg-purple-400 rounded-full animate-float opacity-20"></div>
                        <div class="absolute bottom-12 right-16 w-1 h-1 bg-violet-400 rounded-full animate-float opacity-30" style="animation-delay: 1s;"></div>
                        <div class="absolute top-1/3 left-16 w-0.5 h-0.5 bg-cyan-400 rounded-full animate-float opacity-25" style="animation-delay: 2s;"></div>
                    </div>
                    
                    <slot />
                </div>
            </main>
        </div>
    </div>
</template>

<script>
// Particle system helper functions
const getParticleClass = (index) => {
  const colors = ['bg-violet-400', 'bg-cyan-400', 'bg-purple-400', 'bg-indigo-400']
  return colors[index % colors.length]
}

const getParticleStyle = (index) => {
  return {
    left: `${Math.random() * 100}%`,
    top: `${Math.random() * 100}%`,
    animationDelay: `${Math.random() * 3}s`,
    animationDuration: `${3 + Math.random() * 4}s`
  }
}
</script>

<style scoped>
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes float {
  0%, 100% {
    transform: translateY(0px) rotate(0deg);
  }
  33% {
    transform: translateY(-10px) rotate(120deg);
  }
  66% {
    transform: translateY(5px) rotate(240deg);
  }
}

@keyframes colorShift1 {
  0%, 100% { stop-color: #8b5cf6; }
  33% { stop-color: #06b6d4; }
  66% { stop-color: #a855f7; }
}

@keyframes colorShift2 {
  0%, 100% { stop-color: #06b6d4; }
  33% { stop-color: #a855f7; }
  66% { stop-color: #8b5cf6; }
}

@keyframes colorShift3 {
  0%, 100% { stop-color: #a855f7; }
  33% { stop-color: #8b5cf6; }
  66% { stop-color: #06b6d4; }
}

.animate-fadeInUp {
  animation: fadeInUp 0.6s ease-out;
}

.animate-float {
  animation: float 4s ease-in-out infinite;
}

/* Enhanced responsive navigation transitions */
.sm\:hidden > div {
  animation: fadeInUp 0.3s ease-out;
}
</style>