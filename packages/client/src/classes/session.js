import Cookies from "js-cookie"
import Api from "@/api"

export default class Session {
	static get token() {
		return Cookies.get("token")
	}

	static set token(token) {
		Cookies.set("token", token)
		return token
	}

	static logout() {
		Cookies.remove("token")
		window.location.href = "/"
	}

	static async login({ email, password }, { onSuccess, onError } = {}) {
		try {
			const auth = await Api.login(email, password)

			if (auth) {
				if (typeof onSuccess === "function") {
					await onSuccess(auth)
				}

				Session.token = auth.token
				window.location.href = "/"
			}
		} catch (error) {
			if (typeof onError === "function") {
				await onError(error)
			}

			console.error(error)
		}
	}
}
