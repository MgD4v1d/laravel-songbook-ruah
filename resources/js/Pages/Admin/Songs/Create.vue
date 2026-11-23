<script setup>
import SongForm from '@/Components/SongForm.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const { categories, seasonOptions } = defineProps({
    seasonOptions: Array,
    categories: Array
});

const form = useForm({
    title: '',
    artist: '',
    lyrics_blocks: [],
    key: '',
    rhythm: '',
    tempo: null,
    video_url: '',
    seasons: ['general'],
    categories: []
});


</script>
<template>
    <Head title="Crear Cancion" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    <i class="ri-disc-line"></i> Agregar Nueva Canción
                </h2>
                <Link :href="route('admin.songs.index')" 
                      class="btn btn-ghost btn-sm gap-2">
                      <i class="ri-arrow-left-long-line text-lg"></i>
                    Volver
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <SongForm
                        :form="form"
                        :categories="categories"
                        :season-options="seasonOptions"
                        @submit="form.post(route('admin.songs.store'))"
                    >
                        <template #actions>
                            <div class="flex justify-end gap-3">
                                 <Link 
                                    :href="route('admin.songs.index')"
                                    class="btn btn-error">
                                    Cancelar
                                </Link>
                                <button 
                                    type="submit" 
                                    :disabled="form.processing"
                                    class="btn btn-primary gap-2">
                                    <span v-if="!form.processing">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                                        </svg>
                                    </span>
                                    <span v-if="form.processing" class="loading loading-spinner"></span>
                                    {{ form.processing ? 'Guardando...' : 'Guardar Canción' }}
                                </button>
                            </div>
                        </template>
                    </SongForm>
                </div>
            </div>
        </div>

    </AuthenticatedLayout>
</template>
