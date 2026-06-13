<template>
    <div
        class="sticky top-4 z-50 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 transition-all duration-300"
    >
        <nav
            class="backdrop-blur-md bg-gradient-to-r from-emerald-900/10 to-emerald-950/20 border border-emerald-500/40 dark:border-emerald-800/40 rounded-3xl px-6 py-3.5 transition-all duration-300 hover:border-amber-500/30 dark:hover:border-amber-500/20"
            :class="
                isScrolled
                    ? 'shadow-lg shadow-emerald-900/10 dark:shadow-black/20 '
                    : 'shadow-none '
            "
        >
            <div class="flex justify-between items-center">
                <div
                    class="flex items-center gap-3 group cursor-pointer"
                    @click="goToHome"
                >
                    <NuxtImg
                        src="/logo.png"
                        alt="Logo Aplikasi"
                        class="w-12 sm:w-16 h-auto object-contain"
                    />
                    <span
                        class="text-emerald-900 dark:text-amber-400 font-semibold text-sm sm:text-base"
                        >Hafiz Indonesia Emas</span
                    >
                </div>

                <ul
                    class="hidden md:flex items-center gap-1 font-medium text-sm text-gray-700 dark:text-emerald-100"
                >
                    <NuxtLink
                        v-for="(item, index) in tm('nav.menu')"
                        :key="index"
                        :to="localePath(rt(item.path))"
                        class="relative px-4 py-2 rounded-full cursor-pointer transition-all duration-300 group block"
                        #default="{ isActive }"
                    >
                        <span
                            class="relative z-10 transition-colors duration-300"
                            :class="
                                isActive
                                    ? 'text-white dark:text-amber-400 font-semibold'
                                    : 'hover:text-emerald-600 dark:hover:text-amber-400'
                            "
                        >
                            {{ rt(item.name) }}
                        </span>
                        <span
                            class="absolute inset-0 rounded-full transition-all duration-300"
                            :class="
                                isActive
                                    ? 'bg-emerald-800/90 dark:bg-white/20 scale-100 opacity-100'
                                    : 'bg-emerald-50/60 dark:bg-emerald-900/20 scale-75 opacity-0 group-hover:scale-100 group-hover:opacity-100'
                            "
                        ></span>
                    </NuxtLink>
                </ul>

                <div class="hidden md:flex gap-2 items-center">
                    <USelect
                        v-model="currentLocale"
                        :items="languageOptions"
                        :icon="
                            currentLocale === 'id'
                                ? 'i-circle-flags-id'
                                : 'i-circle-flags-us'
                        "
                        :content="{
                            align: 'center',
                            side: 'bottom',
                            sideOffset: 8,
                        }"
                        class="w-24 cursor-pointer rounded-full uppercase font-semibold text-xs border border-emerald-100 dark:border-emerald-900 bg-white/80 dark:bg-gray-900/80 text-gray-700 dark:text-emerald-200 transition-all duration-300 hover:scale-105 hover:border-amber-500/40 dark:hover:border-amber-400/40 focus:outline-none"
                    />

                    <button
                        @click="toggleTheme"
                        class="w-9 h-9 flex items-center justify-center rounded-full border border-emerald-100 dark:border-emerald-900 bg-white/80 dark:bg-gray-900/80 text-gray-700 dark:text-emerald-200 transition-all duration-300 hover:scale-105 hover:border-amber-500/40 group focus:outline-none"
                        aria-label="Toggle Theme"
                    >
                        <UIcon
                            :name="themeIcon"
                            class="w-5 h-5 transition-transform duration-500 cursor-pointer ease-out group-click:rotate-180"
                            :class="isRotated ? 'rotate-180' : 'rotate-0'"
                        />
                    </button>
                </div>

                <div class="flex md:hidden items-center">
                    <button
                        @click="isMobileMenuOpen = !isMobileMenuOpen"
                        class="flex w-9 h-9 items-center justify-center rounded-full border border-emerald-100 dark:border-emerald-900 bg-white/80 dark:bg-gray-900/80 text-gray-700 dark:text-emerald-200 transition-all duration-300 hover:border-amber-500"
                        aria-label="Toggle Menu"
                    >
                        <UIcon
                            :name="
                                isMobileMenuOpen
                                    ? 'i-lucide-x'
                                    : 'i-lucide-menu'
                            "
                            class="w-5 h-5"
                        />
                    </button>
                </div>
            </div>
        </nav>

        <transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="transform scale-95 opacity-0 -translate-y-4"
            enter-to-class="transform scale-100 opacity-100 translate-y-0"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="transform scale-100 opacity-100 translate-y-0"
            leave-to-class="transform scale-95 opacity-0 -translate-y-4"
        >
            <div
                v-if="isMobileMenuOpen"
                class="md:hidden absolute mt-2 mx-auto w-full backdrop-blur-lg bg-white/95 dark:bg-gray-950/95 border border-emerald-500/20 dark:border-emerald-800/40 rounded-3xl p-5 shadow-xl"
            >
                <ul
                    class="flex flex-col gap-2 font-medium text-sm text-gray-700 dark:text-emerald-100 mb-4"
                >
                    <NuxtLink
                        v-for="(item, index) in tm('nav.menu')"
                        :key="index"
                        :to="localePath(rt(item.path))"
                        @click="isMobileMenuOpen = false"
                        class="relative px-4 py-3 rounded-2xl cursor-pointer transition-all duration-300 block"
                        #default="{ isActive }"
                    >
                        <span
                            class="relative z-10 transition-colors duration-300"
                            :class="
                                isActive
                                    ? 'text-emerald-700 dark:text-amber-400 font-semibold'
                                    : 'hover:text-emerald-600 dark:hover:text-amber-400'
                            "
                        >
                            {{ rt(item.name) }}
                        </span>
                        <span
                            class="absolute inset-0 rounded-2xl transition-all duration-300"
                            :class="
                                isActive
                                    ? 'bg-emerald-100/60 dark:bg-amber-500/10 opacity-100'
                                    : 'bg-transparent'
                            "
                        ></span>
                    </NuxtLink>
                </ul>

                <hr
                    class="border-emerald-500/10 dark:border-emerald-800/20 my-3"
                />

                <div class="flex flex-col gap-4 px-2 pt-2">
                    <div class="flex items-center justify-between">
                        <span
                            class="text-xs font-semibold text-gray-500 dark:text-emerald-300/60 uppercase"
                        >
                            Bahasa / Language
                        </span>
                        <USelect
                            v-model="currentLocale"
                            :items="languageOptions"
                            :icon="
                                currentLocale === 'id'
                                    ? 'i-circle-flags-id'
                                    : 'i-circle-flags-us'
                            "
                            class="w-24 cursor-pointer rounded-full uppercase font-semibold text-xs border border-emerald-100 dark:border-emerald-900 bg-white/80 dark:bg-gray-900/80 text-gray-700 dark:text-emerald-200 transition-all duration-300"
                        />
                    </div>

                    <div class="flex items-center justify-between">
                        <span
                            class="text-xs font-semibold text-gray-500 dark:text-emerald-300/60 uppercase"
                        >
                            Mode Tema
                        </span>
                        <button
                            @click="toggleTheme"
                            class="flex w-9 h-9 items-center justify-center rounded-full border border-emerald-100 dark:border-emerald-900 bg-white/80 dark:bg-gray-900/80 text-gray-700 dark:text-emerald-200 transition-all duration-300 focus:outline-none"
                        >
                            <UIcon
                                :name="themeIcon"
                                class="w-5 h-5 transition-transform duration-500 ease-out"
                                :class="isRotated ? 'rotate-180' : 'rotate-0'"
                            />
                        </button>
                    </div>
                </div>
            </div>
        </transition>
    </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from "vue";

