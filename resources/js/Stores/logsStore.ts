import { defineStore } from 'pinia'

export const useLogsStore = defineStore('logs', {

  state: () => ({
    logs: '',
  }),
  getters: {
    getLogs: (state) => state.logs,
  },
  actions: {
    setLogs(newLogs: string) {
      this.logs = newLogs
    },
    clearLogs() {
      this.logs = ''
    },
  },

})
