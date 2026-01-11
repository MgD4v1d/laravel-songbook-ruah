<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import MarkdownEditor from '@/Components/MarkdownEditor.vue';
import {Head,Link, useForm, router} from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';


const {song, categories} = defineProps({
    song: Object,
    categories: Array
});

let lyrics = song.lyrics;

let proccessLyrics = lyrics.replace(/\r/g, "");



const form = useForm({
    title: song.title,
    artist: song.artist,
    lyrics: proccessLyrics,
    key: song.key || '',
    rhythm: song.rhythm || '',
    tempo: song.tempo || null,
    video_url: song.video_url || '',
    categories: song.categories ? song.categories.map(c => c.id) : []
});

// Detectar si tiene campos de musico
const isMusicianMode = ref(!!(song.key || song.tempo || song.rhythm));
const returnUrl = ref(null);

onMounted(()=>{
    returnUrl.value = localStorage.getItem('songs_return_url');
});

const submit = () => {
    if( !isMusicianMode.value){
        form.key = '';
        form.rhythm = '';
        form.tempo = null;
    }

    form.put(route('admin.songs.update', song.id), {
        onSuccess: () => {
            if(returnUrl.value){
                localStorage.removeItem('songs_return_url');
                router.visit(returnUrl.value);
            }
        }
    });
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
                        Editar Canción
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">
                        {{ song.title }} - {{ song.artist }}
                    </p>
                </div>
                <div class="flex gap-2">
                    <Link :href="route('admin.songs.show', song.id)" 
                          class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition">
                        👁️ Ver
                    </Link>
                    <button 
                        @click="goBack"
                        class="btn btn-neutral gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Volver
                    </button>
                </div>
            </div>
        </template>


        <div class="py-12">
            <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit" class="p-6 space-y-6">
                        
                        <!-- Toggle Modo Tecladista -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <label class="flex items-center cursor-pointer">
                                <input 
                                    type="checkbox"
                                    v-model="isMusicianMode"
                                    class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                />
                                <span class="ml-3 text-lg font-medium text-blue-900">
                                    🎹 Soy tecladista/músico (mostrar campos técnicos)
                                </span>
                            </label>
                        </div>

                        <!-- Grid para campos básicos -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Título -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Título *
                                </label>
                                <input 
                                    v-model="form.title"
                                    type="text"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    required
                                />
                                <p v-if="form.errors.title" class="text-red-500 text-sm mt-1">
                                    {{ form.errors.title }}
                                </p>
                            </div>

                            <!-- Artista -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Artista *
                                </label>
                                <input 
                                    v-model="form.artist"
                                    type="text"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    required
                                />
                                <p v-if="form.errors.artist" class="text-red-500 text-sm mt-1">
                                    {{ form.errors.artist }}
                                </p>
                            </div>
                        </div>

                        <!-- Campos de Músico -->
                        <transition
                            enter-active-class="transition ease-out duration-300"
                            enter-from-class="transform opacity-0 scale-95"
                            enter-to-class="transform opacity-100 scale-100"
                            leave-active-class="transition ease-in duration-200"
                            leave-from-class="transform opacity-100 scale-100"
                            leave-to-class="transform opacity-0 scale-95"
                        >
                            <div v-if="isMusicianMode" class="space-y-6 bg-gray-50 border border-gray-200 rounded-lg p-4">
                                <h3 class="text-lg font-semibold text-gray-900">
                                    🎼 Información Musical
                                </h3>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <!-- Tono -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            🎹 Tono (Key)
                                        </label>                                        
                                        <input
                                            v-model="form.key"
                                            type="text"
                                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                            placeholder="Tono de la canción"
                                        />

                                         <p v-if="form.errors.key" class="text-red-500 text-sm mt-1">
                                            {{ form.errors.key }}
                                        </p>
                                    </div>

                                    <!-- Tempo -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            ⏱️ Tempo (BPM)
                                        </label>
                                        <input 
                                            v-model.number="form.tempo"
                                            type="number"
                                            min="40"
                                            max="240"
                                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                            placeholder="120"
                                        />
                                    </div>

                                    <!-- Ritmo -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            🥁 Ritmo
                                        </label>
                                         <input
                                            v-model="form.rhythm"
                                            type="text"
                                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                            placeholder="Ritmo/Estilo de la canción"
                                        />
                                        <p v-if="form.errors.rhythm" class="text-red-500 text-sm mt-1">
                                            {{ form.errors.rhythm }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </transition>

                        <!-- Editor de Letras -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                📝 Letras de la Canción *
                            </label>
                            
                            <MarkdownEditor 
                                v-model="form.lyrics"
                                height="500px"
                            />
                            
                            <p v-if="form.errors.lyrics" class="text-red-500 text-sm mt-1">
                                {{ form.errors.lyrics }}
                            </p>
                        </div>

                        <!-- URL Video -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                🎥 URL de Video (YouTube)
                            </label>
                            <input
                                v-model="form.video_url"
                                type="url"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="https://www.youtube.com/watch?v=..."
                            />
                            <p v-if="form.errors.video_url" class="text-red-500 text-sm mt-1">
                                {{ form.errors.video_url }}
                            </p>
                        </div>

                        <!-- Categorías -->
                        <div class="bg-white border border-gray-200 rounded-lg p-6 space-y-4">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                                Categorías (Opcional)
                            </h3>

                            <div v-if="categories && categories.length > 0" class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                <label
                                    v-for="category in categories"
                                    :key="category.id"
                                    class="flex items-center gap-3 bg-gray-50 p-3 rounded-lg hover:bg-gray-100 cursor-pointer transition">
                                    <input
                                        type="checkbox"
                                        :value="category.id"
                                        v-model="form.categories"
                                        class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                    />
                                    <span class="text-sm font-medium text-gray-700">{{ category.name }}</span>
                                </label>
                            </div>

                            <div v-else class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <p class="text-blue-700">No hay categorías disponibles. Crea categorías primero.</p>
                            </div>

                            <p v-if="form.errors.categories" class="text-red-500 text-sm mt-1">
                                {{ form.errors.categories }}
                            </p>
                        </div>

                        <!-- Botones -->
                        <div class="flex justify-between items-center">
                            <button 
                                type="button"
                                @click="$inertia.delete(route('admin.songs.destroy', song.id), {
                                    onBefore: () => confirm('¿Estás seguro de eliminar esta canción?'),
                                    onSuccess: () =>{
                                        if(returnUrl){
                                            localStorage.removeItem('songs_return_url');
                                            router.visit(returnUrl);
                                        }
                                    }
                                })"
                                class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                                🗑️ Eliminar Canción
                            </button>
                            
                            <div class="flex gap-3">
                                <button 
                                    type="button"
                                    @click="goBack"
                                    class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700">
                                    Cancelar
                                </button>
                                <button 
                                    type="submit" 
                                    :disabled="form.processing"
                                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 transition">
                                    <span v-if="form.processing">Guardando...</span>
                                    <span v-else>💾 Guardar Cambios</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </AuthenticatedLayout>

</template>