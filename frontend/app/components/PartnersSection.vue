<template>
    <div
        class="w-full mx-auto bg-gradient-to-b from-emerald-50 via-emerald-100 to-emerald-800/40 dark:from-gray-950 dark:via-emerald-950 dark:to-slate-950 px-4 py-16 border-t border-emerald-800/20 dark:border-emerald-500/20"
    >
        <div class="max-w-7xl mx-auto pb-6">
            <h2
                class="text-3xl md:text-5xl font-serif font-bold text-center mb-12 text-gray-800 dark:text-gray-100"
            >
                {{ t("partners.title") }}
            </h2>

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
                v-else-if="!partners || partners.length === 0"
                class="text-center py-12 text-gray-500"
            >
                Tidak ada data partner saat ini.
            </div>

            <div v-else class="max-w-5xl mx-auto space-y-12">
                <div
                    v-if="activePartner"
                    class="flex flex-col md:flex-row items-center gap-8 p-8 bg-white/80 dark:bg-gray-900/80 backdrop-blur-md rounded-2xl border border-emerald-800/10 dark:border-emerald-500/10 shadow-xl transition-all duration-500"
                >
                    <div
                        class="w-full md:w-1/3 flex justify-center p-4 bg-emerald-50/50 dark:bg-gray-950/50 rounded-xl h-48 items-center"
                    >
                        <img
                            :src="getImageUrl(activePartner?.logo)"
                            :alt="
                                locale === 'id'
                                    ? activePartner?.name_id
                                    : activePartner?.name_en
                            "
                            class="max-h-full max-w-full object-contain transition-transform duration-300 hover:scale-105"
                            @error="
                                (e) =>
                                    ((e.target as HTMLImageElement).src =
                                        '/placeholder.jpg')
                            "
                        />
                    </div>

                    <div
                        class="w-full md:w-2/3 text-center md:text-left space-y-3"
                    >
                        <span
                            class="text-xs font-bold uppercase tracking-widest text-emerald-600 dark:text-emerald-400"
                        >
                            Partner Terpilih
                        </span>
                        <h3
                            class="text-2xl font-bold text-gray-900 dark:text-gray-100 font-serif"
                        >
                            {{
                                locale === "id"
                                    ? activePartner?.name_id
                                    : activePartner?.name_en
                            }}
                        </h3>
                        <p
                            class="text-sm md:text-base text-gray-600 dark:text-gray-300 leading-relaxed"
                        >
                            {{
                                locale === "id"
                                    ? activePartner?.description_id
                                    : activePartner?.description_en
                            }}
                        </p>
                    </div>
                </div>

                <div class="space-y-4">
                    <p
                        class="text-center text-sm font-medium text-gray-500 dark:text-gray-400"
                    >
                        Silakan pilih mitra di bawah untuk melihat informasi
                        detail:
                    </p>

                    <div class="flex flex-wrap justify-center gap-4">
                        <button
                            v-for="partner in partners"
                            :key="partner?.id"
                            @click="selectedPartnerId = partner?.id"
                            :class="[
                                'flex items-center gap-3 px-4 py-3 rounded-xl border transition-all duration-300',
                                selectedPartnerId === partner?.id
                                    ? 'bg-emerald-600 border-emerald-600 text-white shadow-md scale-105 ring-2 ring-emerald-500/20'
                                    : 'bg-white dark:bg-gray-900 border-emerald-800/10 dark:border-emerald-500/10 text-gray-700 dark:text-gray-300 hover:border-emerald-500 dark:hover:border-emerald-400 hover:bg-emerald-50/50 dark:hover:bg-emerald-950/20',
                            ]"
                        >
                            <img
                                :src="getImageUrl(partner?.logo)"
                                :alt="
                                    locale === 'id'
                                        ? partner?.name_id
                                        : partner?.name_en
                                "
                                class="w-6 h-6 object-contain rounded"
                                :class="{
                                    'brightness-0 invert':
                                        selectedPartnerId === partner?.id,
                                }"
                                @error="
                                    (e) =>
                                        ((e.target as HTMLImageElement).src =
                                            '/placeholder.jpg')
                                "
                            />
                            <span class="text-xs md:text-sm font-semibold">
                                {{
                                    locale === "id"
                                        ? partner?.name_id
                                        : partner?.name_en
                                }}
                            </span>
                        </button>
                    </div>
                </div>

                <div class="flex justify-center items-center pt-6">
                    <NuxtLink
                        :to="localePath('/partner')"
                        class="px-6 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-medium shadow-md hover:shadow-lg transition flex items-center gap-2 group"
                    >
                        {{ locale === 'en' ? 'View All Partners' : 'Lihat Semua Partner' }}
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
    </div>
</template>

<script setup lang="ts">
import { ref, computed, watch } from "vue";

const { t, locale } = useI18n();
const localePath = useLocalePath();

const config = useRuntimeConfig();
const backendUrl = config.public.sanctum?.baseUrl || "http://127.0.0.1:8000";

const client = useSanctumClient();

// State untuk menyimpan ID partner yang sedang aktif dipilih oleh pengguna
const selectedPartnerId = ref<number | null>(null);

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

// Watcher untuk mengeset ID awal ketika data baru berhasil masuk pertama kali
watch(
    partners,
    (newPartners) => {
        if (newPartners.length > 0 && selectedPartnerId.value === null) {
            selectedPartnerId.value = newPartners[0].id;
        }
    },
    { immediate: true },
);

// Fungsi pembuat URL Gambar
const getImageUrl = (path: string | null | undefined) => {
    if (!path) return "/placeholder.jpg";
    if (path.startsWith("http://") || path.startsWith("https://")) return path;

    const cleanPath = path.startsWith("/") ? path.substring(1) : path;
    return `${backendUrl}/${cleanPath}`;
};
</script>
