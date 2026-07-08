<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link, router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import { watchDebounced } from '@vueuse/core'

const props = defineProps({
    users: {
        type: Object, // LengthAwarePaginator
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
})

// ─── Search & Filters ──────────────────────────────────────────────────────
const search = ref(props.filters.search ?? '')
const role = ref(props.filters.role ?? '')

watchDebounced(
    [search, role],
    ([newSearch, newRole]) => {
        const query = {}
        if (newSearch) query.search = newSearch
        if (newRole) query.role = newRole

        router.get(route('admin.users.index'), query, {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        })
    },
    { debounce: 500, maxWait: 1000 }
)

// ─── Formatting Helpers ───────────────────────────────────────────────────
const formatDate = (dateStr) => {
    return new Date(dateStr).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}

const roleBadgeClass = (role) => {
    return role === 'ADMIN' 
        ? 'bg-purple-100 text-purple-800 border-purple-200' 
        : 'bg-blue-100 text-blue-800 border-blue-200'
}

import Swal from 'sweetalert2'
import EmptyState from '@/Components/EmptyState.vue'

// ─── Actions ──────────────────────────────────────────────────────────────
const deleteUser = (id, name) => {
    Swal.fire({
        title: 'Hapus User?',
        text: `Apakah Anda yakin ingin menghapus user "${name}"? Tindakan ini tidak dapat dibatalkan.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#9ca3af',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('admin.users.destroy', id), {
                preserveScroll: true,
            })
        }
    })
}
</script>

<template>
    <AdminLayout title="Kelola User">
        <div class="space-y-6">
            
            <!-- Top bar -->
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <h3 class="text-lg font-bold text-cream-text">Daftar Pengguna</h3>
                    <p class="text-cream-muted text-xs">Kelola akun Admin dan User di dalam sistem.</p>
                </div>
                <Link
                    :href="route('admin.users.create')"
                    class="btn-primary flex items-center gap-2 self-start md:self-auto py-2.5 px-5 text-sm"
                >
                    <span>➕</span> Tambah User
                </Link>
            </div>

            <!-- Filters Section -->
            <div class="card p-4 flex flex-col md:flex-row gap-4">
                <!-- Search -->
                <div class="flex-1">
                    <label for="search" class="block text-xs font-semibold text-cream-muted mb-1">Cari User</label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-cream-muted">🔍</span>
                        <input
                            id="search"
                            type="text"
                            v-model="search"
                            placeholder="Ketik nama atau email..."
                            class="w-full pl-10 pr-4 py-2 bg-cream-bg border border-cream-border text-cream-text rounded-lg text-sm focus:ring-cream-accent focus:border-cream-accent transition-all duration-200"
                        />
                    </div>
                </div>

                <!-- Filter Role -->
                <div class="md:w-64">
                    <label for="role" class="block text-xs font-semibold text-cream-muted mb-1">Filter Role</label>
                    <select
                        id="role"
                        v-model="role"
                        class="w-full bg-cream-bg border border-cream-border text-cream-text rounded-lg text-sm py-2 focus:ring-cream-accent focus:border-cream-accent"
                    >
                        <option value="">Semua Role</option>
                        <option value="ADMIN">Admin</option>
                        <option value="USER">User Biasa</option>
                    </select>
                </div>
            </div>

            <!-- Table Card -->
            <div class="card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-left text-sm text-cream-text">
                        <thead class="bg-cream-bg/60 border-b border-cream-border text-xs uppercase font-bold text-cream-muted tracking-wider">
                            <tr>
                                <th scope="col" class="px-6 py-4">Pengguna</th>
                                <th scope="col" class="px-6 py-4">Role</th>
                                <th scope="col" class="px-6 py-4">Terdaftar Pada</th>
                                <th scope="col" class="px-6 py-4 text-right">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-cream-border/60">
                            <tr
                                v-for="item in users.data"
                                :key="item.id"
                                class="hover:bg-cream-card/50 transition-colors"
                            >
                                <!-- User Info -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-cream-accent/10 flex items-center justify-center text-cream-accent font-bold">
                                            {{ item.name.charAt(0).toUpperCase() }}
                                        </div>
                                        <div>
                                            <p class="font-semibold text-cream-text">{{ item.name }}</p>
                                            <p class="text-xs text-cream-muted">{{ item.email }}</p>
                                        </div>
                                    </div>
                                </td>

                                <!-- Role -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="['px-2.5 py-1 rounded-full text-[11px] font-bold border tracking-wider', roleBadgeClass(item.role)]">
                                        {{ item.role }}
                                    </span>
                                </td>

                                <!-- Terdaftar -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-cream-muted">
                                    {{ formatDate(item.created_at) }}
                                </td>

                                <!-- Tindakan -->
                                <td class="px-6 py-4 whitespace-nowrap text-right space-x-2">
                                    <Link
                                        :href="route('admin.users.edit', item.id)"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors"
                                        title="Edit User"
                                    >
                                        ✏️
                                    </Link>
                                    
                                    <button
                                        @click="deleteUser(item.id, item.name)"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-colors"
                                        title="Hapus User"
                                    >
                                        🗑️
                                    </button>
                                </td>
                            </tr>

                            <tr v-if="users.data.length === 0">
                                <td colspan="4" class="px-6 py-12">
                                    <EmptyState
                                        icon="👥"
                                        title="Tidak ada pengguna ditemukan"
                                        description="Coba ubah kata kunci pencarian atau filter role."
                                    />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination (jika ada lebih dari 1 halaman) -->
                <div v-if="users.last_page > 1" class="border-t border-cream-border p-4 bg-cream-bg/30">
                    <div class="flex items-center justify-between flex-wrap gap-4">
                        <p class="text-sm text-cream-muted">
                            Menampilkan <span class="font-bold text-cream-text">{{ users.from }}</span> hingga <span class="font-bold text-cream-text">{{ users.to }}</span> dari <span class="font-bold text-cream-text">{{ users.total }}</span> entri
                        </p>
                        
                        <div class="flex items-center gap-1">
                            <Link
                                v-for="(link, i) in users.links"
                                :key="i"
                                :href="link.url ?? '#'"
                                v-html="link.label"
                                class="px-3 py-1.5 text-sm rounded-lg border transition-colors"
                                :class="[
                                    link.active 
                                        ? 'bg-cream-accent border-cream-accent text-white font-bold' 
                                        : 'bg-white border-cream-border text-cream-text hover:bg-cream-bg',
                                    !link.url && 'opacity-50 cursor-not-allowed'
                                ]"
                            />
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </AdminLayout>
</template>
