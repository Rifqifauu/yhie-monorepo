<template>
    <div class="bg-slate-50 dark:bg-emerald-950 text-slate-900 dark:text-white">
        <section
            class="relative overflow-hidden bg-gradient-to-br from-emerald-50 via-white to-amber-50 dark:from-emerald-950 dark:via-emerald-950 dark:to-amber-950/40 border-b border-emerald-100/70 dark:border-emerald-800/60"
        >
            <div
                class="absolute -top-24 -right-24 w-80 h-80 bg-amber-400/20 rounded-full blur-3xl"
            ></div>
            <div
                class="absolute -bottom-28 -left-28 w-96 h-96 bg-emerald-400/20 rounded-full blur-3xl"
            ></div>

            <div
                class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24"
            >
                <div class="grid gap-10 lg:grid-cols-2 items-center">
                    <div data-aos="fade-up">
                        <span
                            class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-[0.3em] text-amber-600 dark:text-amber-400"
                        >
                            Programs
                        </span>
                        <h1
                            class="mt-4 text-3xl md:text-5xl font-serif font-extrabold tracking-tight text-emerald-950 dark:text-emerald-50"
                        >
                            {{ t("programs.title") }}
                        </h1>
                        <p
                            class="mt-4 text-lg text-slate-600 dark:text-emerald-100/80 leading-relaxed"
                        >
                            {{ t("programs.description") }}
                        </p>
                        <div class="mt-6 flex flex-wrap gap-3 text-sm">
                            <span
                                class="px-4 py-2 rounded-full bg-white/70 dark:bg-emerald-900/40 border border-emerald-200/70 dark:border-emerald-800/60"
                            >
                                Total {{ totalItems }}
                            </span>
                            <span
                                class="px-4 py-2 rounded-full bg-white/70 dark:bg-emerald-900/40 border border-emerald-200/70 dark:border-emerald-800/60"
                            >
                                Halaman {{ page }} dari {{ totalPages }}
                            </span>
                            <span
                                class="px-4 py-2 rounded-full bg-white/70 dark:bg-emerald-900/40 border border-emerald-200/70 dark:border-emerald-800/60"
                            >
                                Sertifikasi resmi
                            </span>
                        </div>
                    </div>

                    <div
                        data-aos="fade-left"
                        class="w-full bg-white/70 dark:bg-emerald-900/50 backdrop-blur-xl border border-emerald-200/70 dark:border-emerald-800/60 rounded-3xl p-6 shadow-xl"
                    >
                        <div class="flex flex-col sm:flex-row gap-3">
                            <UInput
                                v-model="searchInput"
                                size="lg"
                                placeholder="Cari program (judul)"
                                class="flex-1"
                                @keyup.enter="applySearch"
                            />
                            <button
                                class="px-6 py-3 rounded-xl bg-gradient-to-r from-emerald-500 to-emerald-600 text-white font-semibold shadow-lg hover:shadow-emerald-500/30 transition-all"
                                @click="applySearch"
                            >
                                Cari
                            </button>
                        </div>
                        <div
                            class="mt-4 flex flex-wrap items-center justify-between gap-2 text-sm text-slate-600 dark:text-emerald-100/70"
                        >
                            <span>
                                Menampilkan {{ fromItem }}-{{ toItem }} dari
                                {{ totalItems }} program
                            </span>
                            <button
                                v-if="searchTerm"
                                class="text-amber-600 hover:text-amber-700 dark:text-amber-400 dark:hover:text-amber-300 font-semibold"
                                @click="clearSearch"
                            >
                                Reset
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-14 lg:py-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div
                    v-if="pending"
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
                >
                    <USkeleton
                        v-for="n in 6"
                        :key="n"
                        class="h-80 rounded-3xl"
                    />
                </div>

                <UAlert
                    v-else-if="error"
                    icon="i-heroicons-exclamation-triangle"
                    color="red"
                    variant="soft"
                    title="Gagal memuat program"
                    description="Silakan coba lagi beberapa saat."
                    class="max-w-xl mx-auto"
                >
                    <template #actions>
                        <UButton size="sm" color="red" @click="refresh">
                            Coba lagi
                        </UButton>
                    </template>
                </UAlert>

                <div v-else-if="!programs.length" class="text-center py-16">
                    <div
                        class="mx-auto w-16 h-16 rounded-2xl bg-emerald-100 dark:bg-emerald-900/60 flex items-center justify-center"
                    >
                        <UIcon
                            name="i-lucide-library"
                            class="w-8 h-8 text-emerald-600"
                        />
                    </div>
                    <p class="mt-4 text-slate-500 dark:text-emerald-100/70">
                        Belum ada program untuk kata kunci ini.
                    </p>
                </div>

                <div
                    v-else
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
                >
                    <NuxtLink
                        v-for="program in programs"
                        :key="program.id"
                        :to="localePath(`/program/${slugOf(program)}`)"
                        class="group relative overflow-hidden rounded-3xl border border-emerald-200/70 dark:border-emerald-800/60 bg-white/70 dark:bg-emerald-950/40 shadow-sm hover:-translate-y-1 hover:shadow-2xl transition-all"
                    >
                        <div class="relative aspect-[4/3] overflow-hidden">
                            <NuxtImg
                                v-if="program.image_path"
                                :src="imageUrl(program.image_path)"
                                :alt="titleOf(program)"
                                width="640"
                                height="480"
                                format="webp"
                                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                            />
                            <div
                                v-else
                                class="w-full h-full flex items-center justify-center bg-emerald-100 dark:bg-emerald-900/60"
                            >
                                <UIcon
                                    name="i-lucide-image"
                                    class="w-10 h-10 text-emerald-500"
                                />
                            </div>
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-emerald-950/40 via-transparent to-transparent"
                            ></div>
                            <span
                                class="absolute top-4 left-4 px-3 py-1 rounded-full text-xs font-semibold bg-white/80 text-emerald-800"
                                >Program</span
                            >
                        </div>

                        <div class="p-5">
                            <div
                                class="flex items-center justify-between text-sm mb-2"
                            >
                                <span
                                    class="text-emerald-600 dark:text-emerald-300"
                                >
                                    Harga
                                </span>
                                <span
                                    class="font-semibold text-emerald-800 dark:text-amber-300"
                                >
                                    {{ formatPrice(priceOf(program)) }}
                                </span>
                            </div>
                            <h3
                                class="text-xl font-serif font-bold text-slate-900 dark:text-emerald-50 line-clamp-2"
                            >
                                {{ titleOf(program) }}
                            </h3>
                            <p
                                class="mt-2 text-sm text-slate-600 dark:text-emerald-100/70 line-clamp-3"
                            >
                                {{ descOf(program) }}
                            </p>
                            <div
                                class="mt-4 flex items-center justify-between text-sm font-semibold text-emerald-700"
                            >
                                <span class="dark:text-emerald-200">
                                    Lihat detail
                                </span>
                                <UIcon
                                    name="i-lucide-arrow-right"
                                    class="w-4 h-4"
                                />
                            </div>
                        </div>
                    </NuxtLink>
                </div>

                <div v-if="totalPages > 1" class="flex justify-center mt-12">
                    <div
                        class="flex items-center gap-2 bg-white/80 dark:bg-emerald-950/50 border border-emerald-200/70 dark:border-emerald-800/60 rounded-2xl px-3 py-2 shadow-sm"
                    >
                        <UButton
                            icon="i-lucide-chevron-left"
                            variant="outline"
                            :disabled="page === 1"
                            @click="changePage(page - 1)"
                        />
                        <div
                            v-for="(item, index) in pageItems"
                            :key="`page-${index}`"
                        >
                            <span
                                v-if="item === 'ellipsis'"
                                class="w-9 h-9 grid place-items-center text-slate-400"
                                >...</span
                            >
                            <button
                                v-else
                                class="w-9 h-9 rounded-xl text-sm font-semibold transition-colors"
                                :class="
                                    item === page
                                        ? 'bg-emerald-700 text-white'
                                        : 'text-slate-600 hover:bg-emerald-100 dark:text-emerald-100/80 dark:hover:bg-emerald-900/60'
                                "
                                @click="changePage(item as number)"
                            >
                                {{ item }}
                            </button>
                        </div>
                        <UButton
                            icon="i-lucide-chevron-right"
                            variant="outline"
                            :disabled="page === totalPages"
                            @click="changePage(page + 1)"
                        />
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<script setup lang="ts">
const config = useRuntimeConfig();
const base = config.public.apiBase;
const { locale, t } = useI18n();
const localePath = useLocalePath();

