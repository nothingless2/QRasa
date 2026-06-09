import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.js",
    ],

    // Safelist dynamic classes that Tailwind cannot detect at build time
    safelist: [
        // Transition / animation classes used by Alpine.js x-transition
        "opacity-0", "opacity-100", "scale-95", "scale-100",
        // Dynamic grid columns from settings
        "lg:grid-cols-1", "lg:grid-cols-2", "lg:grid-cols-3", "lg:grid-cols-4",
        "grid-cols-1", "grid-cols-2", "grid-cols-3",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ["Figtree", ...defaultTheme.fontFamily.sans],
            },
            colors: {
                hijau1: "#3f7d58",
                hijau2: "#537d5d",
                oren:    "#ef9651",
                orenTua: "#ec5228",
                putih:   "#efefef",
            },
        },
    },

    plugins: [forms],
};
