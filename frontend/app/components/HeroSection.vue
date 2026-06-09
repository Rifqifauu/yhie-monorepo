<template>
    <section
        class="relative -mt-24 bg-slate-50 dark:bg-emerald-950 text-slate-800 dark:text-white overflow-hidden pt-36 pb-24 lg:pt-44 lg:pb-32 flex items-center min-h-[85vh] transition-colors duration-300"
    >
        <img
            data-aos="fade-down"
            ref="parallaxLentera"
            src="/lentera.png"
            class="absolute dark:hue-rotate-90 dark:saturate-50 scale-60 z-30 -right-70 -top-35 will-change-transform pointer-events-none origin-center transition-[filter] duration-200"
            alt=""
        />

        <div
            data-aos="fade-up"
            ref="parallaxBg"
            class="absolute inset-0 bg-[url('/shadow-mosque.webp')] bg-cover bg-center will-change-transform shadow-lg transition-[filter] duration-200 dark:invert dark:brightness-150"
            aria-hidden="true"
        ></div>

        <div
            class="absolute inset-0 bg-white/65 dark:bg-emerald-950/90 transition-colors duration-300"
            aria-hidden="true"
        ></div>

        <div class="relative z-10 max-w-7xl mx-auto p-4 md:p-8 w-full">
            <div
                class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-center text-center lg:text-left"
            >
                <div
                    class="flex flex-col items-center lg:items-start order-2 lg:order-1"
                >
                    <span
                        data-aos="fade-up"
                        data-aos-delay="100"
                        class="text-amber-500 dark:text-amber-400 font-bold tracking-widest uppercase text-xs md:text-sm mb-4"
                    >
                        {{ t("hero.subtitle") }}
                    </span>
                    <span
                        class="w-20 h-2 flex bg-amber-400 rounded-2xl mb-4"
                    ></span>

                    <h1
                        data-aos="fade-up"
                        data-aos-delay="200"
                        class="text-4xl md:text-5xl lg:text-6xl font-serif font-extrabold tracking-tight leading-tight mb-6 text-emerald-950 dark:text-transparent dark:bg-clip-text dark:bg-gradient-to-r dark:from-white dark:to-emerald-200 drop-shadow-sm transition-colors"
                    >
                        {{ t("hero.title") }}
                    </h1>

                    <p
                        data-aos="fade-up"
                        data-aos-delay="300"
                        class="max-w-2xl text-lg md:text-xl text-slate-700 dark:text-emerald-100/90 leading-relaxed mb-6 font-medium dark:font-light transition-colors"
                    >
                        {{ t("hero.description") }}
                    </p>

                    <ul
                        data-aos="fade-up"
                        data-aos-delay="400"
                        class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-10 w-full max-w-2xl"
                    >
                        <li
                            v-for="(feature, index) in tm('hero.features')"
                            :key="index"
                            class="flex items-center gap-2.5 px-4 py-2 bg-white/60 dark:bg-emerald-900/40 backdrop-blur-md border border-emerald-300/50 dark:border-emerald-700/50 rounded-full shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-300"
                        >
                            <div
                                class="flex justify-center border-emerald-400 border items-center w-5 h-5 rounded-full bg-emerald-500/20 dark:bg-emerald-400/20 shrink-0"
                            >
                                <UIcon
                                    name="i-lucide-check"
                                    class="w-3.5 h-3.5 text-emerald-500 dark:text-emerald-400"
                                />
                            </div>
                            <span
                                class="font-medium text-sm text-emerald-800 dark:text-emerald-50"
                            >
                                {{ rt(feature) }}
                            </span>
                        </li>
                    </ul>

                    <div
                        data-aos="fade-up"
                        data-aos-delay="300"
                        class="flex flex-col sm:flex-row justify-center lg:justify-start items-center gap-4 w-full sm:w-auto"
                    >
                        <NuxtLink
                            :to="localePath(t('hero.linkPrimary'))"
                            class="w-full sm:w-auto px-8 py-4 bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-400 hover:to-emerald-500 text-slate-200 text-md font-bold rounded-xl shadow-[0_0_20px_rgba(245,158,11,0.2)] dark:shadow-[0_0_20px_rgba(245,158,11,0.3)] hover:shadow-[0_0_30px_rgba(245,158,11,0.5)] transition-all duration-300 transform hover:-translate-y-1 flex justify-center items-center"
                        >
                            {{ t("hero.ctaPrimary") }}
                        </NuxtLink>

                        <NuxtLink
                            :to="localePath(t('hero.linkSecondary'))"
                            class="w-full sm:w-auto px-8 py-4 bg-white/50 dark:bg-transparent backdrop-blur-sm border-2 border-emerald-700 dark:border-emerald-500 hover:border-amber-500 dark:hover:border-amber-400 text-emerald-900 dark:text-white hover:text-amber-700 dark:hover:text-amber-400 text-md font-semibold rounded-xl transition-all duration-300 flex justify-center items-center"
                        >
                            {{ t("hero.ctaSecondary") }}
                        </NuxtLink>
                    </div>
                </div>

                <div
                    class="flex flex-col justify-center items-center w-full order-1 lg:order-2 gap-6"
                >
                    <div
                        data-aos="fade-left"
                        data-aos-delay="400"
                        class="w-full max-w-md bg-white/60 dark:bg-emerald-900/70 backdrop-blur-md p-4 rounded-2xl border border-emerald-500 shadow-xl text-left lg:z-20 lg:-mb-10 lg:translate-y-4 transition-transform"
                    >
                        <div
                            class="flex justify-between items-center px-1 cursor-pointer select-none group/btn"
                            @click="toggleJadwal"
                            role="button"
                            :aria-expanded="showJadwal"
                        >
                            <div
                                class="flex items-center gap-1.5 text-emerald-800 dark:text-emerald-300 font-semibold text-xs md:text-sm"
                            >
                                <UIcon
                                    name="i-lucide-map-pin"
                                    class="w-4 h-4 text-amber-500"
                                />
                                Desa Tejasari, Kabupaten Purbalingga
                                <UIcon
                                    name="i-lucide-chevron-down"
                                    class="w-6 h-6 text-emerald-600 dark:text-emerald-400 transition-transform duration-300 ml-1 group-hover/btn:text-amber-500"
                                    :class="{ 'rotate-180': showJadwal }"
                                />
                            </div>
                            <span
                                class="text-[11px] text-slate-500 dark:text-emerald-200/60 font-mono"
                            >
                                {{ tanggalMasehi }}
                            </span>
                        </div>

                        <Transition name="expand">
                            <div v-if="showJadwal" class="overflow-hidden mt-3">
                                <div
                                    v-if="loading"
                                    class="flex justify-center py-2"
                                >
                                    <span
                                        class="text-xs animate-pulse text-emerald-600 dark:text-emerald-400"
                                        >Memuat jadwal...</span
                                    >
                                </div>

                                <div
                                    v-else
                                    class="grid grid-cols-5 gap-1.5 text-center"
                                >
                                    <div
                                        v-for="(waktu, name) in jadwalSholat"
                                        :key="name"
                                        class="p-1.5 bg-emerald-500/20 dark:bg-emerald-950/40 rounded-xl border border-emerald-500/10 hover:border-emerald-500/30 transition-colors"
                                    >
                                        <span
                                            class="block text-[10px] uppercase font-bold tracking-wider text-slate-500 dark:text-emerald-300/70"
                                            >{{ name }}</span
                                        >
                                        <span
                                            class="block text-xs md:text-sm font-mono font-bold text-emerald-700 dark:text-amber-400 mt-0.5"
                                            >{{ waktu }}</span
                                        >
                                    </div>
                                </div>
                            </div>
                        </Transition>
                    </div>

                    <div
                        data-aos="fade-left"
                        data-aos-delay="200"
                        class="group relative w-full flex justify-center"
                    >
                        <div
                            class="absolute -inset-1 bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-2xl blur-lg opacity-30 group-hover:opacity-50 transition duration-1000 group-hover:duration-300"
                        ></div>

                        <UCarousel
                            dots
                            :autoplay="{ delay: 3000 }"
                            v-slot="{ item }"
                            arrows
                            fade
                            loop
                            :items="imageItems"
                            class="w-full mx-auto"
                        >
                            <NuxtImg
                                :src="item"
                                :alt="t('hero.imageAlt')"
                                width="600"
                                height="450"
                                format="webp"
                                preload
                                class="relative saturate-80 w-full max-w-md lg:max-w-full aspect-[4/3] object-cover rounded-2xl shadow-2xl border-4 border-white dark:border-emerald-900/60 transform group-hover:scale-[1.01] transition-all duration-500"
                            />
                        </UCarousel>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from "vue";

