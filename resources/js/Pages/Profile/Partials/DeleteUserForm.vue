<script setup>
import DangerButton from '@/Components/DangerButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;
    
    nextTick(() => passwordInput.value.focus());
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;
    
    form.clearErrors();
    form.reset();
};
</script>

<template>
    <section class="space-y-8 relative">
        <!-- Warning Background Effect -->
        <div class="absolute inset-0 bg-gradient-to-br from-red-500/5 to-orange-500/5 rounded-2xl blur-xl"></div>
        
        <div class="relative glass-card-danger rounded-2xl p-8 border border-red-500/20 shadow-2xl shadow-red-500/10 hover:shadow-red-500/20 transition-all duration-500">
            <header class="mb-8">
                <h2 class="text-2xl font-bold text-red-400 flex items-center animate-pulse-glow">
                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    Delete Account
                </h2>

                <div class="mt-4 p-4 glass-card-inner rounded-xl border border-red-500/10">
                    <p class="text-slate-300 leading-relaxed">
                        Once your account is deleted, all of its resources and data will
                        be permanently deleted. Before deleting your account, please
                        download any data or information that you wish to retain.
                    </p>
                </div>
            </header>

            <div class="flex justify-center">
                <DangerButton 
                    @click="confirmUserDeletion" 
                    class="enhanced-danger-button group"
                >
                    <svg class="w-5 h-5 mr-2 group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Delete Account
                </DangerButton>
            </div>
        </div>

        <Modal :show="confirmingUserDeletion" @close="closeModal" class="enhanced-modal">
            <div class="relative overflow-hidden">
                <!-- Modal Background Effects -->
                <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900"></div>
                <div class="absolute inset-0 bg-gradient-to-br from-red-500/5 to-orange-500/5"></div>
                
                <!-- Animated Warning Particles -->
                <div class="absolute inset-0 overflow-hidden">
                    <div v-for="i in 8" :key="i" 
                         class="absolute w-1 h-1 bg-red-500 rounded-full opacity-30 animate-float-warning"
                         :style="getWarningParticleStyle(i)">
                    </div>
                </div>
                
                <div class="relative p-8 glass-card-inner border border-red-500/20">
                    <div class="text-center mb-8">
                        <div class="mx-auto w-16 h-16 bg-gradient-to-br from-red-500 to-orange-500 rounded-full flex items-center justify-center mb-4 animate-pulse-scale">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        
                        <h2 class="text-2xl font-bold text-red-400 mb-4 animate-pulse-glow">
                            Are you sure you want to delete your account?
                        </h2>
                    </div>

                    <div class="glass-card-inner rounded-xl p-6 mb-8 border border-red-500/10">
                        <p class="text-slate-300 text-center leading-relaxed">
                            Once your account is deleted, all of its resources and data
                            will be permanently deleted. Please enter your password to
                            confirm you would like to permanently delete your account.
                        </p>
                    </div>

                    <div class="mb-8">
                        <InputLabel
                            for="password"
                            value="Password"
                            class="sr-only"
                        />

                        <div class="relative">
                            <TextInput
                                id="password"
                                ref="passwordInput"
                                v-model="form.password"
                                type="password"
                                class="enhanced-input w-3/4 mx-auto block"
                                placeholder="Enter your password to confirm"
                                @keyup.enter="deleteUser"
                            />
                            <div class="absolute inset-0 rounded-lg bg-gradient-to-r from-red-500/10 to-orange-500/10 pointer-events-none"></div>
                        </div>

                        <InputError :message="form.errors.password" class="mt-3 text-center" />
                    </div>

                    <div class="flex justify-center space-x-4">
                        <SecondaryButton 
                            @click="closeModal"
                            class="enhanced-secondary-button group"
                        >
                            <svg class="w-4 h-4 mr-2 group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Cancel
                        </SecondaryButton>

                        <DangerButton
                            class="enhanced-danger-button group"
                            :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                            :disabled="form.processing"
                            @click="deleteUser"
                        >
                            <svg v-if="form.processing" class="w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <svg v-else class="w-4 h-4 mr-2 group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            {{ form.processing ? 'Deleting...' : 'Delete Account' }}
                        </DangerButton>
                    </div>
                </div>
            </div>
        </Modal>
    </section>
</template>

