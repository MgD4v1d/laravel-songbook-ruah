<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import { computed } from "vue";

defineProps({
    rosters: Array,
});

const page = usePage();
const flashSuccess = computed(() => page.props.flash?.success);

const goToEdit = (rosterId) => {
    router.visit(route("admin.rosters.edit", rosterId));
};

const publishRoster = (id) => {
    if (confirm("¿Publicar este rol? Quedará visible en la app.")) {
        router.post(route("admin.rosters.publish", id), {}, { preserveScroll: true });
    }
};

const deleteRoster = (id) => {
    if (confirm("¿Estás seguro de eliminar este rol? También se quitará de la app.")) {
        router.delete(`/admin/rosters/${id}`, {
            preserveScroll: true,
        });
    }
};
// "T00:00" fuerza hora local; sin eso, en Mérida (UTC-6) "2026-10-01" se vuelve 30 de septiembre
const monthLabel = (m) => {
    const label = new Date(m + "T00:00").toLocaleDateString("es-MX", {
        month: "long",
        year: "numeric",
    });
    return label.charAt(0).toUpperCase() + label.slice(1);
};
</script>

<template>
    <Head title="Rol de misas" />

    <AuthenticatedLayout>
        <template #header>
            <div
                class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4"
            >
                <div>
                    <h2
                        class="font-semibold text-2xl text-gray-800 leading-tight"
                    >
                        <i class="ri-calendar-event-line"></i> Rol de misas
                    </h2>
                </div>
                <Link
                    :href="route('admin.rosters.create')"
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
                    Subir Rol del mes
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div v-if="flashSuccess" class="alert alert-success">
                    <i class="ri-checkbox-circle-line text-xl"></i>
                    <span>{{ flashSuccess }}</span>
                </div>

                <!-- Tabla con DaisyUi -->
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body p-0">
                        <!-- Empty state -->
                        <div
                            v-if="rosters.length === 0"
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
                                No se encontro un rol activo
                            </h3>
                            <p class="text-gray-500 mb-6">Sube tu primer rol del mes</p>
                            <Link
                                :href="route('admin.rosters.create')"
                                class="btn btn-primary"
                            >
                                Subir primer Rol
                            </Link>
                        </div>

                        <!-- Table de Rosters -->

                        <div v-else class="overflow-x-auto">
                            <table class="table table-zebra">
                                <!-- head -->
                                <thead>
                                    <tr>
                                        <th>Mes</th>
                                        <th>Estado</th>
                                        <th>Misas del coro</th>
                                        <th class="text-right">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="roster in rosters"
                                        :key="roster.id"
                                        class="hover"
                                    >
                                        <!-- Month -->
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
                                                                class="ri-calendar-line"
                                                            ></i>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="font-bold">
                                                        {{ monthLabel(roster.month) }}
                                                    </div>
                                                    <div class="text-sm opacity-50">
                                                        <a
                                                            :href="route('admin.rosters.source', roster.id)"
                                                            target="_blank"
                                                            class="link link-hover"
                                                        >
                                                            Ver archivo original
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Estado -->
                                        <td>
                                            <span
                                                v-if="roster.status === 'published'"
                                                class="badge badge-success gap-1"
                                            >
                                                <i class="ri-check-line"></i> Publicado
                                            </span>
                                            <span v-else class="badge badge-warning gap-1">
                                                <i class="ri-draft-line"></i> Borrador
                                            </span>
                                        </td>

                                        <!-- Misas del coro -->
                                        <td>
                                            <span class="font-semibold">{{ roster.choir_count }}</span>
                                            <span class="text-sm opacity-60">
                                                {{ roster.choir_count === 1 ? "misa" : "misas" }}
                                            </span>
                                        </td>

                                        <!-- Acciones -->
                                        <td>
                                            <div class="flex justify-end gap-2">
                                                <button
                                                    v-if="roster.status !== 'published'"
                                                    @click="publishRoster(roster.id)"
                                                    class="btn btn-success btn-xs gap-1"
                                                >
                                                    <i class="ri-send-plane-line text-base"></i>
                                                    Publicar
                                                </button>
                                                <button
                                                    @click="goToEdit(roster.id)"
                                                    class="btn btn-warning btn-xs gap-1"
                                                >
                                                    <i
                                                        class="ri-edit-2-line text-base"
                                                    ></i>
                                                    Revisar
                                                </button>
                                                <button
                                                    @click="
                                                        deleteRoster(
                                                            roster.id
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
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
