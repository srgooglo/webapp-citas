<template>
	<div class="app-loading" v-if="loading">
		<p>Loading...</p>
	</div>
	<div id="app" v-else="!loading">
		<header>
			<nav>
				<router-link to="/">Home</router-link>
				<router-link to="/calendar">Calendar</router-link>
			</nav>

			<div v-if="user" class="header-account">
				<div class="header-account-content">
					<div class="header-account-avatar">
						<img :src="user.avatar" alt="Avatar" />
					</div>
					<p>{{ user.name }} ({{ user.email }})</p>
					<button @click="editUser">Editar usuario</button>
					<Modal
						:isOpen="modalOpen"
						@close="handleModalClose"
					></Modal>
					<button class="secondary" @click="toLogout">Logout</button>
				</div>
			</div>

			<div v-if="!user" class="header-account">
				<div class="header-account-content">
					<button @click="toLogin">Login</button>
				</div>
			</div>
		</header>

		<main>
			<router-view />
		</main>
	</div>
</template>
<script>
import Modal from "./components/ModalConfig.vue"

export default {
	components: {
		Modal,
	},
	data() {
		return {
			loading: true,
			user: null,
			modalOpen: false,
		}
	},
	methods: {
		toLogin() {
			this.$router.push("/login")
		},
		toLogout() {
			this.$router.push("/logout")
		},
		editUser() {
			this.modalOpen = true
		},
		handleModalClose() {
			this.modalOpen = false
		},
	},
	async mounted() {
		const isAuthed = this.$session.token !== undefined

		if (!isAuthed) {
			this.loading = false
			return null
		}

		this.user = await this.$api.selfUser().catch((error) => {
			return null
		})

		console.log(`Loaded user >`, this.user)

		window.user = this.user

		this.appointments = await this.$api
			.selfAppointments()
			.catch((error) => {
				return null
			})

		console.log(`Loaded appointments >`, this.appointments)

		window.appointments = this.appointments

		this.loading = false
	},
}
</script>
