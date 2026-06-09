<template>
    <div class="sticky top-4 z-50 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 transition-all duration-300">
        <nav
            class="backdrop-blur-md bg-gradient-to-r from-emerald-900/10 to-emerald-950/20 border border-emerald-500/40 dark:border-emerald-800/40 rounded-3xl px-6 py-3.5 transition-all duration-300 hover:border-amber-500/30 dark:hover:border-amber-500/20"
            :class="isScrolled ? 'shadow-lg shadow-emerald-900/10 dark:shadow-black/20' : 'shadow-none'"
        >
            <div class="flex justify-between items-center">
                <!-- Logo -->
                <div class="flex items-center gap-3 group cursor-pointer" @click="goToHome">
                    <NuxtImg src="/logo.png" alt="Logo YHIE" class="w-12 sm:w-16 h-auto object-contain" />
                    <span class="text-emerald-900 dark:text-amber-400 font-semibold text-sm sm:text-base">
                        Hafiz Indonesia Emas
                    </span>
                </div>

                <!-- Desktop nav -->
                <ul class="hidden md:flex items-center gap-1 font-medium text-sm text-gray-700 dark:text-emerald-100">
                    <NuxtLink
                        v-for="(item, index) in menuItems"
                        :key="index"
                        :to="localePath(rt(item.path))"
                        class="relative px-4 py-2 rounded-full cursor-pointer transition-all duration-300 group block"
                        v-slot="{ isActive }"
                    >
                        <span
                            class="relative z-10 transition-colors duration-300"
                            :class="isActive ? 'text-white dark:text-amber-400 font-semibold' : 'hover:text-emerald-600 dark:hover:text-amber-400'"
                        >
                            {{ rt(item.name) }}
                        </span>
                        <span
                            class="absolute inset-0 rounded-full transition-all duration-300"
                            :class="isActive ? 'bg-emerald-800/90 dark:bg-white/20 scale-100 opacity-100' : 'bg-emerald-50/60 dark:bg-emerald-900/20 scale-75 opacity-0 group-hover:scale-100 group-hover:opacity-100'"
                        ></span>
                    </NuxtLink>
                </ul>

                <!-- Desktop controls -->
                <div class="hidden md:flex gap-2 items-center">
                    <USelect
                        v-model="currentLocale"
                        :items="languageOptions"
                        :icon="currentLocale === 'id' ? 'i-circle-flags-id' : 'i-circle-flags-us'"
                        :content="{ align: 'center', side: 'bottom', sideOffset: 8 }"
                        class="w-24 cursor-pointer rounded-full uppercase font-semibold text-xs border border-emerald-100 dark:border-emerald-900 bg-white/80 dark:bg-gray-900/80 text-gray-700 dark:text-emerald-200 transition-all duration-300 hover:scale-105 hover:border-amber-500/40 dark:hover:border-amber-400/40 focus:outline-none"
                    />
                    <button
                        @click="toggleTheme"
                        class="w-9 h-9 flex items-center justify-center rounded-full border border-emerald-100 dark:border-emerald-900 bg-white/80 dark:bg-gray-900/80 text-gray-700 dark:text-emerald-200 transition-all duration-300 hover:scale-105 hover:border-amber-500/40 group focus:outline-none"
                        aria-label="Toggle Theme"
                    >
                        <UIcon
                            :name="themeIcon"
                            class="w-5 h-5 transition-transform duration-500 ease-out"
                            :class="isRotated ? 'rotate-180' : 'rotate-0'"
                        />
                    </button>
                </div>

                <!-- Mobile hamburger -->
                <div class="flex md:hidden items-center">
                    <button
                        @click="isMobileMenuOpen = !isMobileMenuOpen"
                        class="flex w-9 h-9 items-center justify-center rounded-full border border-emerald-100 dark:border-emerald-900 bg-white/80 dark:bg-gray-900/80 text-gray-700 dark:text-emerald-200 transition-all duration-300 hover:border-amber-500"
                        aria-label="Toggle Menu"
                    >
                        <UIcon :name="isMobileMenuOpen ? 'i-lucide-x' : 'i-lucide-menu'" class="w-5 h-5" />
                    </button>
                </div>
            </div>
        </nav>

        <!-- Mobile menu dropdown -->
        <transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0 -translate-y-3 scale-95"
            enter-to-class="opacity-100 translate-y-0 scale-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100 translate-y-0 scale-100"
            leave-to-class="opacity-0 -translate-y-3 scale-95"
        >
            <div
                v-if="isMobileMenuOpen"
                class="md:hidden absolute left-0 right-0 mt-2 backdrop-blur-xl bg-white/98 dark:bg-gray-950/98 border border-emerald-500/20 dark:border-emerald-800/40 rounded-3xl overflow-hidden shadow-2xl shadow-emerald-900/10 dark:shadow-black/40"
            >
                <!-- Nav links -->
                <ul class="flex flex-col p-3">
                    <NuxtLink
                        v-for="(item, index) in menuItems"
                        :key="index"
                        :to="localePath(rt(item.path))"
                        @click="isMobileMenuOpen = false"
                        class="flex items-center gap-3 px-4 py-3 rounded-2xl transition-all duration-200 relative"
                        v-slot="{ isActive }"
                    >
                        <span
                            class="w-1.5 h-1.5 rounded-full shrink-0 transition-colors duration-200"
                            :class="isActive ? 'bg-amber-500' : 'bg-emerald-300 dark:bg-emerald-700'"
                        ></span>
                        <span
                            class="text-sm font-medium transition-colors duration-200"
                            :class="isActive ? 'text-emerald-700 dark:text-amber-400 font-semibold' : 'text-slate-700 dark:text-emerald-100'"
                        >
                            {{ rt(item.name) }}
                        </span>
                        <span
                            class="absolute inset-0 rounded-2xl"
                            :class="isActive ? 'bg-emerald-50 dark:bg-amber-500/10' : ''"
                        ></span>
                    </NuxtLink>
                </ul>

                <!-- Divider -->
                <div class="h-px bg-emerald-100 dark:bg-emerald-900/60 mx-4"></div>

                <!-- Controls row -->
                <div class="flex items-center justify-between px-6 py-4">
                    <div class="flex items-center gap-2">
                        <UIcon name="i-lucide-languages" class="w-4 h-4 text-slate-400 dark:text-emerald-600" />
                        <span class="text-xs font-semibold text-slate-400 dark:text-emerald-600 uppercase tracking-wider">
                            Bahasa
                        </span>
                    </div>
                    <div class="flex items-center gap-2">
                        <USelect
                            v-model="currentLocale"
                            :items="languageOptions"
                            :icon="currentLocale === 'id' ? 'i-circle-flags-id' : 'i-circle-flags-us'"
                            class="w-24 rounded-full text-xs font-semibold border border-emerald-100 dark:border-emerald-900 bg-white/80 dark:bg-gray-900/80"
                        />
                        <button
                            @click="toggleTheme"
                            class="w-9 h-9 flex items-center justify-center rounded-full border border-emerald-100 dark:border-emerald-900 bg-white/80 dark:bg-gray-900/80 text-gray-700 dark:text-emerald-200 transition-all duration-300 focus:outline-none"
                        >
                            <UIcon :name="themeIcon" class="w-4 h-4" :class="isRotated ? 'rotate-180' : 'rotate-0'" />
                        </button>
                    </div>
                </div>

                <!-- Daftar CTA -->
                <div class="px-4 pb-4">
                    <NuxtLink
                        :to="localePath('/register')"
                        @click="isMobileMenuOpen = false"
                        class="flex items-center justify-center gap-2 w-full py-3 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold transition-colors duration-200"
                    >
                        <UIcon name="i-lucide-user-plus" class="w-4 h-4" />
                        Daftar Sekarang
                    </NuxtLink>
                </div>
            </div>
        </transition>
    </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted, watch } from "vue"

