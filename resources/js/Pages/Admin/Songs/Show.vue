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
                            class="lyrics-content text-gray-800"
                            v-html="renderedLyrics"
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
import { computed } from 'vue';


const {song} = defineProps({
    song: Object
});


// Escapear HTML
const esc = (s) => s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');

// Procesar una línea: detecta [Chord] y los posiciona arriba de la letra
const processLine = (line) => {
    const hasChords = /\[.+?\]/.test(line);

    if (!hasChords) {
        // Línea sin acordes: aplicar negrita/cursiva markdown
        let html = esc(line);
        html = html.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        html = html.replace(/_(.*?)_/g, '<em>$1</em>');
        return `<div class="lyrics-line">${html}</div>`;
    }

    // Línea con acordes: separar en segmentos [Chord]texto
    const parts = line.split(/(\[[^\]]+\])/);
    let html = '<div class="lyrics-line has-chords">';
    let pendingChord = null;

    for (const part of parts) {
        if (!part) continue;
        const chordMatch = part.match(/^\[([^\]]+)\]$/);
        if (chordMatch) {
            // Si ya hay un acorde pendiente sin texto, emitirlo con espacio
            if (pendingChord) {
                html += `<span class="chord-pair"><span class="chord-name">${esc(pendingChord)}</span>&nbsp;</span>`;
            }
            pendingChord = chordMatch[1];
        } else {
            if (pendingChord) {
                html += `<span class="chord-pair"><span class="chord-name">${esc(pendingChord)}</span>${esc(part)}</span>`;
                pendingChord = null;
            } else {
                html += esc(part);
            }
        }
    }
    // Acorde final sin texto después
    if (pendingChord) {
        html += `<span class="chord-pair"><span class="chord-name">${esc(pendingChord)}</span></span>`;
    }

    html += '</div>';
    return html;
};

// Unir todos los bloques o usar lyrics plano, y renderizar como texto corrido
const renderedLyrics = computed(() => {
    let fullText = '';

    if (song.lyrics_blocks && song.lyrics_blocks.length) {
        fullText = song.lyrics_blocks.map(b => b.content || '').join('\n\n');
    } else {
        fullText = song.lyrics || '';
    }

    if (!fullText) return '';
    return fullText.split('\n').map(processLine).join('');
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
.lyrics-content {
    font-size: 1.05rem;
}

.lyrics-content :deep(.lyrics-line) {
    line-height: 1.6;
    color: #374151;
    min-height: 1.6em;
    padding: 0.1em 0;
}

.lyrics-content :deep(.lyrics-line.has-chords) {
    padding-top: 1.4em;
    line-height: 1.6;
}

.lyrics-content :deep(.chord-pair) {
    position: relative;
    display: inline;
}

.lyrics-content :deep(.chord-name) {
    position: absolute;
    bottom: 100%;
    left: 0;
    font-weight: 700;
    font-size: 0.85em;
    font-family: 'Courier New', Courier, monospace;
    color: #dc2626;
    white-space: nowrap;
    line-height: 1.2;
}

.lyrics-content :deep(strong) {
    font-weight: 700;
}

.lyrics-content :deep(em) {
    font-style: italic;
    color: #6b7280;
}
</style>