const page = ref(1);
const searchInput = ref("");
const search = ref("");

const searchTerm = computed(() => search.value.trim());

const { data, pending, error, refresh } = await useFetch<any>(
    `${base}/programs`,
    {
        query: computed(() => ({
            page: page.value,
            search: searchTerm.value || undefined,
        })),
        watch: [page, searchTerm],
    },
);

const paginator = computed(() => data.value?.data ?? {});
const programs = computed(() => paginator.value?.data ?? []);
const totalPages = computed(() => paginator.value?.last_page ?? 1);
const totalItems = computed(() => paginator.value?.total ?? 0);
const fromItem = computed(() => paginator.value?.from ?? 0);
const toItem = computed(() => paginator.value?.to ?? 0);

const apiHost = computed(() => base.replace(/\/api\/?$/, ""));

const titleOf = (program: any) =>
    locale.value === "en" ? program.title_en : program.title_id;
const descOf = (program: any) =>
    locale.value === "en" ? program.description_en : program.description_id;
const slugOf = (program: any) =>
    locale.value === "en" ? program.slug_en : program.slug_id;
const priceOf = (program: any) =>
    locale.value === "en" ? program.price_en : program.price_id;

const imageUrl = (path?: string) => {
    if (!path) return "";
    if (path.startsWith("http")) return path;
    return `${apiHost.value}${path}`;
};

