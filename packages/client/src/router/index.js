import { createRouter, createWebHistory } from "vue-router"
import HomeView from "../views/HomeView.vue"
import Cookies from "js-cookie"

const router = createRouter({
	history: createWebHistory(import.meta.env.BASE_URL),
	routes: [
		{
			path: "/",
			name: "home",
			component: HomeView,
		},
		{
			path: "/login",
			name: "login",
			component: () => import("../views/Login.vue"),
		},
		{
			path: "/register",
			name: "register",
			component: () => import("../views/Register.vue"),
		},
		{
			path: "/logout",
			name: "logout",
			component: () => import("../views/Logout.vue"),
		},
		{
			path: "/calendar",
			name: "calendar",
			component: () => import("../views/Calendar.vue"),
		},
	],
})

// manejar cada vez que se cambia de ruta, para asegurarse de que el usuario esté autenticado
router.beforeEach((to, from, next) => {
	const isAuth = Cookies.get("token") !== undefined

	// si no está autenticado y la ruta no es login o register, redirigir a login
	if (!isAuth && to.name !== "login" && to.name !== "register") {
		next({ name: "login" })
	} else {
		next()
	}
})

export default router
