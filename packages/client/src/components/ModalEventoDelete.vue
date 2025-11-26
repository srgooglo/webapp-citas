<template>
	<dialog :open="isOpen">
		<article>
			<h2>¿Eliminar cita?</h2>
			<p>
				¿Estás seguro de que deseas eliminar esta cita? Esta acción no
				se puede deshacer.
			</p>

			<div class="form-buttons">
				<button class="secondary" @click.prevent="handleCancel">
					Cancelar
				</button>
				<button class="alert" @click.prevent="handleConfirm">
					Eliminar
				</button>
			</div>
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
		appointment: Object,
	},
	methods: {
		handleCancel() {
			this.$emit("close")
		},
		async handleConfirm() {
			this.$emit("delete", this.appointment)
		},
	},
}
</script>

<style scoped>
.form-buttons {
	display: flex;
	justify-content: space-between;
	margin-top: 1rem;
}
.alert {
	background-color: darkred;
	border: none;
}
</style>
