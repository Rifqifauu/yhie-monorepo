<template>
    <section
        class="relative overflow-hidden py-20 lg:py-28 bg-gradient-to-b from-white via-slate-50 to-emerald-50/50 dark:from-gray-950 dark:via-gray-900 dark:to-emerald-950/20 text-slate-900 dark:text-white transition-colors duration-500"
    >
        <!-- Background Ambient Glow Elements (Lebih dramatis) -->
        <div
            class="absolute top-1/2 left-1/4 -translate-y-1/2 w-[500px] h-[500px] bg-emerald-500/10 dark:bg-emerald-500/5 rounded-full blur-[120px] pointer-events-none"
        ></div>
        <div
            class="absolute top-12 right-10 w-96 h-96 bg-amber-400/10 dark:bg-amber-500/5 rounded-full blur-[100px] pointer-events-none"
        ></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 z-10">
            <!-- Floating Glass Card Wrapper -->
            <div
                class="relative overflow-hidden rounded-[3rem] p-8 md:p-12 lg:p-16 bg-white/40 dark:bg-gray-900/40 backdrop-blur-xl border border-white/60 dark:border-white/10 shadow-[0_20px_50px_rgba(31,38,135,0.05)] dark:shadow-[0_20px_50px_rgba(0,0,0,0.3)]"
                data-aos="zoom-in"
            >
                <!-- Background Mosque Silhouette di dalam Card (Terpotong rapi oleh rounded boks) -->
                <img
                    src="/shadow-mosque.webp"
                    class="absolute bottom-0 right-0 w-auto h-[90%] max-h-[350px] object-contain object-right-bottom pointer-events-none opacity-[0.07] dark:opacity-[0.04] dark:invert dark:brightness-110 z-0"
                    alt=""
                />

                <!-- Decorative crescent accent -->
                <svg
                    class="absolute top-8 right-12 w-24 h-24 text-emerald-800/[0.03] dark:text-amber-400/[0.02] pointer-events-none hidden md:block"
                    viewBox="0 0 100 100"
                    fill="currentColor"
                >
                    <path d="M50 10a40 40 0 1 0 0 80 32 32 0 1 1 0-80z" />
                </svg>

                <div
                    class="relative grid lg:grid-cols-3 gap-10 items-center z-10"
                >
                    <!-- Left content: Text Info -->
                    <div class="lg:col-span-2 text-center lg:text-left">
                        <span
                            class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-100/60 dark:bg-emerald-900/40 border border-emerald-200/50 dark:border-emerald-700/30 text-xs font-bold uppercase tracking-[0.25em] text-emerald-800 dark:text-amber-400 shadow-sm"
                        >
                            <UIcon
                                name="i-lucide-sparkles"
                                class="w-3.5 h-3.5 animate-pulse"
                            />
                            {{
                                locale === "en"
                                    ? "Exclusive Community"
                                    : "Komunitas Eksklusif"
                            }}
                        </span>

                        <h2
                            class="mt-6 text-3xl md:text-5xl font-serif font-bold tracking-tight text-gray-900 dark:text-white leading-[1.15]"
                        >
                            {{ t("cta.title") }}
                        </h2>

                        <p
                            class="mt-4 text-base md:text-lg text-gray-600 dark:text-emerald-100/80 leading-relaxed max-w-2xl mx-auto lg:mx-0"
                        >
                            {{ t("cta.description") }}
                        </p>
                    </div>

                    <!-- Right content: Interactivity Action Buttons -->
                    <div
                        class="flex flex-col sm:flex-row lg:flex-col gap-4 justify-center items-center lg:items-stretch lg:w-64 mx-auto lg:mr-0"
                    >
                        <!-- Primary Button: WhatsApp dengan bayangan menyala -->
                        <a
                            :href="waLink"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="w-full px-8 py-4 rounded-2xl bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 dark:from-amber-400 dark:to-amber-500 dark:hover:from-amber-500 dark:hover:to-amber-600 text-white dark:text-emerald-950 font-bold shadow-lg shadow-emerald-600/20 dark:shadow-amber-500/10 hover:shadow-emerald-600/30 dark:hover:shadow-amber-500/30 hover:-translate-y-0.5 transition-all duration-300 flex items-center justify-center gap-3 text-center"
                        >
                            <UIcon
                                name="i-lucide-phone"
                                class="w-5 h-5 shrink-0"
                            />
                            <span class="text-sm tracking-wide">{{
                                locale === "en"
                                    ? "Chat via WhatsApp"
                                    : "Hubungi WhatsApp"
                            }}</span>
                        </a>

                        <!-- Secondary Button: Pure Glass Button -->
                        <NuxtLink
                            :to="localePath('/program')"
                            class="w-full px-8 py-4 rounded-2xl bg-white/60 dark:bg-white/5 hover:bg-white dark:hover:bg-white/10 border border-gray-200 dark:border-white/10 text-gray-800 dark:text-white font-bold backdrop-blur-md shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-300 flex items-center justify-center gap-3 text-center"
                        >
                            <span class="text-sm tracking-wide">{{
                                locale === "en"
                                    ? "Explore Programs"
                                    : "Lihat Program"
                            }}</span>
                            <UIcon
                                name="i-lucide-arrow-right"
                                class="w-5 h-5 shrink-0 text-emerald-600 dark:text-amber-400"
                            />
                        </NuxtLink>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup lang="ts">
import { useSettings } from "~/composables/useSettings";

const { t, locale } = useI18n();
const localePath = useLocalePath();
const { waLink } = useSettings();
</script>
