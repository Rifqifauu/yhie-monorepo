<template>
    <section class="w-full bg-gradient-to-b from-white via-emerald-50/60 to-emerald-100/40 dark:from-gray-950 dark:via-emerald-950/60 dark:to-gray-950 px-4 py-20 lg:py-28 border-t border-emerald-100 dark:border-emerald-900/40 transition-colors duration-300">
        <div class="max-w-7xl mx-auto">

            <!-- Header -->
            <div class="text-center max-w-2xl mx-auto mb-14">
                <span
                    data-aos="fade-up"
                    class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-[0.3em] text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/60 px-3.5 py-1.5 rounded-full border border-amber-200/50 dark:border-amber-900/40 mb-5"
                >
                    <UIcon name="i-lucide-graduation-cap" class="w-4 h-4" />
                    {{ locale === 'en' ? 'Our Programs' : 'Program Kami' }}
                </span>
                <h2
                    data-aos="fade-up"
                    data-aos-delay="50"
                    class="text-3xl md:text-5xl font-bold font-serif text-gray-900 dark:text-gray-50 tracking-tight mb-4"
                >
                    {{ t("programs.title") }}
                </h2>
                <div class="flex items-center justify-center gap-1.5 mb-5">
                    <div class="h-1 w-12 bg-amber-500 rounded-full"></div>
                    <div class="h-1.5 w-1.5 bg-emerald-500 rounded-full"></div>
                    <div class="h-1 w-12 bg-amber-500 rounded-full"></div>
                </div>
                <p
                    data-aos="fade-up"
                    data-aos-delay="100"
                    class="text-base md:text-lg text-slate-600 dark:text-emerald-100/70 leading-relaxed"
                >
                    {{ t("programs.description") }}
                </p>
            </div>

            <!-- Loading -->
            <div v-if="status === 'pending'" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div v-for="n in 3" :key="n" class="rounded-3xl overflow-hidden border border-emerald-100 dark:border-emerald-900/50">
                    <USkeleton class="aspect-[4/3] w-full" />
                    <div class="p-5 space-y-3">
                        <USkeleton class="h-5 w-1/3 rounded-full" />
                        <USkeleton class="h-6 w-4/5 rounded" />
                        <USkeleton class="h-4 w-full rounded" />
                        <USkeleton class="h-4 w-2/3 rounded" />
                    </div>
                </div>
            </div>

            <!-- Error -->
            <div v-else-if="error" class="max-w-xl mx-auto my-6">
                <UAlert
                    icon="i-heroicons-exclamation-triangle"
                    color="error"
                    variant="soft"
                    title="Gagal memuat data"
                    description="Terjadi kesalahan saat mengambil data program dari server."
                >
                    <template #actions>
                        <UButton size="sm" color="error" icon="i-heroicons-arrow-path-20-solid" @click="() => refresh()">
                            Coba Lagi
                        </UButton>
                    </template>
                </UAlert>
            </div>

            <div v-else>
                <!-- Program cards -->
                <div v-if="data && data.length > 0" class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8">
                    <NuxtLink
                        v-for="(program, index) in data"
                        :key="program.id"
                        :to="localePath('/program/' + (locale === 'id' ? program.slug_id : program.slug_en))"
                        class="group relative overflow-hidden rounded-3xl border border-emerald-100/80 dark:border-emerald-800/50 bg-white/80 dark:bg-emerald-950/30 shadow-sm hover:-translate-y-2 hover:shadow-2xl hover:shadow-emerald-500/10 transition-all duration-500"
                        data-aos="fade-up"
                        :data-aos-delay="index * 100"
                    >
                        <!-- Image -->
                        <div class="relative aspect-[4/3] overflow-hidden">
                            <NuxtImg
                                :src="getImageUrl(program.image_path)"
                                :alt="locale === 'id' ? program.title_id : program.title_en"
                                width="640"
                                height="480"
                                format="webp"
                                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                            />
                            <div class="absolute inset-0 bg-gradient-to-t from-emerald-950/60 via-transparent to-transparent"></div>

                            <!-- Price badge -->
                            <div class="absolute bottom-3 left-3 px-3 py-1.5 rounded-full bg-white/90 dark:bg-emerald-950/90 backdrop-blur-sm text-xs font-bold text-emerald-700 dark:text-amber-400 shadow-sm">
                                {{ formatPrice(locale === "id" ? program.price_id : program.price_en) }}
                            </div>

                            <!-- Arrow on hover -->
                            <div class="absolute top-3 right-3 w-8 h-8 rounded-full bg-white/0 group-hover:bg-white/90 flex items-center justify-center transition-all duration-300 opacity-0 group-hover:opacity-100">
                                <UIcon name="i-lucide-arrow-up-right" class="w-4 h-4 text-emerald-700" />
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-5">
                            <h3 class="text-lg font-serif font-bold text-gray-900 dark:text-emerald-50 line-clamp-2 mb-2 group-hover:text-emerald-700 dark:group-hover:text-amber-300 transition-colors duration-300">
                                {{ locale === "id" ? program.title_id : program.title_en }}
                            </h3>
                            <p class="text-sm text-slate-500 dark:text-emerald-200/60 leading-relaxed line-clamp-3">
                                {{ locale === "id" ? program.description_id : program.description_en }}
                            </p>
                            <div class="mt-4 flex items-center gap-1 text-sm font-semibold text-emerald-600 dark:text-emerald-400">
                                <span>{{ locale === 'en' ? 'Learn more' : 'Selengkapnya' }}</span>
                                <UIcon name="i-lucide-arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform" />
                            </div>
                        </div>
                    </NuxtLink>
                </div>

                <!-- Empty -->
                <div v-else class="text-center py-16 text-slate-400 dark:text-emerald-700">
                    <UIcon name="i-lucide-graduation-cap" class="w-12 h-12 mx-auto mb-3 opacity-40" />
                    <p>Belum ada program tersedia.</p>
                </div>

                <!-- CTA -->
                <div class="text-center mt-12" data-aos="fade-up">
                    <NuxtLink :to="localePath('/program')">
                        <UButton
                            size="lg"
                            variant="outline"
                            color="success"
                            trailing-icon="i-lucide-arrow-right"
                            class="rounded-full px-8 border-emerald-400 dark:border-emerald-600 text-emerald-700 dark:text-emerald-300 hover:bg-emerald-50 dark:hover:bg-emerald-900/40"
                        >
                            {{ locale === 'en' ? 'View All Programs' : 'Lihat Semua Program' }}
                        </UButton>
                    </NuxtLink>
                </div>
            </div>

        </div>
    </section>
</template>

<script setup lang="ts">
import type { Program } from "~/composables/usePrograms"

const { t, locale } = useI18n()
const localePath = useLocalePath()

const config = useRuntimeConfig()
const backendUrl = config.public.sanctum?.baseUrl || "http://127.0.0.1:8000"
const client = useSanctumClient()

const { data, status, error, refresh } = await useAsyncData(
    "programs",
    () => client("/api/programs"),
    {
        transform: (response: any): Program[] => {
            const programList = response?.data?.data || []
            return programList.slice(0, 3)
        },
    }
)

const getImageUrl = (path?: string | null) => {
    if (!path) return "/placeholder.jpg"
    if (path.startsWith("http")) return path
    return `${backendUrl}/${path.startsWith("/") ? path.substring(1) : path}`
}

const formatPrice = (price?: number | string | null) => {
    if (price === null || price === undefined) {
        return locale.value === "en" ? "Free" : "Gratis"
    }
    const n = typeof price === "string" ? parseFloat(price) : price
    if (isNaN(n)) return price
    if (locale.value === "id") {
        return new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR", minimumFractionDigits: 0 }).format(n)
    }
    return new Intl.NumberFormat("en-US", { style: "currency", currency: "USD" }).format(n)
}
</script>