const { locale, tm, rt } = useI18n();
const switchLocalePath = useSwitchLocalePath();
const router = useRouter();
const localePath = useLocalePath();

// Mobile Menu State
const isMobileMenuOpen = ref(false);

// Close menu on route change or home click
const goToHome = () => {
    isMobileMenuOpen.value = false;
    router.push(localePath("/"));
};

// Scroll State Logic
const isScrolled = ref(false);
const handleScroll = () => {
    isScrolled.value = window.scrollY > 10;
};

onMounted(() => {
    window.addEventListener("scroll", handleScroll);
    handleScroll();
});

onUnmounted(() => {
    window.removeEventListener("scroll", handleScroll);
});

// Format object { label, value } agar teks drop-down rapi tapi value tetap aman
const languageOptions = [
    { label: "ID", value: "id" },
    { label: "EN", value: "en" },
];

const currentLocale = computed({
    get() {
        return locale.value;
    },
    set(targetLocale: string) {
        const targetPath = switchLocalePath(targetLocale);
        if (targetPath) {
            router.push(targetPath);
            isMobileMenuOpen.value = false; // Tutup menu setelah ganti bahasa
        }
    },
});

// Theme Logic dengan `@nuxt/image` atau `@nuxtjs/color-mode`
const colorMode = useColorMode();
const isRotated = ref(false);

const toggleTheme = () => {
    // Picu animasi putar icon
    isRotated.value = !isRotated.value;

    // Swap tema (Jika dark -> ganti light, jika light/system -> ganti dark)
    if (colorMode.value === "dark") {
        colorMode.preference = "light";
    } else {
        colorMode.preference = "dark";
    }
};

const themeIcon = computed(() => {
    return colorMode.value === "dark" ? "i-lucide-moon" : "i-lucide-sun";
});
</script>
