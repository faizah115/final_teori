<script setup>
import { ref, computed, watch } from 'vue'
import { Link, useForm, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

// ─── Props dari Inertia ────────────────────────────────────────────────────
const props = defineProps({
    lapangan: {
        type: Object,
        required: true,
    },
    jadwalTerisi: {
        type: Array,
        default: () => [],
    },
})

const page = usePage()
const user = computed(() => page.props.auth?.user ?? null)
const isAdmin = computed(() => user.value?.role === 'ADMIN')

// ─── Format Helpers ────────────────────────────────────────────────────────
const formatRupiah = (amount) =>
    new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(amount)

const jenisColor = (jenis) => {
    const map = {
        VIP:     'badge-warning',
        Premium: 'bg-purple-100 text-purple-700',
        Standar: 'badge bg-blue-100 text-blue-700',
        Outdoor: 'badge bg-emerald-100 text-emerald-700',
    }
    return map[jenis] ?? 'badge bg-cream-border text-cream-muted'
}

// ─── Date Helpers ──────────────────────────────────────────────────────────
const today = new Date().toISOString().split('T')[0]

const formatTanggalIndo = (dateStr) => {
    if (!dateStr) return ''
    return new Intl.DateTimeFormat('id-ID', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    }).format(new Date(dateStr + 'T00:00:00'))
}

// ─── Reservasi Form ────────────────────────────────────────────────────────
const form = useForm({
    lapangan_id: props.lapangan.id,
    tanggal: '',
    jam_mulai: '',
    jam_selesai: '',
})

// ── Frontend Validation ────────────────────────────────────────────────────
const frontendErrors = computed(() => {
    const errors = {}

    if (form.tanggal && form.tanggal < today) {
        errors.tanggal = 'Tanggal tidak boleh di masa lalu.'
    }

    if (form.jam_mulai && form.jam_selesai) {
        if (form.jam_selesai <= form.jam_mulai) {
            errors.jam_selesai = 'Jam selesai harus lebih dari jam mulai.'
        }
    }

    // Cek overlap dengan jadwal terisi
    if (form.tanggal && form.jam_mulai && form.jam_selesai && !errors.jam_selesai) {
        const jamMulaiFull = form.jam_mulai + ':00'
        const jamSelesaiFull = form.jam_selesai + ':00'
        
        const terjadwal = props.jadwalTerisi.filter((j) => j.tanggal === form.tanggal)
        const overlap = terjadwal.some(
            (j) => jamMulaiFull < j.jam_selesai && jamSelesaiFull > j.jam_mulai
        )
        if (overlap) {
            errors.jam_mulai = 'Waktu yang dipilih bentrok dengan reservasi lain.'
        }
    }

    return errors
})

const isFormValid = computed(() =>
    form.tanggal &&
    form.jam_mulai &&
    form.jam_selesai &&
    Object.keys(frontendErrors.value).length === 0
)

// ── Durasi & Harga Total ───────────────────────────────────────────────────
const durasi = computed(() => {
    if (!form.jam_mulai || !form.jam_selesai) return null
    const toMin = (t) => { const [h, m] = t.split(':').map(Number); return h * 60 + m }
    const diff = toMin(form.jam_selesai) - toMin(form.jam_mulai)
    return diff > 0 ? diff / 60 : null
})

const totalHarga = computed(() => {
    if (!durasi.value) return null
    return durasi.value * parseFloat(props.lapangan.harga_per_jam)
})

// ── Submit ─────────────────────────────────────────────────────────────────
const submitReservasi = () => {
    if (!isFormValid.value || form.processing) return
    form.post(route('reservasi.store'), {
        preserveScroll: true,
    })
}

// ── Jadwal display (grouped by tanggal) ───────────────────────────────────
const jadwalGrouped = computed(() => {
    const groups = {}
    props.jadwalTerisi.forEach((j) => {
        if (!groups[j.tanggal]) groups[j.tanggal] = []
        groups[j.tanggal].push(j)
    })
    return groups
})

const showJadwal = ref(false)
</script>

