import '../css/app.css'
import './bootstrap'

import Swal from 'sweetalert2'
import { createInertiaApp } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { createApp, h } from 'vue'

createInertiaApp({
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue')
        ),

    setup({ el, App, props, plugin }) {
        const vueApp = createApp({ render: () => h(App, props) })

        // GLOBAL SWEETALERT FOR FLASH MESSAGE
        vueApp.mixin({
            mounted() {
                const flash = this.$page.props.flash

                if (flash?.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: flash.success,
                        timer: 2000,
                        showConfirmButton: false,
                    })
                }
            }
        })

        vueApp.use(plugin).mount(el)
    },
})
