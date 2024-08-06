<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue'
import { Head } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps<{
  status: number
}>()

const title = computed(() => {
  return {
    503: '503: Service Unaviable',
    500: '500: Server Error',
    404: '404: Page Not Found',
    403: '403: Forbidden',
  }[props.status]
})

const description = computed(() => {
  return {
    503: 'Sorry, we are doing some maintenance. Please check back soon.',
    500: 'Whoops, something went wrong on our servers.',
    404: 'Sorry, the page you are looking for could not be found.',
    403: 'Sorry, you are forbidden from accessing this page.',
  }[props.status]
})

</script>

<template>
  <Head title="Error" />
  <div class="flex flex-col justify-between h-full items-center">
    <h2 class="text-2xl text-center"> {{ title }}</h2>
    <p class="text-center my-auto">{{ description }}</p>
    <a class="bg-blue-800 rounded-md block text-center text-white w-5/6 py-3 hover:bg-blue-700" href="/">Back</a>
  </div>
</template>

<script lang="ts">
export default {
  layout: GuestLayout,
}
</script>
