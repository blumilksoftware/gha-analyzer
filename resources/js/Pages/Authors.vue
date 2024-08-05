<script setup lang="ts">
import { computed } from 'vue'
import { Head } from '@inertiajs/vue3'
import { withSort } from '@/Utils/sort'

interface Author {
  id: number
  name: string
  github_id: number
  avatar_url: string
  minutes: number
  price: number
}

const props = defineProps<{
  data: Author[]
}>()

const { data, sorted, filterBy } = withSort(props.data, 'id')

const totalQuantity = computed(() => {
  return data.value.items.reduce((a, b) => a + b.minutes, 0)
})

const totalPrice = computed(() => {
  return data.value.items.reduce((a, b) => a + (b.price), 0)
})

</script>

<template>
  <Head>
    <title>Authors</title>
  </Head>
  <table v-if="sorted.length > 0" class="w-full border-collapse border table-fixed mt-4 text-sm">
    <thead>
      <tr class="text-left">
        <th class="w-1/2 border p-2 cursor-pointer" @click="filterBy('name')">Author</th>
        <th class="border p-2 cursor-pointer" @click="filterBy('minutes')">Quantity</th>
        <th class="border p-2 text-gray-500 font-normal cursor-pointer" @click="filterBy('minutes')">Quantity per cent</th>
        <th class="border p-2 cursor-pointer" @click="filterBy('price')">Total price</th>
        <th class="border p-2 text-gray-500 font-normal cursor-pointer" @click="filterBy('price')">Price per cent</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="author in sorted" :key="author.id" class="h-12">
        <td class="border p-2">
          <a v-if="author" :href="'https://github.com/' + author.name" class="flex items-center" target="_blank">
            <img :src="author.avatar_url" class="size-6 rounded-full mr-2" :alt="author.name">
            {{ author.name }}
          </a>
          <span v-else class="ml-8">unknown author</span>
        </td>
        <td class="border p-2">{{ author.minutes.toFixed(2) }}</td>
        <td class="border p-2 text-gray-500">{{ (author.minutes * 100 / totalQuantity).toFixed(2) }}%</td>
        <td class="border p-2">${{ author.price.toFixed(2) }}</td>
        <td class="border p-2 text-gray-500">{{ (author.price * 100 / totalPrice).toFixed(2) }}%</td>
      </tr>
    </tbody>
  </table>
  <h1 v-else>No logs loaded</h1>
</template>
