<template>
    <section
        class="py-20 lg:py-28 border-t border-emerald-900/20 dark:border-emerald-500/20 bg-gradient-to-b from-emerald-50 via-white to-white dark:from-emerald-950 dark:via-gray-950 dark:to-gray-950 overflow-hidden relative transition-colors duration-500"
    >
        <!-- Decorative blobs -->
        <div class="absolute -top-20 left-0 w-72 h-72 bg-emerald-400/10 dark:bg-emerald-500/5 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 right-0 w-80 h-80 bg-amber-400/10 dark:bg-amber-500/5 rounded-full blur-3xl pointer-events-none"></div>

        <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-7xl relative z-10">

            <!-- Header -->
            <div class="text-center max-w-2xl mx-auto mb-14">
                <span
                    data-aos="fade-up"
                    class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-[0.3em] text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/60 px-3.5 py-1.5 rounded-full border border-amber-200/50 dark:border-amber-900/40 mb-5"
                >
                    <UIcon name="i-lucide-newspaper" class="w-4 h-4" />
                    {{ locale === 'en' ? 'Latest Articles' : 'Artikel Terbaru' }}
                </span>
                <h2
                    data-aos="fade-up"
                    data-aos-delay="50"
                    class="text-3xl md:text-5xl font-serif font-bold text-gray-900 dark:text-white tracking-tight leading-tight mb-4"
                >
                    {{ locale === 'en' ? 'News & Insights' : 'Berita & Wawasan' }}
                </h2>
                <div class="flex items-center justify-center gap-1.5 mb-5">
                    <div class="h-1 w-12 bg-amber-500 rounded-full"></div>
                    <div class="h-1.5 w-1.5 bg-emerald-500 rounded-full"></div>
                    <div class="h-1 w-12 bg-amber-500 rounded-full"></div>
                </div>
                <p
                    data-aos="fade-up"
                    data-aos-delay="100"
                    class="text-base md:text-lg text-slate-600 dark:text-emerald-100/70 leading-relaxed"
                >
                    {{ locale === 'en'
                        ? 'Stay updated with the latest news, announcements, and spiritual insights from Yayasan Hafiz Indonesia Emas.'
                        : 'Tetap terhubung dengan berita terbaru, pengumuman, dan inspirasi spiritual dari Yayasan Hafiz Indonesia Emas.' }}
                </p>
            </div>

            <!-- Loading skeleton -->
            <div v-if="pending" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="n in 3" :key="n" class="rounded-3xl overflow-hidden border border-emerald-100 dark:border-emerald-900/50">
                    <USkeleton class="h-52 w-full" />
                    <div class="p-5 space-y-3">
                        <USkeleton class="h-4 w-1/3 rounded-full" />
                        <USkeleton class="h-6 w-4/5 rounded" />
                        <USkeleton class="h-4 w-full rounded" />
                        <USkeleton class="h-4 w-2/3 rounded" />
                    </div>
                </div>
            </div>

            <!-- Error -->
            <div v-else-if="error" class="max-w-md mx-auto">
                <UAlert
                    icon="i-heroicons-exclamation-triangle"
                    color="red"
                    variant="soft"
                    :title="locale === 'en' ? 'Failed to load articles' : 'Gagal memuat artikel'"
                    :description="locale === 'en' ? 'Please try again later.' : 'Silakan coba lagi.'"
                >
                    <template #actions>
                        <UButton size="sm" color="red" variant="soft" icon="i-heroicons-arrow-path" @click="refresh">
                            {{ locale === 'en' ? 'Retry' : 'Coba Lagi' }}
                        </UButton>
                    </template>
                </UAlert>
            </div>

            <!-- Articles grid -->
            <div
                v-else-if="previewArticles.length > 0"
                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
            >
                <!-- Featured (index 0) — full width on md -->
                <NuxtLink
                    v-if="previewArticles[0]"
                    :to="localePath('/articles/' + slugOf(previewArticles[0]))"
                    class="group relative md:col-span-2 lg:col-span-1 rounded-3xl overflow-hidden border border-emerald-200/70 dark:border-emerald-800/60 bg-white/70 dark:bg-emerald-950/40 shadow-sm hover:-translate-y-1 hover:shadow-2xl transition-all duration-500"
                    data-aos="fade-up"
                    data-aos-delay="0"
                >
                    <!-- Image -->
                    <div class="relative aspect-[16/9] overflow-hidden">
                        <NuxtImg
                            v-if="coverOf(previewArticles[0])"
                            :src="coverOf(previewArticles[0])"
                            :alt="titleOf(previewArticles[0])"
                            format="webp"
                            width="800"
                            height="450"
                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                        />
                        <div v-else class="w-full h-full bg-emerald-100 dark:bg-emerald-900/60 flex items-center justify-center">
                            <UIcon name="i-lucide-newspaper" class="w-10 h-10 text-emerald-400" />
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-t from-emerald-950/40 via-transparent to-transparent"></div>
                        <span class="absolute top-4 left-4 px-3 py-1 rounded-full text-xs font-bold bg-amber-400/90 text-emerald-950">
                            {{ locale === 'en' ? 'Featured' : 'Utama' }}
                        </span>
                    </div>
                    <!-- Content -->
                    <div class="p-5">
                        <h3 class="text-xl font-serif font-bold text-slate-900 dark:text-emerald-50 line-clamp-2 leading-snug mb-2 group-hover:text-emerald-700 dark:group-hover:text-amber-300 transition-colors">
                            {{ titleOf(previewArticles[0]) }}
                        </h3>
                        <p class="text-sm text-slate-500 dark:text-emerald-200/60 line-clamp-3 leading-relaxed">
                            {{ stripHtml(contentOf(previewArticles[0])) }}
                        </p>
                        <div class="mt-4 flex items-center gap-1 text-sm font-semibold text-emerald-600 dark:text-emerald-300">
                            <span>{{ locale === 'en' ? 'Read more' : 'Baca selengkapnya' }}</span>
                            <UIcon name="i-lucide-arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform" />
                        </div>
                    </div>
                </NuxtLink>

                <!-- Remaining articles -->
                <NuxtLink
                    v-for="(article, index) in previewArticles.slice(1)"
                    :key="article.id ?? index"
                    :to="localePath('/articles/' + slugOf(article))"
                    class="group rounded-3xl overflow-hidden border border-emerald-200/70 dark:border-emerald-800/60 bg-white/70 dark:bg-emerald-950/40 shadow-sm hover:-translate-y-1 hover:shadow-2xl transition-all duration-500 flex flex-col"
                    :data-aos="'fade-up'"
                    :data-aos-delay="(index + 1) * 100"
                >
                    <!-- Image -->
                    <div class="relative aspect-[16/9] overflow-hidden flex-shrink-0">
                        <NuxtImg
                            v-if="coverOf(article)"
                            :src="coverOf(article)"
                            :alt="titleOf(article)"
                            format="webp"
                            width="600"
                            height="338"
                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                        />
                        <div v-else class="w-full h-full bg-emerald-100 dark:bg-emerald-900/60 flex items-center justify-center">
                            <UIcon name="i-lucide-newspaper" class="w-8 h-8 text-emerald-400" />
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-t from-emerald-950/40 via-transparent to-transparent"></div>
                    </div>
                    <!-- Content -->
                    <div class="p-5 flex flex-col flex-1">
                        <h3 class="text-lg font-serif font-bold text-slate-900 dark:text-emerald-50 line-clamp-2 leading-snug mb-2 group-hover:text-emerald-700 dark:group-hover:text-amber-300 transition-colors">
                            {{ titleOf(article) }}
                        </h3>
                        <p class="text-sm text-slate-500 dark:text-emerald-200/60 line-clamp-2 leading-relaxed flex-1">
                            {{ stripHtml(contentOf(article)) }}
                        </p>
                        <div class="mt-4 flex items-center gap-1 text-sm font-semibold text-emerald-600 dark:text-emerald-300">
                            <span>{{ locale === 'en' ? 'Read more' : 'Baca selengkapnya' }}</span>
                            <UIcon name="i-lucide-arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform" />
                        </div>
                    </div>
                </NuxtLink>
            </div>

            <!-- Empty state -->
            <div v-else class="text-center py-16 text-slate-400 dark:text-emerald-600">
                <UIcon name="i-lucide-newspaper" class="w-12 h-12 mx-auto mb-3 opacity-40" />
                <p>{{ locale === 'en' ? 'No articles yet.' : 'Belum ada artikel.' }}</p>
            </div>

            <!-- CTA -->
            <div
                v-if="!pending && !error && previewArticles.length > 0"
                class="text-center mt-12"
                data-aos="fade-up"
            >
                <NuxtLink :to="localePath('/articles')">
                    <UButton
                        size="lg"
                        variant="outline"
                        color="emerald"
                        icon="i-lucide-newspaper"
                        trailing-icon="i-lucide-arrow-right"
                        class="group rounded-full px-8 border-emerald-400 dark:border-emerald-600 text-emerald-700 dark:text-emerald-300 hover:bg-emerald-50 dark:hover:bg-emerald-900/40"
                    >
                        {{ locale === 'en' ? 'View All Articles' : 'Lihat Semua Artikel' }}
                    </UButton>
                </NuxtLink>
            </div>
        </div>
    </section>
</template>

<script setup lang="ts">
import { useArticles } from '~/composables/useArticles';

const { locale } = useI18n();
const localePath = useLocalePath();

const {
    articles,
    pending,
    error,
    refresh,
    titleOf,
    contentOf,
    slugOf,
    coverOf,
} = useArticles();

// Ambil 3 artikel untuk preview beranda
const previewArticles = computed(() => articles.value.slice(0, 3));

// Strip HTML tags untuk preview konten (karena content bisa berupa HTML dari editor)
const stripHtml = (html: string): string => {
    if (!html) return '';
    return html.replace(/<[^>]*>/g, '').replace(/\s+/g, ' ').trim();
};
</script>
