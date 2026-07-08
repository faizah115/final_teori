<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link } from '@inertiajs/vue3'
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend,
  Filler
} from 'chart.js'
import { Line } from 'vue-chartjs'

ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend,
  Filler
)

const props = defineProps({
    stats: {
        type: Object,
        required: true,
    },
    chartLabels: {
        type: Array,
        required: true,
    },
    chartData: {
        type: Array,
        required: true,
    }
})

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: false,
        }
    },
    scales: {
        y: {
            beginAtZero: true,
            ticks: {
                stepSize: 1
            }
        }
    }
}

const chartConfig = {
    labels: props.chartLabels,
    datasets: [
        {
            label: 'Reservasi',
            backgroundColor: 'rgba(196, 172, 116, 0.2)',
            borderColor: '#C4AC74', // cream-accent
            borderWidth: 2,
            pointBackgroundColor: '#fff',
            pointBorderColor: '#C4AC74',
            fill: true,
            data: props.chartData
        }
    ]
}
</script>

<template>
    <AdminLayout title="Dashboard Admin">
        <div class="space-y-6">
            <!-- Welcome banner -->
            <div class="card p-6 bg-cream-warm-gradient border-cream-border relative overflow-hidden flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="z-10">
                    <h3 class="text-xl sm:text-2xl font-bold text-cream-text">Selamat Datang di Panel Admin 👋</h3>
                    <p class="text-cream-muted text-sm mt-1 max-w-xl">
                        Kelola ketersediaan lapangan bulutangkis, konfirmasi pemesanan dari pelanggan, dan lihat statistik performa reservasi lapangan Anda di sini.
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <Link
                        :href="route('admin.lapangan.index')"
                        class="btn-primary py-2.5 px-4 text-xs shadow-none"
                    >
                        Kelola Lapangan
                    </Link>
                    <Link
                        :href="route('admin.reservasi.index')"
                        class="btn-secondary py-2.5 px-4 text-xs"
                    >
                        Kelola Reservasi
                    </Link>
                </div>
                <div class="absolute -right-8 -bottom-8 text-8xl opacity-10 pointer-events-none select-none">🏸</div>
            </div>

            <!-- Stats Grid - Top Row -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                
                <!-- Total Pengguna -->
                <div class="card p-5 flex items-center justify-between group hover:border-cream-accent transition-all duration-200">
                    <div class="space-y-1">
                        <p class="text-[10px] font-bold text-cream-muted tracking-wide uppercase">Total Pengguna</p>
                        <h4 class="text-3xl font-display font-bold text-cream-text">{{ stats.total_pengguna }}</h4>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-500 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                        👥
                    </div>
                </div>

                <!-- Total Lapangan -->
                <div class="card p-5 flex items-center justify-between group hover:border-cream-accent transition-all duration-200">
                    <div class="space-y-1">
                        <p class="text-[10px] font-bold text-cream-muted tracking-wide uppercase">Total Lapangan</p>
                        <h4 class="text-3xl font-display font-bold text-cream-text">{{ stats.total_lapangan }}</h4>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-cream-accent/10 text-cream-accent flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                        🏸
                    </div>
                </div>

                <!-- Total Reservasi -->
                <div class="card p-5 flex items-center justify-between group hover:border-cream-accent transition-all duration-200">
                    <div class="space-y-1">
                        <p class="text-[10px] font-bold text-cream-muted tracking-wide uppercase">Total Reservasi</p>
                        <h4 class="text-3xl font-display font-bold text-cream-text">{{ stats.total_reservasi }}</h4>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-500 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                        📅
                    </div>
                </div>

                <!-- Reservasi Hari Ini -->
                <div class="card p-5 flex items-center justify-between group hover:border-cream-accent transition-all duration-200">
                    <div class="space-y-1">
                        <p class="text-[10px] font-bold text-cream-muted tracking-wide uppercase">Reservasi Hari Ini</p>
                        <h4 class="text-3xl font-display font-bold text-cream-text">{{ stats.reservasi_hari_ini }}</h4>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-pink-50 text-pink-500 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                        ☀️
                    </div>
                </div>
            </div>

            <!-- Stats Grid - Bottom Row -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                
                <!-- Reservasi Pending -->
                <div class="card p-5 flex items-center justify-between group hover:border-amber-400 transition-all duration-200">
                    <div class="space-y-1">
                        <p class="text-[10px] font-bold text-amber-600/70 tracking-wide uppercase">Status Pending</p>
                        <h4 class="text-3xl font-display font-bold text-amber-600">{{ stats.total_pending }}</h4>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                        ⏳
                    </div>
                </div>

                <!-- Reservasi Dikonfirmasi -->
                <div class="card p-5 flex items-center justify-between group hover:border-emerald-400 transition-all duration-200">
                    <div class="space-y-1">
                        <p class="text-[10px] font-bold text-emerald-600/70 tracking-wide uppercase">Selesai (Konfirmasi)</p>
                        <h4 class="text-3xl font-display font-bold text-emerald-600">{{ stats.total_konfirmasi }}</h4>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                        ✅
                    </div>
                </div>

                <!-- Reservasi Batal -->
                <div class="card p-5 flex items-center justify-between group hover:border-red-400 transition-all duration-200">
                    <div class="space-y-1">
                        <p class="text-[10px] font-bold text-red-600/70 tracking-wide uppercase">Dibatalkan</p>
                        <h4 class="text-3xl font-display font-bold text-red-600">{{ stats.total_batal }}</h4>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-red-100 text-red-600 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                        ❌
                    </div>
                </div>

            </div>

            <!-- Panel Grafik -->
            <div class="card p-6 space-y-4">
                <h3 class="text-base font-bold flex items-center gap-2">
                    <span>📈</span> Tren Reservasi (7 Hari Terakhir)
                </h3>
                <div class="divider"></div>
                <div class="h-64 w-full">
                    <Line :data="chartConfig" :options="chartOptions" />
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
