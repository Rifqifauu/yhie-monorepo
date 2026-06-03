<template>
    <div class="bg-slate-50 dark:bg-emerald-950 text-slate-900 dark:text-white min-h-screen">
        
        <!-- Header / Back Navigation -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8">
            <NuxtLink
                to="/articles"
                class="inline-flex items-center gap-2 text-sm font-semibold text-emerald-600 dark:text-amber-400 hover:text-emerald-700 dark:hover:text-amber-300 transition-colors group"
            >
                <UIcon name="i-lucide-arrow-left" class="w-4 h-4 transform group-hover:-translate-x-1 transition-transform" />
                {{ locale === 'en' ? 'Back to Articles' : 'Kembali ke Artikel' }}
            </NuxtLink>
        </div>

        <section class="py-10 lg:py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <!-- Loading State -->
                <div v-if="pending" class="space-y-6 max-w-4xl mx-auto">
                    <USkeleton class="h-8 w-1/4 rounded" />
                    <USkeleton class="h-12 w-3/4 rounded" />
                    <USkeleton class="h-[400px] w-full rounded-2xl" />
                    <div class="space-y-3">
                        <USkeleton class="h-4 w-full" />
                        <USkeleton class="h-4 w-5/6" />
                        <USkeleton class="h-4 w-4/5" />
                    </div>
                </div>

                <!-- Error State -->
                <div v-else-if="error || !article" class="max-w-md mx-auto text-center py-20">
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
                        to="/articles"
                        class="px-6 py-2.5 rounded-xl bg-emerald-600 text-white font-semibold hover:bg-emerald-700 transition-colors inline-block"
                    >
                        {{ locale === 'en' ? 'Browse Articles' : 'Lihat Semua Artikel' }}
                    </NuxtLink>
                </div>

                <!-- Article Content Layout -->
                <div v-else class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
                    
                    <!-- Left: Main Article Content -->
                    <article class="lg:col-span-8 bg-white dark:bg-emerald-900/10 border border-emerald-100/40 dark:border-emerald-800/20 rounded-3xl p-6 sm:p-10 shadow-sm">
                        <!-- Category + Date -->
                        <div class="flex flex-wrap items-center gap-3 text-xs mb-6">
                            <span 
                                class="font-bold uppercase tracking-wider px-3 py-1.5 rounded-lg text-white"
                                :class="getCategoryBg(article.category)"
                            >
                                {{ getCategoryLabel(article.category) }}
                            </span>
                            <span class="text-slate-400 dark:text-emerald-100/40 flex items-center gap-1">
                                <UIcon name="i-lucide-calendar" class="w-3.5 h-3.5" />
                                {{ formatDate(article.created_at) }}
                            </span>
                        </div>

                        <!-- Title -->
                        <h1 class="font-serif font-extrabold text-2xl sm:text-4xl text-emerald-950 dark:text-emerald-50 leading-tight mb-6">
                            {{ titleOf(article) }}
                        </h1>

                        <!-- Author Info Header -->
                        <div class="flex items-center gap-3 pb-6 mb-8 border-b border-slate-100 dark:border-emerald-900/50">
                            <div class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-800 flex items-center justify-center shadow-sm">
                                <UIcon name="i-lucide-user" class="w-5 h-5 text-emerald-700 dark:text-amber-400" />
                            </div>
                            <div>
                                <span class="block text-sm font-bold text-slate-700 dark:text-emerald-50">
                                    {{ article.author?.name || 'Admin YHIE' }}
                                </span>
                                <span class="block text-xs text-slate-400 dark:text-emerald-100/40">
                                    {{ locale === 'en' ? 'Contributor' : 'Kontributor Resmi' }}
                                </span>
                            </div>
                        </div>

                        <!-- Main Image -->
                        <div class="relative rounded-2xl overflow-hidden shadow-lg mb-8 max-h-[450px] bg-slate-100 dark:bg-emerald-950 flex justify-center items-center">
                            <img
                                :src="coverOf(article)"
                                :alt="titleOf(article)"
                                class="w-full h-full object-cover"
                            />
                        </div>

                        <!-- Article Body -->
                        <div 
                            class="prose prose-emerald dark:prose-invert lg:prose-lg max-w-none text-slate-600 dark:text-emerald-100/80 leading-relaxed font-light"
                            v-html="formattedContent"
                        ></div>
                    </article>

                    <!-- Right: Sidebar (Latest Articles) -->
                    <aside class="lg:col-span-4 space-y-8">
                        <div class="bg-white dark:bg-emerald-900/10 border border-emerald-100/40 dark:border-emerald-800/20 rounded-3xl p-6 sm:p-8 shadow-sm">
                            <h3 class="font-serif font-extrabold text-xl text-emerald-950 dark:text-emerald-50 mb-6 pb-3 border-b border-slate-100 dark:border-emerald-900/50">
                                {{ locale === 'en' ? 'Latest Publications' : 'Publikasi Terbaru' }}
                            </h3>

                            <!-- Loading Latest -->
                            <div v-if="latestPending" class="space-y-4">
                                <div v-for="n in 3" :key="n" class="flex gap-3">
                                    <USkeleton class="h-16 w-16 rounded-xl flex-shrink-0" />
                                    <div class="flex-1 space-y-2">
                                        <USkeleton class="h-4 w-full rounded" />
                                        <USkeleton class="h-3 w-1/2 rounded" />
                                    </div>
                                </div>
                            </div>

                            <!-- List Latest -->
                            <div v-else class="space-y-6">
                                <NuxtLink
                                    v-for="lat in latestArticles"
                                    :key="lat.id"
                                    :to="`/articles/${slugOf(lat)}`"
                                    class="flex gap-4 group cursor-pointer align-top"
                                >
                                    <!-- Image Thumbnail -->
                                    <div class="h-16 w-16 rounded-xl overflow-hidden bg-slate-100 dark:bg-emerald-950 flex-shrink-0 relative">
                                        <img
                                            :src="coverOf(lat)"
                                            :alt="titleOf(lat)"
                                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                        />
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
const client = useSanctumClient();

