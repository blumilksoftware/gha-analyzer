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
    <Head>
        <title>GitHub Action simple analyzer</title>
    </Head>
    <textarea class="w-full text-xs font-mono shadow border rounded-xl p-3" :value="logsSample" rows="20"></textarea>
</template>