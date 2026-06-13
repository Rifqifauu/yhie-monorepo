<template>
    <div class="space-y-6 flex flex-col h-full mx-auto w-full">
        <AdminHeader
            title="Program"
            icon="i-lucide-briefcase"
            description="Kelola program pelatihan dan kursus. Tambah, edit, atau hapus program dari sini."
            :showCreate="true"
            createRoute="/admin/programs/create"
        >
            <template #actions>
                <div class="flex items-center gap-3">
                    <UInput
                        v-model="searchInput"
                        icon="i-lucide-search"
                        placeholder="Cari nama program..."
                        size="lg"
                        class="min-w-0 sm:w-72"
                        @keyup.enter="applySearch"
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
            class="flex-1 bg-white/50 dark:bg-gray-900 ring-1 ring-gray-200 dark:ring-gray-800 rounded-lg shadow-sm overflow-hidden flex flex-col"
        >
            <div class="overflow-x-auto">
                <UTable
                    :data="programs"
                    :columns="columns"
                    :loading="pending"
                    class="w-full"
                />
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
                program
            </span>

            <UPagination
                v-model:page="page"
                :total="totalItems"
                :items-per-page="10"
                @update:model-value="changePage"
            />
        </div>

        <div
            v-if="!pending && totalItems === 0"
            class="flex flex-col items-center justify-center py-12 text-center ring-1 ring-gray-200 dark:ring-gray-800 rounded-lg bg-white dark:bg-gray-900"
        >
            <UIcon
                name="i-lucide-file-search"
                class="w-12 h-12 text-gray-400 mb-4"
            />
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                Tidak ada program
            </h3>
            <p class="text-gray-500 mt-1 max-w-sm">
                Coba ubah kata kunci pencarian atau tambahkan program baru.
            </p>
            <div class="mt-4 flex gap-3">
                <UButton color="neutral" variant="soft" @click="clearSearch"
                    >Bersihkan Filter</UButton
                >
                <UButton
                    color="primary"
                    icon="i-lucide-plus"
                    to="/admin/programs/create"
                    >Tambah program</UButton
                >
            </div>
        </div>

        <AdminDeleteModal
            v-model:open="isDeleteModalOpen"
            :id="selectedProgram?.id"
            :title="selectedProgram?.title"
            endpoint="api/programs"
            @success="handleDeleteSuccess"
            @error="handleDeleteError"
        />
    </div>
</template>

<script setup lang="ts">
import { h, resolveComponent, ref } from "vue";
import type { TableColumn } from "@nuxt/ui";
import type { Row } from "@tanstack/vue-table";
import { useClipboard } from "@vueuse/core";

definePageMeta({
    layout: "admin",
});

const UButton = resolveComponent("UButton");
const UDropdownMenu = resolveComponent("UDropdownMenu");
const UAvatar = resolveComponent("UAvatar");

const toast = useToast();
const { copy } = useClipboard();

const {
    programs,
    pending,
    page,
    totalItems,
    fromItem,
    toItem,
    searchInput,
    applySearch,
    clearSearch,
    changePage,
    refresh,
    titleOf,
    descOf,
    imageUrl,
    formatPrice,
} = usePrograms();

export interface Program {
    id?: number | string;
    title_en: string;
    title_id: string;
    description_en?: string;
    description_id?: string;
    price_id?: number | string;
    image?: string;
    [key: string]: any;
}

const isDeleteModalOpen = ref(false);
const selectedProgram = ref<{ id: number | string; title: string } | null>(
    null,
);

function triggerDelete(row: Row<Program>) {
    const id = row.original.id;
    const title = titleOf(row.original) || "Program ini";

    if (id !== undefined && id !== null) {
        selectedProgram.value = {
            id: id,
            title: title,
        };
        isDeleteModalOpen.value = true;
    }
}

function handleDeleteSuccess() {
    toast.add({
        title: "Berhasil!",
        description: "Program telah berhasil dihapus dari sistem.",
        duration: 5000,
    });
    refresh();
}

function handleDeleteError(errorMessage: string) {
    toast.add({
        title: "Gagal menghapus program",
        description: errorMessage || "There was a problem with your request.",
        duration: 5000,
    });
}

const columns: TableColumn<Program>[] = [
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
        id: "name",
        header: "Nama Program",
        meta: { class: { th: "min-w-[200px]" } },
        cell: ({ row }) => {
            return h("div", { class: "flex items-center gap-3 py-1" }, [
                h(UAvatar, {
                    src: imageUrl(row.original.image_path || ""),
                    icon: "i-lucide-image",
                    size: "md",
                    alt: titleOf(row.original),
                    class: "rounded-md bg-gray-100 dark:bg-gray-800 shrink-0",
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
                        row.original.title_en || "",
                    ),
                ]),
            ]);
        },
    },
    {
        id: "price",
        header: "Harga",
        meta: { class: { th: "w-36 text-right", td: "text-right" } },
        cell: ({ row }) =>
            h(
                "div",
                { class: "text-sm text-gray-700 dark:text-gray-300" },
                formatPrice(row.original.price_id),
            ),
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
        cell: ({ row }) =>
            h(
                "div",
                { class: "text-gray-600 dark:text-gray-400 text-sm" },
                descOf(row.original) || "-",
            ),
    },
    {
        id: "actions",
        header: "",
        meta: { class: { th: "w-16", td: "text-right" } },
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

function getRowItems(row: Row<Program>) {
    return [
        [
            {
                label: "Lihat Detail",
                icon: "i-lucide-eye",
                onSelect() {
                    const slug = row.original.slug_id;
                    if (slug) {
                        navigateTo(`/admin/programs/${slug}`);
                    }
                },
            },
        ],
        [
            {
                label: "Copy ID Program",
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
                label: "Hapus Program",
                icon: "i-lucide-trash-2",
                color: "error" as const,
                onSelect() {
                    triggerDelete(row);
                },
            },
        ],
    ];
}
</script>
