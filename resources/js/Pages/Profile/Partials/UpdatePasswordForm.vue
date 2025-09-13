<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value.focus();
            }
            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value.focus();
            }
        },
    });
};
</script>

<template>
    <section class="relative">
        <!-- Security Background Effect -->
        <div class="absolute inset-0 bg-gradient-to-br from-cyan-500/5 to-blue-500/5 rounded-2xl blur-xl"></div>
        
        <div class="relative">
            <header class="mb-8">
                <h2 class="text-2xl font-bold gradient-text flex items-center animate-shimmer">
                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                    </svg>
                    Update Password
                </h2>

                <div class="mt-4 p-4 glass-card-inner rounded-xl border border-cyan-500/10">
                    <p class="text-slate-300 leading-relaxed flex items-center">
                        <svg class="w-4 h-4 mr-2 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        Ensure your account is using a long, random password to stay secure.
                    </p>
                </div>
            </header>

            <form @submit.prevent="updatePassword" class="space-y-8 animate-fade-in-up">
                <!-- Current Password Field -->
                <div class="space-y-3">
                    <InputLabel 
                        for="current_password" 
                        value="Current Password" 
                        class="enhanced-label"
                    />

                    <div class="relative group">
                        <div class="absolute inset-0 bg-gradient-to-r from-slate-500/10 to-cyan-500/10 rounded-xl blur opacity-0 group-hover:opacity-100 transition-all duration-300"></div>
                        <TextInput
                            id="current_password"
                            ref="currentPasswordInput"
                            v-model="form.current_password"
                            type="password"
                            class="enhanced-input relative z-10"
                            autocomplete="current-password"
                            placeholder="Enter your current password"
                        />
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-slate-400 group-focus-within:text-cyan-400 transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                    </div>

                    <InputError
                        :message="form.errors.current_password"
                        class="enhanced-error"
                    />
                </div>

                <!-- New Password Field -->
                <div class="space-y-3">
                    <InputLabel 
                        for="password" 
                        value="New Password" 
                        class="enhanced-label"
                    />

                    <div class="relative group">
                        <div class="absolute inset-0 bg-gradient-to-r from-violet-500/10 to-purple-500/10 rounded-xl blur opacity-0 group-hover:opacity-100 transition-all duration-300"></div>
                        <TextInput
                            id="password"
                            ref="passwordInput"
                            v-model="form.password"
                            type="password"
                            class="enhanced-input relative z-10"
                            autocomplete="new-password"
                            placeholder="Enter your new password"
                        />
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-slate-400 group-focus-within:text-violet-400 transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                            </svg>
                        </div>
                    </div>

                    <InputError :message="form.errors.password" class="enhanced-error" />
                </div>

                <!-- Confirm Password Field -->
                <div class="space-y-3">
                    <InputLabel
                        for="password_confirmation"
                        value="Confirm Password"
                        class="enhanced-label"
                    />

                    <div class="relative group">
                        <div class="absolute inset-0 bg-gradient-to-r from-emerald-500/10 to-cyan-500/10 rounded-xl blur opacity-0 group-hover:opacity-100 transition-all duration-300"></div>
                        <TextInput
                            id="password_confirmation"
                            v-model="form.password_confirmation"
                            type="password"
                            class="enhanced-input relative z-10"
                            autocomplete="new-password"
                            placeholder="Confirm your new password"
                        />
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-slate-400 group-focus-within:text-emerald-400 transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    </div>

                    <InputError
                        :message="form.errors.password_confirmation"
                        class="enhanced-error"
                    />
                </div>

                <!-- Action Area -->
                <div class="glass-card-inner rounded-xl p-6 border border-white/10">
                    <div class="flex items-center justify-between">
                        <PrimaryButton 
                            :disabled="form.processing" 
                            class="enhanced-primary-button group"
                            :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                        >
                            <svg v-if="form.processing" class="w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <svg v-else class="w-4 h-4 mr-2 group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ form.processing ? 'Updating...' : 'Save Password' }}
                        </PrimaryButton>

                        <Transition
                            enter-active-class="transition ease-in-out duration-500"
                            enter-from-class="opacity-0 transform translate-x-4"
                            enter-to-class="opacity-100 transform translate-x-0"
                            leave-active-class="transition ease-in-out duration-500"
                            leave-from-class="opacity-100 transform translate-x-0"
                            leave-to-class="opacity-0 transform translate-x-4"
                        >
                            <div
                                v-if="form.recentlySuccessful"
                                class="success-message"
                            >
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="font-medium">Password updated successfully!</span>
                            </div>
                        </Transition>
                    </div>
                </div>
            </form>
        </div>
    </section>
</template>

<style scoped>
/* Glass morphism effects */
.glass-card-inner {
    @apply bg-white/5 backdrop-blur-sm border border-white/10;
}

/* Gradient text effects */
.gradient-text {
    @apply bg-clip-text text-transparent bg-gradient-to-r from-cyan-400 via-blue-400 to-violet-400;
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

/* Enhanced label styles */
.enhanced-label {
    @apply text-lg font-semibold text-slate-200 flex items-center;
}

/* Enhanced input styles */
.enhanced-input {
    @apply w-full pl-12 pr-4 py-4 bg-white/10 backdrop-blur-sm border border-white/20 
           rounded-xl text-slate-200 placeholder-slate-400 
           focus:border-violet-500/50 focus:ring-2 focus:ring-violet-500/25 
           transition-all duration-300 text-base;
}

/* Enhanced primary button */
.enhanced-primary-button {
    @apply inline-flex items-center px-8 py-4 bg-gradient-to-r from-violet-600 to-purple-600 
           border border-violet-500/50 rounded-xl font-bold text-sm text-white 
           uppercase tracking-wide hover:from-violet-500 hover:to-purple-500 
           hover:scale-105 hover:shadow-lg hover:shadow-violet-500/25 
           transition-all duration-300 backdrop-blur-sm;
}

/* Enhanced error styles */
.enhanced-error {
    @apply text-red-400 text-sm font-medium flex items-center;
}

.enhanced-error:before {
    content: "⚠️";
    margin-right: 0.5rem;
}

/* Success message styles */
.success-message {
    @apply flex items-center px-6 py-3 bg-gradient-to-r from-emerald-500/20 to-green-500/20 
           backdrop-blur-sm border border-emerald-500/30 rounded-xl text-emerald-300 
           font-medium text-sm shadow-lg shadow-emerald-500/10;
}

/* Fade in up animation */
.animate-fade-in-up {
    opacity: 0;
    transform: translateY(20px);
    animation: fadeInUp 0.6s ease-out forwards;
}

@keyframes fadeInUp {
    to {
        opacity: 1;
        transform: translateY(0);
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

/* Icon animations */
.group:hover svg {
    transform: scale(1.05);
    transition: transform 0.3s ease;
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
        @apply pl-12 py-3 text-sm;
    }
    
    .enhanced-primary-button {
        @apply px-6 py-3 text-xs;
    }
    
    .success-message {
        @apply px-4 py-2 text-xs;
    }
}
</style>