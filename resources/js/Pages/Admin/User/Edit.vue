<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link, useForm } from '@inertiajs/vue3'

const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
})

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    password: '',
    password_confirmation: '',
    role: props.user.role,
})

const submit = () => {
    form.put(route('admin.users.update', props.user.id))
}
</script>

<template>
    <AdminLayout title="Edit User">
        <div class="max-w-2xl mx-auto space-y-6">
            
            <!-- Top bar -->
            <div class="flex items-center gap-4">
                <Link
                    :href="route('admin.users.index')"
                    class="w-10 h-10 rounded-xl bg-white border border-cream-border flex items-center justify-center text-cream-muted hover:text-cream-accent hover:border-cream-accent transition-colors"
                >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </Link>
                <div>
                    <h3 class="text-lg font-bold text-cream-text">Edit User</h3>
                    <p class="text-cream-muted text-xs">Perbarui data akun <strong>{{ user.name }}</strong>.</p>
                </div>
            </div>

            <!-- Form Card -->
            <div class="card p-6 md:p-8">
                <form @submit.prevent="submit" class="space-y-6">
                    
                    <!-- Nama -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-cream-text mb-1.5">Nama Lengkap</label>
                        <input
                            id="name"
                            v-model="form.name"
                            type="text"
                            class="input"
                            :class="form.errors.name && 'border-red-400 focus:ring-red-400 focus:border-red-400'"
                            placeholder="Contoh: John Doe"
                            required
                        />
                        <p v-if="form.errors.name" class="mt-1.5 text-xs text-red-600">{{ form.errors.name }}</p>
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-cream-text mb-1.5">Alamat Email</label>
                        <input
                            id="email"
                            v-model="form.email"
                            type="email"
                            class="input"
                            :class="form.errors.email && 'border-red-400 focus:ring-red-400 focus:border-red-400'"
                            placeholder="Contoh: john@example.com"
                            required
                        />
                        <p v-if="form.errors.email" class="mt-1.5 text-xs text-red-600">{{ form.errors.email }}</p>
                    </div>

                    <!-- Role -->
                    <div>
                        <label for="role" class="block text-sm font-medium text-cream-text mb-1.5">Role (Peran)</label>
                        <select
                            id="role"
                            v-model="form.role"
                            class="input"
                            :class="form.errors.role && 'border-red-400 focus:ring-red-400 focus:border-red-400'"
                            required
                        >
                            <option value="USER">User Biasa</option>
                            <option value="ADMIN">Admin</option>
                        </select>
                        <p v-if="form.errors.role" class="mt-1.5 text-xs text-red-600">{{ form.errors.role }}</p>

                        <div v-if="form.role === 'ADMIN'" class="mt-2 p-3 rounded-lg bg-amber-50 border border-amber-200 flex items-start gap-2 text-amber-800 text-xs">
                            <span class="text-sm">⚠️</span>
                            <p>Admin memiliki akses penuh ke Dashboard Admin. Pastikan Anda memberikan role ini kepada orang yang tepat.</p>
                        </div>
                    </div>

                    <!-- Password Section -->
                    <div class="p-4 rounded-xl bg-cream-bg/50 border border-cream-border space-y-4">
                        <div class="flex items-start gap-2">
                            <span class="text-base">🔒</span>
                            <div>
                                <p class="text-sm font-medium text-cream-text">Ubah Password</p>
                                <p class="text-xs text-cream-muted">Kosongkan jika tidak ingin mengubah password.</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Password -->
                            <div>
                                <label for="password" class="block text-sm font-medium text-cream-text mb-1.5">Password Baru</label>
                                <input
                                    id="password"
                                    v-model="form.password"
                                    type="password"
                                    class="input"
                                    :class="form.errors.password && 'border-red-400 focus:ring-red-400 focus:border-red-400'"
                                    placeholder="Minimal 8 karakter"
                                />
                                <p v-if="form.errors.password" class="mt-1.5 text-xs text-red-600">{{ form.errors.password }}</p>
                            </div>

                            <!-- Konfirmasi Password -->
                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-cream-text mb-1.5">Konfirmasi Password</label>
                                <input
                                    id="password_confirmation"
                                    v-model="form.password_confirmation"
                                    type="password"
                                    class="input"
                                    placeholder="Ulangi password baru"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="pt-4 border-t border-cream-border flex items-center justify-end gap-3">
                        <Link
                            :href="route('admin.users.index')"
                            class="px-5 py-2.5 rounded-xl font-semibold text-cream-muted hover:text-cream-text hover:bg-cream-bg transition-colors text-sm"
                        >
                            Batal
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="btn-primary py-2.5 px-6 text-sm"
                        >
                            <span v-if="form.processing">Menyimpan...</span>
                            <span v-else>Simpan Perubahan</span>
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </AdminLayout>
</template>
