<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head, Link, router } from '@inertiajs/vue3';
    import { ref } from 'vue';

    const props = defineProps({
        songs: Object,
        filters: Object
    });

    const search = ref(props.filters.search || '');

    const searchSongs = () => {
        router.get('/admin/songs', { search: search.value }, {
            preserveState: true,
            replace: true
        })
    };

    const deleteSong = (id, title) => {
        if (confirm(`¿Estás seguro de eliminar "${title}"?`)) {
            router.delete(`/admin/songs/${id}`, {
                preserveScroll: true
            });
        }
    };

    const translateLabel = (label) => {
        if (label === 'pagination.previous') return '&laquo; Anterior';
        if (label === 'pagination.next') return 'Siguiente &raquo;';
        return label;
    };
</script>

<template>
    <Head title="Canciones" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                        🎵 Gestionar Canciones
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">
                        {{ songs.total }} canciones en tu biblioteca
                    </p>
                </div>
                <Link :href="route('admin.songs.create')" 
                      class="btn btn-primary gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    Agregar Canción
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
               <!-- Buscador con DaisyUI -->
                <div class="card bg-base-100 shadow-xl mb-6">
                    <div class="card-body p-4">
                        <div class="join w-full">
                            <input 
                                v-model="search"
                                @keyup.enter="searchSongs"
                                type="text"
                                placeholder="Buscar por título, artista o letra..."
                                class="input input-bordered join-item flex-1"
                            />
                            <button 
                                @click="searchSongs" 
                                class="btn btn-primary join-item gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                <span class="hidden sm:inline">Buscar</span>
                            </button>
                            <Link 
                                v-if="search"
                                :href="route('admin.songs.index')"
                                class="btn btn-ghost join-item"
                                title="Limpiar búsqueda">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Alert si hay búsqueda activa -->
                <div v-if="search" class="alert alert-info shadow-lg mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <div class="font-bold">Búsqueda activa</div>
                        <div class="text-xs">Mostrando resultados para "{{ search }}"</div>
                    </div>
                </div>

                <!-- Lista de canciones con DaisyUI -->
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        
                        <!-- Empty state -->
                        <div v-if="songs.data.length === 0" class="text-center py-12">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 mx-auto text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                            </svg>
                            <h3 class="text-xl font-semibold text-gray-700 mb-2">
                                No se encontraron canciones
                            </h3>
                            <p class="text-gray-500 mb-6">
                                {{ search ? 'Intenta con otra búsqueda' : 'Comienza agregando tu primera canción' }}
                            </p>
                            <Link v-if="!search" :href="route('admin.songs.create')" class="btn btn-primary">
                                Agregar Primera Canción
                            </Link>
                        </div>

                        <!-- Lista de canciones -->
                        <div v-else class="space-y-4">
                            <div 
                                v-for="song in songs.data" 
                                :key="song.id"
                                class="card bg-base-200 shadow-md hover:shadow-lg transition-shadow"
                            >
                                <div class="card-body p-4">
                                    <div class="flex flex-col sm:flex-row justify-between gap-4">
                                        
                                        <!-- Info de la canción -->
                                        <div class="flex-1">
                                            <h3 class="card-title text-lg">
                                                {{ song.title }}
                                            </h3>
                                            <p class="text-sm opacity-70">
                                                🎤 {{ song.artist }}
                                            </p>
                                            
                                            <!-- Badges -->
                                            <div class="flex flex-wrap gap-2 mt-2">
                                                <div v-if="song.key" class="badge badge-primary gap-1">
                                                    🎹 {{ song.key }}
                                                </div>
                                                <div v-if="song.tempo" class="badge badge-secondary gap-1">
                                                    ⏱️ {{ song.tempo }} BPM
                                                </div>
                                                <div v-if="song.rhythm" class="badge badge-accent gap-1">
                                                    🥁 {{ song.rhythm }}
                                                </div>
                                                <div v-if="song.video_url" class="badge badge-error gap-1">
                                                    🎥 Video
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Acciones -->
                                        <div class="flex sm:flex-col gap-2">
                                            <Link 
                                                :href="route('admin.songs.show', song.id)"
                                                class="btn btn-success btn-sm gap-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                Ver
                                            </Link>
                                            <Link 
                                                :href="route('admin.songs.edit', song.id)"
                                                class="btn btn-warning btn-sm gap-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                Editar
                                            </Link>
                                            <button 
                                                @click="deleteSong(song.id, song.title)"
                                                class="btn btn-error btn-sm gap-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                Eliminar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Paginación con DaisyUI -->
                        <div v-if="songs.links.length > 3" class="flex justify-center mt-6">
                            <div class="join">
                                <template v-for="(link, index) in songs.links" :key="index">
                                    <Link v-if="link.url"  
                                        :href="link.url"
                                        :class="[
                                            'join-item btn',
                                            link.active ? 'btn-active' : '',
                                            !link.url ? 'btn-disabled' : ''
                                        ]"
                                        v-html="translateLabel(link.label)"
                                    />
                                    <span v-else v-html="translateLabel(link.label)"
                                    class="px-4 py-2 rounded btn-disabled btn cursor-not-allowed"></span>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>


