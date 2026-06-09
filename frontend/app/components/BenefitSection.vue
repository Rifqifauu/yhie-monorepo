<template>
    <section class="py-20 lg:py-28 border-t border-emerald-100 dark:border-emerald-900/40 bg-gradient-to-b from-emerald-50/80 via-white to-white dark:from-gray-950 dark:via-emerald-950/40 dark:to-gray-950 transition-colors duration-500 overflow-hidden relative">

        <!-- Decorative blobs -->
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-amber-400/10 dark:bg-amber-500/5 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-emerald-400/10 dark:bg-emerald-500/5 rounded-full blur-3xl pointer-events-none"></div>

        <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-7xl relative z-10">

            <!-- Header -->
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span
                    data-aos="fade-up"
                    class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-[0.3em] text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/60 px-3.5 py-1.5 rounded-full border border-amber-200/50 dark:border-amber-900/40 mb-5"
                >
                    <UIcon name="i-lucide-sparkles" class="w-4 h-4" />
                    {{ locale === 'en' ? 'Why Choose Us' : 'Keunggulan Kami' }}
                </span>
                <h2
                    data-aos="fade-up"
                    data-aos-delay="50"
                    class="text-3xl md:text-5xl font-serif font-bold text-gray-900 dark:text-white mb-5 tracking-tight leading-tight"
                >
                    {{ t("benefits.title") }}
                </h2>
                <div class="flex items-center justify-center gap-1.5 mb-5">
                    <div class="h-1 w-12 bg-amber-500 rounded-full"></div>
                    <div class="h-1.5 w-1.5 bg-emerald-500 rounded-full"></div>
                    <div class="h-1 w-12 bg-amber-500 rounded-full"></div>
                </div>
                <p
                    data-aos="fade-up"
                    data-aos-delay="100"
                    class="text-base md:text-lg text-slate-600 dark:text-emerald-100/70 leading-relaxed max-w-2xl mx-auto"
                >
                    {{ t("benefits.description") }}
                </p>
            </div>

            <!-- Cards -->
            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                <div
                    v-for="(item, index) in benefitItems"
                    :key="index"
                    data-aos="fade-up"
                    :data-aos-delay="index * 120"
                    class="group relative overflow-hidden rounded-3xl border border-white dark:border-emerald-800/40 bg-white/80 dark:bg-emerald-950/30 shadow-sm hover:shadow-xl hover:shadow-emerald-500/10 hover:-translate-y-2 transition-all duration-500 p-7 backdrop-blur-sm"
                >
                    <!-- Hover glow -->
                    <div class="absolute top-0 right-0 -mt-8 -mr-8 w-40 h-40 bg-gradient-to-br from-emerald-300/20 to-amber-300/20 rounded-full blur-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-700 pointer-events-none"></div>

                    <!-- Number badge -->
                    <div class="flex items-center gap-4 mb-6">
                        <div class="relative w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-50 to-emerald-100 dark:from-emerald-900/60 dark:to-emerald-800/30 flex items-center justify-center border border-emerald-100 dark:border-emerald-700/40 group-hover:scale-110 group-hover:rotate-3 transition-all duration-500 shadow-sm">
                            <UIcon
                                :name="getIcon(index)"
                                class="w-7 h-7 text-emerald-600 dark:text-emerald-400 group-hover:text-amber-500 dark:group-hover:text-amber-400 transition-colors duration-500"
                            />
                        </div>
                        <span class="text-4xl font-serif font-extrabold text-emerald-100 dark:text-emerald-900 select-none">
                            0{{ index + 1 }}
                        </span>
                    </div>

                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3 font-serif group-hover:text-emerald-700 dark:group-hover:text-amber-400 transition-colors duration-300 tracking-tight leading-snug">
                        {{ rt(item.title) }}
                    </h3>

                    <div class="w-8 h-0.5 bg-amber-400/60 group-hover:w-14 group-hover:bg-emerald-500 transition-all duration-500 mb-4 rounded-full"></div>

                    <p class="text-sm text-slate-600 dark:text-emerald-100/70 leading-relaxed">
                        {{ rt(item.description) }}
                    </p>
                </div>
            </div>

        </div>
    </section>
</template>

<script setup lang="ts">
import { computed } from "vue"

interface BenefitItem {
    title: string
    description: string
}

const { t, tm, rt, locale } = useI18n()

const benefitItems = computed<BenefitItem[]>(() => {
    return (tm("benefits.items") || []) as unknown as BenefitItem[]
})

const getIcon = (index: number) => {
    const icons = [
        "i-heroicons-sparkles-solid",
        "i-heroicons-globe-alt-solid",
        "i-heroicons-user-group-solid",
    ]
    return icons[index] || "i-heroicons-check-badge-solid"
}
</script>