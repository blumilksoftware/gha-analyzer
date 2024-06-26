<script setup>
import {Link} from '@inertiajs/vue3'
import { usePage } from '@inertiajs/vue3'
import { useLogsStore } from '@/Stores/logsStore'
import { computed } from 'vue'

const logsStore = useLogsStore()
const logs = computed(() => logsStore.getLogs)

const page = usePage()

function getNavigationClass (tab) {
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
        <Link href="/"><span :class="getNavigationClass('/')" class="cursor-pointer px-8 py-4 text-xs rounded-md bg-opacity-50 bg-blue-400 hover:bg-opacity-75" href="/table">Raw Log</span></Link>
        <div v-if="logs.length > 0" class="flex space-x-4">
          <Link href="/table"><span :class="getNavigationClass('/table')" class="cursor-pointer px-8 py-4 text-xs rounded-md bg-opacity-50 bg-blue-400 hover:bg-opacity-75" href="/table">Table</span></Link>
          <Link href="/repositories"><span :class="getNavigationClass('/repositories')" class="cursor-pointer px-8 py-4 text-xs rounded-md bg-opacity-50 bg-blue-400 hover:bg-opacity-75" href="/table">Repositories</span></Link>
          <Link href="/authors"><span :class="getNavigationClass('/authors')" class="cursor-pointer px-8 py-4 text-xs rounded-md bg-opacity-50 bg-blue-400 hover:bg-opacity-75" href="/table">Authors</span></Link>
        </div>
      </nav>
    </div>
  </div>
</template>