const { t, tm, rt } = useI18n();
const localePath = useLocalePath();

const parallaxBg = ref<HTMLElement | null>(null);
const parallaxLentera = ref<HTMLElement | null>(null);

const loading = ref(true);
const tanggalMasehi = ref("");
const showJadwal = ref(false);
const jadwalSholat = ref({
    subuh: "--:--",
    dzuhur: "--:--",
    ashar: "--:--",
    maghrib: "--:--",
    isya: "--:--",
});

const imageItems = ["/quran.webp", "/tejasari.webp"];

const toggleJadwal = () => {
    showJadwal.value = !showJadwal.value;
};

const fetchJadwalSholat = async () => {
    try {
        const today = new Date();
        const yyyy = today.getFullYear();
        const mm = String(today.getMonth() + 1).padStart(2, "0");
        const dd = String(today.getDate()).padStart(2, "0");

        tanggalMasehi.value = `${dd}/${mm}/${yyyy}`;

        const response = await $fetch<any>(
            `https://api.myquran.com/v2/sholat/jadwal/1413/${yyyy}/${mm}/${dd}`,
        );

        if (response?.status && response?.data?.jadwal) {
            const data = response.data.jadwal;
            jadwalSholat.value = {
                subuh: data.subuh,
                dzuhur: data.dzuhur,
                ashar: data.ashar,
                maghrib: data.maghrib,
                isya: data.isya,
            };
        }
    } catch (error) {
        console.error("Gagal memuat jadwal sholat:", error);
    } finally {
        loading.value = false;
    }
};

let ticking = false;

const handleScroll = () => {
    if (!ticking) {
        window.requestAnimationFrame(() => {
            const scrollValue = window.scrollY;

            if (parallaxBg.value) {
                parallaxBg.value.style.transform = `translate3d(0, ${scrollValue * 0.35}px, 0)`;
            }

            if (parallaxLentera.value) {
                parallaxLentera.value.style.transform = `translate3d(0, ${scrollValue * -0.45}px, 0)`;
            }

            ticking = false;
        });

        ticking = true;
    }
};

onMounted(() => {
    window.addEventListener("scroll", handleScroll, { passive: true });
    fetchJadwalSholat();
});

onUnmounted(() => {
    window.removeEventListener("scroll", handleScroll);
});
</script>

<style scoped>
.expand-enter-active,
.expand-leave-active {
    transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    max-height: 100px;
    opacity: 1;
}

.expand-enter-from,
.expand-leave-to {
    max-height: 0;
    opacity: 0;
    margin-top: 0 !important;
}
</style>
