<script setup lang="ts">
import { computed } from 'vue'
import { Head } from '@inertiajs/vue3'
import Repository from '@/Components/Repository.vue'
import { withSort } from '@/Utils/sort'

interface RepositoryData {
  id: number
  name: string
  organization: string
  avatar_url: string
  minutes: number
  price: number
}

const props = defineProps<{
  data: RepositoryData[]
}>()

const { data, sorted, filterBy } = withSort(props.data, 'id')

const totalQuantity = computed(() => {
  return data.value.items.reduce((a, b) => a + b.minutes, 0)
})

const totalPrice = computed(() => {
  return data.value.items.reduce((a, b) => a + b.price, 0)
})

</script>

<template>
  <Head>
    <title>Repositories</title>
  </Head>

  <table v-if="sorted.length > 0" class="w-full border-collapse border table-fixed mt-4 text-sm">
    <thead>
      <tr class="text-left">
        <th class="w-1/2 border p-2 cursor-pointer" @click="filterBy('name')">Repository</th>
        <th class="border p-2 cursor-pointer" @click="filterBy('minutes')">Quantity</th>
        <th class="border p-2 text-gray-500 font-normal cursor-pointer" @click="filterBy('minutes')">Quantity per cent</th>
        <th class="border p-2 cursor-pointer" @click="filterBy('price')">Total price</th>
        <th class="border p-2 text-gray-500 font-normal cursor-pointer" @click="filterBy('price')">Price per cent</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="repository in sorted" :key="repository.id">
        <td class="border p-2">
          <Repository :id="repository.id" :name="repository.name" :organization="repository.organization" />
        </td>
        <td class="border p-2">{{ repository.minutes.toFixed(2) }}</td>
        <td class="border p-2 text-gray-500">{{ (repository.minutes * 100 / totalQuantity).toFixed(2) }}%</td>
        <td class="border p-2">${{ repository.price.toFixed(2) }}</td>
        <td class="border p-2 text-gray-500">{{ (repository.price * 100 / totalPrice).toFixed(2) }}%</td>
      </tr>
    </tbody>
  </table>
  <h1 v-else>No logs loaded</h1>
</template>
