<template>
    <div class="space-y-6 flex flex-col h-full mx-auto w-full">
        <AdminHeader
            title="Media"
            icon="i-lucide-images"
            description="Kelola media dan galeri Anda di sini. Anda dapat menambah, mengedit, dan menghapus media melalui halaman ini."
            :showCreate="true"
            createRoute="/admin/media/create"
        >
            <template #actions>
                <div class="flex flex-wrap items-center gap-3">
                    <UInput
                        v-model="searchInput"
                        icon="i-lucide-search"
                        placeholder="Cari nama media..."
                        size="lg"
                        class="min-w-0 sm:w-72"
                        @keyup.enter="applySearch"
                    />

                    <USelect
                        v-model="categorySelectValue"
                        :items="categoryOptions"
                        value-key="value"
                        size="lg"
                        class="w-48"
                    />

                    <UButton
                        color="neutral"
                        variant="soft"
                        icon="i-lucide-rotate-cw"
                        @click="refresh"
                        >Muat ulang</UButton
                    >
                    <UButton
                        color="neutral"
                        variant="ghost"
                        icon="i-lucide-eraser"
                        @click="clearSearch"
                        >Reset</UButton
                    >
                </div>
            </template>
        </AdminHeader>

        <div
            class="flex-1 bg-white dark:bg-gray-900 ring-1 ring-gray-200 dark:ring-gray-800 rounded-lg shadow-sm overflow-hidden flex flex-col"
        >
            <div class="overflow-x-auto scrollbar-hide">
                <div class="min-w-max">
                    <UTable
                        :data="mediaItems"
                        :columns="columns"
                        :loading="pending"
                        class="w-full min-w-max"
                    />
                </div>
            </div>
        </div>

        <div
            v-if="totalItems > 0"
            class="flex flex-col sm:flex-row justify-between items-center gap-4 pt-2"
        >
            <span class="text-sm text-gray-500">
                Menampilkan
                <span class="font-medium text-gray-900 dark:text-white">{{
                    fromItem
                }}</span>
                -
                <span class="font-medium text-gray-900 dark:text-white">{{
                    toItem
                }}</span>
                dari
                <span class="font-medium text-gray-900 dark:text-white">{{
                    totalItems
                }}</span>
                media
            </span>

            <UPagination
                v-model:page="page"
                :total="totalItems"
                :items-per-page="10"
                @update:model-value="changePage"
            />
        </div>

        <div
            v-else-if="!pending && totalItems === 0"
            class="flex flex-col items-center justify-center py-12 text-center ring-1 ring-gray-200 dark:ring-gray-800 rounded-lg bg-white dark:bg-gray-900"
        >
            <UIcon
                name="i-lucide-file-search"
                class="w-12 h-12 text-gray-400 mb-4"
            />
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                Tidak ada media
            </h3>
            <p class="text-gray-500 mt-1 max-w-sm">
                Coba sesuaikan kata kunci pencarian atau filter yang Anda
                gunakan.
            </p>
            <UButton
                label="Bersihkan Filter"
                color="neutral"
                variant="soft"
                class="mt-4"
                @click="resetFilters"
            />
        </div>
        <AdminDeleteModal
            v-model:open="isDeleteModalOpen"
            :id="selectedMedia?.id"
            :title="selectedMedia?.title_id"
            endpoint="api/media"
            @success="handleDeleteSuccess"
            @error="handleDeleteError"
        />
    </div>
</template>

<script setup lang="ts">
import { h, resolveComponent, computed, ref } from "vue";
import type { TableColumn } from "@nuxt/ui";
import type { Row } from "@tanstack/vue-table";
import { useClipboard } from "@vueuse/core";
// Asumsi GalleryMedia diexport dari file composable Anda
import type { GalleryMedia } from "~/composables/useGallery";

definePageMeta({
    layout: "admin",
});

// Resolusi komponen Nuxt UI
const UButton = resolveComponent("UButton");
const UDropdownMenu = resolveComponent("UDropdownMenu");
const UBadge = resolveComponent("UBadge");
const UAvatar = resolveComponent("UAvatar");

const toast = useToast();
const { copy } = useClipboard();

// 1. Ekstrak data dan fungsi dari useGallery
const {
    mediaItems,
    pending,
    page,
    totalItems,
    category,
    existingCategories,
    changePage,
    coverOf, // Gunakan helper ini untuk mengambil thumbnail
    searchInput,
    applySearch,
    clearSearch,
    refresh,
} = useGallery();

// Nilai "" tidak boleh dipakai langsung sebagai value item Select (reserved
// oleh komponennya untuk state kosong/placeholder, bikin "Semua Kategori"
// tidak bisa dipilih balik). Pakai sentinel lalu terjemahkan ke "" di sini.
const CATEGORY_ALL = "__all__";

const categoryOptions = computed(() => [
    { label: "Semua Kategori", value: CATEGORY_ALL },
    ...existingCategories.value.map((c) => ({
        label: c.category_id,
        value: c.category_id,
    })),
]);

const categorySelectValue = computed({
    get: () => category.value || CATEGORY_ALL,
    set: (val: string) => {
        category.value = val === CATEGORY_ALL ? "" : val;
    },
});

// 2. Kalkulasi fromItem dan toItem secara lokal
const fromItem = computed(() => {
    if (totalItems.value === 0) return 0;
    return (page.value - 1) * 10 + 1;
});

