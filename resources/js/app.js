import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import '../css/app.css'
import 'flowbite'
import PrimeVue from 'primevue/config'
import ToastService from 'primevue/toastservice'
import Vue3EasyDataTable from 'vue3-easy-data-table';
import 'vue3-easy-data-table/dist/style.css';
import Antd from 'ant-design-vue';
createInertiaApp({
    resolve: name => {
        const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
        const page = pages[`./Pages/${name}.vue`]
        return page
    },
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
        app.use(plugin)
        app.use(PrimeVue)
        app.use(ToastService)
        app.use(Antd)
        app.component('EasyDataTable', Vue3EasyDataTable)
        app.mount(el)
    }
})
