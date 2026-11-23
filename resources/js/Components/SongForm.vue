<script setup>
import { ref } from 'vue';
import LyricsBlocksEditor from '@/Components/LyricsBlocksEditor.vue';

// form es el useForm() de la página: aquí solo se editan sus campos, la página decide a dónde se envía
const { form } = defineProps({
    form: Object,
    categories: Array,
    seasonOptions: Array,
});

const emit = defineEmits(['submit']);

const isMusicianMode = ref(!!(form.key || form.tempo || form.rhythm));

// Tiempo litúrgico: "Uso general" excluye a los demás tiempos
function toggleSeason(value) {
    const selected = form.seasons;

    if (selected.includes(value)) {
        const remaining = selected.filter(s => s !== value);
        form.seasons = remaining.length ? remaining : ['general'];
        return;
    }

    if (value === 'general') {
        form.seasons = ['general'];
        return;
    }

    form.seasons = [...selected.filter(s => s !== 'general'), value];
}

const submit = () => {
    if (!isMusicianMode.value) {
        form.key = '';
        form.tempo = null;
        form.rhythm = '';
    }
    emit('submit');
};
</script>

<template>
    <form @submit.prevent="submit" class="p-6 space-y-6">
        <!-- Toggle Modo Tecladista -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body p-4">
                <div class="flex items-center gap-4">
                    <input 
                        type="checkbox"
                        v-model="isMusicianMode"
                        class="checkbox checkbox-lg"
                        name="toggle"
                    />
                    
                    <div>
                        <span class="flex label-text text-lg font-bold text-primary-content">
                            <svg
                            width="24"
                            height="24"
                            viewBox="0 0 24 24"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                            >
                            <path
                                fill-rule="evenodd"
                                clip-rule="evenodd"
                                d="M22 21C23.1046 21 24 20.1046 24 19V5C24 3.89543 23.1046 3 22 3H3C1.89543 3 1 3.89543 1 5V19C1 20.1046 1.89543 21 3 21H22ZM11 5H8.98486V13H7.98511V19H12V13H11V5ZM18.0151 19H22V5H19.0151V13H18.0151V19ZM17.0151 13H16.0151V5H14V13H13V19H17.0151V13ZM6.98511 19V13H5.98486V5H3L3 19H6.98511Z"
                                fill="currentColor"
                            />
                            </svg> &nbsp; Soy tecladista/músico
                        </span>
                        <p class="text-sm opacity-90">
                            Activa para agregar información musical: tono, tempo y ritmo
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información Básica -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h3 class="card-title">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Información Básica
                </h3>

                <div class="divider"></div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Título -->
                    <div class="form-control w-full">
                        <label class="label">
                            <span class="label-text font-semibold">Título *</span>
                        </label>
                        <input 
                            v-model="form.title"
                            type="text"
                            placeholder="Nombre de la canción"
                            class="input input-bordered w-full"
                            :class="{ 'input-error': form.errors.title }"
                            required
                        />
                        <label v-if="form.errors.title" class="label">
                            <span class="label-text-alt text-error">{{ form.errors.title }}</span>
                        </label>
                    </div>

                    <!-- Artista -->
                    <div class="form-control w-full">
                        <label class="label">
                            <span class="label-text font-semibold">Artista *</span>
                        </label>
                        <input 
                            v-model="form.artist"
                            type="text"
                            placeholder="Nombre del artista o compositor"
                            class="input input-bordered w-full"
                            :class="{ 'input-error': form.errors.artist }"
                            required
                        />
                        <label v-if="form.errors.artist" class="label">
                            <span class="label-text-alt text-error">{{ form.errors.artist }}</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Campos de Músico (condicionales) -->
        <transition
            enter-active-class="transition ease-out duration-300"
            enter-from-class="transform opacity-0 scale-95"
            enter-to-class="transform opacity-100 scale-100"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="transform opacity-100 scale-100"
            leave-to-class="transform opacity-0 scale-95"
        >
            <div v-if="isMusicianMode" class="card bg-base-100 shadow-xl border-2 border-primary">
                <div class="card-body">
                    <h3 class="card-title">
                        🎼 Información Musical
                    </h3>

                    <div class="divider"></div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Tono -->
                        <div class="form-control w-full">
                            <label class="label">
                                <span class="label-text font-semibold">🎹 Tono (Key)</span>
                            </label>
                            <input
                                v-model="form.key"
                                type="text"
                                class="input input-bordered w-full"
                                placeholder="Tono de la canción"
                            />
                            <label class="label">
                                <span class="label-text-alt">Tonalidad musical</span>
                            </label>
                            <p v-if="form.errors.key" class="text-red-500 text-sm mt-1">
                                {{ form.errors.key }}
                            </p>
                        </div>

                        <!-- Tempo -->
                        <div class="form-control w-full">
                            <label class="label">
                                <span class="label-text font-semibold">⏱️ Tempo (BPM)</span>
                            </label>
                            <input 
                                v-model.number="form.tempo"
                                type="number"
                                min="40"
                                max="240"
                                class="input input-bordered w-full"
                                placeholder="Ej: 120"
                            />
                            <label class="label">
                                <span class="label-text-alt">40-240 pulsaciones/min</span>
                            </label>
                            <p v-if="form.errors.tempo" class="text-red-500 text-sm mt-1">
                                {{ form.errors.tempo }}
                            </p>
                        </div>

                        <!-- Ritmo -->
                        <div class="form-control w-full">
                            <label class="label">
                                <span class="label-text font-semibold">🥁 Ritmo / Género</span>
                            </label>
                            <input
                                v-model="form.rhythm"
                                type="text"
                                class="input input-bordered w-full"
                                placeholder="Ritmo/Estilo de la canción"
                            />
                            <label class="label">
                                <span class="label-text-alt">Estilo musical</span>
                            </label>
                            <p v-if="form.errors.rhythm" class="text-red-500 text-sm mt-1">
                                {{ form.errors.rhythm }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </transition>

        <!-- Editor de Letras con Bloques -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h3 class="card-title">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Letra de la Canción *
                </h3>
                
                <div class="divider"></div>
                
                <LyricsBlocksEditor v-model="form.lyrics_blocks" />

                <label v-if="form.errors.lyrics_blocks" class="label">
                    <span class="label-text-alt text-error">{{ form.errors.lyrics_blocks }}</span>
                </label>
            </div>
        </div>

        <!-- Video YouTube -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h3 class="card-title">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Video de YouTube (Opcional)
                </h3>
                
                <div class="divider"></div>

                <div class="form-control w-full">
                    <label class="label">
                        <span class="label-text">URL del video</span>
                    </label>
                    <input 
                        v-model="form.video_url"
                        type="url"
                        placeholder="https://www.youtube.com/watch?v=..."
                        class="input input-bordered w-full"
                        :class="{ 'input-error': form.errors.video_url }"
                    />
                    <label class="label">
                        <span class="label-text-alt">Opcional: Enlace a video de la canción</span>
                        <span v-if="form.errors.video_url" class="label-text-alt text-error">
                            {{ form.errors.video_url }}
                        </span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Liturgical seasons -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h3 class="card-title">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                    Tiempo litúrgico
                </h3>

                <div class="divider"></div>

                <div class="form-control w-full">
                    <label class="label">
                        <span class="label-text">Selecciona el tiempo litúrgico para esta canción</span>
                    </label>

                    <div v-if="seasonOptions && seasonOptions.length > 0" class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        <label
                            v-for="seasonOption in seasonOptions"
                            :key="seasonOption.value"
                            class="label cursor-pointer justify-start gap-3 bg-base-200 p-3 rounded-lg hover:bg-base-300 transition">
                            <input
                                type="checkbox"
                                :checked="form.seasons.includes(seasonOption.value)"
                                @change="toggleSeason(seasonOption.value)"
                                class="checkbox checkbox-primary"
                            />
                            <span class="label-text">{{ seasonOption.label }}</span>
                            <span class="w-3 h-3 rounded-full" :style="{background: seasonOption.color}"></span>
                        </label>
                    </div>

                    <label v-if="form.errors.seasons" class="label">
                        <span class="label-text-alt text-error">{{ form.errors.seasons }}</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Categorías -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h3 class="card-title">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                    Categorías (Opcional)
                </h3>

                <div class="divider"></div>

                <div class="form-control w-full">
                    <label class="label">
                        <span class="label-text">Selecciona las categorías para esta canción</span>
                    </label>

                    <div v-if="categories && categories.length > 0" class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        <label
                            v-for="category in categories"
                            :key="category.id"
                            class="label cursor-pointer justify-start gap-3 bg-base-200 p-3 rounded-lg hover:bg-base-300 transition">
                            <input
                                type="checkbox"
                                :value="category.id"
                                v-model="form.categories"
                                class="checkbox checkbox-primary"
                            />
                            <span class="label-text">{{ category.name }}</span>
                        </label>
                    </div>

                    <div v-else class="alert alert-info">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>No hay categorías disponibles. Crea categorías primero.</span>
                    </div>

                    <label v-if="form.errors.categories" class="label">
                        <span class="label-text-alt text-error">{{ form.errors.categories }}</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Botones de acción: cada página pone los suyos -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <slot name="actions" />
            </div>
        </div>
    </form>
</template>
