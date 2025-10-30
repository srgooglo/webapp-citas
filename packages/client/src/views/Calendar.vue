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
				events: [
					// Aquí puedes agregar eventos locales
					{
						title: "Evento Local 1",
						start: "2025-11-05",
					},
					{
						title: "Evento Local 2",
						start: "2025-11-06",
					},
				],
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
