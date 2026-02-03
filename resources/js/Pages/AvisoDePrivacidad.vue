<script setup>
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Aviso de Privacidad" />
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
                        Aviso de Privacidad
                    </h1>

                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-8">
                        Última actualización: Enero 2025
                    </p>

                    <div class="prose prose-lg max-w-none text-gray-700 dark:text-gray-300">
                        <!-- Sección 1 -->
                        <section class="mb-8">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">1. Introducción</h2>
                            <p>
                                Bienvenido a <strong class="text-[#debb75]">Tehila</strong>. Nos comprometemos a proteger tu privacidad y tus datos personales.
                                Este Aviso de Privacidad describe cómo recopilamos, usamos, almacenamos y protegemos tu información cuando
                                utilizas nuestra aplicación web y móvil disponible en <a href="https://tehila.com.mx" class="text-[#debb75] hover:underline">tehila.com.mx</a>.
                            </p>
                        </section>

                        <!-- Sección 2 -->
                        <section class="mb-8">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">2. Responsable del Tratamiento de Datos</h2>
                            <p>
                                <strong class="text-[#debb75]">Tehila</strong> es el responsable del tratamiento de tus datos personales.
                                Para cualquier consulta relacionada con privacidad, puedes contactarnos a:
                            </p>
                            <ul class="list-disc pl-6 mt-2">
                                <li><strong>Sitio web:</strong> <a href="https://tehila.com.mx" class="text-[#debb75] hover:underline">tehila.com.mx</a></li>
                                <li><strong>Correo electrónico:</strong> <a href="mailto:hola@tehila.com.mx" class="text-[#debb75] hover:underline">hola@tehila.com.mx</a></li>
                            </ul>
                        </section>

                        <!-- Sección 3 -->
                        <section class="mb-8">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">3. Datos que Recopilamos</h2>
                            <p class="mb-4">Recopilamos únicamente la información mínima necesaria para el funcionamiento de la aplicación:</p>

                            <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-2">3.1 Datos proporcionados por el usuario o mediante inicio de sesión:</h3>
                            <ul class="list-disc pl-6 mb-4">
                                <li><strong>Nombre:</strong> Para personalizar tu experiencia en la aplicación.</li>
                                <li><strong>Correo electrónico:</strong> Para crear tu cuenta, iniciar sesión y comunicaciones importantes.</li>
                                <li><strong>Foto de perfil:</strong> Si inicias sesión con Google, podemos recibir tu foto de perfil de Google.</li>
                                <li><strong>Identificador único:</strong> Un ID proporcionado por Google/Firebase para vincular tu cuenta.</li>
                            </ul>

                            <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-2">3.2 Datos obtenidos mediante Google Sign-In:</h3>
                            <p class="mb-2">Si eliges iniciar sesión con tu cuenta de Google, recibiremos:</p>
                            <ul class="list-disc pl-6 mb-4">
                                <li>Tu nombre completo asociado a tu cuenta de Google.</li>
                                <li>Tu dirección de correo electrónico de Google.</li>
                                <li>Tu foto de perfil de Google (si está disponible).</li>
                                <li>Un identificador único de usuario.</li>
                            </ul>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4 p-3 bg-gray-100 dark:bg-gray-800 rounded-lg">
                                <strong>Nota:</strong> No tenemos acceso a tu contraseña de Google ni a otros datos de tu cuenta de Google más allá de los mencionados.
                            </p>

                            <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-2">3.3 Datos recopilados automáticamente:</h3>
                            <ul class="list-disc pl-6">
                                <li><strong>Datos de sesión:</strong> Información técnica necesaria para mantener tu sesión activa.</li>
                                <li><strong>Dirección IP:</strong> Para propósitos de seguridad y prevención de fraude.</li>
                                <li><strong>Información del dispositivo:</strong> Tipo de navegador y sistema operativo para optimizar la experiencia.</li>
                                <li><strong>Datos de Firebase:</strong> Tokens de autenticación y datos analíticos básicos del uso de la aplicación.</li>
                            </ul>
                        </section>

                        <!-- Sección 4 -->
                        <section class="mb-8">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">4. Finalidad del Tratamiento</h2>
                            <p class="mb-4">Utilizamos tus datos personales para:</p>
                            <ul class="list-disc pl-6">
                                <li>Crear y administrar tu cuenta de usuario.</li>
                                <li>Proporcionar acceso a las funcionalidades de la aplicación.</li>
                                <li>Personalizar tu experiencia dentro de la aplicación.</li>
                                <li>Enviar comunicaciones importantes sobre tu cuenta o cambios en el servicio.</li>
                                <li>Garantizar la seguridad de la plataforma y prevenir actividades fraudulentas.</li>
                                <li>Cumplir con obligaciones legales aplicables.</li>
                            </ul>
                        </section>

                        <!-- Sección 5 -->
                        <section class="mb-8">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">5. Base Legal para el Tratamiento</h2>
                            <p class="mb-4">El tratamiento de tus datos se fundamenta en:</p>
                            <ul class="list-disc pl-6">
                                <li><strong>Consentimiento:</strong> Al registrarte y aceptar este aviso de privacidad.</li>
                                <li><strong>Ejecución de contrato:</strong> Para proporcionarte los servicios de la aplicación.</li>
                                <li><strong>Interés legítimo:</strong> Para mejorar la seguridad y funcionalidad del servicio.</li>
                            </ul>
                        </section>

                        <!-- Sección 6 -->
                        <section class="mb-8">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">6. Compartición de Datos</h2>
                            <p class="mb-4">
                                <strong>No vendemos, alquilamos ni compartimos tu información personal con terceros</strong> para fines comerciales o de marketing.
                            </p>
                            <p class="mb-4">Compartimos información con los siguientes proveedores de servicios esenciales:</p>

                            <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-2">6.1 Google y Firebase:</h3>
                            <ul class="list-disc pl-6 mb-4">
                                <li><strong>Firebase Authentication:</strong> Utilizamos Firebase de Google para gestionar la autenticación de usuarios. Esto incluye el almacenamiento seguro de credenciales y tokens de sesión.</li>
                                <li><strong>Google Sign-In:</strong> Si eliges iniciar sesión con Google, tus datos de autenticación son procesados por Google conforme a su <a href="https://policies.google.com/privacy" target="_blank" class="text-[#debb75] hover:underline">Política de Privacidad</a>.</li>
                            </ul>

                            <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-2">6.2 Otros casos de compartición:</h3>
                            <ul class="list-disc pl-6">
                                <li>Cuando sea requerido por ley o autoridades competentes.</li>
                                <li>Para proteger nuestros derechos, privacidad, seguridad o propiedad.</li>
                                <li>Con proveedores de servicios que nos ayudan a operar la aplicación (bajo acuerdos de confidencialidad).</li>
                            </ul>
                        </section>

                        <!-- Sección 7 -->
                        <section class="mb-8">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">7. Seguridad de los Datos</h2>
                            <p class="mb-4">Implementamos medidas de seguridad técnicas y organizativas para proteger tu información:</p>
                            <ul class="list-disc pl-6">
                                <li>Autenticación segura mediante Firebase Authentication de Google.</li>
                                <li>Conexiones seguras mediante HTTPS/TLS.</li>
                                <li>Protección contra ataques CSRF (Cross-Site Request Forgery).</li>
                                <li>Cookies de sesión con atributos de seguridad (HttpOnly, SameSite).</li>
                                <li>Tokens de autenticación con expiración automática.</li>
                                <li>Acceso restringido a datos personales solo a personal autorizado.</li>
                            </ul>
                        </section>

                        <!-- Sección 8 -->
                        <section class="mb-8">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">8. Uso de Cookies</h2>
                            <p class="mb-4">Utilizamos cookies esenciales para el funcionamiento de la aplicación:</p>
                            <ul class="list-disc pl-6">
                                <li><strong>Cookie de sesión:</strong> Mantiene tu sesión activa mientras navegas.</li>
                                <li><strong>Token CSRF:</strong> Protege contra ataques de falsificación de solicitudes.</li>
                                <li><strong>Remember me:</strong> Permite mantener tu sesión iniciada si lo solicitas.</li>
                            </ul>
                            <p class="mt-4 p-3 bg-gray-100 dark:bg-gray-800 rounded-lg">
                                <strong>No utilizamos cookies de terceros</strong>, cookies de publicidad ni cookies de seguimiento/analytics.
                            </p>
                        </section>

                        <!-- Sección 9 -->
                        <section class="mb-8">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">9. Retención de Datos</h2>
                            <p>
                                Conservamos tus datos personales mientras tu cuenta permanezca activa o según sea necesario para
                                proporcionarte los servicios. Si solicitas la eliminación de tu cuenta, eliminaremos tus datos
                                personales en un plazo razonable, salvo que debamos conservarlos por obligaciones legales.
                            </p>
                        </section>

                        <!-- Sección 10 -->
                        <section class="mb-8">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">10. Tus Derechos</h2>
                            <p class="mb-4">De acuerdo con la legislación aplicable, tienes derecho a:</p>
                            <ul class="list-disc pl-6">
                                <li><strong>Acceso:</strong> Solicitar información sobre los datos personales que tenemos sobre ti.</li>
                                <li><strong>Rectificación:</strong> Corregir datos inexactos o incompletos.</li>
                                <li><strong>Cancelación/Eliminación:</strong> Solicitar la eliminación de tus datos personales.</li>
                                <li><strong>Oposición:</strong> Oponerte al tratamiento de tus datos en ciertas circunstancias.</li>
                                <li><strong>Portabilidad:</strong> Recibir tus datos en un formato estructurado y de uso común.</li>
                                <li><strong>Revocación del consentimiento:</strong> Retirar tu consentimiento en cualquier momento.</li>
                            </ul>
                            <p class="mt-4">
                                Para ejercer cualquiera de estos derechos, contáctanos en <a href="mailto:hola@tehila.com.mx" class="text-[#debb75] hover:underline">hola@tehila.com.mx</a>.
                            </p>
                        </section>

                        <!-- Sección 11 -->
                        <section class="mb-8">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">11. Menores de Edad</h2>
                            <p>
                                Nuestra aplicación no está dirigida a menores de 13 años. No recopilamos intencionadamente
                                información personal de niños menores de esta edad. Si eres padre o tutor y crees que tu hijo
                                nos ha proporcionado información personal, contáctanos para que podamos tomar las medidas necesarias.
                            </p>
                        </section>

                        <!-- Sección 12 -->
                        <section class="mb-8">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">12. Cambios en este Aviso de Privacidad</h2>
                            <p>
                                Podemos actualizar este Aviso de Privacidad ocasionalmente. Te notificaremos sobre cambios
                                significativos publicando el nuevo aviso en esta página y actualizando la fecha de "última actualización".
                                Te recomendamos revisar este aviso periódicamente.
                            </p>
                        </section>

                        <!-- Sección 13 -->
                        <section class="mb-8">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">13. Contacto</h2>
                            <p class="mb-4">
                                Si tienes preguntas, comentarios o solicitudes relacionadas con este Aviso de Privacidad o
                                el tratamiento de tus datos personales, puedes contactarnos a través de:
                            </p>
                            <div class="p-4 bg-gray-100 dark:bg-gray-800 rounded-lg">
                                <ul class="list-none space-y-2">
                                    <li><strong>Correo electrónico:</strong> <a href="mailto:hola@tehila.com.mx" class="text-[#debb75] hover:underline">hola@tehila.com.mx</a></li>
                                    <li><strong>Sitio web:</strong> <a href="https://tehila.com.mx" target="_blank" class="text-[#debb75] hover:underline">tehila.com.mx</a></li>
                                </ul>
                            </div>
                        </section>

                        <!-- Sección 14 - Google Play -->
                        <section class="mb-4 p-6 bg-[#debb75]/10 dark:bg-[#debb75]/20 rounded-xl border border-[#debb75]/30">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">14. Información Adicional para Google Play</h2>
                            <p class="mb-4">
                                En cumplimiento con las políticas de Google Play Store, declaramos que:
                            </p>
                            <ul class="list-disc pl-6 mb-6">
                                <li>La aplicación <strong>no recopila datos sensibles</strong> (ubicación, contactos, cámara, micrófono, etc.).</li>
                                <li>Los datos recopilados <strong>no se comparten con terceros</strong> para publicidad.</li>
                                <li>Los datos <strong>se cifran en tránsito</strong> mediante conexiones seguras (HTTPS/TLS).</li>
                                <li>Utilizamos <strong>Firebase Authentication</strong> y <strong>Google Sign-In</strong> para autenticación segura.</li>
                                <li>Los usuarios <strong>pueden solicitar la eliminación</strong> de sus datos contactando a <a href="mailto:hola@tehila.com.mx" class="text-[#debb75] hover:underline">hola@tehila.com.mx</a>.</li>
                                <li>La aplicación <strong>no contiene anuncios</strong> ni servicios de tracking publicitario.</li>
                            </ul>

                            <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-2">Tipo de datos según Google Play Data Safety:</h3>
                            <ul class="list-disc pl-6">
                                <li><strong>Datos recopilados:</strong> Nombre, correo electrónico, foto de perfil (opcional vía Google).</li>
                                <li><strong>Datos compartidos:</strong> Ninguno con terceros para publicidad o marketing.</li>
                                <li><strong>Prácticas de seguridad:</strong> Datos cifrados en tránsito, posibilidad de eliminar datos.</li>
                            </ul>
                        </section>
                    </div>
                </main>

                <!-- Footer -->
                <footer class="text-center text-sm text-black dark:text-white/70 mt-8">
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
