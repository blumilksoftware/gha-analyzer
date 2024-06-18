<script setup>
import {Link} from '@inertiajs/vue3'
</script>

<template>
    <div class="container mt-12 mx-auto">
        <div class="block uppercase text-white font-semibold text-xs tracking-widest">
            <nav class="flex items-center space-x-4" aria-label="Tabs">
                <span class="pr-6 py-2 font-medium text-sm rounded-md">
                    <img src="./icon.png" class="w-12">
                </span>
                <Link href="/"><span class="cursor-pointer px-8 py-4 text-xs rounded-md bg-opacity-50 bg-blue-500" href="/table">Raw Log</span></Link>
                <div class="flex space-x-4">
                    <Link href="/table"><span class="cursor-pointer px-8 py-4 text-xs rounded-md bg-opacity-50 bg-blue-500" href="/table">Table</span></Link>
                    <Link href="/repositories"><span class="cursor-pointer px-8 py-4 text-xs rounded-md bg-opacity-50 bg-blue-500" href="/table">Repositories</span></Link>
                    <Link href="/authors"><span class="cursor-pointer px-8 py-4 text-xs rounded-md bg-opacity-50 bg-blue-500" href="/table">Authors</span></Link>
                </div>
            </nav>
        </div>
    </div>
</template>
