<template>
    <div class="space-y-6 flex flex-col h-full mx-auto w-full">
        <AdminHeader
            title="Pengaturan"
            icon="i-lucide-settings"
            description="Kelola pengaturan aplikasi. Ubah, tambah, atau hapus pengaturan dari sini."
            :showCreate="true"
            createRoute="/admin/settings/create"
        >
            <template #actions>
                <div class="flex items-center gap-3">
                    <UInput
                        v-model="searchInput"
                        icon="i-lucide-search"
                        placeholder="Cari nama pengaturan..."
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
            <div class="overflow-x-auto">
                <UTable
                    :data="settings"
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
                jadwal
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
                Tidak ada pengaturan
            </h3>
            <p class="text-gray-500 mt-1 max-w-sm">
                Coba ubah kata kunci pencarian atau tambahkan pengaturan baru.
            </p>
            <div class="mt-4 flex gap-3">
                <UButton color="neutral" variant="soft" @click="clearSearch"
                    >Bersihkan Filter</UButton
                >
                <UButton
                    color="primary"
                    icon="i-lucide-plus"
                    to="/admin/settings/create"
                    >Tambah Pengaturan
                </UButton>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { h, resolveComponent } from "vue";
import type { TableColumn } from "@nuxt/ui";
import type { Row } from "@tanstack/vue-table";
import { useClipboard } from "@vueuse/core";
import type { Setting } from "~/composables/useSettings";

definePageMeta({
    layout: "admin",
});

const UButton = resolveComponent("UButton");
const UDropdownMenu = resolveComponent("UDropdownMenu");

const toast = useToast();
const { copy } = useClipboard();

const {
    schedules,
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
} = useSchedules();

const columns: TableColumn<Schedule>[] = [
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
        id: "key",
        header: "Nama Pengaturan",
    },
    {
        id: "value",
        header: "Nilai",
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

function getRowItems(row: Row<Setting>) {
    return [
        [
            {
                label: "Copy ID Jadwal",
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
                label: "Hapus Pengaturan",
                icon: "i-lucide-trash-2",
                color: "error" as const,
            },
        ],
    ];
}
</script>
