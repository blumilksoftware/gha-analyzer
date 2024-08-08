<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import {type Organization} from '@/Types/Organization'
import Github from '@/Components/Github.vue'
import ProgressButton from '@/Components/ProgressButton.vue'
import SortableTable from '@/Components/SortableTable.vue'
import moment from 'moment'
import axios from 'axios'
import { router } from '@inertiajs/vue3'
import {type BatchProgress} from '@/Types/BatchProgress'

const props = defineProps<{
  data: Organization[]
  progress: Record<string, BatchProgress>
}>()

const handleClick = async (organization: string) => {
  await axios.post(`/organization/${organization}/fetch`)
  router.visit('/organization', {  preserveScroll: true })
}

const isFetching = (organization: string) => {
  return props.progress[organization] && !props.progress[organization].finished
}

if (Object.keys(props.progress).filter(isFetching).length > 0) {
  setTimeout(() => router.visit('/organization', {  preserveScroll: true }), 1000)
}
</script>

<template>
  <Head>
    <title>Organizations</title>
  </Head>

  <SortableTable
    :data="props.data"
    :header="[
      { key: 'name', text: 'Organization' },
      { key: 'repos', text: 'Repositories' },
      { key: 'users', text: 'Users' },
      { key: 'runs', text: 'Runs' },
      { key: 'jobs', text: 'Jobs' },
      { key: 'minutes', text: 'Minutes' },
      { key: 'price', text: 'Price' },
      { key: 'actors', text: 'Actors' },
      { key: 'fetched_at', text: 'Update' }
    ]"
  >
    <template #cell(name)="{item}">
      <Github :name="item.name" :avatar="item.avatar_url" />
    </template>

    <template #cell(repos)="{item}">
      {{ item.repos }}
    </template>

    <template #cell(users)="{item}">
      {{ item.users }}
    </template>

    <template #cell(runs)="{item}">
      {{ item.runs }}
    </template>

    <template #cell(jobs)="{item}">
      {{ item.jobs }}
    </template>

    <template #cell(actors)="{item}">
      {{ item.actors }}
    </template>

    <template #cell(minutes)="{item}">
      {{ item.minutes.toFixed(2) }}
    </template>

    <template #cell(price)="{item}">
      {{ item.price.toFixed(2) }}$
    </template>

    <template #cell(fetched_at)="{item}">
      <div class="flex justify-between items-center">
        <p class="text-xs text-black px-2"><b>Last Update:</b> {{ item.fetched_at ? moment(item.fetched_at).fromNow() : 'never' }}</p>
        <ProgressButton text="Fetch" method="post" :progress="props.progress[item.id]" class="mr-5" @click="handleClick(item.id)" />
      </div>
    </template>
  </SortableTable>
</template>
