<script setup lang="ts">
const props = defineProps<{
  name: string
  avatar: string
}>()
</script>

<template>
  <a :href="'https://github.com/' + props.name" class="flex items-center" target="_blank">
    <img :src="props.avatar" class="size-6 rounded-full mr-2" :alt="props.name">
    {{ props.name }}
  </a>
</template>
