<template>
	<dialog :open="isOpen">
		<article>
			<h2>Edita esta cita</h2>
			<form @submit="submitAppointment">
				<label>
					Título
					<input type="text" v-model="form.title" required />
				</label>

				<label>
					Descripción
					<textarea v-model="form.description" required></textarea>
				</label>

				<label>
					Dia de la cita
					<input
						type="date"
						:min="getToday()"
						v-model="form.date"
						required
					/>
				</label>

				<label>
					Hora de la cita
					<input type="time" v-model="form.time" required />
				</label>

				<label>
					Nombre del invitado
					<input
						type="text"
						v-model="form.guest_user_name"
						required
					/>
				</label>

				<footer class="form-buttons">
					<button class="secondary" @click.prevent="handleCancel">
						Cancelar
					</button>
					<button type="submit">Confirmar</button>
				</footer>
				<div v-if="error" class="error-message">{{ error }}</div>
			</form>
		</article>
	</dialog>
</template>
<script>
export default {
	props: {
		isOpen: {
			type: Boolean,
			default: false,
		},
		appointmentData: Object,
	},
	data() {
		return {
			error: null,
			form: {
				title: "",
				description: "",
				guest_user_name: "",
				date: "",
				time: "",
			},
		}
	},
	methods: {
		handleCancel() {
			this.resetForm()
			this.$emit("close")
		},
		resetForm() {
			this.form = {
				title: "",
				description: "",
				guest_user_name: "",
				time: "",
			}
		},
		async submitAppointment() {
			this.error = null
			const result = await this.$api
				.createAppointment({
					title: this.form.title,
					description: this.form.description,
					guest_user_name: this.form.guest_user_name,
					date: this.date + " " + this.form.time,
				})
				.catch((error) => {
					console.error(error)

					this.error = error.response.data.error
					return null
				})
		},
		getToday() {
			const today = new Date()
			const year = today.getFullYear()
			const month = String(today.getMonth() + 1).padStart(2, "0")
			const day = String(today.getDate()).padStart(2, "0")
			return `${year}-${month}-${day}`
		},
	},
	watch: {
		appointmentData: {
			immediate: true,
			handler(newData) {
				if (newData) {
					this.form = {
						title: newData.title || "",
						description: newData.description || "",
						guest_user_name:
							newData.guest_user_name ||
							newData.guest_user?.name ||
							"",
						date: newData.date ? newData.date.split(" ")[0] : "",
						time: newData.date
							? newData.date.split(" ")[1]?.slice(0, 5)
							: "",
					}
				}
			},
		},
	},
}
</script>
<style scoped>
* {
	margin: 0;
	box-sizing: border-box;
}

.form-buttons {
	display: flex;
	justify-content: space-between;
	align-items: center;
	margin-top: 1rem;
}

.form-buttons button {
	width: 25%;
}
</style>
