<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import route from 'ziggy-js';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};

// Particle generation function
const getParticleStyle = (index) => {
    const positions = [
        'top: 10%; left: 15%;',
        'top: 25%; left: 85%;',
        'top: 40%; left: 10%;',
        'top: 55%; left: 90%;',
        'top: 70%; left: 20%;',
        'top: 85%; left: 80%;',
        'top: 15%; left: 60%;',
        'top: 30%; left: 40%;',
        'top: 45%; left: 70%;',
        'top: 60%; left: 30%;',
        'top: 75%; left: 65%;',
        'top: 90%; left: 45%;',
        'top: 5%; left: 25%;',
        'top: 20%; left: 75%;',
        'top: 35%; left: 55%;',
        'top: 50%; left: 15%;',
        'top: 65%; left: 85%;',
        'top: 80%; left: 35%;',
        'top: 95%; left: 60%;',
        'top: 12%; left: 95%;'
    ];
    return positions[index - 1] + `animation-duration: ${15 + (index % 10)}s; animation-delay: ${index * 0.5}s;`;
};

const getParticleClass = (index) => {
    const colors = ['bg-violet-500', 'bg-cyan-500', 'bg-purple-500', 'bg-emerald-500', 'bg-pink-500'];
    return colors[index % colors.length];
};
</script>