<template>
    <AppLayout :title="lapangan.nama">

        <!-- ─── Page Header ──────────────────────────────────────────────── -->
        <div class="bg-cream-warm-gradient border-b border-cream-border">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <!-- Breadcrumb -->
                <nav class="flex items-center gap-2 text-sm text-cream-muted mb-3 flex-wrap">
                    <Link :href="route('home')" class="hover:text-cream-accent transition-colors">Beranda</Link>
                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                    </svg>
                    <Link :href="route('lapangan.index')" class="hover:text-cream-accent transition-colors">Lapangan</Link>
                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                    </svg>
                    <span class="text-cream-text font-medium truncate max-w-48">{{ lapangan.nama }}</span>
                </nav>
            </div>
        </div>

        <!-- ─── Main Content ─────────────────────────────────────────────── -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">

                <!-- ── Left: Lapangan Info ──────────────────────────────── -->
                <div class="lg:col-span-3 space-y-5 animate-fade-in">

                    <!-- Image -->
                    <div class="card overflow-hidden">
                        <div class="relative h-72 sm:h-96">
                            <img
                                v-if="lapangan.foto_url"
                                :src="lapangan.foto_url"
                                :alt="lapangan.nama"
                                class="w-full h-full object-cover"
                            />
                            <div
                                v-else
                                class="w-full h-full bg-cream-accent-gradient flex items-center justify-center"
                            >
                                <span class="text-7xl opacity-40">🏸</span>
                            </div>

                            <!-- Overlay badges -->
                            <div class="absolute top-4 left-4 flex gap-2">
                                <span :class="['badge text-xs font-semibold shadow-sm px-2.5 py-1', jenisColor(lapangan.jenis)]">
                                    {{ lapangan.jenis }}
                                </span>
                                <span
                                    :class="[
                                        'badge text-xs font-semibold shadow-sm px-2.5 py-1',
                                        lapangan.is_tersedia ? 'bg-emerald-500 text-white' : 'bg-slate-500 text-white'
                                    ]"
                                >
                                    {{ lapangan.is_tersedia ? '● Tersedia' : '● Tidak Tersedia' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Detail Info -->
                    <div class="card p-6">
                        <h1 class="font-display text-2xl font-bold text-cream-text mb-4">
                            {{ lapangan.nama }}
                        </h1>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-cream-bg rounded-xl p-4 border border-cream-border">
                                <p class="text-xs text-cream-muted mb-1 flex items-center gap-1.5">
                                    <span>🏟️</span> Jenis Lapangan
                                </p>
                                <p class="font-display font-semibold text-cream-text">{{ lapangan.jenis }}</p>
                            </div>
                            <div class="bg-cream-bg rounded-xl p-4 border border-cream-border">
                                <p class="text-xs text-cream-muted mb-1 flex items-center gap-1.5">
                                    <span>💰</span> Harga per Jam
                                </p>
                                <p class="font-display font-bold text-cream-accent text-lg">
                                    {{ formatRupiah(lapangan.harga_per_jam) }}
                                </p>
                            </div>
                            <div class="bg-cream-bg rounded-xl p-4 border border-cream-border">
                                <p class="text-xs text-cream-muted mb-1 flex items-center gap-1.5">
                                    <span>🕐</span> Jam Operasional
                                </p>
                                <p class="font-display font-semibold text-cream-text">06:00 – 22:00</p>
                            </div>
                            <div class="bg-cream-bg rounded-xl p-4 border border-cream-border">
                                <p class="text-xs text-cream-muted mb-1 flex items-center gap-1.5">
                                    <span>📅</span> Buka
                                </p>
                                <p class="font-display font-semibold text-cream-text">Setiap Hari</p>
                            </div>
                        </div>
                    </div>

                    <!-- Jadwal Terisi Toggle -->
                    <div class="card overflow-hidden">
                        <button
                            @click="showJadwal = !showJadwal"
                            class="w-full flex items-center justify-between p-5 hover:bg-cream-bg transition-colors"
                        >
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center">
                                    <span class="text-base">📅</span>
                                </div>
                                <div class="text-left">
                                    <p class="font-semibold text-cream-text text-sm">Jadwal Sudah Terisi</p>
                                    <p class="text-xs text-cream-muted">
                                        {{ jadwalTerisi.length }} sesi terjadwal ke depan
                                    </p>
                                </div>
                            </div>
                            <svg
                                class="w-4 h-4 text-cream-muted transition-transform duration-200"
                                :class="showJadwal ? 'rotate-180' : ''"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <Transition name="accordion">
                            <div v-if="showJadwal" class="border-t border-cream-border px-5 pb-5">
                                <div v-if="jadwalTerisi.length === 0" class="py-6 text-center">
                                    <p class="text-cream-muted text-sm">✅ Belum ada jadwal yang terisi</p>
                                </div>
                                <div v-else class="space-y-3 pt-4">
                                    <div
                                        v-for="(sesiList, tanggal) in jadwalGrouped"
                                        :key="tanggal"
                                        class="bg-cream-bg rounded-xl p-3 border border-cream-border"
                                    >
                                        <p class="text-xs font-semibold text-cream-text mb-2">
                                            📅 {{ formatTanggalIndo(tanggal) }}
                                        </p>
                                        <div class="flex flex-wrap gap-2">
                                            <span
                                                v-for="(sesi, i) in sesiList"
                                                :key="i"
                                                :class="[
                                                    'inline-flex items-center gap-1 px-2 py-1 rounded-lg text-[11px] font-semibold border',
                                                    sesi.status === 'KONFIRMASI' ? 'bg-red-50 text-red-700 border-red-200' : 'bg-amber-50 text-amber-700 border-amber-200'
                                                ]"
                                            >
                                                {{ sesi.jam_mulai }} – {{ sesi.jam_selesai }}
                                                <span class="opacity-70">({{ sesi.status }})</span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </Transition>
                    </div>
                </div>

                <!-- ── Right: Booking Form ───────────────────────────────── -->
                <div class="lg:col-span-2 animate-slide-in-right">
                    <div class="card-lg p-6 sticky top-24">

                        <!-- Form Title -->
                        <div class="flex items-center gap-2.5 mb-6">
                            <div class="w-9 h-9 rounded-xl bg-cream-accent flex items-center justify-center">
                                <span class="text-base">📋</span>
                            </div>
                            <div>
                                <h2 class="font-display font-bold text-cream-text text-base leading-tight">Buat Reservasi</h2>
                                <p class="text-xs text-cream-muted leading-tight">Isi form di bawah ini</p>
                            </div>
                        </div>

                        <!-- Not logged in -->
                        <div v-if="!user" class="text-center py-6">
                            <div class="w-16 h-16 rounded-2xl bg-cream-accent/10 flex items-center justify-center mx-auto mb-4">
                                <span class="text-3xl">🔐</span>
                            </div>
                            <p class="font-semibold text-cream-text mb-1">Login Diperlukan</p>
                            <p class="text-cream-muted text-sm mb-5">Silakan masuk untuk membuat reservasi lapangan ini.</p>
                            <div class="flex flex-col gap-2">
                                <Link :href="route('login')" class="btn-primary w-full text-center">
                                    Masuk Sekarang
                                </Link>
                                <Link :href="route('register')" class="btn-secondary w-full text-center">
                                    Belum punya akun? Daftar
                                </Link>
                            </div>
                        </div>

                        <!-- Admin tidak boleh booking -->
                        <div v-else-if="isAdmin" class="text-center py-6">
                            <div class="w-16 h-16 rounded-2xl bg-blue-100 flex items-center justify-center mx-auto mb-4">
                                <span class="text-3xl">🛡️</span>
                            </div>
                            <p class="font-semibold text-cream-text mb-1">Mode Admin</p>
                            <p class="text-cream-muted text-sm mb-5">
                                Anda login sebagai Admin. Admin tidak dapat membuat reservasi.
                                Gunakan Panel Admin untuk mengelola lapangan dan reservasi.
                            </p>
                            <Link :href="route('admin.dashboard')" class="btn-primary w-full text-center">
                                🏠 Buka Panel Admin
                            </Link>
                        </div>

                        <!-- Lapangan tidak tersedia -->
                        <div v-else-if="!lapangan.is_tersedia" class="text-center py-6">
                            <div class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-4">
                                <span class="text-3xl">🚫</span>
                            </div>
                            <p class="font-semibold text-cream-text mb-1">Lapangan Tidak Tersedia</p>
                            <p class="text-cream-muted text-sm mb-5">
                                Lapangan ini sedang tidak dapat direservasi. Silakan pilih lapangan lain.
                            </p>
                            <Link :href="route('lapangan.index')" class="btn-secondary w-full text-center">
                                Lihat Lapangan Lain
                            </Link>
                        </div>

                        <!-- Form Reservasi -->
                        <template v-else>
                            <div v-if="form.errors.lapangan_id" class="mb-4 p-3 rounded-xl bg-red-50 border border-red-200 flex items-start gap-2">
                                <span class="text-red-500">⚠️</span>
                                <p class="text-sm text-red-700">{{ form.errors.lapangan_id }}</p>
                            </div>
                            <form v-else @submit.prevent="submitReservasi" class="space-y-4" novalidate>

                            <!-- Field: Tanggal -->
                            <div>
                                <label for="tanggal" class="block text-sm font-medium text-cream-text mb-1.5">
                                    📅 Tanggal Reservasi
                                </label>
                                <input
                                    id="tanggal"
                                    v-model="form.tanggal"
                                    type="date"
                                    :min="today"
                                    class="input"
                                    :class="(frontendErrors.tanggal || form.errors.tanggal) && 'border-red-400 focus:ring-red-400 focus:border-red-400'"
                                />
                                <p v-if="frontendErrors.tanggal || form.errors.tanggal" class="mt-1.5 text-xs text-red-600 flex items-start gap-1">
                                    <svg class="w-3.5 h-3.5 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    {{ frontendErrors.tanggal || form.errors.tanggal }}
                                </p>
                                <!-- Preview tanggal -->
                                <p v-if="form.tanggal && !frontendErrors.tanggal" class="mt-1.5 text-xs text-cream-muted">
                                    {{ formatTanggalIndo(form.tanggal) }}
                                </p>
                            </div>

                            <!-- Field: Jam Mulai -->
                            <div>
                                <label for="jam_mulai" class="block text-sm font-medium text-cream-text mb-1.5">
                                    🕐 Jam Mulai
                                </label>
                                <input
                                    id="jam_mulai"
                                    v-model="form.jam_mulai"
                                    type="time"
                                    min="06:00"
                                    max="22:00"
                                    step="1800"
                                    class="input"
                                    :class="(frontendErrors.jam_mulai || form.errors.jam_mulai) && 'border-red-400 focus:ring-red-400 focus:border-red-400'"
                                />
                                <p v-if="frontendErrors.jam_mulai || form.errors.jam_mulai" class="mt-1.5 text-xs text-red-600 flex items-start gap-1">
                                    <svg class="w-3.5 h-3.5 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    {{ frontendErrors.jam_mulai || form.errors.jam_mulai }}
                                </p>
                            </div>

                            <!-- Field: Jam Selesai -->
                            <div>
                                <label for="jam_selesai" class="block text-sm font-medium text-cream-text mb-1.5">
                                    🕕 Jam Selesai
                                </label>
                                <input
                                    id="jam_selesai"
                                    v-model="form.jam_selesai"
                                    type="time"
                                    :min="form.jam_mulai || '06:00'"
                                    max="22:00"
                                    step="1800"
                                    class="input"
                                    :class="(frontendErrors.jam_selesai || form.errors.jam_selesai) && 'border-red-400 focus:ring-red-400 focus:border-red-400'"
                                />
                                <p v-if="frontendErrors.jam_selesai || form.errors.jam_selesai" class="mt-1.5 text-xs text-red-600 flex items-start gap-1">
                                    <svg class="w-3.5 h-3.5 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    {{ frontendErrors.jam_selesai || form.errors.jam_selesai }}
                                </p>
                            </div>

                            <!-- Price Summary -->
                            <Transition name="scale-in">
                                <div
                                    v-if="durasi && !Object.keys(frontendErrors).length"
                                    class="bg-cream-bg rounded-xl p-4 border border-cream-border space-y-2"
                                >
                                    <div class="flex justify-between text-sm">
                                        <span class="text-cream-muted">Durasi</span>
                                        <span class="font-medium text-cream-text">
                                            {{ durasi }} jam
                                        </span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-cream-muted">Harga / jam</span>
                                        <span class="font-medium text-cream-text">
                                            {{ formatRupiah(lapangan.harga_per_jam) }}
                                        </span>
                                    </div>
                                    <div class="divider pt-1" />
                                    <div class="flex justify-between">
                                        <span class="font-semibold text-cream-text">Total</span>
                                        <span class="font-display font-bold text-cream-accent text-lg">
                                            {{ formatRupiah(totalHarga) }}
                                        </span>
                                    </div>
                                </div>
                            </Transition>

                            <!-- Status Info -->
                            <div class="flex items-start gap-2 p-3 rounded-xl bg-amber-50 border border-amber-200">
                                <span class="text-base flex-shrink-0">ℹ️</span>
                                <p class="text-xs text-amber-800 leading-relaxed">
                                    Reservasi akan berstatus <strong>PENDING</strong> dan menunggu konfirmasi dari admin.
                                </p>
                            </div>

                            <!-- Submit Button -->
                            <button
                                type="submit"
                                :disabled="!isFormValid || form.processing"
                                class="w-full py-3 rounded-2xl font-display font-semibold text-white text-sm transition-all duration-200 shadow-cream-md"
                                :class="isFormValid && !form.processing
                                    ? 'bg-cream-accent hover:bg-cream-accent/90 active:scale-[0.98]'
                                    : 'bg-cream-muted cursor-not-allowed opacity-60'"
                            >
                                <span v-if="form.processing" class="flex items-center justify-center gap-2">
                                    <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                                    </svg>
                                    Memproses...
                                </span>
                                <span v-else>
                                    🏸 Reservasi Sekarang
                                </span>
                            </button>
                        </form>
                    </template>
                    </div>
                </div>

            </div>
        </div>

    </AppLayout>
</template>

<style scoped>
.accordion-enter-active,
.accordion-leave-active {
    transition: all 0.25s ease;
    overflow: hidden;
}
.accordion-enter-from,
.accordion-leave-to {
    opacity: 0;
    max-height: 0;
}
.accordion-enter-to,
.accordion-leave-from {
    max-height: 500px;
}

.scale-in-enter-active,
.scale-in-leave-active {
    transition: all 0.2s ease;
}
.scale-in-enter-from,
.scale-in-leave-to {
    opacity: 0;
    transform: scale(0.97) translateY(-4px);
}
</style>
