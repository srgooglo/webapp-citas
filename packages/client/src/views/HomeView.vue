<template>
	<h1>Bienvenido a la aplicación</h1>
	<div v-if="appointments && appointments.length">
		<h3>Tus citas:</h3>
		<div
			class="appointment-cont"
			v-for="appointment in appointments"
			:key="appointment.id"
		>
			<div
				v-if="user.id !== appointment.host_user_id"
				class="appointment"
			>
				<strong>{{ appointment.title }}</strong>
				<div class="appointment-details">
					<p>La cita de {{ appointment.host_user.name }}</p>

					<img :src="appointment.host_user.avatar" alt="Avatar" />

					<p>
						y usted con descripción
						{{ appointment.description }}, es a las
						{{ formatDate(appointment.date) }}
					</p>
				</div>
			</div>
			<div v-else>
				<strong>{{ appointment.title }}</strong>
				<div
					class="appointment-details"
					v-if="appointment.guest_user !== null"
				>
					<p>Su cita con {{ appointment.guest_user.name }}</p>
					<img :src="appointment.guest_user.avatar" alt="Avatar" />
					<p>
						con descripción {{ appointment.description }}, es a las
						{{ formatDate(appointment.date) }}
					</p>
				</div>
				<p v-else>
					Su cita con {{ appointment.guest_user_name }} sobre
					{{ appointment.description }} es a las
					{{ formatDate(appointment.date) }}
				</p>
			</div>
			<div
				class="appointment-actions"
				v-if="user.id === appointment.host_user_id"
			>
				<button @click="editAppointment(appointment)">
					Editar cita
				</button>
				<button class="secondary" @click="">Borrar cita</button>
			</div>
		</div>
		<Modal
			:isOpen="modalOpen"
			@close="handleModalClose"
			:appointmentData="modalAppointment"
		></Modal>
	</div>
	<h2 v-else>No tienes citas registradas</h2>
</template>

<script>
import Modal from "@/components/ModalEventoEdit.vue"
import { format } from "date-fns"
import { es } from "date-fns/locale"

export default {
	components: {
		Modal,
	},
	data() {
		return {
			appointments: null,
			user: null,
			modalOpen: false,
			modalAppointment: null,
		}
	},
	methods: {
		formatDate(dateString) {
			const date = new Date(dateString)
			return format(date, "'a las' HH:mm 'el' dd 'de' MMMM 'de' yyyy", {
				locale: es,
			})
		},
		editAppointment(appointment) {
			this.modalOpen = true
			this.modalAppointment = { ...appointment }
		},
		handleModalClose() {
			this.modalOpen = false
			this.modalAppointment = null
		},
	},
	async mounted() {
		this.appointments = window.appointments
		this.user = window.user
	},
}
</script>

<style scoped>
.appointment-cont {
	display: flex;
	justify-content: space-between;
	align-items: center;
	width: 100%;
	margin: 10px;
	padding: 20px;
	background-color: var(--color-background);
	overflow: hidden;
	border-radius: 12px;
}
.appointment-actions {
	display: flex;
	gap: 10px;
}
.appointment-details {
	display: flex;
	align-items: center;
	gap: 10px;
}

img {
	width: 30px;
	height: 30px;
	border-radius: 50%;
	object-fit: cover;
	margin-bottom: 20px;
}
</style>
