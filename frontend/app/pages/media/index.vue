<template>
    <div
        class="bg-slate-50 dark:bg-emerald-950 text-slate-900 dark:text-white min-h-screen transition-colors duration-500"
    >
        <GalleryHero />

        <section
            class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-12 flex flex-wrap justify-center gap-3"
        >
            <button
                v-for="filter in filters"
                :key="filter.value"
                class="px-6 py-2.5 rounded-full border text-sm font-semibold tracking-wide transition-all duration-300 transform active:scale-95 cursor-pointer"
                :class="
                    category === filter.value
                        ? 'bg-emerald-600 border-emerald-600 text-white shadow-lg shadow-emerald-600/20 dark:bg-amber-500 dark:border-amber-500 dark:text-emerald-950 dark:shadow-amber-500/25 scale-105'
                        : 'border-slate-200 dark:border-emerald-800/80 text-slate-600 dark:text-emerald-200/80 hover:border-emerald-500 dark:hover:border-amber-500 hover:text-emerald-600 dark:hover:text-amber-400 bg-white/80 dark:bg-emerald-950/40 backdrop-blur-sm'
                "
                @click="category = filter.value"
            >
                {{ locale === "en" ? filter.labelEn : filter.labelId }}
            </button>
        </section>

        <section class="py-10 lg:py-14">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div
                    v-if="pending"
                    class="columns-2 md:columns-3 gap-5 space-y-5"
                >
                    <div
                        v-for="n in 9"
                        :key="n"
                        class="break-inside-avoid rounded-2xl overflow-hidden"
                        :class="
                            n % 3 === 0 ? 'h-72' : n % 3 === 1 ? 'h-56' : 'h-44'
                        "
                    >
                        <USkeleton class="w-full h-full" />
                    </div>
                </div>

                <div v-else-if="error" class="max-w-xl mx-auto">
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
                                : 'Silakan coba lagi beberapa saat.'
                        "
                    >
                        <template #actions>
                            <UButton size="sm" color="red" @click="refresh">
                                {{ locale === "en" ? "Retry" : "Coba lagi" }}
                            </UButton>
                        </template>
                    </UAlert>
                </div>

                <div v-else-if="!mediaItems.length" class="text-center py-20">
                    <EmptyData
                        title="Media"
                        description="Belum ada item galeri untuk kata kunci ini."
                    />
                </div>

                <div
                    v-else
                    class="columns-1 sm:columns-2 lg:columns-3 gap-5 space-y-5"
                >
                    <div
                        v-for="(item, index) in mediaItems"
                        :key="item.id"
                        class="break-inside-avoid group cursor-pointer"
                        @click="openLightbox(item)"
                    >
                        <div
                            class="relative overflow-hidden rounded-2xl bg-white dark:bg-emerald-900/20 border border-emerald-100/60 dark:border-emerald-800/40 shadow-sm hover:shadow-xl transition-all duration-500"
                            :class="getCategoryBorder(item.category)"
                        >
                            <div
                                class="overflow-hidden relative"
                                :class="getCardHeight(index)"
                            >
                                <img
                                    v-if="coverOf(item)"
                                    :src="coverOf(item)"
                                    :alt="getDynamicTitle(item)"
                                    class="w-full h-full object-cover grayscale-[30%] group-hover:grayscale-0 transition-all duration-700 transform group-hover:scale-105"
                                />
                                <div
                                    v-else
                                    class="w-full h-full flex items-center justify-center bg-gradient-to-br from-emerald-100 to-amber-50 dark:from-emerald-900/40 dark:to-emerald-950/60"
                                >
                                    <UIcon
                                        name="i-lucide-image"
                                        class="w-16 h-16 text-emerald-300 dark:text-emerald-700"
                                    />
                                </div>

                                <div
                                    v-if="imagesOf(item).length > 1"
                                    class="absolute top-3 right-3 px-2.5 py-1 rounded-lg bg-black/60 backdrop-blur-sm text-white text-xs font-semibold flex items-center gap-1.5 z-10"
                                >
                                    <UIcon
                                        name="i-lucide-layers"
                                        class="w-3.5 h-3.5 text-amber-400"
                                    />
                                    {{ imagesOf(item).length }}
                                </div>

                                <div
                                    class="absolute inset-0 bg-gradient-to-t from-emerald-950/85 via-emerald-950/30 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 flex items-end p-5 z-10"
                                >
                                    <div class="w-full">
                                        <span
                                            class="inline-block text-[10px] font-bold uppercase tracking-[0.2em] text-amber-400 mb-1.5 bg-amber-400/10 px-2 py-0.5 rounded-md border border-amber-400/20"
                                        >
                                            {{
                                                getCategoryLabel(item.category)
                                            }}
                                        </span>
                                        <h3
                                            class="text-white font-serif font-bold text-lg leading-snug line-clamp-1"
                                        >
                                            {{ getDynamicTitle(item) }}
                                        </h3>
                                        <p
                                            class="mt-1 text-emerald-100/80 text-xs line-clamp-2 leading-relaxed"
                                        >
                                            {{ getDynamicDesc(item) }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="p-4 group-hover:bg-emerald-50/50 dark:group-hover:bg-emerald-900/30 transition-colors duration-300"
                            >
                                <h3
                                    class="font-serif font-bold text-sm text-emerald-950 dark:text-emerald-50 line-clamp-1"
                                >
                                    {{ getDynamicTitle(item) }}
                                </h3>
                                <p
                                    class="mt-1 text-xs text-slate-500 dark:text-emerald-100/50 line-clamp-1"
                                >
                                    {{ getDynamicDesc(item) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <ProgramPagination
                    v-if="totalPages > 1"
                    :page="page"
                    :total-pages="totalPages"
                    :page-items="pageItems"
                    class="mt-14"
                    @change-page="changePage"
                />
            </div>
        </section>

        <Teleport to="body">
            <Transition name="lightbox">
                <div
                    v-if="lightboxOpen"
                    class="fixed inset-0 z-[100] flex flex-col items-center justify-center bg-black/95 backdrop-blur-sm select-none"
                    @click.self="closeLightbox"
                >
                    <button
                        class="absolute top-5 right-5 z-50 w-10 h-10 rounded-full bg-white/5 hover:bg-white/15 text-white flex items-center justify-center transition-colors backdrop-blur-md border border-white/10"
                        @click="closeLightbox"
                    >
                        <UIcon name="i-lucide-x" class="w-6 h-6" />
                    </button>

                    <button
                        v-if="imageIndex > 0"
                        class="absolute left-4 z-50 w-12 h-12 rounded-full bg-white/5 hover:bg-white/15 text-white flex items-center justify-center transition-colors backdrop-blur-md border border-white/10"
                        @click.stop="imageIndex--"
                    >
                        <UIcon name="i-lucide-chevron-left" class="w-7 h-7" />
                    </button>

                    <button
                        v-if="imageIndex < totalImages - 1"
                        class="absolute right-4 z-50 w-12 h-12 rounded-full bg-white/5 hover:bg-white/15 text-white flex items-center justify-center transition-colors backdrop-blur-md border border-white/10"
                        @click.stop="imageIndex++"
                    >
                        <UIcon name="i-lucide-chevron-right" class="w-7 h-7" />
                    </button>

                    <div
                        class="max-w-4xl w-full mx-auto px-4 flex flex-col items-center justify-center"
                        @click.stop
                    >
                        <img
                            v-if="currentImageSrc"
                            :src="currentImageSrc"
                            :alt="getDynamicTitle(activeItem)"
                            class="max-h-[62vh] md:max-h-[68vh] mx-auto rounded-xl shadow-2xl object-contain border border-white/5 transition-all duration-300"
                        />

                        <div
                            v-if="activeItem"
                            class="mt-5 text-center px-6 py-5 w-full bg-zinc-900/80 backdrop-blur-md rounded-2xl border border-white/10 shadow-2xl"
                        >
                            <div class="mb-2">
                                <span
                                    class="inline-flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider text-amber-400 bg-amber-500/10 px-2.5 py-1 rounded-md border border-amber-500/20"
                                >
                                    <UIcon
                                        name="i-lucide-tag"
                                        class="w-3 h-3"
                                    />
                                    {{ getCategoryLabel(activeItem.category) }}
                                </span>
                            </div>

                            <h3
                                class="text-white font-serif font-bold text-lg md:text-xl leading-snug"
                            >
                                {{ getDynamicTitle(activeItem) }}
                            </h3>

                            <p
                                class="text-emerald-100/60 text-xs md:text-sm mt-1.5 leading-relaxed max-h-20 overflow-y-auto custom-scrollbar pr-1 font-medium"
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
                    </div>
                </div>
            </Transition>
        </Teleport>
    </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from "vue";
import { useGallery } from "~/composables/useGallery";

const { locale } = useI18n();

const {
    page,
    category,
    mediaItems,
    existingCategories,
    totalPages,
    pending,
    error,
    pageItems,
    titleOf,
    descOf,
    imagesOf,
    coverOf,
    changePage,
    refresh,
} = useGallery();

// --- Filter categories list ---
const categoryTranslations: Record<string, { labelId: string; labelEn: string }> = {
    graduation: { labelId: "Wisuda", labelEn: "Graduation" },
    wisuda: { labelId: "Wisuda", labelEn: "Graduation" },
    academic: { labelId: "Akademik", labelEn: "Academic" },
    akademik: { labelId: "Akademik", labelEn: "Academic" },
    tourism: { labelId: "Wisata Alam", labelEn: "Tourism" },
    social: { labelId: "Kegiatan Sosial", labelEn: "Social" },
    sosial: { labelId: "Kegiatan Sosial", labelEn: "Social" },
};

const filters = computed(() => {
    const list = [{ value: "", labelId: "Semua", labelEn: "All" }];
    existingCategories.value.forEach((cat) => {
        if (!cat) return;
        const trans = categoryTranslations[cat.toLowerCase()] || {
            labelId: cat.charAt(0).toUpperCase() + cat.slice(1),
            labelEn: cat.charAt(0).toUpperCase() + cat.slice(1),
        };
        list.push({
            value: cat.toLowerCase(),
            labelId: trans.labelId,
            labelEn: trans.labelEn,
        });
    });
    return list;
});

const getCategoryBorder = (cat?: string) => {
    switch (cat?.toLowerCase()) {
        case "graduation":
            return "border-t-2 border-amber-500 dark:border-amber-400";
        case "academic":
            return "border-t-2 border-emerald-600 dark:border-emerald-500";
        case "tourism":
            return "border-t-2 border-teal-500 dark:border-teal-400";
        case "social":
            return "border-t-2 border-emerald-500 dark:border-emerald-400";
        default:
            return "border-t-2 border-emerald-600 dark:border-emerald-500";
    }
};

const getCategoryLabel = (cat?: string) => {
    if (!cat) return "";
    const found = filters.value.find((f) => f.value === cat.toLowerCase());
    if (!found) return cat;
    return locale.value === "en" ? found.labelEn : found.labelId;
};

// --- Masonry height variation ---
const getCardHeight = (index: number) => {
    const pattern = index % 6;
    switch (pattern) {
        case 0:
            return "h-64 sm:h-72";
        case 1:
            return "h-48 sm:h-56";
        case 2:
            return "h-56 sm:h-64";
        case 3:
            return "h-44 sm:h-52";
        case 4:
            return "h-60 sm:h-72";
        case 5:
            return "h-48 sm:h-56";
        default:
            return "h-56";
    }
};

// --- CONTROLLER LOGIKA BARU LIGHTBOX (ARRAY FOTO BERDASARKAN ID ALBUM) ---
const lightboxOpen = ref(false);
const activeItem = ref<any>(null); // Menyimpan objek data album utuh yang sedang aktif dibuka
const imageIndex = ref(0); // Menyimpan indeks gambar internal aktif di dalam album tersebut

// Mengambil list seluruh foto khusus milik album aktif
const activeAlbumImages = computed<string[]>(() => {
    if (!activeItem.value) return [];
    return imagesOf(activeItem.value);
});

const totalImages = computed(() => activeAlbumImages.value.length);

const currentImageSrc = computed(() => {
    return activeAlbumImages.value[imageIndex.value] ?? "";
});

const openLightbox = (item: any) => {
    activeItem.value = item;
    imageIndex.value = 0; // Mulai dari foto pertama di dalam album ini
    lightboxOpen.value = true;
    document.body.style.overflow = "hidden";
};

const closeLightbox = () => {
    lightboxOpen.value = false;
    activeItem.value = null;
    document.body.style.overflow = "";
};

// --- HELPER PENANGANAN TRANSLASI MULTI-BAHASA LARAVEL FILLABLE DATA ---
const getDynamicTitle = (item: any): string => {
    if (!item) return "";
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

// Navigasi Menggunakan Keyboard panah kiri kanan
const handleKeydown = (e: KeyboardEvent) => {
    if (!lightboxOpen.value) return;
    if (e.key === "Escape") closeLightbox();
    if (e.key === "ArrowLeft" && imageIndex.value > 0) {
        imageIndex.value--;
    }
    if (e.key === "ArrowRight" && imageIndex.value < totalImages.value - 1) {
        imageIndex.value++;
    }
};

onMounted(() => {
    window.addEventListener("keydown", handleKeydown);
});

onUnmounted(() => {
    window.removeEventListener("keydown", handleKeydown);
});

// SEO Meta setup
useSeoMeta({
    title: () => (locale.value === "en" ? "Gallery - YHIE" : "Galeri - YHIE"),
    description: () =>
        locale.value === "en"
            ? "Explore moments and memories from Yayasan Hafiz Indonesia Emas programs and events."
            : "Jelajahi momen dan kenangan dari program dan acara Yayasan Hafiz Indonesia Emas.",
    ogTitle: () => (locale.value === "en" ? "Gallery - YHIE" : "Galeri - YHIE"),
    ogDescription: () =>
        locale.value === "en"
            ? "Beautiful gallery showcasing our Qur'anic education journey."
            : "Galeri indah yang memperlihatkan perjalanan pendidikan Al-Qur'an kami.",
});
</script>

<style scoped>
.lightbox-enter-active,
.lightbox-leave-active {
    transition: opacity 0.3s ease;
}
.lightbox-enter-from,
.lightbox-leave-to {
    opacity: 0;
}

/* Kustomisasi Scrollbar Mini untuk Box Deskripsi */
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.03);
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(245, 158, 11, 0.3);
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(245, 158, 11, 0.5);
}
</style>
