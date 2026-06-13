<template>
    <div
        class="bg-slate-50 dark:bg-emerald-950 text-slate-900 dark:text-white min-h-screen"
    >
        <GalleryHero />

        <!-- Filters Section -->
        <section
            class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-12 flex flex-wrap justify-center gap-3"
        >
            <button
                v-for="filter in filters"
                :key="filter.value"
                class="px-6 py-2.5 rounded-full border text-sm font-semibold tracking-wide transition-all duration-300 transform active:scale-95"
                :class="
                    category === filter.value
                        ? 'bg-emerald-600 border-emerald-600 text-white shadow-lg shadow-emerald-600/20 dark:bg-amber-500 dark:border-amber-500 dark:text-emerald-950 dark:shadow-amber-500/25'
                        : 'border-slate-200 dark:border-emerald-800/80 text-slate-600 dark:text-emerald-200/80 hover:border-emerald-500 dark:hover:border-amber-500 hover:text-emerald-600 dark:hover:text-amber-400 bg-white/80 dark:bg-emerald-950/40 backdrop-blur-sm'
                "
                @click="category = filter.value"
            >
                {{ locale === "en" ? filter.labelEn : filter.labelId }}
            </button>
        </section>

        <section class="py-10 lg:py-14">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Loading Skeleton -->
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

                <!-- Error -->
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

                <!-- Empty -->
                <div v-else-if="!mediaItems.length" class="text-center py-20">
                    <EmptyData
                        title="Media"
                        description="Belum ada item galeri untuk kata kunci ini."
                    />
                </div>

                <!-- Masonry Gallery Grid -->
                <div
                    v-else
                    class="columns-1 sm:columns-2 lg:columns-3 gap-5 space-y-5"
                >
                    <div
                        v-for="(item, index) in mediaItems"
                        :key="item.id"
                        class="break-inside-avoid group cursor-pointer"
                        @click="openLightbox(index)"
                    >
                        <div
                            class="relative overflow-hidden rounded-2xl bg-white dark:bg-emerald-900/20 border border-emerald-100/60 dark:border-emerald-800/40 shadow-sm hover:shadow-xl transition-all duration-500"
                            :class="getCategoryBorder(item.category)"
                        >
                            <!-- Cover Image -->
                            <div
                                class="overflow-hidden relative"
                                :class="getCardHeight(index)"
                            >
                                <img
                                    v-if="coverOf(item)"
                                    :src="coverOf(item)"
                                    :alt="titleOf(item)"
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

                                <!-- Multi-image badge -->
                                <div
                                    v-if="imagesOf(item).length > 1"
                                    class="absolute top-3 right-3 px-2.5 py-1 rounded-full bg-black/50 backdrop-blur-sm text-white text-xs font-semibold flex items-center gap-1 z-10"
                                >
                                    <UIcon
                                        name="i-lucide-layers"
                                        class="w-3.5 h-3.5"
                                    />
                                    {{ imagesOf(item).length }}
                                </div>

                                <!-- Hover overlay -->
                                <div
                                    class="absolute inset-0 bg-gradient-to-t from-emerald-950/85 via-emerald-950/30 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 flex items-end p-5 z-10"
                                >
                                    <div>
                                        <span
                                            class="inline-block text-xs font-bold uppercase tracking-[0.2em] text-amber-400 mb-2"
                                        >
                                            {{
                                                getCategoryLabel(item.category)
                                            }}
                                        </span>
                                        <h3
                                            class="text-white font-serif font-bold text-lg leading-snug"
                                        >
                                            {{ titleOf(item) }}
                                        </h3>
                                        <p
                                            class="mt-1.5 text-emerald-100/80 text-sm line-clamp-2 leading-relaxed"
                                        >
                                            {{ descOf(item) }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Card Footer with title (visible when not hovered) -->
                            <div
                                class="p-4 group-hover:bg-emerald-50/50 dark:group-hover:bg-emerald-900/30 transition-colors duration-300"
                            >
                                <h3
                                    class="font-serif font-bold text-sm text-emerald-950 dark:text-emerald-50 line-clamp-1"
                                >
                                    {{ titleOf(item) }}
                                </h3>
                                <p
                                    class="mt-1 text-xs text-slate-500 dark:text-emerald-100/50 line-clamp-1"
                                >
                                    {{ descOf(item) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
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

        <!-- Lightbox Overlay -->
        <Teleport to="body">
            <Transition name="lightbox">
                <div
                    v-if="lightboxOpen"
                    class="fixed inset-0 z-[100] flex items-center justify-center bg-black/90 backdrop-blur-sm"
                    @click.self="closeLightbox"
                    @keydown.escape="closeLightbox"
                >
                    <!-- Close button -->
                    <button
                        class="absolute top-5 right-5 z-10 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors"
                        @click="closeLightbox"
                    >
                        <UIcon name="i-lucide-x" class="w-6 h-6" />
                    </button>

                    <!-- Prev button -->
                    <button
                        v-if="lightboxIndex > 0"
                        class="absolute left-4 z-10 w-12 h-12 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors"
                        @click.stop="lightboxIndex--"
                    >
                        <UIcon name="i-lucide-chevron-left" class="w-7 h-7" />
                    </button>

                    <!-- Next button -->
                    <button
                        v-if="lightboxIndex < mediaItems.length - 1"
                        class="absolute right-4 z-10 w-12 h-12 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors"
                        @click.stop="lightboxIndex++"
                    >
                        <UIcon name="i-lucide-chevron-right" class="w-7 h-7" />
                    </button>

                    <!-- Image + Info -->
                    <div class="max-w-5xl mx-auto px-4 text-center" @click.stop>
                        <img
                            v-if="currentLightboxItem"
                            :src="coverOf(currentLightboxItem)"
                            :alt="titleOf(currentLightboxItem)"
                            class="max-h-[75vh] mx-auto rounded-xl shadow-2xl object-contain"
                        />
                        <div v-if="currentLightboxItem" class="mt-5">
                            <h3 class="text-white font-serif font-bold text-xl">
                                {{ titleOf(currentLightboxItem) }}
                            </h3>
                            <p
                                class="mt-2 text-white/60 text-sm max-w-2xl mx-auto"
                            >
                                {{ descOf(currentLightboxItem) }}
                            </p>
                            <p class="mt-2 text-white/30 text-xs">
                                {{ lightboxIndex + 1 }} /
                                {{ mediaItems.length }}
                            </p>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </div>
</template>

<script setup lang="ts">
import { useGallery } from "~/composables/useGallery";

const { locale } = useI18n();

const {
    page,
    category,
    mediaItems,
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
const filters = [
    { value: "", labelId: "Semua", labelEn: "All" },
    { value: "graduation", labelId: "Wisuda", labelEn: "Graduation" },
    { value: "academic", labelId: "Akademik", labelEn: "Academic" },
    { value: "tourism", labelId: "Wisata Alam", labelEn: "Tourism" },
    { value: "social", labelId: "Kegiatan Sosial", labelEn: "Social" },
];

const getCategoryBorder = (cat?: string) => {
    switch (cat) {
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
    const found = filters.find((f) => f.value === cat);
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

// --- Lightbox ---
const lightboxOpen = ref(false);
const lightboxIndex = ref(0);

const currentLightboxItem = computed(
    () => mediaItems.value?.[lightboxIndex.value] ?? null,
);

const openLightbox = (index: number) => {
    lightboxIndex.value = index;
    lightboxOpen.value = true;
    document.body.style.overflow = "hidden";
};

const closeLightbox = () => {
    lightboxOpen.value = false;
    document.body.style.overflow = "";
};

// Keyboard navigation
onMounted(() => {
    const handler = (e: KeyboardEvent) => {
        if (!lightboxOpen.value) return;
        if (e.key === "Escape") closeLightbox();
        if (e.key === "ArrowLeft" && lightboxIndex.value > 0)
            lightboxIndex.value--;
        if (
            e.key === "ArrowRight" &&
            lightboxIndex.value < mediaItems.value.length - 1
        )
            lightboxIndex.value++;
    };
    window.addEventListener("keydown", handler);
    onUnmounted(() => window.removeEventListener("keydown", handler));
});

// SEO
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
</style>
