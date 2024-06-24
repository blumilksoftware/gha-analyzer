<script setup>
import { Head } from '@inertiajs/vue3'
import { useLogsStore } from '../Stores/logsStore'

const {logsSample} = useLogsStore()

</script>
<style>
    .title{
        color: red
    }
</style>

<template>
    <h1>{{ logsSample }}</h1>
    <Head>
        <title>GitHub Action simple analyzer</title>
    </Head>
    <div class="container my-4 mx-auto bg-white rounded-xl p-4 shadow flex">
        <div class="flex-1 p-4">
            <textarea class="w-full text-xs font-mono shadow border rounded-xl p-3" :value="logs" rows="20"></textarea>
        </div>
    </div>
</template>