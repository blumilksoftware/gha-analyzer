import { defineStore } from "pinia";
import { ref } from "vue";

export const useLogsStore = defineStore('logs', ()=>{

    const logs = ref('')

    function clearLogs() {
        logs.value = ''
        console.log('cleared')
    }

    function setLogs(newLogs) {
        logs.value = newLogs
        console.log('setlogs')
    }

    return {
        logs,
        clearLogs,
        setLogs
    }
})