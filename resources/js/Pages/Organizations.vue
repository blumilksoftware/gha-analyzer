<script setup>
const props = defineProps({
    orgs: array
})
</script>

<template>
    <div v-for="org in orgs"></div>
</template>