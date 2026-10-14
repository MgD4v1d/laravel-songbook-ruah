<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

const props = defineProps({
    roster: Object,
    choir: String,
    sourceUrl: String,
});

const page = usePage();
const flashSuccess = computed(() => page.props.flash?.success);

const STORAGE_KEY = 'rosters.onlyOurs';

const form = useForm({
    entries: props.roster.entries.map((e) => ({
        date: e.date,
        time: e.time.slice(0, 5), // MySQL regresa "19:00:00"; la validación espera "19:00"
        choir: e.choir,
        celebration: e.celebration ?? '',
    })),
});

const onlyOurs = ref(localStorage.getItem(STORAGE_KEY) === '1');

// ¿La fila es de nuestro coro? (acepta "RUAH", "Coro Ruah", etc.)
const isOurs = (entry) =>
    (entry.choir ?? '').toLowerCase().includes(props.choir.toLowerCase());

const oursCount = computed(() => form.entries.filter(isOurs).length);

// "T00:00" fuerza hora local (ver Index.vue)
const monthLabel = computed(() => {
    const label = new Date(props.roster.month + 'T00:00').toLocaleDateString('es-MX', {
        month: 'long',
        year: 'numeric',
    });
    return label.charAt(0).toUpperCase() + label.slice(1);
});

watch(onlyOurs, (value) => localStorage.setItem(STORAGE_KEY, value ? '1' : '0'));

// Día de la semana junto a la fecha, para revisar rápido (sáb 7:00 pm, dom 9:30…)
const weekday = (date) => {
    if (!date) return '';
    return new Date(date + 'T00:00').toLocaleDateString('es-MX', { weekday: 'short' });
};

const addEntry = () => {
    form.entries.push({ date: '', time: '', choir: props.choir, celebration: '' });
};

const removeEntry = (index) => {
    form.entries.splice(index, 1);
};

const save = () => {
    form.put(route('admin.rosters.update', props.roster.id), {
        preserveScroll: true,
        preserveState: true,
        // Al guardar, lo guardado pasa a ser el nuevo "original" (isDirty = false)
        onSuccess: () => form.defaults(),
    });
};

const publish = () => {
    if (form.isDirty) {
        alert('Tienes cambios sin guardar. Guárdalos antes de publicar.');
        return;
    }
    if (confirm('¿Publicar este rol? Quedará visible en la app.')) {
        router.post(route('admin.rosters.publish', props.roster.id));
    }
};

const rowError = (index, field) => form.errors[`entries.${index}.${field}`];
</script>

