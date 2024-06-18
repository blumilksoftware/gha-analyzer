<script setup>
import { Link, Head } from '@inertiajs/vue3'
import { ref } from 'vue'

const sample = `Date,Product,SKU,Quantity,Unit Type,Price Per Unit ($),Multiplier,Owner,Repository Slug,Username,Actions Workflow,Notes
2022-05-20,Actions,Compute - UBUNTU,2,minute,0.008,1.0,galaxy,trantor,octocat,.github/workflows/check-pr.yml,
2022-05-20,Actions,Compute - UBUNTU,9,minute,0.008,1.0,galaxy,caprica,octocat,.github/workflows/check-pr.yml,
2022-05-20,Actions,Compute - UBUNTU,8,minute,0.008,1.0,galaxy,mustafar,octocat,.github/workflows/test.yml,
2022-05-20,Actions,Compute - UBUNTU,9,minute,0.008,1.0,galaxy,caprica,octocat,.github/workflows/lint.yml,
2022-05-20,Actions,Compute - UBUNTU,3,minute,0.008,1.0,galaxy,trantor,octocat,.github/workflows/behat.yml,
2022-05-20,Actions,Compute - UBUNTU,3,minute,0.008,1.0,galaxy,mustafar,octocat,.github/workflows/check-pr.yml,
2022-05-20,Actions,Compute - UBUNTU,2,minute,0.008,1.0,galaxy,trantor,octocat,.github/workflows/behat.yml,
2022-05-20,Actions,Compute - UBUNTU,15,minute,0.008,1.0,galaxy,caprica,octocat,.github/workflows/lint.yml,
2022-05-20,Actions,Compute - WINDOWS,7,minute,0.016,1.0,galaxy,mustafar,octocat,.github/workflows/test-windows.yml,
2022-05-20,Actions,Compute - UBUNTU,11,minute,0.008,1.0,galaxy,caprica,octocat,.github/workflows/check-pr.yml,
2022-05-20,Actions,Compute - UBUNTU,2,minute,0.008,1.0,galaxy,mustafar,octocat,.github/workflows/test.yml,
2022-05-20,Actions,Compute - UBUNTU,1,minute,0.008,1.0,galaxy,trantor,dependabot[bot],.github/workflows/check-pr.yml,
2022-05-20,Actions,Compute - UBUNTU,7,minute,0.008,1.0,galaxy,trantor,octocat,.github/workflows/behat.yml,
`
const logs = ref('')
const logs_loaded = ref(false)
const titleClass = ref('title')

function seed () {
    logs.value = sample
    logs_loaded.value = true
}
function clear () {
    logs.value = ''
    logs_loaded.value = false
}

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
    <div class="container my-4 mx-auto bg-white rounded-xl p-4 shadow flex">
        <div class="flex-1 p-4">
            <textarea class="w-full text-xs font-mono shadow border rounded-xl p-3" :value="logs" rows="20"></textarea>
        </div>
    </div>
    <div class="container mt-6 mb-12 mx-auto">
        <div class="block uppercase text-white font-semibold text-xs tracking-widest">
            <nav class="flex items-center justify-end space-x-4">
                <span @click="clear" class="cursor-pointer px-8 py-4 rounded-md bg-blue-400 bg-opacity-50 hover:bg-opacity-25">Clear Data</span>
                <span @click="clear" class="cursor-pointer px-8 py-4 rounded-md bg-blue-400 bg-opacity-50 hover:bg-opacity-25">Upload CSV File</span>
                <span @click="seed" class="cursor-pointer px-8 py-4 rounded-md bg-blue-400 bg-opacity-50 hover:bg-opacity-25">Seed Example Data</span>
            </nav>
        </div>
    </div>
</template>