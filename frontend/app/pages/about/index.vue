<template>
    <div class="bg-slate-50 dark:bg-emerald-950 text-slate-900 dark:text-white min-h-screen">
        <!-- Hero Section -->
        <section class="relative overflow-hidden bg-gradient-to-br from-emerald-50 via-white to-amber-50 dark:from-emerald-950 dark:via-emerald-950 dark:to-amber-950/40 border-b border-emerald-100/70 dark:border-emerald-800/60 pt-20 pb-24 lg:pt-28 lg:pb-32">
            <!-- Background mosque shadow -->
            <img
                src="/shadow-mosque.webp"
                class="absolute bottom-0 left-1/2 -translate-x-1/2 w-auto h-full max-h-[85%] object-contain object-bottom pointer-events-none opacity-20 dark:opacity-35 dark:invert dark:brightness-150 transition-all duration-300"
                alt=""
            />
            <!-- Overlay -->
            <div
                class="absolute inset-0 bg-gradient-to-br from-emerald-50/75 via-white/65 to-amber-50/55 dark:from-emerald-950/70 dark:via-emerald-950/65 dark:to-amber-950/40 transition-colors duration-300"
                aria-hidden="true"
            ></div>
            <div class="absolute -top-24 -right-24 w-80 h-80 bg-amber-400/20 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-28 -left-28 w-96 h-96 bg-emerald-400/20 rounded-full blur-3xl"></div>

            <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center z-10" data-aos="fade-up">
                <span class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-[0.3em] text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/60 px-3.5 py-1.5 rounded-full border border-amber-200/50 dark:border-amber-900/40">
                    <UIcon name="i-lucide-info" class="w-4 h-4" />
                    {{ t('about.title') }}
                </span>
                <h1 class="mt-6 text-3xl md:text-5xl font-serif font-extrabold tracking-tight text-emerald-950 dark:text-emerald-50 leading-[1.15]">
                    {{ t('about.slogan') }}
                </h1>
                <p class="mt-6 text-lg md:text-xl text-slate-600 dark:text-emerald-100/80 leading-relaxed font-light">
                    {{ t('about.description') }}
                </p>
            </div>
        </section>

        <!-- Main Content: History, Vision & Mission -->
        <section class="py-16 lg:py-24 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16">
                <!-- History (Left Column) -->
                <div class="lg:col-span-7 flex flex-col justify-center" data-aos="fade-right">
                    <h2 class="text-2xl md:text-3xl font-serif font-extrabold text-emerald-950 dark:text-emerald-50 mb-6">
                        {{ t('about.history.title') }}
                    </h2>
                    
                    <div class="prose dark:prose-invert text-slate-600 dark:text-emerald-100/80 leading-relaxed space-y-4">
                        <p v-if="isPending" class="space-y-2">
                            <USkeleton class="h-4 w-full" />
                            <USkeleton class="h-4 w-5/6" />
                            <USkeleton class="h-4 w-4/5" />
                        </p>
                        <p v-else class="text-base">
                            {{ aboutHistory }}
                        </p>
                    </div>

                    <!-- Highlight Quote -->
                    <div class="mt-8 p-6 bg-gradient-to-r from-emerald-50 to-amber-50/50 dark:from-emerald-900/20 dark:to-emerald-950/40 rounded-2xl border-l-4 border-amber-500 shadow-sm">
                        <p class="font-serif italic text-emerald-900 dark:text-emerald-100/90 text-base">
                            {{ t('about.highlightQuote') }}
                        </p>
                    </div>
                </div>

                <!-- Vision & Mission Cards (Right Column) -->
                <div class="lg:col-span-5 space-y-8" data-aos="fade-left" data-aos-delay="100">
                    <!-- Vision Card -->
                    <div class="p-6 sm:p-8 bg-white dark:bg-emerald-900/10 rounded-2xl border border-emerald-100/60 dark:border-emerald-800/40 shadow-sm">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-12 h-12 rounded-xl bg-amber-100 dark:bg-amber-900/40 flex items-center justify-center">
                                <UIcon name="i-lucide-eye" class="w-6 h-6 text-amber-600 dark:text-amber-400" />
                            </div>
                            <h3 class="text-xl font-serif font-extrabold text-emerald-950 dark:text-emerald-50">
                                {{ t('about.vision.title') }}
                            </h3>
                        </div>
                        <div v-if="isPending" class="space-y-2">
                            <USkeleton class="h-4 w-full" />
                            <USkeleton class="h-4 w-5/6" />
                        </div>
                        <p v-else class="text-slate-600 dark:text-emerald-100/80 leading-relaxed text-sm">
                            {{ t('about.vision.description') }}
                        </p>
                    </div>

                    <!-- Mission Card -->
                    <div class="p-6 sm:p-8 bg-white dark:bg-emerald-900/10 rounded-2xl border border-emerald-100/60 dark:border-emerald-800/40 shadow-sm">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-12 h-12 rounded-xl bg-emerald-100 dark:bg-emerald-900/60 flex items-center justify-center">
                                <UIcon name="i-lucide-target" class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                            </div>
                            <h3 class="text-xl font-serif font-extrabold text-emerald-950 dark:text-emerald-50">
                                {{ t('about.mission.title') }}
                            </h3>
                        </div>
                        <div v-if="isPending" class="space-y-2">
                            <USkeleton class="h-4 w-full" />
                            <USkeleton class="h-4 w-5/6" />
                            <USkeleton class="h-4 w-4/5" />
                        </div>
                        <ul v-else class="space-y-3">
                            <li 
                                v-for="(missionLine, idx) in   missionLines" 
                                :key="idx"
                                class="flex gap-2.5 text-slate-600 dark:text-emerald-100/80 text-sm leading-relaxed"
                            >
                                <span class="text-emerald-500 dark:text-amber-400 font-bold">•</span>
                                <span>{{ cleanMissionLine(missionLine) }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Core Values section (Minimalist Grid) -->
            <div class="mt-20 lg:mt-32 pt-16 border-t border-slate-200/60 dark:border-emerald-800/40">
                <div class="text-center max-w-3xl mx-auto mb-16" data-aos="fade-up">
                    <span class="text-xs font-bold uppercase tracking-[0.2em] text-emerald-600 dark:text-amber-400">
                        {{ t('about.core') }}
                    </span>
                    <h2 class="mt-3 text-2xl md:text-4xl font-serif font-extrabold text-emerald-950 dark:text-emerald-50">
                        {{ t('about.values') }}
                    </h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div 
                        v-for="(val, index) in coreValues" 
                        :key="index"
                        class="p-6 bg-white dark:bg-emerald-900/10 border border-emerald-100/40 dark:border-emerald-800/20 rounded-2xl shadow-sm hover:shadow-md hover:border-emerald-200 dark:hover:border-emerald-700/60 transition-all duration-300"
                        data-aos="fade-up"
                        :data-aos-delay="index * 100"
                    >
                        <div class="w-10 h-10 rounded-lg bg-emerald-50 dark:bg-emerald-950 flex items-center justify-center mb-4">
                            <UIcon :name="val.icon" class="w-5 h-5 text-emerald-600 dark:text-amber-400" />
                        </div>
                        <h4 class="text-base font-serif font-extrabold text-emerald-950 dark:text-emerald-50 mb-2">
                            {{ t(val.titleKey) }}
                        </h4>
                        <p class="text-slate-500 dark:text-emerald-100/60 text-xs sm:text-sm leading-relaxed">
                            {{ t(val.descKey) }}
                        </p>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<script setup lang="ts">
import { useSettings } from "~/composables/useSettings";

const { t, locale } = useI18n();

const {
    aboutHistory,
    aboutVision,
    aboutMission,
    isPending,
} = useSettings();

// Parse mission text into array lines
const missionLines = computed(() => {
    if (!aboutMission.value) return [];
    return aboutMission.value
        .split("\n")
        .map(line => line.trim())
        .filter(line => line.length > 0);
});

const cleanMissionLine = (line: string) => {
    // Strip bullet symbol if present to prevent double bulleting
    const trimmed = line.trim();
    return trimmed.startsWith("•") || trimmed.startsWith("-") || trimmed.startsWith("*") 
        ? trimmed.substring(1).trim() 
        : trimmed;
};

const coreValues = [
    {
        icon: "i-lucide-shield",
        titleKey: t("about.coreValues.integrity.title"),
        descKey: t("about.coreValues.integrity.desc"),
    },
    {
        icon: "i-lucide-sparkles",
        titleKey: t("about.coreValues.excellence.title"),
        descKey: t("about.coreValues.excellence.desc"),
    },
    {
        icon: "i-lucide-heart-handshake",
        titleKey: t("about.coreValues.stressFree.title"),
        descKey: t("about.coreValues.stressFree.desc"),
    },
    {
        icon: "i-lucide-globe",
        titleKey: t("about.coreValues.globalSynergy.title"),
        descKey: t("about.coreValues.globalSynergy.desc"),
    },
];

// SEO
useSeoMeta({
    title: () => locale.value === "en" ? "About Us - YHIE" : "Tentang Kami - YHIE",
    description: () =>
        locale.value === "en"
            ? "Learn about Yayasan Hafiz Indonesia Emas, our history, world-class vision, mission, and core values."
            : "Pelajari tentang Yayasan Hafiz Indonesia Emas, sejarah kami, visi kelas dunia, misi, dan nilai pilar utama kami.",
    ogTitle: () => locale.value === "en" ? "About Us - YHIE" : "Tentang Kami - YHIE",
    ogDescription: () =>
        locale.value === "en"
            ? "About Yayasan Hafiz Indonesia Emas history, vision, and mission."
            : "Tentang sejarah, visi, dan misi Yayasan Hafiz Indonesia Emas.",
});
</script>
