<script setup>
import { ref, computed, watch } from 'vue'
import { Link, usePage, router } from '@inertiajs/vue3'

// ─── Props ─────────────────────────────────────────────────────────────────
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

// ─── Mobile Menu ───────────────────────────────────────────────────────────
const isMobileMenuOpen = ref(false)
const isUserMenuOpen = ref(false)

const toggleMobileMenu = () => { isMobileMenuOpen.value = !isMobileMenuOpen.value }
const toggleUserMenu = () => { isUserMenuOpen.value = !isUserMenuOpen.value }
const closeMenus = () => {
    isMobileMenuOpen.value = false
    isUserMenuOpen.value = false
}

// ─── Navigation ────────────────────────────────────────────────────────────
const isAdmin = computed(() => user.value?.role === 'ADMIN')

// Menu untuk USER biasa
const userNavLinks = [
    { name: 'Beranda', routeName: 'home' },
    { name: 'Lapangan', routeName: 'lapangan.index' },
]

// Menu untuk ADMIN
const adminNavLinks = [
    { name: 'Dashboard', routeName: 'admin.dashboard' },
    { name: 'Kelola Lapangan', routeName: 'admin.lapangan.index' },
    { name: 'Kelola Reservasi', routeName: 'admin.reservasi.index' },
    { name: 'Kelola User', routeName: 'admin.users.index' },
]

const navLinks = computed(() => isAdmin.value ? adminNavLinks : userNavLinks)

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
    closeMenus()
}

import { useToast } from 'vue-toastification'

const toast = useToast()

watch(flash, (newFlash) => {
    if (newFlash.success) toast.success(newFlash.success)
    if (newFlash.error) toast.error(newFlash.error)
    if (newFlash.warning) toast.warning(newFlash.warning)
    if (newFlash.info) toast.info(newFlash.info)
}, { deep: true, immediate: true })
</script>