<template>
    <GuestLayout>
        <Head title="Forgot Password" />
        
        <!-- Animated Background -->
        <div class="fixed inset-0 bg-slate-900 overflow-hidden">
            <!-- Animated Mesh Background -->
            <svg class="absolute inset-0 w-full h-full opacity-30" viewBox="0 0 100 100" preserveAspectRatio="none">
                <defs>
                    <linearGradient id="meshGradient" x1="0%" y1="0%" x2="100%" y2="100%" gradientTransform="rotate(0)">
                        <stop offset="0%" stop-color="#8b5cf6" class="animate-gradient-shift">
                            <animate attributeName="stop-color" values="#8b5cf6;#06b6d4;#8b5cf6" dur="8s" repeatCount="indefinite" />
                        </stop>
                        <stop offset="50%" stop-color="#06b6d4" class="animate-gradient-shift">
                            <animate attributeName="stop-color" values="#06b6d4;#a855f7;#06b6d4" dur="8s" repeatCount="indefinite" />
                        </stop>
                        <stop offset="100%" stop-color="#a855f7" class="animate-gradient-shift">
                            <animate attributeName="stop-color" values="#a855f7;#8b5cf6;#a855f7" dur="8s" repeatCount="indefinite" />
                        </stop>
                    </linearGradient>
                </defs>
                <path d="M0,0 Q25,25 50,0 T100,0 L100,25 Q75,50 50,25 T0,25 Z" fill="url(#meshGradient)" opacity="0.1">
                    <animateTransform attributeName="transform" attributeType="XML" type="translate" values="0,0;10,-5;0,0" dur="6s" repeatCount="indefinite" />
                </path>
                <path d="M0,25 Q25,50 50,25 T100,25 L100,50 Q75,75 50,50 T0,50 Z" fill="url(#meshGradient)" opacity="0.15">
                    <animateTransform attributeName="transform" attributeType="XML" type="translate" values="0,0;-8,3;0,0" dur="7s" repeatCount="indefinite" />
                </path>
                <path d="M0,50 Q25,75 50,50 T100,50 L100,75 Q75,100 50,75 T0,75 Z" fill="url(#meshGradient)" opacity="0.1">
                    <animateTransform attributeName="transform" attributeType="XML" type="translate" values="0,0;5,8;0,0" dur="9s" repeatCount="indefinite" />
                </path>
            </svg>

            <!-- Floating Particles -->
            <div class="absolute inset-0">
                <div v-for="i in 20" :key="i" 
                     class="absolute w-2 h-2 rounded-full opacity-40 animate-float"
                     :class="getParticleClass(i)"
                     :style="getParticleStyle(i)">
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="relative z-10 min-h-screen flex items-center justify-center p-4">
            <div class="w-full max-w-md animate-fade-in-up">
                <!-- Glassmorphic Card -->
                <div class="glass-card rounded-2xl p-8 shadow-2xl shadow-violet-500/10 hover:shadow-violet-500/20 transition-all duration-500 relative overflow-hidden border border-white/20">
                    <!-- Gradient Border Effect -->
                    <div class="absolute inset-0 rounded-2xl bg-gradient-to-r from-violet-500/10 via-purple-500/10 to-cyan-500/10 pointer-events-none"></div>
                    
                    <div class="relative z-10">
                        <!-- Header with Shimmer Effect -->
                        <div class="text-center mb-8">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-violet-500 to-cyan-500 rounded-full mb-4 shadow-lg shadow-violet-500/25">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                                </svg>
                            </div>
                            
                            <h1 class="text-3xl font-bold gradient-text animate-shimmer mb-2">
                                Reset Password
                            </h1>
                            <div class="w-16 h-1 bg-gradient-to-r from-violet-500 to-cyan-500 mx-auto rounded-full"></div>
                        </div>

                        <!-- Description -->
                        <div class="glass-card-inner rounded-xl p-6 mb-8 border border-white/10">
                            <p class="text-slate-300 leading-relaxed text-center">
                                Forgot your password? No problem. Just let us know your email
                                address and we will email you a password reset link that will allow
                                you to choose a new one.
                            </p>
                        </div>

                        <!-- Status Message -->
                        <div
                            v-if="status"
                            class="mb-6 p-4 bg-gradient-to-r from-green-500/20 to-emerald-500/20 backdrop-blur-sm border border-green-400/30 rounded-xl text-sm font-medium text-green-300 text-center animate-slide-in flex items-center justify-center"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ status }}
                        </div>

                        <!-- Form -->
                        <form @submit.prevent="submit" class="space-y-6">
                            <div class="space-y-3">
                                <InputLabel 
                                    for="email" 
                                    value="Email Address" 
                                    class="enhanced-label"
                                />

                                <div class="relative group">
                                    <div class="absolute inset-0 bg-gradient-to-r from-violet-500/10 to-cyan-500/10 rounded-xl blur opacity-0 group-hover:opacity-100 transition-all duration-300"></div>
                                    <TextInput
                                        id="email"
                                        type="email"
                                        class="enhanced-input relative z-10"
                                        v-model="form.email"
                                        required
                                        autofocus
                                        autocomplete="username"
                                        placeholder="Enter your email address"
                                    />
                                    
                                    <!-- Email Icon -->
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                        <svg class="w-5 h-5 text-slate-400 group-focus-within:text-violet-400 transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                                        </svg>
                                    </div>
                                </div>

                                <InputError class="enhanced-error" :message="form.errors.email" />
                            </div>

                            <div class="pt-2">
                                <PrimaryButton
                                    :class="{ 
                                        'opacity-50 cursor-not-allowed': form.processing,
                                    }"
                                    :disabled="form.processing"
                                    class="enhanced-primary-button group w-full"
                                >
                                    <!-- Button Gradient Overlay -->
                                    <div class="absolute inset-0 bg-gradient-to-r from-cyan-600/0 via-cyan-600/30 to-cyan-600/0 transform -skew-x-12 -translate-x-full group-hover:translate-x-full transition-transform duration-1000 ease-out"></div>
                                    
                                    <span class="relative flex items-center justify-center">
                                        <svg v-if="form.processing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        
                                        <span v-if="!form.processing">Email Password Reset Link</span>
                                        <span v-else>Sending...</span>
                                        
                                        <svg v-if="!form.processing" class="ml-2 w-5 h-5 transform group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                        </svg>
                                    </span>
                                </PrimaryButton>
                            </div>
                        </form>

                        <!-- Back to Login Link -->
                        <div class="mt-8 text-center">
                            <Link :href="route('login')" 
                               class="premium-link group">
                                <svg class="w-4 h-4 mr-2 transform group-hover:-translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Back to Login
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Floating Elements -->
                <div class="absolute -top-4 -left-4 w-8 h-8 bg-violet-500/30 rounded-full animate-bounce-slow"></div>
                <div class="absolute -bottom-4 -right-4 w-6 h-6 bg-cyan-500/30 rounded-full animate-bounce-slow" style="animation-delay: 1s;"></div>
                <div class="absolute top-1/2 -right-8 w-4 h-4 bg-purple-500/30 rounded-full animate-pulse"></div>
            </div>
        </div>
    </GuestLayout>
</template>

<style scoped>
/* Glass morphism effects */
.glass-card {
    @apply bg-white/5 backdrop-blur-xl;
}