<template>
    <Head :title="`Rol ${monthLabel}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                        <i class="ri-calendar-event-line"></i> Rol de {{ monthLabel }}
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">
                        {{ form.entries.length }} misas en total ·
                        <strong>{{ oursCount }}</strong> de {{ choir }}
                        <span
                            class="badge badge-sm ml-2"
                            :class="roster.status === 'published' ? 'badge-success' : 'badge-warning'"
                        >
                            {{ roster.status === 'published' ? 'Publicado' : 'Borrador' }}
                        </span>
                    </p>
                </div>
                <div class="flex gap-2">
                    <Link :href="route('admin.rosters.index')" class="btn btn-ghost btn-sm gap-2">
                        <i class="ri-arrow-left-long-line text-lg"></i>
                        Volver
                    </Link>
                    <button
                        class="btn btn-primary btn-sm gap-2"
                        :disabled="form.processing || !form.isDirty"
                        @click="save"
                    >
                        <span v-if="form.processing" class="loading loading-spinner loading-xs"></span>
                        <i v-else class="ri-save-line text-lg"></i>
                        Guardar
                    </button>
                    <button
                        v-if="roster.status !== 'published'"
                        class="btn btn-success btn-sm gap-2"
                        :disabled="form.processing"
                        @click="publish"
                    >
                        <i class="ri-send-plane-line text-lg"></i>
                        Publicar
                    </button>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-[1600px] mx-auto sm:px-6 lg:px-8 space-y-4">
                <div v-if="flashSuccess" class="alert alert-success">
                    <i class="ri-checkbox-circle-line text-xl"></i>
                    <span>{{ flashSuccess }}</span>
                </div>

                <div v-if="form.isDirty" class="alert alert-warning">
                    <i class="ri-error-warning-line text-xl"></i>
                    <span>Tienes cambios sin guardar.</span>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Archivo original -->
                    <div class="card bg-base-100 shadow-xl lg:sticky lg:top-4 self-start">
                        <div class="card-body p-4">
                            <div class="flex justify-between items-center mb-2">
                                <h3 class="font-semibold text-gray-800">Archivo original</h3>
                                <a :href="sourceUrl" target="_blank" class="link link-hover text-sm">
                                    Abrir en otra pestaña <i class="ri-external-link-line"></i>
                                </a>
                            </div>
                            <iframe
                                :src="sourceUrl"
                                class="w-full h-[75vh] rounded-lg border border-base-300 bg-base-200"
                                title="Rol original"
                            ></iframe>
                        </div>
                    </div>

                    <!-- Tabla editable -->
                    <div class="card bg-base-100 shadow-xl">
                        <div class="card-body p-4">
                            <div class="flex flex-wrap justify-between items-center gap-2 mb-2">
                                <label class="label cursor-pointer gap-2">
                                    <input v-model="onlyOurs" type="checkbox" class="toggle toggle-primary toggle-sm" />
                                    <span class="label-text">Solo {{ choir }}</span>
                                </label>
                                <button class="btn btn-outline btn-sm gap-1" @click="addEntry">
                                    <i class="ri-add-line"></i> Agregar misa
                                </button>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Hora</th>
                                            <th>Coro</th>
                                            <th>Celebración</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- v-for sobre TODAS las filas y v-show para filtrar:
                                             así el índice i siempre corresponde a form.entries[i] -->
                                        <tr
                                            v-for="(entry, i) in form.entries"
                                            v-show="!onlyOurs || isOurs(entry)"
                                            :key="i"
                                            :class="{ 'bg-primary/10': isOurs(entry) }"
                                        >
                                            <td class="min-w-[170px]">
                                                <div class="flex items-center gap-1">
                                                    <input
                                                        v-model="entry.date"
                                                        type="date"
                                                        class="input input-bordered input-sm w-full"
                                                        :class="{ 'input-error': rowError(i, 'date') }"
                                                    />
                                                    <span class="text-xs opacity-60 w-8">{{ weekday(entry.date) }}</span>
                                                </div>
                                                <p v-if="rowError(i, 'date')" class="text-error text-xs mt-1">
                                                    {{ rowError(i, 'date') }}
                                                </p>
                                            </td>
                                            <td class="min-w-[110px]">
                                                <input
                                                    v-model="entry.time"
                                                    type="time"
                                                    class="input input-bordered input-sm w-full"
                                                    :class="{ 'input-error': rowError(i, 'time') }"
                                                />
                                                <p v-if="rowError(i, 'time')" class="text-error text-xs mt-1">
                                                    {{ rowError(i, 'time') }}
                                                </p>
                                            </td>
                                            <td class="min-w-[140px]">
                                                <input
                                                    v-model="entry.choir"
                                                    type="text"
                                                    class="input input-bordered input-sm w-full"
                                                    :class="{
                                                        'input-error': rowError(i, 'choir'),
                                                        'font-semibold': isOurs(entry),
                                                    }"
                                                />
                                                <p v-if="rowError(i, 'choir')" class="text-error text-xs mt-1">
                                                    {{ rowError(i, 'choir') }}
                                                </p>
                                            </td>
                                            <td class="min-w-[150px]">
                                                <input
                                                    v-model="entry.celebration"
                                                    type="text"
                                                    placeholder="—"
                                                    class="input input-bordered input-sm w-full"
                                                />
                                            </td>
                                            <td>
                                                <button
                                                    class="btn btn-ghost btn-xs text-error"
                                                    title="Quitar misa"
                                                    @click="removeEntry(i)"
                                                >
                                                    <i class="ri-delete-bin-line text-base"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                                <p v-if="form.entries.length === 0" class="text-center text-gray-500 py-8">
                                    Este rol no tiene misas. Agrega una con "Agregar misa".
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
