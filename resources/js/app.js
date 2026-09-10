import '../css/app.css'
import './bootstrap'

import Swal from 'sweetalert2'
import 'sweetalert2/dist/sweetalert2.min.css'

import {
    createInertiaApp,
    router
} from '@inertiajs/vue3'

import {
    resolvePageComponent
} from 'laravel-vite-plugin/inertia-helpers'

import { ZiggyVue } from '../../vendor/tightenco/ziggy'

import {
    createApp,
    h
} from 'vue'


createInertiaApp({

    resolve: (name) => {

        return resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue')
        )

    },


    setup({
        el,
        App,
        props,
        plugin
    }) {

        const vueApp = createApp({

            render: () => h(
                App,
                props
            )

        })


        vueApp
            .use(plugin)
            .use(ZiggyVue)
            .mount(el)


        /*
        |--------------------------------------------------------------------------
        | Global Inertia Flash Notifications
        |--------------------------------------------------------------------------
        */

        router.on(
            'success',
            (event) => {

                const flash =
                    event.detail.page.props.flash


                /*
                |--------------------------------------------------------------------------
                | Success
                |--------------------------------------------------------------------------
                */

                if (flash?.success) {

                    Swal.fire({

                        toast: true,

                        position: 'top-end',

                        icon: 'success',

                        title: flash.success,

                        showConfirmButton: false,

                        timer: 2500,

                        timerProgressBar: true,

                        showCloseButton: true,

                        didOpen: (toast) => {

                            toast.addEventListener(
                                'mouseenter',
                                Swal.stopTimer
                            )

                            toast.addEventListener(
                                'mouseleave',
                                Swal.resumeTimer
                            )

                        }

                    })

                }


                /*
                |--------------------------------------------------------------------------
                | Error
                |--------------------------------------------------------------------------
                */

                if (flash?.error) {

                    Swal.fire({

                        toast: true,

                        position: 'top-end',

                        icon: 'error',

                        title: flash.error,

                        showConfirmButton: false,

                        timer: 3000,

                        timerProgressBar: true,

                        showCloseButton: true,

                    })

                }

            }
        )

    },

})