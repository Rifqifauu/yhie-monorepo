<template>
    <section
        class="py-20 lg:py-28 border-t border-emerald-900/20 dark:border-emerald-500/20 bg-gradient-to-b from-emerald-800/30 via-white to-emerald-50 dark:from-slate-950 dark:via-emerald-950 dark:to-emerald-950 overflow-hidden relative transition-colors duration-500"
    >
        <!-- Decorative blobs -->
        <div class="absolute top-0 right-1/4 w-80 h-80 bg-amber-400/10 dark:bg-amber-500/5 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 left-1/4 w-96 h-96 bg-emerald-500/10 dark:bg-emerald-500/5 rounded-full blur-3xl pointer-events-none"></div>

        <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-7xl relative z-10">

            <!-- Header -->
            <div class="text-center max-w-2xl mx-auto mb-14">
                <span
                    data-aos="fade-up"
                    class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-[0.3em] text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/60 px-3.5 py-1.5 rounded-full border border-amber-200/50 dark:border-amber-900/40 mb-5"
                >
                    <UIcon name="i-lucide-images" class="w-4 h-4" />
                    {{ locale === 'en' ? 'Gallery' : 'Galeri' }}
                </span>
                <h2
                    data-aos="fade-up"
                    data-aos-delay="50"
                    class="text-3xl md:text-5xl font-serif font-bold text-gray-900 dark:text-white tracking-tight leading-tight mb-4"
                >
                    {{ locale === 'en' ? 'Moments & Activities' : 'Momen & Kegiatan' }}
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
                        ? 'A glimpse of our beautiful journey — certification moments, nature retreats, and the warmth of our community.'
                        : 'Sekilas perjalanan indah kami — momen sertifikasi, tadabbur alam, dan kehangatan komunitas para hafiz.' }}
                </p>
            </div>

            <!-- Loading skeleton -->
            <div v-if="pending" class="columns-2 md:columns-3 lg:columns-4 gap-4 space-y-4">
                <div
                    v-for="n in 8"
                    :key="n"
                    class="break-inside-avoid rounded-2xl overflow-hidden"
                    :class="n % 4 === 0 ? 'h-64' : n % 4 === 1 ? 'h-44' : n % 4 === 2 ? 'h-52' : 'h-36'"
                >
                    <USkeleton class="w-full h-full" />
                </div>
            </div>

            <!-- Error -->
            <div v-else-if="error" class="max-w-md mx-auto">
                <UAlert
                    icon="i-heroicons-exclamation-triangle"
                    color="red"
                    variant="soft"
                    :title="locale === 'en' ? 'Failed to load gallery' : 'Gagal memuat galeri'"
                    :description="locale === 'en' ? 'Please try again later.' : 'Silakan coba lagi.'"
                >
                    <template #actions>
                        <UButton size="sm" color="red" variant="soft" icon="i-heroicons-arrow-path" @click="refresh">
                            {{ locale === 'en' ? 'Retry' : 'Coba Lagi' }}
                        </UButton>
                    </template>
                </UAlert>
            </div>

            <!-- Masonry grid -->
            <div
                v-else-if="previewItems.length > 0"
                data-aos="fade-up"
                data-aos-delay="100"
                class="columns-2 md:columns-3 lg:columns-4 gap-4 space-y-4"
            >
                <div
                    v-for="(item, index) in previewItems"
                    :key="item.id ?? index"
                    class="break-inside-avoid group relative overflow-hidden rounded-2xl cursor-pointer shadow-sm hover:shadow-xl transition-all duration-500"
                    :data-aos="'fade-up'"
                    :data-aos-delay="index * 60"
                    @click="openLightbox(index)"
                >
                    <div
                        :class="[
                            'overflow-hidden',
                            index % 5 === 0 ? 'aspect-[3/4]' :
                            index % 5 === 1 ? 'aspect-square' :
                            index % 5 === 2 ? 'aspect-[4/3]' :
                            index % 5 === 3 ? 'aspect-[3/4]' : 'aspect-[4/5]'
                        ]"
                    >
                        <NuxtImg
                            v-if="firstImageOf(item)"
                            :src="firstImageOf(item)"
                            :alt="titleOf(item)"
                            format="webp"
                            width="600"
                            height="600"
                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                        />
                        <div v-else class="w-full h-full flex items-center justify-center bg-emerald-100 dark:bg-emerald-900/60">
                            <UIcon name="i-lucide-image" class="w-8 h-8 text-emerald-400" />
                        </div>
                    </div>

                    <!-- Hover overlay -->
                    <div class="absolute inset-0 bg-gradient-to-t from-emerald-950/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-4">
                        <div class="translate-y-3 group-hover:translate-y-0 transition-transform duration-300">
                            <p class="text-white font-semibold text-sm line-clamp-2 leading-snug">{{ titleOf(item) }}</p>
                            <div class="flex items-center gap-1 mt-1">
                                <UIcon name="i-lucide-zoom-in" class="w-3 h-3 text-amber-300" />
                                <span class="text-amber-300 text-xs">{{ locale === 'en' ? 'View' : 'Lihat' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty state -->
            <div v-else class="text-center py-16 text-slate-400 dark:text-emerald-600">
                <UIcon name="i-lucide-images" class="w-12 h-12 mx-auto mb-3 opacity-40" />
                <p>{{ locale === 'en' ? 'No gallery items yet.' : 'Belum ada foto galeri.' }}</p>
            </div>

            <!-- CTA -->
            <div
                v-if="!pending && !error && previewItems.length > 0"
                class="text-center mt-12"
                data-aos="fade-up"
            >
                <NuxtLink :to="localePath('/media')">
                    <UButton
                        size="lg"
                        variant="outline"
                        color="emerald"
                        icon="i-lucide-images"
                        trailing-icon="i-lucide-arrow-right"
                        class="group rounded-full px-8 border-emerald-400 dark:border-emerald-600 text-emerald-700 dark:text-emerald-300 hover:bg-emerald-50 dark:hover:bg-emerald-900/40"
                    >
                        {{ locale === 'en' ? 'View Full Gallery' : 'Lihat Semua Galeri' }}
                    </UButton>
                </NuxtLink>
            </div>
        </div>

        <!-- Lightbox modal -->
        <UModal v-model:open="lightboxOpen" fullscreen>
            <template #content>
                <div class="relative w-full h-full bg-black/95 flex flex-col items-center justify-center" @click.self="lightboxOpen = false">
                    <!-- Close -->
                    <button
                        class="absolute top-4 right-4 z-50 text-white/70 hover:text-white transition-colors"
                        @click="lightboxOpen = false"
                    >
                        <UIcon name="i-lucide-x" class="w-8 h-8" />
                    </button>

                    <!-- Nav prev -->
                    <button
                        v-if="lightboxIndex > 0"
                        class="absolute left-4 top-1/2 -translate-y-1/2 z-50 text-white/70 hover:text-white transition-colors"
                        @click="lightboxIndex--"
                    >
                        <UIcon name="i-lucide-chevron-left" class="w-10 h-10" />
                    </button>

                    <!-- Image -->
                    <NuxtImg
                        v-if="lightboxCurrent"
                        :src="firstImageOf(lightboxCurrent)"
                        :alt="titleOf(lightboxCurrent)"
                        format="webp"
                        class="max-w-[90vw] max-h-[80vh] object-contain rounded-lg shadow-2xl"
                    />

                    <!-- Caption -->
                    <div v-if="lightboxCurrent" class="mt-4 text-center px-8">
                        <p class="text-white font-semibold text-lg">{{ titleOf(lightboxCurrent) }}</p>
                        <p class="text-white/60 text-sm mt-1">{{ descOf(lightboxCurrent) }}</p>
                        <p class="text-white/40 text-xs mt-2">{{ lightboxIndex + 1 }} / {{ previewItems.length }}</p>
                    </div>

                    <!-- Nav next -->
                    <button
                        v-if="lightboxIndex < previewItems.length - 1"
                        class="absolute right-4 top-1/2 -translate-y-1/2 z-50 text-white/70 hover:text-white transition-colors"
                        @click="lightboxIndex++"
                    >
                        <UIcon name="i-lucide-chevron-right" class="w-10 h-10" />
                    </button>
                </div>
            </template>
        </UModal>
    </section>
</template>

<script setup lang="ts">
import { useGallery } from '~/composables/useGallery';

const { locale } = useI18n();
const localePath = useLocalePath();

const {
    mediaItems,
    pending,
    error,
    refresh,
    titleOf,
    descOf,
    imagesOf,
} = useGallery();

// Ambil maksimal 8 item untuk preview di beranda
const previewItems = computed(() => mediaItems.value.slice(0, 8));

// Helper: ambil gambar pertama dari item
const firstImageOf = (item: any): string => {
    const imgs = imagesOf(item);
    return imgs.length > 0 ? imgs[0] : '';
};

// Lightbox state
const lightboxOpen = ref(false);
const lightboxIndex = ref(0);
const lightboxCurrent = computed(() => previewItems.value[lightboxIndex.value] ?? null);

const openLightbox = (index: number) => {
    lightboxIndex.value = index;
    lightboxOpen.value = true;
};
</script>
