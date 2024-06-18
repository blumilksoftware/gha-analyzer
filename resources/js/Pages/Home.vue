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
    <h1>Home/Raw Log</h1>
    <div class="container">

    </div>
    <div class="container">
        <slot></slot>
        <textarea :value="logs"></textarea>
    </div>
    <div>
        <div>
            <button @click="clear">Clear Data</button>
            <button @click="seed">Seed Example Data</button>
        </div>
    </div>
    <div>
        <footer>
            
        </footer>
    </div>
</template>