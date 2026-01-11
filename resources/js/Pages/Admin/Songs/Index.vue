<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { ref, watch } from "vue";

const props = defineProps({
    songs: Object,
    filters: Object,
});

const search = ref(props.filters.search || "");
const filter = ref(props.filters.filter || "all");

const searchSongs = () => {
    router.get(
        "/admin/songs",
        {
            search: search.value,
            filter: filter.value,
        },
        {
            preserveState: true,
            replace: true,
        }
    );
};

const clearSearch = () => {
    search.value = "";
    filter.value = "all";
    router.get("/admin/songs");
};

const debounce = (fn, delay) => {
    let timeoutId;
    return (...args) => {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => fn(...args), delay);
    };
};

const debouncedSearch = debounce(() => searchSongs(), 500);

watch(search, () => {
    debouncedSearch();
});

watch(filter, () => {
    searchSongs();
});

const deleteSong = (id, title) => {
    if (confirm(`¿Estás seguro de eliminar "${title}"?`)) {
        router.delete(`/admin/songs/${id}`, {
            preserveScroll: true,
        });
    }
};

const translateLabel = (label) => {
    if (label === "pagination.previous") return "&laquo; Anterior";
    if (label === "pagination.next") return "Siguiente &raquo;";
    return label;
};

const goToEdit = (songId) => {
    // Guardar Url actual en localStorage
    localStorage.setItem("songs_return_url", window.location.href);
    router.visit(route("admin.songs.edit", songId));
};

const formatDate = (dateString) => {
    const date = new Date(dateString);
    const now = new Date();
    const diffTime = Math.abs(now - date);
    const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));

    if (diffDays === 0) {
        return "Hoy";
    } else if (diffDays === 1) {
        return "Ayer";
    } else if (diffDays < 7) {
        return `Hace ${diffDays} días`;
    } else if (diffDays < 30) {
        const weeks = Math.floor(diffDays / 7);
        return `Hace ${weeks} ${weeks === 1 ? "semana" : "semanas"}`;
    } else {
        return date.toLocaleDateString("es-MX", {
            year: "numeric",
            month: "short",
            day: "numeric",
        });
    }
};
</script>

