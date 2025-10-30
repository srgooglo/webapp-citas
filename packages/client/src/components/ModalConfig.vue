<template>
	<dialog :open="isOpen">
		<article>
			<h2>Edita tu perfil</h2>
			<form v-if="user" @submit.prevent="submitAppointment">
				<label>
					Nombre
					<input type="text" v-model="user.name" />
				</label>

				<label>
					Email
					<input type="email" v-model="user.email" />
				</label>

				<label>
					Telegram <input type="text" v-model="user.telegram_id" />
				</label>
				<label>
					Subir avatar <br />
					<UpdateAvatarButton />
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
import UpdateAvatarButton from "@/components/UpdateAvatarButton.vue"
export default {
	components: {
		UpdateAvatarButton,
	},
	props: {
		isOpen: {
			type: Boolean,
			default: false,
		},
	},
	data() {
		return {
			user: {
				name: "",
				email: "",
				telegram_id: "",
			},
		}
	},
	methods: {
		handleCancel() {
			this.$emit("close")
		},
	},
	async mounted() {
		this.user = await this.$api.selfUser().catch((error) => {
			return null
		})
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