interface MenuItem {
    name: string
    path: string
}

const { locale, tm, rt } = useI18n()
const switchLocalePath = useSwitchLocalePath()
const router = useRouter()
const route = useRoute()
const localePath = useLocalePath()

const menuItems = computed<MenuItem[]>(() => {
    return (tm("nav.menu") || []) as unknown as MenuItem[]
})

const isMobileMenuOpen = ref(false)

// Tutup menu saat navigasi
watch(() => route.path, () => {
    isMobileMenuOpen.value = false
})

const goToHome = () => {
    isMobileMenuOpen.value = false
    router.push(localePath("/"))
}

const isScrolled = ref(false)
const handleScroll = () => {
    isScrolled.value = window.scrollY > 10
}

onMounted(() => {
    window.addEventListener("scroll", handleScroll)
    handleScroll()
})
onUnmounted(() => {
    window.removeEventListener("scroll", handleScroll)
})

const languageOptions = [
    { label: "ID", value: "id" },
    { label: "EN", value: "en" },
]

const currentLocale = computed({
    get() { return locale.value },
    set(targetLocale: string) {
        const targetPath = switchLocalePath(targetLocale as "id" | "en")
        if (targetPath) {
            router.push(targetPath)
            isMobileMenuOpen.value = false
        }
    },
})

const colorMode = useColorMode()
const isRotated = ref(false)

const toggleTheme = () => {
    isRotated.value = !isRotated.value
    colorMode.preference = colorMode.value === "dark" ? "light" : "dark"
}

const themeIcon = computed(() =>
    colorMode.value === "dark" ? "i-lucide-moon" : "i-lucide-sun"
)
</script>