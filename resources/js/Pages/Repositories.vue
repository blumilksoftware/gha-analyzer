<script setup>
import Papa from 'papaparse'
import moment from 'moment';
import { computed, ref, watch } from 'vue';
import { useLogsStore } from '@/Stores/logsStore';

const logsStore = useLogsStore();
const logs = computed(() => logsStore.getLogs);

const colors = [
        'bg-gray-400',
        'bg-blue-400',
        'bg-red-400',
        'bg-green-400',
        'bg-yellow-400',
        'bg-indigo-400',
        'bg-purple-400',
        'bg-pink-400',
      ] 

var tables = ref({
        logs: {
            items: [],
            sort: 'date',
            order: 'asc'
        }      
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
        color: colors[repositories.length % colors.length]
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

function filterLogsBy (tag) {
    if (tables.value.logs.sort === tag) {
        tables.value.logs.order = tables.value.logs.order === 'desc' ? 'asc' : 'desc'
        return
    }

    tables.value.logs.sort = tag
    tables.value.logs.order = 'desc'
}
function quantityPerAuthor (author) {
    return tables.value.logs.items.filter(log => log.author === author).reduce((a, b) => a + parseInt(b.quantity), 0)
}
function pricePerAuthor (author) {
    return tables.value.logs.items.filter(log => log.author === author).reduce((a, b) => a + parseFloat(b.total), 0).toFixed(3)
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

const authors = computed(() => {
  return Object.values(dictionaries.value.authors).sort((a, b) => a.toLowerCase() > b.toLowerCase() ? 1 : -1)
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

function parseLogs(){
    const data = Papa.parse(logs.value)
    console.log(data)
    const parsedData = data.data

    var parsed = parsedData.slice(1,-1).map((line) => parseLineToLog(line))
    console.log(parsed)
    tables.value.logs.items = parsed 
}

</script>
<template>
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
            <tr v-for="repository in repositories">
                <td class="border p-2">
                    <div class="text-gray-500 text-xs">
                        {{ repository.namespace }}
                    </div>
                    <div class="relative flex items-center py-0.5 text-sm">
                        <span class="absolute flex-shrink-0 flex items-center justify-center">
                            <span class="h-3 w-3 rounded-full" :class="repository.color"></span>
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
    
</template>