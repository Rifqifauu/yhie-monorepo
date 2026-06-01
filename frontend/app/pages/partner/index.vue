<template>
    <div class="flex justify-center items-center mx-8 md:my-16 my-8 md:mx-16">
        <h1 class="text-3xl font-bold md:text-5xl font-serif">
            {{ t("partners.title") }}
        </h1>
    </div>

    <div
        class="flex flex-col justify-center items-center mx-8 md:mb-16 mb-8 md:mx-16 gap-8"
    >
        <div
            v-if="status === 'pending'"
            class="flex flex-col items-center justify-center py-16 gap-4"
        >
            <UIcon
                name="i-heroicons-arrow-path"
                class="w-12 h-12 animate-spin text-emerald-600 dark:text-emerald-400"
            />
            <span
                class="text-sm font-medium text-emerald-700 dark:text-emerald-300"
            >
                Memuat data program...
            </span>
        </div>

        <div v-else-if="error" class="max-w-xl mx-auto my-6">
            <UAlert
                icon="i-heroicons-exclamation-triangle"
                color="red"
                variant="soft"
                title="Gagal memuat data"
                description="Terjadi kesalahan saat mengambil data partner dari server."
                :ui="{
                    wrapper:
                        'backdrop-blur-md bg-red-50/50 dark:bg-red-950/20 border border-red-200 dark:border-red-900/50',
                }"
            >
                <template #actions>
                    <UButton
                        size="sm"
                        color="red"
                        variant="solid"
                        icon="i-heroicons-arrow-path-20-solid"
                        @click="refresh"
                    >
                        Coba Lagi
                    </UButton>
                </template>
            </UAlert>
        </div>

        <div v-else class="w-full">
            <div
                v-if="partners && partners.length > 0"
                class="grid grid-cols-1 md:grid-cols-3 gap-8 w-full mb-8"
            >
                <UCard
                    v-for="partner in partners"
                    :key="partner.id"
                    class="group transition-all duration-500 hover:-translate-y-2 overflow-hidden shadow-2xl rounded-xl"
                    :ui="{
                        base: 'backdrop-blur-md bg-white/40 dark:bg-gray-900/40 border border-white/40 dark:border-gray-800/50 shadow-xl hover:shadow-2xl hover:border-emerald-500/30 dark:hover:border-emerald-400/30',
                        body: { padding: 'px-6 py-5' },
                        header: {
                            padding:
                                'p-0 border-b border-gray-200/30 dark:border-gray-800/30',
                        },
                    }"
                >
                    <template #header>
                        <div
                            class="relative w-full h-48 overflow-hidden bg-gray-100 dark:bg-gray-800"
                        >
                            <NuxtImage
                                :src="getImageUrl(partner.logo)"
                                :alt="
                                    locale === 'id'
                                        ? partner.name_id
                                        : partner.name_en
                                "
                                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                                placeholder
                                loading="lazy"
                            />
                        </div>
                    </template>

                    <div class="space-y-3">
                        <h3
                            class="text-xl font-bold font-serif text-gray-900 dark:text-white line-clamp-2 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors duration-300"
                        >
                            {{
                                locale === "id"
                                    ? partner.name_id
                                    : partner.name_en
                            }}
                        </h3>

                        <p
                            class="text-gray-600 dark:text-gray-300 leading-relaxed text-sm line-clamp-4"
                        >
                            {{
                                locale === "id"
                                    ? partner.description_id
                                    : partner.description_en
                            }}
                        </p>
                    </div>
                </UCard>
            </div>

            <div v-else class="text-center py-12">
                <UAlert
                    icon="i-heroicons-information-circle"
                    color="gray"
                    variant="soft"
                    title="Kosong"
                    description="Belum ada partner yang tersedia saat ini."
                    class="max-w-md mx-auto backdrop-blur-md bg-white/30 dark:bg-gray-900/30"
                />
            </div>

            <div v-if="totalItems > 0" class="flex justify-center mt-12">
                <UPagination
                    v-model:page="page"
                    :page-count="perPage"
                    :total="totalItems"
                    active-color="primary"
                    active-variant="subtle"
                />
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
const { t, locale } = useI18n();
const client = useSanctumClient();

// Variable reactive untuk halaman aktif (Sesuai dokumentasi kamu)
const page = ref(1);
const perPage = ref(10);

// Mengambil data dari backend Laravel
const {
    data: apiResponse,
    status,
    error,
    refresh,
} = await useAsyncData("partners", () =>
    client("/api/partners", {
        params: {
            page: page.value, // Mengirim parameter ?page= ke backend
        },
    }),
);

// PENTING: Mengawasi variabel 'page'. Jika diklik, jalankan fungsi refresh() untuk mengambil data baru
watch(page, () => {
    refresh();
});

// Mengolah array data partner
const partners = computed(() => {
    if (!apiResponse.value) return [];
    const responsePayload = (apiResponse.value as any).data;
    return responsePayload && Array.isArray(responsePayload.data)
        ? responsePayload.data
        : [];
});

// Mengambil info total data dari response API Laravel (100)
const totalItems = computed(() => {
    if (!apiResponse.value) return 0;
    const responsePayload = (apiResponse.value as any).data;
    return responsePayload?.total || 0;
});

// Helper URL Gambar
const getImageUrl = (logoPath: string) => {
    if (!logoPath) return "/placeholder.png";
    return logoPath.startsWith("http") ? logoPath : `/storage/${logoPath}`;
};
</script>
