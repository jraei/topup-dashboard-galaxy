
import "./bootstrap";
import "../css/app.css";

import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { ZiggyVue } from "../../vendor/tightenco/ziggy";
import swalPlugin from "./plugins/swalPlugin";
import Toast from "vue-toastification";
import "vue-toastification/dist/index.css";

const appName = import.meta.env.VITE_APP_NAME || "Laravel";

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/*.vue")
        ),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(swalPlugin)
            .use(Toast, {
                transition: "Vue-Toastification__bounce",
                maxToasts: 5,
                newestOnTop: true,
                position: "top-right",
                timeout: 3000,
                closeOnClick: true,
                pauseOnFocusLoss: true,
                pauseOnHover: true,
                draggable: true,
                showCloseButtonOnHover: false,
                hideProgressBar: false,
                closeButton: "button",
                icon: true,
                rtl: false
            })
            .mount(el);
    },
    progress: {
        color: "#4B5563",
    },
});
