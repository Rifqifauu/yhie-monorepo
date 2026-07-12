<template>
    <div
        class="bg-slate-50 dark:bg-emerald-950 text-slate-900 dark:text-white min-h-screen"
    >
        <section
            class="relative overflow-hidden bg-gradient-to-br from-emerald-50 via-white to-amber-50 dark:from-emerald-950 dark:via-emerald-950 dark:to-amber-950/40 border-b border-emerald-100/70 dark:border-emerald-800/60 pt-12 pb-10"
        >
            <img
                src="/shadow-mosque.webp"
                class="absolute bottom-0 left-1/2 -translate-x-1/2 w-auto h-full max-h-[85%] object-contain object-bottom pointer-events-none opacity-20 dark:opacity-35 dark:invert dark:brightness-150 transition-all duration-300"
                alt=""
            />
            <div
                class="absolute inset-0 bg-gradient-to-br from-emerald-50/75 via-white/65 to-amber-50/55 dark:from-emerald-950/70 dark:via-emerald-950/65 dark:to-amber-950/40 transition-colors duration-300"
                aria-hidden="true"
            ></div>

            <div
                class="absolute -top-24 -right-24 w-80 h-80 bg-amber-400/20 rounded-full blur-3xl"
            ></div>
            <div
                class="absolute -bottom-28 -left-28 w-96 h-96 bg-emerald-400/20 rounded-full blur-3xl"
            ></div>

            <div
                class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center z-10"
                data-aos="fade-up"
            >
                <span
                    class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-[0.3em] text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/60 px-3 py-1 rounded-full border border-amber-200/50 dark:border-amber-900/40"
                >
                    <UIcon name="i-lucide-newspaper" class="w-4 h-4" />
                    {{
                        locale === "en"
                            ? "Information Center"
                            : "Pusat Informasi"
                    }}
                </span>
                <h1
                    class="mt-4 text-3xl md:text-4xl lg:text-5xl font-serif font-extrabold tracking-tight text-emerald-950 dark:text-emerald-50 leading-[1.15]"
                >
                    {{
                        locale === "en"
                            ? "Articles & Latest News"
                            : "Artikel & Berita Terbaru"
                    }}
                </h1>
                <p
                    class="mt-3 text-sm md:text-base text-slate-600 dark:text-emerald-100/80 leading-relaxed max-w-2xl mx-auto font-light"
                >
                    {{
                        locale === "en"
                            ? "Discover the latest insights, guidelines, and inspiration for Quranic memorization and heritage excellence with us."
                            : "Temukan wawasan terbaru, panduan ibadah, dan inspirasi hafalan Al-Qur'an serta sejarah keunggulan bersama kami."
                    }}
                </p>

                <div
                    class="relative mt-6 max-w-2xl mx-auto"
                    data-aos="fade-up"
                    data-aos-delay="100"
                >
                    <div
                        class="bg-white/70 dark:bg-emerald-900/45 backdrop-blur-xl border border-emerald-200/60 dark:border-emerald-800/50 rounded-2xl p-2 shadow-lg"
                    >
                        <div class="flex flex-col sm:flex-row gap-2">
                            <UInput
                                :model-value="searchInput"
                                size="lg"
                                :placeholder="
                                    locale === 'en'
                                        ? 'Search article title or category...'
                                        : 'Cari judul atau kategori artikel...'
                                "
                                class="flex-1"
                                @update:model-value="searchInput = $event"
                                @keyup.enter="applySearch"
                            />
                            <button
                                class="px-5 py-2 rounded-xl bg-gradient-to-r from-emerald-500 to-emerald-600 text-white font-semibold shadow-md hover:shadow-emerald-500/25 hover:from-emerald-600 hover:to-emerald-700 transition-all duration-300 transform active:scale-95"
                                @click="applySearch"
                            >
                                {{ locale === "en" ? "Search" : "Cari" }}
                            </button>
                        </div>
                        <div
                            v-if="searchTerm"
                            class="mt-2 flex items-center justify-between text-xs text-slate-500 dark:text-emerald-100/60 px-1"
                        >
                            <span>
                                {{
                                    locale === "en"
                                        ? "Showing results for:"
                                        : "Menampilkan hasil untuk:"
                                }}
                                <strong>"{{ searchTerm }}"</strong>
                            </span>
                            <button
                                class="text-amber-600 hover:text-amber-700 dark:text-amber-400 dark:hover:text-amber-300 font-bold"
                                @click="clearSearch"
                            >
                                Reset
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section
            class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex flex-wrap justify-center gap-2 z-20 relative"
        >
            <button
                v-for="cat in categories"
                :key="cat.value"
                class="px-4 py-1.5 rounded-full border text-xs sm:text-sm font-semibold tracking-wide transition-all duration-300 transform active:scale-95"
                :class="
                    category === cat.value
                        ? 'bg-emerald-600 border-emerald-600 text-white shadow-md shadow-emerald-600/10 dark:bg-amber-500 dark:border-amber-500 dark:text-emerald-950 dark:shadow-amber-500/20'
                        : 'border-slate-200 dark:border-emerald-800/80 text-slate-600 dark:text-emerald-200/80 hover:border-emerald-500 dark:hover:border-amber-500 hover:text-emerald-600 dark:hover:text-amber-400 bg-white/80 dark:bg-emerald-950/40 backdrop-blur-sm'
                "
                @click="category = cat.value"
            >
                {{ cat.label }}
            </button>
        </section>

        <section class="pb-12 lg:pb-16 pt-4">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div
                    v-if="pending"
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
                >
                    <div v-for="n in 6" :key="n" class="space-y-3">
                        <USkeleton class="h-44 w-full rounded-2xl" />
                        <USkeleton class="h-3 w-1/4 rounded" />
                        <USkeleton class="h-5 w-3/4 rounded" />
                        <USkeleton class="h-3 w-5/6 rounded" />
                    </div>
                </div>

                <div v-else-if="error" class="max-w-md mx-auto">
                    <UAlert
                        icon="i-heroicons-exclamation-triangle"
                        color="red"
                        variant="soft"
                        :title="
                            locale === 'en'
                                ? 'Failed to load articles'
                                : 'Gagal memuat artikel'
                        "
                        :description="
                            locale === 'en'
                                ? 'Please check your connection and try again.'
                                : 'Silakan periksa koneksi Anda dan coba lagi.'
                        "
                    >
                        <template #actions>
                            <UButton size="sm" color="red" @click="refresh">
                                {{ locale === "en" ? "Retry" : "Coba lagi" }}
                            </UButton>
                        </template>
                    </UAlert>
                </div>

                <div v-else-if="!articles.length" class="text-center py-12">
                    <EmptyData
                        title="Article"
                        description="Tidak ada artikel ditemukan."
                    />
                </div>

                <div
                    v-else
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
                >
                    <NuxtLink
                        v-for="(article, index) in articles"
                        :key="article.id"
                        :to="`/articles/${slugOf(article)}`"
                        data-aos="fade-up"
                        :data-aos-delay="(index % 6) * 80"
                        class="group bg-white dark:bg-emerald-900/10 border border-emerald-100/40 dark:border-emerald-800/20 rounded-2xl overflow-hidden shadow-sm hover:shadow-lg hover:border-emerald-200 dark:hover:border-emerald-800 transition-all duration-300 flex flex-col h-full"
                    >
                        <div
                            class="relative h-44 sm:h-48 overflow-hidden bg-slate-100 dark:bg-emerald-950"
                        >
                            <img
                                v-if="coverOf(article)"
                                :src="coverOf(article)"
                                :alt="titleOf(article)"
                                class="w-full h-full object-cover grayscale-[20%] group-hover:grayscale-0 group-hover:scale-105 transition-all duration-500"
                            />
                            <div
                                v-else
                                class="w-full h-full flex items-center justify-center bg-gradient-to-br from-emerald-50 to-amber-50 dark:from-emerald-900/40 dark:to-emerald-950/60"
                            >
                                <UIcon
                                    name="i-lucide-image"
                                    class="w-10 h-10 text-emerald-300 dark:text-emerald-700"
                                />
                            </div>

                            <span
                                class="absolute top-3 left-3 text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-lg text-white shadow-sm"
                                :class="getCategoryBg(article.category)"
                            >
                                {{ getCategoryLabel(article.category) }}
                            </span>
                        </div>

                        <div class="p-4 sm:p-5 flex flex-col flex-1">
                            <div
                                class="flex items-center gap-1.5 text-xs text-slate-400 dark:text-emerald-100/40 mb-2"
                            >
                                <UIcon
                                    name="i-lucide-calendar"
                                    class="w-3.5 h-3.5"
                                />
                                <span>{{
                                    formatDate(article.created_at)
                                }}</span>
                            </div>

                            <h3
                                class="font-serif font-extrabold text-base sm:text-lg text-emerald-950 dark:text-emerald-50 leading-snug group-hover:text-emerald-600 dark:group-hover:text-amber-400 transition-colors line-clamp-2 mb-2"
                            >
                                {{ titleOf(article) }}
                            </h3>

                            <p
                                class="text-slate-500 dark:text-emerald-100/60 text-sm leading-relaxed line-clamp-2 mb-4 flex-1"
                            >
                                {{ stripHtml(contentOf(article)) }}
                            </p>

                            <div
                                class="pt-3 border-t border-slate-100 dark:border-emerald-900/50 flex items-center justify-between mt-auto"
                            >
                                <div class="flex items-center gap-2">
                                    <div
                                        class="w-6 h-6 rounded-full bg-emerald-100 dark:bg-emerald-800/80 flex items-center justify-center"
                                    >
                                        <UIcon
                                            name="i-lucide-user"
                                            class="w-3 h-3 text-emerald-700 dark:text-amber-400"
                                        />
                                    </div>
                                    <span
                                        class="text-xs font-bold text-slate-600 dark:text-emerald-100/80"
                                    >
                                        {{
                                            article.author?.name || "Admin YHIE"
                                        }}
                                    </span>
                                </div>

                                <span
                                    class="text-xs font-bold text-emerald-600 group-hover:text-emerald-700 dark:text-amber-400 dark:group-hover:text-amber-300 flex items-center gap-1"
                                >
                                    {{
                                        locale === "en"
                                            ? "Read More"
                                            : "Baca Selengkapnya"
                                    }}
                                    <UIcon
                                        name="i-lucide-arrow-right"
                                        class="w-3 h-3 transform group-hover:translate-x-1 transition-transform"
                                    />
                                </span>
                            </div>
                        </div>
                    </NuxtLink>
                </div>

                <ArticlePagination
                    v-if="totalPages > 1"
                    :page="page"
                    :total-pages="totalPages"
                    :page-items="pageItems"
                    class="mt-8"
                    @change-page="changePage"
                />
            </div>
        </section>
    </div>
