<script setup lang="ts">
import { computed } from 'vue'
import { Head } from '@inertiajs/vue3'
import SortableTable from '@/Components/SortableTable.vue'
import Github from '@/Components/Github.vue'

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

const totalQuantity = computed(() => {
  return props.data.reduce((a, b) => a + b.minutes, 0)
})

const totalPrice = computed(() => {
  return props.data.reduce((a, b) => a + (b.price), 0)
})
</script>

<template>
  <Head>
    <title>Authors</title>
  </Head>

  <SortableTable
    :data="props.data"
    :header="[
      { key: 'name', text: 'Author' },
      { key: 'minutes', text: 'Quantity' },
      { key: 'minutes_per_cent', text: 'Quantity per cent', sort_by: 'minutes' },
      { key: 'price', text: 'Total price' },
      { key: 'price_per_cent', text: 'Price per cent', sort_by: 'price' },
    ]"
  >
    <template #cell(name)="{item}">
      <Github :name="item.name" :avatar="item.avatar_url" />
    </template>

    <template #cell(minutes)="{item}">
      {{ item.minutes.toFixed(2) }}
    </template>

    <template #cell(minutes_per_cent)="{item}">
      <p class="text-gray-500">{{ (item.minutes * 100 / totalQuantity).toFixed(2) }}% </p>
    </template>

    <template #cell(price)="{item}">
      {{ item.price.toFixed(2) }}
    </template>

    <template #cell(price_per_cent)="{item}">
      <p class="text-gray-500">{{ (item.price * 100 / totalPrice).toFixed(2) }}% </p>
    </template>
  </SortableTable>
</template>
