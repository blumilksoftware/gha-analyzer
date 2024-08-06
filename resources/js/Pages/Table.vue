<script setup lang="ts">
import {computed, defineProps} from 'vue'
import {Head} from '@inertiajs/vue3'
import moment from 'moment'
import SortableTable from '@/Components/SortableTable.vue'
import Repository from '@/Components/Repository.vue'
import Github from '@/Components/Github.vue'

interface WorkflowRun {
  id: number
  date: number
  organization: string
  repository: string
  repository_id: number
  minutes: number
  price_per_minute: number
  total_price: number
  workflow: string
  os: string
  actor: {
    id: number
    name: string
    github_id: number
    avatar_url: string
  }
}

const props = defineProps<{
  data: WorkflowRun[]
}>()

const totalMinutes = computed(() => {
  return props.data.reduce((a, b) => a + b.minutes, 0)
})

const totalPrice = computed(() => {
  return props.data.reduce((a, b) => a + b.total_price, 0).toFixed(3)
})

function getOSLogo(os: string) {
  return new URL(`../../assets/images/units/${os}.png`, import.meta.url).href
}

</script>

<template>
  <Head>
    <title>Table</title>
  </Head>
  <SortableTable
    footer
    :data="props.data"
    :header="[
      { key: 'date', text: 'Date' },
      { key: 'repository', text: 'Repository' },
      { key: 'minutes', text: 'Quantity' },
      { key: 'total_price', text: 'Price' },
      { key: 'os', text: 'Unit' },
      { key: 'actor', text: 'Author' },
      { key: 'workflow', text: 'Actions workflow file' }
    ]"
  >
    <template #cell(date)="{item}">
      {{ item.date }}
      <div class="text-gray-500 text-xs">
        {{ moment(item.date).fromNow() }}
      </div>
    </template>

    <template #cell(repository)="{item}">
      <Repository :id="item.repository_id" :organization="item.organization" :name="item.repository" />
    </template>

    <template #cell(minutes)="{item}">
      {{ item.minutes }}
    </template>

    <template #cell(total_price)="{item}">
      <div class="text-gray-500 text-xs">
        {{ item.minutes }} * {{ item.price_per_minute }}
      </div>
      ${{ item.total_price }}
    </template>

    <template #cell(os)="{item}">
      <img :src="getOSLogo(item.os)" :alt="item.os" class="w-8">
    </template>

    <template #cell(actor)="{item}">
      <Github :name="item.actor.name" :avatar="item.actor.avatar_url" />
    </template>

    <template #cell(workflow)="{item}">
      {{ item.workflow }}
    </template>

    <template #footer(minutes)>
      <span class="text-gray-500">&Sigma; =</span> {{ totalMinutes }}
    </template>

    <template #footer(total_price)>
      <span class="text-gray-500">&Sigma; =</span> ${{ totalPrice }}
    </template>
  </SortableTable>
</template>
