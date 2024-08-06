<script setup lang="ts">
import { computed } from 'vue'
import { Head } from '@inertiajs/vue3'
import Repository from '@/Components/Repository.vue'
import SortableTable from '@/Components/SortableTable.vue'
import { type Repository as RepositoryData } from '@/Types/Repository'

const props = defineProps<{
  data: RepositoryData[]
}>()

const totalQuantity = computed(() => {
  return props.data.reduce((a, b) => a + b.minutes, 0)
})

const totalPrice = computed(() => {
  return props.data.reduce((a, b) => a + b.price, 0)
})

</script>

<template>
  <Head>
    <title>Repositories</title>
  </Head>

  <SortableTable
    :data="props.data"
    :header="[
      { key: 'name', text: 'Repository' },
      { key: 'minutes', text: 'Quantity' },
      { key: 'minutes_per_cent', text: 'Quantity per cent', sort_by: 'minutes' },
      { key: 'price', text: 'Total price' },
      { key: 'price_per_cent', text: 'Price per cent', sort_by: 'price' },
    ]"
  >
    <template #cell(name)="{item}">
      <Repository :id="item.id" :name="item.name" :organization="item.organization" />
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
