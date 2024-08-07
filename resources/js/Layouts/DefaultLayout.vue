<script setup lang="ts">
import Navbar from '../Components/Navbar.vue'
</script>

<template>
  <Navbar />
  <main class="container my-4 mx-auto bg-white rounded-xl p-4 shadow flex">
    <div class="flex-1 p-4">
      <slot />
    </div>
  </main>

  <footer>
    <div class="w-full flex items-center justify-center text-white">
      <span class="mr-2 font-semibold text-xs tracking-widest">developed by</span>
      <a href="https://blumilk.pl/" target="_blank">
        <img class="w-32" src="../../assets/images/blumilk.png" alt="Blumilk">
      </a>
    </div>
  </footer>
</template>
