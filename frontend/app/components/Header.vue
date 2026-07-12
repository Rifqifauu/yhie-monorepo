<template>
    <div
        class="sticky top-4 z-50 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 transition-all duration-300"
    >
        <nav
            class="backdrop-blur-md bg-gradient-to-r from-emerald-900/10 to-emerald-950/20 border border-emerald-500/40 dark:border-emerald-800/40 rounded-3xl px-6 py-3.5 transition-all duration-300 hover:border-amber-500/30"
            :class="
                isScrolled
                    ? 'shadow-lg shadow-emerald-900/10 dark:shadow-black/20'
                    : 'shadow-none'
            "
        >
            <div class="flex justify-between items-center">
                <div
                    class="flex items-center gap-3 group cursor-pointer"
                    @click="goToHome"
                >
                    <NuxtImg
                        src="/logo.png"
                        alt="Logo Hafiz Indonesia Emas"
                        class="w-12 sm:w-16 h-auto object-contain transition-transform group-hover:scale-105"
                    />
                    <span
                        class="text-emerald-900 dark:text-amber-400 font-bold text-sm sm:text-base tracking-tight"
                    >
                        Hafiz Indonesia Emas
                    </span>
                </div>

                <ul
                    class="hidden lg:flex items-center gap-1 font-medium text-sm text-gray-700 dark:text-emerald-100"
                >
                    <NuxtLink
                        v-for="(item, index) in tm('nav.menu')"
                        :key="index"
                        :to="localePath(rt(item.path))"
                        class="relative px-4 py-2 rounded-full cursor-pointer transition-all duration-300 group block"
                        #default="{ isActive }"
                    >
                        <span
                            class="relative z-10"
                            :class="
                                isActive
                                    ? 'text-white dark:text-emerald-950 font-bold'
                                    : 'group-hover:text-emerald-700 dark:group-hover:text-amber-300'
                            "
                        >
                            {{ rt(item.name) }}
                        </span>
                        <span
                            class="absolute inset-0 rounded-full transition-all duration-300"
                            :class="
                                isActive
                                    ? 'bg-emerald-800 dark:bg-amber-400 scale-100 opacity-100'
                                    : 'bg-emerald-50/60 dark:bg-emerald-900/40 scale-75 opacity-0 group-hover:scale-100 group-hover:opacity-100'
                            "
                        ></span>
                    </NuxtLink>
                </ul>

                <div class="hidden md:flex gap-3 items-center">
                    <NuxtLink
                        :to="localePath('/invoice/search')"
                        class="flex items-center gap-2 px-4 py-2 rounded-full border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 hover:border-amber-500 dark:hover:border-amber-400 hover:text-amber-600 dark:hover:text-amber-400 transition-all duration-300 text-sm font-semibold"
                    >
                        <UIcon name="i-lucide-receipt-text" class="w-4 h-4" />
                        <span>{{ t("invoice.searchCta") }}</span>
                    </NuxtLink>

                    <NuxtLink
                        v-if="isAdmin"
                        :to="localePath('/admin')"
                        class="flex items-center gap-2 px-4 py-2 rounded-full bg-emerald-800 dark:bg-amber-400 text-white dark:text-emerald-950 transition-all duration-300 hover:scale-105 hover:shadow-md focus:ring-2 focus:ring-amber-500"
                    >
                        <UIcon
                            name="i-lucide-layout-dashboard"
                            class="w-4 h-4"
                        />
                        <span
                            class="text-[10px] font-black uppercase tracking-widest"
                            >Admin</span
                        >
                    </NuxtLink>

                    <USelect
                        v-model="currentLocale"
                        :items="languageOptions"
                        :icon="
                            currentLocale === 'id'
                                ? 'i-circle-flags-id'
                                : 'i-circle-flags-us'
                        "
                        class="w-24 cursor-pointer rounded-full uppercase font-bold text-xs border border-emerald-100 dark:border-emerald-900 bg-white/60 dark:bg-gray-900/60 hover:border-amber-500/40 transition-all"
                    />

                    <button
                        @click="toggleTheme"
                        class="w-10 h-10 flex items-center justify-center rounded-full border border-emerald-100 dark:border-emerald-900 bg-white/60 dark:bg-gray-900/60 transition-all hover:scale-110 active:scale-95 group"
                        aria-label="Toggle Theme"
                    >
                        <UIcon
                            :name="themeIcon"
                            class="w-5 h-5 transition-transform duration-500"
                            :class="isRotated ? 'rotate-180' : 'rotate-0'"
                        />
                    </button>
                </div>

                <div class="flex md:hidden items-center">
                    <button
                        @click="isMobileMenuOpen = !isMobileMenuOpen"
                        :aria-expanded="isMobileMenuOpen"
                        class="w-10 h-10 flex items-center justify-center rounded-full border border-emerald-200 dark:border-emerald-800 bg-white/80 dark:bg-gray-900/80 text-emerald-900 dark:text-amber-400 transition-all"
                        aria-label="Toggle Menu"
                    >
                        <UIcon
                            :name="
                                isMobileMenuOpen
                                    ? 'i-lucide-x'
                                    : 'i-lucide-menu'
                            "
                            class="w-6 h-6"
                        />
                    </button>
                </div>
            </div>
        </nav>

        <transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="transform -translate-y-4 opacity-0 scale-95"
            enter-to-class="transform translate-y-0 opacity-100 scale-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="transform translate-y-0 opacity-100 scale-100"
            leave-to-class="transform -translate-y-4 opacity-0 scale-95"
        >
            <div
                v-if="isMobileMenuOpen"
                class="md:hidden absolute mt-3 inset-x-4 backdrop-blur-xl bg-white/95 dark:bg-gray-950/95 border border-emerald-500/20 rounded-3xl p-5 shadow-2xl overflow-hidden"
            >
                <div class="flex flex-col gap-1.5 mb-4">
                    <NuxtLink
                        v-for="(item, index) in tm('nav.menu')"
                        :key="index"
                        :to="localePath(rt(item.path))"
                        @click="isMobileMenuOpen = false"
                        class="flex items-center px-5 py-3.5 rounded-2xl transition-all"
                        #default="{ isActive }"
                        :class="
                            isActive
                                ? 'bg-emerald-50 dark:bg-emerald-900/30'
                                : 'active:bg-gray-100 dark:active:bg-gray-900'
                        "
                    >
                        <span
                            :class="
                                isActive
                                    ? 'text-emerald-700 dark:text-amber-400 font-bold'
                                    : 'text-gray-600 dark:text-emerald-100/70'
                            "
                        >
                            {{ rt(item.name) }}
                        </span>
                    </NuxtLink>
                </div>

                <div v-if="isAdmin" class="mb-4">
                    <NuxtLink
                        :to="localePath('/admin')"
                        @click="isMobileMenuOpen = false"
                        class="flex items-center justify-center gap-3 w-full py-4 rounded-2xl bg-emerald-800 dark:bg-amber-400 text-white dark:text-emerald-950 font-bold shadow-lg shadow-emerald-900/10"
                    >
                        <UIcon
                            name="i-lucide-layout-dashboard"
                            class="w-5 h-5"
                        />
                        <span>Dashboard Admin</span>
                    </NuxtLink>
                </div>

                <div class="mb-4">
                    <NuxtLink
                        :to="localePath('/invoice/search')"
                        @click="isMobileMenuOpen = false"
                        class="flex items-center justify-center gap-3 w-full py-3.5 rounded-2xl border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 font-semibold"
                    >
                        <UIcon name="i-lucide-receipt-text" class="w-4.5 h-4.5" />
                        <span>{{ t("invoice.searchCta") }}</span>
                    </NuxtLink>
                </div>

                <hr class="border-emerald-500/10 mb-4" />

                <div class="space-y-4">
                    <div class="flex items-center justify-between px-2">
                        <span
                            class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter"
                            >{{
                                locale === "en" ? "Choose Language" : "Pilih Bahasa"
                            }}</span
                        >
                        <USelect
                            v-model="currentLocale"
                            :items="languageOptions"
                            class="w-24 font-bold"
                        />
                    </div>
                    <div class="flex items-center justify-between px-2">
                        <span
                            class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter"
                            >{{
                                locale === "en" ? "Display Mode" : "Mode Tampilan"
                            }}</span
                        >
                        <button
                            @click="toggleTheme"
                            class="flex w-10 h-10 items-center justify-center rounded-full bg-emerald-50 dark:bg-gray-900 border border-emerald-100 dark:border-emerald-800"
                        >
                            <UIcon :name="themeIcon" class="w-5 h-5" />
                        </button>
                    </div>
                </div>
            </div>
        </transition>
    </div>
