<script setup lang="ts">
import moment from 'moment'
import {computed, defineProps} from 'vue'
import {Head} from '@inertiajs/vue3'
import Repository from '@/Components/Repository.vue'
import { withSort } from '@/Utils/sort'

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

const { data, sorted, filterBy } = withSort(props.data, 'id')

const totalQuantity = computed(() => {
  return data.value.items.reduce((a, b) => a + b.minutes, 0)
})

const totalPrice = computed(() => {
  return data.value.items.reduce((a, b) => a + b.total_price, 0).toFixed(3)
})

function getOSLogo(os: string) {
  return new URL(`../../assets/images/units/${os}.png`, import.meta.url).href
}

</script>

<template>
  <Head>
    <title>Table</title>
  </Head>
  <table v-if="sorted.length > 0" class="w-full border-collapse border table-auto mt-4 text-sm">
    <thead>
      <tr class="text-left">
        <th class="border p-2 cursor-pointer" @click="filterBy('date')">Date</th>
        <th class="border p-2 cursor-pointer" @click="filterBy('repository')">Repository</th>
        <th class="border p-2 cursor-pointer" @click="filterBy('minutes')">Quantity</th>
        <th class="border p-2 cursor-pointer" @click="filterBy('total_price')">Price</th>
        <th class="border p-2 w-px cursor-pointer" @click="filterBy('os')">Unit</th>
        <th class="border p-2 cursor-pointer" @click="filterBy('organization')">Author</th>
        <th class="border p-2 cursor-pointer" @click="filterBy('workflow')">Actions workflow file</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="run in sorted" :key="run.id">
        <td class="border p-2">
          {{ run.date }}
          <div class="text-gray-500 text-xs">
            {{ moment(run.date).fromNow() }}
          </div>
        </td>
        <td class="border p-2">
          <Repository :id="run.repository_id" :name="run.repository" :organization="run.organization" />
        </td>
        <td class="border p-2">{{ run.minutes }}</td>
        <td class="border p-2">
          <div class="text-gray-500 text-xs">
            {{ run.minutes }} &times; {{ run.price_per_minute }}
          </div>
          ${{ run.total_price }}
        </td>
        <td class="border p-2">
          <img :src="getOSLogo(run.os)" :alt="run.os" :title="run.os" class="w-8">
        </td>
        <td class="border p-2">
          <a v-if="run.actor" :href="'https://github.com/' + run.actor.name" class="flex items-center" target="_blank">
            <img :src="run.actor.avatar_url" class="size-6 rounded-full mr-2" :alt="run.actor.name">
            {{ run.actor.name }}
          </a>
          <span v-else>
            unknown
          </span>
        </td>
        <td class="border p-2">
          {{ run.workflow }}
        </td>
      </tr>
      <tr>
        <td class="border p-2" colspan="2" />
        <td class="border p-2">
          <span class="text-gray-500">&Sigma; =</span> {{ totalQuantity }}
        </td>
        <td class="border p-2">
          <span class="text-gray-500">&Sigma; =</span> ${{ totalPrice }}
        </td>
        <td class="border p-2" />
      </tr>
    </tbody>
  </table>
  <h1 v-else>No logs loaded</h1>
</template>