const formatPrice = (price?: number) => {
    if (price === null || price === undefined) return "Gratis";
    const currency = locale.value === "en" ? "USD" : "IDR";
    const localeTag = locale.value === "en" ? "en-US" : "id-ID";
    return new Intl.NumberFormat(localeTag, {
        style: "currency",
        currency,
        maximumFractionDigits: 0,
    }).format(Number(price));
};

const pageItems = computed(() => {
    const last = totalPages.value;
    const current = page.value;
    if (last <= 7) {
        return Array.from({ length: last }, (_, i) => i + 1);
    }

    const items: Array<number | "ellipsis"> = [1];
    const start = Math.max(2, current - 1);
    const end = Math.min(last - 1, current + 1);

    if (start > 2) items.push("ellipsis");
    for (let i = start; i <= end; i += 1) items.push(i);
    if (end < last - 1) items.push("ellipsis");

    items.push(last);
    return items;
});

const applySearch = () => {
    search.value = searchInput.value.trim();
    page.value = 1;
};

const clearSearch = () => {
    searchInput.value = "";
    search.value = "";
    page.value = 1;
};

const changePage = (target: number) => {
    if (target < 1 || target > totalPages.value) return;
    page.value = target;
    if (process.client) {
        window.scrollTo({ top: 0, behavior: "smooth" });
    }
};

useSeoMeta({
    title: "Programs - YHIE",
    description:
        "Daftar program Hafiz dan Maulana dengan pencarian dan pagination.",
    ogTitle: "Programs - YHIE",
    ogDescription:
        "Temukan program Hafiz dan Maulana yang sesuai dengan kebutuhan.",
});
</script>
