<template>
    <div
        class="relative w-full mx-auto bg-gradient-to-b from-white via-white to-white dark:from-gray-950 dark:via-gray-950 dark:to-gray-950 px-4 py-20 overflow-hidden transition-colors duration-500"
    >
        <div
            class="absolute -top-20 right-1/4 w-96 h-96 bg-emerald-400/20 dark:bg-emerald-500/10 rounded-full blur-[100px] pointer-events-none animate-pulse"
        ></div>
        <div
            class="absolute bottom-0 left-1/6 w-80 h-80 bg-teal-400/20 dark:bg-teal-500/10 rounded-full blur-[100px] pointer-events-none animate-pulse"
            style="animation-delay: 2s;"
        ></div>

        <!-- Dynamic Background Grid -->
        <div class="absolute inset-0 z-0">
            <div class="absolute inset-0 bg-[linear-gradient(to_right,#10b981_1px,transparent_1px),linear-gradient(to_bottom,#10b981_1px,transparent_1px)] bg-[size:4rem_4rem] [mask-image:radial-gradient(ellipse_60%_50%_at_50%_50%,#000_70%,transparent_100%)] opacity-15 dark:opacity-[0.06]"></div>
        </div>

        <!-- Decorative Elements -->
        <div class="absolute left-0 top-1/4 w-32 h-32 bg-emerald-500/20 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute right-0 bottom-1/4 w-40 h-40 bg-teal-500/20 rounded-full blur-3xl animate-pulse" style="animation-delay: 1.5s;"></div>

        <svg
            class="absolute top-8 left-12 w-20 h-20 text-emerald-500/[0.06] dark:text-emerald-400/[0.03] pointer-events-none"
            viewBox="0 0 100 100"
            fill="currentColor"
        >
            <polygon
                points="50,0 61,35 100,35 68,57 79,91 50,70 21,91 32,57 0,35 39,35"
            />
        </svg>

        <svg
            class="absolute bottom-6 right-8 w-36 h-18 text-amber-400/[0.05] dark:text-amber-400/[0.03] pointer-events-none"
            viewBox="0 0 200 100"
            fill="none"
            stroke="currentColor"
            stroke-width="1.5"
        >
            <path d="M20 80 Q100 10 180 80" />
            <path d="M40 80 Q100 25 160 80" />
        </svg>

        <div class="max-w-7xl mx-auto pb-6 relative z-10">
            <div class="text-center mb-16 max-w-3xl mx-auto px-4">
                <h2 class="text-4xl md:text-5xl font-bold mb-4 font-serif text-gray-900 dark:text-gray-50 tracking-tight">
                    {{ t("partners.title") }}
                </h2>
                <div class="w-20 h-1 bg-gradient-to-r from-emerald-800 to-emerald-500 mx-auto rounded-full"></div>
                <p class="text-lg font-medium text-emerald-850 dark:text-emerald-100/80 mt-4 block">
                    {{ locale === 'id' ? 'Telah berkolaborasi dan dipercaya oleh berbagai institusi serta perusahaan.' : 'Collaborated and trusted by various institutions and companies.' }}
                </p>
            </div>

            <div
                v-if="status === 'pending'"
                class="flex justify-center items-center py-12"
            >
                <div
                    class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-emerald-600"
                ></div>
            </div>

            <div
                v-else-if="error"
                class="text-center py-12 text-red-500 bg-red-50 dark:bg-red-950/20 rounded-xl p-6 max-w-md mx-auto"
            >
                <p class="font-semibold">Gagal memuat data partner</p>
                <p class="text-sm opacity-80">{{ error?.message }}</p>
                <button
                    @click="refresh"
                    class="mt-4 px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm hover:bg-emerald-700 transition"
                >
                    Coba Lagi
                </button>
            </div>

            <div
                v-else-if="partners.length > 0"
                class="relative w-full overflow-hidden [mask-image:_linear-gradient(to_right,transparent_0,_black_128px,_black_calc(100%-128px),transparent_100%)] [-webkit-mask-image:_linear-gradient(to_right,transparent_0,_black_128px,_black_calc(100%-128px),transparent_100%)]"
            >
                <!-- First Row (Moving Right to Left) -->
                <div class="flex animate-marquee py-3 gap-0">
                        <button
                            v-for="partner in partners"
                            :key="partner.id"
                            @click="openModal(partner)"
                            class="group flex items-center justify-center h-24 px-8 min-w-[180px] mx-3 md:mx-4 rounded-3xl border-2 border-emerald-200 dark:border-emerald-800/40 bg-white/70 dark:bg-emerald-950/50 backdrop-blur-md hover:border-emerald-400 dark:hover:border-emerald-500 hover:bg-emerald-50 dark:hover:bg-emerald-900/80 shadow-[0_4px_20px_rgb(0,0,0,0.03)] hover:shadow-xl hover:-translate-y-2 transition-all duration-300 shrink-0 relative overflow-hidden"
                        >
                            <!-- Decorative glow on hover -->
                            <div class="absolute inset-0 bg-gradient-to-tr from-emerald-100/0 via-emerald-100/0 to-emerald-100/0 group-hover:from-emerald-100/60 group-hover:to-transparent dark:group-hover:from-emerald-700/30 transition-colors duration-500 z-0"></div>
                            
                            <img
                                :src="getImageUrl(partner.logo)"
                                :alt="locale === 'id' ? partner.name_id : partner.name_en"
                                class="h-12 max-w-[120px] object-contain grayscale opacity-60 group-hover:grayscale-0 group-hover:opacity-100 group-hover:scale-110 transition-all duration-500 relative z-10"
                                @error="(e) => ((e.target as HTMLImageElement).src = '/placeholder.jpg')"
                            />
                        </button>
                        <button
                            v-for="partner in partners"
                            :key="'dup-' + partner.id"
                            @click="openModal(partner)"
                            class="group flex items-center justify-center h-24 px-8 min-w-[180px] mx-3 md:mx-4 rounded-3xl border-2 border-emerald-200 dark:border-emerald-800/40 bg-white/70 dark:bg-emerald-950/50 backdrop-blur-md hover:border-emerald-400 dark:hover:border-emerald-500 hover:bg-emerald-50 dark:hover:bg-emerald-900/80 shadow-[0_4px_20px_rgb(0,0,0,0.03)] hover:shadow-xl hover:-translate-y-2 transition-all duration-300 shrink-0 relative overflow-hidden"
                            aria-hidden="true"
                        >
                            <div class="absolute inset-0 bg-gradient-to-tr from-emerald-100/0 via-emerald-100/0 to-emerald-100/0 group-hover:from-emerald-100/60 group-hover:to-transparent dark:group-hover:from-emerald-700/30 transition-colors duration-500 z-0"></div>
                            
                            <img
                                :src="getImageUrl(partner.logo)"
                                :alt="locale === 'id' ? partner.name_id : partner.name_en"
                                class="h-12 max-w-[120px] object-contain grayscale opacity-60 group-hover:grayscale-0 group-hover:opacity-100 group-hover:scale-110 transition-all duration-500 relative z-10"
                                @error="(e) => ((e.target as HTMLImageElement).src = '/placeholder.jpg')"
                            />
                        </button>
                </div>

                <!-- Second Row (Moving Left to Right) -->
                <div class="flex animate-marquee-reverse py-3 gap-0 mt-2">
                        <button
                            v-for="partner in [...partners].reverse()"
                            :key="'rev-' + partner.id"
                            @click="openModal(partner)"
                            class="group flex items-center justify-center h-24 px-8 min-w-[180px] mx-3 md:mx-4 rounded-3xl border-2 border-emerald-200 dark:border-emerald-800/40 bg-white/70 dark:bg-emerald-950/50 backdrop-blur-md hover:border-emerald-400 dark:hover:border-emerald-500 hover:bg-emerald-50 dark:hover:bg-emerald-900/80 shadow-[0_4px_20px_rgb(0,0,0,0.03)] hover:shadow-xl hover:-translate-y-2 transition-all duration-300 shrink-0 relative overflow-hidden"
                        >
                            <div class="absolute inset-0 bg-gradient-to-tr from-emerald-100/0 via-emerald-100/0 to-emerald-100/0 group-hover:from-emerald-100/60 group-hover:to-transparent dark:group-hover:from-emerald-700/30 transition-colors duration-500 z-0"></div>
                            
                            <img
                                :src="getImageUrl(partner.logo)"
                                :alt="locale === 'id' ? partner.name_id : partner.name_en"
                                class="h-12 max-w-[120px] object-contain grayscale opacity-60 group-hover:grayscale-0 group-hover:opacity-100 group-hover:scale-110 transition-all duration-500 relative z-10"
                                @error="(e) => ((e.target as HTMLImageElement).src = '/placeholder.jpg')"
                            />
                        </button>
                        <button
                            v-for="partner in [...partners].reverse()"
                            :key="'rev-dup-' + partner.id"
                            @click="openModal(partner)"
                            class="group flex items-center justify-center h-24 px-8 min-w-[180px] mx-3 md:mx-4 rounded-3xl border-2 border-emerald-200 dark:border-emerald-800/40 bg-white/70 dark:bg-emerald-950/50 backdrop-blur-md hover:border-emerald-400 dark:hover:border-emerald-500 hover:bg-emerald-50 dark:hover:bg-emerald-900/80 shadow-[0_4px_20px_rgb(0,0,0,0.03)] hover:shadow-xl hover:-translate-y-2 transition-all duration-300 shrink-0 relative overflow-hidden"
                            aria-hidden="true"
                        >
                            <div class="absolute inset-0 bg-gradient-to-tr from-emerald-100/0 via-emerald-100/0 to-emerald-100/0 group-hover:from-emerald-100/60 group-hover:to-transparent dark:group-hover:from-emerald-700/30 transition-colors duration-500 z-0"></div>
                            
                            <img
                                :src="getImageUrl(partner.logo)"
                                :alt="locale === 'id' ? partner.name_id : partner.name_en"
                                class="h-12 max-w-[120px] object-contain grayscale opacity-60 group-hover:grayscale-0 group-hover:opacity-100 group-hover:scale-110 transition-all duration-500 relative z-10"
                                @error="(e) => ((e.target as HTMLImageElement).src = '/placeholder.jpg')"
                            />
                        </button>
                </div>
            </div>

            <!-- Partner Detail Modal -->
            <UModal v-model:open="isModalOpen">
                <template #content>
                    <UCard v-if="selectedPartner" class="!bg-white dark:!bg-emerald-950 ring-1 !ring-emerald-100 dark:!ring-emerald-900 divide-y !divide-emerald-100 dark:!divide-emerald-800/50">
                        <template #header>
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-serif font-bold text-emerald-950 dark:text-emerald-50">
                                    {{ locale === 'id' ? selectedPartner.name_id : selectedPartner.name_en }}
                                </h3>
                                <UButton color="gray" variant="ghost" icon="i-heroicons-x-mark-20-solid" class="-my-1" @click="isModalOpen = false" />
                            </div>
                        </template>

                        <div class="flex flex-col items-center p-4">
                            <div class="w-32 h-32 rounded-2xl bg-white dark:bg-emerald-900/20 p-2 flex items-center justify-center border border-emerald-50 dark:border-emerald-900/20 mb-6 shrink-0">
                                <img
                                    v-if="selectedPartner.logo"
                                    :src="getImageUrl(selectedPartner.logo)"
                                    :alt="locale === 'id' ? selectedPartner.name_id : selectedPartner.name_en"
                                    class="max-w-full max-h-full object-contain"
                                    @error="(e) => ((e.target as HTMLImageElement).src = '/placeholder.jpg')"
                                />
                                <UIcon
                                    v-else
                                    name="i-lucide-building-2"
                                    class="w-12 h-12 text-emerald-600 dark:text-emerald-400"
                                />
                            </div>
                            <p class="text-sm text-slate-600 dark:text-emerald-100/80 leading-relaxed text-center whitespace-pre-line">
                                {{ locale === 'id' ? selectedPartner.description_id : selectedPartner.description_en }}
                            </p>
                        </div>
                    </UCard>
                </template>
            </UModal>

            <div class="flex justify-center items-center pt-6">
                <NuxtLink
                    :to="localePath('/partner')"
                    class="px-6 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-medium shadow-md hover:shadow-lg transition flex items-center gap-2 group"
                >
                    {{
                        locale === "en"
                            ? "View All Partners"
                            : "Lihat Semua Partner"
                    }}
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="2"
                        stroke="currentColor"
                        class="w-4 h-4 transform group-hover:translate-x-1 transition-transform"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"
                        />
                    </svg>
                </NuxtLink>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, computed, watch } from "vue";
