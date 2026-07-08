<script setup>
import { ref, computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import EmptyState from '@/Components/EmptyState.vue'

// ─── Props dari Inertia ────────────────────────────────────────────────────
const props = defineProps({
    reservasiAktif: {
        type: Array,
        default: () => [],
    },
    riwayatReservasi: {
        type: Array,
        default: () => [],
    },
})

const page = usePage()
const user = computed(() => page.props.auth?.user ?? null)

const activeTab = ref('aktif') // 'aktif' atau 'riwayat'

// ─── Formatting Helpers ───────────────────────────────────────────────────
const formatRupiah = (amount) =>
    new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(amount)

const formatDate = (dateStr) => {
    return new Date(dateStr).toLocaleDateString('id-ID', {
        weekday: 'short',
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    })
}

const statusConfig = (status) => {
    const map = {
        PENDING: {
            class: 'bg-yellow-100 text-yellow-800 border-yellow-200',
            icon: '⏳',
            label: 'Menunggu Konfirmasi',
        },
        KONFIRMASI: {
            class: 'bg-emerald-100 text-emerald-800 border-emerald-200',
            icon: '✅',
            label: 'Dikonfirmasi',
        },
        BATAL: {
            class: 'bg-red-100 text-red-800 border-red-200',
            icon: '❌',
            label: 'Dibatalkan',
        },
    }
    return map[status] ?? { class: 'bg-cream-border text-cream-muted', icon: '❓', label: status }
}

// ─── Stats ─────────────────────────────────────────────────────────────────
const stats = computed(() => {
    const all = [...props.reservasiAktif, ...props.riwayatReservasi]
    const total = all.length
    const pending = all.filter(r => r.status === 'PENDING').length
    const konfirmasi = all.filter(r => r.status === 'KONFIRMASI').length
    const batal = all.filter(r => r.status === 'BATAL').length
    return { total, pending, konfirmasi, batal }
})
</script>

<template>
    <AppLayout title="Dashboard User">

        <!-- ─── Page Header ──────────────────────────────────────────────── -->
        <div class="bg-cream-warm-gradient border-b border-cream-border">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
                <!-- Breadcrumb -->
                <nav class="flex items-center gap-2 text-sm text-cream-muted mb-4">
                    <Link :href="route('home')" class="hover:text-cream-accent transition-colors">Beranda</Link>
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                    </svg>
                    <span class="text-cream-text font-medium">Dashboard Saya</span>
                </nav>

                <div class="flex flex-col sm:flex-row sm:items-end gap-4 justify-between">
                    <div>
                        <h1 class="font-display text-2xl sm:text-3xl font-bold text-cream-text">
                            Dashboard Saya 👤
                        </h1>
                        <p class="text-cream-muted text-sm mt-1">
                            Hai {{ user?.name ?? 'Pengguna' }}, kelola reservasi lapangan dan profil kamu di sini.
                        </p>
                    </div>
                    <div class="flex gap-3">
                        <Link
                            :href="route('profile.edit')"
                            class="btn-secondary self-start sm:self-auto py-2.5 px-5 text-sm"
                        >
                            <span>⚙️</span> Edit Profil
                        </Link>
                        <Link
                            :href="route('lapangan.index')"
                            class="btn-primary self-start sm:self-auto py-2.5 px-5 text-sm"
                        >
                            <span>🏸</span> Pesan Lapangan
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <!-- ─── Content ─────────────────────────────────────────────────── -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

            <!-- Stats Cards -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8 animate-fade-in">
                <div class="card p-4 flex items-center gap-3 group hover:border-cream-accent transition-all duration-200">
                    <div class="w-10 h-10 rounded-xl bg-cream-accent/10 text-cream-accent flex items-center justify-center text-lg group-hover:scale-110 transition-transform">
                        📋
                    </div>
                    <div>
                        <p class="text-2xl font-display font-bold text-cream-text">{{ stats.total }}</p>
                        <p class="text-[11px] font-bold text-cream-muted uppercase tracking-wider">Total Reservasi</p>
                    </div>
                </div>
                <div class="card p-4 flex items-center gap-3 group hover:border-yellow-400 transition-all duration-200">
                    <div class="w-10 h-10 rounded-xl bg-yellow-100 text-yellow-600 flex items-center justify-center text-lg group-hover:scale-110 transition-transform">
                        ⏳
                    </div>
                    <div>
                        <p class="text-2xl font-display font-bold text-yellow-600">{{ stats.pending }}</p>
                        <p class="text-[11px] font-bold text-cream-muted uppercase tracking-wider">Pending</p>
                    </div>
                </div>
                <div class="card p-4 flex items-center gap-3 group hover:border-emerald-400 transition-all duration-200">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-lg group-hover:scale-110 transition-transform">
                        ✅
                    </div>
                    <div>
                        <p class="text-2xl font-display font-bold text-emerald-600">{{ stats.konfirmasi }}</p>
                        <p class="text-[11px] font-bold text-cream-muted uppercase tracking-wider">Konfirmasi</p>
                    </div>
                </div>
                <div class="card p-4 flex items-center gap-3 group hover:border-red-400 transition-all duration-200">
                    <div class="w-10 h-10 rounded-xl bg-red-100 text-red-600 flex items-center justify-center text-lg group-hover:scale-110 transition-transform">
                        ❌
                    </div>
                    <div>
                        <p class="text-2xl font-display font-bold text-red-600">{{ stats.batal }}</p>
                        <p class="text-[11px] font-bold text-cream-muted uppercase tracking-wider">Batal</p>
                    </div>
                </div>
            </div>

            <!-- Tab Navigation -->
            <div class="flex border-b border-cream-border mb-6 space-x-6">
                <button
                    @click="activeTab = 'aktif'"
                    :class="[
                        'pb-3 font-semibold text-sm transition-all duration-200 border-b-2',
                        activeTab === 'aktif' ? 'border-cream-accent text-cream-accent' : 'border-transparent text-cream-muted hover:text-cream-text'
                    ]"
                >
                    Reservasi Aktif ({{ reservasiAktif.length }})
                </button>
                <button
                    @click="activeTab = 'riwayat'"
                    :class="[
                        'pb-3 font-semibold text-sm transition-all duration-200 border-b-2',
                        activeTab === 'riwayat' ? 'border-cream-accent text-cream-accent' : 'border-transparent text-cream-muted hover:text-cream-text'
                    ]"
                >
                    Riwayat Selesai ({{ riwayatReservasi.length }})
                </button>
            </div>

            <!-- Tab Content -->
            <div class="space-y-4">
                
                <!-- Display Array based on Tab -->
                <template v-for="item in (activeTab === 'aktif' ? reservasiAktif : riwayatReservasi)" :key="item.id">
                    <div class="card overflow-hidden hover:shadow-cream-md transition-all duration-300 animate-fade-in">
                        <div class="flex flex-col sm:flex-row">
                            <!-- Left: Lapangan Info with Image -->
                            <div class="sm:w-48 h-32 sm:h-auto flex-shrink-0 relative overflow-hidden">
                                <img
                                    v-if="item.lapangan_foto_url"
                                    :src="item.lapangan_foto_url"
                                    :alt="item.lapangan_nama"
                                    class="w-full h-full object-cover"
                                />
                                <div
                                    v-else
                                    class="w-full h-full bg-cream-accent-gradient flex items-center justify-center"
                                >
                                    <span class="text-3xl opacity-50">🏸</span>
                                </div>
                                <!-- Status Badge on Image -->
                                <span
                                    :class="['absolute top-3 left-3 badge text-[11px] font-semibold shadow-sm border', statusConfig(item.status).class]"
                                >
                                    {{ statusConfig(item.status).icon }} {{ statusConfig(item.status).label }}
                                </span>
                            </div>

                            <!-- Right: Details -->
                            <div class="flex-1 p-5">
                                <div class="flex items-start justify-between mb-3">
                                    <div>
                                        <h3 class="font-display font-semibold text-cream-text text-base leading-snug">
                                            {{ item.lapangan_nama }}
                                        </h3>
                                        <p class="text-xs text-cream-muted mt-0.5">
                                            Reservasi #{{ item.id }} • Dibuat pada {{ formatDate(item.created_at) }}
                                        </p>
                                    </div>
                                    <span
                                        :class="['hidden sm:inline-flex badge text-[11px] font-semibold border', statusConfig(item.status).class]"
                                    >
                                        {{ statusConfig(item.status).icon }} {{ item.status }}
                                    </span>
                                </div>

                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                    <div class="bg-cream-bg rounded-xl p-3 border border-cream-border">
                                        <p class="text-[10px] text-cream-muted font-bold uppercase tracking-wider mb-0.5">📅 Jadwal Main</p>
                                        <p class="text-sm font-semibold text-cream-text">{{ formatDate(item.tanggal) }}</p>
                                    </div>
                                    <div class="bg-cream-bg rounded-xl p-3 border border-cream-border">
                                        <p class="text-[10px] text-cream-muted font-bold uppercase tracking-wider mb-0.5">🕐 Waktu</p>
                                        <p class="text-sm font-semibold text-cream-text font-mono">
                                            {{ item.jam_mulai?.slice(0, 5) }} - {{ item.jam_selesai?.slice(0, 5) }}
                                        </p>
                                    </div>
                                    <div class="bg-cream-bg rounded-xl p-3 border border-cream-border col-span-2 sm:col-span-1">
                                        <p class="text-[10px] text-cream-muted font-bold uppercase tracking-wider mb-0.5">💰 Harga / Jam</p>
                                        <p class="text-sm font-display font-bold text-cream-accent">
                                            {{ item.harga_per_jam ? formatRupiah(item.harga_per_jam) : '-' }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Info tip based on status -->
                                <div v-if="item.status === 'PENDING' && activeTab === 'aktif'" class="mt-3 flex items-start gap-2 p-2.5 rounded-lg bg-yellow-50 border border-yellow-200">
                                    <span class="text-xs flex-shrink-0">ℹ️</span>
                                    <p class="text-[11px] text-yellow-800 leading-relaxed">
                                        Reservasi menunggu konfirmasi dari admin. Mohon ditunggu.
                                    </p>
                                </div>
                                <div v-else-if="item.status === 'KONFIRMASI' && activeTab === 'aktif'" class="mt-3 flex items-start gap-2 p-2.5 rounded-lg bg-emerald-50 border border-emerald-200">
                                    <span class="text-xs flex-shrink-0">🎉</span>
                                    <p class="text-[11px] text-emerald-800 leading-relaxed">
                                        Reservasi telah dikonfirmasi. Selamat bermain!
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Empty State -->
                <EmptyState
                    v-if="(activeTab === 'aktif' && reservasiAktif.length === 0) || (activeTab === 'riwayat' && riwayatReservasi.length === 0)"
                    icon="📭"
                    :title="'Belum Ada Data'"
                    :description="activeTab === 'aktif' ? 'Kamu tidak memiliki reservasi aktif saat ini.' : 'Kamu belum memiliki riwayat reservasi terdahulu.'"
                    class="animate-fade-in"
                >
                    <template #action v-if="activeTab === 'aktif'">
                        <Link :href="route('lapangan.index')" class="btn-primary mt-2">
                            🏸 Pesan Lapangan Sekarang
                        </Link>
                    </template>
                </EmptyState>
            </div>

        </div>

    </AppLayout>
</template>
