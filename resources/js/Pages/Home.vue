<script setup>
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

// ─── Props dari Inertia ────────────────────────────────────────────────────
const props = defineProps({
    lapangan: {
        type: Array,
        default: () => [],
    },
    totalLapangan: {
        type: Number,
        default: 0,
    },
    totalTersedia: {
        type: Number,
        default: 0,
    },
})

const page = usePage()
const user = computed(() => page.props.auth?.user ?? null)

// ─── Helpers ───────────────────────────────────────────────────────────────
const formatRupiah = (amount) =>
    new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(amount)

const jenisColor = (jenis) => {
    const map = {
        VIP: 'bg-amber-100 text-amber-700',
        Premium: 'bg-purple-100 text-purple-700',
        Standar: 'bg-blue-100 text-blue-700',
        Outdoor: 'bg-emerald-100 text-emerald-700',
    }
    return map[jenis] ?? 'bg-cream-border text-cream-muted'
}

const stats = computed(() => [
    { label: 'Total Lapangan', value: props.totalLapangan, icon: '🏟️' },
    { label: 'Tersedia Sekarang', value: props.totalTersedia, icon: '✅' },
    { label: 'Buka Setiap Hari', value: '06.00 – 22.00', icon: '🕐' },
])
</script>

<template>
    <AppLayout title="Beranda">

        <!-- ─── Hero Section ─────────────────────────────────────────────── -->
        <section class="relative overflow-hidden bg-cream-warm-gradient">
            <!-- Decorative circles -->
            <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full bg-cream-accent/10 blur-3xl pointer-events-none" />
            <div class="absolute -bottom-16 -left-16 w-72 h-72 rounded-full bg-cream-accent/8 blur-2xl pointer-events-none" />

            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-28">
                <div class="max-w-3xl">
                    <!-- Badge -->
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-cream-accent/15 border border-cream-accent/30 mb-6 animate-fade-in">
                        <span class="text-sm">🏸</span>
                        <span class="text-xs font-semibold text-cream-accent tracking-wide uppercase">
                            Selamat Datang
                        </span>
                    </div>

                    <!-- Heading -->
                    <h1 class="font-display text-4xl sm:text-5xl lg:text-6xl font-bold text-cream-text leading-tight mb-4 animate-fade-in" style="animation-delay: 0.1s">
                        Reservasi Lapangan
                        <span class="text-cream-accent block sm:inline"> Bulutangkis</span>
                        <span class="text-cream-text block">Online</span>
                    </h1>

                    <!-- Sub-heading -->
                    <p class="text-cream-muted text-lg leading-relaxed mb-8 max-w-xl animate-fade-in" style="animation-delay: 0.2s">
                        Pesan lapangan bulutangkis favoritmu dengan mudah, cepat, dan transparan.
                        Pilih jadwal, pilih lapangan — beres!
                    </p>

                    <!-- CTA Buttons -->
                    <div class="flex flex-col sm:flex-row gap-3 animate-fade-in" style="animation-delay: 0.3s">
                        <Link
                            :href="route('lapangan.index')"
                            class="btn-primary text-base px-8 py-3.5 rounded-2xl shadow-cream-md"
                        >
                            <span>🏟️</span>
                            Lihat Semua Lapangan
                        </Link>
                        <template v-if="!user">
                            <Link
                                :href="route('register')"
                                class="btn-secondary text-base px-8 py-3.5 rounded-2xl"
                            >
                                Daftar Gratis
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                                </svg>
                            </Link>
                        </template>
                    </div>

                    <!-- Stats -->
                    <div class="flex flex-wrap gap-4 mt-12 animate-fade-in" style="animation-delay: 0.4s">
                        <div
                            v-for="stat in stats"
                            :key="stat.label"
                            class="flex items-center gap-2.5 px-4 py-2.5 rounded-xl bg-cream-card border border-cream-border shadow-cream"
                        >
                            <span class="text-lg">{{ stat.icon }}</span>
                            <div>
                                <p class="font-display font-bold text-cream-text text-sm leading-none">{{ stat.value }}</p>
                                <p class="text-cream-muted text-[11px] leading-none mt-0.5">{{ stat.label }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ─── Features Section ─────────────────────────────────────────── -->
        <section class="py-14 bg-cream-bg">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <div
                        v-for="(feat, i) in [
                            { icon: '⚡', title: 'Cepat & Mudah', desc: 'Reservasi selesai dalam hitungan menit. Pilih jadwal, klik pesan, selesai.' },
                            { icon: '🔒', title: 'Terpercaya', desc: 'Jadwal real-time, tidak ada double booking. Transaksi aman dan terverifikasi.' },
                            { icon: '📱', title: 'Fleksibel', desc: 'Akses dari perangkat apa pun, kapan pun. Ubah atau batalkan reservasi dengan mudah.' },
                        ]"
                        :key="i"
                        class="card p-6 hover:shadow-cream-md hover:border-cream-accent/40 transition-all duration-300 group"
                    >
                        <div class="w-11 h-11 rounded-2xl bg-cream-accent/15 flex items-center justify-center mb-4 group-hover:bg-cream-accent/25 transition-colors">
                            <span class="text-xl">{{ feat.icon }}</span>
                        </div>
                        <h3 class="font-display font-semibold text-cream-text mb-1.5">{{ feat.title }}</h3>
                        <p class="text-cream-muted text-sm leading-relaxed">{{ feat.desc }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ─── Lapangan Tersedia Section ────────────────────────────────── -->
        <section class="py-14 bg-cream-warm-gradient">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                <!-- Section Header -->
                <div class="flex items-end justify-between mb-8">
                    <div>
                        <p class="text-cream-accent text-sm font-semibold tracking-wide uppercase mb-1">Tersedia Sekarang</p>
                        <h2 class="font-display text-2xl sm:text-3xl font-bold text-cream-text">Lapangan Pilihan</h2>
                    </div>
                    <Link
                        :href="route('lapangan.index')"
                        class="hidden sm:flex items-center gap-1.5 text-sm font-medium text-cream-accent hover:text-cream-text transition-colors"
                    >
                        Lihat Semua
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                        </svg>
                    </Link>
                </div>

                <!-- Empty State -->
                <div v-if="lapangan.length === 0" class="card p-12 text-center">
                    <span class="text-5xl block mb-4">🏸</span>
                    <h3 class="font-display font-semibold text-cream-text mb-1">Belum Ada Lapangan</h3>
                    <p class="text-cream-muted text-sm">Belum ada lapangan yang tersedia saat ini.</p>
                </div>

                <!-- Lapangan Grid -->
                <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    <Link
                        v-for="lap in lapangan"
                        :key="lap.id"
                        :href="route('lapangan.show', lap.id)"
                        class="card group overflow-hidden hover:shadow-cream-lg hover:-translate-y-1 transition-all duration-300 block"
                    >
                        <!-- Image -->
                        <div class="relative h-44 overflow-hidden">
                            <img
                                v-if="lap.foto_url"
                                :src="lap.foto_url"
                                :alt="lap.nama"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                            />
                            <div
                                v-else
                                class="w-full h-full bg-cream-accent-gradient flex items-center justify-center"
                            >
                                <span class="text-4xl opacity-60">🏸</span>
                            </div>
                            <!-- Jenis Badge -->
                            <span
                                :class="['absolute top-3 left-3 badge text-[11px] font-semibold shadow-sm', jenisColor(lap.jenis)]"
                            >
                                {{ lap.jenis }}
                            </span>
                            <!-- Availability Badge -->
                            <span
                                :class="[
                                    'absolute top-3 right-3 badge shadow-sm text-[11px] font-semibold',
                                    lap.is_tersedia ? 'bg-emerald-500 text-white' : 'bg-gray-500 text-white'
                                ]"
                            >
                                {{ lap.is_tersedia ? '● Tersedia' : '● Penuh' }}
                            </span>
                        </div>

                        <!-- Info -->
                        <div class="p-4">
                            <h3 class="font-display font-semibold text-cream-text text-sm leading-snug mb-1 group-hover:text-cream-accent transition-colors">
                                {{ lap.nama }}
                            </h3>
                            <div class="flex items-center justify-between mt-3">
                                <div>
                                    <p class="text-[11px] text-cream-muted">Harga / jam</p>
                                    <p class="font-display font-bold text-cream-accent text-base leading-tight">
                                        {{ formatRupiah(lap.harga_per_jam) }}
                                    </p>
                                </div>
                                <span class="flex items-center gap-1 text-xs font-medium text-cream-accent bg-cream-accent/10 px-2.5 py-1.5 rounded-lg group-hover:bg-cream-accent group-hover:text-white transition-all duration-200">
                                    Lihat Detail
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </Link>
                </div>

                <!-- Mobile: Lihat Semua button -->
                <div class="sm:hidden text-center mt-6">
                    <Link :href="route('lapangan.index')" class="btn-secondary px-8">
                        Lihat Semua Lapangan
                    </Link>
                </div>
            </div>
        </section>

        <!-- ─── CTA Section ──────────────────────────────────────────────── -->
        <section v-if="!user" class="py-16 bg-cream-bg">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <div class="card-lg p-10 border-cream-accent/20 relative overflow-hidden">
                    <div class="absolute inset-0 bg-cream-accent-gradient opacity-5 rounded-3xl" />
                    <span class="text-4xl block mb-4">🏸</span>
                    <h2 class="font-display text-2xl font-bold text-cream-text mb-2">
                        Siap Bermain Badminton?
                    </h2>
                    <p class="text-cream-muted text-sm mb-6 max-w-md mx-auto">
                        Daftar sekarang dan nikmati kemudahan reservasi lapangan bulutangkis kapan saja dan di mana saja.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-3 justify-center">
                        <Link :href="route('register')" class="btn-primary px-8 py-3 text-base rounded-2xl">
                            Daftar Sekarang — Gratis!
                        </Link>
                        <Link :href="route('login')" class="btn-secondary px-8 py-3 text-base rounded-2xl">
                            Sudah punya akun? Masuk
                        </Link>
                    </div>
                </div>
            </div>
        </section>

    </AppLayout>
</template>