<script>
export default {
    methods: {
        getWarningParticleStyle(index) {
            const positions = [
                'top: 10%; left: 15%;',
                'top: 20%; left: 85%;',
                'top: 40%; left: 10%;',
                'top: 60%; left: 90%;',
                'top: 80%; left: 20%;',
                'top: 30%; left: 80%;',
                'top: 70%; left: 15%;',
                'top: 50%; left: 85%;'
            ];
            return positions[index - 1] + `animation-duration: ${2 + (index % 3)}s; animation-delay: ${index * 0.3}s;`;
        }
    }
}
</script>

<style scoped>
/* Glass morphism effects - Danger themed */
.glass-card-danger {
    @apply bg-red-950/20 backdrop-blur-xl;
}

.glass-card-inner {
    @apply bg-white/5 backdrop-blur-sm border border-white/10;
}

/* Enhanced modal styles */
.enhanced-modal {
    @apply backdrop-blur-sm;
}

/* Enhanced button styles */
.enhanced-danger-button {
    @apply inline-flex items-center px-8 py-4 bg-gradient-to-r from-red-600 to-red-700
           border border-red-500/50 rounded-xl font-bold text-sm text-white 
           uppercase tracking-wide hover:from-red-500 hover:to-red-600 
           hover:scale-105 hover:shadow-lg hover:shadow-red-500/25 
           transition-all duration-300 backdrop-blur-sm;
}

.enhanced-secondary-button {
    @apply inline-flex items-center px-8 py-4 bg-white/10 backdrop-blur-sm 
           border border-white/20 rounded-xl font-bold text-sm text-slate-200
           hover:bg-white/20 hover:border-white/30 hover:text-white hover:scale-105
           transition-all duration-300;
}

/* Enhanced input styles */
.enhanced-input {
    @apply relative bg-white/10 backdrop-blur-sm border border-white/20 rounded-xl
           text-slate-200 placeholder-slate-400 focus:border-red-500/50 focus:ring-2 
           focus:ring-red-500/25 transition-all duration-300 px-4 py-3;
}

/* Pulse glow animation for warnings */
.animate-pulse-glow {
    animation: pulseGlow 2s ease-in-out infinite;
}

@keyframes pulseGlow {
    0%, 100% {
        text-shadow: 0 0 10px rgba(248, 113, 113, 0.3);
    }
    50% {
        text-shadow: 0 0 20px rgba(248, 113, 113, 0.6), 0 0 30px rgba(248, 113, 113, 0.4);
    }
}

/* Pulse scale animation */
.animate-pulse-scale {
    animation: pulseScale 2s ease-in-out infinite;
}

@keyframes pulseScale {
    0%, 100% {
        transform: scale(1);
        box-shadow: 0 0 20px rgba(248, 113, 113, 0.3);
    }
    50% {
        transform: scale(1.05);
        box-shadow: 0 0 30px rgba(248, 113, 113, 0.5), 0 0 40px rgba(248, 113, 113, 0.3);
    }
}

/* Warning particles floating animation */
.animate-float-warning {
    animation: floatWarning 4s ease-in-out infinite;
}

@keyframes floatWarning {
    0%, 100% {
        transform: translateY(0px) translateX(0px) rotate(0deg);
        opacity: 0.3;
    }
    25% {
        transform: translateY(-15px) translateX(5px) rotate(90deg);
        opacity: 0.6;
    }
    50% {
        transform: translateY(-30px) translateX(-5px) rotate(180deg);
        opacity: 0.4;
    }
    75% {
        transform: translateY(-15px) translateX(5px) rotate(270deg);
        opacity: 0.5;
    }
}

/* Spin animation for loading state */
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

/* Enhanced focus states */
.enhanced-input:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(248, 113, 113, 0.1), 0 0 20px rgba(248, 113, 113, 0.2);
}

/* Button hover glow effects */
.enhanced-danger-button:hover {
    box-shadow: 0 10px 25px rgba(248, 113, 113, 0.3), 0 0 20px rgba(248, 113, 113, 0.2);
}

.enhanced-secondary-button:hover {
    box-shadow: 0 10px 25px rgba(255, 255, 255, 0.1);
}

/* Smooth transitions for all interactive elements */
* {
    transition-property: all;
    transition-duration: 300ms;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}
</style>