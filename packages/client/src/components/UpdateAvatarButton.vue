<template>
	<button @click="updateAvatar">Update Avatar</button>
</template>

<script>
export default {
	methods: {
		updateAvatar() {
			const fileInput = document.createElement("input")

			fileInput.type = "file"
			fileInput.accept = "image/*"

			fileInput.addEventListener("change", (event) => {
				if (event.target.files && event.target.files.length > 0) {
					const file = event.target.files[0]

					if (file.type.startsWith("image/")) {
						this.$api
							.updateAvatar(file)
							.then(() => {
								this.$emit(
									"feedback",
									"Avatar actualizado correctamente. La página se recargará en unos segundos.",
								)
							})
							.catch((error) => {
								this.$emit(
									"feedback",
									"Error actualizando el avatar: " +
										error.message,
								)
							})
					} else {
						this.$emit(
							"feedback",
							"Por favor selecciona un archivo de imagen.",
						)
					}
				}
			})

			fileInput.click()
		},
	},
}
</script>
