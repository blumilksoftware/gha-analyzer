<script >
import { Head } from '@inertiajs/vue3'
import { hello } from './Hello.vue';

export default {
  data() {
    return {
      hello
    };
  }
};

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
    <h1>{{hello}}</h1>
    <div class="container my-4 mx-auto bg-white rounded-xl p-4 shadow flex">
        <div class="flex-1 p-4">
            <textarea class="w-full text-xs font-mono shadow border rounded-xl p-3" :value="logs" rows="20"></textarea>
        </div>
    </div>
</template>