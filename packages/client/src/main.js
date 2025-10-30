import { createApp } from "vue"

import App from "./App.vue"
import router from "./router"

import "./assets/main.css"

import Api from "@/api"
import Session from "@/classes/session"

import "@picocss/pico/css/pico.min.css"

// Tal vez este bien integrar pinia para manejar los estados y las acciones de la aplicación,
// como el estado de autenticación del usuario, el estado de la aplicación, etc.
// https://pinia.vuejs.org/

const app = createApp(App)
// exponemos la api y el session helper al contexto global para usarlo en cualquier parte de la aplicación
app.config.globalProperties.$api = Api
app.config.globalProperties.$session = Session

window.api = Api

app.use(router)

app.mount("#app")
