<script setup>
import { useLogsStore } from '@/Stores/logsStore';
import Navbar from './Navbar.vue'
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

const {clearLogs, setLogs} = useLogsStore()

const files = ref([]);
const fileInput = ref(null);

function seed () {
    setLogs(sample)
}
function clear () {
    clearLogs()
}

const triggerFileInput = () => {
  fileInput.value.click();
};

const onFileChange = (event) => {
  const selectedFiles = Array.from(event.target.files);
  files.value.push(...selectedFiles);
};
</script>

<template>
    <Navbar/>
    <main class="container my-4 mx-auto bg-white rounded-xl p-4 shadow flex">
        <div class="flex-1 p-4">
            <slot></slot>
        </div>
        
    </main>
    <div class="container mt-6 mb-12 mx-auto">
            <div class="block uppercase text-white font-semibold text-xs tracking-widest">
                <nav class="flex items-center justify-end space-x-4">
                    <span @click="clear" class="cursor-pointer px-8 py-4 rounded-md bg-blue-400 bg-opacity-50 hover:bg-opacity-25">Clear Data</span>
                    <span class="cursor-pointer px-8 py-4 rounded-md bg-blue-400 bg-opacity-50 hover:bg-opacity-25" @click="triggerFileInput">
                        Upload CSV file
                        <input type="file" ref="fileInput" id="uploader" accept=".csv" class="hidden" @change="onFileChange">
                    </span>
                    <span @click="seed" class="cursor-pointer px-8 py-4 rounded-md bg-blue-400 bg-opacity-50 hover:bg-opacity-25">Seed Example Data</span>
                </nav>
            </div>
        </div>
    <footer>
        <div class="w-full flex items-center justify-center text-white">
            <span class="mr-2 font-semibold text-xs tracking-widest">developed by</span>
            <a href="https://blumilk.pl/" target="_blank">
                <img src="blumilk.png" alt="Blumilk" class="w-32">
            </a>
        </div>
    </footer>
</template>