<template>
    <Head :title="song.title" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                        {{ song.title }}
                    </h2>
                    <p class="text-lg text-gray-600 mt-1">
                        🎤 {{ song.artist }}
                    </p>
                </div>
                <div class="flex gap-2">
                    <Link :href="route('admin.songs.edit', song.id)" 
                          class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600 transition">
                        ✏️ Editar
                    </Link>
                    <Link :href="route('admin.songs.index')" 
                          class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition">
                        ← Volver
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <!-- Información Musical -->
                <div v-if="song.key || song.tempo || song.rhythm" 
                     class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            🎼 Información Musical
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div v-if="song.key" class="bg-blue-50 p-4 rounded-lg">
                                <p class="text-sm text-gray-600">Tono</p>
                                <p class="text-2xl font-bold text-blue-600">{{ song.key }}</p>
                            </div>
                            <div v-if="song.tempo" class="bg-green-50 p-4 rounded-lg">
                                <p class="text-sm text-gray-600">Tempo</p>
                                <p class="text-2xl font-bold text-green-600">{{ song.tempo }} BPM</p>
                            </div>
                            <div v-if="song.rhythm" class="bg-purple-50 p-4 rounded-lg">
                                <p class="text-sm text-gray-600">Ritmo</p>
                                <p class="text-2xl font-bold text-purple-600">{{ song.rhythm }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Video de YouTube -->
                <div v-if="youtubeId" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            🎥 Video
                        </h3>
                        <div class="aspect-video">
                            <iframe
                                :src="`https://www.youtube.com/embed/${youtubeId}`"
                                class="w-full h-full rounded-lg"
                                frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen
                            ></iframe>
                        </div>
                    </div>
                </div>

                <!-- Letras -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            📝 Letras
                        </h3>
                        <div 
                            class="prose prose-lg max-w-none lyrics-content"
                            v-html="htmlLyrics"
                        ></div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>


<script setup>

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { marked } from 'marked';
import { computed } from 'vue';


const {song} = defineProps({
    song: Object
});


marked.setOptions({
    breaks: true,
    gfm: true,
    headerIds: false,
    mangle: false
});


// Covertir markdown a HTML
const htmlLyrics = computed(()=>{
    return marked(song.lyrics || '');
});

// Extraer ID de video de YouTube
const getYouTubeId = (url) => {
    if(!url) return null;
    const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/;
    const match = url.match(regExp);
    return (match && match[2].length === 11) ? match[2] : null;
}

const youtubeId = computed(() => getYouTubeId(song.video_url));

</script>

<style scoped>
.lyrics-content :deep(h2) {
    color: #2563eb;
    font-size: 1.5rem;
    font-weight: 700;
    margin-top: 2rem;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #dbeafe;
}

.lyrics-content :deep(p) {
    line-height: 2;
    margin-bottom: 1rem;
    color: #374151;
    white-space: pre-wrap;
}

.lyrics-content :deep(strong) {
    color: #dc2626;
    font-weight: 700;
    font-size: 0.95em;
    background-color: #fee2e2;
    padding: 0.125rem 0.375rem;
    border-radius: 0.25rem;
}

.lyrics-content :deep(*){
    white-space: pre-wrap;
}

</style>