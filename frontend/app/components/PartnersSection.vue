<template>
    <section class="py-16 lg:py-20 border-t border-emerald-100 dark:border-emerald-900/40 bg-white dark:bg-gray-950 overflow-hidden transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="text-center mb-12">
                <p
                    data-aos="fade-up"
                    class="text-xs font-bold uppercase tracking-[0.3em] text-slate-400 dark:text-emerald-700 mb-3"
                >
                    {{ locale === 'en' ? 'Trusted Partners & Accreditation' : 'Mitra & Akreditasi Terpercaya' }}
                </p>
                <h2
                    data-aos="fade-up"
                    data-aos-delay="50"
                    class="text-2xl md:text-3xl font-serif font-bold text-gray-900 dark:text-gray-100"
                >
                    {{ t("partners.title") }}
                </h2>
            </div>

            <!-- Loading -->
            <div v-if="status === 'pending'" class="flex flex-wrap justify-center gap-4">
                <USkeleton v-for="n in 4" :key="n" class="h-16 w-36 rounded-2xl" />
            </div>

            <!-- Error -->
            <div v-else-if="error" class="text-center text-sm text-slate-400 py-6">
                Gagal memuat data partner.
            </div>

            <!-- Logo grid -->
            <div
                v-else-if="partners.length > 0"
                class="flex flex-wrap justify-center items-center gap-4 md:gap-6"
            >
                <NuxtLink
                    v-for="(partner, index) in partners"
                    :key="partner.id"
                    :to="localePath('/partner')"
                    data-aos="fade-up"
                    :data-aos-delay="index * 60"
                    class="group flex items-center justify-center h-16 px-6 min-w-[120px] rounded-2xl border border-slate-100 dark:border-emerald-900/50 bg-slate-50 dark:bg-emerald-950/20 hover:border-emerald-300 dark:hover:border-emerald-700 hover:bg-white dark:hover:bg-emerald-900/30 hover:shadow-md transition-all duration-300"
                >
                    <img
                        :src="getImageUrl(partner.logo)"
                        :alt="locale === 'id' ? partner.name_id : partner.name_en"
                        class="h-9 max-w-[100px] object-contain grayscale opacity-50 group-hover:grayscale-0 group-hover:opacity-100 transition-all duration-400"
                        @error="(e) => ((e.target as HTMLImageElement).src = '/placeholder.jpg')"
                    />
                </NuxtLink>
            </div>

            <!-- Empty -->
            <div v-else class="text-center text-sm text-slate-400 dark:text-emerald-700 py-6">
                Belum ada data partner.
            </div>

            <!-- CTA -->
            <div class="text-center mt-10" data-aos="fade-up">
                <NuxtLink :to="localePath('/partner')">
                    <UButton
                        variant="ghost"
                        color="success"
                        trailing-icon="i-lucide-arrow-right"
                        class="text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300"
                    >
                        {{ locale === 'en' ? 'View All Partners' : 'Lihat Semua Partner' }}
                    </UButton>
                </NuxtLink>
            </div>

        </div>
    </section>
</template>

<script setup lang="ts">
import { computed } from "vue"
import type { Partner } from "~/composables/usePartners"

const { t, locale } = useI18n()
const localePath = useLocalePath()
const config = useRuntimeConfig()
const backendUrl = config.public.sanctum?.baseUrl || "http://127.0.0.1:8000"
const client = useSanctumClient()

const { data: apiResponse, status, error } = await useAsyncData(
    "partners-preview",
    () => client("/api/partners")
)

const partners = computed<Partner[]>(() => {
    const responseData = (apiResponse.value as any)?.data
    return Array.isArray(responseData?.data) ? responseData.data : []
})

const getImageUrl = (path: string | null | undefined) => {
    if (!path) return "/placeholder.jpg"
    if (path.startsWith("http://") || path.startsWith("https://")) return path
    const cleanPath = path.startsWith("/") ? path.substring(1) : path
    return `${backendUrl}/${cleanPath}`
}
</script>