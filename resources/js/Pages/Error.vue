<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
    status: Number,
})

const title = computed(() => {
    return {
        503: '503: Layanan Tidak Tersedia',
        500: '500: Kesalahan Server',
        404: '404: Halaman Tidak Ditemukan',
        403: '403: Akses Ditolak',
    }[props.status] || 'Terjadi Kesalahan'
})

const description = computed(() => {
    return {
        503: 'Maaf, layanan kami sedang dalam pemeliharaan. Silakan periksa kembali nanti.',
        500: 'Oops, ada yang salah dengan server kami. Kami sedang berusaha memperbaikinya.',
        404: 'Maaf, halaman yang Anda cari tidak dapat ditemukan.',
        403: 'Maaf, Anda tidak memiliki izin untuk mengakses halaman ini.',
    }[props.status] || 'Terjadi kesalahan tidak terduga pada aplikasi.'
})

const emoji = computed(() => {
    return {
        503: '🚧',
        500: '🔥',
        404: '🧐',
        403: '🔒',
    }[props.status] || '⚠️'
})
</script>

<template>
    <div class="min-h-screen bg-cream-bg flex items-center justify-center p-4 font-sans text-cream-text">
        <div class="card p-8 md:p-12 text-center max-w-lg w-full relative overflow-hidden shadow-cream-md border border-cream-border">
            
            <div class="absolute -top-12 -left-12 text-9xl opacity-5 pointer-events-none">{{ emoji }}</div>
            <div class="absolute -bottom-12 -right-12 text-9xl opacity-5 pointer-events-none">{{ emoji }}</div>

            <div class="relative z-10">
                <div class="w-24 h-24 bg-cream-accent/10 rounded-3xl flex items-center justify-center mx-auto mb-6 text-6xl">
                    {{ emoji }}
                </div>
                
                <h1 class="text-3xl font-display font-bold mb-3">{{ title }}</h1>
                <p class="text-cream-muted text-sm mb-8 leading-relaxed">
                    {{ description }}
                </p>
                
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <Link
                        :href="route('home')"
                        class="btn-primary"
                    >
                        🏠 Kembali ke Beranda
                    </Link>
                    <button
                        @click="() => window.history.back()"
                        class="btn-secondary"
                    >
                        ⬅️ Kembali ke Halaman Sebelumnya
                    </button>
                </div>
            </div>
            
        </div>
    </div>
</template>