const { t, locale } = useI18n();
const localePath = useLocalePath();
const config = useRuntimeConfig();
const backendUrl = config.public.sanctum?.baseUrl || "http://127.0.0.1:8000";

const client = useSanctumClient();
const selectedPartnerId = ref<number | null>(null);

const isModalOpen = ref(false);
const selectedPartner = ref<any>(null);

const openModal = (partner: any) => {
    selectedPartner.value = partner;
    isModalOpen.value = true;
};

const {
    data: apiResponse,
    status,
    error,
    refresh,
} = await useAsyncData("partners-preview", () => client("/api/partners"));

const partners = computed(() => {
    if (!apiResponse.value) return [];
    const responseData = (apiResponse.value as any).data;
    if (!responseData) return [];
    return Array.isArray(responseData.data) ? responseData.data : [];
});

const activePartner = computed(() => {
    if (partners.value.length === 0) return null;

    if (selectedPartnerId.value === null) {
        return partners.value[0];
    }

    return (
        partners.value.find((p) => p.id === selectedPartnerId.value) ||
        partners.value[0]
    );
});

watch(
    partners,
    (newPartners) => {
        if (newPartners.length > 0 && selectedPartnerId.value === null) {
            selectedPartnerId.value = newPartners[0].id;
        }
    },
    { immediate: true },
);

const getImageUrl = (path: string | null | undefined) => {
    if (!path) return "/placeholder.jpg";
    if (path.startsWith("http://") || path.startsWith("https://")) return path;
    const cleanPath = path.startsWith("/") ? path.substring(1) : path;
    return `${backendUrl}/${cleanPath}`;
};
</script>

<style scoped>
@keyframes marquee {
    0% {
        transform: translate3d(0, 0, 0);
    }
    100% {
        transform: translate3d(-50%, 0, 0);
    }
}

@keyframes marquee-reverse {
    0% {
        transform: translate3d(-50%, 0, 0);
    }
    100% {
        transform: translate3d(0, 0, 0);
    }
}

.animate-marquee {
    display: flex;
    width: max-content;
    animation: marquee 30s linear infinite;
}

.animate-marquee-reverse {
    display: flex;
    width: max-content;
    animation: marquee-reverse 35s linear infinite;
}

.animate-marquee:hover,
.animate-marquee-reverse:hover {
    animation-play-state: paused;
}
</style>
