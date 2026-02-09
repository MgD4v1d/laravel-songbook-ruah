<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const form = useForm({
    email: '',
});

const submitted = ref(false);

const submit = () => {
    form.post(route('delete-account.request'), {
        onSuccess: () => {
            submitted.value = true;
            form.reset();
        },
    });
};
</script>

<template>
    <Head title="Eliminar Cuenta - Tehila" />
    <div class="bg-gray-50 text-black/50 dark:bg-black dark:text-white/50 min-h-screen relative">
        <div
            id="background"
            class="absolute inset-0 bg-repeat"
            style="background-image: url('https://tehila.com.mx/images/endless-constellation.svg');"
        ></div>

        <div class="relative flex min-h-screen flex-col items-center selection:bg-[#debb75] selection:text-white">
            <div class="relative w-full max-w-5xl px-4 py-12">
                <!-- Header con Logo -->
                <header class="flex justify-center mb-8">
                    <Link href="/">
                        <svg
                            class="h-16 text-white lg:text-[#debb75]"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 1920 1080"
                        >
                            <path d="M69.08,471.08H7.38l183.76-201.2v99.93h101.27v101.27h-101.27v87.86c0,36.89,27.5,50.3,55.67,50.3,17.44,0,34.2-5.37,45.61-12.74v107.31c-26.83,13.41-54.32,19.45-80.48,19.45-78.47,0-142.85-56.34-142.85-147.55v-104.62Z"/>
                            <path d="M684.75,584.42h-237.42c15.43,36.89,50.97,40.24,75.79,40.24s46.28-8.05,63.04-24.14l83.16,58.35c-34.2,43.59-87.19,64.38-148.89,64.38-119.38,0-193.82-79.81-193.82-182.42s76.46-181.08,184.43-181.08,178.4,77.8,178.4,181.75c0,14.75-1.34,28.84-4.69,42.92ZM577.44,505.28c-10.73-36.22-37.56-48.96-65.05-48.96s-56.34,14.75-67.74,48.96h132.79Z"/>
                            <path d="M1100.56,499.92v213.27h-123.4v-185.78c0-38.9-20.12-57.68-53.65-57.68-40.91,0-58.35,30.18-58.35,67.74v175.72h-123.4V241.04h123.4v157.61c20.79-23.47,54.32-38.9,100.6-38.9,88.53,0,134.8,57.01,134.8,140.17Z"/>
                            <path fill="currentColor" d="M1148.85,259.82c0-46.28,38.23-79.14,79.14-79.14,42.92,0,79.81,36.22,79.81,79.14s-36.22,79.14-79.81,79.14-79.14-33.53-79.14-79.14ZM1166.96,713.19v-343.38h54.32v343.38h-54.32ZM1276.27,259.82c0-26.16-22.13-48.96-48.29-48.96s-47.62,22.13-47.62,48.96,20.79,48.96,47.62,48.96,48.29-22.8,48.29-48.96ZM1193.78,259.82c0-20.12,15.43-36.89,34.2-36.89s34.88,17.44,34.88,36.89-16.77,36.89-34.88,36.89c-19.45,0-34.2-16.1-34.2-36.89ZM1236.03,713.19v-343.38h54.32v343.38h-54.32Z"/>
                            <path fill="currentColor" d="M1360.11,713.19V241.04h54.32v472.15h-54.32ZM1429.18,713.19V241.04h54.32v472.15h-54.32Z"/>
                            <path fill="currentColor" d="M1800.73,396.63l50.3-18.78v335.34h-51.64v-25.49c-26.83,25.49-63.71,35.54-93.22,35.54-101.94,0-169.68-84.5-169.68-179.07,0-99.93,70.42-184.43,173.03-184.43,27.5,0,64.38,9.39,91.21,36.89ZM1799.39,644.11v-15.42c-22.13,18.78-45.61,32.19-83.16,32.19-65.73,0-112-50.3-112-122.06,0-67.74,45.61-117.37,108.65-117.37,35.54,0,63.71,10.73,86.52,32.86v-16.1c-23.47-20.12-51.64-30.18-86.52-30.18-71.76,0-123.4,55-123.4,130.78,0,79.81,52.31,135.48,126.76,135.48,31.52,0,54.99-8.72,80.48-28.17l2.68-2.01ZM1802.74,541.5c0-40.24-31.52-71.09-72.43-71.09s-73.1,30.85-73.1,71.09,31.52,71.09,73.1,71.09,72.43-30.85,72.43-71.09ZM1865.79,372.49l50.97-19.45v360.15h-50.97v-340.7Z"/>
                        </svg>
                    </Link>
                </header>

                <!-- Contenido Principal -->
                <main class="bg-white/90 dark:bg-gray-900/90 backdrop-blur-sm rounded-2xl shadow-xl p-8 md:p-12">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                        Solicitud de Eliminación de Cuenta
                    </h1>

                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-8">
                        Tehila &mdash; Gestión de cuenta y datos personales
                    </p>

                    <div class="prose prose-lg max-w-none text-gray-700 dark:text-gray-300">

                        <!-- Mensaje de éxito -->
                        <div v-if="submitted" class="mb-8 p-6 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 rounded-xl">
                            <div class="flex items-center gap-3 mb-2">
                                <svg class="w-6 h-6 text-green-600 dark:text-green-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <h3 class="text-lg font-semibold text-green-800 dark:text-green-300 m-0">Solicitud recibida</h3>
                            </div>
                            <p class="text-green-700 dark:text-green-400 m-0">
                                Hemos recibido tu solicitud de eliminación de cuenta. Tu cuenta y datos asociados serán eliminados
                                en un plazo máximo de <strong>30 días hábiles</strong>. Recibirás una confirmación por correo electrónico
                                cuando el proceso haya sido completado.
                            </p>
                        </div>

                        <!-- Sección informativa -->
                        <section class="mb-8">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Acerca de la eliminación de cuenta</h2>
                            <p>
                                En <strong class="text-[#debb75]">Tehila</strong>, respetamos tu derecho a controlar tus datos personales.
                                Si deseas eliminar tu cuenta, puedes hacerlo mediante el formulario que se encuentra más abajo.
                                Una vez procesada tu solicitud, la eliminación es permanente e irreversible.
                            </p>
                        </section>

                        <!-- Datos que se eliminan -->
                        <section class="mb-8">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Datos que se eliminan</h2>
                            <p class="mb-4">Al procesar tu solicitud, se eliminarán de forma permanente los siguientes datos:</p>
                            <ul class="list-disc pl-6">
                                <li><strong>Nombre y correo electrónico</strong> asociados a tu cuenta.</li>
                                <li><strong>Foto de perfil</strong> (si fue proporcionada mediante Google Sign-In).</li>
                                <li><strong>Repertorios</strong> creados por el usuario dentro de la aplicación.</li>
                                <li><strong>Datos de actividad y preferencias</strong> almacenadas en la aplicación.</li>
                                <li><strong>Tokens de autenticación</strong> y datos de sesión de Firebase.</li>
                            </ul>
                        </section>

                        <!-- Plazo de eliminación -->
                        <section class="mb-8">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Plazo de eliminación</h2>
                            <p>
                                Tu cuenta y todos los datos asociados serán eliminados en un plazo máximo de
                                <strong class="text-[#debb75]">30 días hábiles</strong> a partir de la recepción de tu solicitud.
                                Recibirás una confirmación por correo electrónico una vez que el proceso haya sido completado.
                            </p>
                        </section>

                        <!-- Datos que se conservan -->
                        <section class="mb-8">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Datos que se conservan</h2>
                            <p>
                                La eliminación de tu cuenta es <strong>completa</strong>. No conservamos ningún dato personal
                                después de procesar tu solicitud, salvo en los siguientes casos excepcionales:
                            </p>
                            <ul class="list-disc pl-6 mt-4">
                                <li>Información requerida por <strong>obligaciones legales o fiscales</strong> vigentes.</li>
                                <li>Registros necesarios para la <strong>prevención de fraude o seguridad</strong> de la plataforma (anonimizados).</li>
                            </ul>
                            <p class="mt-4 text-sm text-gray-500 dark:text-gray-400 p-3 bg-gray-100 dark:bg-gray-800 rounded-lg">
                                <strong>Nota:</strong> Si no aplica ninguna excepción legal, la eliminación de tus datos será total y permanente.
                            </p>
                        </section>

                        <!-- Formulario -->
                        <section class="mb-8 p-6 bg-[#debb75]/10 dark:bg-[#debb75]/20 rounded-xl border border-[#debb75]/30">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Solicitar eliminación de cuenta</h2>
                            <p class="mb-6">
                                Ingresa el correo electrónico asociado a tu cuenta de <strong class="text-[#debb75]">Tehila</strong>
                                para enviar tu solicitud de eliminación.
                            </p>

                            <form @submit.prevent="submit" v-if="!submitted">
                                <div class="mb-4">
                                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Correo electrónico de tu cuenta
                                    </label>
                                    <input
                                        id="email"
                                        type="email"
                                        v-model="form.email"
                                        required
                                        placeholder="tu@correo.com"
                                        class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-[#debb75] focus:border-[#debb75] outline-none transition"
                                    />
                                    <p v-if="form.errors.email" class="mt-2 text-sm text-red-600 dark:text-red-400">
                                        {{ form.errors.email }}
                                    </p>
                                </div>

                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="w-full sm:w-auto px-8 py-3 bg-red-600 hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed text-white font-semibold rounded-lg transition-colors duration-200 flex items-center justify-center gap-2"
                                >
                                    <svg v-if="form.processing" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span>{{ form.processing ? 'Enviando...' : 'Solicitar eliminación de cuenta' }}</span>
                                </button>
                            </form>

                            <div v-else class="text-center py-4">
                                <p class="text-green-700 dark:text-green-400 font-medium">
                                    Tu solicitud ha sido enviada correctamente.
                                </p>
                            </div>
                        </section>

                        <!-- Contacto de soporte -->
                        <section class="mb-4">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Contacto de soporte</h2>
                            <p class="mb-4">
                                Si tienes preguntas sobre el proceso de eliminación o necesitas dar seguimiento a tu solicitud,
                                puedes contactarnos a través de:
                            </p>
                            <div class="p-4 bg-gray-100 dark:bg-gray-800 rounded-lg">
                                <ul class="list-none space-y-2">
                                    <li><strong>Correo electrónico:</strong> <a href="mailto:support@tehila.com.mx" class="text-[#debb75] hover:underline">support@tehila.com.mx</a></li>
                                    <li><strong>Sitio web:</strong> <a href="https://tehila.com.mx" target="_blank" class="text-[#debb75] hover:underline">tehila.com.mx</a></li>
                                </ul>
                            </div>
                        </section>
                    </div>
                </main>

                <!-- Footer -->
                <footer class="text-center text-sm text-black dark:text-white/70 mt-8">
                    Copyright &copy; {{ new Date().getFullYear() }} Tehila. Todos los derechos reservados.
                </footer>
            </div>
        </div>
    </div>
</template>

<style scoped>
.prose ul {
    margin-top: 0.5rem;
    margin-bottom: 0.5rem;
}

.prose li {
    margin-top: 0.25rem;
    margin-bottom: 0.25rem;
}

.prose p {
    margin-bottom: 1rem;
}
</style>
