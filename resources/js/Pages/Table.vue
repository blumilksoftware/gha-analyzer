<script>
export default {
    methods: {
        totalQuantity () {
            return this.tables.logs.items.reduce((a, b) => a + parseInt(b.quantity), 0)
          },
          totalPrice () {
            return this.tables.logs.items.reduce((a, b) => a + parseFloat(b.total), 0).toFixed(3)
          },
    }
}
</script>
<template>
    Table Page
    <div class="flex-1 p-4">
        <table class="w-full border-collapse border table-auto mt-4 text-sm">
            <thead>
                <tr class="text-left">
                    <th class="border p-2 cursor-pointer" @click="filterLogsBy('date')">Date</th>
                    <th class="border p-2 cursor-pointer" @click="filterLogsBy('slug')">Repository</th>
                    <th class="border p-2 cursor-pointer" @click="filterLogsBy('quantity')">Quantity</th>
                    <th class="border p-2 cursor-pointer" @click="filterLogsBy('total')">Price</th>
                    <th class="border p-2 w-px">Unit</th>
                    <th class="border p-2 cursor-pointer" @click="filterLogsBy('author')">Author</th>
                    <th class="border p-2">Actions workflow file</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="log in logs">
                    <td class="border p-2">
                        {{ log.date }}
                        <div class="text-gray-500 text-xs">
                            {{ log.dateForHumans }}
                        </div>
                    </td>
                    <td class="border p-2">
                        <div class="text-gray-500 text-xs">
                            {{ log.repository.namespace }}
                        </div>
                        <div class="relative flex items-center py-0.5 text-sm">
                            <span class="absolute flex-shrink-0 flex items-center justify-center">
                                <span class="h-3 w-3 rounded-full" :class="log.repository.color"></span>
                            </span>
                            <span class="ml-5 text-gray-900">{{ log.repository.name }}</span>
                        </div>
                    </td>
                    <td class="border p-2">{{ log.quantity }}</td>
                    <td class="border p-2">
                        <div class="text-gray-500 text-xs">
                            {{ log.quantity }} &times; {{ log.price }}
                        </div>
                        ${{ log.total }}
                    </td>
                    <td class="border p-2">
                        <img :src="getUnitLogo(log.unit)" :alt="log.unit" :title="log.unit" class="w-8">
                    </td>
                    <td class="border p-2">
                        <a :href="'https://github.com/' + log.author" class="flex items-center" target="_blank" v-if="log.author">
                            <img :src="'https://github.com/' + log.author + '.png'" class="w-6 h-6 rounded-full mr-2" :alt="log.author">
                            {{ log.author }}
                        </a>
                        <span v-else>
                            unknown
                        </span>
                    </td>
                    <td class="border p-2">
                        {{ log.workflow.replace('.github/workflows/', '') }}
                    </td>
                </tr>
                <tr>
                    <td class="border p-2" colspan="2"></td>
                    <td class="border p-2">
                        <span class="text-gray-500">&Sigma; =</span> {{ totalQuantity }}
                    </td>
                    <td class="border p-2">
                        <span class="text-gray-500">&Sigma; =</span> ${{ totalPrice }}
                    </td>
                    <td class="border p-2"></td>
                </tr>
            </tbody>
        </table>
    </div>
</template>