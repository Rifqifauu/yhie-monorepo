<template>
    <div class="bg-slate-50 dark:bg-emerald-950 text-slate-900 dark:text-white min-h-screen">

        <!-- Hero Section with Cover Image -->
        <section
            class="relative overflow-hidden bg-gradient-to-br from-emerald-50 via-white to-amber-50 dark:from-emerald-950 dark:via-emerald-950 dark:to-amber-950/40 border-b border-emerald-100/70 dark:border-emerald-800/60"
        >
            <!-- Background mosque shadow -->
            <img
                src="/gunungslamet.png"
                class="absolute inset-0 w-full h-full object-cover pointer-events-none opacity-20 dark:opacity-15 dark:invert dark:brightness-120 transition-all duration-300"
                alt=""
            />
            <!-- Overlay -->
            <div
                class="absolute inset-0 bg-gradient-to-br from-emerald-50/75 via-white/65 to-amber-50/55 dark:from-emerald-950/85 dark:via-emerald-950/80 dark:to-amber-950/50 transition-colors duration-300"
                aria-hidden="true"
            ></div>

            <!-- Decorative blurs -->
            <div class="absolute -top-24 -right-24 w-80 h-80 bg-amber-400/20 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-28 -left-28 w-96 h-96 bg-emerald-400/20 rounded-full blur-3xl"></div>

            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-20">
                <!-- Back Link -->
                <NuxtLink
                    :to="localePath('/articles')"
                    class="inline-flex items-center gap-2 group text-sm font-semibold text-emerald-700 dark:text-emerald-300 hover:text-emerald-800 dark:hover:text-emerald-200 transition-colors mb-6"
                >
                    <UIcon
                        name="i-lucide-arrow-left"
                        class="w-4 h-4 transition-transform duration-300 group-hover:-translate-x-1.5"
                    />
                    <span>{{ locale === 'en' ? 'Back to Articles' : 'Kembali ke Artikel' }}</span>
                </NuxtLink>

                <!-- Loading Hero Skeleton -->
                <div v-if="pending" class="max-w-3xl">
                    <USkeleton class="h-5 w-28 rounded-full mb-4" />
                    <USkeleton class="h-10 md:h-14 w-3/4 rounded-2xl mb-4" />
                    <USkeleton class="h-5 w-1/2 rounded-full" />
                </div>

                <!-- Article Hero Content -->
                <div v-else-if="article" class="max-w-4xl" data-aos="fade-up">
                    <!-- Category Badge -->
                    <span
                        class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-bold uppercase tracking-[0.2em] text-white shadow-sm"
                        :class="getCategoryBg(article.category)"
                    >
                        <UIcon name="i-lucide-tag" class="w-3.5 h-3.5" />
                        {{ getCategoryLabel(article.category) }}
                    </span>

                    <!-- Title -->
                    <h1 class="mt-5 text-3xl md:text-5xl font-serif font-extrabold tracking-tight text-emerald-950 dark:text-emerald-50 leading-[1.15]">
                        {{ titleOf(article) }}
                    </h1>

                    <!-- Meta info row -->
                    <div class="mt-6 flex flex-wrap items-center gap-5 text-sm text-slate-600 dark:text-emerald-100/70">
                        <!-- Author -->
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full bg-emerald-100 dark:bg-emerald-800 flex items-center justify-center">
                                <UIcon name="i-lucide-user" class="w-4 h-4 text-emerald-700 dark:text-amber-400" />
                            </div>
                            <span class="font-semibold">{{ article.author?.name || 'Admin YHIE' }}</span>
                        </div>
                        <!-- Date -->
                        <span class="inline-flex items-center gap-1.5">
                            <UIcon name="i-lucide-calendar" class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                            {{ formatDate(article.created_at) }}
                        </span>
                        <!-- Reading time -->
                        <span class="inline-flex items-center gap-1.5">
                            <UIcon name="i-lucide-clock" class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                            {{ readingTime }} {{ locale === 'en' ? 'min read' : 'menit baca' }}
                        </span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Main Content -->
        <section class="py-12 lg:py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                <!-- Error State -->
                <div v-if="error || (!pending && !article)" class="max-w-md mx-auto text-center py-20">
                    <div class="mx-auto w-20 h-20 rounded-3xl bg-red-100 dark:bg-red-950/40 flex items-center justify-center mb-6">
                        <UIcon name="i-heroicons-exclamation-triangle" class="w-10 h-10 text-red-500" />
                    </div>
                    <h3 class="text-xl font-serif font-extrabold text-emerald-950 dark:text-emerald-50 mb-3">
                        {{ locale === 'en' ? 'Article Not Found' : 'Artikel Tidak Ditemukan' }}
                    </h3>
                    <p class="text-slate-500 dark:text-emerald-100/60 mb-6">
                        {{ locale === 'en' ? 'The article you are looking for does not exist or has been removed.' : 'Artikel yang Anda cari tidak tersedia atau telah dihapus.' }}
                    </p>
                    <NuxtLink
                        :to="localePath('/articles')"
                        class="px-6 py-3 rounded-xl bg-gradient-to-r from-emerald-500 to-emerald-600 text-white font-bold shadow-lg hover:shadow-emerald-500/30 transition-all inline-block"
                    >
                        {{ locale === 'en' ? 'Browse Articles' : 'Lihat Semua Artikel' }}
                    </NuxtLink>
                </div>

                <!-- Loading State -->
                <div v-else-if="pending" class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
                    <div class="lg:col-span-8 space-y-6">
                        <USkeleton class="aspect-[16/9] w-full rounded-3xl" />
                        <div class="space-y-3">
                            <USkeleton class="h-5 w-full rounded" />
                            <USkeleton class="h-5 w-5/6 rounded" />
                            <USkeleton class="h-5 w-4/5 rounded" />
                            <USkeleton class="h-5 w-full rounded" />
                            <USkeleton class="h-5 w-3/4 rounded" />
                        </div>
                    </div>
                    <div class="lg:col-span-4">
                        <USkeleton class="h-96 w-full rounded-3xl" />
                    </div>
                </div>

                <!-- Article Content Grid -->
                <div v-else-if="article" class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">

                    <!-- Left: Main Article Content -->
                    <article class="lg:col-span-8 space-y-8" data-aos="fade-right">

                        <!-- Cover Image Gallery -->
                        <div
                            v-if="allImages.length > 0"
                            class="relative overflow-hidden rounded-3xl border border-emerald-200/70 dark:border-emerald-800/60 bg-white/70 dark:bg-emerald-950/40 p-2 shadow-lg"
                        >
                            <!-- Main image -->
                            <div class="relative aspect-[16/10] overflow-hidden rounded-2xl">
                                <img
                                    :src="allImages[activeImageIndex]"
                                    :alt="titleOf(article)"
                                    class="w-full h-full object-cover transition-all duration-700 hover:scale-[1.02]"
                                />
                                <!-- Image counter badge -->
                                <div
                                    v-if="allImages.length > 1"
                                    class="absolute bottom-3 right-3 px-3 py-1.5 rounded-full bg-black/50 backdrop-blur-md text-white text-xs font-bold"
                                >
                                    {{ activeImageIndex + 1 }} / {{ allImages.length }}
                                </div>
                            </div>

                            <!-- Thumbnail strip -->
                            <div v-if="allImages.length > 1" class="flex gap-2 mt-2 overflow-x-auto pb-1 custom-scrollbar">
                                <button
                                    v-for="(img, idx) in allImages"
                                    :key="idx"
                                    @click="activeImageIndex = idx"
                                    class="relative w-16 h-12 sm:w-20 sm:h-14 rounded-xl overflow-hidden flex-shrink-0 transition-all duration-300"
                                    :class="activeImageIndex === idx
                                        ? 'ring-2 ring-emerald-500 ring-offset-2 ring-offset-white dark:ring-offset-emerald-950 opacity-100'
                                        : 'opacity-50 hover:opacity-80'"
                                >
                                    <img :src="img" :alt="`Image ${idx + 1}`" class="w-full h-full object-cover" />
                                </button>
                            </div>
                        </div>

                        <!-- Placeholder if no image -->
                        <div v-else class="relative overflow-hidden rounded-3xl border border-emerald-200/70 dark:border-emerald-800/60 bg-white/70 dark:bg-emerald-950/40 p-2 shadow-lg">
                            <div class="aspect-[16/10] rounded-2xl bg-gradient-to-br from-emerald-100 to-amber-50 dark:from-emerald-900/40 dark:to-emerald-950/60 flex items-center justify-center">
                                <UIcon name="i-lucide-image" class="w-16 h-16 text-emerald-300 dark:text-emerald-700" />
                            </div>
                        </div>

                        <!-- Article Content Card -->
                        <div class="bg-white/60 dark:bg-emerald-900/20 backdrop-blur-md rounded-3xl border border-emerald-200/50 dark:border-emerald-800/50 p-6 md:p-10 shadow-sm">

                            <!-- Content body -->
                            <div
                                class="prose prose-emerald dark:prose-invert lg:prose-lg max-w-none text-slate-700 dark:text-emerald-100/90 leading-relaxed"
                                v-html="formattedContent"
                            ></div>

                            <!-- Tags / Category footer -->
                            <div class="mt-10 pt-6 border-t border-emerald-200/40 dark:border-emerald-800/40">
                                <div class="flex flex-wrap items-center gap-3">
                                    <span class="text-xs font-bold text-slate-500 dark:text-emerald-200/50 uppercase tracking-wider">
                                        {{ locale === 'en' ? 'Category' : 'Kategori' }}:
                                    </span>
                                    <span
                                        class="px-3 py-1.5 rounded-lg text-xs font-bold uppercase tracking-wider text-white"
                                        :class="getCategoryBg(article.category)"
                                    >
                                        {{ getCategoryLabel(article.category) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Share & Actions Bar -->
                        <div class="bg-white/60 dark:bg-emerald-900/20 backdrop-blur-md rounded-3xl border border-emerald-200/50 dark:border-emerald-800/50 p-5 shadow-sm">
                            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                                <p class="text-sm font-semibold text-slate-700 dark:text-emerald-100/80">
                                    {{ locale === 'en' ? 'Share this article' : 'Bagikan artikel ini' }}
                                </p>
                                <div class="flex items-center gap-3">
                                    <button
                                        @click="copyShareLink"
                                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border border-emerald-300 dark:border-emerald-700 bg-white/40 dark:bg-emerald-950/30 text-emerald-800 dark:text-emerald-200 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 font-semibold transition-all duration-300 text-sm"
                                    >
                                        <UIcon :name="copied ? 'i-lucide-check' : 'i-lucide-link'" class="w-4 h-4" />
                                        {{ copied ? (locale === 'en' ? 'Copied!' : 'Disalin!') : (locale === 'en' ? 'Copy Link' : 'Salin Link') }}
                                    </button>
                                    <a
                                        :href="`https://wa.me/?text=${encodeURIComponent(titleOf(article) + ' - ' + currentUrl)}`"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-400 hover:to-emerald-500 text-white font-semibold shadow-md hover:shadow-emerald-500/25 transition-all duration-300 text-sm"
                                    >
                                        <UIcon name="i-lucide-share-2" class="w-4 h-4" />
                                        WhatsApp
                                    </a>
                                </div>
                            </div>
                        </div>
                    </article>

                    <!-- Right: Sticky Sidebar -->
                    <aside class="lg:col-span-4 lg:sticky lg:top-24 space-y-8" data-aos="fade-left">

                        <!-- Author Card -->
                        <div class="bg-white/80 dark:bg-emerald-900/40 backdrop-blur-xl border border-emerald-200/70 dark:border-emerald-800/60 rounded-3xl p-6 shadow-xl">
                            <div class="flex items-center gap-4 mb-4">
                                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center shadow-lg">
                                    <UIcon name="i-lucide-user" class="w-7 h-7 text-white" />
                                </div>
                                <div>
                                    <p class="font-serif font-bold text-lg text-emerald-950 dark:text-emerald-50">
                                        {{ article.author?.name || 'Admin YHIE' }}
                                    </p>
                                    <p class="text-xs text-slate-500 dark:text-emerald-100/50">
                                        {{ locale === 'en' ? 'Official Contributor' : 'Kontributor Resmi' }}
                                    </p>
                                </div>
                            </div>
                            <hr class="border-emerald-200/40 dark:border-emerald-800/40 mb-4" />
                            <div class="space-y-2.5 text-sm text-slate-600 dark:text-emerald-100/70">
                                <div class="flex items-center gap-2.5">
                                    <UIcon name="i-lucide-calendar" class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" />
                                    <span>{{ locale === 'en' ? 'Published' : 'Diterbitkan' }}: {{ formatDate(article.created_at) }}</span>
                                </div>
                                <div class="flex items-center gap-2.5">
                                    <UIcon name="i-lucide-clock" class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" />
                                    <span>{{ readingTime }} {{ locale === 'en' ? 'min read' : 'menit baca' }}</span>
                                </div>
                                <div class="flex items-center gap-2.5">
                                    <UIcon name="i-lucide-tag" class="w-4 h-4 text-amber-500 shrink-0" />
                                    <span>{{ getCategoryLabel(article.category) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Latest Articles Card -->
                        <div class="bg-white/80 dark:bg-emerald-900/40 backdrop-blur-xl border border-emerald-200/70 dark:border-emerald-800/60 rounded-3xl p-6 shadow-xl">
                            <h3 class="font-serif font-extrabold text-lg text-emerald-950 dark:text-emerald-50 mb-5 pb-3 border-b border-emerald-200/40 dark:border-emerald-800/40 flex items-center gap-2">
                                <UIcon name="i-lucide-newspaper" class="w-5 h-5 text-amber-500" />
                                {{ locale === 'en' ? 'Latest Publications' : 'Publikasi Terbaru' }}
                            </h3>

                            <!-- Loading sidebar -->
                            <div v-if="latestPending" class="space-y-4">
                                <div v-for="n in 4" :key="n" class="flex gap-3">
                                    <USkeleton class="h-14 w-14 rounded-xl flex-shrink-0" />
                                    <div class="flex-1 space-y-2">
                                        <USkeleton class="h-4 w-full rounded" />
                                        <USkeleton class="h-3 w-1/2 rounded" />
                                    </div>
                                </div>
                            </div>

                            <!-- Latest articles list -->
                            <div v-else class="space-y-4">
                                <NuxtLink
                                    v-for="lat in latestArticles"
                                    :key="lat.id"
                                    :to="localePath(`/articles/${slugOf(lat)}`)"
                                    class="flex gap-3.5 group cursor-pointer p-2 -mx-2 rounded-xl hover:bg-emerald-50/60 dark:hover:bg-emerald-900/30 transition-all duration-300"
                                >
                                    <!-- Thumbnail -->
                                    <div class="h-14 w-14 rounded-xl overflow-hidden bg-slate-100 dark:bg-emerald-950 flex-shrink-0">
                                        <img
                                            v-if="coverOf(lat)"
                                            :src="coverOf(lat)"
                                            :alt="titleOf(lat)"
                                            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                                        />
                                        <div v-else class="w-full h-full flex items-center justify-center bg-gradient-to-br from-emerald-50 to-amber-50 dark:from-emerald-900/40 dark:to-emerald-950/60">
                                            <UIcon name="i-lucide-image" class="w-5 h-5 text-emerald-300 dark:text-emerald-700" />
                                        </div>
                                    </div>

                                    <!-- Details -->
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-serif font-bold text-sm text-emerald-950 dark:text-emerald-50 leading-snug group-hover:text-emerald-600 dark:group-hover:text-amber-400 transition-colors line-clamp-2 mb-1">
                                            {{ titleOf(lat) }}
                                        </h4>
                                        <span class="text-[10px] text-slate-400 dark:text-emerald-100/30 flex items-center gap-1">
                                            <UIcon name="i-lucide-calendar" class="w-3 h-3" />
                                            {{ formatDate(lat.created_at) }}
                                        </span>
                                    </div>
                                </NuxtLink>

                                <!-- See all link -->
                                <NuxtLink
                                    :to="localePath('/articles')"
                                    class="flex items-center justify-center gap-2 mt-4 pt-4 border-t border-emerald-200/40 dark:border-emerald-800/40 text-sm font-bold text-emerald-600 dark:text-amber-400 hover:text-emerald-700 dark:hover:text-amber-300 transition-colors group"
                                >
                                    <span>{{ locale === 'en' ? 'View All Articles' : 'Lihat Semua Artikel' }}</span>
                                    <UIcon name="i-lucide-arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform" />
                                </NuxtLink>
                            </div>
                        </div>

                    </aside>
                </div>

            </div>
        </section>
    </div>
</template>

<script setup lang="ts">
const route = useRoute();
const config = useRuntimeConfig();
const { locale } = useI18n();
const localePath = useLocalePath();
const client = useSanctumClient();

const slug = computed(() => route.params.slug as string);
const copied = ref(false);
const activeImageIndex = ref(0);

// --- Fetch main article ---
const {
    data: apiResponse,
    status,
    error,
} = useAsyncData(
    `article-detail-${slug.value}`,
    () => client(`/api/articles/${slug.value}`),
    { watch: [slug] }
);

const article = computed(() => (apiResponse.value as any)?.data ?? null);
const pending = computed(() => status.value === "pending");

// --- Fetch latest articles ---
const {
    data: latestApiResponse,
    status: latestStatus,
} = useAsyncData(
    "latest-articles-sidebar",
    () => client("/api/articles", { params: { per_page: 5 } })
);

const latestArticles = computed(() =>
    ((latestApiResponse.value as any)?.data?.data ?? []).filter(
        (item: any) => item.id !== article.value?.id
    ).slice(0, 4)
);
const latestPending = computed(() => latestStatus.value === "pending");

// --- Categories ---
const categories = [
    { value: "news", labelId: "Berita", labelEn: "News" },
    { value: "announcement", labelId: "Pengumuman", labelEn: "Announcement" },
    { value: "event", labelId: "Kegiatan", labelEn: "Event" },
    { value: "education", labelId: "Edukasi", labelEn: "Education" },
];

const getCategoryLabel = (cat?: string) => {
    if (!cat) return "";
    const found = categories.find(c => c.value === cat);
    if (!found) return cat;
    return locale.value === "en" ? found.labelEn : found.labelId;
};

const getCategoryBg = (cat?: string) => {
    switch (cat) {
        case "news":
            return "bg-emerald-600 dark:bg-emerald-500";
        case "announcement":
            return "bg-amber-500 dark:bg-amber-600";
        case "event":
            return "bg-teal-600 dark:bg-teal-500";
        case "education":
            return "bg-emerald-700 dark:bg-emerald-600";
        default:
            return "bg-emerald-600 dark:bg-emerald-500";
    }
};

// --- Helpers ---
const titleOf = (item: any) =>
    item && (locale.value === "en" ? item.title_en : item.title_id);

const contentOf = (item: any) =>
    item && (locale.value === "en" ? item.content_en : item.content_id);

const slugOf = (item: any) =>
    item && (locale.value === "en" ? item.slug_en : item.slug_id);

const backendUrl = (config.public.sanctum?.baseUrl as string) || "http://127.0.0.1:8000";

const buildImageUrl = (path?: string) => {
    if (!path) return "";
    if (path.startsWith("http")) return path;
    const base = backendUrl.endsWith("/") ? backendUrl.slice(0, -1) : backendUrl;
    const cleanPath = path.startsWith("/") ? path : `/${path}`;
    return `${base}${cleanPath}`;
};

const coverOf = (item: any): string => {
    if (!item) return "";
    const img = item.image;
    if (!img) return "";

    if (Array.isArray(img) && img.length > 0) {
        return buildImageUrl(img[0]);
    }
    if (typeof img === "object" && img.path) {
        return buildImageUrl(img.path);
    }
    if (typeof img === "string") {
        try {
            const parsed = JSON.parse(img);
            if (Array.isArray(parsed) && parsed.length > 0) {
                return buildImageUrl(parsed[0]);
            }
            if (typeof parsed === "object" && parsed.path) {
                return buildImageUrl(parsed.path);
            }
        } catch (e) {
            return buildImageUrl(img);
        }
    }
    return "";
};

// All images for gallery
const allImages = computed<string[]>(() => {
    if (!article.value) return [];
    const img = article.value.image;
    if (!img) return [];

    if (Array.isArray(img)) {
        return img.map((i: any) => buildImageUrl(typeof i === "string" ? i : i?.path || "")).filter(Boolean);
    }

    if (typeof img === "string") {
        try {
            const parsed = JSON.parse(img);
            if (Array.isArray(parsed)) {
                return parsed.map((i: any) => buildImageUrl(typeof i === "string" ? i : i?.path || "")).filter(Boolean);
            }
        } catch {
            const url = buildImageUrl(img);
            return url ? [url] : [];
        }
    }

    return [];
});

const formatDate = (dateStr?: string): string => {
    if (!dateStr) return "";
    return new Date(dateStr).toLocaleDateString(locale.value === "en" ? "en-US" : "id-ID", {
        day: "numeric",
        month: "long",
        year: "numeric",
    });
};

// Reading time calculation
const readingTime = computed(() => {
    const raw = contentOf(article.value);
    if (!raw) return 1;
    const wordCount = raw.replace(/<[^>]*>/g, "").split(/\s+/).length;
    return Math.max(1, Math.ceil(wordCount / 200));
});

// Format plain text content into paragraphs
const formattedContent = computed(() => {
    const raw = contentOf(article.value);
    if (!raw) return "";

    // If content already contains HTML tags, return as is
    if (/<[a-z][\s\S]*>/i.test(raw)) {
        return raw;
    }

    // Otherwise, convert plain text to formatted paragraphs
    return raw
        .split("\n\n")
        .map((p: string) => `<p class="mb-4">${p.replace(/\n/g, "<br>")}</p>`)
        .join("");
});

const stripHtml = (html?: string): string => {
    return html?.replace(/<[^>]*>/g, "") ?? "";
};

// Share link
const currentUrl = computed(() => {
    if (import.meta.client) return window.location.href;
    return "";
});

const copyShareLink = () => {
    if (import.meta.client) {
        navigator.clipboard.writeText(window.location.href);
        copied.value = true;
        setTimeout(() => {
            copied.value = false;
        }, 2000);
    }
};

// SEO
useSeoMeta({
    title: () => titleOf(article.value) ? `${titleOf(article.value)} - YHIE` : "Article - YHIE",
    description: () => stripHtml(contentOf(article.value)).substring(0, 160),
    ogTitle: () => titleOf(article.value) ? `${titleOf(article.value)} - YHIE` : "Article - YHIE",
    ogDescription: () => stripHtml(contentOf(article.value)).substring(0, 160),
    ogImage: () => allImages.value[0] || "",
});
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    height: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(16, 185, 129, 0.2);
    border-radius: 999px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(16, 185, 129, 0.4);
}

:deep(.prose) {
    font-family: 'Georgia', 'Times New Roman', serif;
}

:deep(.prose p) {
    margin-bottom: 1.25em;
    line-height: 1.85;
}

:deep(.prose h2) {
    margin-top: 2em;
    margin-bottom: 0.75em;
    font-weight: 800;
}

:deep(.prose h3) {
    margin-top: 1.5em;
    margin-bottom: 0.5em;
    font-weight: 700;
}

:deep(.prose img) {
    border-radius: 1rem;
    margin-top: 1.5em;
    margin-bottom: 1.5em;
}

:deep(.prose blockquote) {
    border-left: 4px solid #10b981;
    padding-left: 1.25rem;
    font-style: italic;
    color: #64748b;
}

:deep(.prose a) {
    color: #059669;
    text-decoration: underline;
    text-underline-offset: 3px;
}

:deep(.prose a:hover) {
    color: #047857;
}

:deep(.prose ul),
:deep(.prose ol) {
    margin-top: 1em;
    margin-bottom: 1em;
    padding-left: 1.5em;
}

:deep(.prose li) {
    margin-bottom: 0.5em;
}
</style>
