<template>
	<div class="register-container">
		<h1>Register</h1>

		<form
			@submit.prevent="register"
			name="registerForm"
			class="register-form"
		>
			<label for="name">Name:</label>
			<input type="text" id="name" v-model="name" required />

			<label for="email">Email:</label>
			<input type="email" id="email" v-model="email" required />

			<label for="password">Password:</label>
			<input type="password" id="password" v-model="password" required />

			<button type="submit">Register</button>
		</form>

		<p v-if="error">{{ error }}</p>

		<router-link to="/login">Login with a account</router-link>
	</div>
</template>

<script>
export default {
	data() {
		return {
			loading: false,
			error: null,
			name: "Test",
			email: "test@example.com",
			password: "123",
		}
	},
	methods: {
		async register() {
			this.error = null
			this.loading = true

			const result = await this.$api
				.register({
					name: this.name,
					email: this.email,
					password: this.password,
				})
				.catch((error) => {
					console.error(error)

					this.error = error.response.data.error
					return null
				})

			this.loading = false

			if (result) {
				this.$router.push("/login")
			}
		},
	},
}
</script>

<style scoped>
.register-container {
	display: flex;
	flex-direction: column;

	width: 400px;
}

.register-form {
	display: flex;
	flex-direction: column;
}

.register-form label {
	margin-bottom: 0.5rem;
}

.register-form input {
	margin-bottom: 1rem;
	padding: 0.5rem;
	border: 1px solid #ccc;
	border-radius: 4px;
}

.register-form button {
	padding: 0.5rem 1rem;
	background-color: #007bff;
	color: #fff;
	border: none;
	border-radius: 4px;
	cursor: pointer;
}

.error-message {
	background-color: #f8d7da;
	color: #721c24;
	padding: 0.5rem;
	border-radius: 4px;
	margin-top: 1rem;
}
</style>