const slug = computed(() => route.params.slug);

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

// Categories
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

// Formatter Helpers
const titleOf = (item: any) =>
    item && (locale.value === "en" ? item.title_en : item.title_id);

const contentOf = (item: any) =>
    item && (locale.value === "en" ? item.content_en : item.content_id);

const slugOf = (item: any) =>
    item && (locale.value === "en" ? item.slug_en : item.slug_id);

const backendUrl = config.public.sanctum?.baseUrl || "http://127.0.0.1:8000";

const buildImageUrl = (path?: string) => {
    if (!path) return "";
    if (path.startsWith("http")) return path;
    return `${backendUrl}${path.startsWith("/") ? path : `/${path}`}`;
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

const formatDate = (dateStr?: string): string => {
    if (!dateStr) return "";
    return new Date(dateStr).toLocaleDateString(locale.value === "en" ? "en-US" : "id-ID", {
        day: "numeric",
        month: "long",
        year: "numeric",
    });
};

// Replace newlines with <p> or <br> to format plain text content correctly
const formattedContent = computed(() => {
    const raw = contentOf(article.value);
    if (!raw) return "";
    return raw
        .split("\n\n")
        .map(p => `<p class="mb-4">${p.replace(/\n/g, "<br>")}</p>`)
        .join("");
});

// SEO
useSeoMeta({
    title: () => titleOf(article.value) ? `${titleOf(article.value)} - YHIE` : "Articles - YHIE",
    description: () => stripHtml(contentOf(article.value)).substring(0, 160),
    ogTitle: () => titleOf(article.value) ? `${titleOf(article.value)} - YHIE` : "Articles - YHIE",
    ogDescription: () => stripHtml(contentOf(article.value)).substring(0, 160),
});

const stripHtml = (html?: string): string => {
    return html?.replace(/<[^>]*>/g, "") ?? "";
};
</script>
