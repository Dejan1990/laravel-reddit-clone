<template>
    <div v-if="$page.props.flash.message" class="bg-green-400">
        <Message :msg="$page.props.flash.message" />
    </div>

    <div v-if="$page.props.flash.error_message" class="bg-red-500">
        <Message :msg="$page.props.flash.error_message" />
    </div>
</template>

<script setup>
import Message from '@/Components/Message.vue'
</script>