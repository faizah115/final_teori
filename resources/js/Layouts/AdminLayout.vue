<script setup>
import { ref, computed, watch } from 'vue'
import { Link, usePage, router } from '@inertiajs/vue3'

defineProps({
    title: {
        type: String,
        default: '',
    },
})

// ─── Shared State ──────────────────────────────────────────────────────────
const page = usePage()
const user = computed(() => page.props.auth?.user ?? null)
const flash = computed(() => page.props.flash ?? {})

// ─── Mobile Sidebar ────────────────────────────────────────────────────────
const isSidebarOpen = ref(false)

const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value
}
const closeSidebar = () => {
    isSidebarOpen.value = false
}

// ─── Navigation ────────────────────────────────────────────────────────────
const navLinks = [
    { name: 'Dashboard', routeName: 'admin.dashboard', icon: '📊' },
    { name: 'Kelola Lapangan', routeName: 'admin.lapangan.index', icon: '🏸' },
    { name: 'Kelola Reservasi', routeName: 'admin.reservasi.index', icon: '📅' },
    { name: 'Kelola User', routeName: 'admin.users.index', icon: '👥' },
]

const isActive = (routeName) => {
    try {
        return route().current(routeName) || route().current(routeName + '.*')
    } catch {
        return false
    }
}

// ─── Logout ────────────────────────────────────────────────────────────────
const logout = () => {
    router.post(route('logout'))
}

import { useToast } from 'vue-toastification'

const toast = useToast()

// Watch flash to trigger toast
watch(flash, (newFlash) => {
    if (newFlash.success) toast.success(newFlash.success)
    if (newFlash.error) toast.error(newFlash.error)
    if (newFlash.warning) toast.warning(newFlash.warning)
    if (newFlash.info) toast.info(newFlash.info)
}, { deep: true, immediate: true })
</script>

<template>
    <div class="min-h-screen bg-cream-bg flex text-cream-text font-sans">

        <!-- ─── Mobile Sidebar Overlay ─────────────────────────────────────── -->
        <Transition name="fade">
            <div
                v-if="isSidebarOpen"
                @click="closeSidebar"
                class="fixed inset-0 z-40 bg-black/30 backdrop-blur-sm lg:hidden"
            />
        </Transition>

        <!-- ─── Sidebar ────────────────────────────────────────────────────── -->
        <aside
            :class="[
                'fixed inset-y-0 left-0 z-50 w-64 bg-cream-card border-r border-cream-border flex flex-col justify-between transition-transform duration-300 lg:static lg:translate-x-0',
                isSidebarOpen ? 'translate-x-0' : '-translate-x-full'
            ]"
        >
            <div>
                <!-- Brand Logo -->
                <div class="h-16 px-6 border-b border-cream-border flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-cream-accent flex items-center justify-center shadow-cream">
                        <span class="text-lg">🏸</span>
                    </div>
                    <div>
                        <p class="font-display font-bold text-cream-text text-sm leading-tight">Badminton</p>
                        <p class="font-display font-bold text-cream-accent text-xs leading-tight tracking-wider">ADMIN PANEL</p>
                    </div>
                </div>

                <!-- Navigation Menu -->
                <nav class="p-4 space-y-1">
                    <Link
                        v-for="link in navLinks"
                        :key="link.routeName"
                        :href="route(link.routeName)"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200"
                        :class="isActive(link.routeName)
                            ? 'text-white bg-cream-accent shadow-cream'
                            : 'text-cream-muted hover:text-cream-text hover:bg-cream-bg'"
                        @click="closeSidebar"
                    >
                        <span class="text-base">{{ link.icon }}</span>
                        {{ link.name }}
                    </Link>
                </nav>
            </div>

            <!-- Footer Sidebar / User Info -->
            <div class="p-4 border-t border-cream-border bg-cream-bg/40">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-cream-accent flex items-center justify-center text-white font-bold shadow-cream">
                        {{ user?.name ? user.name.charAt(0).toUpperCase() : 'A' }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold truncate">{{ user?.name ?? 'Administrator' }}</p>
                        <span class="inline-flex badge bg-cream-accent/15 text-cream-accent text-[10px] font-bold mt-0.5">
                            Admin Role
                        </span>
                    </div>
                </div>
                
                <Link
                    :href="route('home')"
                    class="flex items-center justify-center gap-2 w-full px-4 py-2.5 rounded-xl border border-cream-border text-xs font-bold bg-cream-card hover:bg-cream-bg transition-colors mb-2"
                >
                    🏠 Ke Halaman Utama
                </Link>

                <button
                    @click="logout"
                    class="flex items-center justify-center gap-2 w-full px-4 py-2.5 rounded-xl text-xs font-bold text-red-600 bg-red-50 hover:bg-red-100 transition-colors"
                >
                    🚪 Keluar / Logout
                </button>
            </div>
        </aside>

        <!-- ─── Main Wrapper ───────────────────────────────────────────────── -->
        <div class="flex-1 flex flex-col min-w-0 overflow-x-hidden">

            <!-- ─── Header ─────────────────────────────────────────────────── -->
            <header class="h-16 bg-cream-card border-b border-cream-border px-6 flex items-center justify-between sticky top-0 z-30 shadow-cream">
                <div class="flex items-center gap-4">
                    <!-- Hamburger button for mobile -->
                    <button
                        @click="toggleSidebar"
                        class="p-2 rounded-xl text-cream-muted hover:text-cream-text hover:bg-cream-bg lg:hidden transition-colors"
                    >
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <h2 class="text-lg font-display font-bold truncate">
                        {{ title }}
                    </h2>
                </div>

                <div class="flex items-center gap-3">
                    <span class="hidden sm:inline text-xs text-cream-muted font-medium bg-cream-bg px-3 py-1.5 rounded-full border border-cream-border">
                        📅 {{ new Date().toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) }}
                    </span>
                </div>
            </header>

            <!-- ─── Main Content ────────────────────────────────────────────── -->
            <main class="flex-1 p-6">
                <slot />
            </main>
        </div>
    </div>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

.flash-enter-active,
.flash-leave-active {
    transition: all 0.3s ease-out;
}
.flash-enter-from,
.flash-leave-to {
    opacity: 0;
    transform: translateY(-8px);
}
</style>
