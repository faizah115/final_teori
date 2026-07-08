<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
    reservasiList: {
        type: Array,
        default: () => [],
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
})

import { ref, watch } from 'vue'

const search = ref(props.filters.search || '')
const tanggal = ref(props.filters.tanggal || '')
const status = ref(props.filters.status || '')

// Watch search and tanggal without debounce (status handled via @change)
watch(search, (newSearch) => {
    applyFilters({ search: newSearch })
})
watch(tanggal, (newTanggal) => {
    applyFilters({ tanggal: newTanggal })
})

function applyFilters(overrides = {}) {
    const query = {
        search: search.value,
        tanggal: tanggal.value,
        status: status.value,
        ...overrides,
    }
    // Remove empty parameters
    Object.keys(query).forEach((k) => {
        if (!query[k]) delete query[k]
    })
    router.get(route('admin.reservasi.index'), query, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    })
}

// ─── Formatting Helpers ───────────────────────────────────────────────────
const formatDate = (dateStr) => {
    return new Date(dateStr).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    })
}

const statusBadgeClass = (status) => {
    const map = {
        PENDING:    'bg-yellow-100 text-yellow-800 border-yellow-200',
        KONFIRMASI: 'bg-emerald-100 text-emerald-800 border-emerald-200',
        BATAL:      'bg-red-100 text-red-800 border-red-200',
    }
    return map[status] ?? 'bg-cream-border text-cream-muted'
}

import Swal from 'sweetalert2'
import EmptyState from '@/Components/EmptyState.vue'

// ─── Actions ──────────────────────────────────────────────────────────────
const changeStatus = (id, newStatus) => {
    const actionLabel = newStatus === 'KONFIRMASI' ? 'mengonfirmasi' : 'membatalkan'
    
    Swal.fire({
        title: `${newStatus === 'KONFIRMASI' ? 'Konfirmasi' : 'Batalkan'} Reservasi?`,
        text: `Apakah Anda yakin ingin ${actionLabel} reservasi ini?`,
        icon: newStatus === 'KONFIRMASI' ? 'question' : 'warning',
        showCancelButton: true,
        confirmButtonColor: newStatus === 'KONFIRMASI' ? '#10b981' : '#ef4444',
        cancelButtonColor: '#9ca3af',
        confirmButtonText: `Ya, ${actionLabel}!`,
        cancelButtonText: 'Kembali'
    }).then((result) => {
        if (result.isConfirmed) {
            router.patch(route('admin.reservasi.updateStatus', id), {
                status: newStatus,
            })
        }
    })
}

const deleteReservasi = (id) => {
    Swal.fire({
        title: 'Hapus Reservasi?',
        text: 'Apakah Anda yakin ingin menghapus reservasi ini secara permanen?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#9ca3af',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('admin.reservasi.destroy', id))
        }
    })
}
</script>

