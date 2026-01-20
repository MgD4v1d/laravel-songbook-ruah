<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});

function handleImageError() {
    document.getElementById('screenshot-container')?.classList.add('!hidden');
    document.getElementById('docs-card')?.classList.add('!row-span-1');
    document.getElementById('docs-card-content')?.classList.add('!flex-row');
    document.getElementById('background')?.classList.add('!hidden');
}
</script>

<template>
    <Head title="Welcome" />
    <div class="bg-gray-50 text-black/50 dark:bg-black dark:text-white/50 min-h-screen relative">
        <div
            id="background"
            class="absolute inset-0 bg-repeat"
            style="background-image: url('https://tehila.com.mx/images/endless-constellation.svg');"
        >
        </div>

        <div
            class="relative flex min-h-screen flex-col items-center justify-center selection:bg-[#debb75] selection:text-white"
        >
            <div class="relative w-full max-w-2xl px-2 lg:max-w-7xl">
                <header
                    class="grid grid-cols-2 items-center gap-2 lg:grid-cols-3"
                >
                    <div class="flex lg:col-start-2 lg:justify-center">
                        <svg
                            class="text-white lg:text-[#debb75]" 
                            xmlns="http://www.w3.org/2000/svg" 
                            viewBox="0 0 1920 1080"
                            >
                            <path d="M69.08,471.08H7.38l183.76-201.2v99.93h101.27v101.27h-101.27v87.86c0,36.89,27.5,50.3,55.67,50.3,17.44,0,34.2-5.37,45.61-12.74v107.31c-26.83,13.41-54.32,19.45-80.48,19.45-78.47,0-142.85-56.34-142.85-147.55v-104.62Z"/>
                            <path d="M684.75,584.42h-237.42c15.43,36.89,50.97,40.24,75.79,40.24s46.28-8.05,63.04-24.14l83.16,58.35c-34.2,43.59-87.19,64.38-148.89,64.38-119.38,0-193.82-79.81-193.82-182.42s76.46-181.08,184.43-181.08,178.4,77.8,178.4,181.75c0,14.75-1.34,28.84-4.69,42.92ZM577.44,505.28c-10.73-36.22-37.56-48.96-65.05-48.96s-56.34,14.75-67.74,48.96h132.79Z"/>
                            <path d="M1100.56,499.92v213.27h-123.4v-185.78c0-38.9-20.12-57.68-53.65-57.68-40.91,0-58.35,30.18-58.35,67.74v175.72h-123.4V241.04h123.4v157.61c20.79-23.47,54.32-38.9,100.6-38.9,88.53,0,134.8,57.01,134.8,140.17Z"/>
                            <path fill="currentColor" d="M1148.85,259.82c0-46.28,38.23-79.14,79.14-79.14,42.92,0,79.81,36.22,79.81,79.14s-36.22,79.14-79.81,79.14-79.14-33.53-79.14-79.14ZM1166.96,713.19v-343.38h54.32v343.38h-54.32ZM1276.27,259.82c0-26.16-22.13-48.96-48.29-48.96s-47.62,22.13-47.62,48.96,20.79,48.96,47.62,48.96,48.29-22.8,48.29-48.96ZM1193.78,259.82c0-20.12,15.43-36.89,34.2-36.89s34.88,17.44,34.88,36.89-16.77,36.89-34.88,36.89c-19.45,0-34.2-16.1-34.2-36.89ZM1236.03,713.19v-343.38h54.32v343.38h-54.32Z"/>
                            <path fill="currentColor" d="M1360.11,713.19V241.04h54.32v472.15h-54.32ZM1429.18,713.19V241.04h54.32v472.15h-54.32Z"/>
                            <path fill="currentColor" d="M1800.73,396.63l50.3-18.78v335.34h-51.64v-25.49c-26.83,25.49-63.71,35.54-93.22,35.54-101.94,0-169.68-84.5-169.68-179.07,0-99.93,70.42-184.43,173.03-184.43,27.5,0,64.38,9.39,91.21,36.89ZM1799.39,644.11v-15.42c-22.13,18.78-45.61,32.19-83.16,32.19-65.73,0-112-50.3-112-122.06,0-67.74,45.61-117.37,108.65-117.37,35.54,0,63.71,10.73,86.52,32.86v-16.1c-23.47-20.12-51.64-30.18-86.52-30.18-71.76,0-123.4,55-123.4,130.78,0,79.81,52.31,135.48,126.76,135.48,31.52,0,54.99-8.72,80.48-28.17l2.68-2.01ZM1802.74,541.5c0-40.24-31.52-71.09-72.43-71.09s-73.1,30.85-73.1,71.09,31.52,71.09,73.1,71.09,72.43-30.85,72.43-71.09ZM1865.79,372.49l50.97-19.45v360.15h-50.97v-340.7Z"/>
                        </svg>
                    </div>
                    
                </header>



                <footer
                    class="text-center text-sm text-black dark:text-white/70"
                >
                    Copyright © {{ new Date().getFullYear() }} Tehila. All rights reserved.
                </footer>
            </div>
        </div>
    </div>
</template>
