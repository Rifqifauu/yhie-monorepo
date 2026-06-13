<template>
    <section class="relative overflow-hidden py-16 lg:py-24 bg-gradient-to-b from-white via-emerald-50 to-slate-50 dark:from-gray-950 dark:via-emerald-950/80 dark:to-emerald-950/80 text-slate-900 dark:text-white transition-colors duration-300">
        <!-- Background Elements -->
        <img
            src="/shadow-mosque.webp"
            class="absolute bottom-0 right-0 w-auto h-full max-h-[85%] object-contain object-right-bottom pointer-events-none opacity-[0.12] dark:opacity-[0.06] dark:invert dark:brightness-110"
            alt=""
        />
        <div
            class="absolute -top-24 -left-24 w-80 h-80 bg-amber-400/20 dark:bg-amber-500/10 rounded-full blur-3xl"
        ></div>
        <div
            class="absolute -bottom-28 -right-28 w-96 h-96 bg-emerald-400/20 dark:bg-emerald-500/10 rounded-full blur-3xl"
        ></div>

        <!-- Decorative crescent accent -->
        <svg class="absolute top-6 left-8 w-16 h-16 text-amber-500/[0.08] dark:text-amber-400/[0.04] pointer-events-none" viewBox="0 0 100 100" fill="currentColor">
            <path d="M50 10a40 40 0 1 0 0 80 32 32 0 1 1 0-80z" />
        </svg>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-3 gap-10 items-center">
                <!-- Left/Center content: Text -->
                <div class="lg:col-span-2 text-center lg:text-left" data-aos="fade-up">
                    <span
                        class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-[0.3em] text-amber-600 dark:text-amber-400"
                    >
                        <UIcon name="i-lucide-sparkles" class="w-4 h-4" />
                        {{ locale === 'en' ? 'Exclusive Community' : 'Komunitas Eksklusif' }}
                    </span>
                    <h2 class="mt-4 text-3xl md:text-5xl font-serif font-extrabold tracking-tight text-emerald-950 dark:text-white leading-tight">
                        {{ t("cta.title") }}
                    </h2>
                    <p class="mt-4 text-lg text-slate-600 dark:text-emerald-100/90 leading-relaxed max-w-2xl mx-auto lg:mx-0">
                        {{ t("cta.description") }}
                    </p>
                </div>

                <!-- Right content: CTA Action Buttons -->
                <div class="flex flex-col sm:flex-row lg:flex-col gap-4 justify-center items-center lg:items-end" data-aos="fade-left">
                    <a
                        :href="waLink"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="w-full sm:w-auto px-8 py-4 rounded-2xl bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-850 dark:from-amber-400 dark:to-amber-500 dark:hover:from-amber-500 dark:hover:to-amber-600 text-white dark:text-emerald-950 font-bold shadow-xl shadow-emerald-600/15 dark:shadow-amber-500/15 hover:shadow-emerald-600/30 dark:hover:shadow-amber-500/30 transition-all flex items-center justify-center gap-3 text-center"
                    >
                        <UIcon name="i-lucide-phone" class="w-5 h-5" />
                        <span>{{ locale === 'en' ? 'Chat via WhatsApp' : 'Hubungi WhatsApp' }}</span>
                    </a>
                    <NuxtLink
                        :to="localePath('/program')"
                        class="w-full sm:w-auto px-8 py-4 rounded-2xl bg-white/70 hover:bg-white border border-emerald-200 dark:bg-white/10 dark:hover:bg-white/20 dark:border-white/25 text-slate-800 dark:text-white font-bold transition-all flex items-center justify-center gap-3 text-center"
                    >
                        <span>{{ locale === 'en' ? 'Explore Programs' : 'Lihat Program' }}</span>
                        <UIcon name="i-lucide-arrow-right" class="w-5 h-5" />
                    </NuxtLink>
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
