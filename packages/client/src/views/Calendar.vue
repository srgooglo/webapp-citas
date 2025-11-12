<template>
	<div>
		<h1>Calendario</h1>

		<!-- FullCalendar -->
		<FullCalendar
			ref="calendar"
			:options="calendarOptions"
			@date-click="handleDateClick"
			@event-click="handleEventClick"
		/>
		<Modal
			:isOpen="modalOpen"
			:date="modalDate"
			@close="handleModalClose"
		></Modal>
	</div>
</template>

<script>
// Importa las dependencias necesarias
import FullCalendar from "@fullcalendar/vue3"
import dayGridPlugin from "@fullcalendar/daygrid"
import interactionPlugin from "@fullcalendar/interaction"
import Modal from "@/components/ModalEvento.vue"
export default {
	components: {
		FullCalendar,
		Modal,
	},
	data() {
		return {
			appointments: null,
			modalOpen: false,
			modalDate: null,
			calendarOptions: {
				plugins: [dayGridPlugin, interactionPlugin],
				dateClick: this.handleDateClick,
				eventClick: this.handleEventClick,
				initialView: "dayGridMonth",
				validRange: function (nowDate) {
					return {
						start: nowDate,
					}
				},
				height: "auto",
				events: [],
			},
		}
	},
	methods: {
		// Función para manejar la selección de fecha (al hacer clic en un día)
		handleDateClick(arg) {
			this.modalDate = arg.dateStr
			this.modalOpen = true
		},

		// Función para manejar clic en un evento
		handleEventClick(arg) {
			alert("Evento clickeado: " + arg.event.title)
		},
		handleModalClose() {
			this.modalOpen = false
			this.modalDate = null
		},
		loadAppointmentsToCalendar() {
			if (this.appointments) {
				this.calendarOptions.events = this.appointments.map(
					(appointment) => ({
						title: appointment.title,
						start: appointment.date.split(" ")[0],
					}),
				)
			}
		},
	},
	async mounted() {
		this.appointments = window.appointments
		this.loadAppointmentsToCalendar()
	},
}
</script>

<style scoped>
button {
	margin: 10px;
	padding: 10px;
	background-color: #42b983;
	color: white;
	cursor: pointer;
}

button:disabled {
	background-color: #cccccc;
	cursor: not-allowed;
}

#calendar {
	margin-top: 20px;
}
</style>
