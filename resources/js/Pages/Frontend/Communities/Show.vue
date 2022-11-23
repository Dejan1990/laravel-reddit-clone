<template>
    <h1>
        {{ community.name }}
    </h1>
</template>

<script setup>
defineProps({
    community: Object
})
</script>