<template>
	<div class="login-container">
		<h1>Login</h1>

		<form name="loginForm" @submit.prevent="login" class="login-form">
			<label for="email">Email:</label>
			<input type="email" id="email" v-model="email" required />

			<label for="password">Password:</label>
			<input type="password" id="password" v-model="password" required />

			<button type="submit" :disabled="loading">Login</button>
		</form>

		<div v-if="error" class="error-message">{{ error }}</div>

		<router-link to="/register">Register a new account</router-link>
	</div>
</template>

<script>
export default {
	name: "Login",
	data() {
		return {
			loading: false,
			error: null,
			email: "test@example.com",
			password: "123",
		}
	},
	methods: {
		async login() {
			this.error = null
			this.loading = true

			await this.$session.login(
				{
					email: this.email,
					password: this.password,
				},
				{
					onSuccess: () => {
						console.log("Login successful")
					},
					onError: (error) => {
						this.error = error.response.data.error
					},
				},
			)

			this.loading = false
		},
	},
}
</script>

<style scoped>
.login-container {
	display: flex;
	flex-direction: column;

	width: 400px;
}

.login-form {
	display: flex;
	flex-direction: column;
}

.login-form label {
	margin-bottom: 0.5rem;
}

.login-form input {
	margin-bottom: 1rem;
	padding: 0.5rem;
	border: 1px solid #ccc;
	border-radius: 4px;
}

.login-form button {
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