<template>
    <Head title="Canciones" />

    <AuthenticatedLayout>
        <template #header>
            <div
                class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4"
            >
                <div>
                    <h2
                        class="font-semibold text-2xl text-gray-800 leading-tight"
                    >
                        <i class="ri-music-ai-fill"></i> Gestionar Canciones
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">
                        {{ songs.total }} canciones en tu biblioteca
                    </p>
                </div>
                <Link
                    :href="route('admin.songs.create')"
                    class="btn btn-primary gap-2"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                            clip-rule="evenodd"
                        />
                    </svg>
                    Agregar Canción
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Buscador Y Filtro con DaisyUI -->
                <div class="card bg-base-100 shadow-xl mb-6">
                    <div class="card-body p-4">
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-3">
                            <!-- Buscador (8 columnas en desktop) -->
                            <div class="lg:col-span-8">
                                <div class="join w-full">
                                    <input
                                        v-model="search"
                                        @keyup.enter="searchSongs"
                                        type="text"
                                        name="search"
                                        placeholder="Buscar por título, artista o letra..."
                                        class="input input-bordered join-item flex-1"
                                    />
                                    <button
                                        @click="searchSongs"
                                        class="btn btn-primary join-item gap-2"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-5 w-5"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                                            />
                                        </svg>
                                        <span class="hidden sm:inline"
                                            >Buscar</span
                                        >
                                    </button>
                                </div>
                            </div>

                            <!-- Filtro (3 columnas en desktop)-->
                            <div class="lg:col-span-3">
                                <select
                                    name="filters"
                                    class="select select-bordered w-full"
                                    v-model="filter"
                                >
                                    <option value="all">Todas (A-Z)</option>
                                    <option value="recent">Recientes</option>
                                </select>
                            </div>

                            <!-- Botón limpiar (1 columna)-->
                            <div class="lg:col-span-1">
                                <button
                                    v-if="search || filter !== 'all'"
                                    @click="clearSearch"
                                    class="btn btn-ghost w-full"
                                    title="Limpiar filtros"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-5 w-5"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"
                                        />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Alert si hay búsqueda activa -->
                <div v-if="search" class="alert alert-info shadow-lg mb-6">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        class="stroke-current shrink-0 w-6 h-6"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                        ></path>
                    </svg>
                    <div>
                        <div class="font-bold">Búsqueda activa</div>
                        <div class="text-xs">
                            Mostrando resultados para "{{ search }}"
                        </div>
                    </div>
                </div>

                <!-- Tabla con DaisyUi -->
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body p-0">
                        <!-- Empty state -->
                        <div
                            v-if="songs.data.length === 0"
                            class="text-center py-12 px-4"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-24 w-24 mx-auto text-gray-300 mb-4"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"
                                />
                            </svg>
                            <h3
                                class="text-xl font-semibold text-gray-700 mb-2"
                            >
                                No se encontraron canciones
                            </h3>
                            <p class="text-gray-500 mb-6">
                                {{
                                    search
                                        ? "Intenta con otra búsqueda"
                                        : "Comienza agregando tu primera canción"
                                }}
                            </p>
                            <Link
                                v-if="!search"
                                :href="route('admin.songs.create')"
                                class="btn btn-primary"
                            >
                                Agregar Primera Canción
                            </Link>
                            <button
                                v-else
                                @click="clearSearch"
                                class="btn btn-ghost"
                            >
                                Limpiar búsqueda
                            </button>
                        </div>

                        <!-- Table de Songs -->

                        <div v-else class="overflow-x-auto">
                            <table class="table table-zebra">
                                <!-- head -->
                                <thead>
                                    <tr>
                                        <th>Canción</th>
                                        <th>Categorías</th>
                                        <th>Info Musical</th>
                                        <th>Extras</th>
                                        <th>Fecha</th>
                                        <th class="text-right">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="song in songs.data"
                                        :key="song.id"
                                        class="hover"
                                    >
                                        <!-- Canción -->
                                        <td>
                                            <div
                                                class="flex items-center gap-3"
                                            >
                                                <div class="avatar placeholder">
                                                    <div
                                                        class="!flex bg-primary text-primary-content rounded-full w-12 justify-center items-center"
                                                    >
                                                        <span class="text-xl">
                                                            <i
                                                                class="ri-music-2-line"
                                                            ></i>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="font-bold">
                                                        {{ song.title }}
                                                    </div>
                                                    <div
                                                        class="text-sm opacity-70"
                                                    >
                                                        {{ song.artist }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Categorías -->
                                        <td>
                                            <div class="flex flex-wrap gap-1">
                                                <div
                                                    v-if="song.categories && song.categories.length > 0"
                                                    class="flex flex-wrap gap-1"
                                                >
                                                    <span
                                                        v-for="category in song.categories"
                                                        :key="category.id"
                                                        class="badge badge-outline badge-sm"
                                                    >
                                                        {{ category.name }}
                                                    </span>
                                                </div>
                                                <span
                                                    v-else
                                                    class="text-sm opacity-50"
                                                >
                                                    Sin categorías
                                                </span>
                                            </div>
                                        </td>

                                        <!-- Info Musical -->
                                        <td>
                                            <div class="flex flex-wrap gap-2">
                                                <div
                                                    v-if="song.key"
                                                    class="badge badge-primary badge-sm p-1"
                                                >
                                                    <svg
                                                        width="16"
                                                        height="16"
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
                                                    </svg>
                                                    {{ song.key }}
                                                </div>
                                                <div
                                                    v-if="song.tempo"
                                                    class="badge badge-secondary badge-sm"
                                                >
                                                    <i
                                                        class="ri-timer-line text-base"
                                                    ></i>
                                                    {{ song.tempo }}
                                                </div>
                                                <div
                                                    v-if="song.rhythm"
                                                    class="badge badge-accent badge-sm"
                                                >
                                                    <i
                                                        class="ri-voiceprint-fill text-base"
                                                    ></i>
                                                    {{ song.rhythm }}
                                                </div>
                                                <div
                                                    v-if="
                                                        !song.key &&
                                                        !song.tempo &&
                                                        !song.rhythm
                                                    "
                                                    class="text-sm opacity-50"
                                                >
                                                    Sin info musical
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Extras -->
                                        <td>
                                            <div
                                                v-if="song.video_url"
                                                class="badge badge-error badge-sm gap-1 text-white"
                                            >
                                                <i
                                                    class="ri-youtube-line text-base"
                                                ></i>
                                                Video
                                            </div>
                                            <span
                                                v-else
                                                class="text-sm opacity-50"
                                                >-</span
                                            >
                                        </td>

                                        <td>
                                            <div
                                                class="tooltip tooltip-right"
                                                :data-tip="
                                                    new Date(
                                                        song.created_at
                                                    ).toLocaleString('es-MX')
                                                "
                                            >
                                                <div
                                                    class="text-sm opacity-70 flex items-center gap-2"
                                                >
                                                    <svg
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        class="h-4 w-4"
                                                        fill="none"
                                                        viewBox="0 0 24 24"
                                                        stroke="currentColor"
                                                    >
                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                                        />
                                                    </svg>
                                                    <span>{{
                                                        formatDate(
                                                            song.created_at
                                                        )
                                                    }}</span>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Acciones -->
                                        <td>
                                            <div class="flex justify-end gap-2">
                                                <Link
                                                    :href="
                                                        route(
                                                            'admin.songs.show',
                                                            song.id
                                                        )
                                                    "
                                                    class="btn btn-success btn-xs gap-1"
                                                >
                                                    <i
                                                        class="ri-eye-line text-base"
                                                    ></i>
                                                    Ver
                                                </Link>
                                                <button
                                                    @click="goToEdit(song.id)"
                                                    class="btn btn-warning btn-xs gap-1"
                                                >
                                                    <i
                                                        class="ri-edit-2-line text-base"
                                                    ></i>
                                                    Editar
                                                </button>
                                                <button
                                                    @click="
                                                        deleteSong(
                                                            song.id,
                                                            song.title
                                                        )
                                                    "
                                                    class="btn btn-error btn-xs gap-1"
                                                >
                                                    <i
                                                        class="ri-delete-bin-line text-base"
                                                    ></i>
                                                    Eliminar
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginación mejorada con DaisyUI -->
                        <div v-if="songs.links.length > 3" class="border-t p-4">
                            <div
                                class="flex flex-col sm:flex-row justify-between items-center gap-4"
                            >
                                <!-- Info de resultados -->
                                <div class="text-sm text-gray-600">
                                    Mostrando
                                    <span class="font-semibold">{{
                                        songs.from
                                    }}</span>
                                    a
                                    <span class="font-semibold">{{
                                        songs.to
                                    }}</span>
                                    de
                                    <span class="font-semibold">{{
                                        songs.total
                                    }}</span>
                                    canciones
                                </div>

                                <!-- Botones de paginación -->
                                <div class="join">
                                    <template
                                        v-for="(link, index) in songs.links"
                                        :key="index"
                                    >
                                        <Link
                                            v-if="link.url"
                                            :href="link.url"
                                            :class="[
                                                'join-item btn btn-sm',
                                                link.active ? 'btn-active' : '',
                                            ]"
                                            :preserve-state="true"
                                            :preserve-scroll="true"
                                        >
                                            <span
                                                v-if="
                                                    link.label ===
                                                    'pagination.previous'
                                                "
                                            >
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    class="h-4 w-4"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke="currentColor"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M15 19l-7-7 7-7"
                                                    />
                                                </svg>
                                            </span>
                                            <span
                                                v-else-if="
                                                    link.label ===
                                                    'pagination.next'
                                                "
                                            >
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    class="h-4 w-4"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke="currentColor"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 5l7 7-7 7"
                                                    />
                                                </svg>
                                            </span>
                                            <span
                                                v-else
                                                v-html="
                                                    translateLabel(link.label)
                                                "
                                            ></span>
                                        </Link>
                                        <button
                                            v-else
                                            class="join-item btn btn-sm btn-disabled"
                                            disabled
                                        >
                                            <span
                                                v-if="
                                                    link.label ===
                                                    'pagination.previous'
                                                "
                                            >
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    class="h-4 w-4"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke="currentColor"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M15 19l-7-7 7-7"
                                                    />
                                                </svg>
                                            </span>
                                            <span
                                                v-else-if="
                                                    link.label ===
                                                    'pagination.next'
                                                "
                                            >
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    class="h-4 w-4"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke="currentColor"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 5l7 7-7 7"
                                                    />
                                                </svg>
                                            </span>
                                            <span
                                                v-else
                                                v-html="
                                                    translateLabel(link.label)
                                                "
                                            ></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
