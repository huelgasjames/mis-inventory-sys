import './assets/main.css'
import 'bootstrap/dist/css/bootstrap.min.css'
import 'bootstrap-icons/font/bootstrap-icons.css'
import 'bootstrap/dist/js/bootstrap.bundle.min.js'

import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import Chart from 'chart.js/auto'

const app = createApp(App)
app.use(router)
app.config.globalProperties.$Chart = Chart
app.mount('#app')
