<script lang="ts" setup>
import {type BatchProgress} from '@/Types/BatchProgress'

const props = defineProps<{
  text: string
  progress?: BatchProgress
  class?: string
}>()

const emit = defineEmits(['click'])
</script>

<template>
  <div v-if="!props.progress || props.progress.finished" class="cursor-pointer px-5 py-3 font-bold text-xs rounded-md bg-blue-500 hover:bg-blue-500/75 text-white" :class="props.class" @click="emit('click')">
    {{ props.text }}
  </div>

  <div v-else class="text-center px-5 py-3 text-xs" :class="props.class">
    {{ props.progress.done }} / {{ props.progress.all }}
  </div>
</template>
