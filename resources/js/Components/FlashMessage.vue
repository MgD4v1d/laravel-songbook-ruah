<script setup>
import { ref, watch, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const show = ref(false);
const message = ref('');
const type = ref('success');
const lastMessage = ref('');

const showNotification = () => {
    const flash = page.props.flash;
    let currentMessage = '';

    if(flash.success){
       currentMessage = flash.success;
        type.value = 'success';
    } else if (flash.error){
        currentMessage = flash.error;
        type.value = 'error';;
    } else if (flash.info){
        currentMessage = flash.info;
        type.value = 'info';
    }

    if(currentMessage && currentMessage !== lastMessage.value){
        message.value = currentMessage;
        lastMessage.value = currentMessage;
        show.value = true;
        autoHide();
    }
};



const autoHide = () => {
    setTimeout(() => {
        show.value = false;
    }, 5000);
};

const close = () => {
    show.value = false;
};

// Detectar cambios en flash message - optimizado para evitar disparos innecesarios
watch(
    () => [page.props.flash?.success, page.props.flash?.error, page.props.flash?.info],
    () => {
        showNotification();
    },
    { immediate: false }
);


// Mostrar al montar si hay mensaje
onMounted(() => {
    showNotification();
});

</script>

<template>
    <transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="transform translate-x-full opacity-0"
        enter-to-class="transform translate-x-0 opacity-100"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="transform translate-x-0 opacity-100"
        leave-to-class="transform translate-x-full opacity-0"
    >
        <div v-if="show" class="toast toast-end toast-top z-50">
            <div 
                :class="[
                    'alert shadow-lg',
                    type === 'success' ? 'alert-success' : '',
                    type === 'error' ? 'alert-error' : '',
                    type === 'warning' ? 'alert-warning' : '',
                    type === 'info' ? 'alert-info' : ''
                ]"
            >
                <div class="flex items-start gap-3">
                    <!-- Iconos según tipo -->
                    <svg 
                        v-if="type === 'success'"
                        xmlns="http://www.w3.org/2000/svg" 
                        class="stroke-current shrink-0 h-6 w-6" 
                        fill="none" 
                        viewBox="0 0 24 24"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>

                    <svg 
                        v-else-if="type === 'error'"
                        xmlns="http://www.w3.org/2000/svg" 
                        class="stroke-current shrink-0 h-6 w-6" 
                        fill="none" 
                        viewBox="0 0 24 24"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>

                    <svg 
                        v-else-if="type === 'warning'"
                        xmlns="http://www.w3.org/2000/svg" 
                        class="stroke-current shrink-0 h-6 w-6" 
                        fill="none" 
                        viewBox="0 0 24 24"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>

                    <svg 
                        v-else
                        xmlns="http://www.w3.org/2000/svg" 
                        fill="none" 
                        viewBox="0 0 24 24" 
                        class="stroke-current shrink-0 w-6 h-6"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>

                    <span class="text-sm font-medium">{{ message }}</span>
                </div>
                <button @click="close" class="btn btn-ghost btn-sm btn-circle">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </transition>
</template>