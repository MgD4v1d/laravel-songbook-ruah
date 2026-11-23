<script setup>
import SongForm from '@/Components/SongForm.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, Link, useForm, router} from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import { useConfirm } from '@/Composables/useConfirm';

const { confirm } = useConfirm();

const {song, categories, seasonOptions} = defineProps({
    song: Object,
    categories: Array,
    seasonOptions: Array,
});

const form = useForm({
    title: song.title,
    artist: song.artist,
    lyrics_blocks: song.lyrics_blocks || [],
    key: song.key || '',
    rhythm: song.rhythm || '',
    tempo: song.tempo || null,
    video_url: song.video_url || '',
    seasons: song.seasons ?? ['general'],
    categories: song.categories ? song.categories.map(c => c.id) : []
});



const returnUrl = ref(null);

onMounted(() => {
    returnUrl.value = localStorage.getItem('songs_return_url');
});

const submit = () => {
    form.put(route('admin.songs.update', song.id), {
        onSuccess: () => {
            if (returnUrl.value) {
                localStorage.removeItem('songs_return_url');
                router.visit(returnUrl.value);
            }
        }
    });
};

const deleteSong = async () => {
    if (await confirm({ message: `Se eliminará "${song.title}".`, confirmText: 'Eliminar', danger: true })) {
        router.delete(route('admin.songs.destroy', song.id), {
            onSuccess: () => {
                if (returnUrl.value) {
                    localStorage.removeItem('songs_return_url');
                    router.visit(returnUrl.value);
                }
            }
        });
    }
};


const goBack = () => {
    if (returnUrl.value) {
        localStorage.removeItem('songs_return_url');
        router.visit(returnUrl.value);
    } else {
        router.visit(route('admin.songs.index'));
    }
};
</script>

<template>
    <Head :title="`Editar : ${song.title}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        <i class="ri-edit-line"></i> Editar Canción
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">
                        {{ song.title }} - {{ song.artist }}
                    </p>
                </div>
                <div class="flex gap-2">
                    <Link :href="route('admin.songs.show', song.id)" 
                          class="btn btn-success btn-sm gap-2">
                        <i class="ri-eye-line"></i>
                        Ver
                    </Link>
                    <button 
                        @click="goBack"
                        class="btn btn-ghost btn-sm gap-2">
                        <i class="ri-arrow-left-long-line"></i>
                        Volver
                    </button>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <SongForm
                        :form="form"
                        :categories="categories"
                        :season-options="seasonOptions"
                        @submit="submit"
                    >
                        <template #actions>
                            <div class="flex justify-between items-center">
                                <!-- Botón Eliminar -->
                                <button
                                    type="button"
                                    @click="deleteSong"
                                    class="btn btn-error gap-2">
                                    <i class="ri-delete-bin-line"></i>
                                    Eliminar Canción
                                </button>
                                
                                <!-- Botones Cancelar y Guardar -->
                                <div class="flex gap-3">
                                    <button 
                                        type="button"
                                        @click="goBack"
                                        class="btn btn-ghost gap-2">
                                        <i class="ri-close-line"></i>
                                        Cancelar
                                    </button>
                                    <button 
                                        type="submit" 
                                        :disabled="form.processing"
                                        class="btn btn-primary gap-2">
                                        <span v-if="!form.processing">
                                            <i class="ri-save-line"></i>
                                        </span>
                                        <span v-if="form.processing" class="loading loading-spinner"></span>
                                        {{ form.processing ? 'Guardando...' : 'Guardar Cambios' }}
                                    </button>
                                </div>
                            </div>
                        </template>
                    </SongForm>
                </div>
            </div>
        </div>

    </AuthenticatedLayout>
</template>