const toItem = computed(() => {
    if (totalItems.value === 0) return 0;
    return (page.value - 1) * 10 + mediaItems.value.length;
});

// 3. Buat aksi reset filter
const resetFilters = () => {
    category.value = "";
    page.value = 1;
    clearSearch();
};

const isDeleteModalOpen = ref(false);
const selectedMedia = ref<{ id: number | string; title: string } | null>(null);

function triggerDelete(row: Row<GalleryMedia>) {
    const id = row.original.id;
    const title = row.original.title_id || row.original.title_en || "Media ini";

    if (id !== undefined && id !== null) {
        selectedMedia.value = {
            id: id,
            title: title,
        };
        isDeleteModalOpen.value = true;
    }
}

function handleDeleteSuccess() {
    isDeleteModalOpen.value = false;
    selectedMedia.value = null;
    toast.add({
        title: "Berhasil!",
        description: "Media telah berhasil dihapus dari sistem.",
        color: "success",
        icon: "i-lucide-circle-check",
    });
    refresh();
}

function handleDeleteError(errorMessage: string) {
    toast.add({
        title: "Gagal menghapus media",
        description:
            errorMessage || "Terjadi masalah saat memproses permintaan Anda.",
        color: "error", // Gunakan 'error' atau 'red' sesuai konfigurasi tema Nuxt UI Anda
        icon: "i-lucide-circle-alert",
    });
}

const columns: TableColumn<GalleryMedia>[] = [
    {
        id: "no",
        header: "No",
        meta: {
            class: { th: "w-12 text-center", td: "text-center text-gray-500" },
        },
        cell: ({ row }) => {
            const itemsPerPage = 10;
            const number = (page.value - 1) * itemsPerPage + row.index + 1;
            return h("span", {}, number);
        },
    },
    {
        id: "title",
        header: "Judul Media",
        meta: {
            class: { th: "min-w-[250px]" },
        },
        cell: ({ row }) => {
            const coverImage = coverOf(row.original);

            return h("div", { class: "flex items-center gap-3 py-1" }, [
                h(UAvatar, {
                    src: coverImage,
                    icon: "i-lucide-image",
                    size: "md",
                    alt: row.original.title_id || "Media Image",
                    class: "rounded-md bg-gray-100 dark:bg-gray-800 shrink-0 object-cover",
                }),
                h("div", { class: "flex flex-col" }, [
                    h(
                        "span",
                        {
                            class: "font-medium text-gray-900 dark:text-white line-clamp-1",
                        },
                        row.original.title_id ||
                            row.original.title_en ||
                            "Tanpa Judul",
                    ),
                    h(
                        "span",
                        { class: "text-xs text-gray-500 line-clamp-1 mt-0.5" },
                        row.original.title_en || "No English Title",
                    ),
                ]),
            ]);
        },
    },
    {
        accessorKey: "category_id",
        header: "Kategori",
        meta: {
            class: { th: "w-32" },
        },
        cell: ({ row }) => {
            return h(
                UBadge,
                {
                    color: "primary",
                    variant: "subtle",
                    class: "capitalize font-medium",
                },
                () => row.original.category_id || "Uncategorized",
            );
        },
    },
    {
        id: "description",
        header: "Deskripsi",
        meta: {
            class: {
                th: "min-w-[250px]",
                td: "whitespace-normal break-words max-w-[400px]",
            },
        },
        cell: ({ row }) => {
            return h(
                "div",
                {
                    class: "text-gray-600 dark:text-gray-400 text-sm line-clamp-2",
                },
                row.original.description_id ||
                    row.original.description_en ||
                    "-",
            );
        },
    },
    {
        id: "actions",
        header: "",
        meta: {
            class: { th: "w-16", td: "text-right" },
        },
        cell: ({ row }) => {
            return h(
                UDropdownMenu,
                {
                    content: { align: "end" },
                    items: getRowItems(row),
                    "aria-label": "Actions dropdown",
                },
                () =>
                    h(UButton, {
                        icon: "i-lucide-more-vertical",
                        color: "neutral",
                        variant: "ghost",
                        size: "sm",
                        class: "text-gray-400 hover:text-gray-900 dark:hover:text-white",
                        "aria-label": "Actions dropdown",
                    }),
            );
        },
    },
];

function getRowItems(row: Row<GalleryMedia>) {
    return [
        [
            {
                label: "Lihat Detail",
                icon: "i-lucide-eye",
                onSelect() {
                    const id = row.original.id;
                    if (id) navigateTo(`/admin/media/${id}`);
                },
            },
        ],
        [
            {
                label: "Copy ID Media",
                icon: "i-lucide-copy",
                onSelect() {
                    const id = row.original.id ? String(row.original.id) : "";
                    if (id) {
                        copy(id);
                        toast.add({
                            title: "ID berhasil disalin!",
                            description: `ID: ${id}`,
                            color: "success",
                            icon: "i-lucide-circle-check",
                        });
                    }
                },
            },
        ],
        [
            {
                label: "Hapus Media",
                icon: "i-lucide-trash-2",
                color: "error" as const,
                onSelect() {
                    triggerDelete(row); // Menyambungkan aksi klik ke pemicu modal
                },
            },
        ],
    ];
}
</script>
