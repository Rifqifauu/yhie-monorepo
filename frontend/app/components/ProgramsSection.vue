<template>
    <div
        class="relative w-full mx-auto px-4 py-24 overflow-hidden transition-colors duration-500"
    >
        <div
            class="absolute inset-0 bg-white/40 dark:bg-gray-800/20 transition-colors duration-500 z-10"
        ></div>
        <div
            class="absolute inset-0 w-full h-full bg-fixed bg-center bg-cover bg-no-repeat opacity-[0.5] dark:opacity-[0.3] -z-10"
            style="background-image: url(&quot;/gunungslamet.png&quot;)"
        ></div>

        <div class="max-w-6xl mx-auto relative z-10">
            <div class="text-center mb-16 max-w-5xl mx-auto p-4 rounded-2xl">
                <h2
                    class="text-4xl md:text-5xl font-bold mb-4 font-serif text-gray-900 dark:text-gray-50 tracking-tight"
                >
                    {{ t("programs.title") }}
                </h2>
                <div
                    class="w-20 h-1 bg-gradient-to-r from-emerald-800 to-emerald-500 mx-auto rounded-full"
                ></div>
                <span
                    class="text-lg font-medium text-emerald-850 dark:text-white mt-4 block"
                >
                    {{ t("programs.description") }}
                </span>
            </div>

            <div
                v-if="status === 'pending'"
                class="flex flex-col items-center justify-center py-20 gap-4"
            >
                <UIcon
                    name="i-heroicons-arrow-path"
                    class="w-12 h-12 animate-spin text-emerald-600 dark:text-emerald-400"
                />
                <span
                    class="text-sm font-medium tracking-wide text-emerald-700 dark:text-emerald-300"
                >
                    Memuat data eksklusif...
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
                            'backdrop-blur-md bg-red-50/50 dark:bg-red-950/20 border border-red-200 dark:border-red-900/50 rounded-2xl',
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
                    class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-10 max-w-5xl mx-auto"
                >
                    <div
                        v-for="program in data"
                        :key="program.id"
                        class="relative w-full h-[520px] rounded-[2.5rem] flex flex-col p-4 group transition-all duration-500 hover:-translate-y-2 bg-white/40 dark:bg-gray-900/40 backdrop-blur-xl border border-white/50 dark:border-white/10 shadow-[0_8px_32px_0_rgba(31,38,135,0.07)] hover:shadow-[0_8px_32px_0_rgba(31,38,135,0.15)]"
                    >
                        <div
                            class="relative w-full h-[55%] rounded-[2rem] overflow-hidden mb-5"
                        >
                            <img
                                :src="getImageUrl(program.image_path)"
                                :alt="getLocalizedData(program, 'title')"
                                class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                                loading="lazy"
                                @error="handleImageError"
                            />
                        </div>

                        <div class="flex-1 flex flex-col px-4 pb-2">
                            <h3
                                class="text-2xl md:text-3xl font-semibold text-gray-900 dark:text-white leading-tight mb-2 line-clamp-1"
                            >
                                {{ getLocalizedData(program, "title") }}
                            </h3>

                            <p
                                class="text-gray-700 dark:text-gray-300 text-sm leading-relaxed line-clamp-2"
                            >
                                {{ getLocalizedData(program, "description") }}
                            </p>

                            <div
                                class="flex flex-wrap items-center gap-3 mt-auto mb-4"
                            >
                                <div
                                    class="px-4 py-1.5 rounded-full bg-white/50 dark:bg-black/30 backdrop-blur-md text-gray-800 dark:text-white text-xs font-semibold border border-white/40 dark:border-white/10 shadow-sm"
                                >
                                    {{
                                        formatPrice(
                                            getLocalizedData(program, "price"),
                                        )
                                    }}
                                </div>
                                <div
                                    class="px-4 py-1.5 rounded-full bg-white/50 dark:bg-black/30 backdrop-blur-md text-gray-800 dark:text-white text-xs font-semibold border border-white/40 dark:border-white/10 shadow-sm flex items-center gap-1.5"
                                >
                                    <UIcon
                                        name="i-heroicons-star-solid"
                                        class="w-3.5 h-3.5 text-amber-500 dark:text-amber-400"
                                    />
                                    Unggulan
                                </div>
                            </div>

                            <NuxtLink
                                :to="
                                    localePath(
                                        `/program/${locale === 'id' ? program.slug_id : program.slug_en}`,
                                    )
                                "
                                class="block w-full"
                            >
                                <button
                                    class="w-full bg-emerald-700 dark:bg-emerald-500 cursor-pointer hover:bg-emerald-900 dark:hover:bg-gray-100 text-white dark:text-gray-900 font-semibold py-3.5 rounded-full transition-colors duration-300 text-sm shadow-md"
                                >
                                    {{
                                        locale === "id"
                                            ? "Pesan sekarang"
                                            : "Reserve now"
                                    }}
                                </button>
                            </NuxtLink>
                        </div>
                    </div>
                </div>

                <div v-else class="text-center py-12">
                    <EmptyData
                        title="program"
                        description="Belum ada program yang tersedia saat ini."
                        icon="i-lucide-box"
                    />
                </div>

                <div
                    v-if="data && data.length > 0"
                    class="mt-16 flex justify-center"
                >
                    <UButton
                        size="xl"
                        color="white"
                        variant="solid"
                        class="rounded-full px-10 py-4 text-sm font-semibold shadow-lg hover:shadow-xl text-gray-900 bg-white/80 dark:bg-white/10 dark:text-white backdrop-blur-md hover:bg-white dark:hover:bg-white/20 transition-all duration-300 border border-white/50 dark:border-white/10"
                        :to="localePath('/program')"
                    >
                        {{
                            locale === "id"
                                ? "Lihat Program Lainnya"
                                : "View All Programs"
                        }}
                    </UButton>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { useI18n } from "vue-i18n"; // Pastikan ini diimport atau menggunakan auto-imports bawaan Nuxt

// Definisikan tipe data untuk program agar lebih aman secara TypeScript
interface Program {
    id: string | number;
    image_path: string;
    title_id: string;
    title_en: string;
    description_id: string;
    description_en: string;
    price_id: number | string;
    price_en: number | string;
    slug_id: string;
    slug_en: string;
}

const { t, locale } = useI18n();
const localePath = useLocalePath();
const client = useSanctumClient();
const fileUrl = useFileUrl();

// Fetch Data
const { data, status, error, refresh } = await useAsyncData<Program[]>(
    "programs",
    () => client("/api/programs"),
    {
        transform: (response: any) => {
            const programList = response?.data?.data || [];
            return programList.slice(0, 2);
        },
    },
);

// Helpers
const getLocalizedData = (
    program: Program,
    field: "title" | "description" | "price",
) => {
    const key = `${field}_${locale.value}` as keyof Program;
    return program[key];
};

const getImageUrl = (path: string) => (path ? fileUrl(path) : "/placeholder.jpg");

const handleImageError = (e: Event) => {
    const target = e.target as HTMLImageElement;
    if (target) target.src = "/placeholder.jpg";
};

const formatPrice = (price: number | string) => {
    const numericPrice = typeof price === "string" ? parseFloat(price) : price;
    if (isNaN(numericPrice)) return price;

    const options =
        locale.value === "id"
            ? { style: "currency", currency: "IDR", minimumFractionDigits: 0 }
            : { style: "currency", currency: "USD" };

    return new Intl.NumberFormat(
        locale.value === "id" ? "id-ID" : "en-US",
        options,
    ).format(numericPrice);
};
</script>
