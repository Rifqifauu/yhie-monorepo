<template>
    <section class="relative -mt-24 bg-slate-50 dark:bg-gray-950 text-slate-800 dark:text-white overflow-hidden pt-36 pb-24 lg:pt-44 lg:pb-32 flex items-center min-h-[90vh] transition-colors duration-300">

        <!-- Parallax background -->
        <div
            ref="parallaxBg"
            class="absolute inset-0 bg-[url('/shadow-mosque.webp')] bg-cover bg-center will-change-transform pointer-events-none opacity-20 dark:opacity-10 dark:invert dark:brightness-150 transition-[opacity,filter] duration-300"
            aria-hidden="true"
        ></div>

        <!-- Decorative blobs -->
        <div class="absolute top-1/3 -left-32 w-96 h-96 bg-emerald-400/20 dark:bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-20 right-0 w-80 h-80 bg-amber-400/15 dark:bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Lentera deco -->
        <img
            ref="parallaxLentera"
            src="/lentera.png"
            class="absolute dark:hue-rotate-90 dark:saturate-50 w-[420px] z-10 -right-20 -top-10 will-change-transform pointer-events-none origin-center transition-[filter] duration-200 opacity-60 dark:opacity-30"
            aria-hidden="true"
            alt=""
        />

        <div class="relative z-10 max-w-7xl mx-auto px-4 md:px-8 w-full">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-center text-center lg:text-left">

                <!-- Left: Text content -->
                <div class="flex flex-col items-center lg:items-start order-2 lg:order-1">

                    <span
                        data-aos="fade-up"
                        data-aos-delay="50"
                        class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-[0.3em] text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/60 px-3.5 py-1.5 rounded-full border border-amber-200/50 dark:border-amber-900/40 mb-5"
                    >
                        <UIcon name="i-lucide-star" class="w-3.5 h-3.5" />
                        {{ t("hero.subtitle") }}
                    </span>

                    <h1
                        data-aos="fade-up"
                        data-aos-delay="150"
                        class="text-4xl md:text-5xl lg:text-[3.5rem] font-serif font-extrabold tracking-tight leading-[1.1] mb-6 text-transparent bg-clip-text bg-gradient-to-br from-emerald-900 via-emerald-700 to-emerald-800 dark:from-white dark:via-emerald-100 dark:to-emerald-300 transition-colors"
                    >
                        {{ t("hero.title") }}
                    </h1>

                    <p
                        data-aos="fade-up"
                        data-aos-delay="250"
                        class="max-w-xl text-base md:text-lg text-slate-600 dark:text-emerald-100/80 leading-relaxed mb-8 transition-colors"
                    >
                        {{ t("hero.description") }}
                    </p>

                    <!-- Feature pills -->
                    <ul
                        data-aos="fade-up"
                        data-aos-delay="350"
                        class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 mb-10 w-full max-w-xl"
                    >
                        <li
                            v-for="(feature, index) in tm('hero.features')"
                            :key="index"
                            class="flex items-center gap-2.5 px-4 py-2.5 bg-white/70 dark:bg-emerald-900/30 backdrop-blur-sm border border-emerald-200/60 dark:border-emerald-700/40 rounded-full shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-300"
                        >
                            <div class="flex-shrink-0 w-5 h-5 rounded-full bg-emerald-500/20 dark:bg-emerald-400/20 border border-emerald-400/50 flex items-center justify-center">
                                <UIcon name="i-lucide-check" class="w-3 h-3 text-emerald-600 dark:text-emerald-400" />
                            </div>
                            <span class="text-sm font-medium text-emerald-900 dark:text-emerald-100">
                                {{ rt(feature) }}
                            </span>
                        </li>
                    </ul>

                    <!-- CTA buttons -->
                    <div
                        data-aos="fade-up"
                        data-aos-delay="400"
                        class="flex flex-col sm:flex-row justify-center lg:justify-start items-center gap-3 w-full sm:w-auto"
                    >
                        <NuxtLink
                            :to="localePath(t('hero.linkPrimary'))"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3.5 bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-500 hover:to-emerald-600 text-white text-sm font-bold rounded-2xl shadow-lg shadow-emerald-600/25 hover:shadow-xl hover:shadow-emerald-600/35 hover:-translate-y-0.5 transition-all duration-300"
                        >
                            {{ t("hero.ctaPrimary") }}
                            <UIcon name="i-lucide-arrow-right" class="w-4 h-4" />
                        </NuxtLink>

                        <NuxtLink
                            :to="localePath(t('hero.linkSecondary'))"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3.5 bg-white/60 dark:bg-white/5 backdrop-blur-sm border border-emerald-300 dark:border-emerald-700 hover:border-amber-400 dark:hover:border-amber-500 text-emerald-900 dark:text-white hover:text-amber-700 dark:hover:text-amber-400 text-sm font-semibold rounded-2xl transition-all duration-300"
                        >
                            <UIcon name="i-lucide-play-circle" class="w-4 h-4" />
                            {{ t("hero.ctaSecondary") }}
                        </NuxtLink>
                    </div>
                </div>

                <!-- Right: Visual card + carousel -->
                <div class="flex flex-col justify-center items-center w-full order-1 lg:order-2 gap-5">

                    <!-- Prayer time widget -->
                    <div
                        data-aos="fade-left"
                        data-aos-delay="200"
                        class="w-full max-w-md bg-white/70 dark:bg-emerald-900/50 backdrop-blur-md px-4 py-3 rounded-2xl border border-emerald-200/60 dark:border-emerald-700/40 shadow-lg text-left lg:z-20 lg:-mb-8 lg:translate-y-3 transition-transform"
                    >
                        <div
                            class="flex justify-between items-center cursor-pointer select-none group/btn"
                            @click="toggleJadwal"
                            role="button"
                            :aria-expanded="showJadwal"
                        >
                            <div class="flex items-center gap-1.5 text-emerald-800 dark:text-emerald-300 font-semibold text-xs md:text-sm">
                                <UIcon name="i-lucide-map-pin" class="w-4 h-4 text-amber-500 shrink-0" />
                                Desa Tejasari, Kab. Purbalingga
                                <UIcon
                                    name="i-lucide-chevron-down"
                                    class="w-4 h-4 text-emerald-500 dark:text-emerald-400 transition-transform duration-300 group-hover/btn:text-amber-500"
                                    :class="{ 'rotate-180': showJadwal }"
                                />
                            </div>
                            <span class="text-[11px] text-slate-400 dark:text-emerald-500 font-mono tabular-nums">
                                {{ tanggalMasehi }}
                            </span>
                        </div>

                        <Transition name="expand">
                            <div v-if="showJadwal" class="overflow-hidden mt-3">
                                <div v-if="loading" class="flex justify-center py-2">
                                    <span class="text-xs animate-pulse text-emerald-600 dark:text-emerald-400">
                                        Memuat jadwal...
                                    </span>
                                </div>
                                <div v-else class="grid grid-cols-5 gap-1.5 text-center">
                                    <div
                                        v-for="(waktu, name) in jadwalSholat"
                                        :key="name"
                                        class="py-2 px-1 bg-emerald-50 dark:bg-emerald-950/60 rounded-xl border border-emerald-100 dark:border-emerald-800/40"
                                    >
                                        <span class="block text-[9px] uppercase font-bold tracking-wider text-slate-400 dark:text-emerald-500">
                                            {{ name }}
                                        </span>
                                        <span class="block text-xs font-mono font-bold text-emerald-700 dark:text-amber-400 mt-0.5 tabular-nums">
                                            {{ waktu }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </Transition>
                    </div>

                    <!-- Image carousel -->
                    <div data-aos="fade-left" data-aos-delay="300" class="group relative w-full max-w-md">
                        <div class="absolute -inset-2 bg-gradient-to-br from-emerald-400/30 to-amber-400/20 rounded-3xl blur-xl opacity-50 group-hover:opacity-75 transition duration-700"></div>
                        <UCarousel
                            dots
                            :autoplay="{ delay: 3500 }"
                            v-slot="{ item }"
                            arrows
                            fade
                            loop
                            :items="imageItems"
                            class="relative w-full"
                        >
                            <NuxtImg
                                :src="item"
                                :alt="t('hero.imageAlt')"
                                width="600"
                                height="450"
                                format="webp"
                                preload
                                class="w-full aspect-[4/3] object-cover rounded-2xl shadow-2xl border-2 border-white/80 dark:border-emerald-800/60 transform group-hover:scale-[1.01] transition-all duration-500"
                            />
                        </UCarousel>
                    </div>

                    <!-- Trust badges -->
                    <div
                        data-aos="fade-up"
                        data-aos-delay="400"
                        class="flex items-center gap-4 flex-wrap justify-center"
                    >
                        <div class="flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/70 dark:bg-emerald-900/30 border border-emerald-100 dark:border-emerald-800/40 text-xs font-semibold text-emerald-700 dark:text-emerald-300">
                            <UIcon name="i-lucide-shield-check" class="w-3.5 h-3.5 text-emerald-500" />
                            Kemenkumham RI
                        </div>
                        <div class="flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/70 dark:bg-emerald-900/30 border border-emerald-100 dark:border-emerald-800/40 text-xs font-semibold text-emerald-700 dark:text-emerald-300">
                            <UIcon name="i-lucide-award" class="w-3.5 h-3.5 text-amber-500" />
                            Akreditasi IAO
                        </div>
                        <div class="flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/70 dark:bg-emerald-900/30 border border-emerald-100 dark:border-emerald-800/40 text-xs font-semibold text-emerald-700 dark:text-emerald-300">
                            <UIcon name="i-lucide-globe" class="w-3.5 h-3.5 text-sky-500" />
                            ASEAN Recognition
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from "vue"

const { t, tm, rt } = useI18n()
const localePath = useLocalePath()

const parallaxBg = ref<HTMLElement | null>(null)
const parallaxLentera = ref<HTMLElement | null>(null)

const loading = ref(true)
const tanggalMasehi = ref("")
const showJadwal = ref(false)
const jadwalSholat = ref({
    subuh: "--:--",
    dzuhur: "--:--",
    ashar: "--:--",
    maghrib: "--:--",
    isya: "--:--",
})

const imageItems = ["/quran.webp", "/tejasari.webp"]

const toggleJadwal = () => {
    showJadwal.value = !showJadwal.value
}

const fetchJadwalSholat = async () => {
    try {
        const today = new Date()
        const yyyy = today.getFullYear()
        const mm = String(today.getMonth() + 1).padStart(2, "0")
        const dd = String(today.getDate()).padStart(2, "0")
        tanggalMasehi.value = `${dd}/${mm}/${yyyy}`

        const response = await $fetch<any>(
            `https://api.myquran.com/v2/sholat/jadwal/1413/${yyyy}/${mm}/${dd}`
        )
        if (response?.status && response?.data?.jadwal) {
            const data = response.data.jadwal
            jadwalSholat.value = {
                subuh: data.subuh,
                dzuhur: data.dzuhur,
                ashar: data.ashar,
                maghrib: data.maghrib,
                isya: data.isya,
            }
        }
    } catch (e) {
        console.error("Gagal memuat jadwal sholat:", e)
    } finally {
        loading.value = false
    }
}

let ticking = false
const handleScroll = () => {
    if (!ticking) {
        window.requestAnimationFrame(() => {
            const scrollValue = window.scrollY
            if (parallaxBg.value) {
                parallaxBg.value.style.transform = `translate3d(0, ${scrollValue * 0.3}px, 0)`
            }
            if (parallaxLentera.value) {
                parallaxLentera.value.style.transform = `translate3d(0, ${scrollValue * -0.4}px, 0)`
            }
            ticking = false
        })
        ticking = true
    }
}

onMounted(() => {
    window.addEventListener("scroll", handleScroll, { passive: true })
    fetchJadwalSholat()
})
onUnmounted(() => {
    window.removeEventListener("scroll", handleScroll)
})
</script>

<style scoped>
.expand-enter-active,
.expand-leave-active {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    max-height: 120px;
    opacity: 1;
}
.expand-enter-from,
.expand-leave-to {
    max-height: 0;
    opacity: 0;
    margin-top: 0 !important;
}
</style>