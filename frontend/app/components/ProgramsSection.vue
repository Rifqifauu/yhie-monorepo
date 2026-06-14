<template>
    <div
        class="relative w-full mx-auto bg-gradient-to-b from-slate-50 via-white to-white dark:from-emerald-950 dark:via-gray-900 dark:to-gray-900 px-4 py-20 overflow-hidden transition-colors duration-500"
    >
        <!-- Decorative blobs -->
        <div
            class="absolute top-0 left-1/4 w-80 h-80 bg-emerald-400/10 dark:bg-emerald-500/5 rounded-full blur-3xl pointer-events-none"
        ></div>
        <div
            class="absolute bottom-0 right-1/6 w-96 h-96 bg-amber-400/8 dark:bg-amber-500/5 rounded-full blur-3xl pointer-events-none"
        ></div>

        <!-- Subtle dot pattern -->
        <div
            class="absolute inset-0 opacity-[0.03] dark:opacity-[0.02] pointer-events-none"
            style="
                background-image: radial-gradient(circle, currentColor 1px, transparent 1px);
                background-size: 24px 24px;
            "
        ></div>

        <!-- Decorative 8-pointed star -->
        <svg class="absolute top-10 right-10 w-32 h-32 text-emerald-500/[0.06] dark:text-emerald-400/[0.04] pointer-events-none" viewBox="0 0 100 100" fill="currentColor">
            <polygon points="50,0 61,35 100,35 68,57 79,91 50,70 21,91 32,57 0,35 39,35" />
        </svg>

        <!-- Decorative crescent -->
        <svg class="absolute bottom-16 left-8 w-24 h-24 text-amber-500/[0.07] dark:text-amber-400/[0.04] pointer-events-none" viewBox="0 0 100 100" fill="currentColor">
            <path d="M50 10a40 40 0 1 0 0 80 32 32 0 1 1 0-80z" />
        </svg>

        <div class="max-w-7xl mx-auto pb-6 relative z-10">
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
                    class="grid grid-cols-1 md:grid-cols-2 gap-10 max-w-4xl mx-auto"
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
                                class="relative w-full h-56 overflow-hidden bg-gray-100 dark:bg-gray-800"
                            >
                                <img
                                    :src="getImageUrl(program.image_path)"
                                    :alt="
                                        locale === 'id'
                                            ? program.title_id
                                            : program.title_en
                                    "
                                    class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                                    loading="lazy"
                                    @error="handleImageError"
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
                    <EmptyData
                        title="program"
                        description="Belum ada program yang tersedia saat ini."
                        icon="i-lucide-box"
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
            return programList.slice(0, 2);
        },
    },
);

const getImageUrl = (path: string) => {
    if (!path) return "/placeholder.jpg";
    if (path.startsWith("http")) return path;
    return `${backendUrl}/${path.startsWith("/") ? path.substring(1) : path}`;
};

const handleImageError = (e: Event) => {
    const target = e.target as HTMLImageElement;
    if (target) target.src = '/placeholder.jpg';
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
