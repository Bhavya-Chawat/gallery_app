

<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    email: {
        type: String,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Reset Password" />

        <!-- Enhanced Animated Background -->
        <div class="fixed inset-0 overflow-hidden">
            <!-- Base Background -->
            <div class="absolute inset-0 bg-slate-900">
                <!-- Dynamic SVG Mesh Background -->
                <svg class="absolute inset-0 w-full h-full opacity-25" viewBox="0 0 100 100" preserveAspectRatio="none">
                    <defs>
                        <linearGradient id="mesh-gradient-reset" x1="0%" y1="0%" x2="100%" y2="100%" gradientTransform="rotate(30)">
                            <stop offset="0%" style="stop-color:#8b5cf6;stop-opacity:0.4">
                                <animate attributeName="stop-color" 
                                    values="#8b5cf6;#06b6d4;#a855f7;#8b5cf6" 
                                    dur="8s" 
                                    repeatCount="indefinite" />
                            </stop>
                            <stop offset="50%" style="stop-color:#06b6d4;stop-opacity:0.3">
                                <animate attributeName="stop-color" 
                                    values="#06b6d4;#a855f7;#8b5cf6;#06b6d4" 
                                    dur="8s" 
                                    repeatCount="indefinite" />
                            </stop>
                            <stop offset="100%" style="stop-color:#a855f7;stop-opacity:0.4">
                                <animate attributeName="stop-color" 
                                    values="#a855f7;#8b5cf6;#06b6d4;#a855f7" 
                                    dur="8s" 
                                    repeatCount="indefinite" />
                            </stop>
                        </linearGradient>
                    </defs>
                    <!-- Animated Mesh Patterns -->
                    <path d="M0,10 Q30,5 60,10 T100,10 L100,0 L0,0 Z" fill="url(#mesh-gradient-reset)" opacity="0.6">
                        <animateTransform
                            attributeName="transform"
                            type="translate"
                            values="0,0;8,5;0,0"
                            dur="12s"
                            repeatCount="indefinite"/>
                    </path>
                    <path d="M0,30 Q50,20 100,30 L100,0 L0,0 Z" fill="url(#mesh-gradient-reset)" opacity="0.4">
                        <animateTransform
                            attributeName="transform"
                            type="translate"
                            values="0,0;-6,3;0,0"
                            dur="15s"
                            repeatCount="indefinite"/>
                    </path>
                    <path d="M0,90 Q25,85 50,90 T100,90 L100,100 L0,100 Z" fill="url(#mesh-gradient-reset)" opacity="0.5">
                        <animateTransform
                            attributeName="transform"
                            type="translate"
                            values="0,0;10,-4;0,0"
                            dur="10s"
                            repeatCount="indefinite"/>
                    </path>
                </svg>
                
                <!-- Floating Orbs -->
                <div class="absolute top-16 right-20 w-80 h-80 bg-gradient-to-r from-violet-500/15 to-cyan-500/15 rounded-full blur-3xl animate-pulse-slow"></div>
                <div class="absolute top-48 left-16 w-96 h-96 bg-gradient-to-l from-purple-500/10 to-violet-500/10 rounded-full blur-3xl animate-pulse-slow delay-1000"></div>
                <div class="absolute bottom-32 right-1/4 w-72 h-72 bg-gradient-to-t from-cyan-500/12 to-purple-500/12 rounded-full blur-3xl animate-pulse-slow delay-700"></div>
                
                <!-- Enhanced Particle System -->
                <div class="absolute inset-0">
                    <div v-for="i in 20" :key="i" 
                         :class="`absolute rounded-full animate-float-particle`"
                         :style="{
                           left: Math.random() * 100 + '%',
                           top: Math.random() * 100 + '%',
                           width: (2 + Math.random() * 4) + 'px',
                           height: (2 + Math.random() * 4) + 'px',
                           animationDelay: Math.random() * 10 + 's',
                           animationDuration: (6 + Math.random() * 8) + 's'
                         }">
                        <div class="w-full h-full bg-gradient-to-r from-cyan-400/30 to-violet-400/30 rounded-full animate-pulse"></div>
                    </div>
                </div>

                <!-- Geometric Accents -->
                <div class="absolute top-1/3 left-8 w-24 h-24 border border-violet-500/20 rounded-full animate-spin-slow"></div>
                <div class="absolute bottom-1/4 right-12 w-20 h-20 border-2 border-cyan-500/20 rotate-45 animate-pulse-slow"></div>
                <div class="absolute top-2/3 right-24 w-16 h-16 bg-gradient-to-br from-purple-500/10 to-transparent rounded-full animate-bounce-gentle"></div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="relative z-10 min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
            <div class="max-w-md w-full animate-fade-in-up">
                
                <!-- Premium Glassmorphism Card -->
                <div class="relative bg-white/5 backdrop-blur-2xl border border-white/20 rounded-3xl p-8 shadow-2xl hover:bg-white/10 hover:shadow-3xl hover:shadow-violet-500/10 transition-all duration-700 group">
                    
                    <!-- Enhanced Gradient Border Effect -->
                    <div class="absolute inset-0 rounded-3xl bg-gradient-to-r from-violet-500/20 via-cyan-500/15 to-purple-500/20 p-[1px] opacity-0 group-hover:opacity-100 transition-opacity duration-500">
                        <div class="w-full h-full bg-slate-900/90 rounded-3xl"></div>
                    </div>
                    
                    <div class="relative z-10">
                        <!-- Header -->
                        <div class="text-center mb-8">
                            <!-- Logo/Icon -->
                            <div class="flex justify-center mb-6">
                                <div class="w-20 h-20 bg-gradient-to-br from-violet-500 via-purple-500 to-cyan-500 rounded-2xl flex items-center justify-center animate-pulse-glow shadow-lg shadow-violet-500/25">
                                    <svg class="h-10 w-10 text-white animate-float-subtle" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                    </svg>
                                </div>
                            </div>

                            <h1 class="text-4xl font-extrabold mb-3">
                                <span class="bg-gradient-to-r from-violet-400 via-white to-cyan-400 bg-clip-text text-transparent animate-shimmer bg-size-200">
                                    Reset Your Password
                                </span>
                            </h1>
                            <p class="text-slate-300 text-lg font-medium leading-relaxed">
                                Create a new secure password for your account
                            </p>
                            <p class="mt-3 text-sm text-slate-400">
                                Enter your new password below to complete the reset process
                            </p>
                        </div>

                        <!-- Password Reset Form -->
                        <form class="space-y-6" @submit.prevent="submit">
                            
                            <!-- Email Field (Read-only display) -->
                            <div class="space-y-2">
                                <InputLabel for="email" value="Email Address" class="text-slate-300 font-medium text-sm tracking-wide" />
                                <div class="relative group">
                                    <TextInput
                                        id="email"
                                        type="email"
                                        class="w-full px-4 py-3 bg-white/5 border border-white/20 rounded-xl text-white placeholder-slate-400 focus:border-violet-500 focus:ring-2 focus:ring-violet-500/20 focus:bg-white/10 transition-all duration-300 hover:bg-white/8"
                                        v-model="form.email"
                                        required
                                        autofocus
                                        autocomplete="username"
                                        readonly
                                    />
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="h-5 w-5 text-slate-400 group-focus-within:text-violet-400 transition-colors duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                        </svg>
                                    </div>
                                </div>
                                <InputError class="text-red-400 text-sm animate-slide-in" :message="form.errors.email" />
                            </div>

                            <!-- New Password Field -->
                            <div class="space-y-2">
                                <InputLabel for="password" value="New Password" class="text-slate-300 font-medium text-sm tracking-wide" />
                                <div class="relative group">
                                    <TextInput
                                        id="password"
                                        type="password"
                                        class="w-full px-4 py-3 bg-white/5 border border-white/20 rounded-xl text-white placeholder-slate-400 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20 focus:bg-white/10 transition-all duration-300 hover:bg-white/8"
                                        v-model="form.password"
                                        required
                                        autocomplete="new-password"
                                        placeholder="Enter your new password"
                                    />
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="h-5 w-5 text-slate-400 group-focus-within:text-cyan-400 transition-colors duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                    </div>
                                </div>
                                <InputError class="text-red-400 text-sm animate-slide-in" :message="form.errors.password" />
                            </div>

                            <!-- Confirm Password Field -->
                            <div class="space-y-2">
                                <InputLabel for="password_confirmation" value="Confirm New Password" class="text-slate-300 font-medium text-sm tracking-wide" />
                                <div class="relative group">
                                    <TextInput
                                        id="password_confirmation"
                                        type="password"
                                        class="w-full px-4 py-3 bg-white/5 border border-white/20 rounded-xl text-white placeholder-slate-400 focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 focus:bg-white/10 transition-all duration-300 hover:bg-white/8"
                                        v-model="form.password_confirmation"
                                        required
                                        autocomplete="new-password"
                                        placeholder="Confirm your new password"
                                    />
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="h-5 w-5 text-slate-400 group-focus-within:text-purple-400 transition-colors duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                </div>
                                <InputError class="text-red-400 text-sm animate-slide-in" :message="form.errors.password_confirmation" />
                            </div>

                            <!-- Security Notice -->
                            <div class="bg-white/5 border border-white/10 rounded-xl p-4 backdrop-blur-sm">
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-cyan-400 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.031 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm text-slate-300 font-medium">Security Tip</p>
                                        <p class="text-xs text-slate-400 mt-1">
                                            Choose a strong password with at least 8 characters, including uppercase, lowercase, numbers, and symbols.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="pt-3">
                                <PrimaryButton
                                    :class="{ 
                                        'opacity-50 cursor-not-allowed': form.processing,
                                        'hover:scale-105 hover:shadow-xl hover:shadow-violet-500/25': !form.processing
                                    }"
                                    :disabled="form.processing"
                                    class="group relative w-full flex justify-center py-4 px-6 border border-transparent text-sm font-semibold rounded-xl text-white bg-gradient-to-r from-violet-600 via-purple-600 to-cyan-600 hover:from-violet-500 hover:via-purple-500 hover:to-cyan-500 focus:outline-none focus:ring-2 focus:ring-violet-500/50 focus:ring-offset-2 focus:ring-offset-slate-900 disabled:opacity-50 transition-all duration-500 shadow-lg hover:shadow-2xl relative overflow-hidden"
                                >
                                    <!-- Button Gradient Overlay -->
                                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent transform -skew-x-12 -translate-x-full group-hover:translate-x-full transition-transform duration-1000 ease-out"></div>
                                    
                                    <!-- Loading Spinner -->
                                    <span v-if="form.processing" class="absolute left-0 inset-y-0 flex items-center pl-4">
                                        <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </span>
                                    
                                    <span class="relative flex items-center justify-center">
                                        <span class="text-base">{{ form.processing ? 'Resetting Password...' : 'Reset Password' }}</span>
                                        
                                        <svg v-if="!form.processing" class="ml-2 w-5 h-5 transform group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                                        </svg>
                                    </span>
                                </PrimaryButton>
                            </div>
                        </form>

                        <!-- Footer Info -->
                        <div class="mt-8 text-center">
                            <p class="text-sm text-slate-400">
                                Remember your password?
                                <a href="/login" class="font-semibold bg-gradient-to-r from-cyan-400 to-violet-400 bg-clip-text text-transparent hover:from-white hover:to-white transition-all duration-300 ml-1 hover:underline">
                                    Back to Login
                                </a>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Floating Decorative Elements -->
                <div class="absolute -top-8 -left-8 w-16 h-16 bg-gradient-to-br from-violet-500/20 to-cyan-500/20 rounded-full animate-bounce-gentle blur-sm"></div>
                <div class="absolute -bottom-8 -right-8 w-12 h-12 bg-gradient-to-br from-cyan-500/20 to-purple-500/20 rounded-full animate-bounce-gentle blur-sm" style="animation-delay: 2s;"></div>
                <div class="absolute top-1/3 -right-12 w-10 h-10 bg-gradient-to-br from-purple-500/20 to-violet-500/20 rounded-full animate-pulse-slow blur-sm"></div>
                <div class="absolute bottom-1/2 -left-12 w-8 h-8 bg-gradient-to-br from-cyan-400/20 to-violet-400/20 rounded-full animate-pulse-slow blur-sm" style="animation-delay: 3s;"></div>
            </div>
        </div>
    </GuestLayout>
