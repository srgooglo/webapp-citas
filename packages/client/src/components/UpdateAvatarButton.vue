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
								console.log("Avatar updated successfully")
							})
							.catch((error) => {
								console.error("Error updating avatar:", error)
							})
					} else {
						console.error("Please select an image file")
					}
				}
			})

			fileInput.click()
		},
	},
}
</script>