<template>
    <div class="min-h-screen bg-cream-bg flex flex-col" @click="isUserMenuOpen && closeMenus()">

        <!-- ─── Navbar ───────────────────────────────────────────────────── -->
        <nav class="sticky top-0 z-50 bg-cream-card/95 backdrop-blur-sm border-b border-cream-border shadow-cream">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">

                    <!-- Logo -->
                    <Link
                        :href="route('home')"
                        class="flex items-center gap-2.5 group"
                        @click="closeMenus"
                    >
                        <div class="w-9 h-9 rounded-xl bg-cream-accent flex items-center justify-center shadow-cream group-hover:scale-105 transition-transform duration-200">
                            <span class="text-lg">🏸</span>
                        </div>
                        <div class="hidden sm:block">
                            <p class="font-display font-bold text-cream-text text-sm leading-tight">Badminton</p>
                            <p class="font-display font-bold text-cream-accent text-xs leading-tight tracking-wide">RESERVASI</p>
                        </div>
                    </Link>

                    <!-- Desktop Navigation -->
                    <div class="hidden md:flex items-center gap-1">
                        <Link
                            v-for="link in navLinks"
                            :key="link.routeName"
                            :href="route(link.routeName)"
                            class="relative px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200"
                            :class="isActive(link.routeName)
                                ? 'text-cream-accent bg-cream-accent/10'
                                : 'text-cream-muted hover:text-cream-text hover:bg-cream-bg'"
                        >
                            {{ link.name }}
                            <!-- Active indicator -->
                            <span
                                v-if="isActive(link.routeName)"
                                class="absolute bottom-1 left-1/2 -translate-x-1/2 w-1 h-1 rounded-full bg-cream-accent"
                            />
                        </Link>

                        <!-- Riwayat Reservasi — hanya untuk USER biasa -->
                        <Link
                            v-if="user && !isAdmin"
                            :href="route('dashboard')"
                            class="relative px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200"
                            :class="isActive('dashboard')
                                ? 'text-cream-accent bg-cream-accent/10'
                                : 'text-cream-muted hover:text-cream-text hover:bg-cream-bg'"
                        >
                            Riwayat
                        </Link>
                    </div>

                    <!-- Auth Section -->
                    <div class="flex items-center gap-3">

                        <!-- Guest -->
                        <template v-if="!user">
                            <Link
                                :href="route('login')"
                                class="hidden sm:inline-flex btn-secondary text-xs px-4 py-2"
                            >
                                Masuk
                            </Link>
                            <Link
                                :href="route('register')"
                                class="btn-primary text-xs px-4 py-2"
                            >
                                Daftar
                            </Link>
                        </template>

                        <!-- Authenticated User -->
                        <template v-else>
                            <div class="relative">
                                <button
                                    @click.stop="toggleUserMenu"
                                    class="flex items-center gap-2.5 px-3 py-1.5 rounded-xl border border-cream-border hover:border-cream-accent bg-cream-bg hover:bg-cream-card transition-all duration-200 group"
                                >
                                    <!-- Avatar -->
                                    <div class="w-7 h-7 rounded-lg bg-cream-accent flex items-center justify-center text-white text-xs font-bold">
                                        {{ user.name.charAt(0).toUpperCase() }}
                                    </div>
                                    <span class="hidden sm:block text-sm font-medium text-cream-text max-w-28 truncate">
                                        {{ user.name }}
                                    </span>
                                    <!-- Role badge -->
                                    <span
                                        v-if="user.role === 'ADMIN'"
                                        class="hidden sm:inline-flex badge bg-cream-accent/15 text-cream-accent text-[10px]"
                                    >
                                        Admin
                                    </span>
                                    <!-- Chevron -->
                                    <svg
                                        class="w-3.5 h-3.5 text-cream-muted transition-transform duration-200"
                                        :class="isUserMenuOpen ? 'rotate-180' : ''"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>

                                <!-- Dropdown -->
                                <Transition name="dropdown">
                                    <div
                                        v-if="isUserMenuOpen"
                                        class="absolute right-0 mt-2 w-52 card-lg py-1.5 shadow-cream-md"
                                    >
                                        <div class="px-3 py-2 border-b border-cream-border mb-1">
                                            <p class="text-sm font-semibold text-cream-text truncate">{{ user.name }}</p>
                                            <p class="text-xs text-cream-muted truncate">{{ user.email }}</p>
                                        </div>
                                        <Link
                                            :href="route('profile.edit')"
                                            class="flex items-center gap-2.5 px-3 py-2 text-sm text-cream-text hover:bg-cream-bg transition-colors"
                                            @click="closeMenus"
                                        >
                                            <svg class="w-4 h-4 text-cream-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                            </svg>
                                            Edit Profil
                                        </Link>
                                        <!-- Riwayat — hanya untuk User biasa -->
                                        <Link
                                            v-if="!isAdmin"
                                            :href="route('dashboard')"
                                            class="flex items-center gap-2.5 px-3 py-2 text-sm text-cream-text hover:bg-cream-bg transition-colors"
                                            @click="closeMenus"
                                        >
                                            <svg class="w-4 h-4 text-cream-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                            </svg>
                                            Riwayat Reservasi
                                        </Link>
                                        <!-- Dashboard Admin — hanya untuk Admin -->
                                        <Link
                                            v-if="isAdmin"
                                            :href="route('admin.dashboard')"
                                            class="flex items-center gap-2.5 px-3 py-2 text-sm text-cream-text hover:bg-cream-bg transition-colors"
                                            @click="closeMenus"
                                        >
                                            <svg class="w-4 h-4 text-cream-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                                            </svg>
                                            Panel Admin
                                        </Link>
                                        <div class="border-t border-cream-border mt-1 pt-1">
                                            <button
                                                @click="logout"
                                                class="flex items-center gap-2.5 w-full px-3 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors"
                                            >
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                                                </svg>
                                                Keluar
                                            </button>
                                        </div>
                                    </div>
                                </Transition>
                            </div>
                        </template>

                        <!-- Mobile Hamburger -->
                        <button
                            @click.stop="toggleMobileMenu"
                            class="md:hidden p-2 rounded-lg text-cream-muted hover:text-cream-text hover:bg-cream-bg transition-colors"
                        >
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path v-if="!isMobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                                <path v-else stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile Menu -->
            <Transition name="slide-down">
                <div
                    v-if="isMobileMenuOpen"
                    class="md:hidden border-t border-cream-border bg-cream-card px-4 py-3 space-y-1"
                >
                    <Link
                        v-for="link in navLinks"
                        :key="link.routeName"
                        :href="route(link.routeName)"
                        class="flex items-center px-3 py-2.5 rounded-xl text-sm font-medium transition-colors"
                        :class="isActive(link.routeName)
                            ? 'text-cream-accent bg-cream-accent/10'
                            : 'text-cream-text hover:bg-cream-bg'"
                        @click="closeMenus"
                    >
                        {{ link.name }}
                    </Link>
                    <!-- Riwayat — hanya untuk User biasa -->
                    <Link
                        v-if="user && !isAdmin"
                        :href="route('dashboard')"
                        class="flex items-center px-3 py-2.5 rounded-xl text-sm font-medium text-cream-text hover:bg-cream-bg transition-colors"
                        @click="closeMenus"
                    >
                        Riwayat Reservasi
                    </Link>
                    <div v-if="!user" class="flex gap-2 pt-2 border-t border-cream-border mt-2">
                        <Link :href="route('login')" class="flex-1 btn-secondary text-sm text-center" @click="closeMenus">Masuk</Link>
                        <Link :href="route('register')" class="flex-1 btn-primary text-sm text-center" @click="closeMenus">Daftar</Link>
                    </div>
                    <button v-else @click="logout" class="w-full text-left px-3 py-2.5 rounded-xl text-sm font-medium text-red-600 hover:bg-red-50 transition-colors mt-1">
                        Keluar
                    </button>
                </div>
            </Transition>
        </nav>

        <!-- ─── Page Content ─────────────────────────────────────────────── -->
        <main class="flex-1">
            <slot />
        </main>

        <!-- ─── Footer ───────────────────────────────────────────────────── -->
        <footer class="bg-cream-card border-t border-cream-border mt-auto">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-cream-accent flex items-center justify-center">
                            <span class="text-base">🏸</span>
                        </div>
                        <div>
                            <p class="font-display font-bold text-cream-text text-sm">Badminton Reservasi</p>
                            <p class="text-xs text-cream-muted">Reservasi lapangan mudah & cepat</p>
                        </div>
                    </div>
                    <p class="text-xs text-cream-muted">
                        &copy; {{ new Date().getFullYear() }} Badminton Reservasi. Hak Cipta Dilindungi.
                    </p>
                </div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
/* Flash transition */
.flash-enter-active,
.flash-leave-active {
    transition: all 0.3s ease;
}
.flash-enter-from,
.flash-leave-to {
    opacity: 0;
    transform: translateY(-8px);
}

/* Dropdown transition */
.dropdown-enter-active,
.dropdown-leave-active {
    transition: all 0.15s ease;
    transform-origin: top right;
}
.dropdown-enter-from,
.dropdown-leave-to {
    opacity: 0;
    transform: scale(0.95) translateY(-4px);
}

/* Slide down (mobile menu) */
.slide-down-enter-active,
.slide-down-leave-active {
    transition: all 0.25s ease;
}
.slide-down-enter-from,
.slide-down-leave-to {
    opacity: 0;
    transform: translateY(-12px);
}
</style>
