<template>
    <div class="space-y-6 flex flex-col h-full mx-auto w-full">
        <AdminHeader
            title="Pendaftaran"
            icon="i-lucide-clipboard-list"
            description="Kelola pendaftaran program. Cari, setujui, atau hapus pendaftaran dari sini."
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

        <!-- Detail Modal -->
        <UModal v-model:open="isDetailOpen" title="Detail Pendaftaran">
            <template #body>
                <div v-if="selectedRegistration" class="space-y-5 text-sm">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3">
                        <div>
                            <p class="text-gray-500">Nama Lengkap</p>
                            <p class="font-medium">
                                {{ selectedRegistration.full_name }}
                            </p>
                        </div>
                        <div>
                            <p class="text-gray-500">Status</p>
                            <UBadge
                                :color="statusColor(selectedRegistration.status)"
                                variant="subtle"
                                class="capitalize"
                                >{{ selectedRegistration.status || "pending" }}</UBadge
                            >
                        </div>
                        <div>
                            <p class="text-gray-500">Email</p>
                            <p class="font-medium break-all">
                                {{ selectedRegistration.email }}
                            </p>
                        </div>
                        <div>
                            <p class="text-gray-500">Telepon</p>
                            <p class="font-medium">
                                {{ selectedRegistration.phone }}
                            </p>
                        </div>
                        <div>
                            <p class="text-gray-500">Jenis Kelamin</p>
                            <p class="font-medium capitalize">
                                {{ selectedRegistration.gender || "-" }}
                            </p>
                        </div>
                        <div>
                            <p class="text-gray-500">Umur</p>
                            <p class="font-medium">
                                {{ selectedRegistration.age || "-" }}
                            </p>
                        </div>
                        <div class="sm:col-span-2">
                            <p class="text-gray-500">Program</p>
                            <p class="font-medium">
                                {{
                                    selectedRegistration.program?.title_id ||
                                    selectedRegistration.program?.title_en ||
                                    "-"
                                }}
                            </p>
                        </div>
                        <div class="sm:col-span-2">
                            <p class="text-gray-500">Alamat</p>
                            <p class="font-medium">
                                {{ selectedRegistration.address || "-" }}
                            </p>
                        </div>
                        <div class="sm:col-span-2">
                            <p class="text-gray-500">Catatan</p>
                            <p class="font-medium">
                                {{ selectedRegistration.notes || "-" }}
                            </p>
                        </div>
                    </div>

                    <!-- Dokumen -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-gray-500 mb-1.5">Foto KTP/Identitas</p>
                            <a
                                v-if="selectedRegistration.id_card"
                                :href="fileUrl(selectedRegistration.id_card)"
                                target="_blank"
                            >
                                <img
                                    :src="fileUrl(selectedRegistration.id_card)"
                                    class="w-full h-32 object-cover rounded-lg ring-1 ring-gray-200 dark:ring-gray-800"
                                />
                            </a>
                            <p v-else class="text-gray-400">-</p>
                        </div>
                        <div>
                            <p class="text-gray-500 mb-1.5">Pas Foto</p>
                            <a
                                v-if="selectedRegistration.photo"
                                :href="fileUrl(selectedRegistration.photo)"
                                target="_blank"
                            >
                                <img
                                    :src="fileUrl(selectedRegistration.photo)"
                                    class="w-full h-32 object-cover rounded-lg ring-1 ring-gray-200 dark:ring-gray-800"
                                />
                            </a>
                            <p v-else class="text-gray-400">-</p>
                        </div>
                    </div>

                    <!-- Pembayaran -->
                    <div
                        v-if="selectedRegistration.transactions?.length"
                        class="border-t border-gray-200 dark:border-gray-800 pt-4 space-y-1"
                    >
                        <p class="font-semibold text-gray-900 dark:text-white">
                            Pembayaran
                        </p>
                        <div class="flex justify-between">
                            <span class="text-gray-500">No. Invoice</span>
                            <span class="font-mono">{{
                                selectedRegistration.transactions[0].reference_id
                            }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Status Pembayaran</span>
                            <span class="capitalize">{{
                                selectedRegistration.transactions[0]
                                    .payment_status
                            }}</span>
                        </div>
                        <div
                            v-if="
                                selectedRegistration.transactions[0]
                                    .transaction_receipt
                            "
                            class="pt-1"
                        >
                            <a
                                :href="
                                    fileUrl(
                                        selectedRegistration.transactions[0]
                                            .transaction_receipt,
                                    )
                                "
                                target="_blank"
                                class="text-primary-600 dark:text-primary-400 hover:underline"
                                >Lihat Bukti Transfer</a
                            >
                        </div>
                    </div>
                </div>
            </template>
        </UModal>
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

const fileUrl = useFileUrl();

const isDetailOpen = ref(false);
const selectedRegistration = ref<any>(null);

function viewDetail(row: Row<RegistrationRow>) {
    selectedRegistration.value = row.original;
    isDetailOpen.value = true;
}

const statusColor = (status?: string) =>
    ({ pending: "warning", approved: "success", rejected: "error" })[
        status || "pending"
    ] || "neutral";

const toast = useToast();
const client = useSanctumClient();

export interface RegistrationRow {
    id?: number | string;
    full_name: string;
    email: string;
    phone: string;
    gender?: string;
    age?: number | null;
    address?: string | null;
    notes?: string | null;
    status?: string;
    id_card?: string | null;
    photo?: string | null;
    created_at?: string;
    program?: any;
    transactions?: any[];
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
            {
                label: "Lihat",
                icon: "i-lucide-eye",
                onSelect: () => viewDetail(row),
            },
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
                            description: e?.data?.message || e?.message || "Error",
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
                            description: e?.data?.message || e?.message || "Error",
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
