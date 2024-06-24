<script setup>
import Papa from 'papaparse'
import moment from 'moment';
import { computed } from 'vue';

const sample = `Date,Product,SKU,Quantity,Unit Type,Price Per Unit ($),Multiplier,Owner,Repository Slug,Username,Actions Workflow,Notes
2022-05-20,Actions,Compute - UBUNTU,2,minute,0.008,1.0,galaxy,trantor,octocat,.github/workflows/check-pr.yml,
2022-05-20,Actions,Compute - UBUNTU,9,minute,0.008,1.0,galaxy,caprica,octocat,.github/workflows/check-pr.yml,
2022-05-20,Actions,Compute - UBUNTU,8,minute,0.008,1.0,galaxy,mustafar,octocat,.github/workflows/test.yml,
2022-05-20,Actions,Compute - UBUNTU,9,minute,0.008,1.0,galaxy,caprica,octocat,.github/workflows/lint.yml,
2022-05-20,Actions,Compute - UBUNTU,3,minute,0.008,1.0,galaxy,trantor,octocat,.github/workflows/behat.yml,
2022-05-20,Actions,Compute - UBUNTU,3,minute,0.008,1.0,galaxy,mustafar,octocat,.github/workflows/check-pr.yml,
2022-05-20,Actions,Compute - UBUNTU,2,minute,0.008,1.0,galaxy,trantor,octocat,.github/workflows/behat.yml,
2022-05-20,Actions,Compute - UBUNTU,15,minute,0.008,1.0,galaxy,caprica,octocat,.github/workflows/lint.yml,
2022-05-20,Actions,Compute - WINDOWS,7,minute,0.016,1.0,galaxy,mustafar,octocat,.github/workflows/test-windows.yml,
2022-05-20,Actions,Compute - UBUNTU,11,minute,0.008,1.0,galaxy,caprica,octocat,.github/workflows/check-pr.yml,
2022-05-20,Actions,Compute - UBUNTU,2,minute,0.008,1.0,galaxy,mustafar,octocat,.github/workflows/test.yml,
2022-05-20,Actions,Compute - UBUNTU,1,minute,0.008,1.0,galaxy,trantor,dependabot[bot],.github/workflows/check-pr.yml,
2022-05-20,Actions,Compute - UBUNTU,7,minute,0.008,1.0,galaxy,trantor,octocat,.github/workflows/behat.yml,
`

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

var tables = {
        logs: {
            items: [],
            sort: 'date',
            order: 'asc'
        }      
    }
var dictionaries = {
    authors: {},
    repositories: {},
}

function parseLineToLog (line) {
    const repository = {
        slug: line[7] + '/' + line[8],
        name: line[8],
        namespace: line[7] ? line[7] : 'unknown',
        color: colors[repositories.length % colors.length]
    }

    if (!dictionaries.repositories[repository.name]) {
        dictionaries.repositories[repository.name] = repository
        dictionaries.repositories = JSON.parse(JSON.stringify(dictionaries.repositories))
    } else {
        repository.color = dictionaries.repositories[repository.name].color
    }

    let author = line[9]
    if (author === 'dependabot[bot]') {
        author = 'dependabot'
    }

    if (dictionaries.authors[author]) {
        dictionaries.authors[author] = author
        dictionaries.authors = JSON.parse(JSON.stringify(sdictionaries.authors))
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
    if (this.tables.logs.sort === tag) {
        this.tables.logs.order = this.tables.logs.order === 'desc' ? 'asc' : 'desc'
        return
    }

    this.tables.logs.sort = tag
    this.tables.logs.order = 'desc'
}

function getUnitLogo (unit) {
    return './icons/units/' + unit.toLowerCase() + '.png'
}

const logs = computed(() => {
  let data = tables.logs.items

  if (tables.logs.sort) {
    data = data.sort((a, b) => {
      if (!isNaN(a[tables.logs.sort]) && !isNaN(b[tables.logs.sort])) {
        return a[tables.logs.sort] > b[tables.logs.sort] ? 1 : -1
      }

      return b[tables.logs.sort] > a[tables.logs.sort] ? 1 : -1
    })
  }

  if (tables.logs.order === 'desc') {
    data = data.reverse()
  }

  return data
})

const repositories = computed(() => {
  return Object.values(dictionaries.repositories).sort((a, b) => a.name.toLowerCase() > b.name.toLowerCase() ? 1 : -1)
})

const totalQuantity = computed(() => {
  return tables.logs.items.reduce((a, b) => a + parseInt(b.quantity), 0)
})

const totalPrice = computed(() => {
  return tables.logs.items.reduce((a, b) => a + parseFloat(b.total), 0).toFixed(3)
})

const data = Papa.parse(sample)
console.log(data)
const headings = data.data[0]
const parsedData = data.data

var parsed = parsedData.slice(1,-1).map((line) => parseLineToLog(line))
console.log(parsed)
tables.logs.items = parsed

</script>
<template>
    <table class="w-full border-collapse border table-auto mt-4 text-sm">
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
            <tr v-for="log in logs">
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
                        <span class="absolute flex-shrink-0 flex items-center justify-center">
                            <span class="h-3 w-3 rounded-full" :class="log.repository.color"></span>
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
                    <img :src="getUnitLogo(log.unit)" :alt="log.unit" :title="log.unit" class="w-8">
                </td>
                <td class="border p-2">
                    <a :href="'https://github.com/' + log.author" class="flex items-center" target="_blank" v-if="log.author">
                        <img :src="'https://github.com/' + log.author + '.png'" class="w-6 h-6 rounded-full mr-2" :alt="log.author">
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
                <td class="border p-2" colspan="2"></td>
                <td class="border p-2">
                    <span class="text-gray-500">&Sigma; =</span> {{ totalQuantity }}
                </td>
                <td class="border p-2">
                    <span class="text-gray-500">&Sigma; =</span> ${{ totalPrice }}
                </td>
                <td class="border p-2"></td>
            </tr>
        </tbody>
    </table>
</template>