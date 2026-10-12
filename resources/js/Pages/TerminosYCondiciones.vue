<script setup>
import { Head, Link } from '@inertiajs/vue3';

// Fecha fija: en un documento legal la "última actualización" debe cambiar
// solo cuando cambia el texto. Actualízala a mano al editar estos términos.

const year = new Date().getFullYear();
const inicioAno = new Date(new Date().getFullYear(), 0, 1).toLocaleDateString('es-MX', { month: 'long' });

const lastUpdated = inicioAno.charAt(0).toUpperCase() + inicioAno.slice(1) + ' ' + year;
</script>

<template>
    <Head title="Términos y Condiciones" />
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
                        Términos y Condiciones
                    </h1>

                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-8">
                        Última actualización: {{ lastUpdated }}
                    </p>

                    <div class="prose prose-lg max-w-none text-gray-700 dark:text-gray-300">
                        <!-- Sección 1 -->
                        <section class="mb-8">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">1. Aceptación de los Términos</h2>
                            <p>
                                Estos Términos y Condiciones regulan el uso de <strong class="text-[#debb75]">Tehila</strong>,
                                cancionero católico disponible como aplicación móvil (Android e iOS) y en
                                <a href="https://tehila.com.mx" class="text-[#debb75] hover:underline">tehila.com.mx</a> (en adelante, "la aplicación").
                                Al descargar, acceder o utilizar la aplicación aceptas estos términos. Si no estás de acuerdo con ellos,
                                te pedimos no utilizarla.
                            </p>
                        </section>

                        <!-- Sección 2 -->
                        <section class="mb-8">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">2. Descripción del Servicio</h2>
                            <p class="mb-4">Tehila es una herramienta de apoyo para coros, ministerios de música y comunidades que permite:</p>
                            <ul class="list-disc pl-6">
                                <li>Consultar letras y acordes de cantos litúrgicos y religiosos.</li>
                                <li>Buscar y filtrar cantos por categoría y tiempo litúrgico.</li>
                                <li>Crear esquemas o repertorios para celebraciones y compartirlos con otras personas.</li>
                                <li>Usar la aplicación sin conexión a internet con los cantos previamente descargados.</li>
                            </ul>
                            <p class="mt-4">
                                La aplicación es <strong>gratuita</strong> y <strong>no contiene publicidad</strong>.
                                Podemos agregar, modificar o retirar funciones en cualquier momento para mejorar el servicio.
                            </p>
                        </section>

                        <!-- Sección 3 -->
                        <section class="mb-8">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">3. Cuenta de Usuario</h2>
                            <ul class="list-disc pl-6">
                                <li>Algunas funciones requieren iniciar sesión con tu cuenta de Google. El acceso se gestiona mediante Firebase Authentication de Google.</li>
                                <li>Eres responsable de mantener la seguridad de la cuenta de Google con la que accedes y de la actividad realizada desde tu dispositivo.</li>
                                <li>La aplicación no está dirigida a menores de 13 años.</li>
                                <li>
                                    Puedes solicitar la eliminación de tu cuenta en cualquier momento desde
                                    <Link :href="route('delete-account')" class="text-[#debb75] hover:underline">esta página</Link>.
                                </li>
                            </ul>
                        </section>

                        <!-- Sección 4 -->
                        <section class="mb-8">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">4. Uso Aceptable</h2>
                            <p class="mb-4">Al utilizar la aplicación te comprometes a:</p>
                            <ul class="list-disc pl-6">
                                <li>Usarla con fines personales, comunitarios, pastorales o litúrgicos, y no con fines comerciales.</li>
                                <li>No copiar, extraer ni redistribuir de forma masiva el contenido del cancionero (por ejemplo, mediante programas automatizados).</li>
                                <li>No intentar acceder sin autorización a los sistemas, a la API o a las cuentas de otros usuarios.</li>
                                <li>No interferir con el funcionamiento normal del servicio ni usarlo para actividades ilícitas.</li>
                            </ul>
                        </section>

                        <!-- Sección 5 -->
                        <section class="mb-8">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">5. Contenido y Derechos de Autor</h2>
                            <p class="mb-4">
                                Las letras, melodías y obras musicales incluidas en el cancionero pertenecen a sus respectivos
                                autores, compositores y editores. Tehila las pone a disposición <strong>sin fines de lucro</strong>,
                                como apoyo para la oración y la celebración litúrgica, y <strong>no reclama la titularidad</strong> sobre ellas.
                            </p>
                            <p class="mb-4">
                                Si eres titular de los derechos de alguna obra y deseas que se corrija su atribución o que sea retirada
                                de la aplicación, escríbenos a <a href="mailto:hola@tehila.com.mx" class="text-[#debb75] hover:underline">hola@tehila.com.mx</a>
                                indicando el título de la obra y los datos que acrediten tu titularidad. Atenderemos tu solicitud a la brevedad.
                            </p>
                            <p>
                                El nombre, el logotipo, el diseño y el software de Tehila son propiedad de sus creadores y no pueden
                                utilizarse sin autorización previa.
                            </p>
                        </section>

                        <!-- Sección 6 -->
                        <section class="mb-8">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">6. Esquemas y Contenido del Usuario</h2>
                            <ul class="list-disc pl-6">
                                <li>Los esquemas o repertorios que creas se guardan <strong>en tu dispositivo</strong>.</li>
                                <li>Si desinstalas la aplicación o borras sus datos, esos esquemas pueden perderse. Te recomendamos compartirlos o respaldarlos si son importantes para ti.</li>
                                <li>Cuando compartes un esquema mediante un enlace, cualquier persona que reciba ese enlace podrá verlo.</li>
                            </ul>
                        </section>

                        <!-- Sección 7 -->
                        <section class="mb-8">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">7. Privacidad</h2>
                            <p>
                                El tratamiento de tus datos personales se describe en nuestro
                                <Link :href="route('aviso-de-privacidad')" class="text-[#debb75] hover:underline">Aviso de Privacidad</Link>,
                                que forma parte de estos Términos y Condiciones.
                            </p>
                        </section>

                        <!-- Sección 8 -->
                        <section class="mb-8">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">8. Disponibilidad y Exactitud</h2>
                            <ul class="list-disc pl-6">
                                <li>La aplicación se ofrece "tal cual" y "según disponibilidad". No garantizamos que funcione sin interrupciones ni errores.</li>
                                <li>Procuramos que las letras, acordes y la información litúrgica sean correctas, pero pueden contener errores. Si encuentras alguno, te agradeceremos que nos lo hagas saber.</li>
                                <li>El tiempo litúrgico sugerido por la aplicación es una ayuda orientativa; la referencia final es el calendario litúrgico de tu diócesis o parroquia.</li>
                            </ul>
                        </section>

                        <!-- Sección 9 -->
                        <section class="mb-8">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">9. Limitación de Responsabilidad</h2>
                            <p>
                                En la medida permitida por la ley, Tehila no será responsable por daños indirectos o pérdidas derivadas
                                del uso o la imposibilidad de uso de la aplicación, incluida la pérdida de esquemas guardados en tu dispositivo.
                            </p>
                        </section>

                        <!-- Sección 10 -->
                        <section class="mb-8">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">10. Suspensión y Terminación</h2>
                            <p>
                                Puedes dejar de usar la aplicación en cualquier momento. Podemos suspender o restringir el acceso
                                a quien incumpla estos términos o haga un uso indebido del servicio.
                            </p>
                        </section>

                        <!-- Sección 11 -->
                        <section class="mb-8">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">11. Cambios en estos Términos</h2>
                            <p>
                                Podemos actualizar estos Términos y Condiciones ocasionalmente. Publicaremos la versión vigente en esta
                                página con su fecha de "última actualización". Si sigues usando la aplicación después de un cambio,
                                se entenderá que aceptas los términos actualizados.
                            </p>
                        </section>

                        <!-- Sección 12 -->
                        <section class="mb-8">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">12. Legislación Aplicable</h2>
                            <p>
                                Estos términos se rigen por las leyes de los Estados Unidos Mexicanos. Para cualquier controversia,
                                las partes se someten a los tribunales competentes de Mérida, Yucatán, México.
                            </p>
                        </section>

                        <!-- Sección 13 -->
                        <section class="mb-4">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">13. Contacto</h2>
                            <p class="mb-4">Si tienes dudas sobre estos Términos y Condiciones, escríbenos:</p>
                            <div class="p-4 bg-gray-100 dark:bg-gray-800 rounded-lg">
                                <ul class="list-none space-y-2">
                                    <li><strong>Correo electrónico:</strong> <a href="mailto:hola@tehila.com.mx" class="text-[#debb75] hover:underline">hola@tehila.com.mx</a></li>
                                    <li><strong>Sitio web:</strong> <a href="https://tehila.com.mx" target="_blank" class="text-[#debb75] hover:underline">tehila.com.mx</a></li>
                                </ul>
                            </div>
                        </section>
                    </div>
                </main>

                <!-- Footer -->
                <footer class="text-center text-sm text-black dark:text-white/70 mt-8">
                    <div class="mb-2 space-x-4">
                        <Link :href="route('aviso-de-privacidad')" class="hover:underline">Aviso de Privacidad</Link>
                        <Link :href="route('delete-account')" class="hover:underline">Eliminar cuenta</Link>
                    </div>
                    Copyright © {{ new Date().getFullYear() }} Tehila. Todos los derechos reservados.
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
