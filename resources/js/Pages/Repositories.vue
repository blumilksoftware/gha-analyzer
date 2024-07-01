<script setup>
import Papa from 'papaparse'
import moment from 'moment'
import { computed, ref, watch } from 'vue'
import { useLogsStore } from '@/Stores/logsStore'
import { Head } from '@inertiajs/vue3'

const colors = ref([])

async function fetchColors() {
    const response = await fetch('api/data/colors');
    const data = await response.json();
    return data.colors;
}
console.log(colors.value)

const logsStore = useLogsStore()
const logs = computed(() => logsStore.getLogs)

var tables = ref({
  logs: {
    items: [],
    sort: 'date',
    order: 'asc',
  },      
})
var dictionaries = ref({
  authors: {},
  repositories: {},
})

function parseLineToLog (line) {
  const repository = {
    slug: line[7] + '/' + line[8],
    name: line[8],
    namespace: line[7] ? line[7] : 'unknown',
    color: colors.value[repositories.value.length % colors.value.length],
  }
  console.log(colors.value)

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

  if (dictionaries.value.authors[author]) {
    dictionaries.value.authors[author] = author
    dictionaries.value.authors = JSON.parse(JSON.stringify(sdictionaries.value.authors))
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

function quantityPerRepository (repository) {
  return tables.value.logs.items.filter(log => log.repository.name === repository.name).reduce((a, b) => a + parseInt(b.quantity), 0)
}
function pricePerRepository (repository) {
  return tables.value.logs.items.filter(log => log.repository.name === repository.name).reduce((a, b) => a + parseFloat(b.total), 0).toFixed(3)
}
const sortedLogs = computed(() => {
  let data = tables.value.logs.items

  if (tables.value.logs.sort) {
    data = data.sort((a, b) => {
      if (!isNaN(a[tables.value.logs.sort]) && !isNaN(b[tables.value.logs.sort])) {
        return a[tables.value.logs.sort] > b[tables.value.logs.sort] ? 1 : -1
      }

      return b[tables.value.logs.sort] > a[tables.value.logs.sort] ? 1 : -1
    })
  }

  if (tables.value.logs.order === 'desc') {
    data = data.reverse()
  }

  return data
})

const repositories = computed(() => {
  return Object.values(dictionaries.value.repositories).sort((a, b) => a.name.toLowerCase() > b.name.toLowerCase() ? 1 : -1)
})

const totalQuantity = computed(() => {
  return tables.value.logs.items.reduce((a, b) => a + parseInt(b.quantity), 0)
})

const totalPrice = computed(() => {
  return tables.value.logs.items.reduce((a, b) => a + parseFloat(b.total), 0).toFixed(3)
})

parseLogs()

watch (logs, () => {
  parseLogs()
}) 

async function parseLogs(){
  const data = Papa.parse(logs.value)
  const parsedData = data.data

  colors.value = await fetchColors()
  console.log(colors.value)

  var parsed = parsedData.slice(1,-1).map((line) => parseLineToLog(line))
  tables.value.logs.items = parsed 
}

</script>

<template>
  <Head>
    <title>Repositories</title>
  </Head>
  <table v-if="sortedLogs.length > 0" class="w-full border-collapse border table-fixed mt-4 text-sm">
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
      <tr v-for="repository in repositories" :key="repository">
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
        <td class="border p-2">{{ quantityPerRepository(repository) }}</td>
        <td class="border p-2 text-gray-500">{{ (quantityPerRepository(repository) * 100 / totalQuantity).toFixed(2) }}%</td>
        <td class="border p-2">${{ pricePerRepository(repository) }}</td>
        <td class="border p-2 text-gray-500">{{ (pricePerRepository(repository) * 100 / totalPrice).toFixed(2) }}%</td>
      </tr>
    </tbody>
  </table>
  <h1 v-else>No logs loaded</h1>
</template>
