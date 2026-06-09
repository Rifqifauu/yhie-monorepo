<template>
    <section class="relative overflow-hidden py-20 lg:py-28 bg-gradient-to-br from-emerald-800 via-emerald-900 to-emerald-950 dark:from-gray-950 dark:via-emerald-950 dark:to-gray-950 text-white border-t border-emerald-700/40 dark:border-emerald-900/60 transition-colors duration-300">

        <!-- Background texture -->
        <img
            src="/shadow-mosque.webp"
            class="absolute inset-0 w-full h-full object-cover object-center opacity-[0.07] pointer-events-none"
            aria-hidden="true"
            alt=""
        />

        <!-- Blobs -->
        <div class="absolute -top-32 -left-32 w-96 h-96 bg-amber-400/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-emerald-400/20 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl mx-auto text-center" data-aos="fade-up">

                <span class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-[0.3em] text-amber-300 bg-amber-400/10 px-3.5 py-1.5 rounded-full border border-amber-400/20 mb-6">
                    <UIcon name="i-lucide-sparkles" class="w-4 h-4" />
                    {{ locale === 'en' ? 'Exclusive Community' : 'Komunitas Eksklusif' }}
                </span>

                <h2 class="text-3xl md:text-5xl font-serif font-extrabold tracking-tight leading-[1.15] mb-5 text-white">
                    {{ t("cta.title") }}
                </h2>

                <p class="text-base md:text-lg text-emerald-100/80 leading-relaxed max-w-2xl mx-auto mb-10">
                    {{ t("cta.description") }}
                </p>

                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a
                        :href="waLink"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-3 px-8 py-4 rounded-2xl bg-amber-400 hover:bg-amber-300 text-emerald-950 font-bold shadow-xl shadow-amber-400/20 hover:shadow-amber-400/40 hover:-translate-y-0.5 transition-all duration-300 text-sm"
                    >
                        <UIcon name="i-lucide-message-circle" class="w-5 h-5" />
                        {{ locale === 'en' ? 'Chat via WhatsApp' : 'Hubungi via WhatsApp' }}
                    </a>

                    <NuxtLink
                        :to="localePath('/program')"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-3 px-8 py-4 rounded-2xl bg-white/10 hover:bg-white/20 border border-white/20 hover:border-white/40 text-white font-semibold transition-all duration-300 text-sm"
                    >
                        {{ locale === 'en' ? 'Explore Programs' : 'Lihat Program' }}
                        <UIcon name="i-lucide-arrow-right" class="w-4 h-4" />
                    </NuxtLink>
                </div>

                <!-- Stats row -->
                <div class="mt-14 pt-10 border-t border-white/10 grid grid-cols-3 gap-6 max-w-lg mx-auto">
                    <div class="text-center">
                        <p class="text-2xl font-serif font-extrabold text-white">500+</p>
                        <p class="text-xs text-emerald-300/70 mt-1 font-medium uppercase tracking-wider">
                            {{ locale === 'en' ? 'Graduates' : 'Wisudawan' }}
                        </p>
                    </div>
                    <div class="text-center border-x border-white/10">
                        <p class="text-2xl font-serif font-extrabold text-white">IAO</p>
                        <p class="text-xs text-emerald-300/70 mt-1 font-medium uppercase tracking-wider">
                            {{ locale === 'en' ? 'Accredited' : 'Terakreditasi' }}
                        </p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-serif font-extrabold text-white">4+</p>
                        <p class="text-xs text-emerald-300/70 mt-1 font-medium uppercase tracking-wider">
                            {{ locale === 'en' ? 'Countries' : 'Negara' }}
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </section>
</template>

<script setup lang="ts">
import { useSettings } from "~/composables/useSettings"

const { t, locale } = useI18n()
const localePath = useLocalePath()
const { waLink } = useSettings()
</script>