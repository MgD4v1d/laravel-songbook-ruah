<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';


const props = defineProps({
    stats: Object
});
const stats = ref(props.stats);

const apiStatus = ref({
    status: 'checking',
    message: 'Verificando...',
    song_count: 0,
    version: ''
});

// consulta el estado real de la api

const checkApi = async () => {
    try{
        const resp = await fetch('https://tehila.com.mx/api/health');
        if(!resp.ok) throw new Error('Error en la respuesta');
        const data = await resp.json();

        apiStatus.value = {
            status: data.status === 'OK' ? 'ok' : 'error',
            message: data.message,
            song_count: data.song_count,
            version: data.laravel_version
        }
    } catch (e){
        apiStatus.value = {
            status:'error',
            message: data.message,
            song_count: 0,
            version: ''
        }
    }
}

onMounted(()=> {
    // Actualiza stats normales cada 15s
    setInterval(()=>{
        router.reload({ 
            only: ['stats'],
            preserveScroll: true,
            preserveState: true
        })
    }, 15000);

    // Verifica estado API cada 5s
    checkApi()
    setInterval(checkApi, 150000)
});

</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
             <h2 class="font-semibold text-xl text-gray-800">Dashboard</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

                <!--Alert con DaisyUI-->

                <div class="alert alert-info shadow-lg mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <h3 class="font-bold">🎵 Bienvenido al Panel de Cancionero</h3>
                        <div class="text-xs">Gestiona tu colección de canciones de manera profesional</div>
                    </div>
                </div>

                <!--Stats con DaisyUI-->

                <div class="stats stats-vertical lg:stats-horizontal shadow w-full mb-6">
                    <div class="stat">
                        <div class="stat-figure text-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-8 h-8 stroke-current">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
                            </svg>
                        </div>
                        <div class="stat-title">Total Canciones</div>
                        <div class="stat-value text-primary">{{ stats.total_songs }}</div>
                        <div class="stat-desc">En tu biblioteca</div>
                    </div>

                    <div class="stat">
                        <div class="stat-figure text-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-8 h-8 stroke-current">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                            </svg>
                        </div>
                        <div class="stat-title">Añadidas Hoy</div>
                        <div class="stat-value text-secondary">{{ stats.today_songs }}</div>
                        <div class="stat-desc">Nuevas entradas</div>
                    </div>

                    <div class="stat">
                        <div class="stat-figure text-success">
                             <svg v-if="apiStatus.status === 'ok'" xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <svg v-else-if="apiStatus.status === 'error'" xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-error" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            <svg v-else class="w-8 h-8 text-warning animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" stroke-width="4" class="opacity-25" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M12 2a10 10 0 000 20 10 10 0 000-20z" />
                            </svg>
                        </div>
                        <div class="stat-title">API Status</div>
                        <div
                            class="stat-value"
                            :class="{
                                'text-success': apiStatus.status === 'ok',
                                'text-error': apiStatus.status === 'error',
                                'text-warning': apiStatus.status === 'checking'
                            }"
                        >
                            {{ apiStatus.status === 'ok' ? 'Activa' : apiStatus.status === 'error' ? 'Inactiva' : 'Verificando...' }}
                        </div>
                        <div class="stat-desc">
                            {{ apiStatus.message }}
                            <template v-if="apiStatus.songs_count"> • {{ apiStatus.songs_count }} canciones</template>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <!-- Card Acciones -->
                    <div class="card bg-base-100 shadow-xl">
                        <div class="card-body">
                            <h2 class="card-title">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                Acciones Rápidas
                            </h2>
                            <p>Gestiona tu cancionero</p>
                            <div class="card-actions justify-end">
                                <Link :href="route('admin.songs.index')" class="btn btn-primary">
                                    📋 Ver Canciones
                                </Link>
                                <Link :href="route('admin.songs.create')" class="btn btn-success">
                                    ➕ Agregar Nueva
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Card API Info -->
                    <div class="card bg-base-100 shadow-xl">
                        <div class="card-body">
                            <h2 class="card-title">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                                </svg>
                                API Endpoints
                            </h2>
                            <div class="space-y-2">
                                <div class="badge badge-outline">GET /api/songs/metadata</div>
                                <div class="badge badge-outline">POST /api/songs/batch</div>
                                <div class="badge badge-outline">GET /api/songs/{id}</div>
                                <div class="badge badge-outline">GET /api/songs/last-modified</div>
                                <div class="badge badge-outline">GET /api/songs/stats</div>
                                <div class="badge badge-outline">GET /api/health</div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