</template>

<script setup lang="ts">
import { useArticles } from "~/composables/useArticles";

const { locale } = useI18n();

const {
    page,
    searchInput,
    searchTerm,
    category,
    articles,
    existingCategories,
    totalPages,
    pending,
    error,
    pageItems,
    titleOf,
    contentOf,
    slugOf,
    coverOf,
    applySearch,
    clearSearch,
    changePage,
    refresh,
} = useArticles();

// Filter categories - dibangun dari kategori yang benar-benar ada di data,
// bukan daftar tetap, supaya selalu cocok dengan apa yang diinput admin.
const categories = computed(() => {
    const list = [
        { value: "", label: locale.value === "en" ? "All" : "Semua" },
    ];
    existingCategories.value.forEach((cat) => {
        if (!cat) return;
        list.push({ value: cat, label: cat });
    });
    return list;
});

const getCategoryLabel = (cat?: string) => cat || "";

// Warna badge kategori diputar dari daftar warna tetap (bukan berdasarkan nama
// kategori tertentu, karena kategori sekarang bebas diisi admin).
const categoryBgColors = [
    "bg-emerald-600 dark:bg-emerald-500",
    "bg-amber-500 dark:bg-amber-600",
    "bg-teal-600 dark:bg-teal-500",
    "bg-emerald-700 dark:bg-emerald-600",
];

