<script setup>
import { ref } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link, useForm } from '@inertiajs/vue3'

const props = defineProps({
    lapangan: {
        type: Object,
        required: true,
    },
})

// ─── Form Setup ────────────────────────────────────────────────────────────
const form = useForm({
    _method: 'PUT',
    nama: props.lapangan.nama,
    jenis: props.lapangan.jenis,
    harga_per_jam: Math.round(props.lapangan.harga_per_jam),
    deskripsi: props.lapangan.deskripsi ?? '',
    foto: null,
    is_tersedia: props.lapangan.is_tersedia,
})

// ─── Image Preview Logic ───────────────────────────────────────────────────
const previewUrl = ref(props.lapangan.foto_url)
const handleFileChange = (e) => {
    const file = e.target.files[0]
    if (file) {
        form.foto = file
        previewUrl.value = URL.createObjectURL(file)
    } else {
        form.foto = null
        previewUrl.value = props.lapangan.foto_url
    }
}

// ─── Submit Form ───────────────────────────────────────────────────────────
const submit = () => {
    // Dikirim sebagai POST karena membawa berkas, tapi disimulasikan sebagai PUT menggunakan field _method
    form.post(route('admin.lapangan.update', props.lapangan.id), {
        forceFormData: true,
    })
}
</script>

