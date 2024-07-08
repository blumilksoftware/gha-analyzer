<script setup>
import { computed, ref, watch } from 'vue'
import { useLogsStore } from '@/Stores/logsStore'
import { Head } from '@inertiajs/vue3'

const props = defineProps({
  colors: {
    type: Array,
    default: () => [],
  },
  repositoriesPROPS: {
    type: Array,
    required: true
  }
})

var colors = props.colors

const logsStore = useLogsStore()
const logs = computed(() => logsStore.getLogs)

const repositories = computed(() => {
  return props.repositoriesPROPS
})

watch(logs, () => {
  
}, { immediate: true })
console.log(props.repositoriesPROPS[1])


</script>

<template>
  <div v-if="repositoriesPROPS">
  <Head>
    <title>Repositories</title>
  </Head>
  <table class="w-full border-collapse border table-fixed mt-4 text-sm">
    <thead>
      <tr class="text-left">
        <th class="w-1/2 border p-2">Repository</th>
        <th class="border p-2">Quantity</th>
        <th class="border p-2 text-gray-500 font-normal">Quantity per cent</th>
        <th class="border p-2">Total price</th>
        <th class="border p-2 text-gray-500 font-normal">Price per cent</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="repository in repositoriesPROPS" :key="repository">
        <td class="border p-2">
          <div class="text-gray-500 text-xs">
            {{ repository.namespace }}
          </div>
          <div class="relative flex items-center py-0.5 text-sm">
            <span class="absolute shrink-0 flex items-center justify-center">
              <span class="size-3 rounded-full" :class="repository.color" />
            </span>
            <span class="ml-5 text-gray-900">{{ repository.name }}</span>
          </div>
        </td>
        <td class="border p-2">{{ }}</td>
        <td class="border p-2 text-gray-500">{{  }}%</td>
        <td class="border p-2">${{  }}</td>
        <td class="border p-2 text-gray-500">%</td>
      </tr>
    </tbody>
  </table>
  </div>
</template>
