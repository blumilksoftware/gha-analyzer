<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue'
import { Head } from '@inertiajs/vue3'
</script>

<template>
  <Head title="Login" />
  <div class="flex flex-col justify-between h-full items-center">
    <h2 class="text-2xl text-center">Welcome in Github Analyzer</h2>
    <p class="text-center my-auto">Login with Github to analyze your GitHub Actions and monitor usage minutes!</p>
    <a class="bg-gray-800 rounded-md block text-center text-white w-5/6 py-3 hover:bg-gray-700" href="/auth/redirect">Login via Github</a>
  </div>
</template>

<script lang="ts">
export default {
  layout: GuestLayout,
}
</script>
