

<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    password: '',
});

const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Confirm Password" />

        <!-- Animated Background -->
        <div class="fixed inset-0 overflow-hidden pointer-events-none">
            <!-- Mesh Background -->
            <svg class="absolute inset-0 w-full h-full opacity-20" viewBox="0 0 100 100" preserveAspectRatio="none">
                <defs>
                    <linearGradient id="meshGradient" x1="0%" y1="0%" x2="100%" y2="100%" gradientTransform="rotate(45)">
                        <stop offset="0%" class="animate-pulse" style="stop-color:#8B5CF6;stop-opacity:0.3">
                            <animate attributeName="stop-color" values="#8B5CF6;#06B6D4;#A855F7;#8B5CF6" dur="8s" repeatCount="indefinite"/>
                        </stop>
                        <stop offset="100%" style="stop-color:#06B6D4;stop-opacity:0.2">
                            <animate attributeName="stop-color" values="#06B6D4;#A855F7;#8B5CF6;#06B6D4" dur="8s" repeatCount="indefinite"/>
                        </stop>
                    </linearGradient>
                </defs>
                <rect width="100" height="100" fill="url(#meshGradient)" opacity="0.1">
                    <animateTransform attributeName="transform" type="translate" values="0,0;10,5;0,0" dur="15s" repeatCount="indefinite"/>
                </rect>
            </svg>

            <!-- Floating Particles -->
            <div class="absolute inset-0">
                <div v-for="i in 20" :key="i" 
                     :class="`absolute w-1 h-1 bg-gradient-to-r from-violet-500 to-cyan-500 rounded-full opacity-30 animate-pulse`"
                     :style="{
                       left: Math.random() * 100 + '%',
                       top: Math.random() * 100 + '%',
                       animationDelay: Math.random() * 5 + 's',
                       animationDuration: (3 + Math.random() * 4) + 's'
                     }">
                    <div class="absolute inset-0 animate-ping bg-gradient-to-r from-purple-500 to-cyan-500 rounded-full"></div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="relative z-10 flex min-h-screen items-center justify-center px-4 py-12 sm:px-6 lg:px-8">
            <div class="w-full max-w-md space-y-8">
                
                <!-- Header Section -->
                <div class="text-center animate-fade-in-up">
                    <div class="relative inline-block mb-6">
                        <div class="w-16 h-16 mx-auto bg-gradient-to-br from-violet-500/20 to-cyan-500/20 rounded-2xl backdrop-blur-sm border border-violet-500/30 flex items-center justify-center group">
                            <svg class="w-8 h-8 text-violet-400 group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <div class="absolute inset-0 animate-ping opacity-25">
                            <div class="w-16 h-16 mx-auto bg-gradient-to-br from-violet-500/20 to-cyan-500/20 rounded-2xl"></div>
                        </div>
                    </div>
                    
                    <h2 class="text-3xl font-bold bg-gradient-to-r from-violet-400 via-purple-400 to-cyan-400 bg-clip-text text-transparent mb-2">
                        <span class="inline-block animate-shimmer bg-gradient-to-r from-violet-400 via-white via-purple-400 to-cyan-400 bg-[length:400%_100%] bg-clip-text text-transparent">
                            Confirm Password
                        </span>
                    </h2>
                </div>

                <!-- Security Notice -->
                <div class="backdrop-blur-xl bg-white/5 border border-white/10 rounded-2xl p-6 shadow-2xl animate-fade-in-up" style="animation-delay: 0.1s;">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-gradient-to-br from-amber-500/20 to-orange-500/20 rounded-lg backdrop-blur-sm border border-amber-500/30 flex items-center justify-center">
                                <svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-slate-300 leading-relaxed">
                                This is a secure area of the application. Please confirm your password before continuing.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Form Container -->
                <div class="backdrop-blur-xl bg-white/5 border border-white/10 rounded-2xl overflow-hidden shadow-2xl hover:shadow-violet-500/10 transition-all duration-500 hover:border-violet-500/30 animate-fade-in-up" style="animation-delay: 0.2s;">
                    <form @submit.prevent="submit" class="p-8 space-y-6">
                        
                        <!-- Password Field -->
                        <div class="group animate-fade-in-up" style="animation-delay: 0.3s;">
                            <label for="password" class="block text-sm font-medium text-slate-300 mb-3 transition-colors group-focus-within:text-violet-400">
                                Password *
                            </label>
                            <div class="relative">
                                <input
                                    id="password"
                                    type="password"
                                    v-model="form.password"
                                    required
                                    autocomplete="current-password"
                                    autofocus
                                    class="w-full px-4 py-4 bg-slate-800/50 border border-slate-600/50 rounded-xl text-white placeholder-slate-400 
                                           focus:border-violet-500 focus:ring-2 focus:ring-violet-500/20 focus:bg-slate-800/80 
                                           transition-all duration-300 backdrop-blur-sm hover:border-slate-500 text-lg"
                                    placeholder="Enter your password..."
                                />
                                <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-violet-500/0 to-cyan-500/0 group-focus-within:from-violet-500/10 group-focus-within:to-cyan-500/10 transition-all duration-300 pointer-events-none"></div>
                                
                                <!-- Password Icon -->
                                <div class="absolute inset-y-0 right-0 flex items-center pr-4">
                                    <svg class="w-5 h-5 text-slate-400 group-focus-within:text-violet-400 transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </div>
                            </div>
                            <div v-if="form.errors.password" class="mt-3 text-sm text-red-400 animate-shake flex items-center space-x-2">
                                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                <span>{{ form.errors.password }}</span>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end pt-6 border-t border-slate-600/30 animate-fade-in-up" style="animation-delay: 0.4s;">
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="relative inline-flex items-center px-8 py-4 bg-gradient-to-r from-violet-600 to-cyan-600 
                                       hover:from-violet-500 hover:to-cyan-500 disabled:from-slate-600 disabled:to-slate-700
                                       border border-transparent rounded-xl font-semibold text-sm text-white uppercase tracking-wider
                                       transition-all duration-300 transform hover:scale-105 hover:shadow-2xl hover:shadow-violet-500/25
                                       disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none disabled:shadow-none
                                       overflow-hidden group min-w-[120px] justify-center"
                                :class="{ 'opacity-25': form.processing }"
                            >
                                <div v-if="!form.processing" class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 group-hover:rotate-12 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Confirm
                                </div>
                                <div v-else class="flex items-center">
                                    <div class="w-4 h-4 mr-2 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                                    Confirming...
                                </div>
                                <div class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/20 to-white/0 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-700"></div>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Security Footer -->
                <div class="text-center animate-fade-in-up" style="animation-delay: 0.5s;">
                    <div class="flex items-center justify-center space-x-2 text-xs text-slate-500">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                        </svg>
                        <span>Your information is secure and encrypted</span>
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>

<style scoped>
@keyframes shimmer {
    0% { background-position: -400% 0; }
    100% { background-position: 400% 0; }
}

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

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
}

.animate-shimmer {
    animation: shimmer 3s ease-in-out infinite;
}

.animate-fade-in-up {
    animation: fade-in-up 0.8s ease-out forwards;
    opacity: 0;
}

.animate-shake {
    animation: shake 0.5s ease-in-out;
}
</style>