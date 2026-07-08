<script setup>
import { ref, watch, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

// ─── Props dari Inertia ────────────────────────────────────────────────────
const props = defineProps({
    lapangan: {
        type: Array,
        default: () => [],
    },
    jenisOptions: {
        type: Array,
        default: () => [],
    },
    filters: {
        type: Object,
        default: () => ({ search: '', jenis: '' }),
    },
})

// ─── Search & Filter State ─────────────────────────────────────────────────
const search = ref(props.filters.search ?? '')
const selectedJenis = ref(props.filters.jenis ?? '')
let debounceTimer = null

const doSearch = () => {
    clearTimeout(debounceTimer)
    debounceTimer = setTimeout(() => {
        router.get(
            route('lapangan.index'),
            {
                search: search.value || undefined,
                jenis: selectedJenis.value || undefined,
            },
            {
                preserveState: true,
                replace: true,
            }
        )
    }, 350)
}

watch(search, doSearch)
watch(selectedJenis, doSearch)

const clearFilters = () => {
    search.value = ''
    selectedJenis.value = ''
}

const hasFilter = computed(() => search.value || selectedJenis.value)

// ─── Helpers ───────────────────────────────────────────────────────────────
const formatRupiah = (amount) =>
    new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(amount)

const jenisColor = (jenis) => {
    const map = {
        VIP:     'bg-amber-100 text-amber-700 border-amber-200',
        Premium: 'bg-purple-100 text-purple-700 border-purple-200',
        Standar: 'bg-blue-100 text-blue-700 border-blue-200',
        Outdoor: 'bg-emerald-100 text-emerald-700 border-emerald-200',
    }
    return map[jenis] ?? 'bg-cream-border/60 text-cream-muted border-cream-border'
}
</script>

<template>
    <AppLayout title="Daftar Lapangan">

        <!-- ─── Page Header ──────────────────────────────────────────────── -->
        <div class="bg-cream-warm-gradient border-b border-cream-border">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
                <!-- Breadcrumb -->
                <nav class="flex items-center gap-2 text-sm text-cream-muted mb-4">
                    <Link :href="route('home')" class="hover:text-cream-accent transition-colors">Beranda</Link>
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                    </svg>
                    <span class="text-cream-text font-medium">Lapangan</span>
                </nav>

                <div class="flex flex-col sm:flex-row sm:items-end gap-4 justify-between">
                    <div>
                        <h1 class="font-display text-2xl sm:text-3xl font-bold text-cream-text">
                            Daftar Lapangan 🏸
                        </h1>
                        <p class="text-cream-muted text-sm mt-1">
                            {{ lapangan.length }} lapangan ditemukan
                            <template v-if="hasFilter"> untuk pencarian "<strong class="text-cream-text">{{ search || selectedJenis }}</strong>"</template>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- ─── Filter Bar ───────────────────────────────────────────────── -->
        <div class="bg-cream-card border-b border-cream-border sticky top-16 z-30 shadow-cream">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
                <div class="flex flex-col sm:flex-row gap-3">

                    <!-- Search Input -->
                    <div class="relative flex-1">
                        <svg
                            class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-cream-muted pointer-events-none"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                        <input
                            id="search-lapangan"
                            v-model="search"
                            type="search"
                            placeholder="Cari nama lapangan..."
                            class="input pl-10 pr-4 h-10 text-sm"
                        />
                    </div>

                    <!-- Jenis Filter -->
                    <select
                        id="filter-jenis"
                        v-model="selectedJenis"
                        class="input h-10 text-sm sm:w-44 cursor-pointer"
                    >
                        <option value="">Semua Jenis</option>
                        <option v-for="j in jenisOptions" :key="j" :value="j">{{ j }}</option>
                    </select>

                    <!-- Clear -->
                    <button
                        v-if="hasFilter"
                        @click="clearFilters"
                        class="flex items-center gap-1.5 px-4 h-10 rounded-xl text-sm font-medium text-red-600 border border-red-200 hover:bg-red-50 transition-colors whitespace-nowrap"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Hapus Filter
                    </button>
                </div>
            </div>
        </div>

        <!-- ─── Content ─────────────────────────────────────────────────── -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

            <!-- Empty State -->
            <div v-if="lapangan.length === 0" class="card-lg p-16 text-center animate-fade-in">
                <div class="w-20 h-20 rounded-3xl bg-cream-accent/10 flex items-center justify-center mx-auto mb-5">
                    <span class="text-4xl">🔍</span>
                </div>
                <h3 class="font-display font-semibold text-cream-text text-lg mb-1">
                    Lapangan Tidak Ditemukan
                </h3>
                <p class="text-cream-muted text-sm mb-5 max-w-xs mx-auto">
                    Tidak ada lapangan yang cocok dengan pencarian "<strong>{{ search }}</strong>".
                    Coba kata kunci lain.
                </p>
                <button @click="clearFilters" class="btn-primary">
                    Tampilkan Semua Lapangan
                </button>
            </div>

            <!-- Grid -->
            <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                <div
                    v-for="(lap, index) in lapangan"
                    :key="lap.id"
                    class="card group overflow-hidden hover:shadow-cream-lg hover:-translate-y-1 transition-all duration-300 animate-fade-in flex flex-col"
                    :style="`animation-delay: ${index * 0.05}s`"
                >
                    <!-- Image -->
                    <div class="relative h-48 overflow-hidden flex-shrink-0">
                        <img
                            v-if="lap.foto_url"
                            :src="lap.foto_url"
                            :alt="lap.nama"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                            loading="lazy"
                        />
                        <div
                            v-else
                            class="w-full h-full bg-cream-accent-gradient flex items-center justify-center"
                        >
                            <span class="text-5xl opacity-50">🏸</span>
                        </div>

                        <!-- Badges -->
                        <div class="absolute top-3 left-3 flex flex-col gap-1.5">
                            <span :class="['badge text-[11px] font-semibold border shadow-sm', jenisColor(lap.jenis)]">
                                {{ lap.jenis }}
                            </span>
                        </div>
                        <span
                            :class="[
                                'absolute top-3 right-3 badge text-[11px] font-semibold shadow-sm',
                                lap.is_tersedia ? 'bg-emerald-500 text-white' : 'bg-slate-500 text-white'
                            ]"
                        >
                            {{ lap.is_tersedia ? '● Tersedia' : '● Tidak Tersedia' }}
                        </span>
                    </div>

                    <!-- Content -->
                    <div class="p-5 flex flex-col flex-1">
                        <h3 class="font-display font-semibold text-cream-text leading-snug mb-1 group-hover:text-cream-accent transition-colors">
                            {{ lap.nama }}
                        </h3>

                        <!-- Price Row -->
                        <div class="flex items-end justify-between mt-auto pt-4 border-t border-cream-border">
                            <div>
                                <p class="text-[11px] text-cream-muted mb-0.5">Harga per jam</p>
                                <p class="font-display font-bold text-cream-accent text-xl leading-none">
                                    {{ formatRupiah(lap.harga_per_jam) }}
                                </p>
                            </div>

                            <!-- Detail Button -->
                            <Link
                                :href="route('lapangan.show', lap.id)"
                                class="flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold text-white bg-cream-accent hover:bg-cream-accent/90 active:scale-95 transition-all duration-200 shadow-cream"
                                :class="!lap.is_tersedia && 'opacity-75 pointer-events-none'"
                            >
                                Lihat Detail
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                                </svg>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </AppLayout>
</template>