const getCategoryBg = (cat?: string) => {
    if (!cat) return categoryBgColors[0];
    let hash = 0;
    for (let i = 0; i < cat.length; i++) hash = (hash + cat.charCodeAt(i)) % categoryBgColors.length;
    return categoryBgColors[hash];
};

// Format Date
const formatDate = (dateStr: string): string => {
    if (!dateStr) return "";
    return new Date(dateStr).toLocaleDateString(
        locale.value === "en" ? "en-US" : "id-ID",
        {
            day: "numeric",
            month: "short",
            year: "numeric",
        },
    );
};

// Strip HTML tags from content
const stripHtml = (html?: string): string => {
    return html?.replace(/<[^>]*>/g, "") ?? "";
};

// SEO
useSeoMeta({
    title: () =>
        locale.value === "en"
            ? "Articles & News - YHIE"
            : "Artikel & Berita - YHIE",
    description: () =>
        locale.value === "en"
            ? "Read the latest news, blogs, and religious resources from Yayasan Hafiz Indonesia Emas."
            : "Baca kabar berita terbaru, blog, dan tulisan keagamaan dari Yayasan Hafiz Indonesia Emas.",
    ogTitle: () =>
        locale.value === "en"
            ? "Articles & News - YHIE"
            : "Artikel & Berita - YHIE",
    ogDescription: () =>
        locale.value === "en"
            ? "Latest news and articles from Yayasan Hafiz Indonesia Emas."
            : "Kabar berita dan artikel terbaru dari Yayasan Hafiz Indonesia Emas.",
});
</script>
