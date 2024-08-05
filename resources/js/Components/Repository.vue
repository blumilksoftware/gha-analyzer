<script setup lang="ts">
import colors from '../../data/colors.json'
import {computed} from 'vue'

const props = defineProps<{
  id: number
  name: string
  organization: string
}>()

const color = computed(() => colors[props.id % colors.length])
</script>

<template>
  <a :href="`https://github.com/${props.organization}/${props.name}`" class="block" target="_blank">
    <div class="text-gray-500 text-xs">
      {{ props.organization }}
    </div>
    <div class="relative flex items-center py-0.5 text-sm">
      <span class="absolute shrink-0 flex items-center justify-center">
        <span class="size-3 rounded-full" :class="color" />
      </span>
      <span class="ml-5 text-gray-900">{{ props.name }}</span>
    </div>
  </a>
</template>
