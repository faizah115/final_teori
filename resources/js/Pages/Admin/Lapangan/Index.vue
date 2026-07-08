<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link, router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'

const props = defineProps({
    lapangan: {
        type: Object, // LengthAwarePaginator
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
})

// ─── Search ────────────────────────────────────────────────────────────────
const search = ref(props.filters.search ?? '')

let searchTimeout = null
watch(search, (val) => {
    clearTimeout(searchTimeout)
    searchTimeout = setTimeout(() => {
        router.get(route('admin.lapangan.index'), { search: val }, {
            preserveState: true,
            replace: true,
        })
    }, 400)
})

// ─── Formatting Helpers ───────────────────────────────────────────────────
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

import Swal from 'sweetalert2'
import EmptyState from '@/Components/EmptyState.vue'

// ─── Delete Lapangan ───────────────────────────────────────────────────────
const deleteLapangan = (id, nama) => {
    Swal.fire({
        title: 'Hapus Lapangan?',
        text: `Apakah Anda yakin ingin menghapus lapangan "${nama}"? Semua reservasi terkait juga akan terdampak.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#9ca3af',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('admin.lapangan.destroy', id))
        }
    })
}

// ─── Toggle Status ─────────────────────────────────────────────────────────
const toggleStatus = (id) => {
    router.patch(route('admin.lapangan.toggleStatus', id), {}, {
        preserveScroll: true,
    })
}
</script>

<template>
    <AdminLayout title="Kelola Lapangan">
        <div class="space-y-6">
            
            <!-- Top bar -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h3 class="text-lg font-bold text-cream-text">Daftar Lapangan Bulutangkis</h3>
                    <p class="text-cream-muted text-xs">Total {{ lapangan.total }} lapangan terdaftar di sistem.</p>
                </div>
                <Link
                    :href="route('admin.lapangan.create')"
                    class="btn-primary flex items-center gap-2 self-start sm:self-auto py-2.5 px-5 text-sm"
                >
                    <span>➕</span> Tambah Lapangan
                </Link>
            </div>

            <!-- Search Bar -->
            <div class="card p-4">
                <div class="relative max-w-sm">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-cream-muted">🔍</span>
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Cari nama atau jenis lapangan..."
                        class="input pl-10 text-sm"
                    />
                </div>
            </div>

            <!-- Table Container -->
            <div class="card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-left text-sm text-cream-text">
                        <thead class="bg-cream-bg/60 border-b border-cream-border text-xs uppercase font-bold text-cream-muted tracking-wider">
                            <tr>
                                <th scope="col" class="px-6 py-4">Foto</th>
                                <th scope="col" class="px-6 py-4">Nama Lapangan</th>
                                <th scope="col" class="px-6 py-4">Jenis</th>
                                <th scope="col" class="px-6 py-4">Harga / Jam</th>
                                <th scope="col" class="px-6 py-4">Status</th>
                                <th scope="col" class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-cream-border/60">
                            <tr
                                v-for="item in lapangan.data"
                                :key="item.id"
                                class="hover:bg-cream-card/50 transition-colors"
                            >
                                <!-- Foto -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="w-16 h-12 rounded-xl overflow-hidden bg-cream-bg border border-cream-border flex items-center justify-center shadow-cream">
                                        <img
                                            v-if="item.foto_url"
                                            :src="item.foto_url"
                                            :alt="item.nama"
                                            class="w-full h-full object-cover"
                                        />
                                        <span v-else class="text-xl">🏸</span>
                                    </div>
                                </td>

                                <!-- Nama -->
                                <td class="px-6 py-4 whitespace-nowrap font-semibold">
                                    {{ item.nama }}
                                </td>

                                <!-- Jenis -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="['px-2.5 py-1 rounded-lg text-xs font-semibold border', jenisColor(item.jenis)]">
                                        {{ item.jenis }}
                                    </span>
                                </td>

                                <!-- Harga -->
                                <td class="px-6 py-4 whitespace-nowrap font-mono font-semibold text-cream-accent">
                                    {{ formatRupiah(item.harga_per_jam) }}
                                </td>

                                <!-- Status Toggle -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <button
                                        @click="toggleStatus(item.id)"
                                        :title="item.is_tersedia ? 'Klik untuk menonaktifkan' : 'Klik untuk mengaktifkan'"
                                        class="flex items-center gap-2 group"
                                    >
                                        <!-- Toggle Switch -->
                                        <div
                                            :class="[
                                                'relative inline-flex h-5 w-9 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out',
                                                item.is_tersedia ? 'bg-emerald-500' : 'bg-gray-300'
                                            ]"
                                        >
                                            <span
                                                :class="[
                                                    'pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                                                    item.is_tersedia ? 'translate-x-4' : 'translate-x-0'
                                                ]"
                                            />
                                        </div>
                                        <span
                                            :class="['text-xs font-semibold', item.is_tersedia ? 'text-emerald-600' : 'text-red-500']"
                                        >
                                            {{ item.is_tersedia ? 'Tersedia' : 'Tutup' }}
                                        </span>
                                    </button>
                                </td>

                                <!-- Aksi -->
                                <td class="px-6 py-4 whitespace-nowrap text-right text-xs font-medium space-x-2">
                                    <Link
                                        :href="route('admin.lapangan.edit', item.id)"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-cream-border text-cream-text bg-cream-card hover:border-cream-accent hover:text-cream-accent transition-colors"
                                    >
                                        ✏️ Edit
                                    </Link>
                                    <button
                                        @click="deleteLapangan(item.id, item.nama)"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-red-200 text-red-600 bg-red-50 hover:bg-red-100 transition-colors"
                                    >
                                        🗑️ Hapus
                                    </button>
                                </td>
                            </tr>

                            <!-- Empty State -->
                            <tr v-if="lapangan.data.length === 0">
                                <td colspan="6" class="px-6 py-12">
                                    <EmptyState
                                        icon="🏸"
                                        title="Tidak ada lapangan ditemukan"
                                        :description="search ? `Tidak ada hasil untuk pencarian \x22${search}\x22.` : 'Silakan klik tombol \x22Tambah Lapangan\x22 untuk membuat lapangan baru.'"
                                    />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="lapangan.last_page > 1" class="px-6 py-4 border-t border-cream-border flex items-center justify-between gap-2 text-sm">
                    <p class="text-cream-muted text-xs">
                        Menampilkan {{ lapangan.from }}–{{ lapangan.to }} dari {{ lapangan.total }} lapangan
                    </p>
                    <div class="flex items-center gap-1">
                        <Link
                            v-if="lapangan.prev_page_url"
                            :href="lapangan.prev_page_url"
                            class="px-3 py-1.5 rounded-lg border border-cream-border text-cream-text hover:border-cream-accent hover:text-cream-accent transition-colors text-xs font-semibold"
                        >
                            ← Sebelumnya
                        </Link>
                        <template v-for="link in lapangan.links" :key="link.label">
                            <Link
                                v-if="link.url && !link.label.includes('Previous') && !link.label.includes('Next')"
                                :href="link.url"
                                :class="[
                                    'px-3 py-1.5 rounded-lg border text-xs font-semibold transition-colors',
                                    link.active
                                        ? 'bg-cream-accent text-white border-cream-accent'
                                        : 'border-cream-border text-cream-text hover:border-cream-accent hover:text-cream-accent'
                                ]"
                            >
                                {{ link.label }}
                            </Link>
                        </template>
                        <Link
                            v-if="lapangan.next_page_url"
                            :href="lapangan.next_page_url"
                            class="px-3 py-1.5 rounded-lg border border-cream-border text-cream-text hover:border-cream-accent hover:text-cream-accent transition-colors text-xs font-semibold"
                        >
                            Berikutnya →
                        </Link>
                    </div>
                </div>
            </div>

        </div>
    </AdminLayout>
</template>
