import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/scss/customer.scss",
                "resources/scss/staff.scss",
                "resources/js/app.js",
            ],
            refresh: true,
        }),
    ],
});
