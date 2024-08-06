<template>
  <div class="flex h-5/6 justify-center items-center px-2 sm:px-0">
    <main class="w-full max-w-96 h-80 bg-white rounded-xl p-4 shadow flex">
      <div class="flex-1 w-full p-6">
        <slot />
      </div>
    </main>
  </div>
  <div class="container mx-auto">
    <div class="block uppercase text-white font-semibold text-xs tracking-widest" />
  </div>
  <footer>
    <div class="w-full flex items-center justify-center text-white">
      <span class="font-semibold text-xs tracking-widest">developed by</span>
      <a href="https://blumilk.pl/" target="_blank">
        <img class="w-32" src="../../assets/images/blumilk.png" alt="Blumilk">
      </a>
    </div>
  </footer>
</template>
