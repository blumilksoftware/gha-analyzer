<script setup>
import { Head } from '@inertiajs/vue3'
import { useLogsStore } from '../Stores/logsStore'
import { ref } from 'vue';

const {logs} = useLogsStore()
const aaa = ref(logs.value)

</script>
<style>
    .title{
        color: red
    }
</style>

<template>
    <Head>
        <title>GitHub Action simple analyzer</title>
    </Head>
    <h1>{{ aaa }}</h1>
    <textarea class="w-full text-xs font-mono shadow border rounded-xl p-3" :value="logs" rows="20"></textarea>
</template>