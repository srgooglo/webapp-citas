<script>
export default {
	name: "Logout",
	mounted() {
		this.$session.logout()
	},
}
</script>

<template>
	<h1>Logging Out...</h1>
</template>
