<template>
    <div class="space-y-6 flex flex-col h-full mx-auto w-full">
        <AdminHeader
            title="Schedule"
            icon="i-lucide-calendar"
            description="Kelola jadwal pelatihan dan kursus. Tambah, edit, atau hapus jadwal dari sini."
            :showCreate="true"
            createRoute="/admin/schedules/create"
        >
            <template #actions>
                <div class="flex items-center gap-3">
                    <UInput
                        v-model="searchInput"
                        icon="i-lucide-search"
                        placeholder="Cari nama jadwal..."
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
                    :data="schedules"
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
                Tidak ada jadwal
            </h3>
            <p class="text-gray-500 mt-1 max-w-sm">
                Coba ubah kata kunci pencarian atau tambahkan jadwal baru.
            </p>
            <div class="mt-4 flex gap-3">
                <UButton color="neutral" variant="soft" @click="clearSearch"
                    >Bersihkan Filter</UButton
                >
                <UButton
                    color="primary"
                    icon="i-lucide-plus"
                    to="/admin/schedules/create"
                    >Tambah jadwal
                </UButton>
            </div>
        </div>

        <AdminDeleteModal
            v-model:open="isDeleteModalOpen"
            :id="selectedSchedule?.id"
            :title="selectedSchedule?.title || 'Jadwal'"
            endpoint="api/schedules"
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
import type { Schedule } from "~/composables/useSchedules";

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

const isDeleteModalOpen = ref(false);

// Perbaikan 1: Mendeklarasikan state untuk menyimpan jadwal yang dipilih saat tombol hapus diklik
const selectedSchedule = ref<{ id: number | string; title: string } | null>(
    null,
);

// Perbaikan 2: Fungsi triggerDelete sekarang menerima row dan mengisi selectedSchedule
function triggerDelete(row: Row<Schedule>) {
    const id = row.original.id;
    const title = titleOf(row.original) || "Jadwal ini";

    if (id !== undefined && id !== null) {
        selectedSchedule.value = {
            id: id,
            title: title,
        };
        isDeleteModalOpen.value = true;
    }
}

// Perbaikan 3: Memanggil fungsi refresh() bawaan composable untuk memperbarui tabel secara real-time
function handleDeleteSuccess() {
    isDeleteModalOpen.value = false;
    toast.add({
        title: "Berhasil!",
        description: "Jadwal telah berhasil dihapus dari sistem.",
        color: "success",
        duration: 5000,
    });
    refresh();
}

function handleDeleteError(err: string) {
    toast.add({
        title: "Gagal menghapus jadwal",
        description: err || "Terjadi kesalahan pada server.",
        color: "warning",
        duration: 5000,
    });
}

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
        id: "name",
        header: "Nama Jadwal",
        meta: { class: { th: "min-w-[200px]" } },
        cell: ({ row }) => {
            return h(
                "div",
                { class: "flex items-center gap-3 py-1 whitespace-normal " },
                [
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
                            {
                                class: "text-xs text-gray-500 line-clamp-1 mt-0.5",
                            },
                            row.original.title_en || "",
                        ),
                    ]),
                ],
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
        cell: ({ row }) =>
            h(
                "div",
                {
                    class: "text-gray-600 dark:text-gray-400 text-sm line-clamp-2",
                },
                descOf(row.original) || "-",
            ),
    },
    {
        id: "tanggal",
        header: "Periode Jadwal",
        meta: { class: { th: "min-w-[180px]" } },
        cell: ({ row }) => {
            const formatIndo = (dateStr: string | undefined) => {
                if (!dateStr) return "-";
                const date = new Date(dateStr);
                if (isNaN(date.getTime())) return dateStr;

                return new Intl.DateTimeFormat("id-ID", {
                    day: "2-digit",
                    month: "short",
                    year: "numeric",
                }).format(date);
            };

            return h(
                "div",
                {
                    class: "flex flex-col text-xs gap-0.5 text-gray-600 dark:text-gray-400",
                },
                [
                    h(
                        "span",
                        {},
                        `Mulai: ${formatIndo(row.original.start_date)}`,
                    ),
                    h(
                        "span",
                        {},
                        `Selesai: ${formatIndo(row.original.end_date)}`,
                    ),
                ],
            );
        },
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

function getRowItems(row: Row<Schedule>) {
    return [
        [
            {
                label: "Lihat Detail",
                icon: "i-lucide-eye",
                onSelect() {
                    const id = row.original.id;
                    if (id) navigateTo(`/admin/schedules/${id}`);
                },
            },
        ],
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
                label: "Hapus Jadwal",
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
