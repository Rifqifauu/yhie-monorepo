<template>
    <div class="space-y-6 flex flex-col h-full mx-auto w-full">
        <AdminHeader
            title="Artikel"
            icon="i-lucide-file-text"
            description="Kelola artikel Anda di sini. Anda dapat membuat, mengedit, dan menghapus artikel melalui halaman ini."
            :showCreate="true"
            createRoute="/admin/articles/create"
        >
            <template #actions>
                <div class="flex items-center gap-3">
                    <UInput
                        v-model="searchInput"
                        icon="i-lucide-search"
                        placeholder="Cari judul atau penulis..."
                        size="lg"
                        class="min-w-0 sm:w-72"
                        @keyup.enter="applySearch"
                    />

                    <UButton
                        color="neutral"
                        variant="soft"
                        icon="i-lucide-rotate-cw"
                        @click="refresh"
                    >
                        Muat ulang
                    </UButton>
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
                        :data="articles"
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
            class="flex flex-col items-center justify-center py-12 text-center ring-1 ring-gray-200 dark:ring-gray-800 rounded-lg bg-white dark:bg-gray-900"
        >
            <UIcon
                name="i-lucide-file-search"
                class="w-12 h-12 text-gray-400 mb-4"
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
                @click="clearSearch"
            />
        </div>
        <AdminDeleteModal
            v-model:open="isDeleteModalOpen"
            :id="selectedArticle?.id"
            :title="selectedArticle?.title"
            endpoint="api/articles"
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

definePageMeta({
    layout: "admin",
});

const UButton = resolveComponent("UButton");
const UDropdownMenu = resolveComponent("UDropdownMenu");
const UBadge = resolveComponent("UBadge");
const UAvatar = resolveComponent("UAvatar");

const toast = useToast();
const { copy } = useClipboard();

const {
    articles,
    pending,
    page,
    totalItems,
    fromItem,
    toItem,
    changePage,
    searchInput,
    applySearch,
    clearSearch,
    refresh,
    coverOf,
    titleOf,
} = useArticles();

const isDeleteModalOpen = ref(false);
const selectedArticle = ref<{ id: number | string; title: string } | null>(
    null,
);

function triggerDelete(row: Row<Article>) {
    const id = row.original.id;
    // Menggunakan titleOf untuk mendapatkan title fallback yang sesuai bahasa
    const title = titleOf(row.original) || "Artikel ini";

    if (id !== undefined && id !== null) {
        selectedArticle.value = {
            id: id,
            title: title,
        };
        isDeleteModalOpen.value = true;
    }
}

const handleDeleteSuccess = () => {
    toast.add({
        title: "Success",
        description: "Article deleted successfully",
        color: "success",
    });
    refresh();
};

const handleDeleteError = () => {
    toast.add({
        title: "Error",
        description: "Failed to delete article",
        color: "error",
    });
};

// ----------------------------------------------------
// Konfigurasi Kolom Tabel (Menggunakan Computed)
// ----------------------------------------------------
const columns = computed<TableColumn<Article>[]>(() => [
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
        accessorKey: "title",
        header: "Judul Artikel",
        meta: {
            class: { th: "min-w-[300px]" },
        },
        cell: ({ row }) => {
            // 💡 PERBAIKAN: Kirim seluruh objek row.original (Article)
            // agar fungsi coverOf bisa mengekstrak properti .image secara internal
            const thumbnailSrc = coverOf(row.original) || undefined;

            return h("div", { class: "flex items-center gap-3 py-1" }, [
                h(UAvatar, {
                    src: thumbnailSrc,
                    icon: "i-lucide-image",
                    size: "md",
                    alt: row.original.title_en || "Thumbnail",
                    class: "rounded-md bg-gray-100 dark:bg-gray-800 object-cover",
                }),
                h("div", { class: "flex flex-col" }, [
                    h(
                        "span",
                        {
                            class: "font-medium text-gray-900 dark:text-white line-clamp-1",
                        },
                        titleOf(row.original) || "Tanpa Judul",
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
        accessorKey: "category",
        header: "Kategori",
        cell: ({ row }) => {
            return h(
                UBadge,
                {
                    color: "primary",
                    variant: "subtle",
                    class: "capitalize font-medium",
                },
                () => row.original.category || "Uncategorized",
            );
        },
    },
    {
        accessorKey: "status",
        header: "Status",
        cell: ({ row }) => {
            const status = row.original.is_published ? "published" : "draft";
            const colorMap: Record<string, string> = {
                published: "success",
                draft: "error",
                archived: "neutral",
            };

            return h(
                UBadge,
                {
                    color: colorMap[status] || "neutral",
                    variant: "subtle",
                    class: "capitalize",
                    size: "sm",
                },
                () => status,
            );
        },
    },
    {
        accessorKey: "created_at",
        header: "Tanggal Dibuat",
        meta: {
            class: { th: "w-32" },
        },
        cell: ({ row }) => {
            const dateStr = row.original.created_at;
            const formatted = dateStr
                ? new Date(dateStr).toLocaleDateString("id-ID", {
                      day: "numeric",
                      month: "short",
                      year: "numeric",
                  })
                : "-";
            return h(
                "span",
                { class: "text-sm text-gray-500 whitespace-nowrap" },
                formatted,
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
]);

function getRowItems(row: Row<Article>) {
    return [
        [
            {
                label: "Lihat Detail",
                icon: "i-lucide-eye",
                onSelect() {
                    const slug = row.original.slug_id;
                    if (slug) navigateTo(`/admin/articles/${slug}`);
                },
            },
        ],
        [
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
        ],
        [
            {
                label: "Hapus Artikel",
                icon: "i-lucide-trash-2",
                color: "error",
                onSelect() {
                    triggerDelete(row);
                },
            },
        ],
    ];
}
</script>
