import { defineStore } from "pinia";
import { ref } from "vue";

export const useLogsStore = defineStore('logs', {

    state: () => ({
        logs: ''
      }),
      getters: {
        getLogs: (state) => state.logs,
      },
      actions: {
        setLogs(newLogs) {
            this.logs = newLogs;
        },
        clearLogs() {
            this.logs = ''
        },
      },

})