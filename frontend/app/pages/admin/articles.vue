<template>
    <div class="space-y-6 flex flex-col h-full max-w-[1400px] mx-auto w-full">
        <div
            class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 pb-6 mb-6 border-b border-emerald-900"
        >
            <div class="flex items-center gap-3">
                <div
                    class="flex items-center justify-center w-10 h-10 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 shadow-sm"
                >
                    <UIcon name="i-lucide-book" class="w-5 h-5" />
                </div>

                <h1
                    class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight"
                >
                    Manajemen Artikel
                </h1>
            </div>

            <UButton
                icon="i-lucide-plus"
                label="Buat Artikel Baru"
                size="md"
                class="shadow-sm bg-emerald-800"
                to="/admin/articles/create"
            />
        </div>

        <div
            class="flex-1 bg-white dark:bg-gray-900 shadow-sm overflow-hidden flex flex-col"
        >
            <UTable
                :data="articles"
                :columns="columns"
                :loading="pending"
                class="flex-1"
            />
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
                artikel
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
            class="flex flex-col items-center justify-center py-12 text-center"
        >
            <UIcon
                name="i-lucide-file-search"
                class="w-12 h-12 text-gray-300 mb-4"
            />
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                Tidak ada artikel
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
    </div>
</template>

<script setup lang="ts">
import { h, resolveComponent, ref, watch } from "vue";
import type { TableColumn } from "@nuxt/ui";
import type { Row } from "@tanstack/vue-table";
import { useClipboard, watchDebounced } from "@vueuse/core";

// import type { Article } from "~/composables/useArticles";

definePageMeta({
    layout: "admin",
});

const UButton = resolveComponent("UButton");
const UDropdownMenu = resolveComponent("UDropdownMenu");
const UBadge = resolveComponent("UBadge"); // Tambahan untuk mempercantik kolom
const toast = useToast();
const { copy } = useClipboard();

const {
    articles,
    pending,
    page,
    totalPages,
    totalItems,
    fromItem,
    toItem,
    pageItems,
    searchInput,
    applySearch,
    clearSearch,
    changePage,
} = useArticles();

// ----------------------------------------------------
// Konfigurasi Kolom Tabel
// ----------------------------------------------------
const columns: TableColumn<Article>[] = [
    {
        accessorKey: "title_id",
        header: "Judul (ID)",
        // Membuat text lebih tebal untuk judul utama
        cell: ({ row }) =>
            h(
                "span",
                { class: "font-medium text-gray-900 dark:text-gray-100" },
                row.original.title_id,
            ),
    },
    {
        accessorKey: "title_en",
        header: "Judul (EN)",
        cell: ({ row }) =>
            h("span", { class: "text-gray-500" }, row.original.title_en || "-"),
    },
    {
        accessorKey: "category",
        header: "Kategori",
        cell: ({ row }) => {
            return h(
                UBadge,
                {
                    color: "secondary",
                    variant: "solid",
                    class: "capitalize font-medium",
                },
                () => row.original.category || "Uncategorized",
            );
        },
    },
    {
        id: "actions",
        header: "",
        meta: {
            class: {
                th: "w-16",
                td: "text-right",
            },
        },
        cell: ({ row }) => {
            return h(
                UDropdownMenu,
                {
                    content: {
                        align: "end",
                    },
                    items: getRowItems(row),
                    "aria-label": "Actions dropdown",
                },
                () =>
                    h(UButton, {
                        icon: "i-lucide-ellipsis-vertical",
                        color: "neutral",
                        variant: "ghost",
                        size: "sm",
                        "aria-label": "Actions dropdown",
                    }),
            );
        },
    },
];

function getRowItems(row: Row<Article>) {
    return [
        {
            type: "label",
            label: "Aksi",
        },
        {
            label: "Copy ID Artikel",
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
        {
            type: "separator",
        },
        {
            label: "Lihat Detail",
            icon: "i-lucide-eye",
        },
        {
            label: "Edit Artikel",
            icon: "i-lucide-edit",
        },
    ];
}
</script>
