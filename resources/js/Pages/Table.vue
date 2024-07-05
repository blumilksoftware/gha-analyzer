<script setup>
import Papa from 'papaparse'
import moment from 'moment'
import { computed, ref, watch } from 'vue'
import { useLogsStore } from '@/Stores/logsStore'
import { Head } from '@inertiajs/vue3'

const logsStore = useLogsStore()
const logs = computed(() => logsStore.getLogs)

const props = defineProps({
  colors: {
    type: Array,
    default: () => [],
  },
  jobs: {
    type: Array,
  },
  runs: {
    type: Array,
  }
})
var colors = props.colors

const tables = ref({
  logs: {
    items: [],
    sort: 'date',
    order: 'asc',
  },
})

const dictionaries = ref({
  authors: {},
  repositories: {},
})

function parseLineToLog(line) {
  const repository = {
    slug: line[7] + '/' + line[8],
    name: line[8],
    namespace: line[7] ? line[7] : 'unknown',
    color: colors[Object.keys(dictionaries.value.repositories).length % colors.length],
  }

  if (!dictionaries.value.repositories[repository.name]) {
    dictionaries.value.repositories[repository.name] = repository
    dictionaries.value.repositories = JSON.parse(JSON.stringify(dictionaries.value.repositories))
  } else {
    repository.color = dictionaries.value.repositories[repository.name].color
  }

  let author = line[9]
  if (author === 'dependabot[bot]') {
    author = 'dependabot'
  }

  if (!dictionaries.value.authors[author]) {
    dictionaries.value.authors[author] = author
    dictionaries.value.authors = JSON.parse(JSON.stringify(dictionaries.value.authors))
  }

  return {
    date: line[0],
    dateForHumans: moment(line[0]).fromNow(),
    product: line[1],
    repository: repository,
    slug: repository.slug,
    quantity: parseInt(line[3]),
    unit: line[2].replace('Compute - ', '').toLowerCase(),
    price: line[5],
    total: (line[3] * line[5]).toFixed(3),
    author: author,
    workflow: line[10],
    notes: line[11],
  }
}

function filterLogsBy(tag) {
  if (tables.value.logs.sort === tag) {
    tables.value.logs.order = tables.value.logs.order === 'desc' ? 'asc' : 'desc'
  } else {
    tables.value.logs.sort = tag
    tables.value.logs.order = 'desc'
  }
}

function getUnitLogo(unit) {
  return new URL(`../../assets/images/units/${unit}.png`, import.meta.url).href
}

const sortedLogs = computed(() => {
  let data = [...tables.value.logs.items]

  if (tables.value.logs.sort) {
    data = data.sort((a, b) => {
      if (typeof a[tables.value.logs.sort] === 'number' && typeof b[tables.value.logs.sort] === 'number') {
        return a[tables.value.logs.sort] - b[tables.value.logs.sort]
      }
      return a[tables.value.logs.sort] > b[tables.value.logs.sort] ? 1 : -1
    })
  }

  if (tables.value.logs.order === 'desc') {
    data = data.reverse()
  }

  return data
})

const logsWithIcons = computed(() => {
  return sortedLogs.value.map(log => ({
    ...log,
    unitLogo: getUnitLogo(log.unit),
  }))
})

const totalQuantity = computed(() => {
  return tables.value.logs.items.reduce((a, b) => a + parseInt(b.quantity), 0)
})

const totalPrice = computed(() => {
  return tables.value.logs.items.reduce((a, b) => a + parseFloat(b.total), 0).toFixed(3)
})

function parseLogs() {
  const data = Papa.parse(logs.value, { header: false })
  const parsedData = data.data

  const parsed = parsedData.slice(1, -1).map((line) => parseLineToLog(line))
  tables.value.logs.items = parsed
}

watch(logs, () => {
  parseLogs()
}, { immediate: true })

</script>

<template>
  <Head>
    <title>Table</title>
  </Head>
  <h1>{{ jobs }}</h1>
  <br>
  <h1>{{ runs }}</h1>
  <table v-if="logsWithIcons.length > 0" class="w-full border-collapse border table-auto mt-4 text-sm">
    <thead>
      <tr class="text-left">
        <th class="border p-2 cursor-pointer" @click="filterLogsBy('date')">Date</th>
        <th class="border p-2 cursor-pointer" @click="filterLogsBy('slug')">Repository</th>
        <th class="border p-2 cursor-pointer" @click="filterLogsBy('quantity')">Quantity</th>
        <th class="border p-2 cursor-pointer" @click="filterLogsBy('total')">Price</th>
        <th class="border p-2 w-px">Unit</th>
        <th class="border p-2 cursor-pointer" @click="filterLogsBy('author')">Author</th>
        <th class="border p-2">Actions workflow file</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="log in logsWithIcons" :key="log">
        <td class="border p-2">
          {{ log.date }}
          <div class="text-gray-500 text-xs">
            {{ log.dateForHumans }}
          </div>
        </td>
        <td class="border p-2">
          <div class="text-gray-500 text-xs">
            {{ log.repository.namespace }}
          </div>
          <div class="relative flex items-center py-0.5 text-sm">
            <span class="absolute shrink-0 flex items-center justify-center">
              <span class="size-3 rounded-full" :class="log.repository.color" />
            </span>
            <span class="ml-5 text-gray-900">{{ log.repository.name }}</span>
          </div>
        </td>
        <td class="border p-2">{{ log.quantity }}</td>
        <td class="border p-2">
          <div class="text-gray-500 text-xs">
            {{ log.quantity }} &times; {{ log.price }}
          </div>
          ${{ log.total }}
        </td>
        <td class="border p-2">
          <img :src="log.unitLogo" :alt="log.unit" :title="log.unit" class="w-8">
        </td>
        <td class="border p-2">
          <a v-if="log.author" :href="'https://github.com/' + log.author" class="flex items-center" target="_blank">
            <img :src="'https://github.com/' + log.author + '.png'" class="size-6 rounded-full mr-2" :alt="log.author">
            {{ log.author }}
          </a>
          <span v-else>
            unknown
          </span>
        </td>
        <td class="border p-2">
          {{ log.workflow.replace('.github/workflows/', '') }}
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
