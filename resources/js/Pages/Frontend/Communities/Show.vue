<template>
    <GuestLayout>
        <h1>Community {{ community.name }} show page</h1>
    </GuestLayout>
</template>

<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue'
import { Link } from '@inertiajs/inertia-vue3'

defineProps({
    community: Object
})
</script>