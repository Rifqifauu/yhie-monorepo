<template>
    <div class="space-y-6 flex flex-col h-full mx-auto w-full">
        <AdminHeader
            title="Pendaftaran"
            icon="i-lucide-clipboard-list"
            description="Kelola pendaftaran program. Cari, setujui, atau hapus pendaftaran dari sini."
            showCreate="false"
        >
            <template #actions>
                <div class="flex items-center gap-3">
                    <UInput
                        v-model="searchInput"
                        icon="i-lucide-search"
                        placeholder="Cari nama, email, atau telepon..."
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
                    >
                        Reset
                    </UButton>
                </div>
            </template>
        </AdminHeader>

        <div
            class="flex-1 bg-white dark:bg-gray-900 ring-1 ring-gray-200 dark:ring-gray-800 rounded-lg shadow-sm overflow-hidden flex flex-col"
        >
            <div class="overflow-x-auto scrollbar-hide">
                <div class="min-w-max">
                    <UTable
                        :data="registrations"
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
                pendaftaran
            </span>

            <UPagination
                v-model:page="page"
                :total="totalItems"
                :items-per-page="15"
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
                Belum ada pendaftaran
            </h3>
            <p class="text-gray-500 mt-1 max-w-sm">
                Coba ubah kata kunci pencarian atau tunggu pendaftaran baru.
            </p>
            <div class="mt-4 flex gap-3">
                <UButton color="neutral" variant="soft" @click="clearSearch"
                    >Bersihkan Filter</UButton
                >
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { h, resolveComponent } from "vue";
import type { TableColumn } from "@nuxt/ui";
import type { Row } from "@tanstack/vue-table";

definePageMeta({ layout: "admin" });

const UButton = resolveComponent("UButton");
const UDropdownMenu = resolveComponent("UDropdownMenu");
const UBadge = resolveComponent("UBadge");

const {
    page,
    searchInput,
    registrations,
    totalItems,
    fromItem,
    toItem,
    pending,
    applySearch,
    clearSearch,
    changePage,
    refresh,
} = useProgramRegistrations();

const toast = useToast();
const client = useSanctumClient();

export interface RegistrationRow {
    id?: number | string;
    full_name: string;
    email: string;
    phone: string;
    status?: string;
    created_at?: string;
    program?: any;
}

const columns: TableColumn<RegistrationRow>[] = [
    {
        id: "no",
        header: "No",
        meta: {
            class: { th: "w-12 text-center", td: "text-center text-gray-500" },
        },
        cell: ({ row }) => {
            const itemsPerPage = 15;
            const number = (page.value - 1) * itemsPerPage + row.index + 1;
            return h("span", {}, number);
        },
    },
    {
        id: "name",
        header: "Nama",
        meta: { class: { th: "min-w-[220px]" } },
        cell: ({ row }) => {
            return h("div", { class: "flex flex-col" }, [
                h(
                    "span",
                    {
                        class: "font-medium text-gray-900 dark:text-white line-clamp-1",
                    },
                    row.original.full_name,
                ),
                h(
                    "span",
                    { class: "text-xs text-gray-500 line-clamp-1" },
                    row.original.email || "",
                ),
            ]);
        },
    },
    {
        accessorKey: "phone",
        header: "Telepon",
        meta: { class: { th: "w-36" } },
        cell: ({ row }) =>
            h(
                "span",
                { class: "text-sm text-gray-700" },
                row.original.phone || "-",
            ),
    },
    {
        id: "program",
        header: "Program",
        meta: { class: { th: "min-w-[220px]" } },
        cell: ({ row }) =>
            h(
                "span",
                { class: "text-sm text-gray-700 line-clamp-1" },
                row.original.program?.title_id ||
                    row.original.program?.title_en ||
                    "-",
            ),
    },
    {
        id: "status",
        header: "Status",
        meta: { class: { th: "w-32" } },
        cell: ({ row }) => {
            const status = row.original.status || "pending";
            const colorMap: Record<string, string> = {
                pending: "warning",
                approved: "success",
                rejected: "error",
            };
            return h(
                UBadge,
                {
                    color: colorMap[status] || "neutral",
                    variant: "subtle",
                    class: "capitalize",
                },
                () => status,
            );
        },
    },
    {
        accessorKey: "created_at",
        header: "Tanggal",
        meta: { class: { th: "w-40" } },
        cell: ({ row }) => {
            const dateStr = row.original.created_at;
            const formatted = dateStr
                ? new Date(dateStr).toLocaleString()
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
        meta: { class: { th: "w-24", td: "text-right" } },
        cell: ({ row }) => {
            return h(
                UDropdownMenu,
                { content: { align: "end" }, items: getRowItems(row) },
                () =>
                    h(UButton, {
                        icon: "i-lucide-more-vertical",
                        color: "neutral",
                        variant: "ghost",
                        size: "sm",
                    }),
            );
        },
    },
];

function getRowItems(row: Row<RegistrationRow>) {
    return [
        [
            { label: "Lihat", icon: "i-lucide-eye" },
            {
                label: "Setujui",
                icon: "i-lucide-check",
                onSelect: async () => {
                    try {
                        await client(
                            `/api/program-registrations/${row.original.id}`,
                            { method: "PUT", body: { status: "approved" } },
                        );
                        toast.add({
                            title: "Berhasil",
                            description: "Pendaftaran disetujui",
                            color: "success",
                            icon: "i-lucide-circle-check",
                        });
                        refresh();
                    } catch (e: any) {
                        toast.add({
                            title: "Gagal",
                            description: e?.message || "Error",
                            color: "red",
                            icon: "i-lucide-triangle-alert",
                        });
                    }
                },
            },
            {
                label: "Tolak",
                icon: "i-lucide-x",
                onSelect: async () => {
                    try {
                        await client(
                            `/api/program-registrations/${row.original.id}`,
                            { method: "PUT", body: { status: "rejected" } },
                        );
                        toast.add({
                            title: "Berhasil",
                            description: "Pendaftaran ditolak",
                            color: "success",
                            icon: "i-lucide-circle-check",
                        });
                        refresh();
                    } catch (e: any) {
                        toast.add({
                            title: "Gagal",
                            description: e?.message || "Error",
                            color: "red",
                            icon: "i-lucide-triangle-alert",
                        });
                    }
                },
            },
        ],
        [
            {
                label: "Copy ID",
                icon: "i-lucide-copy",
                onSelect: () => {
                    const id = row.original.id ? String(row.original.id) : "";
                    if (id) {
                        useClipboard().copy(id);
                        toast.add({
                            title: "ID disalin",
                            description: `ID: ${id}`,
                            color: "success",
                        });
                    }
                },
            },
        ],
        [
            {
                label: "Hapus",
                icon: "i-lucide-trash-2",
                color: "error",
                onSelect: async () => {
                    if (!confirm("Hapus pendaftaran ini?")) return;
                    try {
                        await client(
                            `/api/program-registrations/${row.original.id}`,
                            { method: "DELETE" },
                        );
                        toast.add({
                            title: "Terhapus",
                            description: "Pendaftaran dihapus",
                            color: "success",
                        });
                        refresh();
                    } catch (e: any) {
                        toast.add({
                            title: "Gagal",
                            description: e?.message || "Error",
                            color: "red",
                            icon: "i-lucide-triangle-alert",
                        });
                    }
                },
            },
        ],
    ];
}
</script>
