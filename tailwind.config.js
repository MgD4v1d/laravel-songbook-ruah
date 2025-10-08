import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import themes from 'daisyui/theme/object';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [
        forms,
        require('daisyui')
    ],

    daisyui:{
        themes:[
            "light",
            "dark",
            "cupcake",
            "bumblebee",
            "emerald",
            "corporate",
            "synthwave",
            "retro",
            "cyberpunk",
            "valentine",
            "halloween",
            "garden",
            "forest",
            "aqua",
            "lofi",
            "pastel",
            "fantasy",
            "wireframe",
            "black",
            "luxury",
            "dracula",
            "cmyk",
            "autumn",
            "business",
            "acid",
            "lemonade",
            "night",
            "coffee",
            "winter",
            "dim",
            "nord",
            "sunset",
        ],
        darkTheme: "dark", // Tema oscuro por defecto
        base: true, // Aplicar estilos base
        styled: true, // Aplicar estilos de componentes
        utils: true, // Utilidades
        prefix: "", // Sin prefijo
        logs: true, // Mostrar logs
        themeRoot: ":root", // Root del tema
    }
};
