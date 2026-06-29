<template>
    <section
        class="py-20 lg:py-28 bg-gradient-to-b from-emerald-50 via-white to-emerald-50 dark:from-gray-900 dark:via-emerald-950 dark:to-emerald-950 overflow-hidden relative transition-colors duration-500"
    >
        <div
            class="absolute top-0 right-1/4 w-80 h-80 bg-amber-400/10 dark:bg-amber-500/5 rounded-full blur-3xl pointer-events-none"
        ></div>
        <div
            class="absolute bottom-0 left-1/4 w-96 h-96 bg-emerald-500/10 dark:bg-emerald-500/5 rounded-full blur-3xl pointer-events-none"
        ></div>

        <svg
            class="absolute -top-6 -left-6 w-40 h-40 text-emerald-500/[0.05] dark:text-emerald-400/[0.03] pointer-events-none"
            viewBox="0 0 100 100"
            fill="none"
            stroke="currentColor"
            stroke-width="1"
        >
            <circle cx="50" cy="50" r="40" />
            <circle cx="50" cy="50" r="30" />
            <line x1="50" y1="10" x2="50" y2="20" />
            <line x1="50" y1="80" x2="50" y2="90" />
            <line x1="10" y1="50" x2="20" y2="50" />
            <line x1="80" y1="50" x2="90" y2="50" />
        </svg>

        <svg
            class="absolute bottom-12 right-12 w-20 h-20 text-amber-400/[0.06] dark:text-amber-400/[0.03] pointer-events-none"
            viewBox="0 0 100 100"
            fill="currentColor"
        >
            <polygon
                points="50,5 61,40 98,40 68,60 79,95 50,73 21,95 32,60 2,40 39,40"
            />
        </svg>

        <div
            class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-7xl relative z-10"
        >
            <div class="text-center max-w-2xl mx-auto mb-14">
                <span
                    data-aos="fade-up"
                    class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-[0.3em] text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/60 px-3.5 py-1.5 rounded-full border border-amber-200/50 dark:border-amber-900/40 mb-5"
                >
                    <UIcon name="i-lucide-images" class="w-4 h-4" />
                    {{ locale === "en" ? "Gallery" : "Galeri" }}
                </span>
                <h2
                    data-aos="fade-up"
                    data-aos-delay="50"
                    class="text-3xl md:text-5xl font-serif font-bold text-gray-900 dark:text-white tracking-tight leading-tight mb-4"
                >
                    {{
                        locale === "en"
                            ? "Moments & Activities"
                            : "Momen & Kegiatan"
                    }}
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
                    {{
                        locale === "en"
                            ? "A glimpse of our beautiful journey — certification moments, nature retreats, and the warmth of our community."
                            : "Sekilas perjalanan indah kami — momen sertifikasi, tadabbur alam, dan kehangatan komunitas para hafiz."
                    }}
                </p>
            </div>

            <div
                v-if="pending"
                class="columns-2 md:columns-3 lg:columns-4 gap-4"
            >
                <div
                    v-for="n in 8"
                    :key="n"
                    class="safari-masonry-item mb-4 rounded-2xl overflow-hidden"
                    :class="
                        n % 4 === 0
                            ? 'h-64'
                            : n % 4 === 1
                              ? 'h-44'
                              : n % 4 === 2
                                ? 'h-52'
                                : 'h-36'
                    "
                >
                    <USkeleton class="w-full h-full" />
                </div>
            </div>

            <div v-else-if="error" class="max-w-md mx-auto">
                <UAlert
                    icon="i-heroicons-exclamation-triangle"
                    color="red"
                    variant="soft"
                    :title="
                        locale === 'en'
                            ? 'Failed to load gallery'
                            : 'Gagal memuat galeri'
                    "
                    :description="
                        locale === 'en'
                            ? 'Please try again later.'
                            : 'Silakan coba lagi.'
                    "
                >
                    <template #actions>
                        <UButton
                            size="sm"
                            color="red"
                            variant="soft"
                            icon="i-heroicons-arrow-path"
                            @click="refresh"
                        >
                            {{ locale === "en" ? "Retry" : "Coba Lagi" }}
                        </UButton>
                    </template>
                </UAlert>
            </div>

            <div
                v-else-if="previewItems.length > 0"
                data-aos="fade-up"
                data-aos-delay="100"
                class="columns-2 md:columns-3 lg:columns-4 gap-4"
            >
                <div
                    v-for="(item, index) in previewItems"
                    :key="item.id ?? index"
                    class="safari-masonry-item isolate mb-4 group relative overflow-hidden rounded-2xl cursor-pointer shadow-sm hover:shadow-xl transition-all duration-500"
                    @click="openLightbox(item)"
                >
                    <div
                        :class="[
                            'overflow-hidden',
                            index % 5 === 0
                                ? 'aspect-[3/4]'
                                : index % 5 === 1
                                  ? 'aspect-square'
                                  : index % 5 === 2
                                    ? 'aspect-[4/3]'
                                    : index % 5 === 3
                                      ? 'aspect-[3/4]'
                                      : 'aspect-[4/5]',
                        ]"
                    >
                        <NuxtImg
                            v-if="firstImageOf(item)"
                            :src="firstImageOf(item)"
                            :alt="getDynamicTitle(item)"
                            format="webp"
                            width="600"
                            height="600"
                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110 transform-gpu"
                        />
                        <div
                            v-else
                            class="w-full h-full flex items-center justify-center bg-emerald-100 dark:bg-emerald-900/60"
                        >
                            <UIcon
                                name="i-lucide-image"
                                class="w-8 h-8 text-emerald-400"
                            />
                        </div>
                    </div>

                    <div
                        v-if="imagesOf(item).length > 1"
                        class="absolute top-3 right-3 bg-emerald-950/70 text-white backdrop-blur-sm px-2.5 py-1 rounded-lg text-xs font-semibold flex items-center gap-1.5 z-20"
                    >
                        <UIcon
                            name="i-lucide-layers"
                            class="w-3.5 h-3.5 text-amber-400"
                        />
                        {{ imagesOf(item).length }}
                    </div>

                    <div
                        class="absolute inset-0 bg-gradient-to-t from-emerald-950/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-4"
                    >
                        <div
                            class="translate-y-3 group-hover:translate-y-0 transition-transform duration-300 w-full"
                        >
                            <span
                                class="inline-block text-[10px] font-bold uppercase text-amber-400 bg-amber-400/10 px-2 py-0.5 rounded-md mb-1.5 border border-amber-400/20"
                            >
                                {{ item.category }}
                            </span>
                            <p
                                class="text-white font-semibold text-sm line-clamp-2 leading-snug"
                            >
                                {{ getDynamicTitle(item) }}
                            </p>
                            <div class="flex items-center gap-1 mt-1">
                                <UIcon
                                    name="i-lucide-zoom-in"
                                    class="w-3 h-3 text-amber-300"
                                />
                                <span class="text-amber-300 text-xs">
                                    {{
                                        locale === "en"
                                            ? "View Album"
                                            : "Lihat Album"
                                    }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div
                v-else
                class="text-center py-16 text-slate-400 dark:text-emerald-600"
            >
                <EmptyData
                    title="gallery"
                    description="Belum ada foto galeri."
                    icon="i-lucide-images"
                />
            </div>

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
                        {{
                            locale === "en"
                                ? "View Full Gallery"
                                : "Lihat Semua Galeri"
                        }}
                    </UButton>
                </NuxtLink>
            </div>
        </div>

        <UModal v-model:open="lightboxOpen" fullscreen>
            <template #content>
                <div
                    class="relative w-full h-full bg-black/95 flex flex-col items-center justify-center select-none"
                    @click.self="lightboxOpen = false"
                >
                    <button
                        class="absolute top-4 right-4 z-50 text-white/70 hover:text-white transition-colors p-2 bg-white/5 hover:bg-white/10 rounded-full backdrop-blur-sm"
                        @click="lightboxOpen = false"
                    >
                        <UIcon name="i-lucide-x" class="w-7 h-7" />
                    </button>

                    <button
                        v-if="imageIndex > 0"
                        class="absolute left-4 top-1/2 -translate-y-1/2 z-50 text-white/70 hover:text-white transition-colors bg-white/5 hover:bg-white/10 p-3 rounded-full backdrop-blur-sm"
                        @click="imageIndex--"
                    >
                        <UIcon name="i-lucide-chevron-left" class="w-8 h-8" />
                    </button>

                    <NuxtImg
                        v-if="currentImageSrc"
                        :src="currentImageSrc"
                        :alt="getDynamicTitle(activeItem)"
                        format="webp"
                        class="max-w-[90vw] max-h-[65vh] md:max-h-[72vh] object-contain rounded-xl shadow-2xl transition-all duration-300 border border-white/5"
                    />

                    <div
                        v-if="activeItem"
                        class="mt-6 text-center px-6 py-5 w-full max-w-2xl bg-zinc-900/80 backdrop-blur-md rounded-2xl border border-white/10 shadow-2xl mx-4"
                    >
                        <div class="mb-2.5">
                            <span
                                class="inline-flex items-center gap-1.5 text-[11px] font-bold uppercase tracking-wider text-amber-400 bg-amber-500/10 px-3 py-1 rounded-md border border-amber-500/20"
                            >
                                <UIcon name="i-lucide-tag" class="w-3 h-3" />
                                {{ activeItem.category }}
                            </span>
                        </div>

                        <h3
                            class="text-white font-serif font-bold text-lg md:text-xl leading-snug"
                        >
                            {{ getDynamicTitle(activeItem) }}
                        </h3>

                        <p
                            class="text-emerald-100/60 text-xs md:text-sm mt-2 leading-relaxed max-h-20 overflow-y-auto custom-scrollbar pr-1 font-medium"
                        >
                            {{ getDynamicDesc(activeItem) }}
                        </p>

                        <div
                            class="text-white/40 text-[10px] font-bold mt-4 tracking-widest uppercase flex items-center justify-center gap-1.5"
                        >
                            <UIcon
                                name="i-lucide-layers"
                                class="w-3.5 h-3.5 text-emerald-500"
                            />
                            {{ imageIndex + 1 }} / {{ totalImages }}
                            {{ locale === "en" ? "Photos" : "Foto" }}
                        </div>
                    </div>

                    <button
                        v-if="imageIndex < totalImages - 1"
                        class="absolute right-4 top-1/2 -translate-y-1/2 z-50 text-white/70 hover:text-white transition-colors bg-white/5 hover:bg-white/10 p-3 rounded-full backdrop-blur-sm"
                        @click="imageIndex++"
                    >
                        <UIcon name="i-lucide-chevron-right" class="w-8 h-8" />
                    </button>
                </div>
            </template>
        </UModal>
    </section>
</template>

<script setup lang="ts">
import { useGallery } from "~/composables/useGallery";

const { locale } = useI18n();
const localePath = useLocalePath();

const { mediaItems, pending, error, refresh, titleOf, descOf, imagesOf } =
    useGallery();

// Ambil maksimal 8 item album untuk dipajang di beranda (Homepage Preview)
const previewItems = computed(() => mediaItems.value.slice(0, 8));

// Ambil gambar ke-1 untuk sampul card Grid Masonry
const firstImageOf = (item: any): string => {
    const imgs = imagesOf(item);
    return imgs.length > 0 ? imgs[0] : "";
};

// --- CONTROLLER LOGIKA LIGHTBOX ALBUM ---
const lightboxOpen = ref(false);
const activeItem = ref<any>(null); // Menyimpan objek data album utuh yang sedang aktif dibuka
const imageIndex = ref(0); // Menyimpan indeks gambar yang sedang aktif dilihat di dalam album

// Mengumpulkan semua URL gambar milik album aktif
const activeAlbumImages = computed<string[]>(() => {
    if (!activeItem.value) return [];
    return imagesOf(activeItem.value);
});

const totalImages = computed(() => activeAlbumImages.value.length);

const currentImageSrc = computed(() => {
    return activeAlbumImages.value[imageIndex.value] ?? "";
});

// Dipanggil saat salah satu album diklik
const openLightbox = (item: any) => {
    activeItem.value = item;
    imageIndex.value = 0; // Kembalikan ke foto pertama dari album ini
    lightboxOpen.value = true;
};

// --- HELPER TRANSLATION BACKUP UNTUK LARAVEL FILLABLE DATA ---
const getDynamicTitle = (item: any): string => {
    if (!item) return "";
    // Menggunakan helper dari composable Anda, jika kosong/gagal, ambil langsung properti laravel fillable
    return (
        titleOf(item) ||
        (locale.value === "en" ? item.title_en : item.title_id) ||
        ""
    );
};

const getDynamicDesc = (item: any): string => {
    if (!item) return "";
    return (
        descOf(item) ||
        (locale.value === "en" ? item.description_en : item.description_id) ||
        ""
    );
};
</script>

<style scoped>
/* Safari Webkit Fixes for Masonry Columns */
.safari-masonry-item {
    display: inline-block;
    width: 100%;
    -webkit-column-break-inside: avoid;
    page-break-inside: avoid;
    break-inside: avoid;

    /* Hardware Acceleration Fixes */
    -webkit-backface-visibility: hidden;
    backface-visibility: hidden;
    -webkit-perspective: 1000px;
    perspective: 1000px;
    -webkit-transform: translateZ(0);
    transform: translateZ(0);
    will-change: transform, opacity;
}

/* Desain Scrollbar Halus untuk teks Deskripsi Panjang */
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.03);
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(245, 158, 11, 0.3); /* Warna Amber Khas Tema Anda */
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(245, 158, 11, 0.5);
}
</style>
