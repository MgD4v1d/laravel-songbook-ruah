<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    file: null,
});

// Inertia detecta el File y lo envía como multipart/form-data
const submit = () => form.post(route('admin.rosters.store'));
</script>

<template>
    <Head title="Subir Rol" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    <i class="ri-disc-line"></i> Subir Rol
                </h2>
                <Link :href="route('admin.rosters.index')"
                      class="btn btn-ghost btn-sm gap-2">
                      <i class="ri-arrow-left-long-line text-lg"></i>
                    Volver
                </Link>
            </div>
        </template>
        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit" class="p-6 space-y-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">Rol del mes</h3>
                            <p class="text-sm text-gray-500 mt-1">
                                Sube el PDF o la foto del rol que manda la parroquia. Se leerá automáticamente
                                y después podrás revisarlo y corregirlo antes de publicarlo en la app.
                            </p>
                        </div>

                        <div class="form-control w-full">
                            <input
                                type="file"
                                accept=".pdf,image/*"
                                class="file-input file-input-bordered w-full"
                                :class="{ 'file-input-error': form.errors.file }"
                                :disabled="form.processing"
                                @input="form.file = $event.target.files[0]"
                            />
                            <label class="label">
                                <span v-if="form.errors.file" class="label-text-alt text-error">
                                    {{ form.errors.file }}
                                </span>
                                <span v-else class="label-text-alt text-gray-500">
                                    PDF, JPG, PNG o WEBP · máximo 10 MB
                                </span>
                            </label>
                        </div>

                        <div v-if="form.processing" class="alert alert-info">
                            <span class="loading loading-spinner loading-md"></span>
                            <span>Leyendo el rol… puede tardar hasta un minuto. No cierres esta página.</span>
                        </div>

                        <div class="flex justify-end gap-2">
                            <Link :href="route('admin.rosters.index')" class="btn btn-ghost">
                                Cancelar
                            </Link>
                            <button
                                type="submit"
                                class="btn btn-primary gap-2"
                                :disabled="form.processing || !form.file"
                            >
                                <span v-if="form.processing" class="loading loading-spinner loading-sm"></span>
                                <i v-else class="ri-upload-2-line text-lg"></i>
                                {{ form.processing ? 'Leyendo…' : 'Subir y leer' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
