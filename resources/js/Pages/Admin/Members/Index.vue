<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    members: Array,
});

const page = usePage();
const flashSuccess = computed(() => page.props.flash?.success);

const activeCount = computed(() => props.members.filter((m) => m.active).length);

const form = useForm({ name: '', email: '' });

const addMember = () => {
    form.post(route('admin.members.store'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};

const toggleActive = (member) => {
    router.patch(
        route('admin.members.update', member.id),
        { active: !member.active },
        { preserveScroll: true },
    );
};

const deleteMember = (member) => {
    if (confirm(`¿Eliminar a ${member.name}? Si solo dejó el coro, mejor desactívalo.`)) {
        router.delete(route('admin.members.destroy', member.id), { preserveScroll: true });
    }
};
</script>

<template>
    <Head title="Miembros" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                    <i class="ri-team-line"></i> Miembros del coro
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    {{ members.length }} {{ members.length === 1 ? 'miembro' : 'miembros' }} ·
                    {{ activeCount }} {{ activeCount === 1 ? 'activo' : 'activos' }} ·
                    solo los activos pueden ver el rol en la app
                </p>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div v-if="flashSuccess" class="alert alert-success">
                    <i class="ri-checkbox-circle-line text-xl"></i>
                    <span>{{ flashSuccess }}</span>
                </div>

                <!-- Agregar -->
                <div class="card bg-base-100 shadow-xl">
                    <form class="card-body" @submit.prevent="addMember">
                        <h3 class="font-semibold text-gray-800">Agregar miembro</h3>
                        <p class="text-sm text-gray-500 -mt-1">
                            Usa el correo de la cuenta de Google con la que entra a la app.
                        </p>
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-3 items-start">
                            <div class="md:col-span-2">
                                <input
                                    v-model="form.name"
                                    type="text"
                                    placeholder="Nombre"
                                    class="input input-bordered w-full"
                                    :class="{ 'input-error': form.errors.name }"
                                />
                                <p v-if="form.errors.name" class="text-error text-xs mt-1">{{ form.errors.name }}</p>
                            </div>
                            <div class="md:col-span-2">
                                <input
                                    v-model="form.email"
                                    type="email"
                                    placeholder="correo@gmail.com"
                                    class="input input-bordered w-full"
                                    :class="{ 'input-error': form.errors.email }"
                                />
                                <p v-if="form.errors.email" class="text-error text-xs mt-1">{{ form.errors.email }}</p>
                            </div>
                            <button type="submit" class="btn btn-primary gap-2" :disabled="form.processing">
                                <span v-if="form.processing" class="loading loading-spinner loading-sm"></span>
                                <i v-else class="ri-user-add-line text-lg"></i>
                                Agregar
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Lista -->
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body p-0">
                        <p v-if="members.length === 0" class="text-center text-gray-500 py-12">
                            Aún no hay miembros. Agrega al primero arriba.
                        </p>

                        <div v-else class="overflow-x-auto">
                            <table class="table table-zebra">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Correo</th>
                                        <th>Activo</th>
                                        <th class="text-right">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="member in members" :key="member.id" :class="{ 'opacity-50': !member.active }">
                                        <td class="font-semibold">{{ member.name }}</td>
                                        <td>{{ member.email }}</td>
                                        <td>
                                            <input
                                                type="checkbox"
                                                class="toggle toggle-success toggle-sm"
                                                :checked="member.active"
                                                :aria-label="`Activo: ${member.name}`"
                                                @change="toggleActive(member)"
                                            />
                                        </td>
                                        <td class="text-right">
                                            <button class="btn btn-error btn-xs gap-1" @click="deleteMember(member)">
                                                <i class="ri-delete-bin-line text-base"></i>
                                                Eliminar
                                            </button>
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
