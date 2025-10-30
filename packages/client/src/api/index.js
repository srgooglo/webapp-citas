import axios from "axios"
import Cookies from "js-cookie"

/**
 *
 * Esta clase proporciona una interfaz centralizada para realizar peticiones HTTP
 * a los endpoints del backend de la aplicación. Actúa como una capa de abstracción
 * entre los componentes del frontend y las APIs REST del servidor.
 *
 * Características principales:
 * - Configuración centralizada para todas las peticiones HTTP
 * - Manejo automático de tokens de autenticación mediante cookies
 * - Métodos específicos para operaciones comunes (login, registro, obtención de datos de usuario)
 * - Permite mantener la lógica de comunicación con el backend separada de los componentes UI
 *
 * Todos los métodos devuelven Promises con la respuesta ya procesada (response.data),
 * facilitando su uso con async/await en los componentes.
 */
export default class API {
	/**
	 * Instancia base de axios configurada con la URL base y headers comunes
	 * @type {import('axios').AxiosInstance}
	 */
	static baseRequest = axios.create({
		baseURL: "/api",
		headers: {
			Authorization: Cookies.get("token")
				? `Bearer ${Cookies.get("token")}`
				: "",
		},
	})

	/**
	 * Obtiene los datos de un usuario específico
	 * @param {string|number} user_id - ID del usuario a consultar
	 * @returns {Promise<Object>} Datos del usuario
	 * @throws {Error} Si la petición falla
	 */
	static async getUserData(user_id) {
		const response = await this.baseRequest({
			method: "GET",
			url: `/users/${user_id}`,
		})
		return response.data
	}

	/**
	 * Inicia sesión con email y contraseña
	 * @param {string} email - Correo electrónico del usuario
	 * @param {string} password - Contraseña del usuario
	 * @returns {Promise<Object>} Datos de autenticación, probablemente incluyendo un token
	 * @throws {Error} Si las credenciales son inválidas o la petición falla
	 */
	static async login(email, password) {
		const response = await this.baseRequest({
			method: "POST",
			url: "/auth",
			data: {
				email: email,
				password: password,
			},
		})

		return response.data
	}

	/**
	 * Registra un nuevo usuario
	 * @param {Object} payload - Datos para el registro del usuario
	 * @param {string} payload.email - Correo electrónico del usuario
	 * @param {string} payload.password - Contraseña del usuario
	 * @param {string} [payload.name] - Nombre del usuario (opcional)
	 * @param {Object} [payload.additionalInfo] - Información adicional del usuario (opcional)
	 * @returns {Promise<Object>} Resultado del registro, probablemente incluyendo datos del usuario creado
	 * @throws {Error} Si los datos son inválidos o la petición falla
	 */
	static async register(payload) {
		const response = await this.baseRequest({
			method: "POST",
			url: "/auth/register",
			data: payload,
		})

		return response.data
	}

	/**
	 * Obtiene los datos del usuario autenticado actualmente
	 * @returns {Promise<Object>} Datos del usuario actual
	 * @throws {Error} Si no hay un usuario autenticado o la petición falla
	 */
	static async selfUser() {
		const response = await this.baseRequest({
			method: "GET",
			url: "/users/self",
		})

		return response.data
	}

	static async updateAvatar(file) {
		const formData = new FormData()
		formData.append("file", file)
		console.log([...formData.entries()])

		const response = await this.baseRequest({
			method: "POST",
			url: "/users/self/avatar",
			data: formData,
		})

		return response.data
	}

	static async createAppointment(payload) {
		const response = await this.baseRequest({
			method: "POST",
			url: "/appointments/create",
			data: payload,
		})

		return response.data
	}
}
