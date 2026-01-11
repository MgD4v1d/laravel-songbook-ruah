<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { ref, watch } from "vue";

const props = defineProps({
    categories: Object,
    filters: Object,
});

const search = ref(props.filters.search || "");
const filter = ref(props.filters.filter || "all");

const searchCategories = () => {
    router.get(
        route("admin.categories.index"),
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
    router.get(route("admin.categories.index"));
};

const debounce = (fn, delay) => {
    let timeoutId;
    return (...args) => {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => fn(...args), delay);
    };
};

const debouncedSearch = debounce(() => searchCategories(), 500);

watch(search, () => {
    debouncedSearch();
});

watch(filter, () => {
    searchCategories();
});

const deleteCategory = (id, name) => {
    if (confirm(`¿Estás seguro de eliminar la categoría "${name}"?`)) {
        router.delete(route("admin.categories.destroy", id), {
            preserveScroll: true,
        });
    }
};

const translateLabel = (label) => {
    if (label === "pagination.previous") return "&laquo; Anterior";
    if (label === "pagination.next") return "Siguiente &raquo;";
    return label;
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString("es-MX", {
        year: "numeric",
        month: "short",
        day: "numeric",
    });
};
</script>

<template>
    <Head title="Categorías" />

    <AuthenticatedLayout>
        <template #header>
            <div
                class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4"
            >
                <div>
                    <h2
                        class="font-semibold text-2xl text-gray-800 leading-tight"
                    >
                        <i class="ri-folder-music-line"></i> Gestionar
                        Categorías
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">
                        {{ categories.total }} categorías registradas
                    </p>
                </div>

                <Link
                    :href="route('admin.categories.create')"
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
                    Agregar Categoría
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
                                        @keyup.enter="searchCategories"
                                        type="text"
                                        placeholder="Buscar por nombre o slug..."
                                        class="input input-bordered join-item flex-1"
                                    />
                                    <button
                                        @click="searchCategories"
                                        class="btn btn-primary join-item gap-2"
                                    >
                                        <i class="ri-search-line"></i>
                                        <span class="hidden sm:inline"
                                            >Buscar</span
                                        >
                                    </button>
                                </div>
                            </div>

                            <!-- Filtro (3 columnas en desktop)-->
                            <div class="lg:col-span-3">
                                <select
                                    class="select select-bordered w-full"
                                    v-model="filter"
                                >
                                    <option value="all">Todas (Orden)</option>
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
                                    <i class="ri-close-line text-lg"></i>
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

                <!-- Tabla -->
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body p-0">
                        <div
                            v-if="categories.data.length === 0"
                            class="text-center py-12 px-4"
                        >
                            <i
                                class="ri-folder-open-line text-6xl text-gray-300 mb-4 block"
                            ></i>
                            <h3
                                class="text-xl font-semibold text-gray-700 mb-2"
                            >
                                No se encontraron categorías
                            </h3>
                            <p class="text-gray-500 mb-6">
                                {{
                                    search
                                        ? "Intenta con otra búsqueda"
                                        : "No hay categorías registradas aún"
                                }}
                            </p>
                            <button
                                v-if="search"
                                @click="clearSearch"
                                class="btn btn-ghost"
                            >
                                Limpiar búsqueda
                            </button>
                        </div>

                        <div v-else class="overflow-x-auto">
                            <table class="table table-zebra">
                                <thead>
                                    <tr>
                                        <th>Categoría</th>
                                        <th>Slug</th>
                                        <th>Canciones</th>
                                        <th>Creada</th>
                                        <th class="text-right">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="category in categories.data"
                                        :key="category.id"
                                        class="hover"
                                    >
                                        <td>
                                            <div class="flex items-center gap-3">
                                                <div class="avatar placeholder">
                                                    <div
                                                        class="!flex text-white rounded-lg w-10 h-10 justify-center items-center"
                                                        :style="{
                                                            backgroundColor:
                                                                category.color ||
                                                                '#6b7280',
                                                        }"
                                                    >
                                                        <i
                                                            :class="
                                                                category.icon ||
                                                                'ri-hashtag'
                                                            "
                                                            class="text-xl"
                                                        ></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="font-bold">
                                                        {{ category.name }}
                                                    </div>
                                                    <div
                                                        v-if="
                                                            category.description
                                                        "
                                                        class="text-xs opacity-70 truncate max-w-[200px]"
                                                    >
                                                        {{
                                                            category.description
                                                        }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge badge-ghost">{{
                                                category.slug
                                            }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-neutral"
                                                >{{
                                                    category.songs_count
                                                }}
                                                canciones</span
                                            >
                                        </td>
                                        <td>
                                            <div class="text-sm opacity-70">
                                                {{
                                                    formatDate(
                                                        category.created_at
                                                    )
                                                }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="flex justify-end gap-2">
                                                <Link
                                                    :href="
                                                        route(
                                                            'admin.categories.edit',
                                                            category.id
                                                        )
                                                    "
                                                    class="btn btn-warning btn-xs gap-1"
                                                >
                                                    <i
                                                        class="ri-edit-2-line"
                                                    ></i>
                                                    Editar
                                                </Link>
                                                <button
                                                    @click="
                                                        deleteCategory(
                                                            category.id,
                                                            category.name
                                                        )
                                                    "
                                                    class="btn btn-error btn-xs gap-1"
                                                >
                                                    <i
                                                        class="ri-delete-bin-line"
                                                    ></i>
                                                    Eliminar
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginación -->
                        <div
                            v-if="categories.links.length > 3"
                            class="border-t p-4"
                        >
                            <div
                                class="flex flex-col sm:flex-row justify-between items-center gap-4"
                            >
                                <div class="text-sm text-gray-600">
                                    Mostrando
                                    <span class="font-semibold">{{
                                        categories.from
                                    }}</span>
                                    a
                                    <span class="font-semibold">{{
                                        categories.to
                                    }}</span>
                                    de
                                    <span class="font-semibold">{{
                                        categories.total
                                    }}</span>
                                </div>
                                <div class="join">
                                    <template
                                        v-for="(link, index) in categories.links"
                                        :key="index"
                                    >
                                        <Link
                                            v-if="link.url"
                                            :href="link.url"
                                            class="join-item btn btn-sm"
                                            :class="{
                                                'btn-active': link.active,
                                            }"
                                            v-html="translateLabel(link.label)"
                                        ></Link>
                                        <button
                                            v-else
                                            class="join-item btn btn-sm btn-disabled"
                                            disabled
                                            v-html="translateLabel(link.label)"
                                        ></button>
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
