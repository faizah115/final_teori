<script setup>
import { ref } from 'vue'
import { Link, useForm, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

// ─── Props ─────────────────────────────────────────────────────────────────
const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
})

// ─── Profile Update Form ───────────────────────────────────────────────────
const profileForm = useForm({
    name: props.user.name,
    email: props.user.email,
})

const submitProfile = () => {
    profileForm.patch(route('profile.update'), {
        preserveScroll: true,
    })
}

// ─── Password Update Form ──────────────────────────────────────────────────
const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
})

const showCurrentPassword = ref(false)
const showNewPassword = ref(false)
const showConfirmPassword = ref(false)

const submitPassword = () => {
    passwordForm.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => passwordForm.reset(),
        onError: () => {
            if (passwordForm.errors.password) {
                passwordForm.reset('password', 'password_confirmation')
            }
            if (passwordForm.errors.current_password) {
                passwordForm.reset('current_password')
            }
        },
    })
}

// ─── Delete Account Form ───────────────────────────────────────────────────
const deleteForm = useForm({
    password: '',
})

const showDeleteConfirm = ref(false)
const showDeletePassword = ref(false)

const submitDelete = () => {
    deleteForm.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => {
            showDeleteConfirm.value = false
        },
        onError: () => deleteForm.reset(),
    })
}

// ─── Flash Messages ────────────────────────────────────────────────────────
const page = usePage()
</script>

