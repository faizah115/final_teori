<template>
    <div class="flex flex-col items-center justify-center p-8 text-center bg-cream-bg/50 rounded-2xl border border-dashed border-cream-border/60">
        <div class="w-20 h-20 mb-4 text-cream-muted bg-white shadow-sm rounded-full flex items-center justify-center text-4xl">
            {{ icon }}
        </div>
        <h3 class="text-lg font-bold text-cream-text mb-1">{{ title }}</h3>
        <p class="text-sm text-cream-muted mb-5 max-w-sm">{{ description }}</p>
        <slot name="action"></slot>
    </div>
</template>

<script setup>
defineProps({
    icon: {
        type: String,
        default: '📄'
    },
    title: {
        type: String,
        default: 'Tidak ada data'
    },
    description: {
        type: String,
        default: 'Belum ada data yang bisa ditampilkan di sini saat ini.'
    }
})
</script>
