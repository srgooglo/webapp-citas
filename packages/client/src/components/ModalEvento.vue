<template>
	<dialog :open="isOpen">
		<article>
			<h2>Agrega tu cita</h2>
			<form @submit.prevent="submitAppointment">
				<p><strong>Fecha:</strong> {{ date }}</p>

				<label>
					Título
					<input type="text" v-model="form.title" required />
				</label>

				<label>
					Descripción
					<textarea v-model="form.description" required></textarea>
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
					<button class="secondary" @click="handleCancel">
						Cancelar
					</button>
					<button type="submit">Confirmar</button>
				</footer>
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
		date: {
			type: String,
			default: "",
		},
	},
	data() {
		return {
			form: {
				title: "",
				description: "",
				guest_user_name: "",
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
