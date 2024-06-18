<script>
import Navbar from './Navbar.vue'

export default {
    components: {
        Navbar,
    }
}
</script>

<template>
    <Navbar/>
    <main>
        <slot></slot>
    </main>
    <footer>
        <div class="w-full flex items-center justify-center text-white">
            <span class="mr-2 font-semibold text-xs tracking-widest">developed by</span>
            <a href="https://blumilk.pl/" target="_blank">
                <img src="blumilk.png" alt="Blumilk" class="w-32">
            </a>
        </div>
    </footer>
</template>