<template>
    <AppLayout title="Edit Profil">

        <!-- ─── Page Header ──────────────────────────────────────────────── -->
        <div class="bg-cream-warm-gradient border-b border-cream-border">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
                <!-- Breadcrumb -->
                <nav class="flex items-center gap-2 text-sm text-cream-muted mb-4">
                    <Link :href="route('home')" class="hover:text-cream-accent transition-colors">Beranda</Link>
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                    </svg>
                    <span class="text-cream-text font-medium">Edit Profil</span>
                </nav>

                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-cream-accent flex items-center justify-center text-white text-xl font-bold shadow-cream-md">
                        {{ user.name.charAt(0).toUpperCase() }}
                    </div>
                    <div>
                        <h1 class="font-display text-2xl sm:text-3xl font-bold text-cream-text">
                            Edit Profil
                        </h1>
                        <p class="text-cream-muted text-sm">
                            Kelola informasi akun dan keamanan
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- ─── Content ─────────────────────────────────────────────────── -->
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

            <!-- ── Section 1: Informasi Profil ──────────────────────────── -->
            <div class="card p-6 sm:p-8 animate-fade-in">
                <div class="flex items-center gap-2.5 mb-6">
                    <div class="w-9 h-9 rounded-xl bg-cream-accent/10 flex items-center justify-center">
                        <span class="text-base">👤</span>
                    </div>
                    <div>
                        <h2 class="font-display font-bold text-cream-text text-base">Informasi Profil</h2>
                        <p class="text-xs text-cream-muted">Perbarui nama dan alamat email akun Anda</p>
                    </div>
                </div>

                <form @submit.prevent="submitProfile" class="space-y-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <!-- Nama -->
                        <div class="space-y-2">
                            <label for="profile-name" class="block text-xs font-bold uppercase tracking-wider text-cream-muted">
                                Nama Lengkap
                            </label>
                            <input
                                id="profile-name"
                                v-model="profileForm.name"
                                type="text"
                                class="input"
                                :class="{ 'border-red-400 focus:border-red-400 focus:ring-red-400': profileForm.errors.name }"
                                required
                            />
                            <p v-if="profileForm.errors.name" class="text-xs text-red-500 font-semibold">{{ profileForm.errors.name }}</p>
                        </div>

                        <!-- Email -->
                        <div class="space-y-2">
                            <label for="profile-email" class="block text-xs font-bold uppercase tracking-wider text-cream-muted">
                                Alamat Email
                            </label>
                            <input
                                id="profile-email"
                                v-model="profileForm.email"
                                type="email"
                                class="input"
                                :class="{ 'border-red-400 focus:border-red-400 focus:ring-red-400': profileForm.errors.email }"
                                required
                            />
                            <p v-if="profileForm.errors.email" class="text-xs text-red-500 font-semibold">{{ profileForm.errors.email }}</p>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button
                            type="submit"
                            :disabled="profileForm.processing"
                            class="btn-primary px-6"
                            :class="{ 'opacity-50 cursor-not-allowed': profileForm.processing }"
                        >
                            <span v-if="profileForm.processing">Menyimpan...</span>
                            <span v-else>Simpan Perubahan</span>
                        </button>
                    </div>

                    <Transition enter-active-class="transition ease-in-out" enter-from-class="opacity-0" leave-active-class="transition ease-in-out" leave-to-class="opacity-0">
                        <p v-if="profileForm.recentlySuccessful" class="text-sm font-semibold text-emerald-600 flex items-center gap-1.5">
                            ✅ Profil berhasil diperbarui
                        </p>
                    </Transition>
                </form>
            </div>

            <!-- ── Section 2: Ubah Password ─────────────────────────────── -->
            <div class="card p-6 sm:p-8 animate-fade-in" style="animation-delay: 0.1s">
                <div class="flex items-center gap-2.5 mb-6">
                    <div class="w-9 h-9 rounded-xl bg-amber-100 flex items-center justify-center">
                        <span class="text-base">🔒</span>
                    </div>
                    <div>
                        <h2 class="font-display font-bold text-cream-text text-base">Ubah Password</h2>
                        <p class="text-xs text-cream-muted">Pastikan menggunakan password yang kuat dan unik</p>
                    </div>
                </div>

                <form @submit.prevent="submitPassword" class="space-y-5">
                    <!-- Password Lama -->
                    <div class="space-y-2">
                        <label for="current_password" class="block text-xs font-bold uppercase tracking-wider text-cream-muted">
                            Password Saat Ini
                        </label>
                        <div class="relative">
                            <input
                                id="current_password"
                                v-model="passwordForm.current_password"
                                :type="showCurrentPassword ? 'text' : 'password'"
                                autocomplete="current-password"
                                placeholder="Masukkan password saat ini"
                                class="input pr-12"
                                :class="{ 'border-red-400 focus:border-red-400 focus:ring-red-400': passwordForm.errors.current_password }"
                            />
                            <button type="button" @click="showCurrentPassword = !showCurrentPassword" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-cream-muted hover:text-cream-text transition-colors" tabindex="-1">
                                <svg v-if="!showCurrentPassword" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                <svg v-else class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" /></svg>
                            </button>
                        </div>
                        <p v-if="passwordForm.errors.current_password" class="text-xs text-red-500 font-semibold">{{ passwordForm.errors.current_password }}</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <!-- Password Baru -->
                        <div class="space-y-2">
                            <label for="new_password" class="block text-xs font-bold uppercase tracking-wider text-cream-muted">
                                Password Baru
                            </label>
                            <div class="relative">
                                <input
                                    id="new_password"
                                    v-model="passwordForm.password"
                                    :type="showNewPassword ? 'text' : 'password'"
                                    autocomplete="new-password"
                                    placeholder="Minimal 8 karakter"
                                    class="input pr-12"
                                    :class="{ 'border-red-400 focus:border-red-400 focus:ring-red-400': passwordForm.errors.password }"
                                />
                                <button type="button" @click="showNewPassword = !showNewPassword" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-cream-muted hover:text-cream-text transition-colors" tabindex="-1">
                                    <svg v-if="!showNewPassword" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                    <svg v-else class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" /></svg>
                                </button>
                            </div>
                            <p v-if="passwordForm.errors.password" class="text-xs text-red-500 font-semibold">{{ passwordForm.errors.password }}</p>
                        </div>

                        <!-- Konfirmasi Password Baru -->
                        <div class="space-y-2">
                            <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-cream-muted">
                                Konfirmasi Password Baru
                            </label>
                            <div class="relative">
                                <input
                                    id="password_confirmation"
                                    v-model="passwordForm.password_confirmation"
                                    :type="showConfirmPassword ? 'text' : 'password'"
                                    autocomplete="new-password"
                                    placeholder="Ulangi password baru"
                                    class="input pr-12"
                                    :class="{ 'border-red-400 focus:border-red-400 focus:ring-red-400': passwordForm.errors.password_confirmation }"
                                />
                                <button type="button" @click="showConfirmPassword = !showConfirmPassword" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-cream-muted hover:text-cream-text transition-colors" tabindex="-1">
                                    <svg v-if="!showConfirmPassword" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                    <svg v-else class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" /></svg>
                                </button>
                            </div>
                            <p v-if="passwordForm.errors.password_confirmation" class="text-xs text-red-500 font-semibold">{{ passwordForm.errors.password_confirmation }}</p>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button
                            type="submit"
                            :disabled="passwordForm.processing"
                            class="btn-primary px-6"
                            :class="{ 'opacity-50 cursor-not-allowed': passwordForm.processing }"
                        >
                            <span v-if="passwordForm.processing">Mengubah...</span>
                            <span v-else>Ubah Password</span>
                        </button>
                    </div>

                    <Transition enter-active-class="transition ease-in-out" enter-from-class="opacity-0" leave-active-class="transition ease-in-out" leave-to-class="opacity-0">
                        <p v-if="passwordForm.recentlySuccessful" class="text-sm font-semibold text-emerald-600 flex items-center gap-1.5">
                            ✅ Password berhasil diubah
                        </p>
                    </Transition>
                </form>
            </div>

            <!-- ── Section 3: Hapus Akun ────────────────────────────────── -->
            <div class="card p-6 sm:p-8 border-red-200 animate-fade-in" style="animation-delay: 0.2s">
                <div class="flex items-center gap-2.5 mb-4">
                    <div class="w-9 h-9 rounded-xl bg-red-100 flex items-center justify-center">
                        <span class="text-base">⚠️</span>
                    </div>
                    <div>
                        <h2 class="font-display font-bold text-red-700 text-base">Hapus Akun</h2>
                        <p class="text-xs text-cream-muted">Tindakan ini tidak dapat dibatalkan</p>
                    </div>
                </div>

                <p class="text-sm text-cream-muted mb-4 leading-relaxed">
                    Setelah akun dihapus, semua data dan reservasi Anda akan dihapus secara permanen.
                    Pastikan Anda telah mengunduh data yang ingin disimpan sebelum melanjutkan.
                </p>

                <button
                    v-if="!showDeleteConfirm"
                    @click="showDeleteConfirm = true"
                    class="btn-danger px-6"
                >
                    🗑️ Hapus Akun Saya
                </button>

                <!-- Delete Confirmation -->
                <Transition name="scale-in">
                    <div v-if="showDeleteConfirm" class="mt-4 bg-red-50 border border-red-200 rounded-2xl p-5 space-y-4">
                        <p class="text-sm text-red-800 font-semibold">
                            ⚠️ Apakah Anda yakin ingin menghapus akun? Masukkan password untuk konfirmasi.
                        </p>

                        <div class="space-y-2">
                            <label for="delete-password" class="block text-xs font-bold uppercase tracking-wider text-red-700">
                                Password
                            </label>
                            <div class="relative">
                                <input
                                    id="delete-password"
                                    v-model="deleteForm.password"
                                    :type="showDeletePassword ? 'text' : 'password'"
                                    placeholder="Masukkan password untuk konfirmasi"
                                    class="input pr-12 border-red-300 focus:border-red-400 focus:ring-red-400"
                                />
                                <button type="button" @click="showDeletePassword = !showDeletePassword" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-red-400 hover:text-red-600 transition-colors" tabindex="-1">
                                    <svg v-if="!showDeletePassword" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                    <svg v-else class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" /></svg>
                                </button>
                            </div>
                            <p v-if="deleteForm.errors.password" class="text-xs text-red-500 font-semibold">{{ deleteForm.errors.password }}</p>
                        </div>

                        <div class="flex items-center gap-3">
                            <button
                                @click="submitDelete"
                                :disabled="deleteForm.processing || !deleteForm.password"
                                class="btn-danger px-6"
                                :class="{ 'opacity-50 cursor-not-allowed': deleteForm.processing || !deleteForm.password }"
                            >
                                <span v-if="deleteForm.processing">Menghapus...</span>
                                <span v-else>Ya, Hapus Akun</span>
                            </button>
                            <button
                                @click="showDeleteConfirm = false; deleteForm.reset()"
                                class="btn-secondary px-6"
                            >
                                Batal
                            </button>
                        </div>
                    </div>
                </Transition>
            </div>

        </div>

    </AppLayout>
</template>

<style scoped>
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
