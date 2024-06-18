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
</template>