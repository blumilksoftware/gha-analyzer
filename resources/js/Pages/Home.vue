<script setup>
import { Head } from '@inertiajs/vue3'
import { useLogsStore } from '../Stores/logsStore'
import { computed } from 'vue';

const logsStore = useLogsStore();
const logs = computed(() => logsStore.getLogs);

</script>
<template>
    <Head>
        <title>GitHub Action simple analyzer</title>
    </Head>
    <textarea class="w-full text-xs font-mono shadow border rounded-xl p-3" :value="logs" rows="20"></textarea>
</template>