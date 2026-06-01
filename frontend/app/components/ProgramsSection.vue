<template>
    <div
        class="w-full mx-auto bg-gradient-to-b from-emerald-50 via-emerald-100 to-emerald-800/40 dark:from-gray-950 dark:via-emerald-950 dark:to-slate-950 px-4 py-16 border-t border-emerald-800/20 dark:border-emerald-500/20"
    >
        <div class="max-w-7xl mx-auto pb-6">
            <h2
                class="text-3xl md:text-5xl font-bold font-serif mb-12 text-center text-gray-900 dark:text-gray-50 tracking-tight"
            >
                {{ t("programs.title") }}
            </h2>

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
                    description="Terjadi kesalahan saat mengambil data program dari server."
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

            <div v-else>
                <div
                    v-if="data && data.length > 0"
                    class="grid grid-cols-1 md:grid-cols-3 gap-8"
                >
                    <UCard
                        v-for="program in data"
                        :key="program.id"
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
                                    :src="getImageUrl(program.image_path)"
                                    :alt="
                                        locale === 'id'
                                            ? program.title_id
                                            : program.title_en
                                    "
                                    class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                                    placeholder
                                    loading="lazy"
                                />
                                <div
                                    class="absolute bottom-3 right-3 backdrop-blur-md bg-emerald-600/80 dark:bg-emerald-500/80 text-white px-3 py-1 rounded-full text-xs font-semibold shadow-sm"
                                >
                                    {{
                                        formatPrice(
                                            locale === "id"
                                                ? program.price_id
                                                : program.price_en,
                                        )
                                    }}
                                </div>
                            </div>
                        </template>

                        <div class="space-y-3">
                            <h3
                                class="text-xl font-bold font-serif text-gray-900 dark:text-white line-clamp-2 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors duration-300"
                            >
                                {{
                                    locale === "id"
                                        ? program.title_id
                                        : program.title_en
                                }}
                            </h3>

                            <p
                                class="text-gray-600 dark:text-gray-300 leading-relaxed text-sm line-clamp-4"
                            >
                                {{
                                    locale === "id"
                                        ? program.description_id
                                        : program.description_en
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
                        description="Belum ada program yang tersedia saat ini."
                        class="max-w-md mx-auto backdrop-blur-md bg-white/30 dark:bg-gray-900/30"
                    />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
const { t, locale } = useI18n();

const config = useRuntimeConfig();
const backendUrl = config.public.sanctum?.baseUrl || "http://127.0.0.1:8000";

const client = useSanctumClient();

const { data, status, error, refresh } = await useAsyncData(
    "programs",
    () => client("/api/programs"),
    {
        transform: (response: any) => {
            const programList = response?.data?.data || [];
            return programList.slice(0, 3);
        },
    },
);

const getImageUrl = (path: string) => {
    if (!path) return "/placeholder.jpg";
    if (path.startsWith("http")) return path;
    return `${backendUrl}/${path.startsWith("/") ? path.substring(1) : path}`;
};

// Helper format mata uang sederhana sesuai locale
const formatPrice = (price: number | string) => {
    const numericPrice = typeof price === "string" ? parseFloat(price) : price;
    if (isNaN(numericPrice)) return price;

    if (locale.value === "id") {
        return new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
            minimumFractionDigits: 0,
        }).format(numericPrice);
    } else {
        return new Intl.NumberFormat("en-US", {
            style: "currency",
            currency: "USD",
        }).format(numericPrice);
    }
};
</script>
