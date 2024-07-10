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
  repositories: {
    type: Array,
  },

})

console.log(props.repositories)
var colors = props.colors



function toLocalDate(date){
  var dateObj = new Date(date)
  var formattedDate = dateObj.toLocaleDateString()
  return formattedDate
}

</script>

<template>
  <Head>
    <title>Table</title>
  </Head>
  <br>
  <table class="w-full border-collapse border table-auto mt-4 text-sm">
    <thead>
      <tr class="text-left">
        <th class="border p-2 cursor-pointer" @click="">Date</th>
        <th class="border p-2 cursor-pointer" @click="">Repository</th>
        <th class="border p-2 cursor-pointer" @click=""># of Jobs</th>
        <th class="border p-2 cursor-pointer" @click="">repo Price</th>
        <th class="border p-2">Workflow repo Name</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="repo in props.repositories">
        <td class="border p-2">
          {{ toLocalDate(repo.created_at) }}
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
              <span class="size-3 rounded-full"  />
            </span>
            <span class="ml-5 text-gray-900">{{ repo.name }}</span>
          </div>
        </td>
        <td class="border p-2">{{   }}</td>
        <td class="border p-2">
          ${{  }}
        </td>
        <td class="border p-2">
          {{ repo.name }}
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
