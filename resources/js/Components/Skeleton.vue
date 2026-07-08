<template>
    <div :class="['animate-pulse bg-slate-200 dark:bg-slate-700 rounded-lg', customClass]"></div>
</template>

<script setup>
defineProps({
    customClass: {
        type: String,
        default: 'h-4 w-full'
    }
})
</script>
