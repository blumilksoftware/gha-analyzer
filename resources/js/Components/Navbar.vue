<script setup lang="ts">
import {Link} from '@inertiajs/vue3'
import { usePage } from '@inertiajs/vue3'

const page = usePage()

function getNavigationClass (tab: string) {
  if (tab === page.url) {
    return 'bg-blue-500'
  }
}
</script>

<template>
  <div class="container mt-12 mx-auto">
    <div class="block uppercase text-white font-semibold text-xs tracking-widest">
      <nav class="flex items-center space-x-4" aria-label="Tabs">
        <span class="pr-6 py-2 font-medium text-sm rounded-md">
          <img src="../../assets/images/icon.png" alt="Logo" class="w-12">
        </span>
        <div class="flex space-x-4 w-full">
          <Link href="/organization"><span :class="getNavigationClass('/organization')" class="cursor-pointer px-8 py-4 text-xs rounded-md bg-blue-400/50 hover:bg-blue-400/75">Organizations</span></Link>
          <Link href="/table"><span :class="getNavigationClass('/table')" class="cursor-pointer px-8 py-4 text-xs rounded-md bg-blue-400/50 hover:bg-blue-400/75">Table</span></Link>
          <Link href="/repositories"><span :class="getNavigationClass('/repositories')" class="cursor-pointer px-8 py-4 text-xs rounded-md bg-blue-400/50 hover:bg-blue-400/75">Repositories</span></Link>
          <Link href="/authors"><span :class="getNavigationClass('/authors')" class="cursor-pointer px-8 py-4 text-xs rounded-md bg-blue-400/50 hover:bg-blue-400/75">Authors</span></Link>
          <div class="flex-1" />
          <Link href="/auth/logout"><span class="cursor-pointer px-8 py-4 text-xs rounded-md bg-blue-500 hover:bg-blue-400/50">Log Out</span></Link>
        </div>
      </nav>
    </div>
  </div>
</template>