<template>
    <AdminLayout title="Edit Lapangan">
        <div class="max-w-3xl mx-auto space-y-6">
            
            <!-- Header & Back Button -->
            <div class="flex items-center gap-3">
                <Link
                    :href="route('admin.lapangan.index')"
                    class="p-2 rounded-xl border border-cream-border bg-cream-card text-cream-muted hover:text-cream-text hover:border-cream-accent transition-colors"
                >
                    ⬅️ Kembali
                </Link>
                <div>
                    <h3 class="text-lg font-bold text-cream-text">Ubah Data Lapangan</h3>
                    <p class="text-cream-muted text-xs">Edit detail unit lapangan bulutangkis "{{ lapangan.nama }}".</p>
                </div>
            </div>

            <!-- Form Card -->
            <div class="card p-6 md:p-8">
                <form @submit.prevent="submit" class="space-y-6">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <!-- Nama Lapangan -->
                        <div class="space-y-2">
                            <label for="nama" class="block text-xs font-bold uppercase tracking-wider text-cream-muted">
                                Nama Lapangan <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="nama"
                                v-model="form.nama"
                                type="text"
                                placeholder="Contoh: Lapangan 1 - VIP"
                                class="input"
                                :class="{ 'border-red-400 focus:border-red-400 focus:ring-red-400': form.errors.nama }"
                            />
                            <p v-if="form.errors.nama" class="text-xs text-red-500 font-semibold">{{ form.errors.nama }}</p>
                        </div>

                        <!-- Jenis Lapangan -->
                        <div class="space-y-2">
                            <label for="jenis" class="block text-xs font-bold uppercase tracking-wider text-cream-muted">
                                Jenis Lapangan <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="jenis"
                                v-model="form.jenis"
                                class="input cursor-pointer"
                                :class="{ 'border-red-400 focus:border-red-400 focus:ring-red-400': form.errors.jenis }"
                            >
                                <option value="" disabled>-- Pilih Jenis --</option>
                                <option value="VIP">VIP</option>
                                <option value="Premium">Premium</option>
                                <option value="Standar">Standar</option>
                                <option value="Outdoor">Outdoor</option>
                            </select>
                            <p v-if="form.errors.jenis" class="text-xs text-red-500 font-semibold">{{ form.errors.jenis }}</p>
                        </div>

                        <!-- Harga Per Jam -->
                        <div class="space-y-2">
                            <label for="harga_per_jam" class="block text-xs font-bold uppercase tracking-wider text-cream-muted">
                                Harga Sewa per Jam (Rp) <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm text-cream-muted font-bold">Rp</span>
                                <input
                                    id="harga_per_jam"
                                    v-model="form.harga_per_jam"
                                    type="number"
                                    min="0"
                                    placeholder="50000"
                                    class="input pl-10"
                                    :class="{ 'border-red-400 focus:border-red-400 focus:ring-red-400': form.errors.harga_per_jam }"
                                />
                            </div>
                            <p v-if="form.errors.harga_per_jam" class="text-xs text-red-500 font-semibold">{{ form.errors.harga_per_jam }}</p>
                        </div>

                        <!-- Status Tersedia -->
                        <div class="space-y-2 flex flex-col justify-center">
                            <label class="block text-xs font-bold uppercase tracking-wider text-cream-muted mb-2">
                                Status Ketersediaan <span class="text-red-500">*</span>
                            </label>
                            <div class="flex items-center gap-3">
                                <button
                                    type="button"
                                    @click="form.is_tersedia = !form.is_tersedia"
                                    class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                                    :class="form.is_tersedia ? 'bg-cream-accent' : 'bg-cream-border'"
                                >
                                    <span
                                        class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                        :class="form.is_tersedia ? 'translate-x-5' : 'translate-x-0'"
                                    />
                                </button>
                                <span class="text-sm font-semibold">
                                    {{ form.is_tersedia ? 'Tersedia untuk disewa' : 'Tutup sementara / pemeliharaan' }}
                                </span>
                            </div>
                            <p v-if="form.errors.is_tersedia" class="text-xs text-red-500 font-semibold">{{ form.errors.is_tersedia }}</p>
                        </div>

                        <!-- Deskripsi -->
                        <div class="space-y-2 md:col-span-2">
                            <label for="deskripsi" class="block text-xs font-bold uppercase tracking-wider text-cream-muted">
                                Deskripsi Lapangan
                            </label>
                            <textarea
                                id="deskripsi"
                                v-model="form.deskripsi"
                                rows="3"
                                placeholder="Tambahkan deskripsi singkat tentang fasilitas lapangan ini (opsional)..."
                                class="input resize-none"
                                :class="{ 'border-red-400 focus:border-red-400 focus:ring-red-400': form.errors.deskripsi }"
                            ></textarea>
                            <p v-if="form.errors.deskripsi" class="text-xs text-red-500 font-semibold">{{ form.errors.deskripsi }}</p>
                        </div>

                    </div>

                    <!-- Upload Foto -->
                    <div class="space-y-3">
                        <label class="block text-xs font-bold uppercase tracking-wider text-cream-muted">
                            Foto Lapangan <span class="text-xs text-cream-muted font-normal">(Kosongkan jika tidak ingin mengubah foto)</span>
                        </label>
                        
                        <div class="flex flex-col md:flex-row gap-6 items-start">
                            <!-- Image Preview Area -->
                            <div class="w-full md:w-48 h-32 rounded-2xl border border-cream-border bg-cream-bg flex items-center justify-center overflow-hidden shadow-cream flex-shrink-0">
                                <img
                                    v-if="previewUrl"
                                    :src="previewUrl"
                                    alt="Preview"
                                    class="w-full h-full object-cover"
                                />
                                <div v-else class="text-center p-3 text-cream-muted">
                                    <span class="text-3xl block mb-1">🖼️</span>
                                    <span class="text-[10px] uppercase font-bold">No Image</span>
                                </div>
                            </div>

                            <!-- Upload Button Input -->
                            <div class="flex-1 w-full space-y-2">
                                <div class="relative w-full">
                                    <input
                                        id="foto"
                                        type="file"
                                        accept="image/*"
                                        @change="handleFileChange"
                                        class="hidden"
                                    />
                                    <label
                                        for="foto"
                                        class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl border border-dashed border-cream-accent/50 text-cream-accent bg-cream-accent/5 hover:bg-cream-accent/10 cursor-pointer font-semibold text-xs transition-colors"
                                    >
                                        📤 Ganti Berkas Gambar (Max 2MB)
                                    </label>
                                </div>
                                <p class="text-[11px] text-cream-muted">Biarkan kosong untuk mempertahankan foto saat ini.</p>
                                <p v-if="form.errors.foto" class="text-xs text-red-500 font-semibold">{{ form.errors.foto }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="divider"></div>
                    <div class="flex items-center justify-end gap-3">
                        <Link
                            :href="route('admin.lapangan.index')"
                            class="btn-secondary px-5"
                        >
                            Batal
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="btn-primary px-6"
                            :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                        >
                            <template v-if="form.processing">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Menyimpan...
                            </template>
                            <span v-else>Simpan Perubahan</span>
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </AdminLayout>
</template>