.glass-card-inner {
    @apply bg-white/5 backdrop-blur-sm border border-white/10;
}

/* Gradient text effects */
.gradient-text {
    @apply bg-clip-text text-transparent bg-gradient-to-r from-violet-400 via-purple-400 to-cyan-400;
}

/* Enhanced label styles */
.enhanced-label {
    @apply text-lg font-semibold text-slate-200;
}

/* Enhanced input styles */
.enhanced-input {
    @apply w-full pl-4 pr-12 py-4 bg-white/10 backdrop-blur-sm border border-white/20 
           rounded-xl text-slate-200 placeholder-slate-400 
           focus:border-violet-500/50 focus:ring-2 focus:ring-violet-500/25 
           transition-all duration-300 text-base;
}

/* Enhanced primary button */
.enhanced-primary-button {
    @apply relative inline-flex items-center justify-center px-8 py-4 
           bg-gradient-to-r from-violet-600 to-purple-600 
           border border-violet-500/50 rounded-xl font-bold text-sm text-white 
           uppercase tracking-wide hover:from-violet-500 hover:to-purple-500 
           hover:scale-105 hover:shadow-lg hover:shadow-violet-500/25 
           transition-all duration-300 backdrop-blur-sm overflow-hidden;
}

/* Premium links */
.premium-link {
    @apply inline-flex items-center text-slate-400 hover:text-cyan-400 
           font-medium text-sm hover:scale-105 transition-all duration-300;
}

/* Enhanced error styles */
.enhanced-error {
    @apply text-red-400 text-sm font-medium;
}

/* Shimmer animation */
.animate-shimmer {
    background-size: 200% 200%;
    animation: shimmer 3s ease-in-out infinite;
}

@keyframes shimmer {
    0%, 100% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
}

/* Fade in up animation */
.animate-fade-in-up {
    opacity: 0;
    transform: translateY(30px);
    animation: fadeInUp 0.7s ease-out forwards;
}

@keyframes fadeInUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Slide in animation */
.animate-slide-in {
    opacity: 0;
    transform: translateY(-10px);
    animation: slideIn 0.5s ease-out forwards;
}

@keyframes slideIn {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Float animation */
.animate-float {
    animation: float 20s linear infinite;
}

@keyframes float {
    0%, 100% { 
        transform: translateY(0px) rotate(0deg); 
        opacity: 0.4;
    }
    25% { 
        transform: translateY(-20px) rotate(90deg); 
        opacity: 0.6;
    }
    50% { 
        transform: translateY(-10px) rotate(180deg); 
        opacity: 0.3;
    }
    75% { 
        transform: translateY(-30px) rotate(270deg); 
        opacity: 0.5;
    }
}

/* Bounce slow animation */
.animate-bounce-slow {
    animation: bounceSlow 3s ease-in-out infinite;
}

@keyframes bounceSlow {
    0%, 100% { 
        transform: translateY(0); 
    }
    50% { 
        transform: translateY(-10px); 
    }
}

/* Gradient shift for mesh */
.animate-gradient-shift {
    animation: gradientShift 8s ease-in-out infinite;
}

@keyframes gradientShift {
    0%, 100% {
        stop-color: #8b5cf6;
    }
    33% {
        stop-color: #06b6d4;
    }
    66% {
        stop-color: #a855f7;
    }
}

/* Enhanced focus states */
.enhanced-input:focus {
    outline: none;
    transform: translateY(-2px);
    box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1), 0 10px 25px rgba(139, 92, 246, 0.15);
}

/* Input hover effects */
.enhanced-input:hover {
    border-color: rgba(139, 92, 246, 0.3);
    transform: translateY(-1px);
}

/* Button hover glow effects */
.enhanced-primary-button:hover:not(:disabled) {
    box-shadow: 0 10px 25px rgba(139, 92, 246, 0.3), 0 0 20px rgba(139, 92, 246, 0.2);
}

/* Loading animation */
.animate-spin {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

/* Smooth transitions for all elements */
* {
    transition-property: all;
    transition-duration: 300ms;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}

/* Responsive improvements */
@media (max-width: 640px) {
    .enhanced-input {
        @apply py-3 text-sm;
    }
    
    .enhanced-primary-button {
        @apply px-6 py-3 text-xs;
    }
}
</style>