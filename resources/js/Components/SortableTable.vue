<script setup lang="ts" generic="T extends { id: number }">
import { defineProps } from 'vue'
import { withSort } from '@/Utils/sort'

const props = defineProps<{
  data: T[]
  header: Array<{ text: string, key: keyof T | string, sort_by?: keyof T}>
  footer?: boolean
}>()

const {sorted, sortBy} = withSort(props.data, 'id')
</script>

<template>
  <table v-if="sorted.length > 0" class="w-full border-collapse border table-auto mt-4 text-sm">
    <thead>
      <tr class="text-left">
        <th
          v-for="column in props.header"
          :key="column.key"
          class="border p-2 cursor-pointer" @click="sortBy(column.sort_by ?? column.key as keyof T)"
        >
          {{ column.text }}
        </th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="row in sorted" :key="row.id">
        <td v-for="column in props.header" :key="column.key" class="border p-2">
          <slot
            :name="`cell(${String(column.key)})`"
            :item="row"
          />
        </td>
      </tr>
      <tr v-if="props.footer">
        <td v-for="column in props.header" :key="column.key" class="border p-2">
          <slot
            :name="`footer(${String(column.key)})`"
          />
        </td>
      </tr>
    </tbody>
  </table>
  <h1 v-else>No logs loaded</h1>
</template>

