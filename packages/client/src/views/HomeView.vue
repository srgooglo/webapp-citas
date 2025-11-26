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
			<div class="appointment-actions">
				<button @click="editAppointment(appointment)">
					Editar cita
				</button>
				<button class="secondary" @click="confirmDelete(appointment)">
					Borrar cita
				</button>
			</div>
		</div>
		<ModalEventoEdit
			:isOpen="showEditModal"
			@close="handleModalClose"
			:appointmentData="modalAppointment"
		></ModalEventoEdit>
		<ModalEventoDelete
			:isOpen="showDeleteModal"
			@close="showDeleteModal = false"
			@delete="performDelete"
			:appointment="appointmentToDelete"
		/>
	</div>
	<h2 v-else>No tienes citas registradas</h2>
</template>

<script>
import ModalEventoEdit from "@/components/ModalEventoEdit.vue"
import ModalEventoDelete from "@/components/ModalEventoDelete.vue"
import { format } from "date-fns"
import { es } from "date-fns/locale"

export default {
	components: {
		ModalEventoEdit,
		ModalEventoDelete,
	},
	data() {
		return {
			appointments: null,
			user: null,
			showEditModal: false,
			modalAppointment: null,
			showDeleteModal: false,
			appointmentToDelete: null,
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
			this.showEditModal = true
			this.modalAppointment = appointment
		},
		confirmDelete(appointment) {
			this.appointmentToDelete = appointment
			this.showDeleteModal = true
		},
		async performDelete(appointment) {
			const result = await this.$api
				.deleteAppointment(appointment.id)
				.catch((error) => {
					console.error(error)
					return null
				})

			if (result) {
				window.appointments = window.appointments.filter(
					(a) => a.id !== appointment.id,
				)
				this.appointments = this.appointments.filter(
					(a) => a.id !== appointment.id,
				)
			}
			this.showDeleteModal = false
		},
		handleModalClose() {
			this.showEditModal = false
			this.modalAppointment = null
		},
	},
	async mounted() {
		this.appointments = window.appointments.sort(
			(a, b) => new Date(a.date) - new Date(b.date),
		)
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