</template>

<style scoped>
/* Enhanced Custom Animations */
@keyframes float-particle {
    0%, 100% {
        transform: translateY(0px) translateX(0px) rotate(0deg);
        opacity: 0.3;
    }
    25% {
        transform: translateY(-20px) translateX(15px) rotate(90deg);
        opacity: 0.8;
    }
    50% {
        transform: translateY(-10px) translateX(-8px) rotate(180deg);
        opacity: 1;
    }
    75% {
        transform: translateY(-30px) translateX(-20px) rotate(270deg);
        opacity: 0.5;
    }
}

.animate-float-particle {
    animation: float-particle 12s ease-in-out infinite;
}

@keyframes float-subtle {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    33% { transform: translateY(-2px) rotate(1deg); }
    66% { transform: translateY(2px) rotate(-1deg); }
}
.animate-float-subtle {
    animation: float-subtle 4s ease-in-out infinite;
}

@keyframes shimmer {
    0% { background-position: -200% 50%; }
    100% { background-position: 200% 50%; }
}
.animate-shimmer {
    animation: shimmer 4s ease-in-out infinite;
}

@keyframes fade-in-up {
    0% {
        opacity: 0;
        transform: translateY(40px) scale(0.95);
    }
    100% {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}
.animate-fade-in-up {
    animation: fade-in-up 0.8s ease-out;
}

@keyframes slide-in {
    0% {
        opacity: 0;
        transform: translateY(-15px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}
.animate-slide-in {
    animation: slide-in 0.4s ease-out;
}

@keyframes pulse-slow {
    0%, 100% { 
        opacity: 0.3; 
        transform: scale(1);
    }
    50% { 
        opacity: 0.8; 
        transform: scale(1.08);
    }
}
.animate-pulse-slow {
    animation: pulse-slow 5s ease-in-out infinite;
}

@keyframes pulse-glow {
    0%, 100% {
        box-shadow: 0 0 10px rgba(139, 92, 246, 0.3), 0 0 20px rgba(139, 92, 246, 0.1);
    }
    50% {
        box-shadow: 0 0 25px rgba(139, 92, 246, 0.6), 0 0 40px rgba(6, 182, 212, 0.3);
    }
}
.animate-pulse-glow {
    animation: pulse-glow 3s ease-in-out infinite;
}

@keyframes bounce-gentle {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-12px); }
}
.animate-bounce-gentle {
    animation: bounce-gentle 5s ease-in-out infinite;
}

@keyframes spin-slow {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
.animate-spin-slow {
    animation: spin-slow 25s linear infinite;
}

.bg-size-200 {
    background-size: 200% 200%;
}

/* Enhanced Focus States */
.focus\:border-violet-500:focus {
    border-color: rgb(139 92 246);
}
.focus\:border-cyan-500:focus {
    border-color: rgb(6 182 212);
}
.focus\:border-purple-500:focus {
    border-color: rgb(168 85 247);
}

.focus\:ring-violet-500\/20:focus {
    --tw-ring-color: rgb(139 92 246 / 0.2);
}
.focus\:ring-cyan-500\/20:focus {
    --tw-ring-color: rgb(6 182 212 / 0.2);
}
.focus\:ring-purple-500\/20:focus {
    --tw-ring-color: rgb(168 85 247 / 0.2);
}

/* Group Focus States */
.group:focus-within .group-focus-within\:text-violet-400 {
    color: rgb(139 92 246);
}
.group:focus-within .group-focus-within\:text-cyan-400 {
    color: rgb(6 182 212);
}
.group:focus-within .group-focus-within\:text-purple-400 {
    color: rgb(168 85 247);
}

/* Enhanced Shadow Effects */
.shadow-3xl {
    box-shadow: 0 35px 60px -12px rgba(0, 0, 0, 0.25), 0 0 0 1px rgba(255, 255, 255, 0.05);
}
</style>