<template>
    <AdminLayout title="Kelola Reservasi">
        <div class="space-y-6">
            
            <!-- Header section -->
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <h3 class="text-lg font-bold text-cream-text">Daftar Pemesanan Lapangan</h3>
                    <p class="text-cream-muted text-xs">Kelola semua transaksi pemesanan lapangan di sini.</p>
                </div>
            </div>

            <!-- Filters Section -->
            <div class="card p-4 flex flex-col md:flex-row gap-4">
                <!-- Search -->
                <div class="flex-1">
                    <label for="search" class="block text-xs font-semibold text-cream-muted mb-1">Cari Pelanggan / ID</label>
                    <input
                        id="search"
                        type="text"
                        v-model="search"
                        placeholder="Ketik nama atau ID..."
                        class="w-full bg-cream-bg border-cream-border text-cream-text rounded-lg text-sm focus:ring-emerald-500 focus:border-emerald-500"
                    />
                </div>

                <!-- Filter Tanggal -->
                <div>
                    <label for="tanggal" class="block text-xs font-semibold text-cream-muted mb-1">Filter Tanggal</label>
                    <input
                        id="tanggal"
                        type="date"
                        v-model="tanggal"
                        class="w-full bg-cream-bg border-cream-border text-cream-text rounded-lg text-sm focus:ring-emerald-500 focus:border-emerald-500"
                    />
                </div>

                <!-- Filter Status -->
                <div>
                    <label for="status" class="block text-xs font-semibold text-cream-muted mb-1">Filter Status</label>
                    <select
                        id="status"
                        v-model="status"
                        @change="applyFilters()"
                        class="w-full bg-cream-bg border-cream-border text-cream-text rounded-lg text-sm focus:ring-emerald-500 focus:border-emerald-500"
                    >
                        <option value="">Semua Status</option>
                        <option value="PENDING">PENDING</option>
                        <option value="KONFIRMASI">KONFIRMASI</option>
                        <option value="BATAL">BATAL</option>
                    </select>
                </div>
            </div>

            <!-- Table Card -->
            <div class="card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-left text-sm text-cream-text">
                        <thead class="bg-cream-bg/60 border-b border-cream-border text-xs uppercase font-bold text-cream-muted tracking-wider">
                            <tr>
                                <th scope="col" class="px-6 py-4">ID</th>
                                <th scope="col" class="px-6 py-4">Nama Pelanggan</th>
                                <th scope="col" class="px-6 py-4">Lapangan</th>
                                <th scope="col" class="px-6 py-4">Tanggal Sewa</th>
                                <th scope="col" class="px-6 py-4">Waktu Sewa</th>
                                <th scope="col" class="px-6 py-4">Status</th>
                                <th scope="col" class="px-6 py-4 text-right">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-cream-border/60">
                            <tr
                                v-for="item in reservasiList"
                                :key="item.id"
                                class="hover:bg-cream-card/50 transition-colors"
                            >
                                <!-- ID -->
                                <td class="px-6 py-4 whitespace-nowrap font-mono font-bold text-cream-muted">
                                    #{{ item.id }}
                                </td>

                                <!-- Pelanggan -->
                                <td class="px-6 py-4 whitespace-nowrap font-semibold">
                                    {{ item.user_name }}
                                </td>

                                <!-- Lapangan -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ item.lapangan_nama }}
                                </td>

                                <!-- Tanggal -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ formatDate(item.tanggal) }}
                                </td>

                                <!-- Waktu -->
                                <td class="px-6 py-4 whitespace-nowrap font-mono text-xs">
                                    {{ item.jam_mulai }} - {{ item.jam_selesai }} WIB
                                </td>

                                <!-- Status -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="['px-2.5 py-1 rounded-full text-xs font-semibold border', statusBadgeClass(item.status)]">
                                        {{ item.status }}
                                    </span>
                                </td>

                                <!-- Tindakan -->
                                <td class="px-6 py-4 whitespace-nowrap text-right text-xs font-semibold space-x-2">
                                    <!-- Jika PENDING -->
                                    <template v-if="item.status === 'PENDING'">
                                        <button
                                            @click="changeStatus(item.id, 'KONFIRMASI')"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 transition-colors"
                                        >
                                            ✅ Konfirmasi
                                        </button>
                                        <button
                                            @click="changeStatus(item.id, 'BATAL')"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-red-50 text-red-700 border border-red-200 hover:bg-red-100 transition-colors"
                                        >
                                            ❌ Batalkan
                                        </button>
                                    </template>

                                    <!-- Jika KONFIRMASI -->
                                    <template v-else-if="item.status === 'KONFIRMASI'">
                                        <button
                                            @click="changeStatus(item.id, 'BATAL')"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-red-50 text-red-700 border border-red-200 hover:bg-red-100 transition-colors"
                                        >
                                            ❌ Batalkan
                                        </button>
                                    </template>

                                    <!-- Jika BATAL -->
                                    <template v-else>
                                        <span class="text-cream-muted text-xs font-normal">Selesai</span>
                                    </template>

                                    <!-- Tombol Hapus (selalu tersedia) -->
                                    <button
                                        @click="deleteReservasi(item.id)"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-red-50 text-red-600 border border-red-200 hover:bg-red-100 transition-colors"
                                        title="Hapus Reservasi"
                                    >
                                        🗑️ Hapus
                                    </button>
                                </td>
                            </tr>

                            <tr v-if="reservasiList.length === 0">
                                <td colspan="7" class="px-6 py-12">
                                    <EmptyState
                                        icon="📅"
                                        title="Tidak ada transaksi reservasi"
                                        description="Pemesanan dari pengguna akan muncul di tabel ini. Ubah filter jika perlu."
                                    />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </AdminLayout>
</template>
