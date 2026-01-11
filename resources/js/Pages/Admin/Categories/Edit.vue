<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

const { category } = defineProps({
    category: Object
});

const form = useForm({
    name: category.name,
    slug: category.slug,
    description: category.description || '',
    order: category.order || 0
});

const returnUrl = ref(null);

onMounted(() => {
    returnUrl.value = localStorage.getItem('categories_return_url');
});

// Generar slug automáticamente desde el nombre
const generateSlug = () => {
    if (form.name) {
        form.slug = form.name
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '') // Eliminar acentos
            .replace(/[^\w\s-]/g, '') // Eliminar caracteres especiales
            .replace(/\s+/g, '-') // Reemplazar espacios con guiones
            .replace(/-+/g, '-') // Reemplazar múltiples guiones con uno solo
            .trim();
    }
};

const submit = () => {
    form.put(route('admin.categories.update', category.id), {
        onSuccess: () => {
            if (returnUrl.value) {
                localStorage.removeItem('categories_return_url');
                router.visit(returnUrl.value);
            }
        }
    });
};

const goBack = () => {
    if (returnUrl.value) {
        localStorage.removeItem('categories_return_url');
        router.visit(returnUrl.value);
    } else {
        router.visit(route('admin.categories.index'));
    }
};
</script>

<template>
    <Head :title="`Editar : ${category.name}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Editar Categoría
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">
                        {{ category.name }}
                    </p>
                </div>
                <button
                    @click="goBack"
                    class="btn btn-neutral gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Volver
                </button>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit" class="p-6 space-y-6">

                        <!-- Información de la Categoría -->
                        <div class="card bg-base-100 shadow-xl">
                            <div class="card-body">
                                <h3 class="card-title">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Información de la Categoría
                                </h3>

                                <div class="divider"></div>

                                <div class="space-y-4">
                                    <!-- Nombre -->
                                    <div class="form-control w-full">
                                        <label class="label">
                                            <span class="label-text font-semibold">Nombre *</span>
                                        </label>
                                        <input
                                            v-model="form.name"
                                            @input="generateSlug"
                                            type="text"
                                            placeholder="Nombre de la categoría"
                                            class="input input-bordered w-full"
                                            :class="{ 'input-error': form.errors.name }"
                                            required
                                        />
                                        <label v-if="form.errors.name" class="label">
                                            <span class="label-text-alt text-error">{{ form.errors.name }}</span>
                                        </label>
                                    </div>

                                    <!-- Slug -->
                                    <div class="form-control w-full">
                                        <label class="label">
                                            <span class="label-text font-semibold">Slug *</span>
                                            <span class="label-text-alt">URL amigable</span>
                                        </label>
                                        <input
                                            v-model="form.slug"
                                            type="text"
                                            placeholder="slug-de-la-categoria"
                                            class="input input-bordered w-full font-mono"
                                            :class="{ 'input-error': form.errors.slug }"
                                            required
                                        />
                                        <label class="label">
                                            <span class="label-text-alt">Este será parte de la URL</span>
                                            <span v-if="form.errors.slug" class="label-text-alt text-error">
                                                {{ form.errors.slug }}
                                            </span>
                                        </label>
                                    </div>

                                    <!-- Descripción -->
                                    <div class="form-control w-full">
                                        <label class="label">
                                            <span class="label-text font-semibold">Descripción (Opcional)</span>
                                        </label>
                                        <textarea
                                            v-model="form.description"
                                            class="textarea textarea-bordered h-24"
                                            :class="{ 'textarea-error': form.errors.description }"
                                            placeholder="Breve descripción de la categoría"
                                        ></textarea>
                                        <label v-if="form.errors.description" class="label">
                                            <span class="label-text-alt text-error">{{ form.errors.description }}</span>
                                        </label>
                                    </div>

                                    <!-- Orden -->
                                    <div class="form-control w-full">
                                        <label class="label">
                                            <span class="label-text font-semibold">Orden</span>
                                            <span class="label-text-alt">Controla el orden de visualización</span>
                                        </label>
                                        <input
                                            v-model.number="form.order"
                                            type="number"
                                            min="0"
                                            class="input input-bordered w-full"
                                            :class="{ 'input-error': form.errors.order }"
                                        />
                                        <label class="label">
                                            <span class="label-text-alt">Menor número = mayor prioridad</span>
                                            <span v-if="form.errors.order" class="label-text-alt text-error">
                                                {{ form.errors.order }}
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botones de acción -->
                        <div class="card bg-base-100 shadow-xl">
                            <div class="card-body">
                                <div class="flex justify-between items-center">
                                    <button
                                        type="button"
                                        @click="$inertia.delete(route('admin.categories.destroy', category.id), {
                                            onBefore: () => confirm('¿Estás seguro de eliminar esta categoría?'),
                                            onSuccess: () => {
                                                if (returnUrl.value) {
                                                    localStorage.removeItem('categories_return_url');
                                                    router.visit(returnUrl.value);
                                                }
                                            }
                                        })"
                                        class="btn btn-error gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Eliminar Categoría
                                    </button>

                                    <div class="flex gap-3">
                                        <button
                                            type="button"
                                            @click="goBack"
                                            class="btn btn-ghost">
                                            Cancelar
                                        </button>
                                        <button
                                            type="submit"
                                            :disabled="form.processing"
                                            class="btn btn-primary gap-2">
                                            <span v-if="!form.processing">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                                                </svg>
                                            </span>
                                            <span v-if="form.processing" class="loading loading-spinner"></span>
                                            {{ form.processing ? 'Guardando...' : 'Guardar Cambios' }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </AuthenticatedLayout>
</template>