</template>

<script setup lang="ts">
const { t, locale, tm, rt } = useI18n();
const switchLocalePath = useSwitchLocalePath();
const router = useRouter();
const localePath = useLocalePath();
const route = useRoute();
interface AuthUser {
    id: number;
    name: string;
    email: string;
    role: string;
}

const { user, isAuthenticated } = useSanctumAuth<AuthUser>();
const isAdmin = computed(() => isAuthenticated.value && user.value?.role === "admin");

const isMobileMenuOpen = ref(false);
const isScrolled = ref(false);
const isRotated = ref(false);
const colorMode = useColorMode();

// Helper functions
const goToHome = () => {
    isMobileMenuOpen.value = false;
    router.push(localePath("/"));
};

let ticking = false;

const handleScroll = () => {
    if (ticking) return;
    window.requestAnimationFrame(() => {
        isScrolled.value = window.scrollY > 20;
        ticking = false;
    });
    ticking = true;
};

const toggleTheme = () => {
    isRotated.value = !isRotated.value;
    colorMode.preference = colorMode.value === "dark" ? "light" : "dark";
};

// Observers & Lifecycle
onMounted(() => {
    window.addEventListener("scroll", handleScroll, { passive: true });
    handleScroll();
});

onUnmounted(() => {
    window.removeEventListener("scroll", handleScroll);
});

// Watch route to auto-close menu
watch(
    () => route.fullPath,
    () => {
        isMobileMenuOpen.value = false;
    },
);

const languageOptions = [
    { label: "ID", value: "id" },
    { label: "EN", value: "en" },
];

const currentLocale = computed({
    get: () => locale.value,
    set: (targetLocale: string) => {
        const targetPath = switchLocalePath(targetLocale);
        if (targetPath) router.push(targetPath);
    },
});

const themeIcon = computed(() =>
    colorMode.value === "dark" ? "i-lucide-moon" : "i-lucide-sun",
);
</script>
