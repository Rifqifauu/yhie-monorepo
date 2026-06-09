<template>
    <div class="space-y-6 flex flex-col h-full mx-auto w-full">
        <AdminHeader
            title="Partner"
            icon="i-lucide-stars"
            description="Kelola partner Anda di sini. Anda dapat membuat, mengedit, dan menghapus partner melalui halaman ini."
            :showCreate="true"
            createRoute="/admin/partners/create"
        >
            <template #actions>
                <div class="flex items-center gap-3">
                    <UInput
                        v-model="searchInput"
                        icon="i-lucide-search"
                        placeholder="Cari nama partner..."
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
            class="flex-1 bg-white dark:bg-gray-900 ring-1 ring-gray-200 dark:ring-gray-800 rounded-lg shadow-sm overflow-hidden flex flex-col"
        >
            <div class="overflow-x-auto scrollbar-hide">
                <div class="min-w-max">
                    <UTable
                        :data="partners"
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
                partner
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
                Tidak ada partner
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
            :id="selectedPartner?.id"
            :title="selectedPartner?.title"
            endpoint="api/partners"
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
    partners,
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
} = usePartners();

export interface Partner {
    id?: number | string;
    name_id: string;
    name_en: string;
    description_id: string;
    description_en: string;
    slug_id: string;
    slug_en: string;
    logo: string;
}

const isDeleteModalOpen = ref(false);
const selectedPartner = ref<{ id: number | string; title: string } | null>(
    null,
);

// Memicu modal delete terbuka dan mengikat data terpilih
function triggerDelete(row: Row<Partner>) {
    const id = row.original.id;
    const title = row.original.name_id || row.original.name_en || "Partner ini";

    if (id !== undefined && id !== null) {
        selectedPartner.value = {
            id,
            title,
        };
        isDeleteModalOpen.value = true;
    }
}

// Handler sukses saat item terhapus
function handleDeleteSuccess() {
    isDeleteModalOpen.value = false;
    selectedPartner.value = null;
    toast.add({
        title: "Berhasil!",
        description: "Partner telah berhasil dihapus dari sistem.",
        color: "success",
        icon: "i-lucide-circle-check",
    });
    refresh(); // Muat ulang data tabel
}

// Handler error menerima lemparan string pesan dari modal
function handleDeleteError(errorMessage: string) {
    toast.add({
        title: "Gagal menghapus partner!",
        description: errorMessage || "Terjadi kesalahan pada sistem.",
        color: "error", // Sesuaikan dengan konfigurasi warna Nuxt UI Anda ('error' atau 'red')
        icon: "i-lucide-circle-alert",
    });
}

const columns: TableColumn<Partner>[] = [
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
        header: "Nama Partner",
        meta: {
            class: { th: "min-w-[250px]" },
        },
        cell: ({ row }) => {
            return h("div", { class: "flex items-center gap-3 py-1" }, [
                h(UAvatar, {
                    src: row.original.logo || "",
                    icon: "i-lucide-image",
                    size: "md",
                    alt: row.original.name_id || "Logo",
                    class: "rounded-md bg-gray-100 dark:bg-gray-800 shrink-0",
                }),
                h("div", { class: "flex flex-col" }, [
                    h(
                        "span",
                        {
                            class: "font-medium text-gray-900 dark:text-white line-clamp-1",
                        },
                        row.original.name_id ||
                            row.original.name_en ||
                            "Tanpa Nama",
                    ),
                    h(
                        "span",
                        { class: "text-xs text-gray-500 line-clamp-1 mt-0.5" },
                        row.original.name_en || "No English Name",
                    ),
                ]),
            ]);
        },
    },
    {
        id: "description",
        header: "Deskripsi",
        meta: {
            class: {
                th: "min-w-[300px]",
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

function getRowItems(row: Row<Partner>) {
    return [
        [
            {
                label: "Lihat Detail",
                icon: "i-lucide-eye",
                onSelect() {
                    const id = row.original.id;
                    if (id) {
                        navigateTo(`/admin/partners/${id}`); // Sesuaikan jika ada sub-route detail
                    }
                },
            },
        ],
        [
            {
                label: "Copy ID Partner",
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
                label: "Hapus Partner",
                icon: "i-lucide-trash-2",
                color: "error" as const,
                onSelect() {
                    triggerDelete(row); // Menghubungkan dropdown hapus ke pemicu modal
                },
            },
        ],
    ];
}
</script>
