<script setup>
import { useLogsStore } from '@/Stores/logsStore'
import Navbar from './Navbar.vue'
import { ref } from 'vue'

const {clearLogs, setLogs} = useLogsStore()

const files = ref([])
const fileInput = ref(null)

const sampleDataURL = '../../data/sampleData.json'

async function fetchData(data) {
  try {
    const response = await fetch(new URL(data, import.meta.url).href)
    const jsonData = await response.json()
    return jsonData
    
  } catch (error) {
    console.error('Error loading data:', error)
  }
}

async function seed () {
  const jsonData = await fetchData(sampleDataURL)
  const sample = jsonData.sample
  
  setLogs(sample)
  
}
function clear () {
  clearLogs()
}

const triggerFileInput = () => {
  fileInput.value.click()
}

const onFileChange = (event) => {
  const selectedFiles = Array.from(event.target.files)
  files.value.push(...selectedFiles)
}
</script>

<template>
  <Navbar />
  <main class="container my-4 mx-auto bg-white rounded-xl p-4 shadow flex">
    <div class="flex-1 p-4">
      <slot />
    </div>
  </main>
  <div class="container mt-6 mb-12 mx-auto">
    <div class="block uppercase text-white font-semibold text-xs tracking-widest">
      <nav class="flex items-center justify-end space-x-4">
        <span class="cursor-pointer px-8 py-4 rounded-md bg-blue-400 bg-opacity-50 hover:bg-opacity-25" @click="clear">Clear Data</span>
        <span class="cursor-pointer px-8 py-4 rounded-md bg-blue-400 bg-opacity-50 hover:bg-opacity-25" @click="triggerFileInput">
          Upload CSV file
          <input id="uploader" ref="fileInput" type="file" accept=".csv" class="hidden" @change="onFileChange">
        </span>
        <span class="cursor-pointer px-8 py-4 rounded-md bg-blue-400 bg-opacity-50 hover:bg-opacity-25" @click="seed">Seed Example Data</span>
      </nav>
    </div>
  </div>
  <footer>
    <div class="w-full flex items-center justify-center text-white">
      <span class="mr-2 font-semibold text-xs tracking-widest">developed by</span>
      <a href="https://blumilk.pl/" target="_blank">
        <img class="w-32" src="../../assets/images/blumilk.png" alt="Blumilk">
      </a>
    </div>
  </footer>
</template>
