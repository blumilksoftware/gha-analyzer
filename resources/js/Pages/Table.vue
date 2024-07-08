<script setup>
import Papa from 'papaparse'
import moment from 'moment'
import { computed, onMounted, ref, watch, toRefs } from 'vue'
import { useLogsStore } from '@/Stores/logsStore'
import { Head } from '@inertiajs/vue3'

const logsStore = useLogsStore()
const logs = computed(() => logsStore.getLogs)

const props = defineProps({
  colors: {
    type: Array,
    default: () => [],
  },
  runs: {
    type: Array,
  }
})
var colors = props.colors
const { runs } = toRefs(props);

</script>

<template>
  <Head>
    <title>Table</title>
  </Head>
  <br>
  <h1 v-for="run in runs">{{ run.name }}</h1>
  <table class="w-full border-collapse border table-auto mt-4 text-sm">
    <thead>
      <tr class="text-left">
        <th class="border p-2 cursor-pointer" @click="">Date</th>
        <th class="border p-2 cursor-pointer" @click="">Repository</th>
        <th class="border p-2 cursor-pointer" @click="">Quantity</th>
        <th class="border p-2 cursor-pointer" @click="">Price</th>
        <th class="border p-2 w-px">Unit</th>
        <th class="border p-2 cursor-pointer" @click="">Author</th>
        <th class="border p-2">Actions workflow file</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="run in runs">
        <td class="border p-2">
          {{  }}
          <div class="text-gray-500 text-xs">
            {{  }}
          </div>
        </td>
        <td class="border p-2">
          <div class="text-gray-500 text-xs">
            {{ }}
          </div>
          <div class="relative flex items-center py-0.5 text-sm">
            <span class="absolute shrink-0 flex items-center justify-center">
              <span class="size-3 rounded-full" :class="run.repository.color" />
            </span>
            <span class="ml-5 text-gray-900">{{ run.repository.name }}</span>
          </div>
        </td>
        <td class="border p-2">{{ run.quantity }}</td>
        <td class="border p-2">
          <div class="text-gray-500 text-xs">
            {{ run.quantity }} &times; {{ run.price }}
          </div>
          ${{ run.total }}
        </td>
        <td class="border p-2">
          <img :src="run.unitLogo" :alt="run.unit" :title="run.unit" class="w-8">
        </td>
        <td class="border p-2">
          <a v-if="run.author" :href="'https://github.com/' + run.author" class="flex items-center" target="_blank">
            <img :src="'https://github.com/' + run.author + '.png'" class="size-6 rounded-full mr-2" :alt="run.author">
            {{ run.author }}
          </a>
          <span v-else>
            unknown
          </span>
        </td>
        <td class="border p-2">
          {{ run.workflow.replace('.github/workflows/', '') }}
        </td>
      </tr>
      <tr>
        <td class="border p-2" colspan="2" />
        <td class="border p-2">
          <span class="text-gray-500">&Sigma; =</span> {{  }}
        </td>
        <td class="border p-2">
          <span class="text-gray-500">&Sigma; =</span> ${{  }}
        </td>
        <td class="border p-2" />
      </tr>
    </tbody>
  </table>
</template>
